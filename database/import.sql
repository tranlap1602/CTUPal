-- ===================================================================
-- IMPORT GUIDE - HƯỚNG DẪN IMPORT CƠ SỞ DỮ LIỆU STUDENTMANAGER
-- ===================================================================
-- Mục đích: Import database hoàn chỉnh StudentManager
-- Tác giả: Student Manager System
-- Ngày cập nhật: 2025-01-29
-- Email user: lapb2204945@student.ctu.edu.vn
-- Password: sEM3WQYV
-- ===================================================================

-- CÁCH IMPORT ĐƠN GIẢN:
-- Chỉ cần chạy 1 lệnh duy nhất để có database hoàn chỉnh

-- ===================================================================
-- IMPORT DATABASE HOÀN CHỈNH
-- ===================================================================
SOURCE complete_database.sql;

-- ===================================================================
-- HOÀN THÀNH
-- ===================================================================
SELECT 'IMPORT THÀNH CÔNG!' as status,
       'Database student_manager đã sẵn sàng sử dụng' as message,
       'User: lapb2204945@student.ctu.edu.vn | Password: sEM3WQYV' as login_info,
       NOW() as completed_at;

-- ===================================================================
-- THÔNG TIN SỬ DỤNG
-- ===================================================================
/*
CÁCH IMPORT:
1. Mở MySQL/MariaDB command line hoặc phpMyAdmin
2. Chạy: SOURCE database/import.sql
   HOẶC: SOURCE database/complete_database.sql

THÔNG TIN ĐĂNG NHẬP:
- Email: lapb2204945@student.ctu.edu.vn
- Username: lapb2204945@student.ctu.edu.vn (hoặc B2204945)
- Password: sEM3WQYV

KIỂM TRA SAU KHI IMPORT:
- SHOW TABLES; (phải có 7 bảng)
- SELECT * FROM users; (phải có 1 user)
- SHOW PROCEDURE STATUS WHERE Db = 'student_manager';
- SHOW TRIGGERS;

TÍNH NĂNG DATABASE:
- 7 bảng: users, timetable, documents, expenses, notes, user_settings, activity_logs
- 25+ indexes tối ưu hiệu suất
- 4 views thống kê dữ liệu
- 3 stored procedures tiện ích
- 4 triggers tự động log
- 1 user duy nhất như yêu cầu
*/ 