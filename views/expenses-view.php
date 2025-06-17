<!-- Expenses Section với Tailwind CSS -->
<div class="space-y-8">
    <!-- Header với nút thêm chi tiêu -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý chi tiêu</h3>
            <p class="text-gray-600">Theo dõi và kiểm soát chi tiêu cá nhân</p>
        </div>
        <button onclick="showAddExpenseForm()"
            class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-600 hover:to-pink-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm chi tiêu</span>
        </button>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tổng chi tiêu tháng -->
        <div class="bg-gradient-to-br from-red-400 to-red-600 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Tổng chi tiêu tháng này</p>
                    <p class="text-2xl font-bold mt-1">2,450,000 VNĐ</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-chart-line text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Chi tiêu hôm nay -->
        <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Chi tiêu hôm nay</p>
                    <p class="text-2xl font-bold mt-1">120,000 VNĐ</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-calendar-day text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Ngân sách còn lại -->
        <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Ngân sách còn lại</p>
                    <p class="text-2xl font-bold mt-1">1,550,000 VNĐ</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-piggy-bank text-2xl text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Form thêm chi tiêu (ẩn mặc định) -->
    <div id="add-expense-form" class="hidden bg-gradient-to-br from-red-50 to-pink-50 border border-red-200 rounded-2xl p-8 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle mr-3 text-red-500"></i>
                Thêm chi tiêu mới
            </h4>
            <button onclick="hideAddExpenseForm()" class="text-gray-500 hover:text-gray-700 p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="expense-form" class="space-y-6">
            <!-- Mô tả và số tiền -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="expense-title" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-text mr-2"></i>Mô tả *
                    </label>
                    <input type="text" id="expense-title" name="title" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                        placeholder="VD: Ăn trưa, xe bus, mua sách...">
                </div>

                <div>
                    <label for="expense-amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill mr-2"></i>Số tiền (VNĐ) *
                    </label>
                    <input type="number" id="expense-amount" name="amount" min="0" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                        placeholder="Nhập số tiền...">
                </div>
            </div>

            <!-- Danh mục và ngày -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="expense-category" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>Danh mục *
                    </label>
                    <select id="expense-category" name="category" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                        <option value="">Chọn danh mục</option>
                        <option value="food">🍜 Ăn uống</option>
                        <option value="transport">🚌 Di chuyển</option>
                        <option value="education">📚 Học tập</option>
                        <option value="entertainment">🎬 Giải trí</option>
                        <option value="shopping">🛒 Mua sắm</option>
                        <option value="health">🏥 Y tế</option>
                        <option value="other">📝 Khác</option>
                    </select>
                </div>

                <div>
                    <label for="expense-date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Ngày *
                    </label>
                    <input type="date" id="expense-date" name="date" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                </div>
            </div>

            <!-- Ghi chú -->
            <div>
                <label for="expense-note" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Ghi chú (tùy chọn)
                </label>
                <textarea id="expense-note" name="note" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-vertical"
                    placeholder="Ghi chú thêm về chi tiêu này..."></textarea>
            </div>

            <!-- Nút action -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideAddExpenseForm()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-600 hover:to-pink-700 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Thêm chi tiêu</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Lọc danh mục -->
            <div>
                <label for="filter-expense-category" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
                </label>
                <select id="filter-expense-category" onchange="filterExpenses()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Tất cả danh mục</option>
                    <option value="food">🍜 Ăn uống</option>
                    <option value="transport">🚌 Di chuyển</option>
                    <option value="education">📚 Học tập</option>
                    <option value="entertainment">🎬 Giải trí</option>
                    <option value="shopping">🛒 Mua sắm</option>
                    <option value="health">🏥 Y tế</option>
                    <option value="other">📝 Khác</option>
                </select>
            </div>

            <!-- Lọc tháng -->
            <div>
                <label for="filter-month" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2"></i>Lọc theo tháng
                </label>
                <input type="month" id="filter-month" onchange="filterExpenses()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            </div>
        </div>
    </div>

    <!-- Danh sách chi tiêu -->
    <div class="space-y-4" id="expenses-list">
        <!-- Chi tiêu mẫu 1 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300" data-category="food">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Thông tin chi tiêu -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Ăn trưa</h4>
                            <p class="text-sm text-gray-500">🍜 Ăn uống • 15/01/2025</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm ml-13">Cơm văn phòng</p>
                </div>

                <!-- Số tiền và actions -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-2xl font-bold text-red-600">45,000</p>
                        <p class="text-sm text-gray-500">VNĐ</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editExpense(1)"
                            class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteExpense(1)"
                            class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-all duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiêu mẫu 2 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300" data-category="transport">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Thông tin chi tiêu -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Xe bus đi học</h4>
                            <p class="text-sm text-gray-500">🚌 Di chuyển • 15/01/2025</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm ml-13">Vé xe bus 2 chiều</p>
                </div>

                <!-- Số tiền và actions -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-2xl font-bold text-red-600">20,000</p>
                        <p class="text-sm text-gray-500">VNĐ</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editExpense(2)"
                            class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteExpense(2)"
                            class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-all duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiêu mẫu 3 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300" data-category="education">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Thông tin chi tiêu -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Mua sách giáo khoa</h4>
                            <p class="text-sm text-gray-500">📚 Học tập • 14/01/2025</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm ml-13">Sách Toán học cao cấp</p>
                </div>

                <!-- Số tiền và actions -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-2xl font-bold text-red-600">150,000</p>
                        <p class="text-sm text-gray-500">VNĐ</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editExpense(3)"
                            class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteExpense(3)"
                            class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-all duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state nếu không có chi tiêu -->
        <div class="text-center py-12 text-gray-500" id="no-expenses" style="display: none;">
            <i class="fas fa-wallet text-6xl mb-4 text-gray-300"></i>
            <h3 class="text-xl font-semibold mb-2">Chưa có chi tiêu nào</h3>
            <p class="mb-4">Hãy thêm chi tiêu đầu tiên của bạn!</p>
            <button onclick="showAddExpenseForm()"
                class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-600 hover:to-pink-700 transition-all duration-200">
                Thêm chi tiêu ngay
            </button>
        </div>
    </div>

    <!-- Biểu đồ chi tiêu -->
    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-lg">
        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-chart-pie mr-3 text-blue-500"></i>
            Biểu đồ chi tiêu theo danh mục
        </h4>
        <div class="bg-gradient-to-br from-gray-50 to-blue-50 border-2 border-dashed border-gray-300 rounded-xl p-12 text-center">
            <i class="fas fa-chart-bar text-6xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg font-medium mb-2">Biểu đồ sẽ được hiển thị ở đây</p>
            <p class="text-gray-500">Cần tích hợp Chart.js hoặc library biểu đồ khác</p>
        </div>
    </div>
</div>

<!-- JavaScript cho Expenses -->
<script>
    /**
     * Hiển thị form thêm chi tiêu
     */
    function showAddExpenseForm() {
        const form = document.getElementById('add-expense-form');
        form.classList.remove('hidden');
        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.getElementById('expense-title').focus();

        // Set ngày hiện tại làm mặc định
        document.getElementById('expense-date').value = new Date().toISOString().split('T')[0];
    }

    /**
     * Ẩn form thêm chi tiêu
     */
    function hideAddExpenseForm() {
        const form = document.getElementById('add-expense-form');
        form.classList.add('hidden');
        document.getElementById('expense-form').reset();
    }

    /**
     * Lọc chi tiêu theo category và tháng
     */
    function filterExpenses() {
        const categoryFilter = document.getElementById('filter-expense-category').value;
        const monthFilter = document.getElementById('filter-month').value;
        const expenses = document.querySelectorAll('[data-category]');

        expenses.forEach(expense => {
            const matchCategory = !categoryFilter || expense.dataset.category === categoryFilter;
            // TODO: Implement month filtering logic
            const matchMonth = true; // Placeholder

            if (matchCategory && matchMonth) {
                expense.style.display = 'block';
            } else {
                expense.style.display = 'none';
            }
        });

        checkEmptyState();
    }

    /**
     * Kiểm tra và hiển thị empty state
     */
    function checkEmptyState() {
        const visibleExpenses = document.querySelectorAll('[data-category]:not([style*="display: none"])');
        const emptyState = document.getElementById('no-expenses');

        if (visibleExpenses.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    /**
     * Sửa chi tiêu
     */
    function editExpense(id) {
        alert(`Sửa chi tiêu ID: ${id}`);
        // TODO: Implement edit expense functionality
    }

    /**
     * Xóa chi tiêu
     */
    function deleteExpense(id) {
        if (confirm('Bạn có chắc muốn xóa chi tiêu này?')) {
            alert(`Đã xóa chi tiêu ID: ${id}`);
            // TODO: Implement delete expense functionality
        }
    }

    /**
     * Format số tiền VNĐ
     */
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
    }

    /**
     * Xử lý submit form
     */
    document.getElementById('expense-form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Lấy dữ liệu form
        const formData = new FormData(this);
        const expenseData = {
            title: formData.get('title'),
            amount: formData.get('amount'),
            category: formData.get('category'),
            date: formData.get('date'),
            note: formData.get('note')
        };

        console.log('Expense data:', expenseData);

        // TODO: Send data to server
        alert('Chi tiêu đã được lưu! (Chỉ là demo)');
        hideAddExpenseForm();
    });

    /**
     * Format số tiền trong input
     */
    document.getElementById('expense-amount').addEventListener('input', function(e) {
        // Loại bỏ ký tự không phải số
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>