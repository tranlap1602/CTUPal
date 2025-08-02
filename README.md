# CTUPal

**CTUPal** là ứng dụng web PHP thuần, giúp sinh viên quản lý toàn diện thông tin cá nhân, lịch học, tài liệu, chi tiêu và ghi chú học tập. Ứng dụng có giao diện hiện đại, tối ưu cho cả desktop và mobile, sử dụng Tailwind CSS, tích hợp nhiều tính năng hữu ích phục vụ nhu cầu học tập và sinh hoạt của sinh viên. Bao gồm cả hệ thống quản trị viên để quản lý tài khoản người dùng.

---

## 📋 Mục lục

1. [Tính năng nổi bật](#tính-năng-nổi-bật)
2. [Cấu trúc thư mục dự án](#cấu-trúc-thư-mục-dự-án)
3. [Công nghệ sử dụng](#công-nghệ-sử-dụng)
4. [Hướng dẫn cài đặt & triển khai](#hướng-dẫn-cài-đặt--triển-khai)
5. [Tài khoản mẫu](#tài-khoản-mẫu)
6. [Mô tả chi tiết các module](#mô-tả-chi-tiết-các-module)
7. [Bảo mật & Quy tắc dữ liệu](#bảo-mật--quy-tắc-dữ-liệu)
8. [Hướng dẫn Admin](#hướng-dẫn-admin)
9. [Giao diện & Trải nghiệm người dùng](#giao-diện--trải-nghiệm-người-dùng-uiux)
10. [Nguồn tham khảo](#nguồn-tham-khảo)
11. [Bản quyền](#bản-quyền)
12. [Kết luận](#kết-luận)

---

## 🎯 Tính năng nổi bật

### Dành cho Sinh viên:
- **Quản lý lịch học:** Nhúng Google Calendar, lưu & xem lịch học cá nhân/lớp trực tiếp trên ứng dụng.
- **Quản lý tài liệu:** Upload, phân loại, tìm kiếm, tải về, xóa tài liệu học tập (PDF, Word, Excel, PowerPoint, hình ảnh...).
- **Quản lý chi tiêu:** Thêm, xóa, thống kê chi tiêu theo tháng, ngày, loại, phương thức thanh toán; hiển thị biểu đồ trực quan bằng Chart.js.
- **Ghi chú học tập/cá nhân:** Thêm, sửa, xóa, phân loại ghi chú, hỗ trợ lọc theo danh mục.
- **Quản lý thông tin cá nhân:** Cập nhật họ tên, mật khẩu, số điện thoại, ngày sinh.

### Dành cho Quản trị viên:
- **Dashboard quản trị:** Thống kê tổng quan về số lượng tài khoản, người dùng hoạt động.
- **Quản lý tài khoản:** Thêm, sửa, xóa, khóa/mở khóa tài khoản sinh viên.
- **Báo cáo thống kê:** Biểu đồ trực quan về tình trạng tài khoản và xu hướng đăng ký.
- **Bảo mật dữ liệu:** Admin chỉ quản lý tài khoản, không thể truy cập dữ liệu cá nhân của sinh viên.

### Tính năng chung:
- **Đăng nhập/Đăng xuất:** Bảo mật bằng session, phân quyền admin/user, chức năng "Ghi nhớ đăng nhập".
- **Giao diện hiện đại:** Responsive, sử dụng Tailwind CSS, icon FontAwesome, hiệu ứng động đẹp mắt.
- **Toast thông báo:** Hiển thị thông báo trạng thái thao tác (thành công/lỗi) thân thiện với người dùng.

---

## 🗂️ Cấu trúc thư mục dự án

```
CTUPal/
├── admin/                # Hệ thống quản trị
│   ├── index.php         # Dashboard admin
│   └── users.php         # Quản lý tài khoản sinh viên
├── assets/
│   ├── icon/
│   │   └── logo.svg      # Logo ứng dụng
│   └── js/
│       ├── charts.js     # File custom vẽ biểu đồ chi tiêu (Chart.js)
│       └── toast.js      # Hệ thống thông báo toast
├── config/
│   └── db.php            # Cấu hình & hàm kết nối CSDL, tiện ích truy vấn
├── includes/
│   ├── header.php        # Header, navigation, breadcrumb, user info
│   └── footer.php        # Footer, JS chung, back-to-top, modal
├── src/
│   ├── input.css         # File nguồn Tailwind CSS
│   └── output.css        # File CSS đã build
├── uploads/              # Thư mục lưu file upload (tài liệu, ảnh)
├── views/
│   ├── documents-view.php # View hiển thị tài liệu
│   ├── expenses-view.php  # View hiển thị chi tiêu
│   └── notes-view.php     # View hiển thị ghi chú
├── calendar.php          # Quản lý lịch học (Google Calendar)
├── database.sql          # File khởi tạo CSDL MySQL
├── documents.php         # Quản lý tài liệu
├── expenses.php          # Quản lý chi tiêu
├── index.php             # Dashboard tổng quan sinh viên
├── login.php             # Đăng nhập (hỗ trợ cả user và admin)
├── logout.php            # Đăng xuất
├── notes.php             # Quản lý ghi chú
├── profile.php           # Quản lý thông tin cá nhân
├── register.php          # Đăng ký tài khoản mới
├── ADMIN_GUIDE.md        # Hướng dẫn sử dụng hệ thống admin
├── package.json          # Quản lý dependency Tailwind CSS
└── package-lock.json
```

**Lưu ý:**  
- Thư mục `uploads/` **bắt buộc** phải có quyền ghi (write) để lưu file upload cho từng user. Nếu không, chức năng upload tài liệu sẽ không hoạt động.
- File `database.sql` chứa toàn bộ cấu trúc và dữ liệu mẫu cho CSDL.
- Cấu trúc database đã được đơn giản hóa, không sử dụng index phụ để phù hợp với quy mô nhỏ.

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

- Giải nén/copy toàn bộ thư mục `CTUPal` vào `C:/xampp/htdocs/CTUPal`.
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

- Truy cập: [http://localhost/CTUPal/login.php](http://localhost/CTUPal/login.php)
- Đăng nhập bằng tài khoản mẫu hoặc tự đăng ký tài khoản mới.

---

## 👤 Tài khoản mẫu

Sau khi import database, có sẵn các tài khoản sau:

### Tài khoản Quản trị viên:
- **Email/MSSV:** `admin@studentmanager.com` hoặc `ADMIN001`
- **Mật khẩu:** `admin123`
- **Quyền:** Admin (quản lý tài khoản, xem thống kê)

### Tài khoản Sinh viên mẫu:
Bạn có thể tự đăng ký tài khoản sinh viên mới hoặc liên hệ admin để được tạo tài khoản.

---

## 📝 Mô tả chi tiết các module

### 1. Hệ thống Đăng nhập/Đăng xuất

- **Đăng nhập đa tài khoản:** Hỗ trợ đăng nhập bằng email hoặc MSSV.
- **Phân quyền:** Tự động phân quyền admin/user và chuyển hướng tương ứng.
- **Bảo mật:** Session-based authentication, mã hóa mật khẩu bằng bcrypt.
- **Ghi nhớ đăng nhập:** Tùy chọn "Remember me" với cookie bảo mật.
- **Kiểm tra trạng thái:** Tài khoản bị khóa sẽ không thể đăng nhập.

### 2. Dashboard Sinh viên (index.php)

- **Thống kê tổng quan:** Số ghi chú, tổng chi tiêu tháng hiện tại, số tài liệu đã upload.
- **Navigation nhanh:** Truy cập trực tiếp đến 5 chức năng chính từ dashboard.
- **Giao diện thân thiện:** Card-based layout với màu sắc phân biệt từng module.

### 3. Dashboard Admin (admin/index.php)

- **Thống kê hệ thống:** Tổng số tài khoản, tài khoản hoạt động, tài khoản bị khóa.
- **Danh sách người dùng mới:** 5 tài khoản đăng ký gần đây nhất.
- **Quản lý nhanh:** Link trực tiếp đến quản lý tài khoản và báo cáo.

### 4. Quản lý Tài khoản Admin (admin/users.php)

- **Danh sách tài khoản:** Hiển thị tất cả tài khoản sinh viên với phân trang.
- **Tìm kiếm:** Tìm kiếm theo tên, email, MSSV.
- **Thêm tài khoản:** Tạo tài khoản mới cho sinh viên với validation đầy đủ.
- **Chỉnh sửa:** Cập nhật thông tin cá nhân, thay đổi trạng thái tài khoản.
- **Khóa/Mở khóa:** Quản lý trạng thái hoạt động của tài khoản.
- **Xóa tài khoản:** Xóa tài khoản với xác nhận (không thể xóa admin).
- **Bảo mật:** Admin không thể truy cập dữ liệu cá nhân (tài liệu, chi tiêu, ghi chú).

### 5. Lịch học (calendar.php)

- Nhập Google Calendar ID, nhúng lịch vào trang.
- Hướng dẫn lấy Google Calendar ID chi tiết.
- Hỗ trợ xem lịch học cá nhân/lớp trực tiếp trên web.

### 6. Quản lý tài liệu (documents.php, views/documents-view.php)

- Upload nhiều file cùng lúc, phân loại theo môn/danh mục.
- **Danh mục tài liệu:** Bài giảng, Bài tập, Thi cử, Tài liệu tham khảo, Khác.
- Lọc, tìm kiếm, tải về, xóa file tài liệu.
- Hỗ trợ các định dạng: PDF, Word, Excel, PowerPoint, hình ảnh...

### 7. Quản lý chi tiêu (expenses.php, views/expenses-view.php)

- Thêm/xóa chi tiêu, lọc theo tháng, loại, phương thức.
- Thống kê tổng tiền, hiển thị biểu đồ trực quan bằng Chart.js.
- **Danh mục chi tiêu:** Ăn uống, Di chuyển, Học tập, Giải trí, Mua sắm, Y tế, Khác.
- **Phương thức thanh toán:** Tiền mặt, Thẻ ngân hàng.
- Dữ liệu biểu đồ truyền từ PHP sang JS, cập nhật realtime.

### 8. Quản lý ghi chú (notes.php, views/notes-view.php)

- Thêm/sửa/xóa ghi chú, phân loại, lọc theo danh mục.
- **Danh mục ghi chú:** Học tập, Cá nhân, Công việc, Ý tưởng, Khác.
- Hỗ trợ ghi chú học tập, cá nhân, nhắc việc...

### 9. Quản lý thông tin cá nhân (profile.php)

- Cập nhật họ tên, đổi mật khẩu (kiểm tra bảo mật), số điện thoại, ngày sinh.
- Đảm bảo bảo mật thông tin cá nhân.

### 10. Đăng ký tài khoản (register.php)

- **Đăng ký tự do:** Sinh viên có thể tự đăng ký tài khoản mới.
- **Validation:** Kiểm tra email và MSSV không trùng lặp.
- **Bảo mật:** Mật khẩu được mã hóa trước khi lưu vào database.
- **Kích hoạt:** Tài khoản mới được kích hoạt tự động.

---

## 🔒 Bảo mật & Quy tắc dữ liệu

### Bảo mật chung:
- **Session Management:** Sử dụng session PHP để bảo vệ tất cả chức năng, tự động đăng xuất khi hết phiên.
- **Phân quyền nghiêm ngặt:** Admin và User có quyền truy cập hoàn toàn khác nhau.
- **Mã hóa mật khẩu:** Sử dụng bcrypt để hash mật khẩu trước khi lưu database.
- **Validation đầu vào:** Kiểm tra và sanitize dữ liệu ở cả client (JS) và server (PHP).

### Bảo mật file upload:
- **Kiểm soát loại file:** Chỉ cho phép upload các định dạng an toàn đã định nghĩa.
- **Giới hạn kích thước:** Tối đa 20MB mỗi file để tránh tấn công DoS.
- **Thư mục riêng biệt:** Mỗi user có thư mục riêng, không thể truy cập file của người khác.
- **Path validation:** Kiểm tra đường dẫn file để ngăn chặn directory traversal.

### Quyền riêng tư dữ liệu:
- **Nguyên tắc thiết kế:** Admin chỉ quản lý tài khoản, KHÔNG thể truy cập dữ liệu cá nhân.
- **Dữ liệu cá nhân:** Tài liệu, chi tiêu, ghi chú chỉ chủ sở hữu mới xem được.
- **Tách biệt hoàn toàn:** Admin và User có dashboard riêng biệt.

---

## 👨‍💼 Hướng dẫn Admin

Để biết chi tiết cách sử dụng hệ thống quản trị viên, vui lòng tham khảo file **[ADMIN_GUIDE.md](ADMIN_GUIDE.md)** bao gồm:

- 🔑 Cách đăng nhập bằng tài khoản admin
- 📊 Sử dụng Dashboard admin với các thống kê
- 👥 Quản lý tài khoản sinh viên (thêm, sửa, xóa, khóa/mở khóa)
- 🔍 Tìm kiếm và lọc tài khoản
- 📈 Xem báo cáo và biểu đồ thống kê
- 🛡️ Nguyên tắc bảo mật và quyền riêng tư
- 🔧 Xử lý sự cố thường gặp

---

## 💎 Giao diện & Trải nghiệm người dùng (UI/UX)

- **Responsive Design:** Giao diện hiện đại, tối ưu cho cả desktop và mobile.
- **Interactive Elements:** Hiệu ứng hover, transform, shadow để tăng tính tương tác.
- **Toast Notifications:** Thông báo trạng thái thao tác rõ ràng (thành công/lỗi/cảnh báo).
- **Form Validation:** Kiểm tra dữ liệu realtime, báo lỗi chi tiết và thân thiện.
- **Color-coded Modules:** Mỗi chức năng có màu sắc riêng biệt để dễ nhận diện.
- **Confirmation Dialogs:** Các thao tác quan trọng (xóa, khóa tài khoản) đều có xác nhận.

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

- **Mục đích:** Dự án phục vụ mục đích học tập và nghiên cứu, phi thương mại.
- **Tác giả:** **Trần Công Lập**
- **Phiên bản:** 1.0.0
- **Ngôn ngữ:** Tiếng Việt
- **Đóng góp:** Mọi góp ý, phản hồi và đóng góp đều được hoan nghênh.

---

## 🎉 Kết luận

**CTUPal** là một giải pháp quản lý toàn diện dành cho sinh viên với:
- ✅ **Dễ cài đặt:** Chỉ cần XAMPP và vài bước đơn giản
- ✅ **Bảo mật cao:** Phân quyền rõ ràng, bảo vệ dữ liệu cá nhân
- ✅ **Giao diện đẹp:** Responsive, hiện đại với Tailwind CSS
- ✅ **Tính năng đầy đủ:** Từ quản lý học tập đến chi tiêu cá nhân
- ✅ **Hệ thống admin:** Quản lý tài khoản hiệu quả

**Chúc bạn cài đặt và sử dụng thành công CTUPal!** 🚀  
Nếu có thắc mắc, vui lòng tham khảo [ADMIN_GUIDE.md](ADMIN_GUIDE.md) hoặc liên hệ tác giả.