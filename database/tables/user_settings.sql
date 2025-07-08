-- ===================================================================
-- BẢNG USER_SETTINGS - CÀI ĐẶT NGƯỜI DÙNG
-- ===================================================================
-- Mục đích: Lưu trữ các cài đặt và tùy chọn cá nhân của người dùng
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

CREATE TABLE user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL COMMENT 'Khóa cài đặt',
    setting_value TEXT COMMENT 'Giá trị cài đặt (JSON hoặc text)',
    setting_type ENUM('string', 'number', 'boolean', 'json', 'date') DEFAULT 'string' COMMENT 'Loại dữ liệu',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Có thể truy cập công khai không',
    description VARCHAR(500) COMMENT 'Mô tả cài đặt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys và constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng cài đặt người dùng';

-- Hiển thị thông báo
SELECT 'Bảng user_settings đã được tạo thành công!' as status; 