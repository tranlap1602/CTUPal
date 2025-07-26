# StudentManager

**StudentManager** là ứng dụng web PHP thuần, giúp sinh viên quản lý toàn diện thông tin cá nhân, lịch học, tài liệu, chi tiêu và ghi chú học tập. Ứng dụng có giao diện hiện đại, tối ưu cho cả desktop và mobile, sử dụng Tailwind CSS, tích hợp nhiều tính năng hữu ích phục vụ nhu cầu học tập và sinh hoạt của sinh viên.

---

## 📋 Mục lục

1. [Tính năng nổi bật](#tính-năng-nổi-bật)
2. [Cấu trúc thư mục dự án](#cấu-trúc-thư-mục-dự-án)
3. [Công nghệ sử dụng](#công-nghệ-sử-dụng)
4. [Hướng dẫn cài đặt & triển khai](#hướng-dẫn-cài-đặt--triển-khai)
5. [Tài khoản mẫu](#tài-khoản-mẫu)
6. [Mô tả chi tiết các module](#mô-tả-chi-tiết-các-module)
7. [Nguồn tham khảo](#nguồn-tham-khảo)
8. [Bản quyền](#bản-quyền)

---

## 🎯 Tính năng nổi bật

- **Quản lý lịch học:** Nhúng Google Calendar, lưu & xem lịch học cá nhân/lớp trực tiếp trên ứng dụng.
- **Quản lý tài liệu:** Upload, phân loại, tìm kiếm, tải về, xóa tài liệu học tập (PDF, Word, Excel, PowerPoint, hình ảnh...).
- **Quản lý chi tiêu:** Thêm, xóa, thống kê chi tiêu theo tháng, ngày, loại, phương thức thanh toán; hiển thị biểu đồ trực quan bằng Chart.js.
- **Ghi chú học tập/cá nhân:** Thêm, sửa, xóa, phân loại ghi chú, hỗ trợ lọc theo danh mục.
- **Quản lý thông tin cá nhân:** Cập nhật họ tên, mật khẩu, số điện thoại, ngày sinh.
- **Đăng nhập/Đăng xuất:** Bảo mật bằng session, chỉ user đăng nhập mới truy cập được các chức năng chính.
- **Giao diện hiện đại:** Responsive, sử dụng Tailwind CSS, icon FontAwesome, hiệu ứng động đẹp mắt.

---

## 🗂️ Cấu trúc thư mục dự án

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
├── views/
│   ├── documents-view.php
│   ├── expenses-view.php
│   └── notes-view.php
├── package.json          # Quản lý dependency Tailwind CSS
└── package-lock.json
```

**Lưu ý:**  
- Thư mục `uploads/` **bắt buộc** phải có quyền ghi (write) để lưu file upload cho từng user. Nếu không, chức năng upload tài liệu sẽ không hoạt động.
- File `database.sql` chứa toàn bộ cấu trúc và dữ liệu mẫu cho CSDL.

---

## 🛠️ Công nghệ sử dụng

- **PHP thuần:** Không sử dụng framework, dễ triển khai trên XAMPP, phù hợp sinh viên.
- **MySQL:** Lưu trữ dữ liệu, chuẩn hóa, có khóa ngoại.
- **Tailwind CSS:** Xây dựng giao diện hiện đại, responsive, dễ tuỳ biến.
- **Chart.js:** Vẽ biểu đồ chi tiêu, trực quan, dễ dùng, tích hợp qua CDN.
- **FontAwesome:** Bộ icon phổ biến, tích hợp qua CDN.
- **HTML5, CSS3, JavaScript:** Hiệu ứng UI, xử lý logic phía client.
- **Google Calendar Embed:** Nhúng lịch học cá nhân/lớp.
- **Toast.js:** Thông báo nổi (toast) UI, hiển thị trạng thái thao tác (thành công/lỗi) trên toàn ứng dụng.

---

## ⚙️ Hướng dẫn cài đặt & triển khai

### 1. Chuẩn bị môi trường

- **Cài đặt XAMPP:**  
  Tải và cài đặt XAMPP tại [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).  
  Khởi động Apache và MySQL từ XAMPP Control Panel.

### 2. Tạo cơ sở dữ liệu

- Truy cập [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Tạo database mới tên `student_manager` (hoặc tên khác, nhớ sửa trong `config/db.php`)
- Import file `database.sql` vào database vừa tạo (chọn database, nhấn Import, chọn file, nhấn Go).

### 3. Cấu hình kết nối CSDL

- Mở file `config/db.php`, chỉnh lại thông tin nếu không dùng mặc định:
  - Host: `localhost`
  - Database: `student_manager`
  - User: `root`
  - Password: (để trống nếu dùng mặc định XAMPP)

### 4. Copy source code vào XAMPP

- Giải nén/copy toàn bộ thư mục `StudentManager` vào `C:/xampp/htdocs/StudentManager`.
- Đảm bảo các thư mục con như `uploads/`, `assets/js/` tồn tại và có quyền ghi.

### 5. Cài đặt & build Tailwind CSS (nếu muốn tuỳ chỉnh)

- Đã có sẵn file CSS build sẵn (`src/output.css`). Nếu muốn chỉnh giao diện:
  1. Cài Node.js: [https://nodejs.org/](https://nodejs.org/)
  2. Mở terminal tại thư mục project, chạy:
     ```bash
     npm install tailwindcss @tailwindcss/cli
     npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
     ```
- Khi lưu file CSS, Tailwind sẽ tự động build lại.

### 6. Sử dụng biểu đồ chi tiêu với Chart.js

- **Không cần cài đặt npm**. Chỉ cần nhúng CDN trong file `views/expenses-view.php`:
  ```html
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="assets/js/charts.js"></script>
  ```
- File `assets/js/charts.js` chứa hàm `renderCharts` và plugin custom cho biểu đồ rỗng.
- Dữ liệu biểu đồ được truyền từ PHP sang JS bằng `json_encode`.
- Biểu đồ sẽ tự động hiển thị khi truy cập trang quản lý chi tiêu.

### 7. Chạy ứng dụng

- Truy cập: [http://localhost/StudentManager/login.php](http://localhost/StudentManager/login.php)
- Đăng nhập bằng tài khoản mẫu hoặc tự đăng ký.

---

## 👤 Tài khoản mẫu

Sau khi import database, có sẵn 1 tài khoản:

- **Username/MSSV/Email:** `lapb2204945` hoặc `lapb2204945@student.ctu.edu.vn` hoặc `B2204945`
- **Password:** `sEM3WQYV`

---

## 📝 Mô tả chi tiết các module

### 1. Đăng nhập/Đăng xuất

- Quản lý session, bảo mật, chuyển hướng hợp lý.
- Chỉ user đăng nhập mới truy cập được các chức năng chính.

### 2. Dashboard (index.php)

- Thống kê nhanh số ghi chú, tổng chi tiêu tháng, số tài liệu đã upload.
- Giao diện trực quan, dễ theo dõi.

### 3. Lịch học (calendar.php)

- Nhập Google Calendar ID, nhúng lịch vào trang.
- Hướng dẫn lấy Google Calendar ID chi tiết.
- Hỗ trợ xem lịch học cá nhân/lớp trực tiếp trên web.

### 4. Quản lý tài liệu (documents.php, views/documents-view.php)

- Upload nhiều file cùng lúc, phân loại theo môn/danh mục.
- **Danh mục tài liệu:** Bài giảng, Bài tập, Thi cử, Tài liệu tham khảo, Khác.
- Lọc, tìm kiếm, tải về, xóa file tài liệu.
- Hỗ trợ các định dạng: PDF, Word, Excel, PowerPoint, hình ảnh...

### 5. Quản lý chi tiêu (expenses.php, views/expenses-view.php)

- Thêm/xóa chi tiêu, lọc theo tháng, loại, phương thức.
- Thống kê tổng tiền, hiển thị biểu đồ trực quan bằng Chart.js.
- **Danh mục chi tiêu:** Ăn uống, Di chuyển, Học tập, Giải trí, Mua sắm, Y tế, Khác.
- **Phương thức thanh toán:** Tiền mặt, Thẻ ngân hàng.
- Dữ liệu biểu đồ truyền từ PHP sang JS, cập nhật realtime.

### 6. Quản lý ghi chú (notes.php, views/notes-view.php)

- Thêm/sửa/xóa ghi chú, phân loại, lọc theo danh mục.
- **Danh mục ghi chú:** Học tập, Cá nhân, Công việc, Ý tưởng, Khác.
- Hỗ trợ ghi chú học tập, cá nhân, nhắc việc...

### 7. Quản lý thông tin cá nhân (profile.php)

- Cập nhật họ tên, đổi mật khẩu (kiểm tra bảo mật), số điện thoại, ngày sinh.
- Đảm bảo bảo mật thông tin cá nhân.

---

## 🔒 Bảo mật & Quy tắc dữ liệu

- Ứng dụng sử dụng session để bảo vệ các chức năng chính, chỉ user đăng nhập mới truy cập được.
- Kiểm tra quyền truy cập file upload, mỗi user chỉ truy cập được file của mình.
- Validate dữ liệu đầu vào ở cả phía client (JS) và server (PHP).
- Mật khẩu được mã hóa (hash) khi lưu vào database.
- Thư mục upload được kiểm soát, không cho phép upload file nguy hiểm.

---
## 💎 Giao diện & Trải nghiệm người dùng (UI/UX)

- Giao diện hiện đại, responsive, tối ưu cho cả desktop và mobile.
- Sử dụng hiệu ứng động, toast thông báo trạng thái thao tác.
- Các form có kiểm tra dữ liệu, báo lỗi rõ ràng.
- Các thao tác upload, xóa, cập nhật đều có xác nhận và thông báo.
- Hỗ trợ dark mode (nếu có tuỳ chỉnh Tailwind).

---

## 🌐 Nguồn tham khảo

- **Chart.js:** [https://www.chartjs.org/docs/latest/](https://www.chartjs.org/docs/latest/)
- **Tailwind CSS:** [https://tailwindcss.com/docs/installation](https://tailwindcss.com/docs/installation)
- **FontAwesome:** [https://fontawesome.com/docs/web/setup/cdn](https://fontawesome.com/docs/web/setup/cdn)
- **XAMPP:** [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)
- **phpMyAdmin:** [https://www.phpmyadmin.net/](https://www.phpmyadmin.net/)
- **Google Calendar Embed:** [https://support.google.com/calendar/answer/41207?hl=vi](https://support.google.com/calendar/answer/41207?hl=vi)
- **Hướng dẫn sử dụng JSON trong PHP:** [https://www.php.net/manual/en/function.json-encode.php](https://www.php.net/manual/en/function.json-encode.php)
- **Tài liệu tham khảo lập trình PHP:** [https://www.php.net/manual/vi/](https://www.php.net/manual/vi/)

---

## 📜 Bản quyền

- Dự án phục vụ mục đích học tập, phi thương mại.
- Tác giả: **Trần Công Lập**
- Mọi đóng góp, phản hồi xin gửi về email cá nhân hoặc qua Github.

---

**Chúc bạn cài đặt và sử dụng thành công StudentManager!**  
Nếu có thắc mắc, vui lòng liên hệ tác giả hoặc tham khảo tài liệu hướng dẫn chi tiết ở trên.