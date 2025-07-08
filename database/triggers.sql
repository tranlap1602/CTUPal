-- ===================================================================
-- DATABASE TRIGGERS - CÁC TRIGGER TỰ ĐỘNG
-- ===================================================================
-- Mục đích: Tạo các trigger để duy trì tính toàn vẹn dữ liệu
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

DELIMITER //

-- ===================================================================
-- TRIGGER CẬP NHẬT LAST_LOGIN KHI USER ĐĂNG NHẬP
-- ===================================================================
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

-- ===================================================================
-- TRIGGER LOG KHI TẠO MỚI USER
-- ===================================================================
CREATE TRIGGER log_user_creation
    AFTER INSERT ON users
    FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, table_name, record_id, new_values)
    VALUES (NEW.id, 'CREATE_USER', 'users', NEW.id, 
            CONCAT('{"name": "', NEW.name, '", "email": "', NEW.email, '", "mssv": "', NEW.mssv, '"}'));
END //

-- ===================================================================
-- TRIGGER TẠO CÀI ĐẶT MẶC ĐỊNH CHO USER MỚI
-- ===================================================================
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

-- Hiển thị thông báo
SELECT 'Tất cả triggers đã được tạo thành công!' as status,
       'Tính toàn vẹn dữ liệu được đảm bảo tự động' as details; 