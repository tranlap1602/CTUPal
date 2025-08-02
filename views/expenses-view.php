<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý chi tiêu</h3>
        </div>
        <button onclick="addExpense()"
            class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm chi tiêu</span>
        </button>
    </div>
    <!-- Thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-linear-to-br from-red-600 to-red-500 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold">Chi tiêu tháng này</p>
                    <p class="text-2xl font-bold"><?php echo number_format($monthly_stats['total_amount'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                </div>
                <i class="fas fa-chart-simple text-4xl"></i>
            </div>
        </div>

        <div class="bg-linear-to-br from-blue-600 to-blue-500 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm ">Chi tiêu hôm nay</p>
                    <p class="text-2xl font-bold"><?php echo number_format($today_stats['today_amount'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                </div>
                <i class="fas fa-wallet text-4xl"></i>
            </div>
        </div>
    </div>
    <!-- Biểu đồ -->
    <div class="font-bold text-lg mb-2">Biểu đồ chi tiêu</div>
    <div class="max-w-[800px] mx-auto my-6 flex flex-col md:flex-row gap-8 justify-between items-center w-full">
        <div class="flex flex-col items-center w-full md:w-auto max-w-[220px] md:max-w-[320px]">
            <canvas id="monthChart" height="40"></canvas>
        </div>
        <div class="flex flex-col items-center w-full md:w-auto max-w-[220px] md:max-w-[320px]">
            <canvas id="todayChart" height="40"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/charts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthLabels = <?php echo json_encode(array_column($category_stats, 'category')); ?>;
            const monthData = <?php echo json_encode(array_map('floatval', array_column($category_stats, 'total'))); ?>;
            const todayLabels = <?php echo json_encode(array_column($category_today_stats, 'category')); ?>;
            const todayData = <?php echo json_encode(array_map('floatval', array_column($category_today_stats, 'total'))); ?>;
            window.renderCharts(monthLabels, monthData, todayLabels, todayData);
        });
    </script>

    <!-- Modal thêm chi tiêu -->
    <div id="add-expense-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50 min-h-screen">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-500">
                    <h3 class="text-lg font-semibold text-white">Thêm chi tiêu mới</h3>
                </div>
                <form action="expenses.php" method="POST" class="p-6 space-y-6">
                    <input type="hidden" name="action" value="add">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tags mr-2"></i>Danh mục *
                            </label>
                            <select name="category" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 bg-white">
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200"
                                placeholder="Nhập số tiền...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2"></i>Ngày *
                            </label>
                            <input type="date" name="expense_date" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-credit-card mr-2"></i>Phương thức *
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-3 cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200">
                                    <input type="radio" name="payment_method" value="cash" checked required
                                        class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Tiền mặt</span>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200">
                                    <input type="radio" name="payment_method" value="card" required
                                        class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Thẻ ngân hàng</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2"></i>Mô tả
                        </label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"
                            placeholder="Mô tả chi tiết..."></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeAddExpenseModal()"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-all duration-200 cursor-pointer">
                            Đóng
                        </button>
                        <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 shadow-lg cursor-pointer">
                            <span>Lưu chi tiêu</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- lọc -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-xl p-6 shadow-lg">
        <div class="mb-2">
            <h4 class="text-sm font-medium text-gray-700">
                <i class="fas fa-filter mr-2"></i>Bộ lọc
            </h4>
        </div>
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <!-- Theo danh mục -->
                <div class="w-full">
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
                <!-- Theo phương thức-->
                <div class="w-full">
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
                <!-- Theo tháng -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2"></i>Thời gian
                    </label>
                    <input type="month" name="month" value="<?php echo $month_filter; ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                </div>
                <!-- Tìm kiếm -->
                <div class="w-full">
                    <button type="submit"
                        class="w-full px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <!-- Xóa -->
                <?php if (!empty($category_filter) || !empty($payment_filter) || !empty($month_filter)): ?>
                    <div class="w-full">
                        <a href="expenses.php"
                            class="w-full px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Xóa bộ lọc
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <!-- Danh sách-->
    <div class="space-y-4">
        <?php if (empty($expenses)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-wallet text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold mb-2">Chưa có chi tiêu nào</h3>
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

                    <div class="border border-gray-300 hover:border-blue-300 hover:shadow-lg hover:bg-blue-100 rounded-xl p-4 transition-all duration-300 hover:-translate-y-1 cursor-pointer hover:scale-105">

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                                    <i class="<?php echo $icon; ?> text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-md"><?php echo htmlspecialchars($expense['category']); ?></h4>
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-xl text-xs font-medium">
                                        <?php echo $paymentLabel; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600 leading-none">
                                    <?php echo number_format($expense['amount'], 0, ',', '.'); ?>
                                </p>
                                <p class="text-xs text-gray-700 mt-1">VNĐ</p>
                            </div>
                        </div>

                        <!-- Thời gian -->
                        <div class="flex items-center justify-between text-sm text-gray-500 border-t border-gray-300 pt-2">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    <?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-clock text-gray-400"></i>
                                    <?php echo date('H:i', strtotime($expense['created_at'])); ?>
                                </span>
                            </div>
                            <form action="expenses.php" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa chi tiêu này?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="expense_id" value="<?php echo $expense['expense_id']; ?>">
                                <button type="submit" class="w-6 h-6 bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 rounded flex items-center justify-center transition-all duration-200 group cursor-pointer">
                                    <i class="fas fa-trash text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Mô tả -->
                        <?php if (!empty($expense['description'])): ?>
                            <div class="mb-2">
                                <p class="text-gray-600 text-xs leading-relaxed line-clamp-2 hover:line-clamp-none transition-all duration-200">
                                    <?php echo htmlspecialchars($expense['description']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<script>
    function addExpense() {
        const modal = document.getElementById('add-expense-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        //Lay ngay hien tai
        document.querySelector('input[name="expense_date"]').value = new Date().toISOString().split('T')[0];

    }

    function closeAddExpenseModal() {
        const modal = document.getElementById('add-expense-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.querySelector('form').reset();
    }

    document.querySelector('#add-expense-modal > div').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddExpenseModal();
        }
    });

    // Format tiền
    document.querySelector('input[name="amount"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    document.addEventListener('DOMContentLoaded', function() {
        const monthFilter = document.querySelector('input[name="month"]');
        if (!monthFilter.value) {
            monthFilter.value = new Date().toISOString().slice(0, 7);
        }
    });
</script>