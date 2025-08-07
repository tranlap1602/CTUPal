# Sơ đồ Use Case tổng quát - Hệ thống CTUPal

## Giới thiệu

Sơ đồ Use Case này mô tả tổng quan về các chức năng và tương tác của hệ thống CTUPal - ứng dụng quản lý sinh viên Đại học Cần Thơ.

## Các Actor (Tác nhân)

### 1. Sinh viên (Student)
- **Mô tả:** Người dùng chính của hệ thống, là sinh viên Đại học Cần Thơ
- **Quyền hạn:** Quản lý dữ liệu cá nhân, tài liệu, chi tiêu, ghi chú và lịch học
- **Ràng buộc:** Chỉ được đăng ký bằng email có đuôi `@student.ctu.edu.vn`

### 2. Quản trị viên (Administrator)
- **Mô tả:** Người quản lý hệ thống
- **Quyền hạn:** Quản lý tài khoản người dùng, xem thống kê hệ thống
- **Ràng buộc:** Không thể truy cập dữ liệu cá nhân của sinh viên (tài liệu, chi tiêu, ghi chú)

## Các nhóm chức năng chính

### 1. Xác thực & Tài khoản
- **UC01 - Đăng ký tài khoản:** Sinh viên tạo tài khoản mới với email CTU
- **UC02 - Đăng nhập:** Đăng nhập bằng email hoặc MSSV
- **UC03 - Đăng xuất:** Kết thúc phiên làm việc
- **UC04 - Cập nhật thông tin cá nhân:** Chỉnh sửa profile
- **UC05 - Đổi mật khẩu:** Thay đổi mật khẩu bảo mật

### 2. Quản lý Tài liệu
- **UC06 - Upload tài liệu:** Tải lên multiple files (PDF, Word, Excel, PowerPoint, hình ảnh)
- **UC07 - Phân loại tài liệu:** Gán danh mục (Bài giảng, Bài tập, Thi cử, Tài liệu tham khảo, Khác)
- **UC08 - Tìm kiếm tài liệu:** Tìm kiếm theo tên, mô tả, danh mục
- **UC09 - Tải xuống tài liệu:** Download file đã lưu
- **UC10 - Xóa tài liệu:** Xóa file không cần thiết
- **UC11 - Xem danh sách tài liệu:** Hiển thị tất cả tài liệu đã upload

### 3. Quản lý Chi tiêu
- **UC12 - Thêm chi tiêu:** Ghi nhận chi tiêu với danh mục và phương thức thanh toán
- **UC13 - Xem thống kê chi tiêu:** Hiển thị tổng chi tiêu theo thời gian
- **UC14 - Lọc chi tiêu theo tiêu chí:** Lọc theo tháng, danh mục, phương thức
- **UC15 - Xóa chi tiêu:** Xóa bản ghi chi tiêu
- **UC16 - Xem biểu đồ chi tiêu:** Hiển thị chart phân tích chi tiêu

### 4. Quản lý Ghi chú
- **UC17 - Tạo ghi chú mới:** Thêm ghi chú với tiêu đề và nội dung
- **UC18 - Chỉnh sửa ghi chú:** Cập nhật thông tin ghi chú
- **UC19 - Xóa ghi chú:** Xóa ghi chú không cần thiết
- **UC20 - Phân loại ghi chú:** Gán danh mục (Học tập, Cá nhân, Công việc, Ý tưởng, Khác)
- **UC21 - Tìm kiếm ghi chú:** Tìm kiếm theo tiêu đề và nội dung
- **UC22 - Xem danh sách ghi chú:** Hiển thị tất cả ghi chú

### 5. Quản lý Lịch học
- **UC23 - Tích hợp Google Calendar:** Kết nối với Google Calendar cá nhân
- **UC24 - Xem lịch học:** Hiển thị lịch học và sự kiện
- **UC25 - Quản lý sự kiện cá nhân:** Theo dõi các sự kiện quan trọng

### 6. Dashboard
- **UC26 - Xem dashboard sinh viên:** Hiển thị trang chủ dành cho sinh viên
- **UC27 - Xem thống kê tổng quan:** Thống kê số ghi chú, chi tiêu tháng, số tài liệu

### 7. Quản trị Hệ thống (Chỉ dành cho Admin)
- **UC28 - Xem dashboard admin:** Hiển thị trang quản trị
- **UC29 - Quản lý tài khoản người dùng:** Quản lý tổng thể tài khoản
- **UC30 - Thêm tài khoản sinh viên:** Tạo tài khoản mới cho sinh viên
- **UC31 - Chỉnh sửa thông tin người dùng:** Cập nhật thông tin tài khoản
- **UC32 - Khóa/Mở khóa tài khoản:** Quản lý trạng thái tài khoản
- **UC33 - Xóa tài khoản:** Xóa tài khoản không sử dụng
- **UC34 - Tìm kiếm người dùng:** Tìm kiếm theo tên, email, MSSV
- **UC35 - Xem thống kê người dùng:** Thống kê tổng số tài khoản, hoạt động

## Mối quan hệ Use Case

### Include (<<include>>)
- **UC06 → UC07:** Upload tài liệu luôn bao gồm phân loại
- **UC12 → UC16:** Thêm chi tiêu tự động cập nhật biểu đồ
- **UC17 → UC20:** Tạo ghi chú mới bao gồm phân loại
- **UC26 → UC27:** Dashboard sinh viên bao gồm thống kê
- **UC28 → UC35:** Dashboard admin bao gồm thống kê người dùng

### Extend (<<extend>>)
- **UC01 → UC02:** Sau đăng ký có thể mở rộng thành đăng nhập

## Ràng buộc và Quy tắc nghiệp vụ

### Bảo mật và Phân quyền
1. **Email validation:** Chỉ chấp nhận email có đuôi `@student.ctu.edu.vn`
2. **Role-based access:** Admin và User có quyền hạn khác nhau
3. **Data isolation:** Admin không thể truy cập dữ liệu cá nhân của sinh viên
4. **Session management:** Quản lý phiên đăng nhập an toàn

### File Upload
1. **Supported formats:** PDF, Word (.doc, .docx), Excel (.xls, .xlsx), PowerPoint (.ppt, .pptx), Images (.jpg, .png, .gif)
2. **Size limit:** Tối đa 20MB per file
3. **Security:** Kiểm tra file type và scan malware
4. **Storage:** Mỗi user có thư mục riêng biệt

### Validation
1. **Password strength:** Yêu cầu mật khẩu mạnh
2. **Input sanitization:** Làm sạch dữ liệu đầu vào
3. **SQL injection prevention:** Sử dụng prepared statements
4. **XSS protection:** Escape output data

## Công nghệ và Kiến trúc

- **Backend:** PHP 7.4+ (thuần, không framework)
- **Database:** MySQL 5.7+ với InnoDB engine
- **Frontend:** Tailwind CSS 4.x, Chart.js, FontAwesome
- **Integration:** Google Calendar API
- **Security:** bcrypt password hashing, session-based auth

## Lưu ý quan trọng

1. **Responsive Design:** Hỗ trợ đầy đủ trên desktop, tablet và mobile
2. **Performance:** Tối ưu hóa queries và caching
3. **User Experience:** Toast notifications, smooth transitions
4. **Accessibility:** Tuân thủ các chuẩn accessibility cơ bản

---

*Sơ đồ này được tạo bằng PlantUML và có thể được cập nhật dễ dàng khi có thay đổi về yêu cầu hệ thống.*