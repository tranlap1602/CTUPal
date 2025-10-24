# 📖 Hướng dẫn Triển khai CTUPal Thủ công - Chi tiết Từng Bước

> **Lưu ý**: Hướng dẫn này viết rất chi tiết, giải thích từng lệnh. Hãy làm từng bước một, đừng vội.

---

## 📋 Chuẩn bị

### Thông tin bạn cần ghi nhớ

Chuẩn bị sẵn một file text để ghi lại các thông tin sau:

```
=== THÔNG TIN HỆ THỐNG ===

MySQL VM:
- IP Floating: 172.24.4.36
- IP Private: 10.0.0.40
- MySQL Root Password: _________________ (bạn sẽ đặt)
- MySQL User: ctupal_user
- MySQL User Password: _________________ (bạn sẽ đặt)

Web VM:
- IP Floating: 172.24.4.95
- IP Private: 10.0.0.7

HAProxy VM:
- IP Floating: 172.24.4.66
- IP Private: 10.0.0.4

GitHub:
- Username: _________________
- Repository: CTUPal
```

---

## 🔄 PHẦN 1: ĐẨY CODE LÊN GITHUB

### Bước 1.1: Mở PowerShell trên Windows

1. Nhấn `Win + X`
2. Chọn **Windows PowerShell**

### Bước 1.2: Di chuyển vào thư mục dự án

```powershell
cd C:\Users\tranl\Desktop\CTUPal
```

**Giải thích**: Lệnh `cd` (change directory) để chuyển vào thư mục dự án.

### Bước 1.3: Kiểm tra Git status

```powershell
git status
```

**Giải thích**: Xem các file đã thay đổi. Bạn sẽ thấy danh sách file mới màu xanh/đỏ.

**Kết quả mong đợi**: Thấy các file mới như `setup-mysql.sh`, `setup-web.sh`, `DEPLOYMENT.md`, v.v.

### Bước 1.4: Add tất cả file vào Git

```powershell
git add .
```

**Giải thích**: 
- `git add .` = thêm tất cả file mới/đã sửa vào staging area
- Dấu `.` nghĩa là "tất cả"

### Bước 1.5: Commit các thay đổi

```powershell
git commit -m "Thêm script deploy cho OpenStack"
```

**Giải thích**: 
- `commit` = lưu lại các thay đổi
- `-m "..."` = thêm message mô tả

**Kết quả mong đợi**: Thấy dòng kiểu `[main abc1234] Thêm script deploy...`

### Bước 1.6: Kiểm tra xem đã có remote chưa

```powershell
git remote -v
```

**Giải thích**: Xem danh sách remote repository.

**Nếu thấy `origin` với link GitHub** → Chuyển sang Bước 1.8  
**Nếu không thấy gì** → Làm Bước 1.7

### Bước 1.7: Tạo Repository trên GitHub (nếu chưa có)

**Trên trình duyệt:**

1. Vào https://github.com/new
2. **Repository name**: `CTUPal`
3. **Description**: `Student Management System for CTU`
4. **Visibility**: Private (hoặc Public tùy bạn)
5. **KHÔNG** tích vào "Add a README file"
6. Click **Create repository**

**Sau đó quay lại PowerShell:**

```powershell
# Thay YOUR_USERNAME bằng username GitHub của bạn
git remote add origin https://github.com/YOUR_USERNAME/CTUPal.git
```

**Ví dụ**: Nếu username là `john123`:
```powershell
git remote add origin https://github.com/john123/CTUPal.git
```

### Bước 1.8: Push code lên GitHub

```powershell
git push origin main
```

**Nếu gặp lỗi "branch main doesn't exist"**, chạy:
```powershell
git branch -M main
git push -u origin main
```

**Giải thích**:
- `git push` = đẩy code lên server
- `origin` = tên remote
- `main` = tên branch

**Kết quả mong đợi**: Thấy progress bar upload file, cuối cùng thấy `Branch 'main' set up to track remote branch 'main'`

### Bước 1.9: Xác nhận trên GitHub

1. Mở trình duyệt
2. Vào `https://github.com/YOUR_USERNAME/CTUPal`
3. Kiểm tra xem các file đã có chưa

✅ **HOÀN THÀNH PHẦN 1**

---

## 🗄️ PHẦN 2: CÀI ĐẶT MYSQL SERVER (mysql-vm)

### Bước 2.1: Kết nối SSH vào MySQL VM

**Mở PowerShell mới** (hoặc dùng cửa sổ cũ):

```powershell
ssh user@172.24.4.36
```

**Lưu ý**: Thay `user` bằng username thực tế của bạn trên VM.

**Kết quả**: Bạn sẽ thấy dòng nhắc lệnh thay đổi, ví dụ: `user@mysql-vm:~$`

### Bước 2.2: Update hệ thống

```bash
sudo apt update
```

**Giải thích**:
- `sudo` = chạy với quyền administrator
- `apt update` = cập nhật danh sách package

**Bạn sẽ thấy**: Dòng chữ "Reading package lists... Done"

Tiếp tục:

```bash
sudo apt upgrade -y
```

**Giải thích**:
- `upgrade` = nâng cấp các package
- `-y` = tự động trả lời "yes"

**Đợi 2-5 phút** để quá trình hoàn tất.

### Bước 2.3: Cài đặt MariaDB

```bash
sudo apt install mariadb-server mariadb-client -y
```

**Giải thích**:
- `mariadb-server` = máy chủ MySQL
- `mariadb-client` = công cụ client để kết nối

**Đợi 1-2 phút** cài đặt.

### Bước 2.4: Kiểm tra MariaDB đã chạy chưa

```bash
sudo systemctl status mariadb
```

**Giải thích**: Xem trạng thái dịch vụ MariaDB

**Kết quả mong đợi**: Thấy dòng màu xanh `Active: active (running)`

**Nhấn `q`** để thoát.

### Bước 2.5: Khởi động và enable MariaDB

```bash
sudo systemctl start mariadb
```

**Giải thích**: Khởi động dịch vụ (nếu chưa chạy)

```bash
sudo systemctl enable mariadb
```

**Giải thích**: Đặt MariaDB tự động chạy khi khởi động hệ thống

### Bước 2.6: Bảo mật MySQL

```bash
sudo mysql_secure_installation
```

**Giải thích**: Chạy script bảo mật MySQL. Bạn sẽ được hỏi nhiều câu:

#### Câu hỏi 1:
```
Enter current password for root (enter for none):
```
**Trả lời**: Nhấn **Enter** (chưa có password)

#### Câu hỏi 2:
```
Switch to unix_socket authentication [Y/n]
```
**Trả lời**: Gõ `n` rồi Enter

#### Câu hỏi 3:
```
Change the root password? [Y/n]
```
**Trả lời**: Gõ `Y` rồi Enter

#### Câu hỏi 4:
```
New password:
```
**Trả lời**: Nhập password mạnh, ví dụ: `Mysql@2025!`  
**⚠️ QUAN TRỌNG**: Ghi lại password này!

#### Câu hỏi 5:
```
Re-enter new password:
```
**Trả lời**: Nhập lại password y hệt

#### Câu hỏi 6:
```
Remove anonymous users? [Y/n]
```
**Trả lời**: Gõ `Y` rồi Enter

#### Câu hỏi 7:
```
Disallow root login remotely? [Y/n]
```
**Trả lời**: Gõ `Y` rồi Enter

#### Câu hỏi 8:
```
Remove test database and access to it? [Y/n]
```
**Trả lời**: Gõ `Y` rồi Enter

#### Câu hỏi 9:
```
Reload privilege tables now? [Y/n]
```
**Trả lời**: Gõ `Y` rồi Enter

**Kết quả**: Thấy "All done!" và "Thanks for using MariaDB!"

### Bước 2.7: Đăng nhập vào MySQL

```bash
sudo mysql -u root -p
```

**Giải thích**:
- `mysql` = chương trình client
- `-u root` = login với user root
- `-p` = sẽ hỏi password

**Nhập password root** bạn vừa đặt ở Bước 2.6.

**Kết quả**: Thấy prompt `MariaDB [(none)]>`

### Bước 2.8: Tạo database

Trong MySQL prompt, gõ lệnh sau (chú ý dấu `;` cuối lệnh):

```sql
CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Nhấn Enter.**

**Kết quả**: `Query OK, 1 row affected`

**Giải thích**:
- Tạo database tên `student_manager`
- Dùng bộ ký tự `utf8mb4` (hỗ trợ tiếng Việt và emoji)

### Bước 2.9: Tạo user cho Web Server

**Lệnh 1**: Tạo user kết nối từ Web Server (10.0.0.7)

```sql
CREATE USER 'ctupal_user'@'10.0.0.7' IDENTIFIED BY 'CTUpal@2025!';
```

**Nhấn Enter.**

**⚠️ QUAN TRỌNG**: Ghi lại password `CTUpal@2025!` (hoặc đổi thành password khác nếu muốn)

**Kết quả**: `Query OK, 0 rows affected`

**Lệnh 2**: Cấp quyền cho user này

```sql
GRANT ALL PRIVILEGES ON student_manager.* TO 'ctupal_user'@'10.0.0.7';
```

**Nhấn Enter.**

**Kết quả**: `Query OK, 0 rows affected`

**Lệnh 3**: Tạo user cho localhost (để test)

```sql
CREATE USER 'ctupal_user'@'localhost' IDENTIFIED BY 'CTUpal@2025!';
```

**Nhấn Enter.**

**Lệnh 4**: Cấp quyền cho localhost user

```sql
GRANT ALL PRIVILEGES ON student_manager.* TO 'ctupal_user'@'localhost';
```

**Nhấn Enter.**

**Lệnh 5**: Reload privileges

```sql
FLUSH PRIVILEGES;
```

**Nhấn Enter.**

**Kết quả**: `Query OK, 0 rows affected`

### Bước 2.10: Kiểm tra user đã tạo

```sql
SELECT User, Host FROM mysql.user WHERE User='ctupal_user';
```

**Nhấn Enter.**

**Kết quả mong đợi**: Thấy bảng với 2 dòng:
```
+-------------+-----------+
| User        | Host      |
+-------------+-----------+
| ctupal_user | 10.0.0.7  |
| ctupal_user | localhost |
+-------------+-----------+
```

**Thoát MySQL**:

```sql
EXIT;
```

hoặc nhấn **Ctrl + D**

### Bước 2.11: Cấu hình MySQL cho phép kết nối từ xa

Mở file cấu hình:

```bash
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
```

**Giải thích**:
- `nano` = trình soạn thảo văn bản
- File này chứa cấu hình server

**Trong nano**:

1. Nhấn **Ctrl + W** (tìm kiếm)
2. Gõ: `bind-address` rồi nhấn **Enter**
3. Con trỏ sẽ nhảy đến dòng: `bind-address = 127.0.0.1`
4. Dùng phím mũi tên di chuyển đến số `127.0.0.1`
5. Xóa `127.0.0.1` và thay bằng `10.0.0.40`
6. Dòng sau khi sửa: `bind-address = 10.0.0.40`

**Lưu file**:
1. Nhấn **Ctrl + X**
2. Gõ **Y**
3. Nhấn **Enter**

### Bước 2.12: Restart MariaDB

```bash
sudo systemctl restart mariadb
```

**Giải thích**: Khởi động lại để áp dụng cấu hình mới.

**Kiểm tra lại**:

```bash
sudo systemctl status mariadb
```

**Phải thấy**: `Active: active (running)` màu xanh.

Nhấn `q` để thoát.

### Bước 2.13: Cài đặt Git

```bash
sudo apt install git -y
```

**Đợi 30 giây** cài đặt.

### Bước 2.14: Clone repository

```bash
git clone https://github.com/YOUR_USERNAME/CTUPal.git
```

**Lưu ý**: Thay `YOUR_USERNAME` bằng username GitHub của bạn.

**Ví dụ**:
```bash
git clone https://github.com/john123/CTUPal.git
```

**Nếu repo là Private**, bạn sẽ được hỏi username và password GitHub.

**Kết quả**: Thấy "Cloning into 'CTUPal'..."

### Bước 2.15: Di chuyển vào thư mục

```bash
cd CTUPal
```

**Giải thích**: Vào thư mục vừa clone.

**Kiểm tra file**:

```bash
ls -la
```

**Kết quả**: Thấy danh sách file như `database.sql`, `setup-mysql.sh`, v.v.

### Bước 2.16: Import database schema

```bash
mysql -u root -p student_manager < database.sql
```

**Giải thích**:
- Đọc file `database.sql` và chạy các lệnh SQL trong đó
- `<` = đọc từ file

**Nhập password root** MySQL.

**Kết quả**: Không có gì hiện ra = thành công!

### Bước 2.17: Kiểm tra database đã import

```bash
sudo mysql -u root -p
```

Nhập password, sau đó:

```sql
USE student_manager;
```

**Kết quả**: `Database changed`

```sql
SHOW TABLES;
```

**Kết quả mong đợi**: Thấy 4 bảng:
```
+----------------------------+
| Tables_in_student_manager  |
+----------------------------+
| documents                  |
| expenses                   |
| notes                      |
| users                      |
+----------------------------+
```

**Kiểm tra user admin**:

```sql
SELECT id, name, email, mssv, role FROM users;
```

**Kết quả**: Thấy 1 dòng với user Administrator.

**Thoát**:

```sql
EXIT;
```

### Bước 2.18: Cài đặt Firewall UFW

```bash
sudo apt install ufw -y
```

**Đợi 20 giây** cài đặt.

### Bước 2.19: Cấu hình Firewall

**Lệnh 1**: Cho phép SSH (QUAN TRỌNG - không làm mất kết nối SSH!)

```bash
sudo ufw allow ssh
```

**Kết quả**: `Rules updated`

**Lệnh 2**: Cho phép MySQL từ Web Server

```bash
sudo ufw allow from 10.0.0.7 to any port 3306
```

**Giải thích**:
- Chỉ cho phép IP `10.0.0.7` (Web Server) kết nối port 3306 (MySQL)

**Kết quả**: `Rule added`

**Lệnh 3**: Bật firewall

```bash
sudo ufw enable
```

**Bạn sẽ thấy cảnh báo**:
```
Command may disrupt existing ssh connections. Proceed with operation (y|n)?
```

**Gõ**: `y` rồi Enter

**Kết quả**: `Firewall is active and enabled on system startup`

**Kiểm tra**:

```bash
sudo ufw status
```

**Kết quả mong đợi**:
```
Status: active

To                         Action      From
--                         ------      ----
22/tcp                     ALLOW       Anywhere
3306                       ALLOW       10.0.0.7
```

### Bước 2.20: Test kết nối MySQL từ localhost

```bash
mysql -h 10.0.0.40 -u ctupal_user -p
```

**Nhập password**: `CTUpal@2025!` (hoặc password bạn đã đặt)

**Kết quả**: Vào được MySQL prompt `MariaDB [(none)]>`

```sql
SHOW DATABASES;
```

**Phải thấy**: `student_manager` trong danh sách.

```sql
EXIT;
```

✅ **HOÀN THÀNH PHẦN 2 - MYSQL SERVER**

---

## 🌐 PHẦN 3: CÀI ĐẶT WEB SERVER (web-vm)

### Bước 3.1: Mở SSH mới đến Web VM

**Mở PowerShell MỚI** (giữ nguyên cửa sổ SSH của MySQL VM):

```powershell
ssh user@172.24.4.95
```

**Thay `user`** bằng username của bạn.

**Kết quả**: Thấy prompt `user@web-vm:~$`

### Bước 3.2: Update hệ thống

```bash
sudo apt update
```

Đợi xong, tiếp tục:

```bash
sudo apt upgrade -y
```

**Đợi 2-5 phút.**

### Bước 3.3: Cài đặt các công cụ cần thiết

```bash
sudo apt install apt-transport-https lsb-release ca-certificates wget curl git unzip -y
```

**Giải thích**: Cài đặt các công cụ để thêm repository và download.

**Đợi 1 phút.**

### Bước 3.4: Thêm repository PHP 8

Debian 10 mặc định có PHP 7.3, chúng ta cần PHP 8.2.

**Lệnh 1**: Download GPG key

```bash
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
```

**Kết quả**: Thấy dòng "saved [...]"

**Lệnh 2**: Thêm repository

```bash
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
```

**Kết quả**: Thấy dòng "deb https://packages.sury.org/php/ buster main"

**Lệnh 3**: Update lại danh sách package

```bash
sudo apt update
```

### Bước 3.5: Cài đặt Apache Web Server

```bash
sudo apt install apache2 -y
```

**Đợi 1 phút.**

**Kết quả**: Thấy "Processing triggers for apache2..."

### Bước 3.6: Kiểm tra Apache

```bash
sudo systemctl status apache2
```

**Phải thấy**: `Active: active (running)` màu xanh.

Nhấn `q` để thoát.

### Bước 3.7: Enable Apache tự động khởi động

```bash
sudo systemctl enable apache2
```

**Kết quả**: `Synchronizing state of apache2.service...`

### Bước 3.8: Cài đặt PHP 8.2 và extensions

```bash
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath -y
```

**Giải thích**:
- `php8.2` = PHP core
- `php8.2-mysql` = extension kết nối MySQL
- `php8.2-gd` = xử lý hình ảnh
- `php8.2-mbstring` = xử lý Unicode
- Các extension khác cần thiết

**Đợi 2-3 phút** cài đặt.

### Bước 3.9: Kiểm tra PHP version

```bash
php -v
```

**Kết quả mong đợi**: Thấy `PHP 8.2.x`

### Bước 3.10: Enable module Apache

```bash
sudo a2enmod rewrite
```

**Giải thích**: Enable module `mod_rewrite` (cần cho .htaccess)

**Kết quả**: `Enabling module rewrite`

**Restart Apache**:

```bash
sudo systemctl restart apache2
```

### Bước 3.11: Clone repository

```bash
git clone https://github.com/YOUR_USERNAME/CTUPal.git
```

**Thay `YOUR_USERNAME`** bằng username GitHub của bạn.

**Đợi clone xong.**

### Bước 3.12: Di chuyển source code vào thư mục web

**Tạo thư mục**:

```bash
sudo mkdir -p /var/www/ctupal
```

**Giải thích**: `/var/www/` là thư mục mặc định cho website trên Linux.

**Copy source code**:

```bash
sudo cp -r CTUPal/* /var/www/ctupal/
```

**Giải thích**:
- `cp -r` = copy recursive (cả thư mục con)
- `CTUPal/*` = tất cả file trong CTUPal
- `/var/www/ctupal/` = đích đến

**Copy cả file ẩn**:

```bash
sudo cp -r CTUPal/.gitignore /var/www/ctupal/ 2>/dev/null || true
```

### Bước 3.13: Phân quyền thư mục

```bash
sudo chown -R www-data:www-data /var/www/ctupal
```

**Giải thích**:
- `chown` = change owner (đổi chủ sở hữu)
- `www-data` = user mà Apache chạy
- `-R` = recursive (tất cả file con)

```bash
sudo chmod -R 755 /var/www/ctupal
```

**Giải thích**:
- `chmod` = change mode (đổi quyền)
- `755` = owner có quyền đọc/ghi/thực thi, các user khác chỉ đọc/thực thi

```bash
sudo chmod -R 775 /var/www/ctupal/uploads
```

**Giải thích**: Thư mục `uploads` cần quyền ghi để upload file.

### Bước 3.14: Kiểm tra file đã copy

```bash
ls -la /var/www/ctupal
```

**Phải thấy**: `index.php`, `login.php`, thư mục `config/`, `uploads/`, v.v.

### Bước 3.15: Tạo file cấu hình database

```bash
sudo nano /var/www/ctupal/config/db.php
```

**Copy toàn bộ nội dung sau** vào nano (click chuột phải để paste):

```php
<?php
//Kết nối database
$host = '10.0.0.40';           // IP của MySQL VM
$dbname = 'student_manager';
$username = 'ctupal_user';
$password = 'CTUpal@2025!';    // Password bạn đã đặt ở MySQL VM
$charset = 'utf8mb4';

define('APP_NAME', 'CTUPal');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://172.24.4.66');  // IP của HAProxy

// Cấu hình upload
define('UPLOAD_PATH', realpath(__DIR__ . '/../uploads/') . DIRECTORY_SEPARATOR);
define('MAX_FILE_SIZE', 20 * 1024 * 1024);
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif']);

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}

//Hàm để truy vấn
function executeQuery($query, $params = [])
{
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $e->getMessage());
    }
}

//lấy một bản ghi
function fetchOne($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

//lấy nhiều bản ghi
function fetchAll($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

//insert và trả về ID
function insertAndGetId($query, $params = [])
{
    global $pdo;
    $stmt = executeQuery($query, $params);
    return $pdo->lastInsertId();
}

//kiểm tra và tạo thư mục upload
function createUserUploadDir($userId)
{
    $userDir = UPLOAD_PATH . $userId;
    if (!is_dir($userDir)) {
        if (!mkdir($userDir, 0755, true)) {
            throw new Exception("Không thể tạo thư mục upload");
        }
    }
    return $userDir;
}

//Hàm validate file upload
function validateUploadFile($file)
{
    $errors = [];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Lỗi upload file";
        return $errors;
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "File quá lớn. Kích thước tối đa: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB";
    }
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ALLOWED_FILE_TYPES)) {
        $errors[] = "Loại file không được phép. Chỉ chấp nhận: " . implode(', ', ALLOWED_FILE_TYPES);
    }
    return $errors;
}

//Hàm kiểm tra quyền truy cập file
function checkFileAccess($filePath, $userId)
{
    $userDir = UPLOAD_PATH . $userId;
    $realPath = realpath($filePath);
    $realUserDir = realpath($userDir);
    return $realPath && $realUserDir && strpos($realPath, $realUserDir) === 0;
}

//Hàm làm sạch dữ liệu đầu vào
function sanitizeInput($input)
{
    if (is_string($input)) {
        $input = trim($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $input = str_replace(chr(0), '', $input);
    }
    return $input;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
```

**⚠️ QUAN TRỌNG**: Kiểm tra lại dòng `$password = 'CTUpal@2025!';` - phải giống password bạn đã đặt ở MySQL VM!

**Lưu file**:
1. **Ctrl + X**
2. **Y**
3. **Enter**

### Bước 3.16: Cấu hình Apache VirtualHost

```bash
sudo nano /etc/apache2/sites-available/ctupal.conf
```

**Copy toàn bộ nội dung sau** vào:

```apache
<VirtualHost *:80>
    ServerName 172.24.4.95
    ServerAdmin admin@ctupal.local
    DocumentRoot /var/www/ctupal
    
    <Directory /var/www/ctupal>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    <Directory /var/www/ctupal/uploads>
        php_flag engine off
        RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .php8
        Options -ExecCGI -Indexes
        AddType text/plain .php .phtml .php3 .php4 .php5 .php7 .php8
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ctupal_error.log
    CustomLog ${APACHE_LOG_DIR}/ctupal_access.log combined
</VirtualHost>
```

**Lưu**: **Ctrl + X** → **Y** → **Enter**

### Bước 3.17: Enable site mới

```bash
sudo a2ensite ctupal.conf
```

**Kết quả**: `Enabling site ctupal`

### Bước 3.18: Disable site mặc định

```bash
sudo a2dissite 000-default.conf
```

**Kết quả**: `Site 000-default disabled`

### Bước 3.19: Cấu hình PHP cho upload file lớn

```bash
sudo nano /etc/php/8.2/apache2/php.ini
```

**File này rất dài**. Bạn cần tìm và sửa 4 dòng:

**Tìm dòng 1**:
1. Nhấn **Ctrl + W**
2. Gõ: `upload_max_filesize` rồi Enter
3. Thấy dòng: `upload_max_filesize = 2M`
4. Sửa thành: `upload_max_filesize = 20M`

**Tìm dòng 2**:
1. Nhấn **Ctrl + W**
2. Gõ: `post_max_size` rồi Enter
3. Thấy dòng: `post_max_size = 8M`
4. Sửa thành: `post_max_size = 20M`

**Tìm dòng 3**:
1. Nhấn **Ctrl + W**
2. Gõ: `max_execution_time` rồi Enter
3. Thấy dòng: `max_execution_time = 30`
4. Sửa thành: `max_execution_time = 300`

**Tìm dòng 4**:
1. Nhấn **Ctrl + W**
2. Gõ: `memory_limit` rồi Enter
3. Thấy dòng: `memory_limit = 128M`
4. Sửa thành: `memory_limit = 256M`

**Lưu**: **Ctrl + X** → **Y** → **Enter**

### Bước 3.20: Restart Apache

```bash
sudo systemctl restart apache2
```

**Kiểm tra**:

```bash
sudo systemctl status apache2
```

**Phải thấy**: `Active: active (running)`

Nhấn `q` để thoát.

### Bước 3.21: Test kết nối MySQL từ Web VM

```bash
mysql -h 10.0.0.40 -u ctupal_user -p
```

**Nhập password**: `CTUpal@2025!`

**Nếu kết nối thành công**, bạn sẽ vào MySQL prompt:

```sql
USE student_manager;
SHOW TABLES;
SELECT COUNT(*) FROM users;
EXIT;
```

**Nếu lỗi "Can't connect"**:
- Quay lại MySQL VM
- Kiểm tra firewall: `sudo ufw status`
- Kiểm tra bind-address: `sudo grep bind-address /etc/mysql/mariadb.conf.d/50-server.cnf`
- Restart MySQL: `sudo systemctl restart mariadb`

### Bước 3.22: Cấu hình Firewall

```bash
sudo apt install ufw -y
```

```bash
sudo ufw allow ssh
```

```bash
sudo ufw allow 80/tcp
```

```bash
sudo ufw enable
```

Gõ `y` khi được hỏi.

```bash
sudo ufw status
```

### Bước 3.23: Test truy cập web

**Test từ chính Web VM**:

```bash
curl -I http://localhost/login.php
```

**Kết quả mong đợi**: Thấy `HTTP/1.1 200 OK`

**Test từ Windows** (mở trình duyệt):

```
http://172.24.4.95/login.php
```

**Bạn sẽ thấy trang đăng nhập CTUPal!** 🎉

### Bước 3.24: Đặt lại password Admin

**Tạo hash password**:

```bash
php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"
```

**Copy hash** vừa tạo (dạng `$2y$10$...`)

**Kết nối MySQL**:

```bash
mysql -h 10.0.0.40 -u ctupal_user -p
```

**Update password**:

```sql
USE student_manager;
UPDATE users SET password = 'DÁN_HASH_VÀO_ĐÂY' WHERE email = 'admin@studentmanager.com';
EXIT;
```

**Ví dụ thực tế**:
```sql
UPDATE users SET password = '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCD' WHERE email = 'admin@studentmanager.com';
```

✅ **HOÀN THÀNH PHẦN 3 - WEB SERVER**

---

## ⚖️ PHẦN 4: CÀI ĐẶT HAPROXY (haproxy-vm)

### Bước 4.1: SSH vào HAProxy VM

**Mở PowerShell MỚI**:

```powershell
ssh user@172.24.4.66
```

### Bước 4.2: Update hệ thống

```bash
sudo apt update && sudo apt upgrade -y
```

**Đợi 2-5 phút.**

### Bước 4.3: Cài đặt HAProxy

```bash
sudo apt install haproxy -y
```

**Đợi 30 giây.**

### Bước 4.4: Kiểm tra HAProxy version

```bash
haproxy -v
```

**Kết quả**: `HA-Proxy version 2.x.x`

### Bước 4.5: Backup cấu hình gốc

```bash
sudo cp /etc/haproxy/haproxy.cfg /etc/haproxy/haproxy.cfg.backup
```

**Giải thích**: Lưu file gốc phòng khi cần khôi phục.

### Bước 4.6: Sửa cấu hình HAProxy

```bash
sudo nano /etc/haproxy/haproxy.cfg
```

**Xóa toàn bộ nội dung cũ**:
1. Nhấn **Ctrl + K** nhiều lần để xóa từng dòng
2. Hoặc **Alt + T** rồi gõ `yes` để xóa toàn bộ

**Paste nội dung mới**:

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
    
    # Nếu có thêm Web Server, thêm dòng bên dưới:
    # server web2 10.0.0.X:80 check inter 3000 rise 2 fall 3
```

**Lưu**: **Ctrl + X** → **Y** → **Enter**

**Giải thích cấu hình**:
- `listen stats`: Dashboard để xem status
- `frontend http_front`: Nhận request từ port 80
- `backend web_servers`: Danh sách Web Server
- `option httpchk GET /login.php`: Health check bằng cách request /login.php
- `check inter 3000`: Check mỗi 3 giây

### Bước 4.7: Kiểm tra cấu hình

```bash
sudo haproxy -c -f /etc/haproxy/haproxy.cfg
```

**Kết quả mong đợi**: `Configuration file is valid`

**Nếu có lỗi**: Kiểm tra lại cấu hình, đảm bảo không thiếu dấu, sai indent.

### Bước 4.8: Restart HAProxy

```bash
sudo systemctl restart haproxy
```

**Kiểm tra**:

```bash
sudo systemctl status haproxy
```

**Phải thấy**: `Active: active (running)`

Nhấn `q` để thoát.

### Bước 4.9: Enable HAProxy tự động khởi động

```bash
sudo systemctl enable haproxy
```

### Bước 4.10: Cấu hình Firewall

```bash
sudo apt install ufw -y
```

```bash
sudo ufw allow ssh
```

```bash
sudo ufw allow 80/tcp
```

**Giải thích**: Cho phép HTTP (port 80)

```bash
sudo ufw allow 8080/tcp
```

**Giải thích**: Cho phép HAProxy Stats (port 8080)

```bash
sudo ufw enable
```

Gõ `y` khi được hỏi.

```bash
sudo ufw status
```

**Kết quả**:
```
22/tcp    ALLOW    Anywhere
80/tcp    ALLOW    Anywhere
8080/tcp  ALLOW    Anywhere
```

### Bước 4.11: Test HAProxy từ chính VM

```bash
curl -I http://localhost
```

**Kết quả mong đợi**: `HTTP/1.1 200 OK`

```bash
curl -I http://10.0.0.7/login.php
```

**Kết quả**: Phải kết nối được đến Web Server.

✅ **HOÀN THÀNH PHẦN 4 - HAPROXY**

---

## 🎉 PHẦN 5: KIỂM TRA HỆ THỐNG

### Bước 5.1: Test truy cập qua HAProxy

**Mở trình duyệt trên Windows**:

```
http://172.24.4.66/login.php
```

**Bạn sẽ thấy trang đăng nhập CTUPal!** 🎊

### Bước 5.2: Test HAProxy Statistics

```
http://172.24.4.66:8080/stats
```

**Nhập**:
- Username: `admin`
- Password: `admin123`

**Bạn sẽ thấy dashboard** với status của web1 (màu xanh = UP)

### Bước 5.3: Đăng nhập vào hệ thống

**Tại trang login**:
- Email: `admin@studentmanager.com`
- Password: `admin123`

**Click Đăng nhập.**

**Bạn sẽ được chuyển đến Admin Dashboard!** 🎉

### Bước 5.4: Test các tính năng

**Thử các tính năng**:

1. **Ghi chú**: Tạo ghi chú mới
2. **Chi tiêu**: Thêm khoản chi tiêu
3. **Tài liệu**: Upload file PDF thử
4. **Hồ sơ**: Cập nhật thông tin
5. **Admin**: Quản lý user (nếu đăng nhập admin)

### Bước 5.5: Kiểm tra log (nếu có lỗi)

**Trên Web VM**:

```bash
sudo tail -f /var/log/apache2/ctupal_error.log
```

**Trên HAProxy VM**:

```bash
sudo tail -f /var/log/haproxy.log
```

Nhấn **Ctrl + C** để thoát.

---

## ✅ CHECKLIST TỔNG HỢP

### MySQL VM ✓
- [x] Cài đặt MariaDB
- [x] Tạo database `student_manager`
- [x] Tạo user `ctupal_user`
- [x] Import database.sql
- [x] Cấu hình bind-address = 10.0.0.40
- [x] Cấu hình firewall cho port 3306
- [x] Test kết nối từ localhost

### Web VM ✓
- [x] Cài đặt Apache
- [x] Cài đặt PHP 8.2
- [x] Clone source code
- [x] Cấu hình db.php
- [x] Cấu hình VirtualHost
- [x] Phân quyền thư mục
- [x] Cấu hình PHP (upload size)
- [x] Test kết nối MySQL
- [x] Đặt lại password admin
- [x] Cấu hình firewall
- [x] Test truy cập web

### HAProxy VM ✓
- [x] Cài đặt HAProxy
- [x] Cấu hình load balancer
- [x] Cấu hình health check
- [x] Cấu hình stats dashboard
- [x] Cấu hình firewall
- [x] Test kết nối

### Hệ thống ✓
- [x] Truy cập được qua HAProxy (http://172.24.4.66)
- [x] Đăng nhập thành công
- [x] Tất cả tính năng hoạt động
- [x] Upload file thành công

---

## 📊 THÔNG TIN HỆ THỐNG

### URL truy cập:
- **Website chính**: http://172.24.4.66
- **HAProxy Stats**: http://172.24.4.66:8080/stats

### Tài khoản:
- **Admin**: admin@studentmanager.com / admin123

### Database:
- **Host**: 10.0.0.40
- **Database**: student_manager
- **User**: ctupal_user
- **Password**: CTUpal@2025!

### SSH:
- **MySQL VM**: ssh user@172.24.4.36
- **Web VM**: ssh user@172.24.4.95
- **HAProxy VM**: ssh user@172.24.4.66

---

## 🔧 XỬ LÝ SỰ CỐ

### Lỗi: Can't connect to MySQL server

**Kiểm tra trên MySQL VM**:

```bash
# MySQL có chạy không?
sudo systemctl status mariadb

# Bind-address đúng chưa?
sudo grep bind-address /etc/mysql/mariadb.conf.d/50-server.cnf

# Firewall có cho phép không?
sudo ufw status | grep 3306

# Restart MySQL
sudo systemctl restart mariadb
```

**Kiểm tra từ Web VM**:

```bash
# Ping đến MySQL VM
ping -c 3 10.0.0.40

# Test port 3306
telnet 10.0.0.40 3306
```

### Lỗi: 502 Bad Gateway

**Kiểm tra Web Server**:

```bash
# Apache có chạy không?
sudo systemctl status apache2

# Restart Apache
sudo systemctl restart apache2

# Xem log lỗi
sudo tail -f /var/log/apache2/ctupal_error.log
```

**Kiểm tra HAProxy**:

```bash
# HAProxy có chạy không?
sudo systemctl status haproxy

# Test kết nối đến Web Server
curl -I http://10.0.0.7/login.php
```

### Lỗi: Cannot upload file

```bash
# Kiểm tra phân quyền
ls -la /var/www/ctupal/uploads

# Sửa phân quyền
sudo chmod -R 775 /var/www/ctupal/uploads
sudo chown -R www-data:www-data /var/www/ctupal/uploads
```

---

## 🎓 CHO NIÊN LUẬN

### Screenshots cần chụp:

1. **Kiến trúc**: Sơ đồ 3 VM (MySQL → Web → HAProxy)
2. **MySQL**: `SHOW DATABASES;`, `SHOW TABLES;`
3. **Web Server**: Trang login, dashboard
4. **HAProxy Stats**: Dashboard với web1 UP
5. **Firewall**: `sudo ufw status` trên cả 3 VM
6. **Tính năng**: Upload file, tạo ghi chú, chi tiêu

### Báo cáo nên có:

1. **Giới thiệu**: Mục đích, yêu cầu
2. **Kiến trúc**: Giải thích 3-tier architecture
3. **Triển khai**: Các bước đã làm
4. **Bảo mật**: Firewall, .htaccess, password hash
5. **Kết quả**: Screenshots
6. **Đánh giá**: Ưu/nhược điểm

---

**🎉 CHÚC MỪNG! Bạn đã hoàn thành việc deploy CTUPal lên OpenStack!**

Nếu gặp bất kỳ lỗi nào, hãy check lại từng bước một. Hầu hết lỗi đều do:
- Sai password
- Firewall chặn
- IP/hostname không đúng
- Phân quyền file

Good luck với niên luận! 🚀

