# Hướng dẫn sử dụng chức năng Import .ics

## Tổng quan

Chức năng import .ics cho phép bạn nhập thời khóa biểu từ file iCalendar (.ics) vào hệ thống Student Manager. Hệ thống hỗ trợ:

- ✅ Import file .ics từ Google Calendar, Outlook, Apple Calendar
- ✅ Xử lý sự kiện lặp lại (RRULE) với giới hạn COUNT để tránh lặp vô hạn
- ✅ Xử lý các ngày ngoại lệ (EXDATE)
- ✅ Tự động phân tích tên môn học và phòng học
- ✅ Kiểm tra trùng lặp trước khi import
- ✅ Tôn trọng chính xác giá trị COUNT từ file .ics, chỉ giới hạn khi quá lớn (>1000)

## Cách sử dụng

### 1. Truy cập chức năng import
- Đăng nhập vào hệ thống Student Manager
- Vào trang "Thời khóa biểu" (timetable.php)
- Click nút "Import/Thêm môn học"
- Chọn tab "Import từ .ics"

### 2. Upload file .ics
- Click vào khu vực "Chọn file .ics" hoặc kéo thả file vào đó
- Chọn file .ics từ máy tính của bạn
- Hệ thống chỉ chấp nhận file có đuôi .ics, tối đa 5MB

### 3. Tùy chọn xóa dữ liệu cũ
- Nếu muốn xóa toàn bộ thời khóa biểu hiện tại và thay thế bằng dữ liệu mới, tick vào checkbox "Xóa toàn bộ thời khóa biểu cũ trước khi import"
- Nếu không tick, dữ liệu mới sẽ được thêm vào cùng với dữ liệu hiện tại

### 4. Thực hiện import
- Click nút "Import từ .ics"
- Hệ thống sẽ xử lý file và hiển thị kết quả
- Trang sẽ tự động reload sau 3 giây để hiển thị dữ liệu mới

## Định dạng file .ics được hỗ trợ

### Cấu trúc cơ bản
```
BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:Lịch học CTU
X-WR-TIMEZONE:Asia/Ho_Chi_Minh

BEGIN:VEVENT
DTSTART;TZID=Asia/Ho_Chi_Minh:20250127T070000
DTEND;TZID=Asia/Ho_Chi_Minh:20250127T094000
RRULE:FREQ=WEEKLY;COUNT=5
SUMMARY:Lập trình Web, A101
DESCRIPTION:Tiết 1, phòng A101
UID:demo1@student.ctu.edu.vn
END:VEVENT

END:VCALENDAR
```

### Các trường quan trọng
- `DTSTART`: Thời gian bắt đầu sự kiện
- `DTEND`: Thời gian kết thúc sự kiện
- `SUMMARY`: Tên môn học và phòng học (định dạng: "Tên môn học, Phòng học")
- `DESCRIPTION`: Mô tả chi tiết (định dạng: "Tiết X, phòng Y")
- `RRULE`: Quy tắc lặp lại sự kiện
- `EXDATE`: Các ngày ngoại lệ (không diễn ra sự kiện)

### Xử lý RRULE
Hệ thống hỗ trợ xử lý các quy tắc lặp lại:
- `FREQ=WEEKLY`: Lặp lại hàng tuần
- `COUNT=X`: Số lần lặp lại (sử dụng chính xác giá trị từ file .ics)
- `EXDATE`: Các ngày bị loại trừ

## File demo

Đã tạo file `demo-schedule.ics` để bạn test chức năng import. File này chứa:
- 3 môn học: Lập trình Web, Cơ sở dữ liệu, Toán học
- Mỗi môn học lặp lại 5 tuần
- Có thông tin phòng học và tiết học

## Cấu trúc code

### 1. ICSParser.php
Class chính để phân tích file .ics:
- `parseFile()`: Đọc và phân tích file .ics
- `parseContent()`: Phân tích nội dung .ics
- `processEvent()`: Xử lý từng sự kiện
- `processRecurringEvent()`: Xử lý sự kiện lặp lại với RRULE
- `createTimetableEvent()`: Chuyển đổi sang định dạng database

### 2. timetable-import-ics.php
Script xử lý upload và import:
- Kiểm tra file upload
- Sử dụng ICSParser để phân tích
- Lưu vào database với transaction
- Trả về kết quả JSON

### 3. timetable-import.php
Giao diện import:
- Tab "Import từ .ics"
- Form upload file
- Hiển thị kết quả import
- JavaScript xử lý AJAX

## Bảo mật

- File upload được lưu trong thư mục `uploads/ics/` có bảo vệ .htaccess
- Kiểm tra loại file và kích thước
- Sử dụng prepared statements để tránh SQL injection
- Xóa file upload sau khi xử lý
- Tôn trọng giá trị COUNT từ file .ics, chỉ giới hạn khi quá lớn (>1000) để tránh spam

## Ghi log

Tất cả hoạt động import được ghi log vào file `logs/activity.log` với định dạng:
```
[2025-01-29 10:30:45] User 2 - import_ics: Import ICS: 15 thành công, 3 trùng lặp, 0 lỗi
```

## Xử lý lỗi

Hệ thống xử lý các lỗi phổ biến:
- File không tồn tại hoặc không đọc được
- Định dạng .ics không hợp lệ
- Lỗi database khi lưu dữ liệu
- File quá lớn (>5MB)
- Loại file không được hỗ trợ

## Giới hạn

- Chỉ hỗ trợ RRULE với FREQ=WEEKLY
- Tôn trọng chính xác giá trị COUNT từ file .ics (chỉ giới hạn khi >1000 để tránh spam)
- Kích thước file tối đa 5MB
- Chỉ hỗ trợ timezone Asia/Ho_Chi_Minh

## Phát triển trong tương lai

- Hỗ trợ thêm các loại RRULE khác (DAILY, MONTHLY, YEARLY)
- Import từ URL .ics
- Sync định kỳ với calendar bên ngoài
- Hỗ trợ nhiều timezone
- Export thời khóa biểu sang .ics 