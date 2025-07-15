<?php
/**
 * File: documents.php
 * Mục đích: Quản lý tài liệu học tập
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 */

$page_title = 'Tài liệu';
$current_page = 'documents.php';

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';
?>

<div class="bg-white rounded-2xl shadow-lg p-8">
    <!-- Nút chức năng -->
    <div class="flex gap-4 mb-8 justify-center">
        <button onclick="showView('list')" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-list mr-2"></i>Danh sách tài liệu
        </button>
        <button onclick="showView('upload')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-upload mr-2"></i>Upload tài liệu
        </button>
    </div>

    <!-- View danh sách -->
    <div id="documents-list" class="view-container">
        <!-- Filter Controls -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <!-- Category Filter -->
                <div class="w-full md:w-48">
                    <select id="category-filter" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả danh mục</option>
                        <option value="lecture">Bài giảng</option>
                        <option value="assignment">Bài tập</option>
                        <option value="exam">Thi cử</option>
                        <option value="reference">Tài liệu tham khảo</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <!-- Subject Filter -->
                <div class="w-full md:w-48">
                    <select id="subject-filter" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả môn học</option>
                        <!-- Options sẽ được load động -->
                    </select>
                </div>

                <!-- Clear Filters -->
                <button type="button" onclick="clearFilters()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg whitespace-nowrap">
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
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Đang tải danh sách tài liệu...</p>
            </div>
        </div>
    </div>

    <!-- View upload -->
    <div id="documents-upload" class="view-container hidden">
        <div class="bg-green-50 rounded-lg p-8 border border-green-200">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-cloud-upload-alt mr-3 text-green-500"></i>
                Upload tài liệu mới
            </h3>

            <form id="upload-form" enctype="multipart/form-data" class="space-y-6">
                <!-- Chọn file -->
                <div class="border-2 border-dashed border-green-300 rounded-lg p-8 text-center">
                    <i class="fas fa-cloud-upload-alt text-6xl text-green-400 mb-4"></i>
                    <p class="text-xl font-semibold text-gray-700 mb-2">Chọn file để upload</p>

                    <input type="file" id="document-file" name="document_file[]" multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar"
                        class="hidden" onchange="handleFileSelection(this)">

                    <button type="button" onclick="document.getElementById('document-file').click()"
                        class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg">
                        <i class="fas fa-folder-open mr-2"></i>Chọn file
                    </button>
                </div>

                <!-- Files đã chọn -->
                <div id="selected-files" class="hidden space-y-4">
                    <h4 class="font-semibold text-gray-800">Files đã chọn:</h4>
                    <div id="file-list" class="space-y-2"></div>
                </div>

                <!-- Thông tin tài liệu -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tiêu đề tài liệu
                        </label>
                        <input type="text" id="title" name="title"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                            placeholder="Tự động điền từ tên file">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Danh mục
                            </label>
                            <select id="category" name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="other">Khác</option>
                                <option value="lecture">Bài giảng</option>
                                <option value="assignment">Bài tập</option>
                                <option value="exam">Thi cử</option>
                                <option value="reference">Tài liệu tham khảo</option>
                            </select>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Môn học
                            </label>
                            <input type="text" id="subject" name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                                placeholder="Tên môn học">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                            placeholder="Mô tả về tài liệu"></textarea>
                    </div>
                </div>

                <!-- Nút upload -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="resetUploadForm()"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Đặt lại
                    </button>
                    <button type="submit" id="upload-btn"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg">
                        <i class="fas fa-upload mr-2"></i>Upload tài liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script src="assets/js/documents.js"></script>

<?php include 'includes/footer.php'; ?>