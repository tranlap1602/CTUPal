<!-- 
    File: views/timetable-import.php 
    Mục đích: Form import/thêm môn học vào thời khóa biểu
    Sử dụng: Include trong timetable.php
-->

<div class="import-section">
    <!-- Header của view -->
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center mb-4">
            <i class="fas fa-file-import mr-3 text-green-500"></i>
            Import & Thêm môn học
        </h3>
        <p class="text-gray-600">Bạn có thể import thời khóa biểu từ file Excel/CSV hoặc thêm từng môn học thủ công</p>
    </div>

    <!-- Tab navigation -->
    <div class="flex flex-wrap border-b border-gray-200 mb-8">
        <button onclick="showImportTab('file')"
            id="tab-file"
            class="px-6 py-3 font-medium text-blue-600 border-b-2 border-blue-600 transition-all duration-200">
            <i class="fas fa-file-upload mr-2"></i>Import từ file
        </button>
        <button onclick="showImportTab('manual')"
            id="tab-manual"
            class="px-6 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition-all duration-200">
            <i class="fas fa-plus-circle mr-2"></i>Thêm thủ công
        </button>
    </div>

    <!-- Tab content: Import từ file -->
    <div id="import-file" class="tab-content">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-8 border border-blue-200">
            <!-- Hướng dẫn import file -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Hướng dẫn import file
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Hỗ trợ file <strong>.xlsx, .csv</strong></span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Cột 1: Tên môn học</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Cột 2: Thứ (2-7)</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Cột 3: Giờ bắt đầu (HH:MM)</span>
                        </div>
                    </div>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Cột 4: Giờ kết thúc (HH:MM)</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Cột 5: Phòng học (tùy chọn)</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span>Cột 6: Giảng viên (tùy chọn)</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                            <span>Dòng đầu tiên là header</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form upload file -->
            <form enctype="multipart/form-data" class="space-y-6">
                <div class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-200">
                    <div class="mb-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-blue-400 mb-4"></i>
                        <p class="text-lg font-medium text-gray-700 mb-2">Chọn file thời khóa biểu</p>
                        <p class="text-sm text-gray-500">Kéo thả file vào đây hoặc click để chọn</p>
                    </div>

                    <input type="file"
                        id="timetable-file"
                        name="timetable_file"
                        accept=".xlsx,.xls,.csv"
                        class="hidden"
                        onchange="handleFileSelect(this)">

                    <button type="button"
                        onclick="document.getElementById('timetable-file').click()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center space-x-2 mx-auto">
                        <i class="fas fa-folder-open"></i>
                        <span>Chọn file</span>
                    </button>
                </div>

                <!-- Hiển thị file đã chọn -->
                <div id="file-info" class="hidden bg-white rounded-lg p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-excel text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p id="file-name" class="font-medium text-gray-800"></p>
                                <p id="file-size" class="text-sm text-gray-500"></p>
                            </div>
                        </div>
                        <button type="button"
                            onclick="removeFile()"
                            class="text-red-500 hover:text-red-700 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Nút upload -->
                <div class="flex justify-center">
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        <i class="fas fa-upload"></i>
                        <span>Upload và Import</span>
                    </button>
                </div>
            </form>

            <!-- Link download template -->
            <div class="mt-8 text-center">
                <a href="#"
                    onclick="downloadTemplate()"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-download"></i>
                    <span>Tải template mẫu</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tab content: Thêm thủ công -->
    <div id="import-manual" class="tab-content hidden">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-8 border border-green-200">
            <h4 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                Thêm môn học mới
            </h4>

            <!-- Form thêm môn học -->
            <form class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tên môn học -->
                    <div class="md:col-span-2">
                        <label for="subject-name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-book mr-2 text-blue-500"></i>Tên môn học *
                        </label>
                        <input type="text"
                            id="subject-name"
                            name="subject_name"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                            placeholder="Ví dụ: Toán học, Lập trình Web, ...">
                    </div>

                    <!-- Ngày trong tuần -->
                    <div>
                        <label for="day-of-week" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-2 text-blue-500"></i>Ngày trong tuần *
                        </label>
                        <select id="day-of-week"
                            name="day_of_week"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            <option value="">Chọn ngày</option>
                            <option value="2">Thứ 2</option>
                            <option value="3">Thứ 3</option>
                            <option value="4">Thứ 4</option>
                            <option value="5">Thứ 5</option>
                            <option value="6">Thứ 6</option>
                            <option value="7">Thứ 7</option>
                        </select>
                    </div>

                    <!-- Mã môn học -->
                    <div>
                        <label for="subject-code" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag mr-2 text-blue-500"></i>Mã môn học
                        </label>
                        <input type="text"
                            id="subject-code"
                            name="subject_code"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                            placeholder="Ví dụ: MATH101, CS201, ...">
                    </div>

                    <!-- Giờ bắt đầu -->
                    <div>
                        <label for="start-time" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>Giờ bắt đầu *
                        </label>
                        <input type="time"
                            id="start-time"
                            name="start_time"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Giờ kết thúc -->
                    <div>
                        <label for="end-time" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>Giờ kết thúc *
                        </label>
                        <input type="time"
                            id="end-time"
                            name="end_time"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Phòng học -->
                    <div>
                        <label for="classroom" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Phòng học
                        </label>
                        <input type="text"
                            id="classroom"
                            name="classroom"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                            placeholder="Ví dụ: A101, B203, Lab1, ...">
                    </div>

                    <!-- Giảng viên -->
                    <div>
                        <label for="teacher" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Giảng viên
                        </label>
                        <input type="text"
                            id="teacher"
                            name="teacher"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                            placeholder="Tên giảng viên">
                    </div>

                    <!-- Ghi chú -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-blue-500"></i>Ghi chú
                        </label>
                        <textarea id="notes"
                            name="notes"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 resize-none"
                            placeholder="Ghi chú thêm về môn học (tùy chọn)"></textarea>
                    </div>
                </div>

                <!-- Nút submit -->
                <div class="flex justify-center space-x-4 pt-6">
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-plus"></i>
                        <span>Thêm môn học</span>
                    </button>

                    <button type="button"
                        onclick="resetForm()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-redo"></i>
                        <span>Reset form</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview kết quả import (hiển thị sau khi upload file) -->
    <div id="import-preview" class="hidden mt-8">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-eye mr-2 text-purple-500"></i>
                Preview dữ liệu import
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Môn học</th>
                            <th class="px-4 py-2 text-left">Thứ</th>
                            <th class="px-4 py-2 text-left">Giờ bắt đầu</th>
                            <th class="px-4 py-2 text-left">Giờ kết thúc</th>
                            <th class="px-4 py-2 text-left">Phòng</th>
                            <th class="px-4 py-2 text-left">Giảng viên</th>
                        </tr>
                    </thead>
                    <tbody id="preview-data">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center space-x-4 mt-6">
                <button onclick="confirmImport()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-all duration-200">
                    <i class="fas fa-check mr-2"></i>Xác nhận Import
                </button>
                <button onclick="cancelImport()"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition-all duration-200">
                    <i class="fas fa-times mr-2"></i>Hủy
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript cho import functionality -->
<script>
    /**
     * Hàm chuyển đổi giữa các tab import
     */
    function showImportTab(tabType) {
        const fileTab = document.getElementById('tab-file');
        const manualTab = document.getElementById('tab-manual');
        const fileContent = document.getElementById('import-file');
        const manualContent = document.getElementById('import-manual');

        if (tabType === 'file') {
            // Active file tab
            fileTab.className = 'px-6 py-3 font-medium text-blue-600 border-b-2 border-blue-600 transition-all duration-200';
            manualTab.className = 'px-6 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition-all duration-200';

            fileContent.classList.remove('hidden');
            manualContent.classList.add('hidden');
        } else {
            // Active manual tab
            manualTab.className = 'px-6 py-3 font-medium text-green-600 border-b-2 border-green-600 transition-all duration-200';
            fileTab.className = 'px-6 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition-all duration-200';

            manualContent.classList.remove('hidden');
            fileContent.classList.add('hidden');
        }
    }

    /**
     * Xử lý khi user chọn file
     */
    function handleFileSelect(input) {
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const submitBtn = input.closest('form').querySelector('button[type="submit"]');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Hiển thị thông tin file
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');

            // Enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
        }
    }

    /**
     * Xóa file đã chọn
     */
    function removeFile() {
        const input = document.getElementById('timetable-file');
        const fileInfo = document.getElementById('file-info');
        const submitBtn = document.querySelector('#import-file button[type="submit"]');

        input.value = '';
        fileInfo.classList.add('hidden');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
    }

    /**
     * Format file size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Download template file
     */
    function downloadTemplate() {
        alert('Chức năng download template đang được phát triển!\nTemplate sẽ có format:\nTên môn học | Thứ | Giờ bắt đầu | Giờ kết thúc | Phòng học | Giảng viên');
        // TODO: Implement template download
    }

    /**
     * Reset form thêm môn học
     */
    function resetForm() {
        const form = document.querySelector('#import-manual form');
        form.reset();

        // Focus vào tên môn học
        document.getElementById('subject-name').focus();
    }

    /**
     * Confirm import data
     */
    function confirmImport() {
        alert('Chức năng import data đang được phát triển!');
        // TODO: Send data to server
    }

    /**
     * Cancel import
     */
    function cancelImport() {
        document.getElementById('import-preview').classList.add('hidden');
        removeFile();
    }
</script>