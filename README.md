# StudentManager

**StudentManager** là ứng dụng web PHP thuần giúp sinh viên quản lý thông tin cá nhân, lịch học, tài liệu, chi tiêu và ghi chú học tập. Giao diện hiện đại, tối ưu cho desktop/mobile, sử dụng Tailwind CSS.

---

## 🚀 Tính năng chính

- **Quản lý lịch học**: Tích hợp Google Calendar, lưu & xem lịch học cá nhân/lớp.
- **Quản lý tài liệu**: Upload, phân loại, tìm kiếm, tải về, xóa tài liệu học tập (PDF, Word, Excel, PowerPoint, ảnh...)
- **Quản lý chi tiêu**: Thêm, xóa, thống kê chi tiêu theo tháng, ngày, loại, phương thức thanh toán.
- **Ghi chú học tập/cá nhân**: Thêm, sửa, xóa, phân loại ghi chú.
- **Quản lý thông tin cá nhân**: Cập nhật họ tên, mật khẩu, số điện thoại, ngày sinh.
- **Đăng nhập/Đăng xuất**: Bảo mật bằng session, chỉ user đăng nhập mới truy cập được các chức năng chính.
- **Giao diện hiện đại**: Responsive, Tailwind CSS, icon FontAwesome, hiệu ứng động đẹp mắt.

---

## 🗂️ Cấu trúc thư mục

```
StudentManager/
├── calendar.php           # Quản lý lịch học (Google Calendar)
├── config/
│   └── db.php            # Cấu hình & hàm kết nối CSDL, tiện ích truy vấn
├── database.sql          # File khởi tạo CSDL MySQL
├── documents.php         # Quản lý tài liệu
├── expenses.php          # Quản lý chi tiêu
├── includes/
│   ├── header.php        # Header, navigation, breadcrumb, user info
│   └── footer.php        # Footer, JS chung, back-to-top, modal
├── index.php             # Dashboard tổng quan
├── login.php             # Đăng nhập
├── logout.php            # Đăng xuất
├── notes.php             # Quản lý ghi chú
├── profile.php           # Quản lý thông tin cá nhân
├── src/
│   ├── input.css         # File nguồn Tailwind CSS
│   └── output.css        # File CSS đã build
├── uploads/              # Thư mục lưu file upload (tài liệu, ảnh)
│   └── documents/
├── views/
│   ├── documents-view.php
│   ├── expenses-view.php
│   └── notes-view.php
├── package.json          # Quản lý dependency Tailwind CSS
└── package-lock.json
```

---

## ⚙️ Hướng dẫn cài đặt trên XAMPP

### 1. Cài đặt cơ sở dữ liệu

- Mở **XAMPP Control Panel**, bật Apache & MySQL.
- Truy cập **phpMyAdmin** tại [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Tạo database mới tên `student_manager` (hoặc tên khác, nhớ sửa trong `config/db.php`)
- Import file `database.sql` vào database vừa tạo.

### 2. Cấu hình kết nối MySQL
 
- Mặc định:
  - Host: `localhost`
  - Database: `student_manager`
  - User: `root`
  - Password: ` `
- Nếu khác, sửa trong file `config/db.php`.

### 3. Chạy ứng dụng

- Đặt toàn bộ source vào thư mục `htdocs/StudentManager` của XAMPP.
- Truy cập: [http://localhost/StudentManager/login.php](http://localhost/StudentManager/login.php)

### 4. (Tùy chọn) Cài đặt & build giao diện với Tailwind CSS CLI

- Đã có sẵn file `src/output.css`, KHÔNG cần build lại nếu không chỉnh giao diện.
- Nếu muốn tùy chỉnh hoặc phát triển giao diện:
  1. **Cài đặt Tailwind CSS và CLI:**
     ```bash
     npm install tailwindcss @tailwindcss/cli
     ```
  2. **Khởi động quá trình build tự động:**
     ```bash
     npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
     ```
  - Lệnh trên sẽ tự động quét các file nguồn, build lại CSS mỗi khi bạn lưu file.

---

## 👤 Tài khoản mẫu

Sau khi import database, có sẵn 1 tài khoản:

- **Username/MSSV/Email:** `lapb2204945` hoặc `lapb2204945@student.ctu.edu.vn` hoặc `B2204945`
- **Password:** `sEM3WQYV`

---

## 📝 Mô tả các module chính

- **Đăng nhập/Đăng xuất:** Quản lý session, bảo mật, chuyển hướng hợp lý.
- **Dashboard:** Thống kê nhanh số ghi chú, tổng chi tiêu tháng, số tài liệu đã upload.
- **Lịch học:** Nhập Google Calendar ID, nhúng lịch vào trang, hướng dẫn lấy ID chi tiết.
- **Tài liệu:** Upload nhiều file, phân loại, lọc theo môn/danh mục, tải về/xóa file.
- **Chi tiêu:** Thêm/xóa chi tiêu, lọc theo tháng, loại, phương thức, thống kê tổng tiền.
- **Ghi chú:** Thêm/sửa/xóa ghi chú, phân loại, lọc theo danh mục.
- **Thông tin cá nhân:** Cập nhật họ tên, đổi mật khẩu (kiểm tra bảo mật), số điện thoại, ngày sinh.

---

## 🛠️ Công nghệ sử dụng

- **PHP thuần** (không framework)
- **MySQL** (InnoDB, chuẩn hóa, có khóa ngoại)
- **Tailwind CSS** (build thủ công, không dùng CDN)
- **FontAwesome** (CDN)
- **HTML5, CSS3, JavaScript** (chủ yếu cho hiệu ứng UI)
- **Google Calendar Embed** (nhúng lịch học)

---


## 📜 Bản quyền

- Dự án phục vụ mục đích học tập, phi thương mại.
- Tác giả: Trần Công Lập