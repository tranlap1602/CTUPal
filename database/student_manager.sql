-- Tạo cơ sở dữ liệu StudentManager
CREATE DATABASE IF NOT EXISTS student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_manager;

-- Bảng users - quản lý tài khoản sinh viên CTU
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) COMMENT 'Họ và tên sinh viên',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email CTU format: tenb1234567@student.ctu.edu.vn',
    mssv VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã số sinh viên (tự động tách từ email)',
    username VARCHAR(100) UNIQUE NOT NULL COMMENT 'Username để đăng nhập (= email)',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa bằng password_hash',
    phone VARCHAR(20) COMMENT 'Số điện thoại (không bắt buộc)',
    birthday DATE COMMENT 'Ngày sinh (không bắt buộc)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo tài khoản',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý tài khoản sinh viên CTU';

-- Bảng timetable - thời khóa biểu
CREATE TABLE timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '1=Monday, 2=Tuesday, ..., 7=Sunday',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    classroom VARCHAR(50),
    teacher VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_day (user_id, day_of_week)
);

-- Bảng documents - tài liệu
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category ENUM('lecture', 'assignment', 'exam', 'reference', 'other') DEFAULT 'other',
    subject VARCHAR(100),
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT,
    file_type VARCHAR(50),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_category (user_id, category),
    INDEX idx_subject (subject)
);

-- Bảng expenses - chi tiêu
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    category ENUM('food', 'transport', 'education', 'entertainment', 'shopping', 'health', 'other') DEFAULT 'other',
    expense_date DATE NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, expense_date),
    INDEX idx_user_category (user_id, category)
);

-- Bảng notes - ghi chú
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('study', 'personal', 'work', 'idea', 'todo', 'other') DEFAULT 'other',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    tags VARCHAR(500),
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_category (user_id, category),
    INDEX idx_user_priority (user_id, priority),
    FULLTEXT(title, content, tags)
);

-- Bảng settings - cài đặt người dùng
CREATE TABLE user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key)
);

-- Chèn dữ liệu mẫu

-- Tạo user demo với cấu trúc mới
INSERT INTO users (name, email, mssv, username, password, phone, birthday) VALUES 
('Quản trị viên', 'adminb2204999@student.ctu.edu.vn', 'b2204999', 'adminb2204999@student.ctu.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', '1995-01-01'),
('Nguyễn Văn A', 'nguyenvanab2204945@student.ctu.edu.vn', 'b2204945', 'nguyenvanab2204945@student.ctu.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', '2004-03-15'),
('Trần Thị B', 'tranthib2204946@student.ctu.edu.vn', 'b2204946', 'tranthib2204946@student.ctu.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987123456', '2004-07-20');

-- Password cho tất cả user demo là: password123

-- Thời khóa biểu mẫu cho user ID 2 (student1)
INSERT INTO timetable (user_id, subject_name, day_of_week, start_time, end_time, classroom, teacher) VALUES
(2, 'Toán học', 2, '07:00:00', '08:30:00', 'A101', 'TS. Nguyễn Văn X'),
(2, 'Vật lý', 4, '07:00:00', '08:30:00', 'B205', 'PGS. Trần Thị Y'),
(2, 'Hóa học', 6, '07:00:00', '08:30:00', 'C302', 'ThS. Lê Văn Z'),
(2, 'Tiếng Anh', 3, '08:45:00', '10:15:00', 'D104', 'Cô Maria'),
(2, 'Văn học', 5, '08:45:00', '10:15:00', 'A203', 'ThS. Phạm Thị M'),
(2, 'Lịch sử', 7, '08:45:00', '10:15:00', 'E105', 'TS. Hoàng Văn N'),
(2, 'Thể dục', 2, '10:30:00', '12:00:00', 'Sân thể thao', 'Thầy Minh'),
(2, 'Sinh học', 4, '10:30:00', '12:00:00', 'F201', 'PGS. Vũ Thị P'),
(2, 'Địa lý', 6, '10:30:00', '12:00:00', 'G303', 'ThS. Đào Văn Q'),
(2, 'Tin học', 3, '13:00:00', '14:30:00', 'H401', 'ThS. Bùi Thị R'),
(2, 'Mỹ thuật', 5, '13:00:00', '14:30:00', 'I102', 'Cô Lan'),
(2, 'Âm nhạc', 7, '13:00:00', '14:30:00', 'J204', 'Thầy Hùng');

-- Dữ liệu chi tiêu mẫu
INSERT INTO expenses (user_id, title, amount, category, expense_date, note) VALUES
(2, 'Ăn trưa', 45000, 'food', '2025-01-15', 'Cơm văn phòng'),
(2, 'Xe bus đi học', 20000, 'transport', '2025-01-15', 'Vé xe bus 2 chiều'),
(2, 'Mua sách giáo khoa', 150000, 'education', '2025-01-14', 'Sách Toán học cao cấp'),
(2, 'Xem phim', 80000, 'entertainment', '2025-01-13', 'Rạp CGV'),
(2, 'Mua quần áo', 200000, 'shopping', '2025-01-12', 'Áo khoác mùa đông'),
(2, 'Khám bác sĩ', 300000, 'health', '2025-01-11', 'Khám tổng quát'),
(2, 'Ăn sáng', 25000, 'food', '2025-01-15', 'Bánh mì + cà phê'),
(2, 'Taxi về nhà', 50000, 'transport', '2025-01-14', 'Trời mưa to'),
(2, 'Mua bút viết', 15000, 'education', '2025-01-13', 'Bút bi xanh'),
(2, 'Karaoke', 120000, 'entertainment', '2025-01-12', 'Cùng bạn bè');

-- Dữ liệu ghi chú mẫu
INSERT INTO notes (user_id, title, content, category, priority, tags) VALUES
(2, 'Công thức Toán học quan trọng', 'Các công thức tích phân cần nhớ cho kỳ thi:\n∫x^n dx = x^(n+1)/(n+1) + C\n∫sin(x) dx = -cos(x) + C\n∫cos(x) dx = sin(x) + C\n∫e^x dx = e^x + C\n∫(1/x) dx = ln|x| + C', 'study', 'high', 'toán học,công thức,thi cuối kỳ'),
(2, 'Danh sách việc cần làm tuần này', '- Hoàn thành bài tập Vật lý chương 3\n- Chuẩn bị thuyết trình môn Văn về Nguyễn Du\n- Đăng ký học phần kỳ tới\n- Mua sách tham khảo cho môn Hóa\n- Nộp báo cáo thực hành Tin học', 'todo', 'medium', 'todo,tuần này'),
(2, 'Ý tưởng cho dự án cuối kỳ', 'Tạo ứng dụng quản lý sinh viên với các tính năng:\n- Quản lý thời khóa biểu\n- Theo dõi điểm số\n- Quản lý tài liệu học tập\n- Ghi chú và nhắc nhở\n- Thống kê chi tiêu\n\nCông nghệ sử dụng: PHP, MySQL, JavaScript, CSS', 'idea', 'low', 'dự án,ý tưởng,cuối kỳ'),
(2, 'Ghi chú cá nhân', 'Cần cải thiện thói quen học tập:\n- Dậy sớm hơn (6:00 AM)\n- Đọc sách ít nhất 1 giờ/ngày\n- Tập thể dục 3 lần/tuần\n- Hạn chế sử dụng điện thoại khi học', 'personal', 'medium', 'cá nhân,thói quen,cải thiện'),
(2, 'Kế hoạch nghề nghiệp', 'Mục tiêu 5 năm tới:\n- Tốt nghiệp đại học với GPA > 3.5\n- Học thêm các khóa học lập trình\n- Thực tập tại công ty công nghệ\n- Phát triển portfolio dự án cá nhân\n- Tìm việc làm tại công ty lớn', 'work', 'high', 'nghề nghiệp,kế hoạch,tương lai');

-- Cài đặt mẫu
INSERT INTO user_settings (user_id, setting_key, setting_value) VALUES
(2, 'theme', 'light'),
(2, 'language', 'vi'),
(2, 'notifications', 'true'),
(2, 'budget_limit', '3000000'),
(2, 'currency', 'VND');

-- Tạo view để thống kê chi tiêu theo tháng
CREATE VIEW monthly_expense_summary AS
SELECT 
    user_id,
    YEAR(expense_date) as year,
    MONTH(expense_date) as month,
    category,
    SUM(amount) as total_amount,
    COUNT(*) as transaction_count
FROM expenses 
GROUP BY user_id, YEAR(expense_date), MONTH(expense_date), category;

-- Tạo view để lấy thời khóa biểu theo ngày
CREATE VIEW daily_schedule AS
SELECT 
    t.*,
    u.name as student_name,
    u.mssv as student_mssv,
    CASE t.day_of_week
        WHEN 1 THEN 'Chủ nhật'
        WHEN 2 THEN 'Thứ hai'
        WHEN 3 THEN 'Thứ ba'
        WHEN 4 THEN 'Thứ tư'
        WHEN 5 THEN 'Thứ năm'
        WHEN 6 THEN 'Thứ sáu'
        WHEN 7 THEN 'Thứ bảy'
    END as day_name
FROM timetable t
JOIN users u ON t.user_id = u.id
ORDER BY t.user_id, t.day_of_week, t.start_time;

-- Tạo stored procedure để lấy tổng chi tiêu theo danh mục
DELIMITER //
CREATE PROCEDURE GetExpensesByCategory(IN userId INT, IN fromDate DATE, IN toDate DATE)
BEGIN
    SELECT 
        category,
        SUM(amount) as total_amount,
        COUNT(*) as transaction_count,
        AVG(amount) as average_amount
    FROM expenses 
    WHERE user_id = userId 
        AND expense_date BETWEEN fromDate AND toDate
    GROUP BY category
    ORDER BY total_amount DESC;
END //
DELIMITER ;

-- Tạo trigger để cập nhật thống kê khi thêm chi tiêu mới
DELIMITER //
CREATE TRIGGER update_expense_stats AFTER INSERT ON expenses
FOR EACH ROW
BEGIN
    -- Có thể thêm logic cập nhật bảng thống kê ở đây
    -- Ví dụ: cập nhật tổng chi tiêu tháng hiện tại
    INSERT INTO user_settings (user_id, setting_key, setting_value)
    VALUES (NEW.user_id, 'last_expense_date', NEW.expense_date)
    ON DUPLICATE KEY UPDATE setting_value = NEW.expense_date;
END //
DELIMITER ;

-- Tạo index để tăng tốc độ truy vấn
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_mssv ON users(mssv);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_timetable_user_day_time ON timetable(user_id, day_of_week, start_time);
CREATE INDEX idx_expenses_user_date_category ON expenses(user_id, expense_date, category);
CREATE INDEX idx_notes_user_category_priority ON notes(user_id, category, priority);
CREATE INDEX idx_documents_user_upload_date ON documents(user_id, upload_date);

-- Thêm ràng buộc kiểm tra
ALTER TABLE expenses ADD CONSTRAINT chk_positive_amount CHECK (amount > 0);
ALTER TABLE timetable ADD CONSTRAINT chk_valid_day CHECK (day_of_week BETWEEN 1 AND 7);
ALTER TABLE timetable ADD CONSTRAINT chk_valid_time CHECK (start_time < end_time);

COMMIT; 