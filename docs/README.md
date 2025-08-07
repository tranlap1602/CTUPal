# Tài liệu dự án CTUPal

Thư mục này chứa các tài liệu kỹ thuật và sơ đồ thiết kế cho dự án CTUPal.

## Nội dung

### 📊 Sơ đồ Use Case
- **`USE_CASE_DIAGRAM.md`** - Mô tả chi tiết về sơ đồ use case, các actors và chức năng
- **`use-case-diagram.puml`** - Source PlantUML cho sơ đồ use case đầy đủ
- **`use-case-diagram-simple.puml`** - Source PlantUML cho sơ đồ use case đơn giản
- **`CTUPal_Use_Case_Diagram.png`** - Hình ảnh sơ đồ use case đầy đủ
- **`CTUPal_Simplified_Use_Case_Diagram.png`** - Hình ảnh sơ đồ use case đơn giản

## Cách sử dụng

### Xem sơ đồ Use Case
1. Đọc tài liệu chi tiết tại [`USE_CASE_DIAGRAM.md`](USE_CASE_DIAGRAM.md)
2. Xem hình ảnh sơ đồ:
   - **Sơ đồ đầy đủ:** `CTUPal_Use_Case_Diagram.png`
   - **Sơ đồ đơn giản:** `CTUPal_Simplified_Use_Case_Diagram.png`

### Chỉnh sửa sơ đồ
1. Cài đặt PlantUML và Java
2. Chỉnh sửa file `.puml`
3. Generate lại hình ảnh:
   ```bash
   java -jar plantuml.jar use-case-diagram.puml
   java -jar plantuml.jar use-case-diagram-simple.puml
   ```

## Cấu trúc Use Case

### Actors
- **Sinh viên (Student)** - Người dùng chính
- **Quản trị viên (Administrator)** - Người quản lý hệ thống

### Nhóm chức năng chính
1. **Xác thực & Tài khoản** - Đăng ký, đăng nhập, quản lý profile
2. **Quản lý Tài liệu** - Upload, download, phân loại tài liệu
3. **Quản lý Chi tiêu** - Theo dõi chi tiêu, thống kê, biểu đồ
4. **Quản lý Ghi chú** - Tạo, sửa, xóa, phân loại ghi chú
5. **Quản lý Lịch học** - Tích hợp Google Calendar
6. **Dashboard** - Trang tổng quan cho sinh viên và admin
7. **Quản trị Hệ thống** - Quản lý tài khoản (admin only)

---

*Tài liệu này được cập nhật thường xuyên theo sự phát triển của dự án.*