<?php

/**
 * File: expenses.php
 * Mục đích: Trang quản lý chi tiêu đơn giản
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Theo dõi chi tiêu cá nhân - phiên bản đơn giản cho niên luận cơ sở
 */

// Thiết lập biến cho header
$page_title = 'Quản lý Chi tiêu';
$current_page = 'expenses.php';

// Bắt đầu session
session_start();
require_once 'config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? $_SESSION['username'];

// API endpoint cho AJAX
if (isset($_GET['action']) && $_GET['action'] === 'filter') {
    header('Content-Type: application/json');

    try {
        $category_filter = $_GET['category'] ?? '';
        $month_filter = $_GET['month'] ?? date('Y-m');

        $where_conditions = ['user_id = :user_id'];
        $params = [':user_id' => $user_id];

        if ($category_filter) {
            $where_conditions[] = 'category = :category';
            $params[':category'] = $category_filter;
        }

        if ($month_filter) {
            $where_conditions[] = 'DATE_FORMAT(expense_date, "%Y-%m") = :month';
            $params[':month'] = $month_filter;
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Lấy danh sách chi tiêu
        $sql = "SELECT * FROM expenses WHERE $where_clause ORDER BY expense_date DESC LIMIT 50";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy thống kê
        $sql_monthly = "SELECT SUM(amount) as total_amount FROM expenses 
                        WHERE user_id = :user_id AND DATE_FORMAT(expense_date, '%Y-%m') = :month";
        $stmt = $pdo->prepare($sql_monthly);
        $stmt->execute([':user_id' => $user_id, ':month' => $month_filter]);
        $monthly_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql_today = "SELECT SUM(amount) as today_amount FROM expenses 
                      WHERE user_id = :user_id AND DATE(expense_date) = CURDATE()";
        $stmt = $pdo->prepare($sql_today);
        $stmt->execute([':user_id' => $user_id]);
        $today_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'expenses' => $expenses,
            'stats' => [
                'monthly_total' => $monthly_stats['total_amount'] ?? 0,
                'today_total' => $today_stats['today_amount'] ?? 0
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}

// Xử lý thêm chi tiêu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $category = trim($_POST['category']);
        $amount = floatval($_POST['amount']);
        $description = trim($_POST['description'] ?? '');
        $expense_date = $_POST['expense_date'];
        $payment_method = $_POST['payment_method'];

        if (empty($category) || $amount <= 0 || empty($expense_date)) {
            $error_message = 'Vui lòng điền đầy đủ thông tin bắt buộc';
        } else {
            $sql = "INSERT INTO expenses (user_id, category, amount, description, expense_date, payment_method) 
                    VALUES (:user_id, :category, :amount, :description, :expense_date, :payment_method)";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $user_id,
                ':category' => $category,
                ':amount' => $amount,
                ':description' => $description,
                ':expense_date' => $expense_date,
                ':payment_method' => $payment_method
            ]);

            if ($result) {
                header('Location: expenses.php?success=1');
                exit();
            } else {
                $error_message = 'Không thể thêm chi tiêu';
            }
        }
    } catch (Exception $e) {
        $error_message = 'Lỗi: ' . $e->getMessage();
    }
}

// Xử lý xóa chi tiêu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $expense_id = intval($_POST['expense_id']);

        $sql = "DELETE FROM expenses WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([':id' => $expense_id, ':user_id' => $user_id]);

        if ($result) {
            $success_message = 'Đã xóa chi tiêu thành công!';
        } else {
            $error_message = 'Không thể xóa chi tiêu';
        }
    } catch (Exception $e) {
        $error_message = 'Lỗi: ' . $e->getMessage();
    }
}

// Lấy thống kê đơn giản (cho lần load đầu tiên)
try {
    $month = $_GET['month'] ?? date('Y-m');

    // Thống kê tháng
    $sql_monthly = "SELECT SUM(amount) as total_amount FROM expenses 
                    WHERE user_id = :user_id AND DATE_FORMAT(expense_date, '%Y-%m') = :month";
    $stmt = $pdo->prepare($sql_monthly);
    $stmt->execute([':user_id' => $user_id, ':month' => $month]);
    $monthly_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Chi tiêu hôm nay
    $sql_today = "SELECT SUM(amount) as today_amount FROM expenses 
                  WHERE user_id = :user_id AND DATE(expense_date) = CURDATE()";
    $stmt = $pdo->prepare($sql_today);
    $stmt->execute([':user_id' => $user_id]);
    $today_stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'Lỗi thống kê: ' . $e->getMessage();
}

// Lấy danh sách chi tiêu (cho lần load đầu tiên)
try {
    $category_filter = $_GET['category'] ?? '';
    $month_filter = $_GET['month'] ?? date('Y-m');

    $where_conditions = ['user_id = :user_id'];
    $params = [':user_id' => $user_id];

    if ($category_filter) {
        $where_conditions[] = 'category = :category';
        $params[':category'] = $category_filter;
    }

    if ($month_filter) {
        $where_conditions[] = 'DATE_FORMAT(expense_date, "%Y-%m") = :month';
        $params[':month'] = $month_filter;
    }

    $where_clause = implode(' AND ', $where_conditions);

    $sql = "SELECT * FROM expenses WHERE $where_clause ORDER BY expense_date DESC LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'Lỗi lấy danh sách: ' . $e->getMessage();
    $expenses = [];
}

// Include header
include 'includes/header.php';
?>

<!-- Main content -->
<div class="bg-white rounded-lg shadow-md p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý Chi tiêu</h3>
            <p class="text-gray-600">Theo dõi chi tiêu cá nhân</p>
        </div>
        <button onclick="showAddExpenseForm()"
            class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm chi tiêu</span>
        </button>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>Thêm chi tiêu thành công!
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8" id="stats-container">
        <!-- Tổng chi tiêu tháng -->
        <div class="bg-gradient-to-br from-red-400 to-red-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Tổng chi tiêu tháng này</p>
                    <p class="text-2xl font-bold" id="monthly-total"><?php echo number_format($monthly_stats['total_amount'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-red-200"></i>
            </div>
        </div>

        <!-- Chi tiêu hôm nay -->
        <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Chi tiêu hôm nay</p>
                    <p class="text-2xl font-bold" id="today-total"><?php echo number_format($today_stats['today_amount'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                </div>
                <i class="fas fa-calendar-day text-3xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Form thêm chi tiêu -->
    <div id="add-expense-form" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-8 shadow-lg mb-8">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle mr-3 text-blue-500"></i>
                Thêm chi tiêu mới
            </h4>
            <button onclick="hideAddExpenseForm()" class="text-gray-500 hover:text-gray-700 p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add">

            <!-- Danh mục và số tiền -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>Danh mục *
                    </label>
                    <select name="category" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2"></i>Phương thức *
                    </label>
                    <select name="payment_method" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="cash">Tiền mặt</option>
                        <option value="card">Thẻ ngân hàng</option>
                    </select>
                </div>
            </div>

            <!-- Ghi chú -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Ghi chú
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-vertical"
                    placeholder="Ghi chú thêm về chi tiêu này..."></textarea>
            </div>

            <!-- Nút submit -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideAddExpenseForm()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Thêm chi tiêu</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-lg mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
                </label>
                <select id="category-filter" onchange="filterExpenses()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2"></i>Lọc theo tháng
                </label>
                <input type="month" id="month-filter" onchange="filterExpenses()" value="<?php echo $month_filter; ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" class="hidden text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-500">Đang tải...</p>
    </div>

    <!-- Danh sách chi tiêu -->
    <div class="space-y-4" id="expenses-list">
        <?php if (empty($expenses)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-wallet text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold mb-2">Chưa có chi tiêu nào</h3>
                <p class="mb-4">Hãy thêm chi tiêu đầu tiên của bạn!</p>
                <button onclick="showAddExpenseForm()"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                    Thêm chi tiêu ngay
                </button>
            </div>
        <?php else: ?>
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

                $categoryColors = [
                    'Ăn uống' => 'orange',
                    'Di chuyển' => 'blue',
                    'Học tập' => 'purple',
                    'Giải trí' => 'pink',
                    'Mua sắm' => 'green',
                    'Y tế' => 'red',
                    'Khác' => 'gray'
                ];

                $paymentLabels = [
                    'cash' => 'Tiền mặt',
                    'card' => 'Thẻ ngân hàng'
                ];

                $icon = $categoryIcons[$expense['category']] ?? 'fas fa-receipt';
                $color = $categoryColors[$expense['category']] ?? 'gray';
                $paymentLabel = $paymentLabels[$expense['payment_method']] ?? 'Tiền mặt';
                ?>

                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <!-- Thông tin chi tiêu -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-<?php echo $color; ?>-100 text-<?php echo $color; ?>-600 rounded-full flex items-center justify-center">
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($expense['category']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo $paymentLabel; ?> • <?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($expense['description'])): ?>
                                <p class="text-gray-600 text-sm ml-13"><?php echo htmlspecialchars($expense['description']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Số tiền và nút xóa -->
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-2xl font-bold text-red-600"><?php echo number_format($expense['amount'], 0, ',', '.'); ?></p>
                                <p class="text-sm text-gray-500">VNĐ</p>
                            </div>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa chi tiêu này?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-all duration-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

    // Format số tiền chỉ cho phép số
    document.querySelector('input[name="amount"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Lọc chi tiêu bằng AJAX
    async function filterExpenses() {
        const categoryFilter = document.getElementById('category-filter').value;
        const monthFilter = document.getElementById('month-filter').value;
        const loadingIndicator = document.getElementById('loading-indicator');
        const expensesList = document.getElementById('expenses-list');

        // Hiển thị loading
        loadingIndicator.classList.remove('hidden');
        expensesList.style.opacity = '0.5';

        try {
            const params = new URLSearchParams({
                action: 'filter',
                category: categoryFilter,
                month: monthFilter
            });

            const response = await fetch(`expenses.php?${params}`);
            const data = await response.json();

            if (data.success) {
                // Cập nhật thống kê
                document.getElementById('monthly-total').textContent =
                    new Intl.NumberFormat('vi-VN').format(data.stats.monthly_total) + ' VNĐ';
                document.getElementById('today-total').textContent =
                    new Intl.NumberFormat('vi-VN').format(data.stats.today_total) + ' VNĐ';

                // Cập nhật danh sách chi tiêu
                renderExpensesList(data.expenses);
            } else {
                console.error('Error:', data.error);
                showToast('Có lỗi xảy ra khi lọc dữ liệu', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Không thể kết nối đến server', 'error');
        } finally {
            // Ẩn loading
            loadingIndicator.classList.add('hidden');
            expensesList.style.opacity = '1';
        }
    }

    // Render danh sách chi tiêu
    function renderExpensesList(expenses) {
        const expensesList = document.getElementById('expenses-list');

        if (expenses.length === 0) {
            expensesList.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-wallet text-6xl mb-4 text-gray-300"></i>
                    <h3 class="text-xl font-semibold mb-2">Không tìm thấy chi tiêu nào</h3>
                    <p class="mb-4">Thử thay đổi bộ lọc hoặc thêm chi tiêu mới!</p>
                    <button onclick="showAddExpenseForm()" 
                        class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        Thêm chi tiêu ngay
                    </button>
                </div>
            `;
            return;
        }

        const categoryIcons = {
            'Ăn uống': 'fas fa-utensils',
            'Di chuyển': 'fas fa-bus',
            'Học tập': 'fas fa-book',
            'Giải trí': 'fas fa-film',
            'Mua sắm': 'fas fa-shopping-bag',
            'Y tế': 'fas fa-heartbeat',
            'Khác': 'fas fa-receipt'
        };

        const categoryColors = {
            'Ăn uống': 'orange',
            'Di chuyển': 'blue',
            'Học tập': 'purple',
            'Giải trí': 'pink',
            'Mua sắm': 'green',
            'Y tế': 'red',
            'Khác': 'gray'
        };

        const paymentLabels = {
            'cash': 'Tiền mặt',
            'card': 'Thẻ ngân hàng'
        };

        const expensesHtml = expenses.map(expense => {
            const icon = categoryIcons[expense.category] || 'fas fa-receipt';
            const color = categoryColors[expense.category] || 'gray';
            const paymentLabel = paymentLabels[expense.payment_method] || 'Tiền mặt';
            const expenseDate = new Date(expense.expense_date).toLocaleDateString('vi-VN');

            return `
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <!-- Thông tin chi tiêu -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-${color}-100 text-${color}-600 rounded-full flex items-center justify-center">
                                    <i class="${icon}"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">${expense.category}</h4>
                                    <p class="text-sm text-gray-500">${paymentLabel} • ${expenseDate}</p>
                                </div>
                            </div>
                            ${expense.description ? `<p class="text-gray-600 text-sm ml-13">${expense.description}</p>` : ''}
                        </div>

                        <!-- Số tiền và nút xóa -->
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-2xl font-bold text-red-600">${new Intl.NumberFormat('vi-VN').format(expense.amount)}</p>
                                <p class="text-sm text-gray-500">VNĐ</p>
                            </div>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa chi tiêu này?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="expense_id" value="${expense.id}">
                                <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-all duration-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        expensesList.innerHTML = expensesHtml;
    }

    // Xóa bộ lọc
    function clearFilters() {
        document.getElementById('category-filter').value = '';
        document.getElementById('month-filter').value = new Date().toISOString().slice(0, 7);
        filterExpenses();
    }

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

    // Khởi tạo
    document.addEventListener('DOMContentLoaded', function() {
        // Set tháng hiện tại nếu chưa có
        const monthFilter = document.getElementById('month-filter');
        if (!monthFilter.value) {
            monthFilter.value = new Date().toISOString().slice(0, 7);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>