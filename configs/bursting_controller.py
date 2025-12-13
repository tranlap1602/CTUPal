#!/usr/bin/env python3

from flask import Flask, request, jsonify
import subprocess
import logging
import requests
import boto3
import pytz
import time
import threading
from datetime import datetime

app = Flask(__name__)

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('/var/log/cloud-bursting.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)


# Telegram Bot
TELEGRAM_BOT_TOKEN = 'TELEGRAM_BOT_TOKEN'
TELEGRAM_CHAT_ID = 'chatid'

# AWS Configuration
AWS_REGION = 'ap-southeast-2'
EC2_INSTANCE_ID = 'ec2_instance_id'
AWS_ACCESS_KEY = 'aws_access_key'
AWS_SECRET_KEY = 'aws_secret_key'

# EC2 Tailscale IP
EC2_TAILSCALE_IP = 'tailscale_ip'

EC2_STOP_DELAY = 30

HAPROXY_CONFIG = '/etc/haproxy/haproxy.cfg'

try:
    ec2_client = boto3.client(
        'ec2',
        region_name=AWS_REGION,
        aws_access_key_id=AWS_ACCESS_KEY,
        aws_secret_access_key=AWS_SECRET_KEY
    )
    logger.info("✅ AWS EC2 client initialized")
except Exception as e:
    logger.error(f"❌ Failed to initialize AWS client: {e}")
    ec2_client = None

# ===== STATE =====
bursting_enabled = False
stop_ec2_timer = None

# ===== HELPER FUNCTIONS =====

def get_vietnam_time():
    """Lấy thời gian Việt Nam"""
    return datetime.now(pytz.timezone('Asia/Ho_Chi_Minh'))

def send_telegram(message):
    """Gửi thông báo Telegram"""
    try:
        url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
        response = requests.post(url, json={
            'chat_id': TELEGRAM_CHAT_ID,
            'text': message,
            'parse_mode': 'HTML'
        }, timeout=10)
        if response.status_code == 200:
            logger.info("📱 Telegram notification sent")
        return response.status_code == 200
    except Exception as e:
        logger.error(f"Telegram error: {e}")
        return False


def get_ec2_status():
    """Lấy trạng thái EC2"""
    if not ec2_client:
        return 'unknown'
    try:
        response = ec2_client.describe_instances(InstanceIds=[EC2_INSTANCE_ID])
        state = response['Reservations'][0]['Instances'][0]['State']['Name']
        logger.info(f"EC2 status: {state}")
        return state
    except Exception as e:
        logger.error(f"Error getting EC2 status: {e}")
        return 'unknown'

def start_ec2_and_wait():
    """Khởi động EC2 và chờ sẵn sàng"""
    if not ec2_client:
        logger.error("AWS client not initialized")
        return False

    try:
        status = get_ec2_status()

        # Đã chạy rồi
        if status == 'running':
            logger.info("✅ EC2 already running")
            return True

        # Đang tắt, chờ tắt xong
        if status == 'stopping':
            logger.info("⏳ EC2 is stopping, waiting...")
            time.sleep(30)
            status = get_ec2_status()

        # Bắt đầu khởi động
        logger.info(f"🚀 Starting EC2 instance: {EC2_INSTANCE_ID}")
        ec2_client.start_instances(InstanceIds=[EC2_INSTANCE_ID])

        # Chờ EC2 running (tối đa 2 phút)
        logger.info("⏳ Waiting for EC2 to start...")
        waiter = ec2_client.get_waiter('instance_running')
        waiter.wait(
            InstanceIds=[EC2_INSTANCE_ID],
            WaiterConfig={'Delay': 5, 'MaxAttempts': 24}
        )

        # Chờ thêm cho services khởi động (Apache, Tailscale)
        logger.info("⏳ Waiting for services to initialize...")
        time.sleep(20)

        logger.info("✅ EC2 is ready!")
        return True

    except Exception as e:
        logger.error(f"❌ Error starting EC2: {e}")
        return False

def stop_ec2():
    """Tắt EC2"""
    if not ec2_client:
        return False

    try:
        status = get_ec2_status()
        if status in ['stopped', 'stopping']:
            logger.info("EC2 already stopped/stopping")
            return True

        logger.info(f"🔴 Stopping EC2 instance: {EC2_INSTANCE_ID}")
        ec2_client.stop_instances(InstanceIds=[EC2_INSTANCE_ID])

        send_telegram(
            f"<b>EC2 ĐÃ TẮT</b>\n\n"
            f"🕐 {get_vietnam_time().strftime('%d/%m/%Y %H:%M:%S')}\n\n"
            f"✅ Instance: {EC2_INSTANCE_ID}"
        )

        logger.info("✅ EC2 stop command sent")
        return True

    except Exception as e:
        logger.error(f"❌ Error stopping EC2: {e}")
        return False

def schedule_ec2_stop():
    """Lên lịch tắt EC2 sau một khoảng thời gian"""
    global stop_ec2_timer

    # Hủy timer cũ nếu có
    if stop_ec2_timer:
        stop_ec2_timer.cancel()

    logger.info(f"⏳ Scheduled EC2 stop in {EC2_STOP_DELAY} seconds")
    stop_ec2_timer = threading.Timer(EC2_STOP_DELAY, stop_ec2)
    stop_ec2_timer.start()

def cancel_ec2_stop():
    """Hủy lệnh tắt EC2 đang pending"""
    global stop_ec2_timer
    if stop_ec2_timer:
        stop_ec2_timer.cancel()
        stop_ec2_timer = None
        logger.info("🚫 Cancelled scheduled EC2 stop")

# ===== CLOUD BURSTING FUNCTIONS =====

def enable_aws_backend():
    """Enable Cloud Bursting - Tự động khởi động EC2"""
    global bursting_enabled

    logger.info("=" * 50)
    logger.info("🚨 ENABLE CLOUD BURSTING TRIGGERED")
    logger.info("=" * 50)

    # Hủy lệnh tắt EC2 nếu đang pending
    cancel_ec2_stop()

    # Gửi thông báo đang xử lý
    send_telegram(
        f"⚡ <b>PHÁT HIỆN QUÁ TẢI!</b>\n\n"
        f"🕐 {get_vietnam_time().strftime('%d/%m/%Y %H:%M:%S')}\n\n"
        f"⏳ Đang khởi động AWS EC2...\n"
        f"⏱️ Vui lòng chờ 60-90 giây..."
    )

    #Khởi động EC2
    if not start_ec2_and_wait():
        send_telegram(
            f"❌ <b>LỖI: Không thể khởi động EC2!</b>\n\n"
            f"Vui lòng kiểm tra AWS Console."
        )
        return False

    #Update HAProxy config
    try:
        with open(HAPROXY_CONFIG, 'r') as f:
            config = f.read()

        # Tìm và thay thế
        old_line = f'server app-aws {EC2_TAILSCALE_IP}:80 check weight 0 inter 5000 rise 3 fall 5'
        new_line = f'server app-aws {EC2_TAILSCALE_IP}:80 check weight 192 inter 5000 rise 3 fall 5'

        if old_line in config:
            config = config.replace(old_line, new_line)

            with open(HAPROXY_CONFIG, 'w') as f:
                f.write(config)

            # Reload HAProxy
            result = subprocess.run(
                ['systemctl', 'reload', 'haproxy'],
                capture_output=True, text=True
            )

            if result.returncode == 0:
                bursting_enabled = True
                logger.info("✅ CLOUD BURSTING ENABLED SUCCESSFULLY")

                send_telegram(
                    f"🚨 <b>CLOUD BURSTING ĐÃ KÍCH HOẠT!</b>\n\n"
                    f"🕐 {get_vietnam_time().strftime('%d/%m/%Y %H:%M:%S')}\n\n"
                    f"<b>Phân phối lưu lượng:</b>\n"
                    f"   Private Cloud: 25%\n"
                    f"   AWS EC2: 75%\n\n"
                    f"✅ EC2 đã tự động khởi động!\n"
                    f"🔄 HAProxy đã reload thành công!"
                )
                return True
            else:
                logger.error(f"HAProxy reload failed: {result.stderr}")

        else:
            # Có thể đã enable rồi
            if 'weight 192' in config:
                logger.info("Bursting already enabled")
                bursting_enabled = True
                return True
            else:
                logger.warning("Could not find expected HAProxy config line")

    except Exception as e:
        logger.error(f"Error updating HAProxy config: {e}")

    return False

def disable_aws_backend():
    """Disable Cloud Bursting - Lên lịch tắt EC2"""
    global bursting_enabled

    logger.info("=" * 50)
    logger.info("📉 DISABLE CLOUD BURSTING TRIGGERED")
    logger.info("=" * 50)

    try:
        with open(HAPROXY_CONFIG, 'r') as f:
            config = f.read()

        old_line = f'server app-aws {EC2_TAILSCALE_IP}:80 check weight 192 inter 5000 rise 3 fall 5'
        new_line = f'server app-aws {EC2_TAILSCALE_IP}:80 check weight 0 inter 5000 rise 3 fall 5'

        if old_line in config:
            config = config.replace(old_line, new_line)

            with open(HAPROXY_CONFIG, 'w') as f:
                f.write(config)

            result = subprocess.run(
                ['systemctl', 'reload', 'haproxy'],
                capture_output=True, text=True
            )

            if result.returncode == 0:
                bursting_enabled = False
                logger.info("✅ CLOUD BURSTING DISABLED")

                # Lên lịch tắt EC2 sau 5 phút
                schedule_ec2_stop()

                send_telegram(
                    f"✅ <b>HỆ THỐNG ĐÃ ỔN ĐỊNH!</b>\n\n"
                    f"🕐 {get_vietnam_time().strftime('%d/%m/%Y %H:%M:%S')}\n\n"
                    f"<b>Phân phối:</b>\n"
                    f"   Private Cloud: 100%\n"
                    f"   AWS EC2: Standby\n\n"
                    f"⏳ EC2 sẽ tự động tắt sau {EC2_STOP_DELAY} giây"
                )
                return True

    except Exception as e:
        logger.error(f"Error disabling bursting: {e}")

    return False

# ===== API ENDPOINTS =====

@app.route('/webhook', methods=['POST'])
def webhook():
    """Nhận webhook từ Alertmanager"""
    data = request.json
    logger.info(f"📥 Received webhook: {data}")

    for alert in data.get('alerts', []):
        alert_name = alert['labels'].get('alertname', 'Unknown')
        action = alert['labels'].get('action', '')
        status = alert.get('status', '')

        logger.info(f"Alert: {alert_name}, Status: {status}, Action: {action}")

        if status == 'firing':
            if action == 'enable_bursting':
                # Chạy trong thread riêng để không block
                thread = threading.Thread(target=enable_aws_backend)
                thread.start()
            elif action == 'disable_bursting':
                disable_aws_backend()

    return jsonify({'status': 'ok', 'message': 'Webhook processed'})

@app.route('/status', methods=['GET'])
def status():
    """Lấy trạng thái hệ thống"""
    ec2_status = get_ec2_status()
    return jsonify({
        'bursting_enabled': bursting_enabled,
        'ec2_status': ec2_status,
        'ec2_instance_id': EC2_INSTANCE_ID,
        'timestamp': get_vietnam_time().isoformat()
    })

@app.route('/enable', methods=['POST'])
def manual_enable():
    """Kích hoạt bursting thủ công"""
    thread = threading.Thread(target=enable_aws_backend)
    thread.start()
    return jsonify({'status': 'enabling', 'message': 'Cloud Bursting is being enabled'})

@app.route('/disable', methods=['POST'])
def manual_disable():
    """Tắt bursting thủ công"""
    disable_aws_backend()
    return jsonify({'status': 'disabled', 'message': 'Cloud Bursting disabled'})

@app.route('/start-ec2', methods=['POST'])
def manual_start_ec2():
    """Khởi động EC2 thủ công"""
    if start_ec2_and_wait():
        return jsonify({'status': 'running', 'message': 'EC2 started'})
    return jsonify({'status': 'error', 'message': 'Failed to start EC2'}), 500

@app.route('/stop-ec2', methods=['POST'])
def manual_stop_ec2():
    """Tắt EC2 thủ công"""
    cancel_ec2_stop()  # Hủy timer nếu có
    if stop_ec2():
        return jsonify({'status': 'stopping', 'message': 'EC2 stopping'})
    return jsonify({'status': 'error', 'message': 'Failed to stop EC2'}), 500

@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    return jsonify({'status': 'healthy'})

@app.route('/test-telegram', methods=['GET'])
def test_telegram():
    """Test Telegram notification"""
    send_telegram(
        f"<b>TEST NOTIFICATION</b>\n\n"
        f"🕐 {get_vietnam_time().strftime('%d/%m/%Y %H:%M:%S')}\n\n"
        f"✅ Telegram Bot hoạt động bình thường!"
    )
    return jsonify({'status': 'sent'})

# ===== MAIN =====

if __name__ == '__main__':
    logger.info("=" * 60)
    logger.info("🚀 CLOUD BURSTING CONTROLLER v2.0 - AUTO EC2 MODE")
    logger.info("=" * 60)
    logger.info(f"EC2 Instance: {EC2_INSTANCE_ID}")
    logger.info(f"EC2 Region: {AWS_REGION}")
    logger.info(f"Auto-stop delay: {EC2_STOP_DELAY}s")

    # Gửi thông báo khởi động
    ec2_status = get_ec2_status()
    send_telegram(
        f"<b>Cloud Bursting</b>\n\n"
        f"🕐 {get_vietnam_time().strftime('%d/%m/%Y %H:%M:%S')}\n\n"
        f"Chế độ: <b>Auto Start/Stop EC2</b>\n"
        f"EC2: {EC2_INSTANCE_ID}\n"
        f"Trạng thái EC2: {ec2_status}\n"
        f"Auto-stop delay: {EC2_STOP_DELAY} giây\n\n"
        f"✅ Sẵn sàng hoạt động!"
    )

    app.run(host='0.0.0.0', port=5000, threaded=True)