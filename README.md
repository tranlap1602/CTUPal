# StudentManager

**StudentManager** là ứng dụng web PHP thuần giúp sinh viên quản lý thông tin cá nhân, lịch học, tài liệu, chi tiêu và ghi chú học tập. Giao diện hiện đại, tối ưu cho desktop/mobile, sử dụng Tailwind CSS.

---

## 🚀 Tính năng chính

- **Quản lý lịch học**: Tích hợp Google Calendar, lưu & xem lịch học cá nhân/lớp.
- **Quản lý tài liệu**: Upload, phân loại, tìm kiếm, tải về, xóa tài liệu học tập (PDF, Word, Excel, PowerPoint, ảnh...)
- **Quản lý chi tiêu**: Thêm, xóa, thống kê chi tiêu theo tháng, ngày, loại, phương thức thanh toán, **hiển thị biểu đồ trực quan bằng Chart.js**.
- **Ghi chú học tập/cá nhân**: Thêm, sửa, xóa, phân loại ghi chú.
- **Quản lý thông tin cá nhân**: Cập nhật họ tên, mật khẩu, số điện thoại, ngày sinh.
- **Đăng nhập/Đăng xuất**: Bảo mật bằng session, chỉ user đăng nhập mới truy cập được các chức năng chính.
- **Giao diện hiện đại**: Responsive, Tailwind CSS, icon FontAwesome, hiệu ứng động đẹp mắt.

---

## 🗂️ Cấu trúc thư mục

```
StudentManager/
├── calendar.php          # Quản lý lịch học (Google Calendar)
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
├── assets/
│   └── js/
│       └── charts.js     # File custom vẽ biểu đồ chi tiêu (Chart.js)
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

## ⚙️ Hướng dẫn cài đặt chi tiết (trình bày dạng báo cáo)

### 1. Lý do chọn công nghệ

- **PHP thuần**: Đơn giản, dễ triển khai trên XAMPP, phù hợp sinh viên, không cần framework phức tạp.
- **MySQL**: Phổ biến, dễ dùng, tích hợp sẵn trong XAMPP.
- **Tailwind CSS**: Giúp xây dựng giao diện hiện đại, responsive nhanh chóng, dễ tuỳ biến.
- **Chart.js**: Thư viện mã nguồn mở, dễ dùng, trực quan, hỗ trợ nhiều loại biểu đồ, chỉ cần nhúng CDN, không cần cài đặt phức tạp ([Tài liệu Chart.js](https://www.chartjs.org/docs/latest/)).
- **FontAwesome**: Bộ icon phổ biến, dễ tích hợp qua CDN.

### 2. Các bước cài đặt và chạy ứng dụng

#### Bước 1: Cài đặt môi trường XAMPP
- Tải và cài đặt XAMPP tại [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)
- Khởi động Apache và MySQL từ XAMPP Control Panel.

#### Bước 2: Tạo cơ sở dữ liệu
- Truy cập [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Tạo database mới tên `student_manager` (hoặc tên khác, nhớ sửa trong `config/db.php`)
- Import file `database.sql` vào database vừa tạo (chọn database, nhấn Import, chọn file, nhấn Go).

#### Bước 3: Cấu hình kết nối CSDL
- Mở file `config/db.php`, chỉnh lại thông tin nếu không dùng mặc định:
  - Host: `localhost`
  - Database: `student_manager`
  - User: `root`
  - Password: (để trống nếu dùng mặc định XAMPP)

#### Bước 4: Copy source code vào XAMPP
- Giải nén/copy toàn bộ thư mục `StudentManager` vào `C:/xampp/htdocs/StudentManager`.
- Đảm bảo các thư mục con như `uploads/`, `assets/js/` tồn tại và có quyền ghi.

#### Bước 5: Cài đặt và build giao diện với Tailwind CSS (nếu muốn tuỳ chỉnh)
- Đã có sẵn file CSS build sẵn (`src/output.css`). Nếu muốn chỉnh giao diện:
  1. Cài Node.js: [https://nodejs.org/](https://nodejs.org/)
  2. Mở terminal tại thư mục project, chạy:
     ```bash
     npm install tailwindcss @tailwindcss/cli
     npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
     ```
- Khi lưu file CSS, Tailwind sẽ tự động build lại.

#### Bước 6: Sử dụng biểu đồ chi tiêu với Chart.js
- **Không cần cài đặt npm**. Chỉ cần nhúng CDN trong file `views/expenses-view.php`:
  ```html
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="assets/js/charts.js"></script>
  ```
- File `assets/js/charts.js` chứa hàm `renderCharts` và plugin custom cho biểu đồ rỗng.
- Dữ liệu biểu đồ được truyền từ PHP sang JS bằng `json_encode`.
- Biểu đồ sẽ tự động hiển thị khi truy cập trang quản lý chi tiêu.
- Tham khảo chi tiết: [Chart.js Documentation](https://www.chartjs.org/docs/latest/)

#### Bước 7: Chạy ứng dụng
- Truy cập: [http://localhost/StudentManager/login.php](http://localhost/StudentManager/login.php)
- Đăng nhập bằng tài khoản mẫu hoặc tự đăng ký.

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
- **Chi tiêu:** Thêm/xóa chi tiêu, lọc theo tháng, loại, phương thức, thống kê tổng tiền, **hiển thị biểu đồ trực quan bằng Chart.js**.
- **Ghi chú:** Thêm/sửa/xóa ghi chú, phân loại, lọc theo danh mục.
- **Thông tin cá nhân:** Cập nhật họ tên, đổi mật khẩu (kiểm tra bảo mật), số điện thoại, ngày sinh.

---

## 🛠️ Công nghệ sử dụng

- **PHP thuần** (không framework)
- **MySQL** (InnoDB, chuẩn hóa, có khóa ngoại)
- **Tailwind CSS** (build thủ công, không dùng CDN)
- **FontAwesome** (CDN)
- **Chart.js** (CDN, vẽ biểu đồ chi tiêu)
- **HTML5, CSS3, JavaScript** (chủ yếu cho hiệu ứng UI)
- **Google Calendar Embed** (nhúng lịch học)

---

## 🌐 Nguồn tham khảo bên ngoài

- **Chart.js**: Thư viện vẽ biểu đồ JavaScript, tài liệu chính thức: [https://www.chartjs.org/docs/latest/](https://www.chartjs.org/docs/latest/)
- **Tailwind CSS**: Framework CSS tiện dụng, tài liệu: [https://tailwindcss.com/docs/installation](https://tailwindcss.com/docs/installation)
- **FontAwesome**: Bộ icon miễn phí, tài liệu: [https://fontawesome.com/docs/web/setup/cdn](https://fontawesome.com/docs/web/setup/cdn)
- **XAMPP**: Môi trường phát triển PHP/MySQL: [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)
- **phpMyAdmin**: Quản lý MySQL qua web: [https://www.phpmyadmin.net/](https://www.phpmyadmin.net/)
- **Google Calendar Embed**: Hướng dẫn nhúng lịch: [https://support.google.com/calendar/answer/41207?hl=vi](https://support.google.com/calendar/answer/41207?hl=vi)
- **Hướng dẫn sử dụng JSON trong PHP**: [https://www.php.net/manual/en/function.json-encode.php](https://www.php.net/manual/en/function.json-encode.php)
- **Tài liệu tham khảo lập trình PHP**: [https://www.php.net/manual/vi/](https://www.php.net/manual/vi/)

---

## 📜 Bản quyền

- Dự án phục vụ mục đích học tập, phi thương mại.
- Tác giả: Trần Công Lập