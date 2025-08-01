# Hướng dẫn sử dụng Admin Dashboard

## Tổng quan
Admin Dashboard là giao diện quản lý dành cho quản trị viên hệ thống Student Manager. Hệ thống tập trung vào việc quản lý tài khoản sinh viên, đảm bảo quyền riêng tư của dữ liệu cá nhân.

## Đăng nhập
1. Truy cập trang đăng nhập: `http://localhost/StudentManager/login.php`
2. Sử dụng tài khoản admin mặc định:
   - **Email:** admin@studentmanager.com
   - **MSSV:** ADMIN001
   - **Mật khẩu:** admin123
3. Sau khi đăng nhập thành công, bạn sẽ được chuyển hướng đến Admin Dashboard

## Admin Dashboard

### Thống kê tổng quan
Dashboard hiển thị 3 thống kê chính:
- **Tổng số sinh viên:** Tổng số tài khoản sinh viên trong hệ thống
- **Tài khoản hoạt động:** Số tài khoản đang hoạt động
- **Tài khoản bị khóa:** Số tài khoản đã bị khóa

### Menu quản lý
- **Quản lý tài khoản:** Thêm, sửa, xóa và quản lý trạng thái tài khoản sinh viên
- **Báo cáo thống kê:** Xem biểu đồ và báo cáo chi tiết

### Sinh viên mới đăng ký
Hiển thị danh sách 5 sinh viên mới đăng ký gần đây nhất với thông tin:
- Tên sinh viên
- Email
- MSSV
- Trạng thái tài khoản
- Ngày đăng ký

## Quản lý tài khoản

### Truy cập
Từ Admin Dashboard, nhấp vào "Quản lý tài khoản" hoặc truy cập trực tiếp: `http://localhost/StudentManager/admin/users.php`

### Chức năng chính

#### 1. Xem danh sách tài khoản
- Hiển thị tất cả tài khoản sinh viên với thông tin chi tiết
- Phân trang (10 tài khoản/trang)
- Tìm kiếm theo tên, email, MSSV

#### 2. Thêm tài khoản mới
1. Nhấp vào nút "Thêm tài khoản"
2. Điền thông tin bắt buộc:
   - **Tên:** Họ và tên sinh viên
   - **Email:** Địa chỉ email (phải hợp lệ)
   - **MSSV:** Mã số sinh viên (duy nhất)
   - **Mật khẩu:** Mật khẩu đăng nhập
3. Điền thông tin tùy chọn:
   - **Số điện thoại:** Số điện thoại liên hệ
   - **Ngày sinh:** Ngày sinh của sinh viên
4. Nhấp "Lưu" để tạo tài khoản

#### 3. Chỉnh sửa tài khoản
1. Nhấp vào nút "Sửa" bên cạnh tài khoản cần chỉnh sửa
2. Cập nhật thông tin cần thiết
3. Bật/tắt trạng thái "Hoạt động" để khóa/mở khóa tài khoản
4. Nhấp "Cập nhật" để lưu thay đổi

#### 4. Xóa tài khoản
1. Nhấp vào nút "Xóa" bên cạnh tài khoản cần xóa
2. Xác nhận việc xóa
3. **Lưu ý:** Không thể xóa tài khoản admin

#### 5. Khóa/Mở khóa tài khoản
- Sử dụng nút "Khóa/Mở khóa" để thay đổi trạng thái tài khoản
- Tài khoản bị khóa sẽ không thể đăng nhập vào hệ thống

### Tìm kiếm và lọc
- Sử dụng ô tìm kiếm để tìm tài khoản theo tên, email hoặc MSSV
- Kết quả tìm kiếm được hiển thị ngay lập tức

## Báo cáo thống kê

### Truy cập
Từ Admin Dashboard, nhấp vào "Báo cáo thống kê" hoặc truy cập trực tiếp: `http://localhost/StudentManager/admin/reports.php`

### Biểu đồ và thống kê

#### 1. Thống kê tổng quan
- Tổng số sinh viên
- Số tài khoản hoạt động
- Số tài khoản bị khóa

#### 2. Biểu đồ trạng thái tài khoản
- Biểu đồ tròn hiển thị tỷ lệ tài khoản hoạt động vs bị khóa
- Màu xanh: Tài khoản hoạt động
- Màu đỏ: Tài khoản bị khóa

#### 3. Biểu đồ đăng ký theo tháng
- Biểu đồ đường hiển thị số sinh viên đăng ký trong 6 tháng gần đây
- Giúp theo dõi xu hướng tăng trưởng

#### 4. Danh sách sinh viên mới đăng ký
- Hiển thị 5 sinh viên đăng ký gần đây nhất
- Thông tin: Tên, email, MSSV, ngày đăng ký

## Bảo mật và quyền riêng tư

### Nguyên tắc thiết kế
- **Quyền riêng tư:** Tài liệu và chi tiêu là tài sản cá nhân, admin không thể truy cập
- **Quản lý tài khoản:** Admin chỉ quản lý thông tin tài khoản và trạng thái hoạt động
- **Bảo mật:** Mật khẩu được mã hóa bằng bcrypt

### Quyền admin
- Xem danh sách tất cả tài khoản sinh viên
- Thêm, sửa, xóa tài khoản sinh viên
- Khóa/mở khóa tài khoản
- Xem thống kê tổng quan
- **Không thể:** Truy cập tài liệu, chi tiêu, ghi chú của sinh viên

## Xử lý sự cố

### Lỗi thường gặp

#### 1. Không thể đăng nhập admin
- Kiểm tra thông tin đăng nhập
- Đảm bảo tài khoản có role 'admin'
- Kiểm tra trạng thái tài khoản (không bị khóa)

#### 2. Lỗi khi thêm tài khoản
- Kiểm tra email và MSSV không trùng lặp
- Đảm bảo email có định dạng hợp lệ
- Điền đầy đủ thông tin bắt buộc

#### 3. Không thể xóa tài khoản
- Tài khoản admin không thể xóa
- Kiểm tra quyền truy cập

#### 4. Lỗi cơ sở dữ liệu
- Kiểm tra kết nối database
- Đảm bảo bảng `users` đã được tạo với cấu trúc đúng
- Kiểm tra quyền truy cập database

### Liên hệ hỗ trợ
Nếu gặp vấn đề không thể tự khắc phục, vui lòng:
1. Ghi lại thông báo lỗi chi tiết
2. Chụp màn hình lỗi
3. Liên hệ quản trị viên hệ thống

## Cập nhật và bảo trì

### Backup dữ liệu
- Thực hiện backup cơ sở dữ liệu định kỳ
- Backup file cấu hình và mã nguồn

### Cập nhật hệ thống
- Kiểm tra cập nhật bảo mật
- Cập nhật thư viện và framework
- Test kỹ lưỡng trước khi triển khai

### Monitoring
- Theo dõi log hệ thống
- Kiểm tra hiệu suất database
- Giám sát hoạt động bất thường 