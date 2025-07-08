-- ===================================================================
-- IMPORT GUIDE - HƯỚNG DẪN IMPORT CƠ SỞ DỮ LIỆU
-- ===================================================================
-- Mục đích: Hướng dẫn import đầy đủ cơ sở dữ liệu theo đúng thứ tự
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

-- CÁCH SỬ DỤNG:
-- 1. Chạy từng file theo thứ tự bên dưới
-- 2. Hoặc sao chép nội dung từng phần và chạy trực tiếp

-- ===================================================================
-- BƯỚC 1: TẠO DATABASE VÀ CẤU HÌNH CƠ BẢN
-- ===================================================================
SOURCE setup.sql;

-- ===================================================================
-- BƯỚC 2: TẠO CÁC BẢNG (THEO THỨ TỰ DEPENDENCY)
-- ===================================================================
-- Bảng chính (không có foreign key)
SOURCE tables/users.sql;

-- Bảng có foreign key tới users
SOURCE tables/timetable.sql;
SOURCE tables/documents.sql;
SOURCE tables/expenses.sql;
SOURCE tables/notes.sql;
SOURCE tables/user_settings.sql;
SOURCE tables/activity_logs.sql;

-- ===================================================================
-- BƯỚC 3: TẠO INDEXES ĐỂ TỐI ƯU HIỆU SUẤT
-- ===================================================================
SOURCE indexes.sql;

-- ===================================================================
-- BƯỚC 4: TẠO VIEWS ĐỂ THỐNG KÊ DỮ LIỆU
-- ===================================================================
SOURCE views.sql;

-- ===================================================================
-- BƯỚC 5: TẠO STORED PROCEDURES
-- ===================================================================
SOURCE procedures.sql;

-- ===================================================================
-- BƯỚC 6: TẠO TRIGGERS TỰ ĐỘNG
-- ===================================================================
SOURCE triggers.sql;

-- ===================================================================
-- HOÀN THÀNH
-- ===================================================================
SELECT 'HOÀN THÀNH IMPORT DATABASE!' as status,
       'Database student_manager đã sẵn sàng sử dụng' as message,
       'Tổng cộng: 7 bảng, 30+ indexes, 4 views, 3 procedures, 3 triggers' as summary,
       NOW() as completed_at;

-- ===================================================================
-- THÔNG TIN SỬ DỤNG
-- ===================================================================
/*
CÁCH IMPORT TOÀN BỘ:
1. Mở MySQL/MariaDB command line hoặc phpMyAdmin
2. Chạy: SOURCE /path/to/database/import.sql

HOẶC IMPORT TỪNG FILE:
1. setup.sql - Tạo database
2. tables/*.sql - Tạo bảng (7 files)
3. indexes.sql - Tạo indexes
4. views.sql - Tạo views
5. procedures.sql - Tạo procedures  
6. triggers.sql - Tạo triggers

KIỂM TRA SAU KHI IMPORT:
- SHOW TABLES; (phải có 7 bảng)
- SHOW INDEX FROM users; (kiểm tra indexes)
- SHOW CREATE VIEW daily_schedule; (kiểm tra views)
- SHOW PROCEDURE STATUS WHERE Db = 'student_manager'; (kiểm tra procedures)
- SHOW TRIGGERS; (kiểm tra triggers)
*/ 