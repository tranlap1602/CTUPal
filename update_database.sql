-- Script cập nhật cơ sở dữ liệu để đổi tên các cột id
-- Chạy script này sau khi đã backup cơ sở dữ liệu

USE student_manager;

-- Đổi tên cột id thành doc_id trong bảng documents
ALTER TABLE documents CHANGE COLUMN id doc_id INT AUTO_INCREMENT PRIMARY KEY;

-- Đổi tên cột id thành expense_id trong bảng expenses  
ALTER TABLE expenses CHANGE COLUMN id expense_id INT AUTO_INCREMENT PRIMARY KEY;

-- Đổi tên cột id thành note_id trong bảng notes
ALTER TABLE notes CHANGE COLUMN id note_id INT AUTO_INCREMENT PRIMARY KEY;

-- Kiểm tra kết quả
DESCRIBE documents;
DESCRIBE expenses;
DESCRIBE notes; 