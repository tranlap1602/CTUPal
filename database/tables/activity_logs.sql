-- ===================================================================
-- BẢNG ACTIVITY_LOGS - NHẬT KÝ HOẠT ĐỘNG
-- ===================================================================
-- Mục đích: Ghi lại tất cả các hoạt động của người dùng trong hệ thống
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL COMMENT 'Hành động thực hiện',
    table_name VARCHAR(50) COMMENT 'Bảng bị tác động',
    record_id INT COMMENT 'ID bản ghi bị tác động',
    old_values TEXT COMMENT 'Giá trị cũ (JSON)',
    new_values TEXT COMMENT 'Giá trị mới (JSON)',
    ip_address VARCHAR(45) COMMENT 'Địa chỉ IP',
    user_agent TEXT COMMENT 'Thông tin trình duyệt',
    session_id VARCHAR(100) COMMENT 'ID phiên làm việc',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng nhật ký hoạt động hệ thống';

-- Hiển thị thông báo
SELECT 'Bảng activity_logs đã được tạo thành công!' as status; 