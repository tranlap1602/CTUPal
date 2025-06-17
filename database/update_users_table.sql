-- File: database/update_users_table.sql
-- Mục đích: Cập nhật cấu trúc bảng users theo yêu cầu mới
-- Ngày tạo: [Ngày hiện tại]
-- Mô tả: Tạo lại bảng users với cấu trúc mới phù hợp với register.php và profile.php

-- Xóa bảng cũ nếu tồn tại (CẨNH THẬN: Sẽ mất dữ liệu)
-- DROP TABLE IF EXISTS users;

-- Tạo bảng users mới với cấu trúc phù hợp
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) COMMENT 'Họ và tên sinh viên',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email CTU format: tenb1234567@student.ctu.edu.vn',
    mssv VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã số sinh viên (tự động tách từ email)',
    username VARCHAR(100) UNIQUE NOT NULL COMMENT 'Username để đăng nhập (= email)',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa bằng password_hash',
    phone VARCHAR(20) COMMENT 'Số điện thoại (không bắt buộc)',
    birthday DATE COMMENT 'Ngày sinh (không bắt buộc)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo tài khoản',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài khoản sinh viên CTU';

-- Tạo index cho tìm kiếm nhanh
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_mssv ON users(mssv);
CREATE INDEX idx_users_username ON users(username);

-- Insert dữ liệu mẫu cho testing
INSERT INTO users (name, email, mssv, username, password, phone, birthday) VALUES
('Lap', 'lapb2204945@student.ctu.edu.vn', 'b2204945', 'lapb2204945@student.ctu.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', '2004-01-15'),
('An', 'anb2204946@student.ctu.edu.vn', 'b2204946', 'anb2204946@student.ctu.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', '2004-05-20'),
('Minh', 'minhb2204947@student.ctu.edu.vn', 'b2204947', 'minhb2204947@student.ctu.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL);

-- Mật khẩu mẫu cho tất cả tài khoản: "password123"

-- Kiểm tra kết quả
SELECT 
    id,
    name,
    email,
    mssv,
    username,
    phone,
    birthday,
    created_at
FROM users
ORDER BY created_at DESC;

-- Thống kê
SELECT 
    COUNT(*) as total_users,
    COUNT(phone) as users_with_phone,
    COUNT(birthday) as users_with_birthday
FROM users; 