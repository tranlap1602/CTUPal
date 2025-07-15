-- ===================================================================
-- CẬP NHẬT BẢNG NOTES - LOẠI BỎ CÁC TRƯỜNG KHÔNG CẦN THIẾT
-- ===================================================================

USE student_manager;

-- Bước 1: Loại bỏ các cột không cần thiết
ALTER TABLE notes 
DROP COLUMN due_date,
DROP COLUMN reminder_time,
DROP COLUMN is_pinned,
DROP COLUMN is_completed,
DROP COLUMN subject,
DROP COLUMN priority;

-- Bước 2: Loại bỏ các index liên quan đến các cột đã xóa
DROP INDEX idx_notes_subject ON notes;
DROP INDEX idx_notes_priority ON notes;
DROP INDEX idx_notes_due_date ON notes;
DROP INDEX idx_notes_completed ON notes;
DROP INDEX idx_notes_pinned ON notes;

-- Bước 3: Cập nhật comment cho bảng
ALTER TABLE notes COMMENT = 'Bảng ghi chú đơn giản';

-- Bước 4: Hiển thị cấu trúc bảng sau khi cập nhật
DESCRIBE notes;

-- Bước 5: Hiển thị thông tin về các index còn lại
SHOW INDEX FROM notes; 