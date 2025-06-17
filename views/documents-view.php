<div class="documents-section">
    <div class="documents-header">
        <h3>Tài liệu của tôi</h3>
        <button class="btn btn-primary" onclick="showUploadForm()">Upload tài liệu</button>
    </div>

    <div id="upload-form" class="upload-section" style="display: none;">
        <h4>Upload tài liệu mới</h4>
        <form id="document-upload-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="document-title">Tiêu đề:</label>
                <input type="text" id="document-title" name="title" required>
            </div>
            <div class="form-group">
                <label for="document-category">Danh mục:</label>
                <select id="document-category" name="category" required>
                    <option value="">Chọn danh mục</option>
                    <option value="lecture">Bài giảng</option>
                    <option value="assignment">Bài tập</option>
                    <option value="exam">Đề thi</option>
                    <option value="reference">Tài liệu tham khảo</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div class="form-group">
                <label for="document-subject">Môn học:</label>
                <input type="text" id="document-subject" name="subject">
            </div>
            <div class="form-group">
                <label for="document-file">Chọn file:</label>
                <input type="file" id="document-file" name="document_file" required>
            </div>
            <div class="form-group">
                <label for="document-description">Mô tả:</label>
                <textarea id="document-description" name="description" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary" onclick="hideUploadForm()">Hủy</button>
            </div>
        </form>
    </div>

    <div class="documents-filter">
        <div class="filter-group">
            <label for="filter-category">Lọc theo danh mục:</label>
            <select id="filter-category" onchange="filterDocuments()">
                <option value="">Tất cả</option>
                <option value="lecture">Bài giảng</option>
                <option value="assignment">Bài tập</option>
                <option value="exam">Đề thi</option>
                <option value="reference">Tài liệu tham khảo</option>
                <option value="other">Khác</option>
            </select>
        </div>
        <div class="search-group">
            <input type="text" id="search-documents" placeholder="Tìm kiếm tài liệu..." onkeyup="searchDocuments()">
        </div>
    </div>

    <div class="documents-grid">
        <div class="document-item" data-category="lecture">
            <div class="document-icon">📄</div>
            <div class="document-info">
                <h4>Bài giảng Toán học - Chương 1</h4>
                <p class="document-meta">Danh mục: Bài giảng | Môn: Toán học</p>
                <p class="document-description">Giới thiệu về đại số tuyến tính</p>
                <div class="document-actions">
                    <button class="btn btn-sm" onclick="viewDocument(1)">Xem</button>
                    <button class="btn btn-sm" onclick="downloadDocument(1)">Tải về</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteDocument(1)">Xóa</button>
                </div>
            </div>
        </div>

        <div class="document-item" data-category="assignment">
            <div class="document-icon">📝</div>
            <div class="document-info">
                <h4>Bài tập Vật lý - Tuần 3</h4>
                <p class="document-meta">Danh mục: Bài tập | Môn: Vật lý</p>
                <p class="document-description">Bài tập về chuyển động thẳng đều</p>
                <div class="document-actions">
                    <button class="btn btn-sm" onclick="viewDocument(2)">Xem</button>
                    <button class="btn btn-sm" onclick="downloadDocument(2)">Tải về</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteDocument(2)">Xóa</button>
                </div>
            </div>
        </div>

        <div class="document-item" data-category="exam">
            <div class="document-icon">📋</div>
            <div class="document-info">
                <h4>Đề thi giữa kỳ Hóa học</h4>
                <p class="document-meta">Danh mục: Đề thi | Môn: Hóa học</p>
                <p class="document-description">Đề thi giữa kỳ năm học 2024-2025</p>
                <div class="document-actions">
                    <button class="btn btn-sm" onclick="viewDocument(3)">Xem</button>
                    <button class="btn btn-sm" onclick="downloadDocument(3)">Tải về</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteDocument(3)">Xóa</button>
                </div>
            </div>
        </div>
    </div>
</div>