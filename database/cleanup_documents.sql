-- ===================================================================
-- CLEANUP DOCUMENTS TABLE - LOẠI BỎ CÁC CỘT KHÔNG CẦN THIẾT
-- ===================================================================
-- Mục đích: Bỏ các cột không cần thiết trong bảng documents
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

-- BƯỚC 1: Backup dữ liệu quan trọng trước khi thay đổi (tùy chọn)
SELECT 'Đang chuẩn bị cleanup bảng documents...' as status;

-- BƯỚC 2: Xóa trigger liên quan đến cột download_count
DROP TRIGGER IF EXISTS tr_documents_download_count;

-- BƯỚC 3: Xóa view có tham chiếu đến cột download_count
DROP VIEW IF EXISTS documents_by_subject;

-- BƯỚC 4: Xóa các indexes liên quan đến cột sẽ bỏ
DROP INDEX IF EXISTS idx_documents_semester ON documents;
DROP INDEX IF EXISTS idx_documents_public ON documents;

-- BƯỚC 5: Xóa các cột không cần thiết
ALTER TABLE documents 
DROP COLUMN semester,
DROP COLUMN tags,
DROP COLUMN is_public, 
DROP COLUMN download_count,
DROP COLUMN updated_at;

-- BƯỚC 6: Tạo lại view documents_by_subject (đã bỏ download_count)
CREATE VIEW documents_by_subject AS
SELECT 
    user_id,
    subject,
    category,
    COUNT(*) as total_documents,
    SUM(file_size) as total_size,
    MAX(created_at) as latest_upload
FROM documents
WHERE subject IS NOT NULL
GROUP BY user_id, subject, category
ORDER BY total_documents DESC;

-- BƯỚC 7: Kiểm tra cấu trúc mới
DESCRIBE documents;

-- BƯỚC 8: Hiển thị kết quả
SELECT 'Cleanup hoàn thành!' as status,
       'Đã xóa: semester, tags, is_public, download_count, updated_at' as removed_columns,
       'Bảng documents giờ đã gọn gàng hơn' as message;

-- ===================================================================
-- CẤU TRÚC MỚI CỦA BẢNG DOCUMENTS:
-- ===================================================================
/*
id - Primary key
user_id - ID người dùng  
title - Tiêu đề tài liệu
description - Mô tả
file_name - Tên file gốc
file_path - Đường dẫn lưu trữ
file_size - Kích thước (bytes)
mime_type - Loại MIME
file_extension - Phần mở rộng (.pdf, .doc, ...)
category - Danh mục (lecture, assignment, exam, reference, other)
subject - Môn học liên quan
created_at - Ngày tạo
*/ 