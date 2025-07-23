<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý ghi chú</h3>
        </div>
        <button onclick="showAddNoteForm()"
            class="bg-indigo-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm ghi chú</span>
        </button>
    </div>
    <!-- Thêm ghi chú-->
    <div id="add-note-form" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 border border-indigo-300 rounded-2xl p-8 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit mr-3 text-indigo-500"></i>
                Thêm ghi chú mới
            </h4>
            <button onclick="hideAddNoteForm()" class="text-gray-500 hover:text-gray-700 p-2">
                <i class="fas fa-times text-xl cursor-pointer"></i>
            </button>
        </div>

        <form action="notes.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add">
            <!-- Tiêu đề -->
            <div>
                <label for="note-title" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heading mr-2"></i>Tiêu đề *
                </label>
                <input type="text" id="note-title" name="title" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200"
                    placeholder="Nhập tiêu đề ghi chú...">
            </div>
            <!-- Danh mục -->
            <div>
                <label for="note-category" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tags mr-2"></i>Danh mục *
                </label>
                <select id="note-category" name="category" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    <option value="">Chọn danh mục</option>
                    <option value="study">Học tập</option>
                    <option value="personal">Cá nhân</option>
                    <option value="work">Công việc</option>
                    <option value="idea">Ý tưởng</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <!-- Nội dung -->
            <div>
                <label for="note-content" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file-text mr-2"></i>Nội dung *
                </label>
                <textarea id="note-content" name="content" rows="6" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"
                    placeholder="Nhập nội dung ghi chú..."></textarea>
            </div>
            <!-- Hủy và lưu -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideAddNoteForm()"
                    class="bg-red-500 text-white px-6 py-3 border rounded-lg font-semibold hover:bg-red-600 cursor-pointer transition-all duration-200">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-indigo-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 cursor-pointer transition-all duration-200 flex items-center space-x-2">
                    <span>Lưu ghi chú</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Lọc theo danh mục -->
    <div class="bg-white border border-indigo-300 rounded-2xl p-6 shadow-lg">
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700">
                <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
            </h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="notes.php"
                class="px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center <?php echo empty($_GET['category']) ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-list block text-lg mb-1"></i>
                <span class="text-sm">Tất cả</span>
            </a>

            <a href="notes.php?category=study"
                class="px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center <?php echo ($_GET['category'] ?? '') === 'study' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-graduation-cap block text-lg mb-1"></i>
                <span class="text-sm">Học tập</span>
            </a>

            <a href="notes.php?category=personal"
                class="px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center <?php echo ($_GET['category'] ?? '') === 'personal' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-user block text-lg mb-1"></i>
                <span class="text-sm">Cá nhân</span>
            </a>

            <a href="notes.php?category=work"
                class="px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center <?php echo ($_GET['category'] ?? '') === 'work' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-briefcase block text-lg mb-1"></i>
                <span class="text-sm">Công việc</span>
            </a>

            <a href="notes.php?category=idea"
                class="px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center <?php echo ($_GET['category'] ?? '') === 'idea' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-lightbulb block text-lg mb-1"></i>
                <span class="text-sm">Ý tưởng</span>
            </a>

            <a href="notes.php?category=other"
                class="px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center <?php echo ($_GET['category'] ?? '') === 'other' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-ellipsis-h block text-lg mb-1"></i>
                <span class="text-sm">Khác</span>
            </a>
        </div>
    </div>

    <!-- Danh sách ghi chú -->
    <?php if (empty($notes)): ?>
        <div class="flex justify-center items-center py-8">
            <div class="text-center max-w-md">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-sticky-note text-6xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có ghi chú nào</h3>
                <p class="text-gray-500 mb-4">Tạo ghi chú!</p>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($notes as $note): ?>
                <div class="border border-indigo-300 hover:bg-indigo-50 hover:border-indigo-500 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 hover:scale-105 flex flex-col h-full">
                    <!-- Header ghi chú -->
                    <div class="flex justify-between items-start border-b border-gray-300 mb-2">
                        <h4 class="text-lg font-bold text-gray-800 pb-2 line-clamp-2"><?php echo htmlspecialchars($note['title']); ?></h4>
                    </div>
                    <!-- Thông tin -->
                    <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                        <span class="bg-blue-200 text-blue-800 px-2 py-1.5 rounded-full text-xs font-semibold">
                            <?php echo getCategoryText($note['category']); ?>
                        </span>
                    </div>
                    <!-- Preview nội dung -->
                    <div class="text-gray-600 text-sm mb-4 line-clamp-3 hover:line-clamp-none transition-all duration-200">
                        <?php echo htmlspecialchars($note['content']); ?>
                    </div>
                    <!-- Thông tin và actions -->
                    <div class="pt-2 border-t border-gray-300 mt-auto">
                        <div class="flex items-center justify-between">
                            <!-- Thời gian -->
                            <div class="flex items-center space-x-1 text-xs text-gray-500">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                                <span><?php echo date('d/m/Y H:i', strtotime($note['updated_at'])); ?></span>
                            </div>
                            <!-- Sửa và xóa -->
                            <div class="flex items-center space-x-2">
                                <a href="notes.php?action=edit&id=<?php echo $note['id']; ?>"
                                    class="bg-green-100 text-green-600 hover:bg-green-300 py-1.5 px-3 rounded-lg transition-all duration-200"
                                    title="Xem hoặc sửa ghi chú">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="notes.php" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghi chú này?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $note['id']; ?>">
                                    <button type="submit"
                                        class="bg-red-100 text-red-600 hover:bg-red-300 py-1.5 px-3 rounded-lg transition-all duration-200"
                                        title="Xóa ghi chú">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
</div>

<!-- Modal sửa ghi chú -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($edit_note)): ?>
    <div class="fixed inset-0 z-50 bg-black/10 backdrop-blur-sm">
        <div class="flex items-center justify-center p-4 h-full">
            <div class="bg-white rounded-2xl border border-indigo-300 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-edit mr-3 text-indigo-500"></i>
                            Sửa ghi chú
                        </h3>
                        <a href="notes.php" class="text-gray-500 hover:text-gray-700 p-2">
                            <i class="fas fa-times text-xl cursor-pointer"></i>
                        </a>
                    </div>

                    <form action="notes.php" method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $edit_note['id']; ?>">
                        <!-- Tiêu đề -->
                        <div>
                            <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-heading mr-2"></i>Tiêu đề
                            </label>
                            <input type="text" id="edit-title" name="title" value="<?php echo htmlspecialchars($edit_note['title']); ?>" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200">
                        </div>
                        <!-- Danh mục -->
                        <div>
                            <label for="edit-category" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2"></i>Danh mục
                            </label>
                            <select id="edit-category" name="category" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200">
                                <option value="study" <?php echo $edit_note['category'] === 'study' ? 'selected' : ''; ?>>Học tập</option>
                                <option value="personal" <?php echo $edit_note['category'] === 'personal' ? 'selected' : ''; ?>>Cá nhân</option>
                                <option value="work" <?php echo $edit_note['category'] === 'work' ? 'selected' : ''; ?>>Công việc</option>
                                <option value="idea" <?php echo $edit_note['category'] === 'idea' ? 'selected' : ''; ?>>Ý tưởng</option>
                                <option value="other" <?php echo $edit_note['category'] === 'other' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                        <!-- Nội dung -->
                        <div>
                            <label for="edit-content" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-file-text mr-2"></i>Nội dung
                            </label>
                            <textarea id="edit-content" name="content" rows="12" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"><?php echo htmlspecialchars($edit_note['content']); ?></textarea>
                        </div>
                        <!-- Hủy và lưu -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="notes.php"
                                class="bg-red-500 text-white px-6 py-3 border rounded-lg font-semibold hover:bg-red-600 cursor-pointer transition-all duration-200">
                                Hủy
                            </a>
                            <button type="submit"
                                class="bg-indigo-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 cursor-pointer transition-all duration-200 flex items-center space-x-2">
                                <i class="fas fa-save"></i>
                                <span>Cập nhật</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function showAddNoteForm() {
        document.getElementById('add-note-form').classList.remove('hidden');
        document.getElementById('note-form').reset();
    }

    function hideAddNoteForm() {
        document.getElementById('add-note-form').classList.add('hidden');
    }
    // Hàm hiển thị thông báo
    function showToast(message, type = 'success') {
        const color = type === 'success' ? 'green' : 'red';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 bg-${color}-500 text-white px-6 py-3 rounded-lg shadow-lg z-50`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icon} mr-2"></i>
                <span>${message}</span>
            </div>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 1000);
    }

    // Hiển thị thông báo
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const type = urlParams.get('type') || 'success';

        if (message) {
            showToast(decodeURIComponent(message), type);
            // Clean up URL
            const newUrl = new URL(window.location);
            newUrl.searchParams.delete('message');
            newUrl.searchParams.delete('type');
            window.history.replaceState({}, '', newUrl);
        }
    });
</script>

<?php
function getCategoryText($category)
{
    $texts = [
        'study' => 'Học tập',
        'personal' => 'Cá nhân',
        'work' => 'Công việc',
        'idea' => 'Ý tưởng',
        'other' => 'Khác'
    ];
    return $texts[$category] ?? 'Khác';
}
?>