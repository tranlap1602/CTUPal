# 🎓 StudentManager - Hệ thống Quản lý Sinh viên

## 📖 Giới thiệu
StudentManager là một ứng dụng web đơn giản được phát triển bằng PHP thuần (không framework) dành cho sinh viên quản lý thông tin cá nhân và hoạt động học tập.

## ✨ Tính năng chính

### 🏠 Dashboard (Trang chủ)
- Hiển thị thống kê nhanh: ghi chú cần làm, chi tiêu tháng, tài liệu đã lưu
- Cards chức năng với thiết kế gradient đẹp mắt
- Responsive design hoàn hảo trên mọi thiết bị

### 📅 Thời khóa biểu  
- Xem lịch học theo tuần dạng bảng
- Thêm/sửa/xóa môn học trực tiếp trên giao diện
- Import thời khóa biểu từ file Excel
- Hiển thị màu sắc khác nhau cho từng môn học

### 📄 Quản lý Tài liệu
- Upload và lưu trữ tài liệu học tập
- Phân loại theo danh mục (Bài giảng, Bài tập, Đề thi...)
- Tìm kiếm và lọc tài liệu
- Download và xem trước file

### 💰 Quản lý Chi tiêu
- Theo dõi các khoản chi tiêu cá nhân
- Phân loại theo danh mục (Ăn uống, Học tập, Giải trí...)
- Báo cáo chi tiêu theo tháng/quý
- Biểu đồ thống kê trực quan

### 📝 Ghi chú cá nhân
- Tạo và quản lý ghi chú học tập
- To-do list với tính năng đánh dấu hoàn thành
- Phân loại ghi chú theo mức độ ưu tiên
- Tìm kiếm nhanh trong ghi chú

### 👤 Thông tin cá nhân
- Cập nhật thông tin tài khoản
- Đổi mật khẩu bảo mật
- Quản lý thông tin liên hệ

## 🛠️ Công nghệ sử dụng

### Backend
- **PHP 8.0+** - Ngôn ngữ lập trình chính
- **MySQL** - Cơ sở dữ liệu quan hệ
- **PDO** - Kết nối database bảo mật

### Frontend  
- **Tailwind CSS** - Framework CSS utility-first
- **Font Awesome** - Bộ icons đẹp mắt
- **JavaScript** - Tương tác động và validation

### Environment
- **XAMPP** - Môi trường phát triển local
- **Apache** - Web server
- **phpMyAdmin** - Quản lý database

## 📱 Responsive Design

### Mobile First Approach
- **Header thông minh**: Hiển thị logo rút gọn trên mobile, đầy đủ trên desktop
- **Navigation responsive**: Menu collapsible trên mobile với animation mượt mà
- **User greeting**: 
  - Mobile: Chỉ hiển thị icon user
  - Tablet: "Chào [Tên]!"  
  - Desktop: "Xin chào, [Họ và tên]!"

### Breakpoints chính
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px  
- **Desktop**: > 1024px

## 🎨 Cải tiến gần đây

### ✅ v1.1 - Hiển thị tên thân thiện
- **Trước**: Hiển thị username/email trong header
- **Sau**: Hiển thị tên thật của người dùng
- **Logic**: Ưu tiên `user_name` từ session, fallback về `username`
- **Responsive**: Tên đầy đủ (desktop) → Tên đầu tiên (tablet) → Icon (mobile)

### ✅ v1.1 - Navigation mobile cải tiến
- **Mobile menu**: Collapsible với animation rotate icon
- **Desktop**: Horizontal menu đầy đủ với hover effects
- **Accessibility**: Focus states và keyboard navigation

### ✅ v1.1 - Header responsive hoàn thiện
- **Logo adaptive**: Kích thước và nội dung thay đổi theo viewport
- **Breadcrumb**: Ẩn trên mobile để tiết kiệm không gian
- **Logout button**: Icon only (mobile) → Text + Icon (desktop)

## 📁 Cấu trúc thư mục

```
StudentManager/
├── 📄 index.php              # Trang chủ dashboard
├── 📄 login.php              # Đăng nhập
├── 📄 logout.php             # Đăng xuất  
├── 📄 timetable.php          # Thời khóa biểu
├── 📄 documents.php          # Quản lý tài liệu
├── 📄 expenses.php           # Quản lý chi tiêu
├── 📄 notes.php              # Ghi chú cá nhân
├── 📄 profile.php            # Thông tin cá nhân
├── 📁 views/                 # Template views
│   ├── timetable-view.php    # Giao diện xem TKB
│   ├── timetable-import.php  # Import TKB từ file
│   ├── documents-view.php    # Giao diện quản lý tài liệu
│   ├── expenses-view.php     # Giao diện quản lý chi tiêu
│   └── notes-view.php        # Giao diện quản lý ghi chú
├── 📁 includes/              # Files include chung
│   ├── header.php            # Header với navigation
│   └── footer.php            # Footer với scripts
├── 📁 config/                # Cấu hình
│   └── db.php                # Kết nối database + helper functions
├── 📁 database/              # Database
│   └── student_manager.sql   # Schema + data mẫu
├── 📁 assets/                # Static files
│   ├── css/style.css         # Custom CSS (deprecated)
│   └── js/script.js          # Custom JavaScript
├── 📁 uploads/               # File uploads
│   └── [user_id]/            # Thư mục riêng cho từng user
└── 📄 README.md              # File này
```

## 🚀 Hướng dẫn cài đặt

### 1. Yêu cầu hệ thống
- **XAMPP** (Apache + MySQL + PHP 8.0+)
- **Web browser** hiện đại (Chrome, Firefox, Safari, Edge)

### 2. Cài đặt bước 1: Database
```sql
-- Tạo database mới
CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema từ file
mysql -u root -p student_manager < database/student_manager.sql
```

### 3. Cài đặt bước 2: Cấu hình
```php
// Cập nhật config/db.php với thông tin database của bạn
$host = 'localhost';
$dbname = 'student_manager';  
$username = 'root';
$password = '';
```

### 4. Cài đặt bước 3: Chạy ứng dụng
- Đặt project vào thư mục `htdocs` của XAMPP
- Khởi động Apache và MySQL trong XAMPP Control Panel
- Truy cập: `http://localhost/StudentManager`

### 5. Tài khoản demo
```
👨‍💼 Admin: admin / admin123
👨‍🎓 Sinh viên: student1 / password  
👩‍🎓 Sinh viên: student2 / password123
```

## 🎯 Mục tiêu học tập

### Kỹ năng Backend
- [x] PHP OOP và Procedural programming
- [x] MySQL query và database design
- [x] Session management và authentication
- [x] File upload và validation
- [x] Error handling và logging

### Kỹ năng Frontend
- [x] Responsive design với Tailwind CSS
- [x] JavaScript DOM manipulation
- [x] Form validation client-side
- [x] AJAX requests (sẽ có trong phiên bản sau)
- [x] User experience (UX) design

### Best Practices
- [x] Code organization và structure
- [x] Security practices (SQL injection, XSS prevention)
- [x] Comment và documentation
- [x] Version control với Git
- [x] Testing và debugging

## 📚 Kiến thức học được

### 1. PHP Development
- **MVC Pattern**: Tách logic, view và controller
- **Database Integration**: PDO với prepared statements
- **Session Management**: Bảo mật và quản lý phiên làm việc
- **File Handling**: Upload, validation và storage

### 2. Frontend Development  
- **Responsive Design**: Mobile-first approach
- **CSS Framework**: Tailwind CSS utilities
- **JavaScript**: DOM manipulation và event handling
- **User Interface**: Design thinking và accessibility

### 3. Database Design
- **Relational Design**: Primary/Foreign keys, indexes
- **Data Integrity**: Constraints và validation
- **Query Optimization**: Efficient queries
- **Backup & Recovery**: Data management

## 🔧 Tính năng nâng cao (Roadmap)

### 🚧 Đang phát triển
- [ ] **API REST** cho mobile app
- [ ] **Real-time notifications** với WebSocket  
- [ ] **Dark mode** theme switcher
- [ ] **Export data** PDF/Excel reports
- [ ] **Email notifications** cho deadline

### 🎯 Kế hoạch tương lai
- [ ] **Progressive Web App** (PWA) support
- [ ] **Multi-language** interface (EN/VI)
- [ ] **Social features** chia sẻ ghi chú
- [ ] **Calendar integration** Google Calendar
- [ ] **AI suggestions** cho lịch học

## 🐛 Bug Reports & Feature Requests

Nếu bạn phát hiện lỗi hoặc có ý tưởng tính năng mới:
1. Tạo issue trong GitHub repository
2. Mô tả chi tiết vấn đề/yêu cầu
3. Attach screenshots nếu có thể

## 👥 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp:
1. **Fork** repository
2. **Create** feature branch
3. **Commit** changes với message rõ ràng
4. **Push** và tạo Pull Request

## 📄 License

Dự án này được phát triển cho mục đích học tập và nghiên cứu. Sử dụng tự do với credit đến tác giả.

## 📞 Liên hệ

- **Email**: support@studentmanager.edu.vn
- **GitHub**: [Link repository]
- **Documentation**: [Link wiki]

---

### 🎉 Cập nhật gần đây

**v1.1.0** (Hôm nay)
- ✨ Hiển thị tên người dùng thay vì email/username để thân thiện hơn
- 📱 Cải thiện responsive design cho header và navigation
- 🔧 Mobile menu collapsible với animation mượt mà
- 💅 User greeting adaptive theo kích thước màn hình
- 🎨 Header logo và text responsive hoàn hảo
- 🧹 Loại bỏ page header riêng biệt ở các trang để giao diện gọn gàng

**Trước đó**
- ✅ Hoàn thành chuyển đổi sang Tailwind CSS
- ✅ Tối ưu timetable view với interactive features
- ✅ Database schema hoàn chỉnh với sample data
- ✅ Authentication system bảo mật

> 💡 **Tip**: Project này phù hợp cho sinh viên năm 2-3 đang học lập trình web cơ bản. Code được comment chi tiết bằng tiếng Việt để dễ hiểu và học hỏi! 