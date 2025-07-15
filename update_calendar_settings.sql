-- Cập nhật database để thêm Calendar ID setting
-- Chạy script này để thêm trường google_calendar_id vào user_settings

-- Thêm setting mặc định cho tất cả user hiện có
INSERT IGNORE INTO user_settings (user_id, setting_key, setting_value, setting_type, description)
SELECT id, 'google_calendar_id', '', 'string', 'Google Calendar ID của người dùng'
FROM users;

-- Cập nhật trigger để tự động thêm setting cho user mới
DROP TRIGGER IF EXISTS tr_create_user_settings;

DELIMITER //

CREATE TRIGGER tr_create_user_settings
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_settings (user_id, setting_key, setting_value, setting_type, description) VALUES
    (NEW.id, 'theme', 'light', 'string', 'Giao diện sáng/tối'),
    (NEW.id, 'language', 'vi', 'string', 'Ngôn ngữ hiển thị'),
    (NEW.id, 'notifications', 'true', 'boolean', 'Bật/tắt thông báo'),
    (NEW.id, 'auto_backup', 'true', 'boolean', 'Tự động backup dữ liệu'),
    (NEW.id, 'dashboard_layout', 'default', 'string', 'Bố cục dashboard'),
    (NEW.id, 'google_calendar_id', '', 'string', 'Google Calendar ID của người dùng');
END //

DELIMITER ;

-- Thông báo hoàn thành
SELECT 'Đã cập nhật thành công database để hỗ trợ Google Calendar ID!' as message; 