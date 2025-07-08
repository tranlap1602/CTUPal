-- ===================================================================
-- BẢNG TIMETABLE - THỜI KHÓA BIỂU (CÓ HỖ TRỢ ICS)
-- ===================================================================
-- Mục đích: Quản lý thời khóa biểu sinh viên với hỗ trợ import ICS
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

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

-- Hiển thị thông báo
SELECT 'Bảng timetable đã được tạo thành công!' as status; 