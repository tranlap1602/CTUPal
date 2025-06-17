-- File: database/reset_database.sql
-- Mục đích: Reset database để tránh conflict khi import lại
-- Ngày tạo: [Ngày hiện tại]
-- Mô tả: Chạy script này TRƯỚC KHI import student_manager.sql

-- CẢNH BÁO: Script này sẽ XÓA TOÀN BỘ DỮ LIỆU!
-- Chỉ chạy khi bạn chắc chắn muốn reset lại từ đầu

-- Xóa database cũ nếu tồn tại
DROP DATABASE IF EXISTS student_manager;

-- Tạo lại database mới
CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Chọn database để sử dụng
USE student_manager;

-- Hiển thị thông báo
SELECT 'Database đã được reset! Bây giờ bạn có thể import file student_manager.sql' AS message; 