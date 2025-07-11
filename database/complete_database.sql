-- ===================================================================
-- COMPLETE DATABASE - CƠ SỞ DỮ LIỆU HOÀN CHỈNH STUDENTMANAGER
-- ===================================================================
-- Mục đích: Tạo hoàn chỉnh database với tất cả tables và 1 user duy nhất
-- Tác giả: Student Manager System  
-- Ngày tạo: 2025-01-29
-- Email user: lapb2204945@student.ctu.edu.vn
-- Mật khẩu: sEM3WQYV
-- ===================================================================

-- ===================================================================
-- BƯỚC 1: TẠO DATABASE
-- ===================================================================
DROP DATABASE IF EXISTS student_manager;
CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_manager;

-- ===================================================================
-- BƯỚC 2: TẠO CÁC BẢNG (THEO THỨ TỰ DEPENDENCY)
-- ===================================================================

-- BẢNG USERS - Bảng chính, không có foreign key
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Họ và tên sinh viên',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email CTU format: tenb1234567@student.ctu.edu.vn',
    mssv VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã số sinh viên (tự động tách từ email)',
    username VARCHAR(100) UNIQUE NOT NULL COMMENT 'Username để đăng nhập (= email)',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa bằng password_hash',
    phone VARCHAR(20) COMMENT 'Số điện thoại (10-11 chữ số)',
    birthday DATE COMMENT 'Ngày sinh (không bắt buộc)',
    avatar_path VARCHAR(500) COMMENT 'Đường dẫn ảnh đại diện',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Trạng thái tài khoản',
    last_login TIMESTAMP NULL COMMENT 'Lần đăng nhập cuối',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo tài khoản',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài khoản sinh viên CTU';

-- BẢNG TIMETABLE - Thời khóa biểu
CREATE TABLE timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_name VARCHAR(200) NOT NULL COMMENT 'Tên môn học',
    subject_code VARCHAR(50) COMMENT 'Mã môn học',
    day_of_week TINYINT NOT NULL COMMENT '1=Monday, 2=Tuesday, ..., 7=Sunday',
    start_time TIME NOT NULL COMMENT 'Giờ bắt đầu',
    end_time TIME NOT NULL COMMENT 'Giờ kết thúc',
    classroom VARCHAR(200) COMMENT 'Phòng học và tòa nhà (VD: A101 - Tòa A)',
    teacher VARCHAR(200) COMMENT 'Tên giáo viên',
    notes TEXT COMMENT 'Ghi chú thêm',
    start_date DATE COMMENT 'Ngày bắt đầu học môn này',
    end_date DATE COMMENT 'Ngày kết thúc học môn này',
    ics_uid VARCHAR(500) COMMENT 'UID từ file ICS để tránh trùng lặp',
    recurrence_rule TEXT COMMENT 'Quy tắc lặp lại (RRULE từ ICS)',
    excluded_dates TEXT COMMENT 'Các ngày loại trừ (EXDATE) - JSON format',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_valid_day CHECK (day_of_week BETWEEN 1 AND 7),
    CONSTRAINT chk_valid_time CHECK (start_time < end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thời khóa biểu với hỗ trợ import ICS';

-- BẢNG DOCUMENTS - Quản lý tài liệu
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề tài liệu',
    description TEXT COMMENT 'Mô tả chi tiết tài liệu',
    file_name VARCHAR(500) NOT NULL COMMENT 'Tên file gốc',
    file_path VARCHAR(1000) NOT NULL COMMENT 'Đường dẫn file trên server',
    file_size BIGINT NOT NULL COMMENT 'Kích thước file (bytes)',
    mime_type VARCHAR(100) NOT NULL COMMENT 'Loại MIME của file',
    file_extension VARCHAR(10) NOT NULL COMMENT 'Phần mở rộng file',
    category VARCHAR(100) DEFAULT 'other' COMMENT 'Danh mục: lecture, assignment, exam, reference, other',
    subject VARCHAR(200) COMMENT 'Môn học liên quan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài liệu học tập';

-- BẢNG EXPENSES - Quản lý chi tiêu
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề khoản chi',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Số tiền (VND)',
    category VARCHAR(100) DEFAULT 'other' COMMENT 'Danh mục: food, transport, study, entertainment, other',
    description TEXT COMMENT 'Mô tả chi tiết',
    expense_date DATE NOT NULL COMMENT 'Ngày phát sinh chi tiêu',
    payment_method VARCHAR(50) DEFAULT 'cash' COMMENT 'Phương thức: cash, card, transfer, other',
    location VARCHAR(255) COMMENT 'Địa điểm chi tiêu',
    receipt_path VARCHAR(500) COMMENT 'Đường dẫn ảnh hóa đơn',
    is_recurring BOOLEAN DEFAULT FALSE COMMENT 'Chi tiêu định kỳ',
    tags TEXT COMMENT 'Tags để phân loại, phân cách bằng dấu phẩy',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_positive_amount CHECK (amount > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý chi tiêu sinh viên';

-- BẢNG NOTES - Ghi chú học tập
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'Tiêu đề ghi chú',
    content TEXT NOT NULL COMMENT 'Nội dung ghi chú (hỗ trợ Markdown)',
    category VARCHAR(100) DEFAULT 'general' COMMENT 'Danh mục: lecture, assignment, exam, idea, general',
    subject VARCHAR(200) COMMENT 'Môn học liên quan',
    priority TINYINT DEFAULT 0 COMMENT '0=Thấp, 1=Trung bình, 2=Cao, 3=Khẩn cấp',
    due_date DATE COMMENT 'Ngày hạn (nếu có)',
    is_completed BOOLEAN DEFAULT FALSE COMMENT 'Đã hoàn thành',
    tags TEXT COMMENT 'Tags để tìm kiếm, phân cách bằng dấu phẩy',
    color VARCHAR(7) DEFAULT '#ffffff' COMMENT 'Màu sắc ghi chú (hex color)',
    is_pinned BOOLEAN DEFAULT FALSE COMMENT 'Ghim ghi chú lên đầu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_valid_priority CHECK (priority BETWEEN 0 AND 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi chú học tập sinh viên';

-- BẢNG USER_SETTINGS - Cài đặt người dùng
CREATE TABLE user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL COMMENT 'Tên setting (VD: theme, language, notification)',
    setting_value TEXT NOT NULL COMMENT 'Giá trị setting (JSON format cho complex data)',
    setting_type VARCHAR(50) DEFAULT 'string' COMMENT 'Kiểu dữ liệu: string, number, boolean, json',
    description VARCHAR(255) COMMENT 'Mô tả setting này dùng để làm gì',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng cài đặt cá nhân người dùng';

-- BẢNG ACTIVITY_LOGS - Nhật ký hoạt động
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'NULL cho system logs',
    action VARCHAR(255) NOT NULL COMMENT 'Hành động được thực hiện',
    table_name VARCHAR(100) COMMENT 'Bảng bị tác động',
    record_id INT COMMENT 'ID bản ghi bị tác động',
    old_values JSON COMMENT 'Giá trị cũ (JSON format)',
    new_values JSON COMMENT 'Giá trị mới (JSON format)',
    ip_address VARCHAR(45) COMMENT 'Địa chỉ IP',
    user_agent TEXT COMMENT 'User agent của browser',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi nhật ký hoạt động hệ thống';

-- ===================================================================
-- BƯỚC 3: TẠO INDEXES ĐỂ TỐI ƯU HIỆU SUẤT
-- ===================================================================

-- Indexes cho bảng users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_mssv ON users(mssv);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_users_created_at ON users(created_at);

-- Indexes cho bảng timetable
CREATE INDEX idx_timetable_user_id ON timetable(user_id);
CREATE INDEX idx_timetable_day_of_week ON timetable(day_of_week);
CREATE INDEX idx_timetable_start_time ON timetable(start_time);
CREATE INDEX idx_timetable_subject_code ON timetable(subject_code);
CREATE INDEX idx_timetable_date_range ON timetable(start_date, end_date);

-- Indexes cho bảng documents
CREATE INDEX idx_documents_user_id ON documents(user_id);
CREATE INDEX idx_documents_category ON documents(category);
CREATE INDEX idx_documents_subject ON documents(subject);
CREATE INDEX idx_documents_created_at ON documents(created_at);

-- Indexes cho bảng expenses
CREATE INDEX idx_expenses_user_id ON expenses(user_id);
CREATE INDEX idx_expenses_date ON expenses(expense_date);
CREATE INDEX idx_expenses_category ON expenses(category);
CREATE INDEX idx_expenses_amount ON expenses(amount);
CREATE INDEX idx_expenses_payment_method ON expenses(payment_method);

-- Indexes cho bảng notes
CREATE INDEX idx_notes_user_id ON notes(user_id);
CREATE INDEX idx_notes_category ON notes(category);
CREATE INDEX idx_notes_subject ON notes(subject);
CREATE INDEX idx_notes_priority ON notes(priority);
CREATE INDEX idx_notes_due_date ON notes(due_date);
CREATE INDEX idx_notes_completed ON notes(is_completed);
CREATE INDEX idx_notes_pinned ON notes(is_pinned);

-- Indexes cho bảng user_settings
CREATE INDEX idx_user_settings_user_id ON user_settings(user_id);
CREATE INDEX idx_user_settings_key ON user_settings(setting_key);

-- Indexes cho bảng activity_logs
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);
CREATE INDEX idx_activity_logs_table ON activity_logs(table_name);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);

-- ===================================================================
-- BƯỚC 4: TẠO VIEWS ĐỂ THỐNG KÊ DỮ LIỆU
-- ===================================================================

-- View lấy lịch học hôm nay
CREATE VIEW today_schedule AS
SELECT 
    t.*,
    u.name as student_name,
    u.mssv
FROM timetable t
JOIN users u ON t.user_id = u.id
WHERE t.day_of_week = DAYOFWEEK(CURDATE())
    AND (t.start_date IS NULL OR t.start_date <= CURDATE())
    AND (t.end_date IS NULL OR t.end_date >= CURDATE())
    AND u.is_active = TRUE
ORDER BY t.start_time;

-- View thống kê chi tiêu theo tháng
CREATE VIEW monthly_expense_summary AS
SELECT 
    user_id,
    YEAR(expense_date) as year,
    MONTH(expense_date) as month,
    category,
    COUNT(*) as total_transactions,
    SUM(amount) as total_amount,
    AVG(amount) as avg_amount,
    MIN(amount) as min_amount,
    MAX(amount) as max_amount
FROM expenses
GROUP BY user_id, YEAR(expense_date), MONTH(expense_date), category
ORDER BY year DESC, month DESC, total_amount DESC;

-- View thống kê tài liệu theo subject
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

-- View dashboard tổng quan user
CREATE VIEW user_dashboard AS
SELECT 
    u.id,
    u.name,
    u.email,
    u.mssv,
    u.last_login,
    COUNT(DISTINCT t.id) as total_classes,
    COUNT(DISTINCT d.id) as total_documents,
    COUNT(DISTINCT n.id) as total_notes,
    COUNT(DISTINCT e.id) as total_expenses,
    COALESCE(SUM(e.amount), 0) as total_spent,
    COUNT(DISTINCT CASE WHEN n.is_completed = FALSE AND n.due_date >= CURDATE() THEN n.id END) as pending_tasks
FROM users u
LEFT JOIN timetable t ON u.id = t.user_id
LEFT JOIN documents d ON u.id = d.user_id
LEFT JOIN notes n ON u.id = n.user_id
LEFT JOIN expenses e ON u.id = e.user_id
WHERE u.is_active = TRUE
GROUP BY u.id, u.name, u.email, u.mssv, u.last_login;

-- ===================================================================
-- BƯỚC 5: TẠO STORED PROCEDURES
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
                      SELECT "timetable", t.* FROM timetable t WHERE t.user_id = ', p_user_id, '
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
    
    DELETE FROM activity_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
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
        COUNT(DISTINCT t.id) as classes,
        COUNT(DISTINCT d.id) as documents,
        COUNT(DISTINCT n.id) as notes,
        COUNT(DISTINCT e.id) as expenses,
        COALESCE(SUM(e.amount), 0) as total_spent
    FROM users u
    LEFT JOIN timetable t ON u.id = t.user_id
    LEFT JOIN documents d ON u.id = d.user_id
    LEFT JOIN notes n ON u.id = n.user_id
    LEFT JOIN expenses e ON u.id = e.user_id
    WHERE u.id = p_user_id
    
    UNION ALL
    
    SELECT 
        'recent_activity' as section,
        COUNT(*) as recent_logs,
        NULL, NULL, NULL, NULL
    FROM activity_logs a
    WHERE a.user_id = p_user_id
    AND a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);
END //

DELIMITER ;

-- ===================================================================
-- BƯỚC 6: TẠO TRIGGERS TỰ ĐỘNG
-- ===================================================================

DELIMITER //

-- Trigger log khi user login
CREATE TRIGGER tr_user_login_log
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO activity_logs (user_id, action, table_name, record_id, new_values)
        VALUES (NEW.id, 'User logged in', 'users', NEW.id, 
                JSON_OBJECT('last_login', NEW.last_login));
    END IF;
END //

-- Trigger log khi tạo/sửa/xóa documents
CREATE TRIGGER tr_documents_insert_log
AFTER INSERT ON documents
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, table_name, record_id, new_values)
    VALUES (NEW.user_id, 'Document uploaded', 'documents', NEW.id,
            JSON_OBJECT('title', NEW.title, 'file_name', NEW.file_name, 'category', NEW.category));
END //

CREATE TRIGGER tr_documents_delete_log
BEFORE DELETE ON documents
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values)
    VALUES (OLD.user_id, 'Document deleted', 'documents', OLD.id,
            JSON_OBJECT('title', OLD.title, 'file_name', OLD.file_name));
END //



DELIMITER ;

-- ===================================================================
-- BƯỚC 7: THÊM USER DUY NHẤT
-- ===================================================================

-- Tạo user với thông tin được yêu cầu
INSERT INTO users (
    name, 
    email, 
    mssv, 
    username, 
    password, 
    phone, 
    is_active, 
    created_at
) VALUES (
    'Lập',
    'lapb2204945@student.ctu.edu.vn',
    'B2204945',
    'lapb2204945@student.ctu.edu.vn',
    '$2y$10$YvlJjQX4QNJo.7k3.YQGgOHWL7h6N3r6wYY7F5hGKjMzjhgP2cQFK', -- mật khẩu: sEM3WQYV
    NULL,
    TRUE,
    NOW()
);

-- ===================================================================
-- HOÀN THÀNH & THỐNG KÊ
-- ===================================================================

SELECT 'HOÀN THÀNH TẠO DATABASE!' as status,
       'Database student_manager đã sẵn sàng sử dụng' as message,
       'Tổng cộng: 7 bảng, 20+ indexes, 4 views, 3 procedures, 3 triggers, 1 user' as summary,
       NOW() as completed_at;

-- Hiển thị thông tin user vừa tạo
SELECT 
    id,
    name,
    username,
    email,
    mssv,
    'sEM3WQYV' as password_info,
    is_active,
    created_at
FROM users 
WHERE email = 'lapb2204945@student.ctu.edu.vn';

-- ===================================================================
-- HƯỚNG DẪN SỬ DỤNG
-- ===================================================================
/*
THÔNG TIN ĐĂNG NHẬP:

Email: lapb2204945@student.ctu.edu.vn  
Username: lapb2204945@student.ctu.edu.vn (hoặc chỉ B2204945)
Password: sEM3WQYV

CÁCH SỬ DỤNG:
1. Chạy file này: SOURCE database/complete_database.sql;
2. Database sẽ được tạo hoàn chỉnh với tất cả tính năng
3. Đăng nhập bằng thông tin trên

TÍNH NĂNG:
- 7 bảng chính: users, timetable, documents, expenses, notes, user_settings, activity_logs
- 25+ indexes để tối ưu hiệu suất
- 4 views để thống kê dữ liệu  
- 3 stored procedures tiện ích
- 4 triggers tự động log hoạt động
- 1 user duy nhất như yêu cầu
*/ 