-- Migration: Đơn giản hóa bảng notes
-- Tạo bảng notes mới với cấu trúc đơn giản hơn

-- Xóa bảng cũ nếu tồn tại
DROP TABLE IF EXISTS notes;

-- Tạo bảng notes mới với cấu trúc đơn giản
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề ghi chú',
    content TEXT NOT NULL COMMENT 'Nội dung ghi chú',
    category VARCHAR(100) DEFAULT 'other' COMMENT 'Danh mục: study, personal, work, idea, other',
    subject VARCHAR(200) COMMENT 'Môn học liên quan (lấy từ timetable)',
    priority VARCHAR(20) DEFAULT 'medium' COMMENT 'Mức độ ưu tiên: low, medium, high',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi chú đơn giản';

-- Tạo indexes cho hiệu suất
CREATE INDEX idx_notes_user_id ON notes(user_id);
CREATE INDEX idx_notes_category ON notes(category);
CREATE INDEX idx_notes_priority ON notes(priority);
CREATE INDEX idx_notes_subject ON notes(subject);
CREATE INDEX idx_notes_created_at ON notes(created_at); 