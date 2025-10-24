# 🚀 Hướng dẫn Triển khai CTUPal trên OpenStack

Tài liệu này hướng dẫn chi tiết cách triển khai hệ thống CTUPal lên hạ tầng OpenStack với kiến trúc 3-tier.

## 📋 Mục lục

1. [Kiến trúc hệ thống](#kiến-trúc-hệ-thống)
2. [Yêu cầu](#yêu-cầu)
3. [Chuẩn bị](#chuẩn-bị)
4. [Triển khai MySQL Server](#triển-khai-mysql-server)
5. [Triển khai Web Server](#triển-khai-web-server)
6. [Triển khai HAProxy](#triển-khai-haproxy)
7. [Kiểm tra và Testing](#kiểm-tra-và-testing)
8. [Bảo mật](#bảo-mật)
9. [Backup và Restore](#backup-và-restore)
10. [Xử lý sự cố](#xử-lý-sự-cố)

---

## 🏗️ Kiến trúc hệ thống

```
Internet 
    ↓
HAProxy VM (172.24.4.66)
    ↓ (10.0.0.4 → 10.0.0.7)
Web Server VM (172.24.4.95)
    ↓ (10.0.0.7 → 10.0.0.40)
MySQL Server VM (172.24.4.36)
```

### Thông tin VM

| Tên VM       | IP Private | IP Floating | Vai trò                 | OS        |
| ------------ | ---------- | ----------- | ----------------------- | --------- |
| `mysql-vm`   | 10.0.0.40  | 172.24.4.36 | Database Server         | Debian 10 |
| `web-vm`     | 10.0.0.7   | 172.24.4.95 | Web Server (Apache+PHP) | Debian 10 |
| `haproxy-vm` | 10.0.0.4   | 172.24.4.66 | Load Balancer / Gateway | Debian 10 |

---

## ✅ Yêu cầu

### Phần cứng (mỗi VM)
- **CPU**: 2 vCPU
- **RAM**: 2 GB
- **Disk**: 20 GB
- **Network**: 1 interface kết nối mạng private

### Phần mềm
- **OS**: Debian 10 (Buster)
- **MySQL**: MariaDB 10.3+
- **Web Server**: Apache 2.4+
- **PHP**: 8.2+
- **HAProxy**: 2.0+

### Quyền truy cập
- SSH access vào tất cả các VM
- Sudo/root privileges
- Floating IP đã được assign

---

## 🔧 Chuẩn bị

### Bước 1: Chuẩn bị Git Repository

**Trên máy local (Windows):**

```powershell
# Di chuyển vào thư mục dự án
cd C:\Users\tranl\Desktop\CTUPal

# Kiểm tra git status
git status

# Add tất cả các file mới (nếu chưa có trong git)
git add .

# Commit
git commit -m "Chuẩn bị deploy lên OpenStack"

# Push lên GitHub (giả sử bạn đã có remote repository)
git push origin main
```

**Nếu chưa có remote repository:**

```powershell
# Tạo repository mới trên GitHub, rồi:
git remote add origin https://github.com/YOUR_USERNAME/CTUPal.git
git branch -M main
git push -u origin main
```

### Bước 2: Test SSH đến các VM

```powershell
# Test kết nối đến MySQL VM
ssh user@172.24.4.36

# Test kết nối đến Web VM
ssh user@172.24.4.95

# Test kết nối đến HAProxy VM
ssh user@172.24.4.66
```

---

## 🗄️ Triển khai MySQL Server

### Phương án 1: Sử dụng Script Tự động (Khuyến nghị)

**Bước 1: SSH vào MySQL VM**

```bash
ssh user@172.24.4.36
```

**Bước 2: Clone repository**

```bash
# Cài đặt git nếu chưa có
sudo apt update
sudo apt install git -y

# Clone dự án
git clone https://github.com/YOUR_USERNAME/CTUPal.git
cd CTUPal
```

**Bước 3: Chạy script setup**

```bash
# Cấp quyền thực thi
chmod +x setup-mysql.sh

# Chạy script với quyền root
sudo bash setup-mysql.sh
```

Script sẽ hỏi bạn:
- MySQL root password (ví dụ: `Mysql@2025!`)
- Password cho user `ctupal_user` (ví dụ: `CTUpal@2025!`)
- IP của Web Server (mặc định: `10.0.0.7`)

**Lưu ý**: Nhớ lưu lại các password này!

**Bước 4: Kiểm tra**

```bash
# Kiểm tra MySQL đang chạy
sudo systemctl status mariadb

# Kiểm tra database đã được tạo
sudo mysql -u root -p
```

```sql
SHOW DATABASES;
USE student_manager;
SHOW TABLES;
SELECT * FROM users;
EXIT;
```

### Phương án 2: Cài đặt Thủ công

Xem hướng dẫn chi tiết trong file `README.md` phần "Cài đặt MySQL VM thủ công".

---

## 🌐 Triển khai Web Server

### Phương án 1: Sử dụng Script Tự động (Khuyến nghị)

**Bước 1: SSH vào Web VM**

```bash
ssh user@172.24.4.95
```

**Bước 2: Clone repository**

```bash
sudo apt update
sudo apt install git -y

git clone https://github.com/YOUR_USERNAME/CTUPal.git
cd CTUPal
```

**Bước 3: Chạy script setup**

```bash
chmod +x setup-web.sh
sudo bash setup-web.sh
```

Script sẽ hỏi bạn:
- IP MySQL Server (mặc định: `10.0.0.40`)
- MySQL username (mặc định: `ctupal_user`)
- MySQL password (nhập password bạn đã đặt ở MySQL VM)
- Database name (mặc định: `student_manager`)
- APP_URL (mặc định: `http://172.24.4.66`)
- IP HAProxy (mặc định: `10.0.0.4`)

**Bước 4: Đặt lại password Admin**

```bash
# Tạo hash password mới
php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"

# Copy hash vừa tạo, rồi update trong database
mysql -h 10.0.0.40 -u ctupal_user -p student_manager
```

```sql
UPDATE users SET password = '<HASH_VỪA_COPY>' WHERE email = 'admin@studentmanager.com';
EXIT;
```

**Bước 5: Kiểm tra**

```bash
# Kiểm tra Apache
sudo systemctl status apache2

# Kiểm tra PHP version
php -v

# Test truy cập local
curl -I http://localhost/login.php

# Test truy cập qua IP
curl -I http://10.0.0.7/login.php
```

Mở trình duyệt trên máy Windows:
```
http://172.24.4.95/login.php
```

---

## ⚖️ Triển khai HAProxy

**Bước 1: SSH vào HAProxy VM**

```bash
ssh user@172.24.4.66
```

**Bước 2: Cài đặt HAProxy**

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install haproxy -y
```

**Bước 3: Backup cấu hình gốc**

```bash
sudo cp /etc/haproxy/haproxy.cfg /etc/haproxy/haproxy.cfg.backup
```

**Bước 4: Cấu hình HAProxy**

```bash
sudo nano /etc/haproxy/haproxy.cfg
```

Thay thế toàn bộ nội dung bằng:

```haproxy
global
    log /dev/log local0
    log /dev/log local1 notice
    chroot /var/lib/haproxy
    stats socket /run/haproxy/admin.sock mode 660 level admin
    stats timeout 30s
    user haproxy
    group haproxy
    daemon

defaults
    log     global
    mode    http
    option  httplog
    option  dontlognull
    timeout connect 5000
    timeout client  50000
    timeout server  50000
    errorfile 400 /etc/haproxy/errors/400.http
    errorfile 403 /etc/haproxy/errors/403.http
    errorfile 408 /etc/haproxy/errors/408.http
    errorfile 500 /etc/haproxy/errors/500.http
    errorfile 502 /etc/haproxy/errors/502.http
    errorfile 503 /etc/haproxy/errors/503.http
    errorfile 504 /etc/haproxy/errors/504.http

# HAProxy Statistics Dashboard
listen stats
    bind *:8080
    stats enable
    stats uri /stats
    stats realm HAProxy\ Statistics
    stats auth admin:admin123
    stats refresh 30s

# Frontend - Nhận request từ Internet
frontend http_front
    bind *:80
    mode http
    option httplog
    
    # Security headers
    http-response set-header X-Frame-Options SAMEORIGIN
    http-response set-header X-XSS-Protection "1; mode=block"
    http-response set-header X-Content-Type-Options nosniff
    
    default_backend web_servers

# Backend - Web Servers Pool
backend web_servers
    mode http
    balance roundrobin
    option httpchk GET /login.php
    http-check expect status 200
    
    # Web Server 1
    server web1 10.0.0.7:80 check inter 3000 rise 2 fall 3
    
    # Nếu có thêm Web Server, uncomment dòng dưới:
    # server web2 10.0.0.X:80 check inter 3000 rise 2 fall 3
```

**Bước 5: Kiểm tra cấu hình**

```bash
sudo haproxy -c -f /etc/haproxy/haproxy.cfg
```

Phải thấy: `Configuration file is valid`

**Bước 6: Restart HAProxy**

```bash
sudo systemctl restart haproxy
sudo systemctl status haproxy
```

**Bước 7: Cấu hình Firewall**

```bash
sudo apt install ufw -y
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 8080/tcp
sudo ufw --force enable
sudo ufw status
```

**Bước 8: Kiểm tra**

Mở trình duyệt:

1. **Website chính**: `http://172.24.4.66/login.php`
2. **HAProxy Stats**: `http://172.24.4.66:8080/stats`
   - Username: `admin`
   - Password: `admin123`

---

## 🧪 Kiểm tra và Testing

### Test 1: Kết nối Database

**Từ Web VM:**

```bash
mysql -h 10.0.0.40 -u ctupal_user -p
```

Nhập password, sau đó:

```sql
USE student_manager;
SELECT COUNT(*) FROM users;
EXIT;
```

### Test 2: Truy cập Website

**Qua HAProxy:**
```
http://172.24.4.66/login.php
```

**Trực tiếp Web Server:**
```
http://172.24.4.95/login.php
```

### Test 3: Đăng nhập

- Email: `admin@studentmanager.com`
- Password: `admin123` (hoặc password bạn đã đặt)

### Test 4: Upload File

1. Đăng nhập
2. Vào mục "Tài liệu"
3. Upload một file PDF thử
4. Kiểm tra file trong thư mục `/var/www/ctupal/uploads/1/`

### Test 5: Health Check HAProxy

```bash
curl -I http://172.24.4.66
```

Phải thấy: `HTTP/1.1 200 OK`

---

## 🔒 Bảo mật

### 1. Tường lửa (UFW)

**MySQL VM:**
```bash
sudo ufw status
# Chỉ cho phép:
# - SSH từ mọi nơi
# - MySQL (3306) từ 10.0.0.7 (Web VM)
```

**Web VM:**
```bash
sudo ufw status
# Chỉ cho phép:
# - SSH từ mọi nơi
# - HTTP (80) từ 10.0.0.4 (HAProxy)
```

**HAProxy VM:**
```bash
sudo ufw status
# Cho phép:
# - SSH từ mọi nơi
# - HTTP (80) từ mọi nơi
# - Stats (8080) từ mọi nơi (hoặc giới hạn IP cụ thể)
```

### 2. Bảo mật MySQL

```bash
# Trên MySQL VM
sudo mysql -u root -p
```

```sql
-- Xóa user anonymous
DELETE FROM mysql.user WHERE User='';

-- Xóa database test
DROP DATABASE IF EXISTS test;

-- Kiểm tra user
SELECT User, Host FROM mysql.user;

-- Chỉ nên thấy:
-- root@localhost
-- ctupal_user@10.0.0.7
-- ctupal_user@localhost

FLUSH PRIVILEGES;
```

### 3. Bảo mật Apache

```bash
# Trên Web VM
sudo nano /etc/apache2/conf-available/security.conf
```

Đảm bảo có các dòng:
```apache
ServerTokens Prod
ServerSignature Off
```

```bash
sudo a2enconf security
sudo systemctl restart apache2
```

### 4. Bảo mật thư mục uploads

Đã có file `.htaccess` trong thư mục `uploads/` để chặn thực thi PHP.

Kiểm tra:
```bash
ls -la /var/www/ctupal/uploads/.htaccess
cat /var/www/ctupal/uploads/.htaccess
```

### 5. Đổi password HAProxy Stats

```bash
sudo nano /etc/haproxy/haproxy.cfg
```

Tìm dòng:
```
stats auth admin:admin123
```

Đổi thành password mạnh hơn, sau đó restart:
```bash
sudo systemctl restart haproxy
```

---

## 💾 Backup và Restore

### Backup Database

**Script tự động trên MySQL VM:**

```bash
sudo nano /root/backup_db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/root/backups"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

mysqldump -u root -p'YOUR_ROOT_PASSWORD' student_manager > $BACKUP_DIR/student_manager_$DATE.sql

# Giữ backup 7 ngày gần nhất
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete

echo "Backup completed: $BACKUP_DIR/student_manager_$DATE.sql"
```

```bash
chmod +x /root/backup_db.sh

# Test chạy thử
sudo /root/backup_db.sh

# Thêm vào crontab (chạy mỗi ngày 2h sáng)
sudo crontab -e
```

Thêm dòng:
```
0 2 * * * /root/backup_db.sh
```

### Restore Database

```bash
mysql -u root -p student_manager < /root/backups/student_manager_YYYYMMDD_HHMMSS.sql
```

### Backup Source Code

**Trên Web VM:**

```bash
sudo tar -czf /root/ctupal_backup_$(date +%Y%m%d).tar.gz /var/www/ctupal
```

---

## 🔧 Xử lý sự cố

### Lỗi: Can't connect to MySQL server

**Kiểm tra trên MySQL VM:**

```bash
# MySQL có đang chạy?
sudo systemctl status mariadb

# Port 3306 có mở không?
sudo netstat -tlnp | grep 3306

# Bind-address đúng chưa?
sudo grep bind-address /etc/mysql/mariadb.conf.d/50-server.cnf
# Phải là: bind-address = 10.0.0.40

# Firewall có cho phép không?
sudo ufw status | grep 3306

# Restart MySQL
sudo systemctl restart mariadb
```

**Kiểm tra từ Web VM:**

```bash
# Ping đến MySQL VM
ping -c 3 10.0.0.40

# Telnet test port 3306
telnet 10.0.0.40 3306

# Test với mysql client
mysql -h 10.0.0.40 -u ctupal_user -p
```

### Lỗi: 502 Bad Gateway từ HAProxy

**Kiểm tra trên HAProxy VM:**

```bash
# HAProxy có chạy không?
sudo systemctl status haproxy

# Xem log
sudo tail -f /var/log/haproxy.log

# Kiểm tra backend status
curl http://10.0.0.7/login.php
```

**Kiểm tra trên Web VM:**

```bash
# Apache có chạy không?
sudo systemctl status apache2

# Xem log lỗi
sudo tail -f /var/log/apache2/ctupal_error.log
```

### Lỗi: 403 Forbidden

```bash
# Kiểm tra phân quyền
ls -la /var/www/ctupal

# Sửa phân quyền
sudo chown -R www-data:www-data /var/www/ctupal
sudo chmod -R 755 /var/www/ctupal
sudo chmod -R 775 /var/www/ctupal/uploads
```

### Lỗi: Cannot upload file

```bash
# Kiểm tra thư mục uploads
ls -la /var/www/ctupal/uploads

# Kiểm tra PHP config
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Kiểm tra log
sudo tail -f /var/log/apache2/ctupal_error.log
```

### Lỗi: Database connection failed

```bash
# Kiểm tra file config
cat /var/www/ctupal/config/db.php

# Test kết nối PHP
php -r "
\$pdo = new PDO('mysql:host=10.0.0.40;dbname=student_manager', 'ctupal_user', 'YOUR_PASSWORD');
echo 'Connected successfully';
"
```

---

## 📚 Tài liệu tham khảo

- [Apache Documentation](https://httpd.apache.org/docs/)
- [PHP Manual](https://www.php.net/manual/)
- [MariaDB Documentation](https://mariadb.org/documentation/)
- [HAProxy Configuration Manual](http://www.haproxy.org/)
- [UFW Documentation](https://help.ubuntu.com/community/UFW)

---

## 📝 Checklist Deploy

- [ ] MySQL VM: Cài đặt MariaDB
- [ ] MySQL VM: Tạo database và user
- [ ] MySQL VM: Import database.sql
- [ ] MySQL VM: Cấu hình bind-address
- [ ] MySQL VM: Cấu hình firewall
- [ ] Web VM: Cài đặt Apache + PHP 8.2
- [ ] Web VM: Clone source code
- [ ] Web VM: Cấu hình db.php
- [ ] Web VM: Cấu hình VirtualHost
- [ ] Web VM: Phân quyền uploads
- [ ] Web VM: Test kết nối MySQL
- [ ] Web VM: Cấu hình firewall
- [ ] HAProxy VM: Cài đặt HAProxy
- [ ] HAProxy VM: Cấu hình load balancer
- [ ] HAProxy VM: Cấu hình firewall
- [ ] Test truy cập qua HAProxy
- [ ] Đặt lại password admin
- [ ] Kiểm tra tất cả tính năng
- [ ] Cài đặt backup tự động
- [ ] Bảo mật hệ thống

---

## 💡 Mở rộng

### Thêm Web Server thứ 2 (High Availability)

1. Tạo VM mới: `web-vm-2` (10.0.0.8)
2. Làm theo các bước triển khai Web Server
3. Cập nhật HAProxy config:

```haproxy
backend web_servers
    server web1 10.0.0.7:80 check
    server web2 10.0.0.8:80 check
```

### Cài đặt HTTPS/SSL

1. Cài đặt Certbot trên HAProxy VM
2. Lấy certificate Let's Encrypt
3. Cấu hình HAProxy bind :443 ssl

### Monitoring

- Cài đặt Prometheus + Grafana
- Monitor CPU, RAM, Disk, Network
- Alert khi có vấn đề

---

**Tác giả**: CTUPal Team  
**Phiên bản**: 1.0.0  
**Cập nhật**: October 2025

