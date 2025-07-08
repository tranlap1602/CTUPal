-- ===================================================================
-- BẢNG NOTES - QUẢN LÝ GHI CHÚ
-- ===================================================================
-- Mục đích: Lưu trữ ghi chú, todo và ý tưởng của sinh viên
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(300) NOT NULL COMMENT 'Tiêu đề ghi chú',
    content TEXT NOT NULL COMMENT 'Nội dung ghi chú',
    content_type ENUM('text', 'markdown', 'html') DEFAULT 'text' COMMENT 'Định dạng nội dung',
    category ENUM('study', 'personal', 'work', 'idea', 'todo', 'meeting', 'research', 'project', 'other') DEFAULT 'other' COMMENT 'Danh mục ghi chú',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium' COMMENT 'Mức độ ưu tiên',
    status ENUM('draft', 'active', 'completed', 'archived') DEFAULT 'active' COMMENT 'Trạng thái ghi chú',
    tags VARCHAR(1000) COMMENT 'Các tag để tìm kiếm',
    color VARCHAR(20) COMMENT 'Màu sắc đánh dấu',
    is_pinned BOOLEAN DEFAULT FALSE COMMENT 'Có ghim lên đầu không',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Có chia sẻ công khai không',
    reminder_date DATETIME COMMENT 'Ngày nhắc nhở',
    due_date DATE COMMENT 'Ngày đến hạn (cho todo)',
    completion_date DATETIME COMMENT 'Ngày hoàn thành',
    attachments TEXT COMMENT 'Đường dẫn file đính kèm (JSON)',
    view_count INT DEFAULT 0 COMMENT 'Số lần xem',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý ghi chú cá nhân';

-- Hiển thị thông báo
SELECT 'Bảng notes đã được tạo thành công!' as status; 