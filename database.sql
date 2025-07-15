-- ===================================================================
-- STUDENT MANAGER DATABASE - CẤU TRÚC HOÀN CHỈNH (KHÔNG CÓ TIMETABLE)
-- ===================================================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_manager;

-- ===================================================================
-- BƯỚC 1: TẠO BẢNG USERS
-- ===================================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Họ và tên đầy đủ',
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Email CTU (lapb2204945@student.ctu.edu.vn)',
    mssv VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã số sinh viên (B2204945)',
    username VARCHAR(100) UNIQUE NOT NULL COMMENT 'Username để đăng nhập',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã hash',
    phone VARCHAR(20) NULL COMMENT 'Số điện thoại',
    birthday DATE NULL COMMENT 'Ngày sinh',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Trạng thái hoạt động',
    last_login TIMESTAMP NULL COMMENT 'Lần đăng nhập cuối',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Bảng quản lý tài khoản sinh viên';

-- ===================================================================
-- BƯỚC 2: TẠO BẢNG DOCUMENTS
-- ===================================================================

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề tài liệu',
    description TEXT NULL COMMENT 'Mô tả tài liệu',
    file_name VARCHAR(255) NOT NULL COMMENT 'Tên file gốc',
    file_path VARCHAR(500) NOT NULL COMMENT 'Đường dẫn file trên server',
    file_size BIGINT NOT NULL COMMENT 'Kích thước file (bytes)',
    file_type VARCHAR(50) NOT NULL COMMENT 'Loại file (pdf, doc, etc.)',
    category VARCHAR(100) NULL COMMENT 'Danh mục tài liệu',
    subject VARCHAR(100) NULL COMMENT 'Môn học liên quan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_documents_user_id (user_id),
    INDEX idx_documents_category (category),
    INDEX idx_documents_subject (subject),
    INDEX idx_documents_created_at (created_at)
) ENGINE=InnoDB COMMENT='Bảng quản lý tài liệu học tập';

-- ===================================================================
-- BƯỚC 3: TẠO BẢNG EXPENSES
-- ===================================================================

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề chi tiêu',
    amount DECIMAL(10,2) NOT NULL COMMENT 'Số tiền',
    category VARCHAR(100) NOT NULL COMMENT 'Danh mục chi tiêu',
    description TEXT NULL COMMENT 'Mô tả chi tiết',
    expense_date DATE NOT NULL COMMENT 'Ngày chi tiêu',
    payment_method VARCHAR(50) NULL COMMENT 'Phương thức thanh toán',
    is_recurring BOOLEAN DEFAULT FALSE COMMENT 'Chi tiêu định kỳ',
    recurrence_rule VARCHAR(255) NULL COMMENT 'Quy tắc lặp lại (hàng tháng, hàng tuần)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expenses_user_id (user_id),
    INDEX idx_expenses_date (expense_date),
    INDEX idx_expenses_category (category),
    INDEX idx_expenses_amount (amount),
    INDEX idx_expenses_payment_method (payment_method),
    INDEX idx_expenses_recurring (is_recurring)
) ENGINE=InnoDB COMMENT='Bảng quản lý chi tiêu cá nhân';

-- ===================================================================
-- BƯỚC 4: TẠO BẢNG NOTES
-- ===================================================================

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề ghi chú',
    content TEXT NOT NULL COMMENT 'Nội dung ghi chú',
    category VARCHAR(100) NULL COMMENT 'Danh mục ghi chú',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notes_user_id (user_id),
    INDEX idx_notes_category (category)
) ENGINE=InnoDB COMMENT='Bảng ghi chú đơn giản';

-- ===================================================================
-- BƯỚC 5: TẠO BẢNG USER_SETTINGS
-- ===================================================================

CREATE TABLE user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL COMMENT 'Khóa cài đặt',
    setting_value TEXT NULL COMMENT 'Giá trị cài đặt (có thể là JSON)',
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string' COMMENT 'Kiểu dữ liệu',
    description TEXT NULL COMMENT 'Mô tả cài đặt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key),
    INDEX idx_user_settings_user_id (user_id),
    INDEX idx_user_settings_key (setting_key)
) ENGINE=InnoDB COMMENT='Bảng cài đặt người dùng';

-- ===================================================================
-- BƯỚC 6: TẠO INDEXES CHO BẢNG USERS
-- ===================================================================

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_mssv ON users(mssv);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_users_created_at ON users(created_at);

-- ===================================================================
-- BƯỚC 7: TẠO VIEWS THỐNG KÊ
-- ===================================================================

-- View thống kê chi tiêu theo tháng
CREATE VIEW monthly_expense_summary AS
SELECT 
    u.id as user_id,
    u.name as student_name,
    YEAR(e.expense_date) as year,
    MONTH(e.expense_date) as month,
    COUNT(*) as total_expenses,
    SUM(e.amount) as total_amount,
    AVG(e.amount) as avg_amount,
    MAX(e.amount) as max_amount,
    MIN(e.amount) as min_amount
FROM users u
JOIN expenses e ON u.id = e.user_id
GROUP BY u.id, u.name, YEAR(e.expense_date), MONTH(e.expense_date)
ORDER BY year DESC, month DESC;

-- View tài liệu theo môn học
CREATE VIEW documents_by_subject AS
SELECT 
    u.name as student_name,
    d.subject,
    COUNT(*) as document_count,
    SUM(d.file_size) as total_size,
    MAX(d.created_at) as last_upload
FROM users u
JOIN documents d ON u.id = d.user_id
WHERE d.subject IS NOT NULL
GROUP BY u.id, u.name, d.subject
ORDER BY document_count DESC;

-- View dashboard tổng quan
CREATE VIEW user_dashboard AS
SELECT 
    u.id,
    u.name,
    u.email,
    u.mssv,
    COUNT(DISTINCT d.id) as total_documents,
    COUNT(DISTINCT n.id) as total_notes,
    COUNT(DISTINCT e.id) as total_expenses,
    COALESCE(SUM(e.amount), 0) as total_spent,
    u.last_login,
    u.created_at
FROM users u
LEFT JOIN documents d ON u.id = d.user_id
LEFT JOIN notes n ON u.id = n.user_id
LEFT JOIN expenses e ON u.id = e.user_id
GROUP BY u.id, u.name, u.email, u.mssv, u.last_login, u.created_at;

-- ===================================================================
-- BƯỚC 8: TẠO STORED PROCEDURES
-- ===================================================================

DELIMITER //

-- Procedure backup dữ liệu user
CREATE PROCEDURE BackupUserData(IN p_user_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Tạo bảng backup với timestamp
    SET @backup_table = CONCAT('user_backup_', p_user_id, '_', UNIX_TIMESTAMP());
    
    SET @sql = CONCAT('CREATE TABLE ', @backup_table, ' AS 
                      SELECT "users" as table_name, u.* FROM users u WHERE u.id = ', p_user_id, '
                      UNION ALL  
                      SELECT "documents", d.* FROM documents d WHERE d.user_id = ', p_user_id, '
                      UNION ALL
                      SELECT "expenses", e.* FROM expenses e WHERE e.user_id = ', p_user_id, '
                      UNION ALL
                      SELECT "notes", n.* FROM notes n WHERE n.user_id = ', p_user_id);
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    COMMIT;
    
    SELECT CONCAT('Backup completed: ', @backup_table) as result;
END //

-- Procedure xóa dữ liệu cũ
CREATE PROCEDURE CleanOldData(IN p_days_old INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    DELETE FROM expenses 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY)
    AND is_recurring = FALSE;
    
    COMMIT;
    
    SELECT ROW_COUNT() as deleted_records;
END //

-- Procedure thống kê user
CREATE PROCEDURE GetUserStats(IN p_user_id INT)
BEGIN
    SELECT 
        'summary' as section,
        COUNT(DISTINCT d.id) as documents,
        COUNT(DISTINCT n.id) as notes,
        COUNT(DISTINCT e.id) as expenses,
        COALESCE(SUM(e.amount), 0) as total_spent
    FROM users u
    LEFT JOIN documents d ON u.id = d.user_id
    LEFT JOIN notes n ON u.id = n.user_id
    LEFT JOIN expenses e ON u.id = e.user_id
    WHERE u.id = p_user_id;
END //

DELIMITER ;

-- ===================================================================
-- BƯỚC 9: TẠO TRIGGERS TỰ ĐỘNG
-- ===================================================================

DELIMITER //

-- Trigger tạo settings mặc định khi tạo user mới
CREATE TRIGGER tr_create_user_settings
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_settings (user_id, setting_key, setting_value, setting_type, description) VALUES
    (NEW.id, 'theme', 'light', 'string', 'Giao diện sáng/tối'),
    (NEW.id, 'language', 'vi', 'string', 'Ngôn ngữ hiển thị'),
    (NEW.id, 'notifications', 'true', 'boolean', 'Bật/tắt thông báo'),
    (NEW.id, 'auto_backup', 'true', 'boolean', 'Tự động backup dữ liệu'),
    (NEW.id, 'dashboard_layout', 'default', 'string', 'Bố cục dashboard');
END //

-- Trigger cập nhật thời gian sửa đổi
CREATE TRIGGER tr_update_timestamp
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

DELIMITER ;

-- ===================================================================
-- BƯỚC 10: INSERT DỮ LIỆU MẪU
-- ===================================================================

-- Tạo user mẫu (password: 123456)
INSERT INTO users (name, email, mssv, username, password, phone, birthday) VALUES
('Lê Anh Phương Bình', 'lapb2204945@student.ctu.edu.vn', 'B2204945', 'lapb2204945', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', '2004-01-01');

-- ===================================================================
-- HOÀN THÀNH
-- ===================================================================

SELECT 'HOÀN THÀNH TẠO DATABASE!' as status,
       'Database student_manager đã sẵn sàng sử dụng' as message,
       'Tổng cộng: 5 bảng, 15+ indexes, 3 views, 3 procedures, 2 triggers' as summary,
       NOW() as completed_at; 