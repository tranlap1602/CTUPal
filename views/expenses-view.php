<div class="space-y-8">
    <!-- Header với nút thêm chi tiêu -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý Chi tiêu</h3>
        </div>
        <button onclick="showAddExpenseForm()"
            class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm chi tiêu</span>
        </button>
    </div>
    <!-- Thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tổng chi tiêu tháng -->
        <div class="bg-linear-to-br from-red-400 to-red-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold">Tổng chi tiêu tháng này</p>
                    <p class="text-2xl font-bold"><?php echo number_format($monthly_stats['total_amount'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                </div>
                <i class="fas fa-chart-line text-4xl text-red-200"></i>
            </div>
        </div>
        <!-- Chi tiêu hôm nay -->
        <div class="bg-linear-to-br from-blue-400 to-blue-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm ">Chi tiêu hôm nay</p>
                    <p class="text-2xl font-bold"><?php echo number_format($today_stats['today_amount'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                </div>
                <i class="fas fa-wallet text-4xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Form thêm chi tiêu -->
    <div id="add-expense-form" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-8 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit mr-3 text-blue-500"></i>
                Thêm chi tiêu mới
            </h4>
        </div>

        <form action="expenses.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add">

            <!-- Danh mục và số tiền -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>Danh mục *
                    </label>
                    <select name="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">Chọn danh mục</option>
                        <option value="Ăn uống">Ăn uống</option>
                        <option value="Di chuyển">Di chuyển</option>
                        <option value="Học tập">Học tập</option>
                        <option value="Giải trí">Giải trí</option>
                        <option value="Mua sắm">Mua sắm</option>
                        <option value="Y tế">Y tế</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill mr-2"></i>Số tiền (VNĐ) *
                    </label>
                    <input type="number" name="amount" min="0" step="1000" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200"
                        placeholder="Nhập số tiền...">
                </div>
            </div>

            <!-- Ngày và phương thức -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Ngày *
                    </label>
                    <input type="date" name="expense_date" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2"></i>Phương thức *
                    </label>
                    <div class="flex space-x-4 pt-2">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" checked required
                                class="w-6 h-6 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Tiền mặt</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="payment_method" value="card" required
                                class="w-6 h-6 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Thẻ ngân hàng</span>
                        </label>
                    </div>
                </div>
            </div>
            <!-- Ghi chú -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Mô tả
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"
                    placeholder="Mô tả..."></textarea>
            </div>

            <!-- Nút submit -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideAddExpenseForm()"
                    class="bg-red-500 text-white px-6 py-3 border rounded-lg font-semibold hover:bg-red-600 cursor-pointer transition-all duration-200">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 flex items-center space-x-2 cursor-pointer">
                    <span>Thêm chi tiêu</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white border border-blue-300 rounded-lg p-6 shadow-lg">
        <h3 class="text-base font-semibold text-gray-800 mb-2">
            <i class="fas fa-filter mr-2"></i>Bộ lọc
        </h3>
        <form method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:flex-row sm:items-end sm:gap-4">
            <!-- Bộ lọc theo danh mục -->
            <div class="w-full sm:flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tags mr-2"></i>Danh mục
                </label>
                <select name="category"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    <option value="">Tất cả danh mục</option>
                    <option value="Ăn uống" <?php echo ($category_filter === 'Ăn uống') ? 'selected' : ''; ?>>Ăn uống</option>
                    <option value="Di chuyển" <?php echo ($category_filter === 'Di chuyển') ? 'selected' : ''; ?>>Di chuyển</option>
                    <option value="Học tập" <?php echo ($category_filter === 'Học tập') ? 'selected' : ''; ?>>Học tập</option>
                    <option value="Giải trí" <?php echo ($category_filter === 'Giải trí') ? 'selected' : ''; ?>>Giải trí</option>
                    <option value="Mua sắm" <?php echo ($category_filter === 'Mua sắm') ? 'selected' : ''; ?>>Mua sắm</option>
                    <option value="Y tế" <?php echo ($category_filter === 'Y tế') ? 'selected' : ''; ?>>Y tế</option>
                    <option value="Khác" <?php echo ($category_filter === 'Khác') ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>

            <!-- Bộ lọc theo phương thức thanh toán -->
            <div class="w-full sm:flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-credit-card mr-2"></i>Phương thức
                </label>
                <select name="payment"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    <option value="">Tất cả phương thức</option>
                    <option value="cash" <?php echo ($payment_filter === 'cash') ? 'selected' : ''; ?>>Tiền mặt</option>
                    <option value="card" <?php echo ($payment_filter === 'card') ? 'selected' : ''; ?>>Thẻ ngân hàng</option>
                </select>
            </div>

            <!-- Bộ lọc theo tháng -->
            <div class="w-full sm:flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2"></i>Thời gian
                </label>
                <input type="month" name="month" value="<?php echo $month_filter; ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
            </div>

            <!-- Nút Tìm kiếm -->
            <div class="w-full sm:w-auto">
                <button type="submit"
                    class="w-full sm:w-auto inline-block px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Nút xóa bộ lọc -->
            <?php if (!empty($category_filter) || !empty($payment_filter) || !empty($month_filter)): ?>
                <div class="w-full sm:w-auto">
                    <a href="expenses.php"
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Xóa bộ lọc
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Danh sách chi tiêu -->
    <div class="space-y-4">
        <?php if (empty($expenses)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-wallet text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold mb-2">Chưa có chi tiêu nào</h3>
                <p class="mb-4">Thêm chi tiêu đầu tiên!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <?php foreach ($expenses as $expense): ?>
                    <?php
                    $categoryIcons = [
                        'Ăn uống' => 'fas fa-utensils',
                        'Di chuyển' => 'fas fa-bus',
                        'Học tập' => 'fas fa-book',
                        'Giải trí' => 'fas fa-film',
                        'Mua sắm' => 'fas fa-shopping-bag',
                        'Y tế' => 'fas fa-heartbeat',
                        'Khác' => 'fas fa-receipt'
                    ];

                    $paymentLabels = [
                        'cash' => 'Tiền mặt',
                        'card' => 'Thẻ ngân hàng'
                    ];

                    $icon = $categoryIcons[$expense['category']] ?? 'fas fa-receipt';
                    $paymentLabel = $paymentLabels[$expense['payment_method']] ?? 'Tiền mặt';
                    ?>

                    <div class="border border-blue-300 hover:bg-blue-100 rounded-lg p-4 sm:p-6 shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
                            <!-- Thông tin chi tiêu -->
                            <div class="flex-1 min-w-0 w-full">
                                <div class="flex items-start space-x-3 mb-2">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="<?php echo $icon; ?> text-sm sm:text-base"></i>
                                    </div>
                                    <div class="min-w-0 flex-1 overflow-hidden">
                                        <h4 class="text-base sm:text-lg font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($expense['category']); ?></h4>
                                        <div class="flex flex-col text-xs sm:text-sm text-gray-600 mt-1">
                                            <div class="flex items-center flex-wrap gap-1">
                                                <span class="flex-shrink-0 bg-blue-200 text-blue-800 px-2 py-1.5 rounded-full text-xs font-semibold"><?php echo $paymentLabel; ?></span>
                                                <span class="flex-shrink-0 text-gray-400">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                                <span class="flex-shrink-0"><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></span>
                                                <span class="flex-shrink-0 text-gray-400">|</span>
                                                <span class="text-gray-500"><?php echo date('H:i', strtotime($expense['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($expense['description'])): ?>
                                    <div class="ml-11 sm:ml-13 mt-2">
                                        <p class="text-gray-600 text-xs sm:text-sm leading-relaxed line-clamp-2 hover:line-clamp-none transition-all duration-200"><?php echo htmlspecialchars($expense['description']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Số tiền và nút xóa -->
                            <div class="flex items-center space-x-3 sm:space-x-4 flex-shrink-0 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="text-right">
                                    <p class="text-lg sm:text-2xl font-bold text-green-600"><?php echo number_format($expense['amount'], 0, ',', '.'); ?></p>
                                    <p class="text-xs sm:text-sm text-gray-600">VNĐ</p>
                                </div>
                                <form action="expenses.php" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa chi tiêu này?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 px-2 py-1 sm:px-3 sm:py-2 rounded-lg hover:bg-red-100 transition-all duration-200">
                                        <i class="fas fa-trash text-sm sm:text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function showAddExpenseForm() {
        const form = document.getElementById('add-expense-form');
        form.classList.remove('hidden');
        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.querySelector('input[name="expense_date"]').value = new Date().toISOString().split('T')[0];
    }

    function hideAddExpenseForm() {
        document.getElementById('add-expense-form').classList.add('hidden');
        document.querySelector('form').reset();
    }

    document.querySelector('input[name="amount"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Hiển thị toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="${icon}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 1500);
    }

    // Khởi tạo
    document.addEventListener('DOMContentLoaded', function() {
        // Set tháng hiện tại nếu chưa có
        const monthFilter = document.querySelector('input[name="month"]');
        if (!monthFilter.value) {
            monthFilter.value = new Date().toISOString().slice(0, 7);
        }
    });

    // Check for URL parameters and show toast
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