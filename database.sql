CREATE DATABASE IF NOT EXISTS student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_manager;

-- Bảng users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Họ và tên đầy đủ',
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Email CTU',
    mssv VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã số sinh viên',
    username VARCHAR(100) UNIQUE NOT NULL COMMENT 'Username để đăng nhập',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã hash',
    phone VARCHAR(20) NULL,
    birthday DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    google_calendar_id VARCHAR(255) NULL COMMENT 'Google Calendar ID của người dùng',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Bảng quản lý tài khoản sinh viên';

-- Bảng documents
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    category VARCHAR(100) NULL,
    subject VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_documents_user_id (user_id),
    INDEX idx_documents_category (category),
    INDEX idx_documents_subject (subject),
    INDEX idx_documents_created_at (created_at)
) ENGINE=InnoDB;

-- Bảng expenses
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NULL,
    expense_date DATE NOT NULL,
    payment_method VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expenses_user_id (user_id),
    INDEX idx_expenses_date (expense_date),
    INDEX idx_expenses_category (category),
    INDEX idx_expenses_amount (amount),
    INDEX idx_expenses_payment_method (payment_method)
) ENGINE=InnoDB;

-- Bảng notes
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notes_user_id (user_id),
    INDEX idx_notes_category (category)
) ENGINE=InnoDB;

-- Dữ liệu mẫu
INSERT INTO users (name, email, mssv, username, password, phone, birthday) VALUES
('Trần Công Lập', 'lapb2204945@student.ctu.edu.vn', 'B2204945', 'lapb2204945', '$2y$10$ztNQ0QtMtFYzA0/Bl.JUDO4tMTzLCOPVAtQp1qqBFfXRf0QUmYwYG', '0123456789', '2004-01-01');

-- HOÀN THÀNH
SELECT 'HOÀN THÀNH TẠO DATABASE!' as status, NOW() as completed_at; 