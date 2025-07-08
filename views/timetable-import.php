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
            Import thời khóa biểu
        </h3>
        <p class="text-gray-600">Import thời khóa biểu từ file .ics</p>
    </div>
    <!-- Import content -->
    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-8 border border-purple-200">
        <!-- Form upload .ics -->
        <form id="ics-import-form" enctype="multipart/form-data" class="space-y-6">
            <!-- Tùy chọn xóa dữ liệu cũ -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox"
                        id="clear-old-data"
                        name="clear_old_data"
                        value="true"
                        class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        Xóa toàn bộ thời khóa biểu cũ trước khi import
                    </span>
                </label>
            </div>

            <!-- Khu vực upload file -->
            <div class="border-2 border-dashed border-purple-300 rounded-lg p-8 text-center hover:border-purple-400 hover:bg-purple-50/50 transition-all duration-200">
                <div class="mb-4">
                    <i class="fas fa-calendar-plus text-4xl text-purple-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-700 mb-2">Chọn file .ics</p>
                    <p class="text-sm text-gray-500">Kéo thả file vào đây hoặc click để chọn</p>
                </div>

                <input type="file"
                    id="ics-file"
                    name="ics_file"
                    accept=".ics"
                    class="hidden"
                    onchange="handleIcsFileSelect(this)">

                <button type="button"
                    onclick="document.getElementById('ics-file').click()"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center space-x-2 mx-auto">
                    <i class="fas fa-folder-open"></i>
                    <span>Chọn file .ics</span>
                </button>
            </div>

            <!-- Hiển thị file đã chọn -->
            <div id="ics-file-info" class="hidden bg-white rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-purple-500 text-2xl mr-3"></i>
                        <div>
                            <p id="ics-file-name" class="font-medium text-gray-800"></p>
                            <p id="ics-file-size" class="text-sm text-gray-500"></p>
                        </div>
                    </div>
                    <button type="button"
                        onclick="removeIcsFile()"
                        class="text-red-500 hover:text-red-700 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Nút upload -->
            <div class="flex justify-center space-x-4">
                <button type="submit"
                    id="ics-submit-btn"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-8 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    <i class="fas fa-upload"></i>
                    <span>Import từ .ics</span>
                </button>
            </div>
        </form>

        <!-- Kết quả import -->
        <div id="ics-import-result" class="hidden mt-8">
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                    Kết quả import
                </h5>
                <div id="ics-result-content" class="space-y-4">
                    <!-- Nội dung sẽ được thêm bằng JavaScript -->
                </div>
            </div>
        </div>
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
     * Xử lý khi user chọn file .ics
     */
    function handleIcsFileSelect(input) {
        const fileInfo = document.getElementById('ics-file-info');
        const fileName = document.getElementById('ics-file-name');
        const fileSize = document.getElementById('ics-file-size');
        const submitBtn = document.getElementById('ics-submit-btn');

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
     * Xóa file .ics đã chọn
     */
    function removeIcsFile() {
        const input = document.getElementById('ics-file');
        const fileInfo = document.getElementById('ics-file-info');
        const submitBtn = document.getElementById('ics-submit-btn');

        input.value = '';
        fileInfo.classList.add('hidden');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
    }

    /**
     * Xử lý submit form .ics
     */
    document.getElementById('ics-import-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Xử lý checkbox clear_old_data
        const clearOldData = document.getElementById('clear-old-data').checked;
        if (clearOldData) {
            formData.set('clear_old_data', 'true');
        } else {
            formData.delete('clear_old_data');
        }
        const submitBtn = document.getElementById('ics-submit-btn');
        const resultDiv = document.getElementById('ics-import-result');
        const resultContent = document.getElementById('ics-result-content');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Đang xử lý...</span>';

        // Gửi request
        fetch('includes/timetable-import-ics.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response is not JSON:', text);
                        throw new Error('Server trả về dữ liệu không hợp lệ: ' + text.substring(0, 100));
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    // Hiển thị kết quả thành công
                    resultContent.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="font-medium text-green-800">Import thành công!</span>
                        </div>
                        <p class="text-sm text-green-700 mt-2">${data.message}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">${data.data.success_count}</div>
                            <div class="text-sm text-blue-700">Sự kiện mới</div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">${data.data.duplicate_count}</div>
                            <div class="text-sm text-yellow-700">Trùng lặp</div>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">${data.data.error_count}</div>
                            <div class="text-sm text-red-700">Lỗi</div>
                        </div>
                    </div>
                `;
                    resultDiv.classList.remove('hidden');

                    // Reset form
                    removeIcsFile();

                    // Auto reload page after 3 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    // Hiển thị lỗi
                    resultContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="font-medium text-red-800">Import thất bại!</span>
                        </div>
                        <p class="text-sm text-red-700 mt-2">${data.message}</p>
                    </div>
                `;
                    resultDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="font-medium text-red-800">Lỗi kết nối!</span>
                        </div>
                        <p class="text-sm text-red-700 mt-2">${error.message}</p>
                    </div>
                `;
                resultDiv.classList.remove('hidden');
            })
            .finally(() => {
                // Enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-upload"></i><span>Import từ .ics</span>';
            });
    });
</script>