# BÁO CÁO PHÂN TÍCH YÊU CẦU BÀI TOÁN VÀ XÂY DỰNG DỮ LIỆU CẦN THIẾT
## HỆ THỐNG QUẢN LÝ SINH VIÊN (STUDENTMANAGER)

---

## 1. PHÂN TÍCH YÊU CẦU BÀI TOÁN

### 1.1. Bối cảnh và mục tiêu

#### 1.1.1. Bối cảnh
- Sinh viên đại học thường gặp khó khăn trong việc quản lý thông tin học tập, tài chính và tài liệu cá nhân
- Cần một hệ thống tập trung để quản lý toàn diện các hoạt động học tập và sinh hoạt
- Yêu cầu giao diện thân thiện, dễ sử dụng trên nhiều thiết bị

#### 1.1.2. Mục tiêu chính
- Xây dựng hệ thống quản lý sinh viên toàn diện
- Hỗ trợ quản lý lịch học, tài liệu, chi tiêu và ghi chú
- Cung cấp giao diện hiện đại, responsive
- Đảm bảo bảo mật thông tin người dùng

### 1.2. Phân tích đối tượng người dùng

#### 1.2.1. Đối tượng chính
- **Sinh viên đại học**: Người dùng chính của hệ thống
- **Độ tuổi**: 18-25 tuổi
- **Trình độ kỹ thuật**: Cơ bản đến trung bình
- **Thiết bị sử dụng**: Điện thoại thông minh, máy tính xách tay, máy tính bảng

#### 1.2.2. Đặc điểm người dùng
- Quen thuộc với công nghệ web
- Cần truy cập nhanh thông tin
- Ưa thích giao diện đơn giản, trực quan
- Thường xuyên sử dụng thiết bị di động

### 1.3. Phân tích yêu cầu chức năng

#### 1.3.1. Yêu cầu chức năng chính

**a) Quản lý tài khoản người dùng**
- Đăng ký tài khoản mới
- Đăng nhập/đăng xuất hệ thống
- Quản lý thông tin cá nhân
- Đổi mật khẩu

**b) Quản lý lịch học**
- Tích hợp Google Calendar
- Xem lịch học theo tuần/tháng
- Cài đặt Calendar ID cá nhân

**c) Quản lý tài liệu học tập**
- Upload nhiều file cùng lúc
- Phân loại tài liệu theo môn học và danh mục
- Tìm kiếm và lọc tài liệu
- Tải xuống và xóa tài liệu

**d) Quản lý chi tiêu cá nhân**
- Thêm/sửa/xóa chi tiêu
- Phân loại chi tiêu theo danh mục
- Thống kê chi tiêu theo thời gian
- Hiển thị biểu đồ trực quan

**e) Quản lý ghi chú**
- Tạo/sửa/xóa ghi chú
- Phân loại ghi chú theo danh mục
- Tìm kiếm ghi chú
- Lưu trữ lịch sử cập nhật

#### 1.3.2. Yêu cầu chức năng phụ

**a) Dashboard tổng quan**
- Hiển thị thống kê nhanh
- Truy cập nhanh các chức năng chính
- Giao diện trực quan

**b) Bảo mật hệ thống**
- Mã hóa mật khẩu
- Quản lý session
- Kiểm soát quyền truy cập
- Validation dữ liệu đầu vào

### 1.4. Phân tích yêu cầu phi chức năng

#### 1.4.1. Yêu cầu hiệu suất
- Thời gian phản hồi < 3 giây
- Hỗ trợ đồng thời 100+ người dùng
- Tối ưu tốc độ tải trang

#### 1.4.2. Yêu cầu bảo mật
- Mã hóa mật khẩu bằng bcrypt
- Bảo vệ chống SQL injection
- Validation dữ liệu đầu vào
- Kiểm soát quyền truy cập file

#### 1.4.3. Yêu cầu giao diện
- Responsive design (mobile-first)
- Giao diện hiện đại, thân thiện
- Hỗ trợ đa trình duyệt
- Tương thích với thiết bị di động

#### 1.4.4. Yêu cầu khả năng mở rộng
- Kiến trúc module hóa
- Dễ dàng thêm tính năng mới
- Hỗ trợ nhiều ngôn ngữ
- Tích hợp API bên ngoài

---

## 2. XÂY DỰNG CÁC DỮ LIỆU CẦN THIẾT

### 2.1. Phân tích thực thể và thuộc tính

#### 2.1.1. Thực thể chính

**a) Thực thể USER (Người dùng)**
- **Mô tả**: Thông tin người dùng hệ thống
- **Thuộc tính chính**:
  - `id`: Khóa chính, tự động tăng
  - `name`: Họ và tên người dùng
  - `email`: Email đăng nhập (duy nhất)
  - `mssv`: Mã số sinh viên (duy nhất)
  - `phone`: Số điện thoại
  - `password`: Mật khẩu đã mã hóa
  - `birthday`: Ngày sinh
  - `is_active`: Trạng thái hoạt động
  - `google_calendar_id`: ID Google Calendar
  - `created_at`: Thời gian tạo tài khoản

**b) Thực thể DOCUMENT (Tài liệu)**
- **Mô tả**: Thông tin tài liệu học tập
- **Thuộc tính chính**:
  - `doc_id`: Khóa chính, tự động tăng
  - `user_id`: Khóa ngoại liên kết với USER
  - `title`: Tiêu đề tài liệu
  - `description`: Mô tả tài liệu
  - `file_name`: Tên file gốc
  - `file_path`: Đường dẫn lưu trữ file
  - `file_size`: Kích thước file (bytes)
  - `file_type`: Loại file (PDF, DOC, etc.)
  - `category`: Danh mục tài liệu
  - `subject`: Môn học
  - `created_at`: Thời gian upload

**c) Thực thể EXPENSE (Chi tiêu)**
- **Mô tả**: Thông tin chi tiêu cá nhân
- **Thuộc tính chính**:
  - `expense_id`: Khóa chính, tự động tăng
  - `user_id`: Khóa ngoại liên kết với USER
  - `amount`: Số tiền chi tiêu
  - `category`: Danh mục chi tiêu
  - `description`: Mô tả chi tiêu
  - `expense_date`: Ngày chi tiêu
  - `payment_method`: Phương thức thanh toán
  - `created_at`: Thời gian tạo bản ghi

**d) Thực thể NOTE (Ghi chú)**
- **Mô tả**: Thông tin ghi chú học tập/cá nhân
- **Thuộc tính chính**:
  - `note_id`: Khóa chính, tự động tăng
  - `user_id`: Khóa ngoại liên kết với USER
  - `title`: Tiêu đề ghi chú
  - `content`: Nội dung ghi chú
  - `category`: Danh mục ghi chú
  - `created_at`: Thời gian tạo
  - `updated_at`: Thời gian cập nhật cuối

### 2.2. Thiết kế cơ sở dữ liệu

#### 2.2.1. Sơ đồ quan hệ thực thể (ERD)

```
┌─────────────┐         ┌─────────────┐
│    USERS    │         │  DOCUMENTS  │
├─────────────┤         ├─────────────┤
│ id (PK)     │◄────────┤ user_id (FK)│
│ name        │         │ doc_id (PK) │
│ email       │         │ title       │
│ mssv        │         │ description │
│ phone       │         │ file_name   │
│ password    │         │ file_path   │
│ birthday    │         │ file_size   │
│ is_active   │         │ file_type   │
│ google_cal  │         │ category    │
│ created_at  │         │ subject     │
└─────────────┘         │ created_at  │
                        └─────────────┘
         │
         │
         ▼
┌─────────────┐         ┌─────────────┐
│  EXPENSES   │         │    NOTES    │
├─────────────┤         ├─────────────┤
│ expense_id  │         │ note_id (PK)│
│ user_id (FK)│         │ user_id (FK)│
│ amount      │         │ title       │
│ category    │         │ content     │
│ description │         │ category    │
│ expense_date│         │ created_at  │
│ payment_meth│         │ updated_at  │
│ created_at  │         └─────────────┘
└─────────────┘
```

#### 2.2.2. Định nghĩa bảng chi tiết

**Bảng USERS**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mssv VARCHAR(20) UNIQUE NOT NULL,
    phone VARCHAR(10) NULL,
    password VARCHAR(255) NOT NULL,
    birthday DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    google_calendar_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

**Bảng DOCUMENTS**
```sql
CREATE TABLE documents (
    doc_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    category VARCHAR(100) NULL,
    subject VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

**Bảng EXPENSES**
```sql
CREATE TABLE expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NULL,
    expense_date DATE NOT NULL,
    payment_method VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

**Bảng NOTES**
```sql
CREATE TABLE notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 2.3. Thiết kế dữ liệu danh mục

#### 2.3.1. Danh mục tài liệu (Document Categories)
- **Bài giảng**: Tài liệu bài giảng của giảng viên
- **Bài tập**: Bài tập về nhà, bài tập nhóm
- **Thi cử**: Đề thi, đáp án, tài liệu ôn thi
- **Tài liệu tham khảo**: Sách, bài báo, tài liệu bổ sung
- **Khác**: Các tài liệu khác

#### 2.3.2. Danh mục chi tiêu (Expense Categories)
- **Ăn uống**: Chi phí ăn uống hàng ngày
- **Di chuyển**: Chi phí xăng xe, xe buýt, taxi
- **Học tập**: Sách vở, dụng cụ học tập
- **Giải trí**: Xem phim, chơi game, du lịch
- **Mua sắm**: Quần áo, đồ dùng cá nhân
- **Y tế**: Thuốc men, khám bệnh
- **Khác**: Chi phí khác

#### 2.3.3. Danh mục ghi chú (Note Categories)
- **Học tập**: Ghi chú bài học, công thức
- **Cá nhân**: Ghi chú cá nhân, nhắc nhở
- **Công việc**: Ghi chú công việc, deadline
- **Ý tưởng**: Ý tưởng, sáng kiến
- **Khác**: Ghi chú khác

#### 2.3.4. Phương thức thanh toán (Payment Methods)
- **Tiền mặt**: Thanh toán bằng tiền mặt
- **Thẻ ngân hàng**: Thanh toán bằng thẻ ATM/credit

### 2.4. Thiết kế chỉ mục (Indexing)

#### 2.4.1. Chỉ mục chính
- **Primary Key**: Tất cả các bảng đều có khóa chính tự động tăng
- **Foreign Key**: Các khóa ngoại liên kết giữa các bảng

#### 2.4.2. Lưu ý về hiệu suất
- **Không sử dụng index phụ**: Để đơn giản hóa cấu trúc database
- **Dựa vào Primary Key và Foreign Key**: Đủ cho ứng dụng quy mô nhỏ
- **Tối ưu query**: Sử dụng điều kiện WHERE hiệu quả
- **Phù hợp với dữ liệu ít**: Không cần index phức tạp

### 2.5. Thiết kế ràng buộc và toàn vẹn dữ liệu

#### 2.5.1. Ràng buộc khóa ngoại
- **CASCADE DELETE**: Khi xóa user, tự động xóa tất cả dữ liệu liên quan
- **NOT NULL**: Các trường bắt buộc không được để trống
- **UNIQUE**: Email và MSSV phải duy nhất

#### 2.5.2. Ràng buộc dữ liệu
- **Email**: Chỉ chấp nhận email hợp lệ (@student.ctu.edu.vn)
- **MSSV**: Định dạng chuẩn của trường đại học
- **Phone**: Số điện thoại 10 chữ số
- **Amount**: Số tiền phải > 0
- **File size**: Giới hạn kích thước file upload

#### 2.5.3. Validation dữ liệu
- **Client-side**: Kiểm tra bằng JavaScript
- **Server-side**: Kiểm tra bằng PHP
- **Database**: Ràng buộc ở mức cơ sở dữ liệu

### 2.6. Thiết kế bảo mật dữ liệu

#### 2.6.1. Mã hóa mật khẩu
- Sử dụng bcrypt với salt tự động
- Độ mạnh mật khẩu tối thiểu 6 ký tự
- Không lưu trữ mật khẩu dạng plain text

#### 2.6.2. Bảo vệ dữ liệu
- Mỗi user chỉ truy cập được dữ liệu của mình
- Kiểm tra quyền truy cập file upload
- Sanitize dữ liệu đầu vào
- Bảo vệ chống SQL injection

#### 2.6.3. Quản lý session
- Session timeout tự động
- Xóa session khi logout
- Kiểm tra session trước mỗi request

---

## 3. KẾT LUẬN

### 3.1. Tóm tắt phân tích
- Hệ thống StudentManager được thiết kế để đáp ứng nhu cầu quản lý toàn diện của sinh viên
- Cơ sở dữ liệu được thiết kế tối ưu với 4 bảng chính và các mối quan hệ rõ ràng
- Bảo mật được đảm bảo ở nhiều lớp: client, server và database

### 3.2. Điểm mạnh của thiết kế
- Kiến trúc đơn giản, dễ bảo trì và mở rộng
- Giao diện responsive, thân thiện với người dùng
- Bảo mật cao với nhiều lớp bảo vệ
- Cấu trúc database đơn giản, phù hợp với quy mô nhỏ

### 3.3. Hướng phát triển
- Tích hợp thêm các tính năng mới
- Mở rộng hỗ trợ đa ngôn ngữ
- Tối ưu hóa hiệu suất database
- Thêm tính năng backup và restore

---

**Tác giả**: Trần Công Lập  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0 