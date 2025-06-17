<?php

/**
 * File: documents.php
 * Mục đích: Quản lý tài liệu học tập của sinh viên
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Upload, xem, tải về, xóa tài liệu. Hỗ trợ nhiều định dạng file
 */

// Thiết lập biến cho header
$page_title = 'Tài liệu';
$current_page = 'documents.php';

// Bắt đầu session và kiểm tra đăng nhập
session_start();
require_once 'config/db.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin user hiện tại
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? $_SESSION['username'];

// Lấy danh sách tài liệu của user
try {
    $documents_query = "SELECT * FROM documents WHERE user_id = ? ORDER BY upload_date DESC";
    $documents = fetchAll($documents_query, [$user_id]);
} catch (Exception $e) {
    error_log("Documents error: " . $e->getMessage());
    $documents = [];
}

// Định nghĩa các loại file được hỗ trợ
$allowed_types = [
    'pdf' => ['icon' => 'fas fa-file-pdf', 'color' => 'text-red-500', 'name' => 'PDF'],
    'doc' => ['icon' => 'fas fa-file-word', 'color' => 'text-blue-500', 'name' => 'Word'],
    'docx' => ['icon' => 'fas fa-file-word', 'color' => 'text-blue-500', 'name' => 'Word'],
    'xls' => ['icon' => 'fas fa-file-excel', 'color' => 'text-green-500', 'name' => 'Excel'],
    'xlsx' => ['icon' => 'fas fa-file-excel', 'color' => 'text-green-500', 'name' => 'Excel'],
    'ppt' => ['icon' => 'fas fa-file-powerpoint', 'color' => 'text-orange-500', 'name' => 'PowerPoint'],
    'pptx' => ['icon' => 'fas fa-file-powerpoint', 'color' => 'text-orange-500', 'name' => 'PowerPoint'],
    'txt' => ['icon' => 'fas fa-file-alt', 'color' => 'text-gray-500', 'name' => 'Text'],
    'jpg' => ['icon' => 'fas fa-file-image', 'color' => 'text-purple-500', 'name' => 'Image'],
    'jpeg' => ['icon' => 'fas fa-file-image', 'color' => 'text-purple-500', 'name' => 'Image'],
    'png' => ['icon' => 'fas fa-file-image', 'color' => 'text-purple-500', 'name' => 'Image'],
    'zip' => ['icon' => 'fas fa-file-archive', 'color' => 'text-yellow-500', 'name' => 'Archive'],
    'rar' => ['icon' => 'fas fa-file-archive', 'color' => 'text-yellow-500', 'name' => 'Archive']
];

// Hàm để lấy thông tin file type
function getFileTypeInfo($filename, $allowed_types)
{
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return $allowed_types[$extension] ?? ['icon' => 'fas fa-file', 'color' => 'text-gray-500', 'name' => 'File'];
}

// Function formatFileSize() đã được khai báo trong config/db.php

// Include header
include 'includes/header.php';
?>

<!-- Main content -->
<div class="bg-white rounded-2xl shadow-lg p-8">
    <!-- Các nút chức năng -->
    <div class="flex flex-wrap gap-4 mb-8 justify-center">
        <button onclick="showView('list')"
            id="btn-list"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md">
            <i class="fas fa-list"></i>
            <span>Danh sách tài liệu</span>
        </button>

        <button onclick="showView('upload')"
            id="btn-upload"
            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md">
            <i class="fas fa-upload"></i>
            <span>Upload tài liệu</span>
        </button>
    </div>

    <!-- View danh sách tài liệu -->
    <div id="documents-list" class="view-container">
        <?php include 'views/documents-view.php'; ?>
    </div>

    <!-- View upload tài liệu -->
    <div id="documents-upload" class="view-container hidden">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-8 border border-green-200">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center mb-6">
                <i class="fas fa-cloud-upload-alt mr-3 text-green-500"></i>
                Upload tài liệu mới
            </h3>

            <!-- Form upload -->
            <form id="upload-form" enctype="multipart/form-data" class="space-y-6">
                <!-- Drag & Drop area -->
                <div class="border-2 border-dashed border-green-300 rounded-lg p-8 text-center hover:border-green-400 hover:bg-green-50/50 transition-all duration-200"
                    id="drop-zone">
                    <i class="fas fa-cloud-upload-alt text-6xl text-green-400 mb-4"></i>
                    <p class="text-xl font-semibold text-gray-700 mb-2">Kéo thả file vào đây</p>
                    <p class="text-gray-500 mb-4">hoặc</p>

                    <input type="file"
                        id="document-file"
                        name="document_file"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar"
                        class="hidden"
                        onchange="handleFileSelection(this)">

                    <button type="button"
                        onclick="document.getElementById('document-file').click()"
                        class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg transition-all duration-200 flex items-center space-x-2 mx-auto">
                        <i class="fas fa-folder-open"></i>
                        <span>Chọn file</span>
                    </button>
                </div>

                <!-- Hiển thị files đã chọn -->
                <div id="selected-files" class="hidden space-y-4">
                    <h4 class="font-semibold text-gray-800">Files đã chọn:</h4>
                    <div id="file-list" class="space-y-2"></div>
                </div>

                <!-- Thông tin bổ sung -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-folder mr-2"></i>Danh mục
                        </label>
                        <select id="category" name="category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="general">Chung</option>
                            <option value="lecture">Bài giảng</option>
                            <option value="assignment">Bài tập</option>
                            <option value="exam">Thi cử</option>
                            <option value="reference">Tài liệu tham khảo</option>
                        </select>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-book mr-2"></i>Môn học
                        </label>
                        <input type="text" id="subject" name="subject"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Tên môn học">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-2"></i>Mô tả
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                        placeholder="Mô tả về tài liệu"></textarea>
                </div>

                <!-- Nút upload -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="resetUploadForm()"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                        Đặt lại
                    </button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center space-x-2 disabled:opacity-50"
                        id="upload-btn">
                        <i class="fas fa-upload"></i>
                        <span>Upload tài liệu</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript cho Documents -->
<script>
    /**
     * Chuyển đổi giữa các view
     */
    function showView(viewType) {
        const listContainer = document.getElementById('documents-list');
        const uploadContainer = document.getElementById('documents-upload');
        const btnList = document.getElementById('btn-list');
        const btnUpload = document.getElementById('btn-upload');

        if (viewType === 'list') {
            listContainer.classList.remove('hidden');
            uploadContainer.classList.add('hidden');

            btnList.classList.add('bg-blue-500');
            btnList.classList.remove('bg-gray-300');
            btnUpload.classList.add('bg-gray-300');
            btnUpload.classList.remove('bg-green-500');
        } else if (viewType === 'upload') {
            listContainer.classList.add('hidden');
            uploadContainer.classList.remove('hidden');

            btnUpload.classList.add('bg-green-500');
            btnUpload.classList.remove('bg-gray-300');
            btnList.classList.add('bg-gray-300');
            btnList.classList.remove('bg-blue-500');
        }
    }

    /**
     * Xử lý chọn file
     */
    function handleFileSelection(input) {
        const files = Array.from(input.files);
        const fileList = document.getElementById('file-list');
        const selectedFiles = document.getElementById('selected-files');

        if (files.length > 0) {
            selectedFiles.classList.remove('hidden');
            fileList.innerHTML = '';

            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between bg-white border border-gray-200 rounded-lg p-3';
                fileItem.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-file text-gray-400"></i>
                    <div>
                        <p class="font-medium text-gray-800">${file.name}</p>
                        <p class="text-sm text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                    </div>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
                fileList.appendChild(fileItem);
            });
        } else {
            selectedFiles.classList.add('hidden');
        }
    }

    /**
     * Xóa file khỏi danh sách
     */
    function removeFile(index) {
        const input = document.getElementById('document-file');
        const files = Array.from(input.files);
        files.splice(index, 1);

        // Tạo FileList mới (workaround vì FileList readonly)
        const dt = new DataTransfer();
        files.forEach(file => dt.items.add(file));
        input.files = dt.files;

        handleFileSelection(input);
    }

    /**
     * Reset form upload
     */
    function resetUploadForm() {
        document.getElementById('upload-form').reset();
        document.getElementById('selected-files').classList.add('hidden');
    }

    /**
     * Xử lý drag & drop
     */
    const dropZone = document.getElementById('drop-zone');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-green-500', 'bg-green-50');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-green-500', 'bg-green-50');

        const files = e.dataTransfer.files;
        const input = document.getElementById('document-file');
        input.files = files;
        handleFileSelection(input);
    });

    /**
     * Xử lý submit form upload
     */
    document.getElementById('upload-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const uploadBtn = document.getElementById('upload-btn');

        // Disable button và hiển thị loading
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang upload...';

        // TODO: Implement file upload to server
        setTimeout(() => {
            alert('Upload thành công! (Demo)');
            resetUploadForm();
            showView('list');

            // Reset button
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i><span>Upload tài liệu</span>';
        }, 2000);
    });

    // Set default view
    showView('list');
</script>

<?php
// Include footer
include 'includes/footer.php';
?>