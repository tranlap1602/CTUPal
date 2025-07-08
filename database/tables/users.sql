-- ===================================================================
-- BẢNG USERS - QUẢN LÝ TÀI KHOẢN SINH VIÊN CTU
-- ===================================================================
-- Mục đích: Lưu trữ thông tin tài khoản sinh viên
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Họ và tên sinh viên',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email CTU format: tenb1234567@student.ctu.edu.vn',
    mssv VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã số sinh viên (tự động tách từ email)',
    username VARCHAR(100) UNIQUE NOT NULL COMMENT 'Username để đăng nhập (= email)',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa bằng password_hash',
    phone VARCHAR(20) COMMENT 'Số điện thoại (10-11 chữ số)',
    birthday DATE COMMENT 'Ngày sinh (không bắt buộc)',
    avatar_path VARCHAR(500) COMMENT 'Đường dẫn ảnh đại diện',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Trạng thái tài khoản',
    last_login TIMESTAMP NULL COMMENT 'Lần đăng nhập cuối',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo tài khoản',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài khoản sinh viên CTU';

-- Hiển thị thông báo
SELECT 'Bảng users đã được tạo thành công!' as status; 