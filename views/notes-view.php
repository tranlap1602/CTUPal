<!-- Notes Section với Tailwind CSS -->
<div class="space-y-8">
    <!-- Header với nút thêm ghi chú -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Ghi chú của tôi</h3>
            <p class="text-gray-600">Quản lý ghi chú học tập và cá nhân</p>
        </div>
        <button onclick="showAddNoteForm()"
            class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm ghi chú</span>
        </button>
    </div>

    <!-- Form thêm ghi chú (ẩn mặc định) -->
    <div id="add-note-form" class="hidden bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 rounded-2xl p-8 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit mr-3 text-purple-500"></i>
                Thêm ghi chú mới
            </h4>
            <button onclick="hideAddNoteForm()" class="text-gray-500 hover:text-gray-700 p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="note-form" class="space-y-6">
            <!-- Tiêu đề -->
            <div>
                <label for="note-title" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heading mr-2"></i>Tiêu đề *
                </label>
                <input type="text" id="note-title" name="title" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                    placeholder="Nhập tiêu đề ghi chú...">
            </div>

            <!-- Danh mục -->
            <div>
                <label for="note-category" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-folder mr-2"></i>Danh mục *
                </label>
                <select id="note-category" name="category" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-vertical"
                    placeholder="Nhập nội dung ghi chú..."></textarea>
            </div>

            <!-- Nút action -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideAddNoteForm()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Lưu ghi chú</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Bộ lọc và tìm kiếm -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Lọc danh mục -->
            <div>
                <label for="filter-note-category" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
                </label>
                <select id="filter-note-category" onchange="filterNotes()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Tất cả danh mục</option>
                    <option value="study">Học tập</option>
                    <option value="personal">Cá nhân</option>
                    <option value="work">Công việc</option>
                    <option value="idea">Ý tưởng</option>
                    <option value="other">Khác</option>
                </select>
            </div>

            <!-- Tìm kiếm -->
            <div>
                <label for="search-notes" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </label>
                <input type="text" id="search-notes" onkeyup="searchNotes()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Tìm kiếm ghi chú...">
            </div>
        </div>
    </div>

    <!-- Grid hiển thị ghi chú -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="notes-grid">
        <!-- Ghi chú sẽ được load bằng JavaScript -->
    </div>

    <!-- Loading spinner -->
    <div id="loading-spinner" class="hidden justify-center items-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
    </div>

    <!-- Empty state -->
    <div id="empty-state" class="hidden text-center py-12">
        <div class="text-gray-400 mb-4">
            <i class="fas fa-sticky-note text-6xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có ghi chú nào</h3>
        <p class="text-gray-500 mb-6">Bắt đầu tạo ghi chú đầu tiên của bạn!</p>
        <button onclick="showAddNoteForm()"
            class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>Thêm ghi chú
        </button>
    </div>
</div>

<!-- Modal thêm/sửa ghi chú (dùng chung) -->
<div id="note-modal" class="fixed inset-0 z-50 hidden bg-black/10 backdrop-blur-sm">
    <div class="flex items-center justify-center p-4 h-full">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="note-modal-title" class="text-xl font-bold text-gray-800"></h3>
                    <button onclick="closeNoteModal()" class="text-gray-500 hover:text-gray-700 p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="note-modal-form" class="space-y-6">
                    <input type="hidden" id="note-modal-id">
                    <!-- Tiêu đề -->
                    <div>
                        <label for="note-modal-title-input" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heading mr-2"></i>Tiêu đề *
                        </label>
                        <input type="text" id="note-modal-title-input" name="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <!-- Danh mục -->
                    <div>
                        <label for="note-modal-category" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-folder mr-2"></i>Danh mục *
                        </label>
                        <select id="note-modal-category" name="category" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="study">Học tập</option>
                            <option value="personal">Cá nhân</option>
                            <option value="work">Công việc</option>
                            <option value="idea">Ý tưởng</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <!-- Nội dung -->
                    <div>
                        <label for="note-modal-content" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-text mr-2"></i>Nội dung *
                        </label>
                        <textarea id="note-modal-content" name="content" rows="6" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-vertical"></textarea>
                    </div>
                    <!-- Nút action -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button type="button" onclick="closeNoteModal()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            Hủy
                        </button>
                        <button type="submit" id="note-modal-submit"
                            class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span id="note-modal-submit-text"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal sửa ghi chú -->
<div id="edit-note-modal" class="fixed inset-0 z-50 hidden bg-black/10 backdrop-blur-sm">
    <div class="flex items-center justify-center p-4 h-full">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Sửa ghi chú</h3>
                    <button onclick="closeEditNoteModal()" class="text-gray-500 hover:text-gray-700 p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="edit-note-form" class="space-y-6">
                    <input type="hidden" id="edit-note-id">

                    <!-- Tiêu đề -->
                    <div>
                        <label for="edit-note-title" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heading mr-2"></i>Tiêu đề *
                        </label>
                        <input type="text" id="edit-note-title" name="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Danh mục -->
                    <div>
                        <label for="edit-note-category" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-folder mr-2"></i>Danh mục *
                        </label>
                        <select id="edit-note-category" name="category" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="study">Học tập</option>
                            <option value="personal">Cá nhân</option>
                            <option value="work">Công việc</option>
                            <option value="idea">Ý tưởng</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <!-- Nội dung -->
                    <div>
                        <label for="edit-note-content" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-text mr-2"></i>Nội dung *
                        </label>
                        <textarea id="edit-note-content" name="content" rows="12" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-vertical"></textarea>
                    </div>

                    <!-- Nút action -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button type="button" onclick="closeEditNoteModal()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            Hủy
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Cập nhật</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Biến global
    let allNotes = [];
    let searchTimeout;

    // Khởi tạo khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        loadNotes();
    });

    // Load danh sách ghi chú
    function loadNotes() {
        showLoading(true);

        fetch('notes.php?api=get_notes')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allNotes = data.data;
                    displayNotes(allNotes);
                } else {
                    showToast('Không thể tải danh sách ghi chú: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Lỗi kết nối server', 'error');
            })
            .finally(() => {
                showLoading(false);
            });
    }

    // Hiển thị ghi chú
    function displayNotes(notes) {
        const grid = document.getElementById('notes-grid');
        const emptyState = document.getElementById('empty-state');

        if (notes.length === 0) {
            grid.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        grid.innerHTML = notes.map(note => `
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-category="${note.category}">
            <!-- Header ghi chú -->
            <div class="flex justify-between items-start mb-4">
                <h4 class="text-lg font-bold text-gray-800 line-clamp-2">${escapeHtml(note.title)}</h4>
            </div>

            <!-- Meta info -->
            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                    ${getCategoryText(note.category)}
                </span>
                <span class="flex items-center">
                    <i class="fas fa-calendar mr-1"></i>
                    ${note.created_at_formatted}
                </span>
            </div>

            <!-- Preview nội dung -->
            <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                ${escapeHtml(note.content)}
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <button onclick="editNote(${note.id})"
                    class="bg-green-50 text-green-600 hover:bg-green-100 p-2 rounded-lg transition-all duration-200"
                    title="Sửa ghi chú">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteNote(${note.id})"
                    class="bg-red-50 text-red-600 hover:bg-red-100 p-2 rounded-lg transition-all duration-200"
                    title="Xóa ghi chú">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
    }

    // Lọc ghi chú
    function filterNotes() {
        const categoryFilter = document.getElementById('filter-note-category').value;
        const searchTerm = document.getElementById('search-notes').value.toLowerCase();

        let filteredNotes = allNotes.filter(note => {
            // Lọc theo danh mục
            if (categoryFilter && note.category !== categoryFilter) return false;

            // Lọc theo tìm kiếm
            if (searchTerm) {
                const searchText = `${note.title} ${note.content}`.toLowerCase();
                if (!searchText.includes(searchTerm)) return false;
            }

            return true;
        });

        displayNotes(filteredNotes);
    }

    // Tìm kiếm với debounce
    function searchNotes() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterNotes();
        }, 300);
    }

    // Hiển thị form thêm ghi chú
    function showAddNoteForm() {
        document.getElementById('add-note-form').classList.remove('hidden');
        document.getElementById('note-form').reset();
    }

    // Ẩn form thêm ghi chú
    function hideAddNoteForm() {
        document.getElementById('add-note-form').classList.add('hidden');
    }

    // Submit form thêm ghi chú
    document.getElementById('note-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const noteData = {
            title: formData.get('title'),
            content: formData.get('content'),
            category: formData.get('category')
        };

        fetch('notes.php?api=add_note', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(noteData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    hideAddNoteForm();
                    loadNotes();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Lỗi kết nối server', 'error');
            });
    });

    // Sửa ghi chú
    function editNote(id) {
        const note = allNotes.find(n => n.id == id);
        if (!note) return;

        document.getElementById('edit-note-id').value = note.id;
        document.getElementById('edit-note-title').value = note.title;
        document.getElementById('edit-note-content').value = note.content;
        document.getElementById('edit-note-category').value = note.category;

        // Hiển thị modal
        document.getElementById('edit-note-modal').classList.remove('hidden');
    }

    // Đóng modal sửa ghi chú
    function closeEditNoteModal() {
        document.getElementById('edit-note-modal').classList.add('hidden');
    }

    // Submit form sửa ghi chú
    document.getElementById('edit-note-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const noteId = document.getElementById('edit-note-id').value;
        const formData = new FormData(this);
        const noteData = {
            id: noteId,
            title: formData.get('title'),
            content: formData.get('content'),
            category: formData.get('category')
        };

        fetch('notes.php?api=update_note', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(noteData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeEditNoteModal();
                    loadNotes();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Lỗi kết nối server', 'error');
            });
    });

    // Xóa ghi chú
    function deleteNote(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa ghi chú này?')) return;

        fetch('notes.php?api=delete_note', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    loadNotes();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Lỗi kết nối server', 'error');
            });
    }

    // Utility functions
    function getCategoryText(category) {
        const texts = {
            'study': 'Học tập',
            'personal': 'Cá nhân',
            'work': 'Công việc',
            'idea': 'Ý tưởng',
            'other': 'Khác'
        };
        return texts[category] || 'Khác';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showLoading(show) {
        const spinner = document.getElementById('loading-spinner');
        if (show) {
            spinner.classList.remove('hidden');
            spinner.classList.add('flex');
        } else {
            spinner.classList.add('hidden');
            spinner.classList.remove('flex');
        }
    }

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
        }, 3000);
    }
</script>