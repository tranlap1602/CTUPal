# 📚 HƯỚNG DẪN SỬ DỤNG APIs DOCUMENTS

## 🎯 **TỔNG QUAN**
Đây là bộ APIs đơn giản để quản lý tài liệu học tập cho sinh viên. Gồm 4 chức năng chính: Upload, List, Delete, Download.

---

## 📤 **1. UPLOAD TÀI LIỆU**

**Endpoint:** `POST api/documents-upload.php`

**Form Data:**
```html
<form enctype="multipart/form-data">
    <input type="text" name="title" required>
    <textarea name="description"></textarea>
    <select name="category">
        <option value="lecture">Bài giảng</option>
        <option value="assignment">Bài tập</option>
        <option value="exam">Thi cử</option>
        <option value="reference">Tài liệu tham khảo</option>
        <option value="other">Khác</option>
    </select>
    <input type="text" name="subject">
    <input type="file" name="document_file[]" multiple>
</form>
```

**Response thành công:**
```json
{
    "success": true,
    "message": "2 file đã được upload thành công!",
    "data": {
        "uploaded_count": 2,
        "files": [
            {
                "id": 1,
                "original_name": "bai_giang.pdf",
                "saved_name": "bai_giang_20250129_143022_1234.pdf",
                "size": 2048576,
                "type": "pdf"
            }
        ]
    }
}
```

**Response lỗi:**
```json
{
    "success": false,
    "message": "File 'virus.exe' không được hỗ trợ!"
}
```

---

## 📋 **2. LẤY DANH SÁCH TÀI LIỆU**

**Endpoint:** `GET api/documents-list.php`

**Query Parameters (tùy chọn):**
- `category` - Filter theo danh mục
- `subject` - Filter theo môn học  
- `search` - Tìm kiếm theo title/description
- `sort` - Sắp xếp: upload_date, title, category, subject
- `order` - ASC hoặc DESC

**Ví dụ:**
```
GET api/documents-list.php?category=lecture&search=toán&sort=title&order=ASC
```

**Response:**
```json
{
    "success": true,
    "message": "Lấy danh sách tài liệu thành công!",
    "data": {
        "documents": [
            {
                "id": 1,
                "title": "Bài giảng Toán cao cấp",
                "description": "Chương 1: Đạo hàm",
                "category": "lecture",
                "subject": "Toán cao cấp",
                "file_name": "toan_c1.pdf",
                "file_size": 2048576,
                "file_size_formatted": "2.00 MB",
                "file_extension": "pdf",
                "upload_date": "2025-01-29 14:30:22",
                "upload_date_formatted": "29/01/2025 14:30",
                "download_url": "api/documents-download.php?id=1"
            }
        ],
        "total_count": 5,
        "filtered_count": 1,
        "category_stats": {
            "lecture": 3,
            "assignment": 2
        }
    }
}
```

---

## 🗑️ **3. XÓA TÀI LIỆU**

**Endpoint:** `POST api/documents-delete.php` hoặc `DELETE api/documents-delete.php`

**POST Body:**
```javascript
// Form data
const formData = new FormData();
formData.append('id', '1');

// Hoặc JSON cho DELETE
{
    "id": 1
}
```

**Response thành công:**
```json
{
    "success": true,
    "message": "Xóa tài liệu thành công!",
    "data": {
        "deleted_document": {
            "id": 1,
            "title": "Bài giảng Toán cao cấp",
            "file_name": "toan_c1.pdf"
        },
        "file_deleted": true
    }
}
```

---

## ⬇️ **4. TẢI VỀ TÀI LIỆU**

**Endpoint:** `GET api/documents-download.php?id={document_id}`

**Ví dụ:**
```html
<a href="api/documents-download.php?id=1" download>
    Tải về
</a>
```

**Response:** 
- Thành công: File download trực tiếp
- Lỗi: Trang HTML thông báo lỗi

---

## 🔒 **BẢO MẬT**

### **Kiểm tra quyền truy cập:**
- User chỉ có thể thao tác với tài liệu của chính mình
- Tất cả APIs đều kiểm tra `$_SESSION['user_id']`

### **File security:**
- Chỉ chấp nhận: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, jpg, jpeg, png, zip, rar
- Tối đa 10MB/file
- File được lưu với tên unique để tránh conflict

### **Folder structure:**
```
uploads/documents/
├── 1/          (user_id = 1)
│   ├── file1_20250129_143022_1234.pdf
│   └── file2_20250129_143023_5678.docx
├── 2/          (user_id = 2)
└── ...
```

---

## 💡 **CÁCH SỬ DỤNG VỚI JAVASCRIPT**

### **Upload file:**
```javascript
const formData = new FormData();
formData.append('title', 'Bài giảng mới');
formData.append('category', 'lecture');
formData.append('subject', 'Toán học');
formData.append('description', 'Mô tả');

// Multiple files
const files = document.getElementById('file-input').files;
for (let i = 0; i < files.length; i++) {
    formData.append('document_file[]', files[i]);
}

fetch('api/documents-upload.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Upload thành công!');
        loadDocumentList(); // Reload danh sách
    } else {
        alert('Lỗi: ' + data.message);
    }
});
```

### **Load danh sách:**
```javascript
function loadDocumentList() {
    fetch('api/documents-list.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayDocuments(data.data.documents);
        }
    });
}
```

### **Xóa tài liệu:**
```javascript
function deleteDocument(id) {
    if (confirm('Bạn có chắc muốn xóa?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/documents-delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Xóa thành công!');
                loadDocumentList();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }
}
```

---

## 🚀 **BƯỚC TIẾP THEO**

Phase 1 đã hoàn thành! Tiếp theo cần:

1. **Phase 2:** Kết nối frontend với các APIs này
2. **Phase 3:** Thêm search/filter nâng cao
3. **Phase 4:** File preview và mobile optimization

**Happy coding! 🎉** 