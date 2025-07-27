# SƠ ĐỒ CHỨC NĂNG HỆ THỐNG STUDENTMANAGER

## 🎯 TỔNG QUAN HỆ THỐNG
StudentManager là hệ thống quản lý sinh viên toàn diện, hỗ trợ sinh viên quản lý học tập, tài chính và tài liệu cá nhân.

---

## 🔐 CHỨC NĂNG XÁC THỰC & BẢO MẬT

### 1. Đăng ký tài khoản (`register.php`)
- **Mô tả**: Đăng ký tài khoản mới cho sinh viên
- **Chức năng chính**:
  - ✅ Kiểm tra email hợp lệ (chỉ chấp nhận @student.ctu.edu.vn)
  - ✅ Tự động trích xuất MSSV từ email
  - ✅ Kiểm tra trùng lặp email/MSSV
  - ✅ Mã hóa mật khẩu với bcrypt
  - ✅ Validation dữ liệu đầu vào

### 2. Đăng nhập (`login.php`)
- **Mô tả**: Xác thực người dùng vào hệ thống
- **Chức năng chính**:
  - ✅ Đăng nhập bằng email hoặc MSSV
  - ✅ Xác thực mật khẩu
  - ✅ Tạo session và cookie
  - ✅ Ghi nhớ đăng nhập (Remember me)
  - ✅ Chuyển hướng sau đăng nhập

### 3. Đăng xuất (`logout.php`)
- **Mô tả**: Kết thúc phiên làm việc
- **Chức năng chính**:
  - ✅ Xóa session
  - ✅ Xóa cookie
  - ✅ Chuyển hướng về trang đăng nhập

---

## 🏠 TRANG CHỦ & DASHBOARD (`index.php`)

### 1. Dashboard tổng quan
- **Thống kê nhanh**:
  - 📊 Số lượng ghi chú
  - 💰 Tổng chi tiêu tháng hiện tại
  - 📁 Số lượng tài liệu đã lưu

### 2. Menu chức năng chính
- 📅 **Lịch học**: Xem lịch học và sự kiện
- 📄 **Tài liệu**: Quản lý tài liệu học tập
- 💸 **Chi tiêu**: Theo dõi chi tiêu cá nhân
- 📝 **Ghi chú**: Ghi chú học tập và cá nhân
- 👤 **Thông tin cá nhân**: Cập nhật thông tin

---

## 📅 QUẢN LÝ LỊCH HỌC (`calendar.php`)

### 1. Tích hợp Google Calendar
- **Chức năng chính**:
  - ✅ Hiển thị lịch Google Calendar
  - ✅ Cài đặt Calendar ID
  - ✅ Xem lịch theo tuần/tháng
  - ✅ Múi giờ Việt Nam
  - ✅ Responsive design

### 2. Quản lý cài đặt
- **Cài đặt Calendar ID**:
  - Nhập Google Calendar ID
  - Lưu vào database
  - Cập nhật real-time

---

## 📄 QUẢN LÝ TÀI LIỆU (`documents.php`)

### 1. Upload tài liệu
- **Chức năng chính**:
  - ✅ Upload nhiều file cùng lúc
  - ✅ Hỗ trợ nhiều định dạng (PDF, DOC, DOCX, PPT, XLS, TXT, JPG, PNG)
  - ✅ Giới hạn kích thước file (50MB)
  - ✅ Tạo tên file an toàn
  - ✅ Lưu trữ theo thư mục user

### 2. Quản lý tài liệu
- **Thông tin tài liệu**:
  - 📝 Tiêu đề và mô tả
  - 🏷️ Danh mục (study, work, personal, other)
  - 📚 Môn học
  - 📅 Ngày tạo
  - 📏 Kích thước file

### 3. Tìm kiếm và lọc
- **Bộ lọc**:
  - Theo danh mục
  - Theo môn học
  - Theo ngày tạo
  - Tìm kiếm theo tên

### 4. Thao tác tài liệu
- ✅ **Xem chi tiết**: Thông tin đầy đủ
- ✅ **Tải xuống**: Download file gốc
- ✅ **Xóa**: Xóa tài liệu và file

---

## 💰 QUẢN LÝ CHI TIÊU (`expenses.php`)

### 1. Thêm chi tiêu
- **Thông tin chi tiêu**:
  - 💵 Số tiền
  - 🏷️ Danh mục (food, transport, study, entertainment, other)
  - 📝 Mô tả
  - 📅 Ngày chi tiêu
  - 💳 Phương thức thanh toán

### 2. Thống kê chi tiêu
- **Báo cáo**:
  - 📊 Tổng chi tiêu tháng
  - 💸 Chi tiêu hôm nay
  - 📈 Biểu đồ theo danh mục
  - 📅 Thống kê theo thời gian

### 3. Quản lý chi tiêu
- **Bộ lọc**:
  - Theo danh mục
  - Theo tháng
  - Theo phương thức thanh toán
- **Thao tác**:
  - ✅ Thêm chi tiêu mới
  - ✅ Xóa chi tiêu
  - ✅ Xem chi tiết

---

## 📝 QUẢN LÝ GHI CHÚ (`notes.php`)

### 1. Tạo ghi chú
- **Thông tin ghi chú**:
  - 📝 Tiêu đề
  - 📄 Nội dung (hỗ trợ text dài)
  - 🏷️ Danh mục (study, personal, work, idea, other)
  - 📅 Ngày tạo/cập nhật

### 2. Quản lý ghi chú
- **Thao tác**:
  - ✅ Thêm ghi chú mới
  - ✅ Chỉnh sửa ghi chú
  - ✅ Xóa ghi chú
  - ✅ Xem chi tiết

### 3. Tìm kiếm và lọc
- **Bộ lọc**:
  - Theo danh mục
  - Tìm kiếm theo tiêu đề/nội dung
  - Sắp xếp theo thời gian

---

## 👤 QUẢN LÝ THÔNG TIN CÁ NHÂN (`profile.php`)

### 1. Thông tin cá nhân
- **Thông tin cơ bản**:
  - 👤 Họ và tên
  - 📧 Email (không thay đổi được)
  - 🆔 MSSV (không thay đổi được)
  - 📱 Số điện thoại
  - 🎂 Ngày sinh

### 2. Bảo mật
- **Đổi mật khẩu**:
  - ✅ Xác thực mật khẩu hiện tại
  - ✅ Mật khẩu mới (tối thiểu 6 ký tự)
  - ✅ Xác nhận mật khẩu
  - ✅ Validation độ mạnh mật khẩu

### 3. Cập nhật thông tin
- ✅ Cập nhật thông tin cá nhân
- ✅ Validation dữ liệu
- ✅ Thông báo thành công/lỗi

---

## 🗄️ CƠ SỞ DỮ LIỆU

### 1. Bảng `users`
- Thông tin người dùng
- Xác thực và bảo mật
- Cài đặt Google Calendar

### 2. Bảng `documents`
- Thông tin tài liệu
- Đường dẫn file
- Phân loại và môn học

### 3. Bảng `expenses`
- Thông tin chi tiêu
- Danh mục và phương thức thanh toán
- Thống kê theo thời gian

### 4. Bảng `notes`
- Nội dung ghi chú
- Danh mục và thời gian
- Lịch sử cập nhật

---

## 🎨 GIAO DIỆN & UX

### 1. Thiết kế
- 🎨 **Modern UI**: Sử dụng Tailwind CSS
- 📱 **Responsive**: Tương thích mobile/desktop
- 🎯 **User-friendly**: Giao diện trực quan
- ⚡ **Performance**: Tối ưu tốc độ tải

### 2. Thông báo
- ✅ **Toast notifications**: Thông báo thành công/lỗi
- 🔔 **Real-time feedback**: Phản hồi ngay lập tức
- 📊 **Loading states**: Hiển thị trạng thái tải

### 3. Bảo mật
- 🔒 **Session management**: Quản lý phiên làm việc
- 🛡️ **Input validation**: Kiểm tra dữ liệu đầu vào
- 🔐 **Password hashing**: Mã hóa mật khẩu
- 🚫 **Access control**: Kiểm soát quyền truy cập

---

## 📊 BIỂU ĐỒ LUỒNG CHỨC NĂNG

```
┌─────────────────┐
│   Đăng nhập     │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│   Dashboard     │
└─────────┬───────┘
          │
    ┌─────┴─────┐
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│ Lịch học│ │Tài liệu │
└─────────┘ └─────────┘
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│Chi tiêu │ │ Ghi chú │
└─────────┘ └─────────┘
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│ Profile │ │ Logout  │
└─────────┘ └─────────┘
```

---

## 🔧 CÔNG NGHỆ SỬ DỤNG

### Backend
- **PHP**: Ngôn ngữ lập trình chính
- **MySQL**: Cơ sở dữ liệu
- **PDO**: Kết nối database an toàn

### Frontend
- **HTML5**: Cấu trúc trang
- **Tailwind CSS**: Framework CSS
- **JavaScript**: Tương tác client-side
- **Font Awesome**: Icons

### Bảo mật
- **bcrypt**: Mã hóa mật khẩu
- **Session**: Quản lý phiên làm việc
- **Input sanitization**: Làm sạch dữ liệu
- **SQL injection prevention**: Bảo vệ database

---

## 📈 TÍNH NĂNG NỔI BẬT

1. **🎯 Tập trung vào sinh viên**: Thiết kế riêng cho sinh viên CTU
2. **📱 Responsive**: Hoạt động tốt trên mọi thiết bị
3. **🔒 Bảo mật cao**: Bảo vệ thông tin người dùng
4. **⚡ Hiệu suất tốt**: Tối ưu tốc độ tải trang
5. **🎨 Giao diện đẹp**: Thiết kế hiện đại, dễ sử dụng
6. **📊 Thống kê trực quan**: Dashboard với biểu đồ
7. **🔍 Tìm kiếm thông minh**: Bộ lọc và tìm kiếm nâng cao
8. **📅 Tích hợp lịch**: Kết nối Google Calendar 