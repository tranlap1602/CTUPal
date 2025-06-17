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

            <!-- Danh mục và Ưu tiên -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="note-category" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder mr-2"></i>Danh mục *
                    </label>
                    <select id="note-category" name="category" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="">Chọn danh mục</option>
                        <option value="study">📚 Học tập</option>
                        <option value="personal">👤 Cá nhân</option>
                        <option value="work">💼 Công việc</option>
                        <option value="idea">💡 Ý tưởng</option>
                        <option value="todo">✅ Việc cần làm</option>
                        <option value="other">📝 Khác</option>
                    </select>
                </div>

                <div>
                    <label for="note-priority" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-2"></i>Mức độ ưu tiên
                    </label>
                    <select id="note-priority" name="priority"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="low">🟢 Thấp</option>
                        <option value="medium" selected>🟡 Trung bình</option>
                        <option value="high">🔴 Cao</option>
                    </select>
                </div>
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

            <!-- Tags -->
            <div>
                <label for="note-tags" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tags mr-2"></i>Tags (tùy chọn)
                </label>
                <input type="text" id="note-tags" name="tags"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                    placeholder="Ví dụ: toán học, bài tập, quan trọng (phân cách bằng dấu phẩy)">
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Lọc danh mục -->
            <div>
                <label for="filter-note-category" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
                </label>
                <select id="filter-note-category" onchange="filterNotes()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Tất cả danh mục</option>
                    <option value="study">📚 Học tập</option>
                    <option value="personal">👤 Cá nhân</option>
                    <option value="work">💼 Công việc</option>
                    <option value="idea">💡 Ý tưởng</option>
                    <option value="todo">✅ Việc cần làm</option>
                    <option value="other">📝 Khác</option>
                </select>
            </div>

            <!-- Lọc ưu tiên -->
            <div>
                <label for="filter-priority" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-flag mr-2"></i>Mức độ ưu tiên
                </label>
                <select id="filter-priority" onchange="filterNotes()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Tất cả mức độ</option>
                    <option value="high">🔴 Cao</option>
                    <option value="medium">🟡 Trung bình</option>
                    <option value="low">🟢 Thấp</option>
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

        <!-- Ghi chú mẫu 1 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-category="study" data-priority="high">
            <!-- Header ghi chú -->
            <div class="flex justify-between items-start mb-4">
                <h4 class="text-lg font-bold text-gray-800 line-clamp-2">Công thức Toán học quan trọng</h4>
                <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                    🔴 Cao
                </span>
            </div>

            <!-- Meta info -->
            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                    📚 Học tập
                </span>
                <span class="flex items-center">
                    <i class="fas fa-calendar mr-1"></i>
                    15/01/2025
                </span>
            </div>

            <!-- Preview nội dung -->
            <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                Các công thức tích phân cần nhớ cho kỳ thi:
                ∫x^n dx = x^(n+1)/(n+1) + C
                ∫sin(x) dx = -cos(x) + C...
            </div>

            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#toán-học</span>
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#công-thức</span>
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#thi-cuối-kỳ</span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <button onclick="viewNote(1)"
                    class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center">
                    <i class="fas fa-eye mr-1"></i>Xem
                </button>
                <div class="flex space-x-2">
                    <button onclick="editNote(1)"
                        class="text-green-600 hover:text-green-800 font-semibold text-sm">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteNote(1)"
                        class="text-red-600 hover:text-red-800 font-semibold text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Ghi chú mẫu 2 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-category="todo" data-priority="medium">
            <!-- Header ghi chú -->
            <div class="flex justify-between items-start mb-4">
                <h4 class="text-lg font-bold text-gray-800 line-clamp-2">Danh sách việc cần làm tuần này</h4>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                    🟡 Trung bình
                </span>
            </div>

            <!-- Meta info -->
            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                    ✅ Việc cần làm
                </span>
                <span class="flex items-center">
                    <i class="fas fa-calendar mr-1"></i>
                    14/01/2025
                </span>
            </div>

            <!-- Preview nội dung -->
            <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                - Hoàn thành bài tập Vật lý chương 3<br>
                - Chuẩn bị thuyết trình môn Văn về Nguyễn Du<br>
                - Đăng ký học phần kỳ tới...
            </div>

            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#todo</span>
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#tuần-này</span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <button onclick="viewNote(2)"
                    class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center">
                    <i class="fas fa-eye mr-1"></i>Xem
                </button>
                <div class="flex space-x-2">
                    <button onclick="editNote(2)"
                        class="text-green-600 hover:text-green-800 font-semibold text-sm">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteNote(2)"
                        class="text-red-600 hover:text-red-800 font-semibold text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty state nếu không có ghi chú -->
        <div class="col-span-full text-center py-12 text-gray-500" id="no-notes" style="display: none;">
            <i class="fas fa-sticky-note text-6xl mb-4 text-gray-300"></i>
            <h3 class="text-xl font-semibold mb-2">Chưa có ghi chú nào</h3>
            <p class="mb-4">Hãy thêm ghi chú đầu tiên của bạn!</p>
            <button onclick="showAddNoteForm()"
                class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200">
                Thêm ghi chú ngay
            </button>
        </div>
    </div>
</div>

<!-- JavaScript cho Notes -->
<script>
    /**
     * Hiển thị form thêm ghi chú
     */
    function showAddNoteForm() {
        const form = document.getElementById('add-note-form');
        form.classList.remove('hidden');
        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.getElementById('note-title').focus();
    }

    /**
     * Ẩn form thêm ghi chú
     */
    function hideAddNoteForm() {
        const form = document.getElementById('add-note-form');
        form.classList.add('hidden');
        document.getElementById('note-form').reset();
    }

    /**
     * Lọc ghi chú theo category và priority
     */
    function filterNotes() {
        const categoryFilter = document.getElementById('filter-note-category').value;
        const priorityFilter = document.getElementById('filter-priority').value;
        const notes = document.querySelectorAll('[data-category]');

        notes.forEach(note => {
            const matchCategory = !categoryFilter || note.dataset.category === categoryFilter;
            const matchPriority = !priorityFilter || note.dataset.priority === priorityFilter;

            if (matchCategory && matchPriority) {
                note.style.display = 'block';
            } else {
                note.style.display = 'none';
            }
        });

        checkEmptyState();
    }

    /**
     * Tìm kiếm ghi chú
     */
    function searchNotes() {
        const searchTerm = document.getElementById('search-notes').value.toLowerCase();
        const notes = document.querySelectorAll('[data-category]');

        notes.forEach(note => {
            const title = note.querySelector('h4').textContent.toLowerCase();
            const content = note.querySelector('.text-gray-600').textContent.toLowerCase();

            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                note.style.display = 'block';
            } else {
                note.style.display = 'none';
            }
        });

        checkEmptyState();
    }

    /**
     * Kiểm tra và hiển thị empty state
     */
    function checkEmptyState() {
        const visibleNotes = document.querySelectorAll('[data-category]:not([style*="display: none"])');
        const emptyState = document.getElementById('no-notes');

        if (visibleNotes.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    /**
     * Xem chi tiết ghi chú
     */
    function viewNote(id) {
        alert(`Xem ghi chú ID: ${id}`);
        // TODO: Implement view note modal
    }

    /**
     * Sửa ghi chú
     */
    function editNote(id) {
        alert(`Sửa ghi chú ID: ${id}`);
        // TODO: Implement edit note functionality
    }

    /**
     * Xóa ghi chú
     */
    function deleteNote(id) {
        if (confirm('Bạn có chắc muốn xóa ghi chú này?')) {
            alert(`Đã xóa ghi chú ID: ${id}`);
            // TODO: Implement delete note functionality
        }
    }

    /**
     * Xử lý submit form
     */
    document.getElementById('note-form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Lấy dữ liệu form
        const formData = new FormData(this);
        const noteData = {
            title: formData.get('title'),
            category: formData.get('category'),
            priority: formData.get('priority'),
            content: formData.get('content'),
            tags: formData.get('tags')
        };

        console.log('Note data:', noteData);

        // TODO: Send data to server
        alert('Ghi chú đã được lưu! (Chỉ là demo)');
        hideAddNoteForm();
    });
</script>