-- Tạo bảng expenses với cấu trúc mới
-- Chạy script này để tạo bảng mới (xóa bảng cũ trước)

-- Xóa bảng cũ nếu tồn tại
DROP TABLE IF EXISTS expenses;

-- Tạo bảng mới với cấu trúc theo yêu cầu
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(100) NOT NULL COMMENT 'Danh mục chi tiêu (đóng vai trò như title)',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Số tiền (VND)',
    description TEXT COMMENT 'Ghi chú chi tiết',
    expense_date DATETIME NOT NULL COMMENT 'Ngày và giờ phát sinh chi tiêu',
    payment_method ENUM('cash', 'card') DEFAULT 'cash' COMMENT 'Phương thức: cash=tiền mặt, card=thẻ ngân hàng',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_positive_amount CHECK (amount > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý chi tiêu sinh viên (cấu trúc mới)';

-- Tạo indexes để tối ưu hiệu suất
CREATE INDEX idx_expenses_user_id ON expenses(user_id);
CREATE INDEX idx_expenses_date ON expenses(expense_date);
CREATE INDEX idx_expenses_category ON expenses(category);
CREATE INDEX idx_expenses_amount ON expenses(amount);
CREATE INDEX idx_expenses_payment_method ON expenses(payment_method);

-- Tạo view thống kê theo tháng
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

-- Hiển thị thông báo hoàn thành
SELECT 'Tạo bảng expenses thành công!' as message; 