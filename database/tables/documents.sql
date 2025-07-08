-- ===================================================================
-- BẢNG DOCUMENTS - QUẢN LÝ TÀI LIỆU HỌC TẬP
-- ===================================================================
-- Mục đích: Lưu trữ và quản lý tài liệu học tập của sinh viên
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(300) NOT NULL COMMENT 'Tiêu đề tài liệu',
    description TEXT COMMENT 'Mô tả chi tiết',
    category ENUM('lecture', 'assignment', 'exam', 'reference', 'note', 'slide', 'book', 'other') DEFAULT 'other' COMMENT 'Danh mục tài liệu',
    subject VARCHAR(200) COMMENT 'Môn học liên quan',
    subject_code VARCHAR(50) COMMENT 'Mã môn học',
    file_name VARCHAR(500) NOT NULL COMMENT 'Tên file gốc',
    file_path VARCHAR(1000) NOT NULL COMMENT 'Đường dẫn file đã lưu',
    file_size BIGINT COMMENT 'Kích thước file (bytes)',
    file_type VARCHAR(100) COMMENT 'Loại file (extension)',
    mime_type VARCHAR(200) COMMENT 'MIME type',
    download_count INT DEFAULT 0 COMMENT 'Số lần tải xuống',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Có chia sẻ công khai không',
    is_favorite BOOLEAN DEFAULT FALSE COMMENT 'Có đánh dấu yêu thích không',
    tags VARCHAR(1000) COMMENT 'Các tag để tìm kiếm',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày upload',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài liệu học tập';

-- Hiển thị thông báo
SELECT 'Bảng documents đã được tạo thành công!' as status; 