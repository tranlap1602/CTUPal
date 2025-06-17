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

<!-- JavaScript cho Notes - OPTIMIZED VERSION -->
<script>
    // Global variables
    let currentNotes = [];
    let allNotes = []; // Cache tất cả notes để filter client-side
    let editingNoteId = null;
    let searchTimeout = null; // Để debouncing

    /**
     * Load tất cả ghi chú khi trang được tải
     */
    document.addEventListener('DOMContentLoaded', function() {
        loadAllNotes();

        // Setup search debouncing
        setupSearchDebouncing();
    });

    /**
     * Setup debouncing cho search input
     */
    function setupSearchDebouncing() {
        const searchInput = document.getElementById('search-notes');
        searchInput.addEventListener('input', function() {
            // Clear timeout trước đó
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Set timeout mới - chỉ search sau 300ms không gõ
            searchTimeout = setTimeout(() => {
                performClientSideFilter();
            }, 300);
        });
    }

    /**
     * Load tất cả ghi chú từ server (chỉ gọi khi cần thiết)
     */
    async function loadAllNotes() {
        try {
            showLoading();

            const response = await fetch('notes.php?api=get_notes');
            const result = await response.json();

            if (result.success) {
                allNotes = result.data;
                currentNotes = [...allNotes]; // Copy để filter
                renderNotes(currentNotes);
                hideLoading();
            } else {
                showNotification(result.message || 'Không thể tải ghi chú', 'error');
                hideLoading();
            }
        } catch (error) {
            console.error('Error loading notes:', error);
            showNotification('Lỗi kết nối server', 'error');
            hideLoading();
        }
    }

    /**
     * Filter notes trên client-side (không gọi API)
     */
    function performClientSideFilter() {
        const searchTerm = document.getElementById('search-notes').value.toLowerCase().trim();
        const categoryFilter = document.getElementById('filter-note-category').value;
        const priorityFilter = document.getElementById('filter-priority').value;

        let filteredNotes = [...allNotes];

        // Filter theo search term
        if (searchTerm) {
            filteredNotes = filteredNotes.filter(note =>
                note.title.toLowerCase().includes(searchTerm) ||
                note.content.toLowerCase().includes(searchTerm) ||
                note.tags.some(tag => tag.toLowerCase().includes(searchTerm))
            );
        }

        // Filter theo category
        if (categoryFilter) {
            filteredNotes = filteredNotes.filter(note => note.category === categoryFilter);
        }

        // Filter theo priority
        if (priorityFilter) {
            filteredNotes = filteredNotes.filter(note => note.priority === priorityFilter);
        }

        currentNotes = filteredNotes;
        renderNotesSmooth(currentNotes);
    }

    /**
     * Render notes mượt mà không có loading spinner
     */
    function renderNotesSmooth(notes) {
        const container = document.getElementById('notes-grid');

        // Thêm transition smooth
        container.style.opacity = '0.7';

        setTimeout(() => {
            if (notes.length === 0) {
                const searchTerm = document.getElementById('search-notes').value.trim();
                const hasFilters = document.getElementById('filter-note-category').value ||
                    document.getElementById('filter-priority').value;

                if (searchTerm || hasFilters) {
                    // Empty state khi search/filter không có kết quả
                    container.innerHTML = `
                        <div class="col-span-full text-center py-12 text-gray-500">
                            <i class="fas fa-search text-4xl mb-4 text-gray-300"></i>
                            <h3 class="text-lg font-semibold mb-2">Không tìm thấy ghi chú nào</h3>
                            <p class="mb-4">Thử thay đổi từ khóa hoặc bộ lọc</p>
                            <button onclick="clearAllFilters()"
                                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                Xóa bộ lọc
                            </button>
                        </div>
                    `;
                } else {
                    // Empty state khi chưa có ghi chú nào
                    container.innerHTML = `
                        <div class="col-span-full text-center py-12 text-gray-500">
                            <i class="fas fa-sticky-note text-6xl mb-4 text-gray-300"></i>
                            <h3 class="text-xl font-semibold mb-2">Chưa có ghi chú nào</h3>
                            <p class="mb-4">Hãy thêm ghi chú đầu tiên của bạn!</p>
                            <button onclick="showAddNoteForm()"
                                class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200">
                                Thêm ghi chú ngay
                            </button>
                        </div>
                    `;
                }
            } else {
                container.innerHTML = notes.map(note => createNoteHTML(note)).join('');
            }

            // Restore opacity
            container.style.opacity = '1';
        }, 100);
    }

    /**
     * Render danh sách ghi chú ra HTML (cho lần đầu load)
     */
    function renderNotes(notes) {
        const container = document.getElementById('notes-grid');

        if (notes.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12 text-gray-500" id="no-notes">
                    <i class="fas fa-sticky-note text-6xl mb-4 text-gray-300"></i>
                    <h3 class="text-xl font-semibold mb-2">Chưa có ghi chú nào</h3>
                    <p class="mb-4">Hãy thêm ghi chú đầu tiên của bạn!</p>
                    <button onclick="showAddNoteForm()"
                        class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200">
                        Thêm ghi chú ngay
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = notes.map(note => createNoteHTML(note)).join('');
    }

    /**
     * Xóa tất cả bộ lọc
     */
    function clearAllFilters() {
        document.getElementById('search-notes').value = '';
        document.getElementById('filter-note-category').value = '';
        document.getElementById('filter-priority').value = '';

        currentNotes = [...allNotes];
        renderNotesSmooth(currentNotes);
    }

    /**
     * Lọc ghi chú theo category và priority (sử dụng client-side)
     */
    function filterNotes() {
        performClientSideFilter();
    }

    /**
     * Tìm kiếm ghi chú (sử dụng debouncing, không cần function riêng)
     */
    function searchNotes() {
        // Function này sẽ được thay thế bởi debouncing trong setupSearchDebouncing()
        // Giữ lại để backward compatibility
        performClientSideFilter();
    }

    /**
     * Tạo HTML cho một ghi chú
     */
    function createNoteHTML(note) {
        const priorityColors = {
            low: {
                bg: 'bg-green-100',
                text: 'text-green-800',
                icon: '🟢'
            },
            medium: {
                bg: 'bg-yellow-100',
                text: 'text-yellow-800',
                icon: '🟡'
            },
            high: {
                bg: 'bg-red-100',
                text: 'text-red-800',
                icon: '🔴'
            }
        };

        const categoryIcons = {
            study: {
                icon: '📚',
                bg: 'bg-blue-100',
                text: 'text-blue-800',
                name: 'Học tập'
            },
            personal: {
                icon: '👤',
                bg: 'bg-purple-100',
                text: 'text-purple-800',
                name: 'Cá nhân'
            },
            work: {
                icon: '💼',
                bg: 'bg-gray-100',
                text: 'text-gray-800',
                name: 'Công việc'
            },
            idea: {
                icon: '💡',
                bg: 'bg-yellow-100',
                text: 'text-yellow-800',
                name: 'Ý tưởng'
            },
            todo: {
                icon: '✅',
                bg: 'bg-green-100',
                text: 'text-green-800',
                name: 'Việc cần làm'
            },
            other: {
                icon: '📝',
                bg: 'bg-gray-100',
                text: 'text-gray-800',
                name: 'Khác'
            }
        };

        const priority = priorityColors[note.priority] || priorityColors.medium;
        const category = categoryIcons[note.category] || categoryIcons.other;

        const tagsHTML = note.tags.map(tag =>
            `<span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#${tag.trim()}</span>`
        ).join('');

        return `
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" 
                 data-category="${note.category}" data-priority="${note.priority}" data-note-id="${note.id}">
                <!-- Header ghi chú -->
                <div class="flex justify-between items-start mb-4">
                    <h4 class="text-lg font-bold text-gray-800 line-clamp-2">${escapeHtml(note.title)}</h4>
                    <span class="${priority.bg} ${priority.text} text-xs font-semibold px-3 py-1 rounded-full">
                        ${priority.icon} ${getPriorityText(note.priority)}
                    </span>
                </div>

                <!-- Meta info -->
                <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                    <span class="${category.bg} ${category.text} px-3 py-1 rounded-full text-xs font-semibold">
                        ${category.icon} ${category.name}
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-calendar mr-1"></i>
                        ${note.created_at_formatted}
                    </span>
                </div>

                <!-- Preview nội dung -->
                <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                    ${escapeHtml(note.content.substring(0, 150))}${note.content.length > 150 ? '...' : ''}
                </div>

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 mb-4">
                    ${tagsHTML}
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <button onclick="viewNote(${note.id})"
                        class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center">
                        <i class="fas fa-eye mr-1"></i>Xem
                    </button>
                    <div class="flex space-x-2">
                        <button onclick="editNote(${note.id})"
                            class="text-green-600 hover:text-green-800 font-semibold text-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteNote(${note.id})"
                            class="text-red-600 hover:text-red-800 font-semibold text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Hiển thị form thêm ghi chú
     */
    function showAddNoteForm() {
        editingNoteId = null;
        document.getElementById('note-form').reset();
        document.querySelector('#add-note-form h4').innerHTML = '<i class="fas fa-edit mr-3 text-purple-500"></i>Thêm ghi chú mới';

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
        editingNoteId = null;
    }

    /**
     * Xem chi tiết ghi chú
     */
    function viewNote(id) {
        const note = allNotes.find(n => n.id == id);
        if (!note) return;

        // Tạo modal xem chi tiết
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-gray-800">${escapeHtml(note.title)}</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">${getCategoryText(note.category)}</span>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm ml-2">${getPriorityText(note.priority)}</span>
                    </div>
                    <div class="text-gray-600 whitespace-pre-wrap mb-4">${escapeHtml(note.content)}</div>
                    ${note.tags.length > 0 ? `
                        <div class="flex flex-wrap gap-2 mb-4">
                            ${note.tags.map(tag => `<span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">#${tag.trim()}</span>`).join('')}
                        </div>
                    ` : ''}
                    <div class="text-sm text-gray-500">Tạo lúc: ${note.created_at_formatted}</div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
    }

    /**
     * Sửa ghi chú
     */
    function editNote(id) {
        const note = allNotes.find(n => n.id == id);
        if (!note) return;

        editingNoteId = id;

        // Fill form với dữ liệu note
        document.getElementById('note-title').value = note.title;
        document.getElementById('note-category').value = note.category;
        document.getElementById('note-priority').value = note.priority;
        document.getElementById('note-content').value = note.content;
        document.getElementById('note-tags').value = note.tags.join(', ');

        // Thay đổi tiêu đề form
        document.querySelector('#add-note-form h4').innerHTML = '<i class="fas fa-edit mr-3 text-green-500"></i>Cập nhật ghi chú';

        // Hiển thị form
        const form = document.getElementById('add-note-form');
        form.classList.remove('hidden');
        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.getElementById('note-title').focus();
    }

    /**
     * Xóa ghi chú
     */
    async function deleteNote(id) {
        if (!confirm('Bạn có chắc muốn xóa ghi chú này?')) return;

        try {
            const response = await fetch('notes.php?api=delete_note', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message, 'success');

                // Cập nhật cache local
                allNotes = allNotes.filter(note => note.id !== id);
                performClientSideFilter(); // Re-filter với dữ liệu mới
            } else {
                showNotification(result.message || 'Không thể xóa ghi chú', 'error');
            }
        } catch (error) {
            console.error('Error deleting note:', error);
            showNotification('Lỗi kết nối server', 'error');
        }
    }

    /**
     * Xử lý submit form
     */
    document.getElementById('note-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Lấy dữ liệu form
        const formData = new FormData(this);
        const noteData = {
            title: formData.get('title').trim(),
            category: formData.get('category'),
            priority: formData.get('priority'),
            content: formData.get('content').trim(),
            tags: formData.get('tags').trim()
        };

        // Validation
        if (!noteData.title || !noteData.content) {
            showNotification('Vui lòng nhập đầy đủ tiêu đề và nội dung', 'error');
            return;
        }

        try {
            const apiEndpoint = editingNoteId ? 'update_note' : 'add_note';
            const requestData = editingNoteId ? {
                ...noteData,
                id: editingNoteId
            } : noteData;

            const response = await fetch(`notes.php?api=${apiEndpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message, 'success');
                hideAddNoteForm();

                // Cập nhật cache local
                if (editingNoteId) {
                    // Update existing note
                    const index = allNotes.findIndex(note => note.id == editingNoteId);
                    if (index !== -1) {
                        allNotes[index] = result.data;
                    }
                } else {
                    // Add new note
                    allNotes.unshift(result.data);
                }

                performClientSideFilter(); // Re-filter với dữ liệu mới
            } else {
                showNotification(result.message || 'Không thể lưu ghi chú', 'error');
            }
        } catch (error) {
            console.error('Error saving note:', error);
            showNotification('Lỗi kết nối server', 'error');
        }
    });

    // ==================== UTILITY FUNCTIONS ====================

    /**
     * Escape HTML để tránh XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    }

    /**
     * Lấy text hiển thị cho priority
     */
    function getPriorityText(priority) {
        const priorities = {
            low: 'Thấp',
            medium: 'Trung bình',
            high: 'Cao'
        };
        return priorities[priority] || 'Trung bình';
    }

    /**
     * Lấy text hiển thị cho category
     */
    function getCategoryText(category) {
        const categories = {
            study: 'Học tập',
            personal: 'Cá nhân',
            work: 'Công việc',
            idea: 'Ý tưởng',
            todo: 'Việc cần làm',
            other: 'Khác'
        };
        return categories[category] || 'Khác';
    }

    /**
     * Hiển thị loading
     */
    function showLoading() {
        const container = document.getElementById('notes-grid');
        container.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Đang tải...</p>
            </div>
        `;
    }

    /**
     * Ẩn loading
     */
    function hideLoading() {
        // Loading sẽ được thay thế bởi renderNotes()
    }

    /**
     * Hiển thị thông báo
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 animate-slide-down ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' :
                    'fa-info-circle'
                } mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Tự động xóa sau 5 giây
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
</script>