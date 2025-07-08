-- ===================================================================
-- STORED PROCEDURES - CÁC THỦ TỤC LƯU TRỮ
-- ===================================================================
-- Mục đích: Tạo các stored procedure cho các thao tác phức tạp
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

DELIMITER //

-- ===================================================================
-- PROCEDURE THỐNG KÊ CHI TIÊU THEO KHOẢNG THỜI GIAN
-- ===================================================================
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

-- ===================================================================
-- PROCEDURE LẤY THỜI KHÓA BIỂU THEO TUẦN
-- ===================================================================
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

-- ===================================================================
-- PROCEDURE DỌN DẸP DỮ LIỆU CŨ
-- ===================================================================
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

-- Hiển thị thông báo
SELECT 'Tất cả stored procedures đã được tạo thành công!' as status,
       'Có thể gọi các procedure để thực hiện thao tác phức tạp' as details; 