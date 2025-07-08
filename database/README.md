# Student Manager Database - Modular Structure

## 📁 Cấu trúc file database

```
database/
├── setup.sql              # Tạo database và cấu hình cơ bản
├── import.sql             # Import tất cả file theo đúng thứ tự
├── indexes.sql            # Tất cả indexes tối ưu hiệu suất
├── views.sql              # Views thống kê hữu ích  
├── procedures.sql         # Stored procedures
├── triggers.sql           # Triggers tự động
├── tables/                # Thư mục chứa từng bảng riêng biệt
│   ├── users.sql          # Bảng quản lý tài khoản sinh viên
│   ├── timetable.sql      # Bảng thời khóa biểu (có hỗ trợ ICS)
│   ├── documents.sql      # Bảng quản lý tài liệu học tập
│   ├── expenses.sql       # Bảng quản lý chi tiêu
│   ├── notes.sql          # Bảng ghi chú và todo
│   ├── user_settings.sql  # Bảng cài đặt người dùng
│   └── activity_logs.sql  # Bảng nhật ký hoạt động
└── README.md              # File này
```

## 🚀 Cách sử dụng

### Import toàn bộ database (Khuyến nghị)
```sql
SOURCE database/import.sql;
```

### Import từng phần riêng biệt
```sql
-- 1. Tạo database
SOURCE database/setup.sql;

-- 2. Tạo bảng users trước (vì các bảng khác phụ thuộc vào nó)
SOURCE database/tables/users.sql;

-- 3. Tạo các bảng còn lại
SOURCE database/tables/timetable.sql;
SOURCE database/tables/documents.sql;
SOURCE database/tables/expenses.sql;
SOURCE database/tables/notes.sql;
SOURCE database/tables/user_settings.sql;
SOURCE database/tables/activity_logs.sql;

-- 4. Tạo indexes
SOURCE database/indexes.sql;

-- 5. Tạo views
SOURCE database/views.sql;

-- 6. Tạo procedures
SOURCE database/procedures.sql;

-- 7. Tạo triggers
SOURCE database/triggers.sql;
```

## 📊 Thống kê database

| Thành phần | Số lượng | Mô tả |
|------------|----------|--------|
| **Bảng** | 7 | users, timetable, documents, expenses, notes, user_settings, activity_logs |
| **Indexes** | 30+ | Tối ưu hiệu suất truy vấn |
| **Views** | 4 | Thống kê chi tiêu, lịch học, tài liệu, ghi chú ưu tiên |
| **Procedures** | 3 | Thống kê chi tiêu, lịch tuần, dọn dẹp dữ liệu |
| **Triggers** | 3 | Log hoạt động, tạo settings mặc định |

## 🔧 Tính năng chính

### Bảng Users
- Quản lý tài khoản sinh viên CTU
- Tự động tách MSSV từ email CTU
- Theo dõi trạng thái và lần login cuối

### Bảng Timetable  
- Thời khóa biểu với hỗ trợ import ICS
- Quản lý phạm vi tuần học
- Xử lý lịch lặp lại và ngày ngoại lệ

### Bảng Documents
- Quản lý tài liệu học tập  
- Phân loại theo môn học và danh mục
- Theo dõi lượt tải và yêu thích

### Bảng Expenses
- Theo dõi chi tiêu cá nhân
- Phân loại chi tiết và thống kê
- Hỗ trợ chi tiêu định kỳ

### Bảng Notes
- Ghi chú và todo list thông minh
- Ưu tiên và nhắc nhở
- Hỗ trợ markdown và file đính kèm

### Bảng User Settings
- Cài đặt cá nhân linh hoạt
- Hỗ trợ JSON và nhiều kiểu dữ liệu
- Tự động tạo settings mặc định

### Bảng Activity Logs
- Ghi lại mọi hoạt động người dùng
- Theo dõi thay đổi dữ liệu
- Hỗ trợ audit và debug

## 🎯 Ưu điểm của cấu trúc modular

✅ **Dễ bảo trì**: Mỗi bảng trong file riêng  
✅ **Linh hoạt**: Import theo nhu cầu  
✅ **Rõ ràng**: Cấu trúc được tổ chức logic  
✅ **Tái sử dụng**: Có thể sử dụng riêng từng phần  
✅ **Kiểm soát**: Dễ theo dõi thay đổi  

## ⚠️ Lưu ý quan trọng

1. **Thứ tự import**: Phải tạo bảng `users` trước vì các bảng khác có foreign key tới nó
2. **Dependencies**: Tables → Indexes → Views → Procedures → Triggers  
3. **Encoding**: Tất cả file sử dụng UTF-8 để hỗ trợ tiếng Việt
4. **Engine**: Sử dụng InnoDB để đảm bảo ACID và foreign keys

## 🔍 Kiểm tra sau khi import

```sql
-- Kiểm tra bảng
SHOW TABLES;

-- Kiểm tra indexes  
SHOW INDEX FROM users;

-- Kiểm tra views
SHOW CREATE VIEW daily_schedule;

-- Kiểm tra procedures
SHOW PROCEDURE STATUS WHERE Db = 'student_manager';

-- Kiểm tra triggers
SHOW TRIGGERS;
```

## 📈 Version History

- **v2.0**: Cấu trúc modular, tối ưu bảng timetable
- **v1.0**: Cấu trúc monolithic ban đầu 