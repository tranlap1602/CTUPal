# ⚡ Hướng dẫn Deploy Nhanh - CTUPal trên OpenStack

Hướng dẫn này giúp bạn deploy CTUPal lên OpenStack một cách nhanh chóng bằng script tự động.

## 📋 Thông tin VM

| VM           | IP Private | IP Floating | Vai trò      |
| ------------ | ---------- | ----------- | ------------ |
| `mysql-vm`   | 10.0.0.40  | 172.24.4.36 | Database     |
| `web-vm`     | 10.0.0.7   | 172.24.4.95 | Web Server   |
| `haproxy-vm` | 10.0.0.4   | 172.24.4.66 | Load Balance |

---

## 🚀 BƯỚC 1: Push Code lên Git (Trên Windows)

```powershell
# Di chuyển vào thư mục dự án
cd C:\Users\tranl\Desktop\CTUPal

# Kiểm tra status
git status

# Add tất cả file mới
git add .

# Commit
git commit -m "Add deployment scripts for OpenStack"

# Push lên GitHub (cần tạo repo trước trên GitHub)
git push origin main
```

**Nếu chưa có repository:**

1. Vào https://github.com/new
2. Tạo repository mới tên `CTUPal` (hoặc tên khác)
3. Chọn **Private** nếu không muốn public
4. Không tạo README (vì đã có sẵn)
5. Click **Create repository**

Sau đó chạy:

```powershell
git remote add origin https://github.com/YOUR_USERNAME/CTUPal.git
git branch -M main
git push -u origin main
```

---

## 🗄️ BƯỚC 2: Deploy MySQL Server

### SSH vào MySQL VM

```bash
ssh user@172.24.4.36
```

### Clone repository

```bash
# Update và cài git
sudo apt update
sudo apt install git -y

# Clone dự án (thay YOUR_USERNAME bằng username GitHub của bạn)
git clone https://github.com/YOUR_USERNAME/CTUPal.git

cd CTUPal
```

### Chạy script setup

```bash
# Cấp quyền thực thi
chmod +x setup-mysql.sh

# Chạy script
sudo bash setup-mysql.sh
```

**Script sẽ hỏi:**

1. **MySQL root password**: Nhập password mạnh (ví dụ: `Mysql@2025!`)
2. **Password cho ctupal_user**: Nhập password (ví dụ: `CTUpal@2025!`) 
3. **IP Web Server**: Nhấn Enter (mặc định: `10.0.0.7`)

> **⚠️ QUAN TRỌNG**: Lưu lại password `ctupal_user` - bạn cần nó ở bước tiếp theo!

### Kiểm tra

```bash
# Kiểm tra MySQL
sudo systemctl status mariadb

# Kiểm tra database
sudo mysql -u root -p
```

```sql
SHOW DATABASES;
USE student_manager;
SHOW TABLES;
EXIT;
```

✅ **MySQL VM xong!**

---

## 🌐 BƯỚC 3: Deploy Web Server

### SSH vào Web VM

**Mở terminal mới** (PowerShell):

```bash
ssh user@172.24.4.95
```

### Clone repository

```bash
sudo apt update
sudo apt install git -y

git clone https://github.com/YOUR_USERNAME/CTUPal.git

cd CTUPal
```

### Chạy script setup

```bash
chmod +x setup-web.sh
sudo bash setup-web.sh
```

**Script sẽ hỏi:**

1. **IP MySQL Server**: Nhấn Enter (mặc định: `10.0.0.40`)
2. **MySQL username**: Nhấn Enter (mặc định: `ctupal_user`)
3. **MySQL password**: Nhập password bạn đã đặt ở BƯỚC 2 (ví dụ: `CTUpal@2025!`)
4. **Database name**: Nhấn Enter (mặc định: `student_manager`)
5. **APP_URL**: Nhấn Enter (mặc định: `http://172.24.4.66`)
6. **IP HAProxy**: Nhấn Enter (mặc định: `10.0.0.4`)

Script sẽ tự động:
- Cài Apache + PHP 8.2
- Cấu hình VirtualHost
- Phân quyền thư mục
- Test kết nối MySQL

### Đặt lại password Admin

```bash
# Tạo hash password
php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"
```

Copy hash vừa tạo (dạng `$2y$10$...`), sau đó:

```bash
# Kết nối MySQL
mysql -h 10.0.0.40 -u ctupal_user -p
```

Nhập password `ctupal_user`, sau đó:

```sql
USE student_manager;
UPDATE users SET password = 'DÁN_HASH_VÀO_ĐÂY' WHERE email = 'admin@studentmanager.com';
EXIT;
```

### Kiểm tra

```bash
# Kiểm tra Apache
sudo systemctl status apache2

# Test truy cập
curl -I http://localhost/login.php
```

**Mở trình duyệt trên Windows:**

```
http://172.24.4.95/login.php
```

Bạn sẽ thấy trang đăng nhập CTUPal!

✅ **Web VM xong!**

---

## ⚖️ BƯỚC 4: Deploy HAProxy

### SSH vào HAProxy VM

**Mở terminal mới**:

```bash
ssh user@172.24.4.66
```

### Cài đặt HAProxy

```bash
sudo apt update
sudo apt install haproxy -y
```

### Cấu hình HAProxy

```bash
# Backup config gốc
sudo cp /etc/haproxy/haproxy.cfg /etc/haproxy/haproxy.cfg.backup

# Sửa config
sudo nano /etc/haproxy/haproxy.cfg
```

**Xóa toàn bộ nội dung cũ**, dán nội dung sau:

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

# HAProxy Statistics
listen stats
    bind *:8080
    stats enable
    stats uri /stats
    stats realm HAProxy\ Statistics
    stats auth admin:admin123
    stats refresh 30s

# Frontend
frontend http_front
    bind *:80
    mode http
    option httplog
    default_backend web_servers

# Backend
backend web_servers
    mode http
    balance roundrobin
    option httpchk GET /login.php
    http-check expect status 200
    server web1 10.0.0.7:80 check inter 3000 rise 2 fall 3
```

Lưu: **Ctrl + X** → **Y** → **Enter**

### Kiểm tra và khởi động

```bash
# Kiểm tra config
sudo haproxy -c -f /etc/haproxy/haproxy.cfg

# Restart HAProxy
sudo systemctl restart haproxy
sudo systemctl status haproxy
```

### Cấu hình firewall

```bash
sudo apt install ufw -y
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 8080/tcp
sudo ufw --force enable
```

✅ **HAProxy xong!**

---

## 🎉 BƯỚC 5: Kiểm tra Hệ thống

### Test 1: Truy cập Website

**Mở trình duyệt:**

```
http://172.24.4.66/login.php
```

Bạn sẽ thấy trang đăng nhập CTUPal!

### Test 2: Đăng nhập

- **Email**: `admin@studentmanager.com`
- **Password**: `admin123`

### Test 3: HAProxy Stats

```
http://172.24.4.66:8080/stats
```

- **Username**: `admin`
- **Password**: `admin123`

### Test 4: Các tính năng

Thử các tính năng:
- ✅ Ghi chú
- ✅ Chi tiêu
- ✅ Tài liệu (upload file)
- ✅ Lịch học
- ✅ Hồ sơ

---

## 🔧 Xử lý lỗi nhanh

### Lỗi: Can't connect to MySQL

**Trên MySQL VM:**

```bash
sudo systemctl restart mariadb
sudo ufw status | grep 3306
```

**Trên Web VM:**

```bash
mysql -h 10.0.0.40 -u ctupal_user -p
```

### Lỗi: 502 Bad Gateway

**Trên HAProxy VM:**

```bash
curl http://10.0.0.7/login.php
```

**Trên Web VM:**

```bash
sudo systemctl status apache2
sudo tail -f /var/log/apache2/ctupal_error.log
```

### Lỗi: 403 Forbidden

**Trên Web VM:**

```bash
sudo chown -R www-data:www-data /var/www/ctupal
sudo chmod -R 755 /var/www/ctupal
sudo chmod -R 775 /var/www/ctupal/uploads
```

---

## 📊 Tổng kết

### Đã hoàn thành:

- ✅ MySQL Server (10.0.0.40)
- ✅ Web Server (10.0.0.7)
- ✅ HAProxy (10.0.0.4)
- ✅ Website chạy tại: http://172.24.4.66

### Thông tin quan trọng:

| Thông tin            | Giá trị                       |
| -------------------- | ----------------------------- |
| URL Website          | http://172.24.4.66            |
| HAProxy Stats        | http://172.24.4.66:8080/stats |
| Admin Email          | admin@studentmanager.com      |
| Admin Password       | admin123                      |
| MySQL Host           | 10.0.0.40                     |
| MySQL Database       | student_manager               |
| MySQL User           | ctupal_user                   |
| MySQL Password       | (bạn đã đặt ở BƯỚC 2)         |

---

## 📚 Tài liệu chi tiết

Xem thêm: [`DEPLOYMENT.md`](./DEPLOYMENT.md) - Hướng dẫn chi tiết, bảo mật, backup

---

**Chúc mừng! Hệ thống CTUPal đã sẵn sàng! 🎊**

