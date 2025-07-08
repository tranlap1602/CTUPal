-- ===================================================================
-- DATABASE INDEXES - CÁC CHỈ MỤC TỐI ƯU HIỆU SUẤT
-- ===================================================================
-- Mục đích: Tạo các index để tăng tốc độ truy vấn
-- Tác giả: Student Manager System
-- Ngày tạo: 2025-01-29
-- ===================================================================

USE student_manager;

-- ===================================================================
-- INDEX CHO BẢNG USERS
-- ===================================================================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_mssv ON users(mssv);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_users_last_login ON users(last_login);

-- ===================================================================
-- INDEX CHO BẢNG TIMETABLE
-- ===================================================================
CREATE INDEX idx_timetable_user_day ON timetable(user_id, day_of_week);
CREATE INDEX idx_timetable_user_day_time ON timetable(user_id, day_of_week, start_time);
CREATE INDEX idx_timetable_date_range ON timetable(user_id, start_date, end_date);
CREATE INDEX idx_timetable_subject ON timetable(subject_code, subject_name);
CREATE INDEX idx_timetable_ics_uid ON timetable(ics_uid);
CREATE INDEX idx_timetable_teacher ON timetable(teacher);
CREATE INDEX idx_timetable_classroom ON timetable(classroom);

-- ===================================================================
-- INDEX CHO BẢNG DOCUMENTS
-- ===================================================================
CREATE INDEX idx_documents_user_category ON documents(user_id, category);
CREATE INDEX idx_documents_subject ON documents(subject, subject_code);
CREATE INDEX idx_documents_upload_date ON documents(user_id, upload_date);
CREATE INDEX idx_documents_public ON documents(is_public);
CREATE INDEX idx_documents_favorite ON documents(user_id, is_favorite);
CREATE FULLTEXT INDEX idx_documents_search ON documents(title, description, tags);

-- ===================================================================
-- INDEX CHO BẢNG EXPENSES
-- ===================================================================
CREATE INDEX idx_expenses_user_date ON expenses(user_id, expense_date);
CREATE INDEX idx_expenses_user_category ON expenses(user_id, category);
CREATE INDEX idx_expenses_date_range ON expenses(expense_date);
CREATE INDEX idx_expenses_amount ON expenses(amount);
CREATE INDEX idx_expenses_recurring ON expenses(is_recurring);

-- ===================================================================
-- INDEX CHO BẢNG NOTES
-- ===================================================================
CREATE INDEX idx_notes_user_category ON notes(user_id, category);
CREATE INDEX idx_notes_user_priority ON notes(user_id, priority);
CREATE INDEX idx_notes_status ON notes(user_id, status);
CREATE INDEX idx_notes_pinned ON notes(user_id, is_pinned);
CREATE INDEX idx_notes_reminder ON notes(reminder_date);
CREATE INDEX idx_notes_due_date ON notes(due_date);
CREATE FULLTEXT INDEX idx_notes_search ON notes(title, content, tags);

-- ===================================================================
-- INDEX CHO BẢNG USER_SETTINGS
-- ===================================================================
CREATE INDEX idx_settings_key ON user_settings(setting_key);
CREATE INDEX idx_settings_type ON user_settings(setting_type);

-- ===================================================================
-- INDEX CHO BẢNG ACTIVITY_LOGS
-- ===================================================================
CREATE INDEX idx_logs_user_date ON activity_logs(user_id, created_at);
CREATE INDEX idx_logs_action ON activity_logs(action);
CREATE INDEX idx_logs_table ON activity_logs(table_name, record_id);

-- Hiển thị thông báo
SELECT 'Tất cả indexes đã được tạo thành công!' as status,
       'Hiệu suất truy vấn đã được tối ưu' as details; 