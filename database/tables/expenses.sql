-- ===================================================================
-- BẢNG EXPENSES - QUẢN LÝ CHI TIÊU
-- ===================================================================
-- Mục đích: Theo dõi và quản lý chi tiêu cá nhân của sinh viên
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

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

-- Hiển thị thông báo
SELECT 'Bảng expenses đã được tạo thành công!' as status; 