# 💰 Quản lý Chi tiêu - StudentManager

## 📋 Mô tả
Chức năng quản lý chi tiêu cá nhân cho sinh viên với giao diện đơn giản, dễ sử dụng.

## 🗄️ Cấu trúc bảng

### Bảng `expenses`
| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| `id` | INT AUTO_INCREMENT | Khóa chính |
| `user_id` | INT | ID người dùng |
| `category` | VARCHAR(100) | Danh mục chi tiêu |
| `amount` | DECIMAL(15,2) | Số tiền (VND) |
| `description` | TEXT | Ghi chú chi tiết |
| `expense_date` | DATETIME | Ngày và giờ phát sinh |
| `payment_method` | ENUM('cash','card') | Phương thức thanh toán |

## 📊 Danh mục chi tiêu
- 🍜 **Ăn uống** - Chi phí ăn uống hàng ngày
- 🚌 **Di chuyển** - Chi phí đi lại, xe bus, taxi
- 📚 **Học tập** - Sách vở, dụng cụ học tập, laptop
- 🎬 **Giải trí** - Xem phim, chơi game, du lịch
- 🛒 **Mua sắm** - Quần áo, đồ dùng cá nhân
- 🏥 **Y tế** - Khám bệnh, thuốc men
- 📝 **Khác** - Chi phí khác

## 💳 Phương thức thanh toán
- 💵 **Tiền mặt** (cash)
- 💳 **Thẻ ngân hàng** (card)

## 🚀 Cách sử dụng

### 1. Tạo bảng
```sql
-- Chạy SQL trong phpMyAdmin hoặc MySQL client
-- Copy nội dung từ file: database/create_expenses_table_new.sql
```

### 2. Sử dụng chức năng
```bash
# Truy cập trang Thu Chi
http://localhost/StudentManager/expenses.php
```

## ✨ Tính năng

### Thêm chi tiêu
- Chọn danh mục
- Nhập số tiền (bước nhảy 1000 VNĐ)
- Chọn ngày và giờ
- Chọn phương thức thanh toán
- Thêm ghi chú (tùy chọn)

### Xem danh sách
- Hiển thị theo thời gian mới nhất
- Thông tin chi tiết: danh mục, số tiền, thời gian, phương thức
- Ghi chú chi tiết

### Bộ lọc
- Lọc theo danh mục
- Lọc theo tháng
- Xóa bộ lọc

### Thống kê
- Tổng chi tiêu tháng hiện tại
- Chi tiêu hôm nay
- Trung bình chi tiêu/ngày

### Xóa chi tiêu
- Xác nhận trước khi xóa
- Chỉ xóa chi tiêu của chính mình

## 🔧 Files chính

- `expenses.php` - Trang chính quản lý chi tiêu
- `database/create_expenses_table_new.sql` - SQL tạo bảng

## ⚠️ Lưu ý
- Cần đăng nhập để sử dụng
- Dữ liệu được lưu theo user_id
- Có thể xóa chi tiêu nhưng không thể sửa
- Bước nhảy số tiền: 1000 VNĐ 