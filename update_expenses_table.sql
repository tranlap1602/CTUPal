-- Script cập nhật bảng expenses - loại bỏ các trường không cần thiết
-- Chạy script này để cập nhật cấu trúc bảng expenses

-- Bước 1: Xóa các index liên quan đến trường sẽ bị xóa
DROP INDEX idx_expenses_recurring ON expenses;

-- Bước 2: Xóa các trường không cần thiết
ALTER TABLE expenses 
DROP COLUMN title,
DROP COLUMN is_recurring,
DROP COLUMN recurrence_rule;

-- Bước 3: Cập nhật comment cho bảng
ALTER TABLE expenses COMMENT='Bảng quản lý chi tiêu cá nhân - đã tối ưu';

-- Kiểm tra cấu trúc bảng sau khi cập nhật
DESCRIBE expenses; 