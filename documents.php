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

// Dữ liệu và file types sẽ được xử lý bởi JavaScript APIs

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
        <!-- Search & Filter Controls -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <!-- Search Box -->
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                        id="search-input"
                        placeholder="Tìm kiếm tài liệu theo tên hoặc mô tả..."
                        class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button"
                            id="clear-search"
                            class="text-gray-400 hover:text-gray-600 hidden"
                            onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="w-full lg:w-48">
                    <select id="category-filter"
                        class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả danh mục</option>
                        <option value="lecture">Bài giảng</option>
                        <option value="assignment">Bài tập</option>
                        <option value="exam">Thi cử</option>
                        <option value="reference">Tài liệu tham khảo</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <!-- Subject Filter -->
                <div class="w-full lg:w-48">
                    <select id="subject-filter"
                        class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả môn học</option>
                        <!-- Options sẽ được load động -->
                    </select>
                </div>

                <!-- Sort Options -->
                <div class="w-full lg:w-48">
                    <select id="sort-filter"
                        class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="upload_date_desc">Mới nhất</option>
                        <option value="upload_date_asc">Cũ nhất</option>
                        <option value="title_asc">Tên A→Z</option>
                        <option value="title_desc">Tên Z→A</option>
                        <option value="size_desc">File lớn nhất</option>
                        <option value="size_asc">File nhỏ nhất</option>
                    </select>
                </div>

                <!-- Clear All Filters -->
                <button type="button"
                    id="clear-filters"
                    onclick="clearAllFilters()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg whitespace-nowrap">
                    <i class="fas fa-eraser mr-2"></i>Xóa bộ lọc
                </button>
            </div>

            <!-- Filter Summary -->
            <div id="filter-summary" class="mt-4 text-sm text-gray-600 hidden">
                <span>Hiển thị <strong id="results-count">0</strong> kết quả</span>
                <span id="active-filters" class="ml-4"></span>
            </div>
        </div>

        <!-- Documents Container -->
        <div id="documents-container">
            <!-- Danh sách sẽ được load động bằng JavaScript -->
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Đang tải danh sách tài liệu...</p>
            </div>
        </div>
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
                        name="document_file[]"
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
                <div class="space-y-6">
                    <!-- Tiêu đề tài liệu (tự động từ tên file) -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heading mr-2"></i>Tiêu đề tài liệu <span class="text-gray-400">(tự động từ tên file)</span>
                        </label>
                        <input type="text" id="title" name="title"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Sẽ tự động điền khi chọn file">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2"></i>Danh mục
                            </label>
                            <select id="category" name="category"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="other">Khác</option>
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

<!-- CSS bổ sung cho Documents -->
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .view-container {
        min-height: 400px;
    }

    /* Animation cho notification */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .fixed.top-4.right-4 {
        animation: slideInRight 0.3s ease-out;
    }

    /* Search & Filter Styles */
    #search-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    mark {
        background-color: rgb(254, 240, 138) !important;
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 500;
    }

    /* Responsive cho filter controls */
    @media (max-width: 1024px) {
        .flex.flex-col.lg\\:flex-row {
            flex-direction: column;
        }

        .w-full.lg\\:w-48 {
            width: 100%;
        }
    }

    /* Animation cho filter results */
    .documents-container {
        transition: opacity 0.2s ease-in-out;
    }

    .documents-container.loading {
        opacity: 0.6;
    }

    /* Hover effects cho document cards */
    .document-card {
        transition: all 0.2s ease-in-out;
    }

    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Custom scrollbar cho mobile */
    @media (max-width: 768px) {
        .overflow-x-auto::-webkit-scrollbar {
            height: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
    }
</style>

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
        const titleInput = document.getElementById('title');

        if (files.length > 0) {
            selectedFiles.classList.remove('hidden');
            fileList.innerHTML = '';

            // Tự động điền tiêu đề từ file đầu tiên
            if (files.length === 1) {
                // Nếu chỉ có 1 file, dùng tên file đó (loại bỏ phần mở rộng)
                const fileName = files[0].name;
                const nameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                titleInput.value = nameWithoutExt;
            } else {
                // Nếu có nhiều file, dùng tên chung + số lượng
                const firstFileName = files[0].name;
                const nameWithoutExt = firstFileName.substring(0, firstFileName.lastIndexOf('.')) || firstFileName;
                titleInput.value = `${nameWithoutExt} và ${files.length - 1} file khác`;
            }

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
            // Xóa tiêu đề khi không có file
            titleInput.value = '';
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

        // Cập nhật lại hiển thị và tiêu đề
        handleFileSelection(input);
    }

    /**
     * Reset form upload
     */
    function resetUploadForm() {
        document.getElementById('upload-form').reset();
        document.getElementById('selected-files').classList.add('hidden');
        document.getElementById('title').value = ''; // Reset title
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
     * Hiển thị thông báo
     */
    function showMessage(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        alertDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Biến global lưu trữ tất cả documents và subjects
    let allDocuments = [];
    let allSubjects = [];
    let searchTimeout = null;

    /**
     * Load danh sách tài liệu từ API
     */
    function loadDocumentList() {
        fetch('api/documents-list.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allDocuments = data.data.documents;
                    extractSubjects(allDocuments);
                    applyFilters(); // Áp dụng filters hiện tại
                } else {
                    showMessage('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Lỗi kết nối API!', 'error');
            });
    }

    /**
     * Trích xuất danh sách môn học từ documents
     */
    function extractSubjects(documents) {
        const subjects = [...new Set(documents
            .map(doc => doc.subject)
            .filter(subject => subject && subject.trim() !== '')
        )].sort();

        const subjectFilter = document.getElementById('subject-filter');
        const currentValue = subjectFilter.value;

        // Clear và rebuild options
        subjectFilter.innerHTML = '<option value="">Tất cả môn học</option>';
        subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            subjectFilter.appendChild(option);
        });

        // Restore previous selection
        if (currentValue && subjects.includes(currentValue)) {
            subjectFilter.value = currentValue;
        }

        allSubjects = subjects;
    }

    /**
     * Áp dụng tất cả filters và search
     */
    function applyFilters() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
        const categoryFilter = document.getElementById('category-filter').value;
        const subjectFilter = document.getElementById('subject-filter').value;
        const sortFilter = document.getElementById('sort-filter').value;

        let filteredDocuments = [...allDocuments];

        // Apply search filter
        if (searchTerm) {
            filteredDocuments = filteredDocuments.filter(doc =>
                doc.title.toLowerCase().includes(searchTerm) ||
                (doc.description && doc.description.toLowerCase().includes(searchTerm)) ||
                (doc.subject && doc.subject.toLowerCase().includes(searchTerm))
            );
        }

        // Apply category filter
        if (categoryFilter) {
            filteredDocuments = filteredDocuments.filter(doc => doc.category === categoryFilter);
        }

        // Apply subject filter
        if (subjectFilter) {
            filteredDocuments = filteredDocuments.filter(doc => doc.subject === subjectFilter);
        }

        // Apply sorting
        filteredDocuments = sortDocuments(filteredDocuments, sortFilter);

        // Display results
        displayDocuments(filteredDocuments);
        updateFilterSummary(searchTerm, categoryFilter, subjectFilter, filteredDocuments.length);
        updateClearSearchButton(searchTerm);
    }

    /**
     * Hiển thị danh sách tài liệu
     */
    function displayDocuments(documents) {
        const container = document.getElementById('documents-container');

        // Cập nhật số lượng kết quả
        updateResultsCount(documents.length);

        if (documents.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl text-gray-500 mb-2">Không tìm thấy tài liệu nào</h3>
                    <p class="text-gray-400">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                    <button onclick="clearAllFilters()" class="mt-4 bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-eraser mr-2"></i>Xóa bộ lọc
                    </button>
                </div>
            `;
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
        documents.forEach(doc => {
            const fileIcon = getFileIcon(doc.file_extension);
            // Highlight search terms trong title
            const highlightedTitle = highlightSearchTerms(doc.title);
            const highlightedDescription = doc.description ? highlightSearchTerms(doc.description) : '';

            html += `
                <div class="document-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-3">
                        <div class="text-3xl ${fileIcon.color}">
                            <i class="${fileIcon.icon}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 truncate">${highlightedTitle}</h4>
                            <p class="text-sm text-gray-500">${getCategoryDisplay(doc.category)} • ${doc.subject || 'Không có môn học'}</p>
                            <p class="text-xs text-gray-400 mt-1">${doc.file_size_formatted} • ${doc.upload_date_formatted}</p>
                            ${highlightedDescription ? `<p class="text-sm text-gray-600 mt-2 line-clamp-2">${highlightedDescription}</p>` : ''}
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-4">
                        <a href="${doc.download_url}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-download mr-1"></i>Tải về
                        </a>
                        <button onclick="deleteDocument(${doc.id})" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                            <i class="fas fa-trash mr-1"></i>Xóa
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    /**
     * Lấy icon cho file type
     */
    function getFileIcon(extension) {
        const icons = {
            'pdf': {
                icon: 'fas fa-file-pdf',
                color: 'text-red-500'
            },
            'doc': {
                icon: 'fas fa-file-word',
                color: 'text-blue-500'
            },
            'docx': {
                icon: 'fas fa-file-word',
                color: 'text-blue-500'
            },
            'xls': {
                icon: 'fas fa-file-excel',
                color: 'text-green-500'
            },
            'xlsx': {
                icon: 'fas fa-file-excel',
                color: 'text-green-500'
            },
            'ppt': {
                icon: 'fas fa-file-powerpoint',
                color: 'text-orange-500'
            },
            'pptx': {
                icon: 'fas fa-file-powerpoint',
                color: 'text-orange-500'
            },
            'txt': {
                icon: 'fas fa-file-alt',
                color: 'text-gray-500'
            },
            'jpg': {
                icon: 'fas fa-file-image',
                color: 'text-purple-500'
            },
            'jpeg': {
                icon: 'fas fa-file-image',
                color: 'text-purple-500'
            },
            'png': {
                icon: 'fas fa-file-image',
                color: 'text-purple-500'
            },
            'zip': {
                icon: 'fas fa-file-archive',
                color: 'text-yellow-500'
            },
            'rar': {
                icon: 'fas fa-file-archive',
                color: 'text-yellow-500'
            }
        };
        return icons[extension] || {
            icon: 'fas fa-file',
            color: 'text-gray-500'
        };
    }

    /**
     * Sắp xếp documents theo option được chọn
     */
    function sortDocuments(documents, sortOption) {
        const sortParts = sortOption.split('_');
        const field = sortParts[0];
        const direction = sortParts[1]; // 'asc' hoặc 'desc'

        return documents.sort((a, b) => {
            let compareValue = 0;

            switch (field) {
                case 'upload':
                    compareValue = new Date(a.upload_date) - new Date(b.upload_date);
                    break;
                case 'title':
                    compareValue = a.title.localeCompare(b.title, 'vi');
                    break;
                case 'size':
                    compareValue = (parseInt(a.file_size) || 0) - (parseInt(b.file_size) || 0);
                    break;
                default:
                    compareValue = new Date(a.upload_date) - new Date(b.upload_date);
            }

            return direction === 'desc' ? -compareValue : compareValue;
        });
    }

    /**
     * Highlight search terms trong text
     */
    function highlightSearchTerms(text) {
        const searchTerm = document.getElementById('search-input').value.trim();
        if (!searchTerm || !text) return text;

        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
    }

    /**
     * Lấy tên hiển thị cho category
     */
    function getCategoryDisplay(category) {
        const categoryMap = {
            'lecture': 'Bài giảng',
            'assignment': 'Bài tập',
            'exam': 'Thi cử',
            'reference': 'Tài liệu tham khảo',
            'other': 'Khác'
        };
        return categoryMap[category] || category;
    }

    /**
     * Cập nhật số lượng kết quả
     */
    function updateResultsCount(count) {
        document.getElementById('results-count').textContent = count;
    }

    /**
     * Cập nhật filter summary
     */
    function updateFilterSummary(searchTerm, categoryFilter, subjectFilter, resultCount) {
        const summaryDiv = document.getElementById('filter-summary');
        const activeFiltersSpan = document.getElementById('active-filters');

        let activeFilters = [];
        if (searchTerm) activeFilters.push(`Tìm kiếm: "${searchTerm}"`);
        if (categoryFilter) activeFilters.push(`Danh mục: ${getCategoryDisplay(categoryFilter)}`);
        if (subjectFilter) activeFilters.push(`Môn học: ${subjectFilter}`);

        if (activeFilters.length > 0 || resultCount !== allDocuments.length) {
            summaryDiv.classList.remove('hidden');
            activeFiltersSpan.textContent = activeFilters.length > 0 ? '• ' + activeFilters.join(' • ') : '';
        } else {
            summaryDiv.classList.add('hidden');
        }
    }

    /**
     * Cập nhật button clear search
     */
    function updateClearSearchButton(searchTerm) {
        const clearBtn = document.getElementById('clear-search');
        if (searchTerm) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    /**
     * Xóa search term
     */
    function clearSearch() {
        document.getElementById('search-input').value = '';
        applyFilters();
    }

    /**
     * Xóa tất cả filters
     */
    function clearAllFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('category-filter').value = '';
        document.getElementById('subject-filter').value = '';
        document.getElementById('sort-filter').value = 'upload_date_desc';
        applyFilters();
    }



    /**
     * Xóa tài liệu
     */
    function deleteDocument(id) {
        if (!confirm('Bạn có chắc muốn xóa tài liệu này?')) return;

        const formData = new FormData();
        formData.append('id', id);

        fetch('api/documents-delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Xóa tài liệu thành công!');
                    loadDocumentList(); // Reload danh sách
                } else {
                    showMessage('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Lỗi kết nối API!', 'error');
            });
    }

    /**
     * Xử lý submit form upload
     */
    document.getElementById('upload-form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Kiểm tra đã chọn file chưa
        const fileInput = document.getElementById('document-file');
        if (fileInput.files.length === 0) {
            showMessage('Vui lòng chọn file để upload!', 'error');
            return;
        }

        // Title sẽ được tự động tạo từ tên file nếu trống, không cần validate

        const formData = new FormData(this);
        const uploadBtn = document.getElementById('upload-btn');

        // Disable button và hiển thị loading
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang upload...';

        // Gọi API upload
        fetch('api/documents-upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message);
                    resetUploadForm();
                    showView('list');
                    loadDocumentList(); // Reload danh sách
                } else {
                    showMessage('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Lỗi kết nối API!', 'error');
            })
            .finally(() => {
                // Reset button
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload tài liệu';
            });
    });

    // Khởi tạo khi page load
    document.addEventListener('DOMContentLoaded', function() {
        loadDocumentList(); // Load danh sách ban đầu
        showView('list'); // Hiển thị view list
        initializeEventListeners(); // Khởi tạo event listeners
    });

    /**
     * Khởi tạo event listeners cho search và filter
     */
    function initializeEventListeners() {
        // Real-time search với debounce
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 300); // Delay 300ms để tránh quá nhiều requests
        });

        // Filter change events
        document.getElementById('category-filter').addEventListener('change', applyFilters);
        document.getElementById('subject-filter').addEventListener('change', applyFilters);
        document.getElementById('sort-filter').addEventListener('change', applyFilters);

        // Enter key trong search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                applyFilters();
            }
        });
    }
</script>

<?php
// Include footer
include 'includes/footer.php';
?>