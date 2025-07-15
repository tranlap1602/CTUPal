/**
 * DOCUMENTS MANAGEMENT - PHIÊN BẢN TỐI GIẢN + FILTER
 * File: assets/js/documents.js
 * Mục đích: Quản lý tài liệu học tập với filter (Niên luận)
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: 2025-01-29
 */

// ==================== BIẾN TOÀN CỤC ====================
let allDocuments = [];
let currentFilters = {
    category: '',
    subject: ''
};

// ==================== CHUYỂN ĐỔI VIEW ====================
function showView(viewType) {
    const listView = document.getElementById('documents-list');
    const uploadView = document.getElementById('documents-upload');

    if (viewType === 'list') {
        listView.classList.remove('hidden');
        uploadView.classList.add('hidden');
    } else {
        listView.classList.add('hidden');
        uploadView.classList.remove('hidden');
    }
}

// ==================== XỬ LÝ UPLOAD FILE ====================
function handleFileSelection(input) {
    const files = Array.from(input.files);
    const fileList = document.getElementById('file-list');
    const selectedFiles = document.getElementById('selected-files');
    const titleInput = document.getElementById('title');

    if (files.length > 0) {
        selectedFiles.classList.remove('hidden');
        fileList.innerHTML = '';

        // Tự động điền tiêu đề từ file đầu tiên
        const fileName = files[0].name;
        const nameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
        titleInput.value = files.length === 1 ? nameWithoutExt : `${nameWithoutExt} và ${files.length - 1} file khác`;

        // Hiển thị danh sách file
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
        titleInput.value = '';
    }
}

function removeFile(index) {
    const input = document.getElementById('document-file');
    const files = Array.from(input.files);
    files.splice(index, 1);

    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    handleFileSelection(input);
}

function resetUploadForm() {
    document.getElementById('upload-form').reset();
    document.getElementById('selected-files').classList.add('hidden');
    document.getElementById('title').value = '';
}

// ==================== QUẢN LÝ DANH SÁCH VÀ FILTER ====================
function loadDocumentList() {
    const categoryFilter = document.getElementById('category-filter').value;
    const subjectFilter = document.getElementById('subject-filter').value;

    // Cập nhật current filters
    currentFilters.category = categoryFilter;
    currentFilters.subject = subjectFilter;

    // Tạo URL với parameters
    const params = new URLSearchParams();
    if (categoryFilter) params.append('category', categoryFilter);
    if (subjectFilter) params.append('subject', subjectFilter);

    fetch(`api/documents-list.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allDocuments = data.data.documents;
                displayDocuments(allDocuments);
                updateFilterOptions(data.data.filters);
                updateFilterSummary();
            } else {
                showMessage('Lỗi: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Lỗi kết nối API!', 'error');
        });
}

function updateFilterOptions(filters) {
    // Cập nhật subject filter options
    const subjectFilter = document.getElementById('subject-filter');
    const currentSubject = subjectFilter.value;

    // Giữ lại option "Tất cả môn học"
    subjectFilter.innerHTML = '<option value="">Tất cả môn học</option>';

    if (filters.subjects) {
        filters.subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            subjectFilter.appendChild(option);
        });
    }

    // Restore selection nếu vẫn tồn tại
    if (currentSubject && filters.subjects && filters.subjects.includes(currentSubject)) {
        subjectFilter.value = currentSubject;
    }
}

function displayDocuments(documents) {
    const container = document.getElementById('documents-container');

    if (documents.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl text-gray-500 mb-2">Không tìm thấy tài liệu nào</h3>
        `;
        return;
    }

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
    documents.forEach(doc => {
        const fileIcon = getFileIcon(doc.file_type);
        html += `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start space-x-3">
                    <div class="text-3xl ${fileIcon.color}">
                        <i class="${fileIcon.icon}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 truncate">${doc.title}</h4>
                        <p class="text-sm text-gray-500">${getCategoryDisplay(doc.category)} • ${doc.subject || 'Không có môn học'}</p>
                        <p class="text-xs text-gray-400 mt-1">${formatFileSize(doc.file_size)} • ${formatDate(doc.created_at)}</p>
                        ${doc.description ? `<p class="text-sm text-gray-600 mt-2 line-clamp-2">${doc.description}</p>` : ''}
                    </div>
                </div>
                <div class="flex space-x-2 mt-4">
                    <a href="api/documents-download.php?id=${doc.id}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
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

function updateFilterSummary() {
    const summaryDiv = document.getElementById('filter-summary');
    const resultsCount = document.getElementById('results-count');
    const activeFilters = document.getElementById('active-filters');

    resultsCount.textContent = allDocuments.length;

    let activeFilterTexts = [];
    if (currentFilters.category) {
        activeFilterTexts.push(`Danh mục: ${getCategoryDisplay(currentFilters.category)}`);
    }
    if (currentFilters.subject) {
        activeFilterTexts.push(`Môn học: ${currentFilters.subject}`);
    }

    if (activeFilterTexts.length > 0) {
        summaryDiv.classList.remove('hidden');
        activeFilters.textContent = '• ' + activeFilterTexts.join(' • ');
    } else {
        summaryDiv.classList.add('hidden');
    }
}

function clearFilters() {
    document.getElementById('category-filter').value = '';
    document.getElementById('subject-filter').value = '';
    loadDocumentList();
}

// ==================== HÀM TIỆN ÍCH ====================
function getFileIcon(extension) {
    const icons = {
        'pdf': { icon: 'fas fa-file-pdf', color: 'text-red-500' },
        'doc': { icon: 'fas fa-file-word', color: 'text-blue-500' },
        'docx': { icon: 'fas fa-file-word', color: 'text-blue-500' },
        'xls': { icon: 'fas fa-file-excel', color: 'text-green-500' },
        'xlsx': { icon: 'fas fa-file-excel', color: 'text-green-500' },
        'ppt': { icon: 'fas fa-file-powerpoint', color: 'text-orange-500' },
        'pptx': { icon: 'fas fa-file-powerpoint', color: 'text-orange-500' },
        'txt': { icon: 'fas fa-file-alt', color: 'text-gray-500' },
        'jpg': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'jpeg': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'png': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'zip': { icon: 'fas fa-file-archive', color: 'text-yellow-500' },
        'rar': { icon: 'fas fa-file-archive', color: 'text-yellow-500' }
    };
    return icons[extension] || { icon: 'fas fa-file', color: 'text-gray-500' };
}

function getCategoryDisplay(category) {
    const categories = {
        'lecture': 'Bài giảng',
        'assignment': 'Bài tập',
        'exam': 'Thi cử',
        'reference': 'Tài liệu tham khảo',
        'other': 'Khác'
    };
    return categories[category] || category;
}

function formatFileSize(bytes) {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;

    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }

    return `${size.toFixed(2)} ${units[unitIndex]}`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

function showMessage(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
    alertDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(alertDiv);

    setTimeout(() => alertDiv.remove(), 3000);
}

// ==================== API OPERATIONS ====================
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
                loadDocumentList();
            } else {
                showMessage('Lỗi: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Lỗi kết nối API!', 'error');
        });
}

// ==================== EVENT LISTENERS ====================
function initializeUploadForm() {
    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const fileInput = document.getElementById('document-file');
            if (fileInput.files.length === 0) {
                showMessage('Vui lòng chọn file để upload!', 'error');
                return;
            }

            const formData = new FormData(this);
            const uploadBtn = document.getElementById('upload-btn');

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang upload...';

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
                        loadDocumentList();
                    } else {
                        showMessage('Lỗi: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Lỗi kết nối API!', 'error');
                })
                .finally(() => {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload tài liệu';
                });
        });
    }
}

function initializeFilterListeners() {
    // Category filter change
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', loadDocumentList);
    }

    // Subject filter change
    const subjectFilter = document.getElementById('subject-filter');
    if (subjectFilter) {
        subjectFilter.addEventListener('change', loadDocumentList);
    }
}

// ==================== KHỞI TẠO ====================
document.addEventListener('DOMContentLoaded', function () {
    loadDocumentList();
    showView('list');
    initializeUploadForm();
    initializeFilterListeners();
}); 