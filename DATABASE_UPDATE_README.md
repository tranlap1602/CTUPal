# Hướng dẫn cập nhật cơ sở dữ liệu

## Thay đổi tên cột ID

Đã thay đổi tên các cột ID trong cơ sở dữ liệu để rõ ràng hơn:

- `documents.id` → `documents.doc_id`
- `expenses.id` → `expenses.expense_id`  
- `notes.id` → `notes.note_id`

## Các bước cập nhật

### 1. Backup cơ sở dữ liệu
```sql
mysqldump -u root -p student_manager > backup_before_update.sql
```

### 2. Chạy script cập nhật
```sql
mysql -u root -p student_manager < update_database.sql
```

### 3. Kiểm tra kết quả
```sql
DESCRIBE documents;
DESCRIBE expenses;
DESCRIBE notes;
```

## Các file đã được cập nhật

### File PHP chính:
- `documents.php` - Cập nhật tất cả truy vấn SQL
- `expenses.php` - Cập nhật tất cả truy vấn SQL  
- `notes.php` - Cập nhật tất cả truy vấn SQL

### File View:
- `views/documents-view.php` - Cập nhật tham chiếu `$doc['id']` → `$doc['doc_id']`
- `views/expenses-view.php` - Cập nhật tham chiếu `$expense['id']` → `$expense['expense_id']`
- `views/notes-view.php` - Cập nhật tham chiếu `$note['id']` → `$note['note_id']`

### File cơ sở dữ liệu:
- `database.sql` - Cập nhật schema mới
- `update_database.sql` - Script cập nhật cho DB hiện tại

## Lưu ý quan trọng

1. **Backup trước khi cập nhật**: Luôn backup cơ sở dữ liệu trước khi chạy script cập nhật
2. **Kiểm tra kết nối**: Đảm bảo ứng dụng vẫn hoạt động bình thường sau khi cập nhật
3. **Test chức năng**: Kiểm tra các chức năng CRUD của documents, expenses, notes

## Rollback (nếu cần)

Nếu cần rollback, sử dụng backup:
```sql
mysql -u root -p student_manager < backup_before_update.sql
``` 