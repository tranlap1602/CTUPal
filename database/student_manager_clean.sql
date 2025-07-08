-- ===================================================================
-- STUDENT MANAGER DATABASE - PHIÊN BẢN HOÀN CHỈNH
-- ===================================================================
-- Mục đích: Cơ sở dữ liệu quản lý sinh viên CTU
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- Phiên bản: 2.0 (Clean version - không có dữ liệu mẫu)
-- ===================================================================

-- Tạo cơ sở dữ liệu
DROP DATABASE IF EXISTS student_manager;
CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_manager;

-- ===================================================================
-- BẢNG USERS - QUẢN LÝ TÀI KHOẢN SINH VIÊN CTU
-- ===================================================================
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

-- ===================================================================
-- BẢNG TIMETABLE - THỜI KHÓA BIỂU (CÓ HỖ TRỢ ICS)
-- ===================================================================
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
    -- Hỗ trợ ICS import và quản lý phạm vi tuần
    start_date DATE COMMENT 'Ngày bắt đầu học môn này',
    end_date DATE COMMENT 'Ngày kết thúc học môn này',
    ics_uid VARCHAR(500) COMMENT 'UID từ file ICS để tránh trùng lặp',
    recurrence_rule TEXT COMMENT 'Quy tắc lặp lại (RRULE từ ICS)',
    excluded_dates TEXT COMMENT 'Các ngày loại trừ (EXDATE) - JSON format',
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys và constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_valid_day CHECK (day_of_week BETWEEN 1 AND 7),
    CONSTRAINT chk_valid_time CHECK (start_time < end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thời khóa biểu với hỗ trợ import ICS';

-- ===================================================================
-- BẢNG DOCUMENTS - QUẢN LÝ TÀI LIỆU HỌC TẬP
-- ===================================================================
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(300) NOT NULL COMMENT 'Tiêu đề tài liệu',
    description TEXT COMMENT 'Mô tả chi tiết',
    category ENUM('lecture', 'assignment', 'exam', 'reference', 'note', 'slide', 'book', 'other') DEFAULT 'other' COMMENT 'Danh mục tài liệu',
    subject VARCHAR(200) COMMENT 'Môn học liên quan',
    subject_code VARCHAR(50) COMMENT 'Mã môn học',
    file_name VARCHAR(500) NOT NULL COMMENT 'Tên file gốc',
    file_path VARCHAR(1000) NOT NULL COMMENT 'Đường dẫn file đã lưu',
    file_size BIGINT COMMENT 'Kích thước file (bytes)',
    file_type VARCHAR(100) COMMENT 'Loại file (extension)',
    mime_type VARCHAR(200) COMMENT 'MIME type',
    download_count INT DEFAULT 0 COMMENT 'Số lần tải xuống',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Có chia sẻ công khai không',
    is_favorite BOOLEAN DEFAULT FALSE COMMENT 'Có đánh dấu yêu thích không',
    tags VARCHAR(1000) COMMENT 'Các tag để tìm kiếm',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày upload',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài liệu học tập';

-- ===================================================================
-- BẢNG EXPENSES - QUẢN LÝ CHI TIÊU
-- ===================================================================
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(300) NOT NULL COMMENT 'Tiêu đề khoản chi',
    description TEXT COMMENT 'Mô tả chi tiết',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Số tiền (VNĐ)',
    currency VARCHAR(10) DEFAULT 'VND' COMMENT 'Đơn vị tiền tệ',
    category ENUM('food', 'transport', 'education', 'entertainment', 'shopping', 'health', 'accommodation', 'utilities', 'communication', 'insurance', 'other') DEFAULT 'other' COMMENT 'Danh mục chi tiêu',
    subcategory VARCHAR(100) COMMENT 'Danh mục phụ',
    expense_date DATE NOT NULL COMMENT 'Ngày chi tiêu',
    location VARCHAR(200) COMMENT 'Địa điểm chi tiêu',
    payment_method ENUM('cash', 'card', 'bank_transfer', 'e_wallet', 'other') DEFAULT 'cash' COMMENT 'Phương thức thanh toán',
    receipt_path VARCHAR(500) COMMENT 'Đường dẫn ảnh hóa đơn',
    note TEXT COMMENT 'Ghi chú thêm',
    is_recurring BOOLEAN DEFAULT FALSE COMMENT 'Chi tiêu định kỳ',
    recurring_period ENUM('daily', 'weekly', 'monthly', 'yearly') COMMENT 'Chu kỳ lặp lại',
    tags VARCHAR(500) COMMENT 'Các tag để phân loại',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys và constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_positive_amount CHECK (amount > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý chi tiêu cá nhân';

-- ===================================================================
-- BẢNG NOTES - QUẢN LÝ GHI CHÚ
-- ===================================================================
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(300) NOT NULL COMMENT 'Tiêu đề ghi chú',
    content TEXT NOT NULL COMMENT 'Nội dung ghi chú',
    content_type ENUM('text', 'markdown', 'html') DEFAULT 'text' COMMENT 'Định dạng nội dung',
    category ENUM('study', 'personal', 'work', 'idea', 'todo', 'meeting', 'research', 'project', 'other') DEFAULT 'other' COMMENT 'Danh mục ghi chú',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium' COMMENT 'Mức độ ưu tiên',
    status ENUM('draft', 'active', 'completed', 'archived') DEFAULT 'active' COMMENT 'Trạng thái ghi chú',
    tags VARCHAR(1000) COMMENT 'Các tag để tìm kiếm',
    color VARCHAR(20) COMMENT 'Màu sắc đánh dấu',
    is_pinned BOOLEAN DEFAULT FALSE COMMENT 'Có ghim lên đầu không',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Có chia sẻ công khai không',
    reminder_date DATETIME COMMENT 'Ngày nhắc nhở',
    due_date DATE COMMENT 'Ngày đến hạn (cho todo)',
    completion_date DATETIME COMMENT 'Ngày hoàn thành',
    attachments TEXT COMMENT 'Đường dẫn file đính kèm (JSON)',
    view_count INT DEFAULT 0 COMMENT 'Số lần xem',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý ghi chú cá nhân';

-- ===================================================================
-- BẢNG USER_SETTINGS - CÀI ĐẶT NGƯỜI DÙNG
-- ===================================================================
CREATE TABLE user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL COMMENT 'Khóa cài đặt',
    setting_value TEXT COMMENT 'Giá trị cài đặt (JSON hoặc text)',
    setting_type ENUM('string', 'number', 'boolean', 'json', 'date') DEFAULT 'string' COMMENT 'Loại dữ liệu',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Có thể truy cập công khai không',
    description VARCHAR(500) COMMENT 'Mô tả cài đặt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys và constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng cài đặt người dùng';

-- ===================================================================
-- BẢNG ACTIVITY_LOGS - NHẬT KÝ HOẠT ĐỘNG
-- ===================================================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL COMMENT 'Hành động thực hiện',
    table_name VARCHAR(50) COMMENT 'Bảng bị tác động',
    record_id INT COMMENT 'ID bản ghi bị tác động',
    old_values TEXT COMMENT 'Giá trị cũ (JSON)',
    new_values TEXT COMMENT 'Giá trị mới (JSON)',
    ip_address VARCHAR(45) COMMENT 'Địa chỉ IP',
    user_agent TEXT COMMENT 'Thông tin trình duyệt',
    session_id VARCHAR(100) COMMENT 'ID phiên làm việc',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng nhật ký hoạt động hệ thống';

-- ===================================================================
-- TẠO CÁC INDEX ĐỂ TỐI ƯU HIỆU SUẤT
-- ===================================================================

-- Index cho bảng users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_mssv ON users(mssv);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_users_last_login ON users(last_login);

-- Index cho bảng timetable
CREATE INDEX idx_timetable_user_day ON timetable(user_id, day_of_week);
CREATE INDEX idx_timetable_user_day_time ON timetable(user_id, day_of_week, start_time);
CREATE INDEX idx_timetable_date_range ON timetable(user_id, start_date, end_date);
CREATE INDEX idx_timetable_subject ON timetable(subject_code, subject_name);
CREATE INDEX idx_timetable_ics_uid ON timetable(ics_uid);
CREATE INDEX idx_timetable_teacher ON timetable(teacher);
CREATE INDEX idx_timetable_classroom ON timetable(classroom);

-- Index cho bảng documents
CREATE INDEX idx_documents_user_category ON documents(user_id, category);
CREATE INDEX idx_documents_subject ON documents(subject, subject_code);
CREATE INDEX idx_documents_upload_date ON documents(user_id, upload_date);
CREATE INDEX idx_documents_public ON documents(is_public);
CREATE INDEX idx_documents_favorite ON documents(user_id, is_favorite);
CREATE FULLTEXT INDEX idx_documents_search ON documents(title, description, tags);

-- Index cho bảng expenses
CREATE INDEX idx_expenses_user_date ON expenses(user_id, expense_date);
CREATE INDEX idx_expenses_user_category ON expenses(user_id, category);
CREATE INDEX idx_expenses_date_range ON expenses(expense_date);
CREATE INDEX idx_expenses_amount ON expenses(amount);
CREATE INDEX idx_expenses_recurring ON expenses(is_recurring);

-- Index cho bảng notes
CREATE INDEX idx_notes_user_category ON notes(user_id, category);
CREATE INDEX idx_notes_user_priority ON notes(user_id, priority);
CREATE INDEX idx_notes_status ON notes(user_id, status);
CREATE INDEX idx_notes_pinned ON notes(user_id, is_pinned);
CREATE INDEX idx_notes_reminder ON notes(reminder_date);
CREATE INDEX idx_notes_due_date ON notes(due_date);
CREATE FULLTEXT INDEX idx_notes_search ON notes(title, content, tags);

-- Index cho bảng user_settings
CREATE INDEX idx_settings_key ON user_settings(setting_key);
CREATE INDEX idx_settings_type ON user_settings(setting_type);

-- Index cho bảng activity_logs
CREATE INDEX idx_logs_user_date ON activity_logs(user_id, created_at);
CREATE INDEX idx_logs_action ON activity_logs(action);
CREATE INDEX idx_logs_table ON activity_logs(table_name, record_id);

-- ===================================================================
-- TẠO CÁC VIEW HỮU ÍCH
-- ===================================================================

-- View thống kê chi tiêu theo tháng
CREATE VIEW monthly_expense_summary AS
SELECT 
    user_id,
    YEAR(expense_date) as year,
    MONTH(expense_date) as month,
    category,
    COUNT(*) as transaction_count,
    SUM(amount) as total_amount,
    AVG(amount) as average_amount,
    MIN(amount) as min_amount,
    MAX(amount) as max_amount
FROM expenses 
GROUP BY user_id, YEAR(expense_date), MONTH(expense_date), category
ORDER BY user_id, year DESC, month DESC;

-- View thời khóa biểu theo ngày với thông tin sinh viên
CREATE VIEW daily_schedule AS
SELECT 
    t.*,
    u.name as student_name,
    u.mssv as student_mssv,
    u.email as student_email,
    CASE t.day_of_week
        WHEN 1 THEN 'Chủ nhật'
        WHEN 2 THEN 'Thứ hai'
        WHEN 3 THEN 'Thứ ba'
        WHEN 4 THEN 'Thứ tư'
        WHEN 5 THEN 'Thứ năm'
        WHEN 6 THEN 'Thứ sáu'
        WHEN 7 THEN 'Thứ bảy'
    END as day_name,
    CONCAT(t.start_time, ' - ', t.end_time) as time_range,
    CASE 
        WHEN t.recurrence_rule IS NOT NULL THEN TRUE
        ELSE FALSE
    END as is_recurring
FROM timetable t
JOIN users u ON t.user_id = u.id
WHERE u.is_active = TRUE
ORDER BY t.user_id, t.day_of_week, t.start_time;

-- View thống kê tài liệu theo danh mục
CREATE VIEW document_statistics AS
SELECT 
    user_id,
    category,
    COUNT(*) as file_count,
    SUM(file_size) as total_size,
    AVG(file_size) as avg_size,
    SUM(download_count) as total_downloads
FROM documents 
GROUP BY user_id, category
ORDER BY user_id, category;

-- View ghi chú ưu tiên cao chưa hoàn thành
CREATE VIEW urgent_notes AS
SELECT 
    n.*,
    u.name as student_name,
    u.mssv as student_mssv
FROM notes n
JOIN users u ON n.user_id = u.id
WHERE n.priority IN ('high', 'urgent') 
    AND n.status IN ('draft', 'active')
    AND u.is_active = TRUE
ORDER BY 
    CASE n.priority 
        WHEN 'urgent' THEN 1 
        WHEN 'high' THEN 2 
    END,
    n.due_date ASC,
    n.created_at ASC;

-- ===================================================================
-- TẠO CÁC STORED PROCEDURE HỮU ÍCH
-- ===================================================================

DELIMITER //

-- Procedure lấy thống kê chi tiêu theo khoảng thời gian
CREATE PROCEDURE GetExpenseStatistics(
    IN p_user_id INT,
    IN p_from_date DATE,
    IN p_to_date DATE
)
BEGIN
    SELECT 
        category,
        COUNT(*) as transaction_count,
        SUM(amount) as total_amount,
        AVG(amount) as average_amount,
        MIN(amount) as min_amount,
        MAX(amount) as max_amount,
        ROUND(SUM(amount) / (SELECT SUM(amount) FROM expenses 
                            WHERE user_id = p_user_id 
                            AND expense_date BETWEEN p_from_date AND p_to_date) * 100, 2) as percentage
    FROM expenses 
    WHERE user_id = p_user_id 
        AND expense_date BETWEEN p_from_date AND p_to_date
    GROUP BY category
    ORDER BY total_amount DESC;
END //

-- Procedure lấy thời khóa biểu theo tuần
CREATE PROCEDURE GetWeeklySchedule(
    IN p_user_id INT,
    IN p_week_start DATE
)
BEGIN
    DECLARE p_week_end DATE;
    SET p_week_end = DATE_ADD(p_week_start, INTERVAL 6 DAY);
    
    SELECT 
        *,
        CASE day_of_week
            WHEN 1 THEN 'Chủ nhật'
            WHEN 2 THEN 'Thứ hai'
            WHEN 3 THEN 'Thứ ba'
            WHEN 4 THEN 'Thứ tư'
            WHEN 5 THEN 'Thứ năm'
            WHEN 6 THEN 'Thứ sáu'
            WHEN 7 THEN 'Thứ bảy'
        END as day_name,
        CASE 
            WHEN recurrence_rule IS NOT NULL THEN TRUE
            ELSE FALSE
        END as is_recurring
    FROM timetable 
    WHERE user_id = p_user_id
        AND (start_date IS NULL OR start_date <= p_week_end)
        AND (end_date IS NULL OR end_date >= p_week_start)
    ORDER BY day_of_week, start_time;
END //

-- Procedure làm sạch dữ liệu cũ
CREATE PROCEDURE CleanOldData()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_count INT;
    
    -- Xóa log cũ hơn 6 tháng
    DELETE FROM activity_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    GET DIAGNOSTICS v_count = ROW_COUNT;
    SELECT CONCAT('Đã xóa ', v_count, ' bản ghi log cũ') as result;
    
    -- Xóa tài khoản không hoạt động hơn 1 năm
    DELETE FROM users 
    WHERE is_active = FALSE 
        AND last_login < DATE_SUB(NOW(), INTERVAL 1 YEAR);
        
    GET DIAGNOSTICS v_count = ROW_COUNT;
    SELECT CONCAT('Đã xóa ', v_count, ' tài khoản không hoạt động') as result;
END //

DELIMITER ;

-- ===================================================================
-- TẠO CÁC TRIGGER ĐỂ DUY TRÌ TÍNH TOÀN VẸN DỮ LIỆU
-- ===================================================================

DELIMITER //

-- Trigger cập nhật last_login khi user đăng nhập thành công
CREATE TRIGGER update_last_login
    AFTER UPDATE ON users
    FOR EACH ROW
BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO activity_logs (user_id, action, table_name, record_id, new_values)
        VALUES (NEW.id, 'LOGIN', 'users', NEW.id, 
                CONCAT('{"last_login": "', NEW.last_login, '"}'));
    END IF;
END //

-- Trigger log khi tạo mới user
CREATE TRIGGER log_user_creation
    AFTER INSERT ON users
    FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, table_name, record_id, new_values)
    VALUES (NEW.id, 'CREATE_USER', 'users', NEW.id, 
            CONCAT('{"name": "', NEW.name, '", "email": "', NEW.email, '", "mssv": "', NEW.mssv, '"}'));
END //

-- Trigger tự động tạo thư mục settings mặc định cho user mới
CREATE TRIGGER create_default_settings
    AFTER INSERT ON users
    FOR EACH ROW
BEGIN
    INSERT INTO user_settings (user_id, setting_key, setting_value, setting_type, description) VALUES
    (NEW.id, 'theme', 'light', 'string', 'Giao diện sáng/tối'),
    (NEW.id, 'language', 'vi', 'string', 'Ngôn ngữ hiển thị'),
    (NEW.id, 'notifications', 'true', 'boolean', 'Bật/tắt thông báo'),
    (NEW.id, 'budget_limit', '5000000', 'number', 'Giới hạn ngân sách hàng tháng (VNĐ)'),
    (NEW.id, 'currency', 'VND', 'string', 'Đơn vị tiền tệ mặc định'),
    (NEW.id, 'timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ'),
    (NEW.id, 'date_format', 'd/m/Y', 'string', 'Định dạng ngày tháng'),
    (NEW.id, 'time_format', 'H:i', 'string', 'Định dạng giờ');
END //

DELIMITER ;

-- ===================================================================
-- HẾT FILE - DATABASE ĐÃ ĐƯỢC TẠO HOÀN CHỈNH
-- ===================================================================

-- Thông báo hoàn thành
SELECT 'Database student_manager đã được tạo thành công!' as status,
       'Đã tạo 7 bảng chính được tối ưu với đầy đủ index, view, procedure và trigger' as details,
       'Bảng timetable đã được tối ưu: loại bỏ 5 cột không cần thiết' as optimization,
       NOW() as created_at; 