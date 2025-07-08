-- ===================================================================
-- DATABASE VIEWS - CÁC VIEW HỮU ÍCH
-- ===================================================================
-- Mục đích: Tạo các view để dễ dàng truy vấn dữ liệu thống kê
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

-- ===================================================================
-- VIEW THỐNG KÊ CHI TIÊU THEO THÁNG
-- ===================================================================
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

-- ===================================================================
-- VIEW THỜI KHÓA BIỂU THEO NGÀY
-- ===================================================================
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

-- ===================================================================
-- VIEW THỐNG KÊ TÀI LIỆU THEO DANH MỤC
-- ===================================================================
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

-- ===================================================================
-- VIEW GHI CHÚ ƯU TIÊN CAO CHƯA HOÀN THÀNH
-- ===================================================================
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

-- Hiển thị thông báo
SELECT 'Tất cả views đã được tạo thành công!' as status,
       'Có thể sử dụng các view để thống kê dữ liệu' as details; 