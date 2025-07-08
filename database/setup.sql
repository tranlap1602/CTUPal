-- ===================================================================
-- DATABASE SETUP - THIẾT LẬP CƠ SỞ DỮ LIỆU
-- ===================================================================
-- Mục đích: Tạo database và cấu hình cơ bản
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- Phiên bản: 2.0 (Modular version)
-- ===================================================================

-- Tạo cơ sở dữ liệu
DROP DATABASE IF EXISTS student_manager;
CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng database
USE student_manager;

-- Hiển thị thông báo
SELECT 'Database student_manager đã được tạo thành công!' as status,
       'Sẵn sàng để import các bảng' as next_step,
       NOW() as created_at; 