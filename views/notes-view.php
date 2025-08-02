<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý ghi chú</h3>
        </div>
        <button onclick="openNote('add')"
            class="bg-indigo-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm ghi chú</span>
        </button>
    </div>

    <!-- Lọc-->
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-300 rounded-2xl p-6 shadow-lg">
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700">
                <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
            </h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="notes.php"
                class="border border-indigo-300 px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center 
                <?php echo empty($_GET['category']) ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-list block text-lg mb-1"></i>
                <span class="text-sm">Tất cả</span>
            </a>

            <a href="notes.php?category=study"
                class="border border-indigo-300 px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center 
                <?php echo ($_GET['category'] ?? '') === 'study' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-graduation-cap block text-lg mb-1"></i>
                <span class="text-sm">Học tập</span>
            </a>

            <a href="notes.php?category=personal"
                class="border border-indigo-300 px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center 
                <?php echo ($_GET['category'] ?? '') === 'personal' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-user block text-lg mb-1"></i>
                <span class="text-sm">Cá nhân</span>
            </a>

            <a href="notes.php?category=work"
                class="border border-indigo-300 px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center 
                <?php echo ($_GET['category'] ?? '') === 'work' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-briefcase block text-lg mb-1"></i>
                <span class="text-sm">Công việc</span>
            </a>

            <a href="notes.php?category=idea"
                class="border border-indigo-300 px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center 
                <?php echo ($_GET['category'] ?? '') === 'idea' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-lightbulb block text-lg mb-1"></i>
                <span class="text-sm">Ý tưởng</span>
            </a>

            <a href="notes.php?category=other"
                class="border border-indigo-300 px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center 
                <?php echo ($_GET['category'] ?? '') === 'other' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-indigo-500 hover:text-white'; ?>">
                <i class="fas fa-ellipsis-h block text-lg mb-1"></i>
                <span class="text-sm">Khác</span>
            </a>
        </div>
    </div>

    <!-- Danh sách -->
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
                <div class="border border-indigo-300 hover:bg-indigo-50 hover:border-indigo-500 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 hover:scale-105 flex flex-col h-full cursor-pointer">
                    <div class="flex justify-between items-start border-b border-gray-300 mb-2">
                        <h4 class="text-lg font-bold text-gray-800 pb-2 line-clamp-2"><?php echo htmlspecialchars($note['title']); ?></h4>
                    </div>
                    <!-- Thông tin -->
                    <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-xl text-xs font-semibold">
                            <?php echo getCategoryText($note['category']); ?>
                        </span>
                    </div>
                    <!-- Preview-->
                    <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                        <?php echo htmlspecialchars($note['content']); ?>
                    </div>
                    <!-- Thoi gian va chuc nang -->
                    <div class="pt-2 border-t border-gray-300 mt-auto">
                        <div class="flex items-center justify-between">

                            <div class="flex items-center space-x-1 text-sm text-gray-500">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                                <span><?php echo date('d/m/Y H:i', strtotime($note['updated_at'])); ?></span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button type="button"
                                    class="bg-blue-100 text-blue-600 hover:bg-blue-300 py-1.5 px-3 rounded-lg transition-all duration-200 cursor-pointer"
                                    title="Xem ghi chú"
                                    onclick="openNote('view', <?php echo $note['note_id']; ?>, '<?php echo htmlspecialchars(addslashes($note['title'])); ?>', 
                                    '<?php echo htmlspecialchars(addslashes($note['content'])); ?>', '<?php echo $note['category']; ?>')">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button type="button"
                                    class="bg-green-100 text-green-600 hover:bg-green-300 py-1.5 px-3 rounded-lg transition-all duration-200 cursor-pointer"
                                    title="Sửa ghi chú"
                                    onclick="openNote('edit', <?php echo $note['note_id']; ?>, '<?php echo htmlspecialchars(addslashes($note['title'])); ?>', 
                                    '<?php echo htmlspecialchars(addslashes($note['content'])); ?>', '<?php echo $note['category']; ?>')">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <form action="notes.php" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghi chú này?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $note['note_id']; ?>">
                                    <button type="submit"
                                        class="bg-red-100 text-red-600 hover:bg-red-300 py-1.5 px-3 rounded-lg transition-all duration-200 cursor-pointer"
                                        title="Xóa ghi chú">
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

<!-- Modal thêm sửa và xem -->
<div id="note-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-indigo-600">
                <h3 class="text-lg font-semibold text-white" id="note-modal-title">Thêm ghi chú</h3>
            </div>
            <form id="note-form" action="notes.php" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="action" id="note-action" value="add">
                <input type="hidden" name="id" id="note-id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div id="note-title-section">
                        <label for="note-title" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heading mr-2"></i>Tiêu đề *
                        </label>
                        <input type="text" id="note-title" name="title" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200"
                            placeholder="Nhập tiêu đề ghi chú...">
                    </div>
                    <div id="note-category-section">
                        <label for="note-category" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-2"></i>Danh mục *
                        </label>
                        <select id="note-category" name="category" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200">
                            <option value="">Chọn danh mục</option>
                            <option value="study">Học tập</option>
                            <option value="personal">Cá nhân</option>
                            <option value="work">Công việc</option>
                            <option value="idea">Ý tưởng</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="note-content" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-text mr-2"></i>Nội dung *
                    </label>
                    <textarea id="note-content" name="content" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"
                        placeholder="Nhập nội dung ghi chú..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" id="note-cancel-btn" onclick="closeNote()"
                        class="px-4 py-2 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-all duration-200 cursor-pointer">
                        <span id="note-cancel-text">Đóng</span>
                    </button>
                    <button type="submit" id="note-submit-btn"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-xl font-semibold hover:bg-indigo-600 transition-all duration-200 flex items-center space-x-2 shadow-lg cursor-pointer">
                        <span id="note-submit-text">Lưu ghi chú</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    function openNote(mode, id = '', title = '', content = '', category = '') {
        const modal = document.getElementById('note-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('note-form').reset();

        // Cho phep nhap
        document.getElementById('note-title').disabled = false;
        document.getElementById('note-category').disabled = false;
        document.getElementById('note-content').disabled = false;
        document.getElementById('note-cancel-btn').classList.remove('hidden');
        document.getElementById('note-submit-btn').classList.remove('hidden');
        document.getElementById('note-title-section').style.display = 'block';
        document.getElementById('note-category-section').style.display = 'block';
        document.getElementById('note-content').rows = 6;

        if (mode === 'add') {
            document.getElementById('note-modal-title').textContent = 'Thêm ghi chú';
            document.getElementById('note-action').value = 'add';
            document.getElementById('note-id').value = '';
        } else if (mode === 'edit') {
            document.getElementById('note-modal-title').textContent = 'Sửa ghi chú';
            document.getElementById('note-action').value = 'update';
            document.getElementById('note-id').value = id;
            document.getElementById('note-title').value = title;
            document.getElementById('note-content').value = content;
            document.getElementById('note-category').value = category;
            document.getElementById('note-submit-text').textContent = 'Cập nhật';
        } else if (mode === 'view') {
            document.getElementById('note-modal-title').textContent = 'Xem ghi chú';
            document.getElementById('note-action').value = 'view';
            document.getElementById('note-id').value = id;
            document.getElementById('note-title').value = title;
            document.getElementById('note-content').value = content;
            document.getElementById('note-cancel-text').textContent = 'Đóng';
            document.getElementById('note-submit-btn').classList.add('hidden');

            document.getElementById('note-title').disabled = true;
            document.getElementById('note-content').disabled = true;
            document.getElementById('note-title-section').style.display = 'block';
            document.getElementById('note-category-section').style.display = 'none';
            //So dong khi xem
            document.getElementById('note-content').rows = 12;
        }
    }

    function closeNote() {
        const modal = document.getElementById('note-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    document.querySelector('#note-modal > div').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNote();
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