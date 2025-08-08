<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý tài liệu</h3>
        </div>
        <button onclick="openUp()"
            class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Upload tài liệu</span>
        </button>
    </div>

    <!-- Modal Up tài liệu -->
    <div id="upload-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50 min-h-screen">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-600 to-green-500">
                    <h3 class="text-lg font-semibold text-white">Upload tài liệu mới</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <input type="hidden" name="action" value="upload">
                    <!-- Chọn file -->
                    <div class="border border-dashed border-green-300 rounded-xl p-6 text-center bg-green-50">
                        <i class="fas fa-file-arrow-up text-4xl text-green-600 mb-3"></i>
                        <p class="text-lg font-semibold text-gray-700 mb-2">Chọn file để upload</p>
                        <p class="text-sm text-gray-500 mb-4">Hỗ trợ: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG, ZIP, RAR (Tối đa 20MB)</p>

                        <input type="file" id="document-file" name="document_file[]" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar"
                            class="hidden" onchange="autoFillTitle(this)">

                        <button type="button" onclick="document.getElementById('document-file').click()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 font-semibold cursor-pointer">
                            <i class="fas fa-folder-open mr-2"></i>Chọn file
                        </button>

                        <div id="file-name-display" class="mt-4 text-gray-600 font-medium"></div>
                    </div>
                    <!-- Thông tin tài liệu -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-heading mr-2"></i>Tiêu đề tài liệu
                            </label>
                            <input type="text" id="title" name="title"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent focus:outline-none transition-all duration-200"
                                placeholder="Tên tài liệu">
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tags mr-2"></i>Danh mục
                            </label>
                            <div class="relative">
                                <select id="category" name="category" required
                                    class="w-full px-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200"
                                    style="appearance:none;-webkit-appearance:none;background-image:none;">
                                    <option value="">Chọn danh mục</option>
                                    <option value="lecture">Bài giảng</option>
                                    <option value="assignment">Bài tập</option>
                                    <option value="exam">Thi cử</option>
                                    <option value="reference">Tài liệu tham khảo</option>
                                    <option value="other">Khác</option>
                                </select>
                                <i class="fas fa-chevron-down text-sm transition-transform absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-book mr-2"></i>Môn học
                        </label>
                        <input type="text" id="subject" name="subject"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent focus:outline-none transition-all duration-200"
                            placeholder="Tự chọn">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2"></i>Mô tả
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"
                            placeholder="Mô tả về tài liệu"></textarea>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeUp()"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-all duration-200 cursor-pointer">
                            Hủy
                        </button>
                        <button type="submit" id="upload-btn"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition-all duration-200 flex items-center space-x-2 shadow-lg cursor-pointer">
                            <span>Upload tài liệu</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- lọc -->
    <div class="bg-gradient-to-br from-green-100 to-emerald-200 border border-green-200 rounded-xl p-6 shadow-lg">
        <div class="mb-2">
            <h4 class="text-sm font-medium text-gray-700 flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Bộ lọc
            </h4>
        </div>

        <form method="GET" action="" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <!-- Theo danh mục -->
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>Danh mục
                    </label>
                    <div class="relative">
                        <select id="category-filter" name="category"
                            class="w-full px-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200"
                            style="appearance:none;-webkit-appearance:none;background-image:none;">
                            <option value="">Tất cả danh mục</option>
                            <option value="lecture" <?php echo $category_filter === 'lecture' ? 'selected' : ''; ?>>Bài giảng</option>
                            <option value="assignment" <?php echo $category_filter === 'assignment' ? 'selected' : ''; ?>>Bài tập</option>
                            <option value="exam" <?php echo $category_filter === 'exam' ? 'selected' : ''; ?>>Thi cử</option>
                            <option value="reference" <?php echo $category_filter === 'reference' ? 'selected' : ''; ?>>Tài liệu tham khảo</option>
                            <option value="other" <?php echo $category_filter === 'other' ? 'selected' : ''; ?>>Khác</option>
                        </select>
                        <i class="fas fa-chevron-down text-sm transition-transform absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label for="subject-filter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-book mr-2"></i>Môn học
                    </label>
                    <div class="relative">
                        <select id="subject-filter" name="subject"
                            class="w-full px-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200"
                            style="appearance:none;-webkit-appearance:none;background-image:none;">
                            <option value="">Tất cả môn học</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo htmlspecialchars($subject['subject']); ?>"
                                    <?php echo $subject_filter === $subject['subject'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subject['subject']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-chevron-down text-sm transition-transform absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center cursor-pointer">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm
                    </button>
                </div>

                <div>
                    <a href="documents.php"
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Xóa bộ lọc
                    </a>
                </div>
            </div>
        </form>
    </div>
    <!-- Danh sách -->
    <div class="documents-container">
        <?php if (empty($documents)): ?>
            <div class="text-center py-12">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl text-gray-500 mb-2">Không tìm thấy tài liệu nào</h3>
                <p class="text-gray-400">Hãy thử thay đổi bộ lọc hoặc upload tài liệu mới</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($documents as $doc): ?>
                    <div class="border border-green-300 rounded-2xl hover:bg-green-50 hover:border-green-600 p-6 shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden transform hover:-translate-y-1 hover:scale-105 flex flex-col h-full cursor-pointer">
                        <!-- Icon -->
                        <div class="flex items-start space-x-3 p-2">
                            <div class="text-2xl <?php echo getFileIconColor($doc['file_type']); ?> flex-shrink-0 mt-1">
                                <i class="<?php echo getFileIcon($doc['file_type']); ?>"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-base leading-tight truncate border-b border-gray-100 mb-3 pb-2" title="<?php echo htmlspecialchars($doc['title']); ?>">
                                    <?php echo htmlspecialchars($doc['title']); ?>
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        <?php echo getCategoryDisplay($doc['category']); ?>
                                    </span>
                                    <i class="fas fa-ellipsis-h text-gray-500 text-xs"></i>
                                    <span class="truncate" title="<?php echo htmlspecialchars($doc['subject'] ?: 'Tự chọn'); ?>">
                                        <?php echo htmlspecialchars($doc['subject'] ?: 'Tự chọn'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <?php if (!empty($doc['description'])): ?>
                            <div class="px-2 py-3 flex-grow">
                                <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed hover:line-clamp-none transition-all duration-200">
                                    <?php echo htmlspecialchars($doc['description']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <!-- Thông tin -->
                        <div class="px-2 pt-2 border-t border-gray-100 mt-auto">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <div class="flex items-center space-x-1">
                                        <i class="fas fa-hdd text-gray-400"></i>
                                        <span><?php echo formatFileSize($doc['file_size']); ?></span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                        <span><?php echo date('d/m/Y', strtotime($doc['created_at'])); ?></span>
                                    </div>
                                </div>
                                <!-- Xem trước, tải và xóa -->
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                        class="bg-blue-100 text-blue-600 hover:bg-blue-300 py-1.5 px-3 rounded-lg transition-all duration-200 preview-btn cursor-pointer"
                                        data-file="<?php echo htmlspecialchars($doc['file_path']); ?>"
                                        data-type="<?php echo htmlspecialchars($doc['file_type']); ?>"
                                        data-title="<?php echo htmlspecialchars($doc['title']); ?>">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <a href="?action=download&id=<?php echo $doc['doc_id']; ?>"
                                        class="bg-green-100 text-green-600 hover:bg-green-300 py-1.5 px-3 rounded-lg transition-all duration-200">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                    <form method="POST" action="" class="inline" data-confirm="Bạn có chắc muốn xóa tài liệu này?">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="document_id" value="<?php echo $doc['doc_id']; ?>">
                                        <button type="submit"
                                            class="bg-red-100 text-red-600 hover:bg-red-300 py-1.5 px-3 rounded-lg transition-all duration-200 cursor-pointer">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Preview tài liệu -->
<div id="preview-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50 min-h-screen">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-500 flex items-center justify-start">
                <h3 id="preview-title" class="text-lg font-semibold text-white">Xem trước</h3>
            </div>
            <div id="preview-body" class="bg-white" style="height: 80vh;">
                <div class="w-full h-full flex items-center justify-center text-gray-500">
                    Đang tải...
                </div>
            </div>
            <div class="px-6 py-3 border-t border-gray-200 flex items-center justify-end bg-gray-50">
                <button onclick="closePreview()" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-all duration-200 cursor-pointer">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Tự động điền tiêu đề
    function autoFillTitle(input) {
        const files = input.files;
        const titleInput = document.getElementById('title');
        const fileDisplay = document.getElementById('file-name-display');

        if (files.length > 0) {
            // Điền tiêu đề từ file đầu tiên
            const fileName = files[0].name;
            const nameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
            titleInput.value = files.length === 1 ? nameWithoutExt : `${nameWithoutExt} và ${files.length - 1} file khác`;
            // Hiển thị tên file đã chọn
            if (files.length === 1) {
                fileDisplay.textContent = `Đã chọn: ${files[0].name}`;
            } else {
                fileDisplay.textContent = `Đã chọn ${files.length} files`;
            }
        } else {
            titleInput.value = '';
            fileDisplay.textContent = '';
        }
    }

    function openUp() {
        const modal = document.getElementById('upload-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeUp() {
        const modal = document.getElementById('upload-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.querySelector('#upload-modal form').reset();
        document.getElementById('file-name-display').textContent = '';
    }

    document.querySelector('#upload-modal > div').addEventListener('click', function(e) {
        if (e.target === this) {
            closeUp();
        }
    });

    // Preview tài liệu
    const previewModal = document.getElementById('preview-modal');
    const previewBody = document.getElementById('preview-body');
    const previewTitle = document.getElementById('preview-title');

    function openPreview() {
        previewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePreview() {
        previewModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Dọn nội dung để tránh giữ iframe/img cũ
        previewBody.innerHTML = '<div class="w-full h-full flex items-center justify-center text-gray-500">Đang tải...</div>';
    }
    window.closePreview = closePreview;

    function renderPreview(filePath, fileType) {
        const ext = (fileType || '').toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            previewBody.innerHTML = `<div class="w-full h-full flex items-center justify-center bg-black/5"><img src="${filePath}" alt="preview" class="max-h-full max-w-full rounded shadow" /></div>`;
            return;
        }
        if (ext === 'pdf') {
            previewBody.innerHTML = `<iframe src="${filePath}" style="width: 100%; height: 100%;" frameborder="0"></iframe>`;
            return;
        }
        if (ext === 'txt') {
            fetch(filePath).then(r => r.text()).then(text => {
                const safe = text.replace(/[&<>]/g, s => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;'
                } [s]));
                previewBody.innerHTML = `<pre class="w-full h-full overflow-auto p-4 text-sm">${safe}</pre>`;
            }).catch(() => {
                previewBody.innerHTML = `<div class="w-full h-full flex items-center justify-center text-gray-500">Không thể tải nội dung</div>`;
            });
            return;
        }
        previewBody.innerHTML = `<div class="w-full h-full flex items-center justify-center text-gray-500 p-6 text-center">Định dạng .${ext} chưa hỗ trợ xem trước. Vui lòng tải về hoặc chuyển sang PDF/Ảnh để xem trong trình duyệt.</div>`;
    }

    document.querySelectorAll('.preview-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const path = btn.getAttribute('data-file');
            const type = btn.getAttribute('data-type');
            const title = btn.getAttribute('data-title') || 'Xem trước';
            previewTitle.textContent = title;
            renderPreview(path, type);
            openPreview();
        });
    });
</script>

<?php
//Format kích thước
function formatFileSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}
function getFileIcon($extension)
{
    $icons = [
        'pdf' => 'fas fa-file-pdf',
        'doc' => 'fas fa-file-word',
        'docx' => 'fas fa-file-word',
        'xls' => 'fas fa-file-excel',
        'xlsx' => 'fas fa-file-excel',
        'ppt' => 'fas fa-file-powerpoint',
        'pptx' => 'fas fa-file-powerpoint',
        'txt' => 'fas fa-file-alt',
        'jpg' => 'fas fa-file-image',
        'jpeg' => 'fas fa-file-image',
        'png' => 'fas fa-file-image',
        'zip' => 'fas fa-file-archive',
        'rar' => 'fas fa-file-archive'
    ];
    return $icons[$extension] ?? 'fas fa-file';
}

function getFileIconColor($extension)
{
    $colors = [
        'pdf' => 'text-red-500',
        'doc' => 'text-blue-500',
        'docx' => 'text-blue-500',
        'xls' => 'text-green-500',
        'xlsx' => 'text-green-500',
        'ppt' => 'text-orange-500',
        'pptx' => 'text-orange-500',
        'txt' => 'text-gray-500',
        'jpg' => 'text-purple-500',
        'jpeg' => 'text-purple-500',
        'png' => 'text-purple-500',
        'zip' => 'text-yellow-500',
        'rar' => 'text-yellow-500'
    ];
    return $colors[$extension] ?? 'text-gray-500';
}

function getCategoryDisplay($category)
{
    $categories = [
        'lecture' => 'Bài giảng',
        'assignment' => 'Bài tập',
        'exam' => 'Thi cử',
        'reference' => 'Tài liệu tham khảo',
        'other' => 'Khác'
    ];
    return $categories[$category] ?? $category;
}
?>