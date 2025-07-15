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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="expense-stats">
        <!-- Tổng chi tiêu tháng -->
        <div class="bg-gradient-to-br from-red-400 to-red-600 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Tổng chi tiêu tháng này</p>
                    <p class="text-2xl font-bold mt-1" id="monthly-total">0 VNĐ</p>
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
                    <p class="text-2xl font-bold mt-1" id="today-total">0 VNĐ</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-calendar-day text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Trung bình chi tiêu -->
        <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Trung bình/ngày</p>
                    <p class="text-2xl font-bold mt-1" id="avg-amount">0 VNĐ</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-calculator text-2xl text-white"></i>
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
        <!-- Loading state -->
        <div class="text-center py-12" id="loading-expenses">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
            <span class="text-gray-600">Đang tải danh sách chi tiêu...</span>
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
    // Biến global
    let currentExpenses = [];
    let currentFilters = {
        category: '',
        month: new Date().toISOString().slice(0, 7) // Format: YYYY-MM
    };

    // Khởi tạo khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        loadExpenseStats();
        loadExpensesList();

        // Set tháng hiện tại cho filter
        document.getElementById('filter-month').value = currentFilters.month;
    });

    /**
     * Load thống kê chi tiêu
     */
    async function loadExpenseStats() {
        try {
            const response = await fetch(`api/expenses-api.php?action=stats&month=${currentFilters.month}`);
            const data = await response.json();

            if (data.success) {
                updateStatsDisplay(data.data);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    /**
     * Cập nhật hiển thị thống kê
     */
    function updateStatsDisplay(stats) {
        document.getElementById('monthly-total').textContent = stats.monthly.total_amount_formatted;
        document.getElementById('today-total').textContent = stats.today.today_amount_formatted;
        document.getElementById('avg-amount').textContent = stats.monthly.avg_amount_formatted;
    }

    /**
     * Load danh sách chi tiêu
     */
    async function loadExpensesList() {
        const loadingEl = document.getElementById('loading-expenses');
        const listEl = document.getElementById('expenses-list');

        try {
            loadingEl.style.display = 'block';

            const params = new URLSearchParams({
                action: 'list',
                category: currentFilters.category,
                month: currentFilters.month
            });

            const response = await fetch(`api/expenses-api.php?${params}`);
            const data = await response.json();

            if (data.success) {
                currentExpenses = data.data;
                renderExpensesList(data.data);
            }
        } catch (error) {
            console.error('Error loading expenses:', error);
            showError('Không thể tải danh sách chi tiêu');
        } finally {
            loadingEl.style.display = 'none';
        }
    }

    /**
     * Render danh sách chi tiêu
     */
    function renderExpensesList(expenses) {
        const listEl = document.getElementById('expenses-list');
        const emptyEl = document.getElementById('no-expenses');

        // Xóa loading và empty state
        const loadingEl = document.getElementById('loading-expenses');
        loadingEl.style.display = 'none';
        emptyEl.style.display = 'none';

        if (expenses.length === 0) {
            emptyEl.style.display = 'block';
            return;
        }

        const expensesHtml = expenses.map(expense => createExpenseCard(expense)).join('');
        listEl.innerHTML = expensesHtml;
    }

    /**
     * Tạo HTML cho một khoản chi
     */
    function createExpenseCard(expense) {
        const categoryIcons = {
            'food': 'fas fa-utensils',
            'transport': 'fas fa-bus',
            'education': 'fas fa-book',
            'entertainment': 'fas fa-film',
            'shopping': 'fas fa-shopping-bag',
            'health': 'fas fa-heartbeat',
            'other': 'fas fa-receipt'
        };

        const categoryColors = {
            'food': 'orange',
            'transport': 'blue',
            'education': 'purple',
            'entertainment': 'pink',
            'shopping': 'green',
            'health': 'red',
            'other': 'gray'
        };

        const categoryLabels = {
            'food': '🍜 Ăn uống',
            'transport': '🚌 Di chuyển',
            'education': '📚 Học tập',
            'entertainment': '🎬 Giải trí',
            'shopping': '🛒 Mua sắm',
            'health': '🏥 Y tế',
            'other': '📝 Khác'
        };

        const icon = categoryIcons[expense.category] || 'fas fa-receipt';
        const color = categoryColors[expense.category] || 'gray';
        const label = categoryLabels[expense.category] || '📝 Khác';

        return `
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300" data-category="${expense.category}" data-id="${expense.id}">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <!-- Thông tin chi tiêu -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-${color}-100 text-${color}-600 rounded-full flex items-center justify-center">
                                <i class="${icon}"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">${expense.title}</h4>
                                <p class="text-sm text-gray-500">${label} • ${expense.expense_date_formatted}</p>
                            </div>
                        </div>
                        ${expense.description ? `<p class="text-gray-600 text-sm ml-13">${expense.description}</p>` : ''}
                    </div>

                    <!-- Số tiền và actions -->
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-2xl font-bold text-red-600">${expense.amount_formatted.replace(' VNĐ', '')}</p>
                            <p class="text-sm text-gray-500">VNĐ</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editExpense(${expense.id})"
                                class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteExpense(${expense.id})"
                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-all duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

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
        currentFilters.category = document.getElementById('filter-expense-category').value;
        currentFilters.month = document.getElementById('filter-month').value;

        loadExpenseStats();
        loadExpensesList();
    }

    /**
     * Sửa chi tiêu
     */
    function editExpense(id) {
        const expense = currentExpenses.find(e => e.id == id);
        if (!expense) {
            showError('Không tìm thấy chi tiêu');
            return;
        }

        // TODO: Implement edit modal
        alert(`Sửa chi tiêu: ${expense.title}`);
    }

    /**
     * Xóa chi tiêu
     */
    async function deleteExpense(id) {
        if (!confirm('Bạn có chắc muốn xóa chi tiêu này?')) {
            return;
        }

        try {
            const response = await fetch(`api/expenses-api.php?action=delete&id=${id}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('Đã xóa chi tiêu thành công');
                loadExpenseStats();
                loadExpensesList();
            } else {
                showError(data.error || 'Không thể xóa chi tiêu');
            }
        } catch (error) {
            console.error('Error deleting expense:', error);
            showError('Không thể xóa chi tiêu');
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
    document.getElementById('expense-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

            // Lấy dữ liệu form
            const formData = new FormData(this);
            const expenseData = {
                title: formData.get('title'),
                amount: parseFloat(formData.get('amount')),
                category: formData.get('category'),
                expense_date: formData.get('date'),
                description: formData.get('note')
            };

            const response = await fetch('api/expenses-api.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(expenseData)
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('Thêm chi tiêu thành công!');
                hideAddExpenseForm();
                loadExpenseStats();
                loadExpensesList();
            } else {
                showError(data.error || 'Không thể thêm chi tiêu');
            }
        } catch (error) {
            console.error('Error adding expense:', error);
            showError('Không thể thêm chi tiêu');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    /**
     * Format số tiền trong input
     */
    document.getElementById('expense-amount').addEventListener('input', function(e) {
        // Loại bỏ ký tự không phải số
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    /**
     * Hiển thị thông báo thành công
     */
    function showSuccess(message) {
        // Tạo toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Hiển thị toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Ẩn toast sau 3 giây
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    /**
     * Hiển thị thông báo lỗi
     */
    function showError(message) {
        // Tạo toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Hiển thị toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Ẩn toast sau 3 giây
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
</script>