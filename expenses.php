<?php

/**
 * File: expenses.php
 * Mục đích: Trang quản lý chi tiêu với cấu trúc bảng mới
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Theo dõi thu chi với cấu trúc bảng đơn giản
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

// Xử lý thêm chi tiêu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $category = trim($_POST['category']);
        $amount = floatval($_POST['amount']);
        $description = trim($_POST['note'] ?? '');
        $expense_date = $_POST['date'] . ' ' . $_POST['time'];
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
                $success_message = 'Thêm chi tiêu thành công!';
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

// Lấy thống kê
try {
    $month = $_GET['month'] ?? date('Y-m');

    // Thống kê tháng
    $sql_monthly = "SELECT 
                        SUM(amount) as total_amount,
                        COUNT(*) as total_transactions,
                        AVG(amount) as avg_amount
                    FROM expenses 
                    WHERE user_id = :user_id 
                    AND DATE_FORMAT(expense_date, '%Y-%m') = :month";

    $stmt = $pdo->prepare($sql_monthly);
    $stmt->execute([':user_id' => $user_id, ':month' => $month]);
    $monthly_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Chi tiêu hôm nay
    $sql_today = "SELECT 
                    SUM(amount) as today_amount,
                    COUNT(*) as today_count
                  FROM expenses 
                  WHERE user_id = :user_id 
                  AND DATE(expense_date) = CURDATE()";

    $stmt = $pdo->prepare($sql_today);
    $stmt->execute([':user_id' => $user_id]);
    $today_stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'Lỗi thống kê: ' . $e->getMessage();
}

// Lấy danh sách chi tiêu
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

    $sql = "SELECT * FROM expenses 
            WHERE $where_clause 
            ORDER BY expense_date DESC 
            LIMIT 50";

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
<div class="bg-white rounded-2xl shadow-lg p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Quản lý Chi tiêu</h3>
            <p class="text-gray-600">Theo dõi và kiểm soát chi tiêu cá nhân</p>
        </div>
        <button onclick="showAddExpenseForm()"
            class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-600 hover:to-pink-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Thêm chi tiêu</span>
        </button>
    </div>

    <!-- Thông báo -->
    <?php if (isset($success_message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Tổng chi tiêu tháng -->
        <div class="bg-gradient-to-br from-red-400 to-red-600 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Tổng chi tiêu tháng này</p>
                    <p class="text-2xl font-bold mt-1">
                        <?php echo number_format($monthly_stats['total_amount'] ?? 0, 0, ',', '.'); ?> VNĐ
                    </p>
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
                    <p class="text-2xl font-bold mt-1">
                        <?php echo number_format($today_stats['today_amount'] ?? 0, 0, ',', '.'); ?> VNĐ
                    </p>
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
                    <p class="text-2xl font-bold mt-1">
                        <?php echo number_format($monthly_stats['avg_amount'] ?? 0, 0, ',', '.'); ?> VNĐ
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-calculator text-2xl text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Form thêm chi tiêu (ẩn mặc định) -->
    <div id="add-expense-form" class="hidden bg-gradient-to-br from-red-50 to-pink-50 border border-red-200 rounded-2xl p-8 shadow-lg mb-8">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle mr-3 text-red-500"></i>
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
                    <label for="expense-category" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>Danh mục *
                    </label>
                    <select id="expense-category" name="category" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                        <option value="">Chọn danh mục</option>
                        <option value="Ăn uống">🍜 Ăn uống</option>
                        <option value="Di chuyển">🚌 Di chuyển</option>
                        <option value="Học tập">📚 Học tập</option>
                        <option value="Giải trí">🎬 Giải trí</option>
                        <option value="Mua sắm">🛒 Mua sắm</option>
                        <option value="Y tế">🏥 Y tế</option>
                        <option value="Khác">📝 Khác</option>
                    </select>
                </div>

                <div>
                    <label for="expense-amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill mr-2"></i>Số tiền (VNĐ) *
                    </label>
                    <input type="number" id="expense-amount" name="amount" min="0" step="1000" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                        placeholder="Nhập số tiền...">
                </div>
            </div>

            <!-- Ngày, giờ và phương thức thanh toán -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="expense-date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Ngày *
                    </label>
                    <input type="date" id="expense-date" name="date" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                </div>

                <div>
                    <label for="expense-time" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2"></i>Giờ *
                    </label>
                    <input type="time" id="expense-time" name="time" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                </div>

                <div>
                    <label for="expense-payment" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2"></i>Phương thức *
                    </label>
                    <select id="expense-payment" name="payment_method" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="card">💳 Thẻ ngân hàng</option>
                    </select>
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
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Lọc danh mục -->
            <div>
                <label for="filter-expense-category" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-2"></i>Lọc theo danh mục
                </label>
                <select id="filter-expense-category" name="category"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Tất cả danh mục</option>
                    <option value="Ăn uống" <?php echo ($category_filter === 'Ăn uống') ? 'selected' : ''; ?>>🍜 Ăn uống</option>
                    <option value="Di chuyển" <?php echo ($category_filter === 'Di chuyển') ? 'selected' : ''; ?>>🚌 Di chuyển</option>
                    <option value="Học tập" <?php echo ($category_filter === 'Học tập') ? 'selected' : ''; ?>>📚 Học tập</option>
                    <option value="Giải trí" <?php echo ($category_filter === 'Giải trí') ? 'selected' : ''; ?>>🎬 Giải trí</option>
                    <option value="Mua sắm" <?php echo ($category_filter === 'Mua sắm') ? 'selected' : ''; ?>>🛒 Mua sắm</option>
                    <option value="Y tế" <?php echo ($category_filter === 'Y tế') ? 'selected' : ''; ?>>🏥 Y tế</option>
                    <option value="Khác" <?php echo ($category_filter === 'Khác') ? 'selected' : ''; ?>>📝 Khác</option>
                </select>
            </div>

            <!-- Lọc tháng -->
            <div>
                <label for="filter-month" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2"></i>Lọc theo tháng
                </label>
                <input type="month" id="filter-month" name="month" value="<?php echo $month_filter; ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-all duration-200">
                    <i class="fas fa-search mr-2"></i>Lọc
                </button>
                <a href="expenses.php" class="ml-4 text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times mr-2"></i>Xóa bộ lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách chi tiêu -->
    <div class="space-y-4">
        <?php if (empty($expenses)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-wallet text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-semibold mb-2">Chưa có chi tiêu nào</h3>
                <p class="mb-4">Hãy thêm chi tiêu đầu tiên của bạn!</p>
                <button onclick="showAddExpenseForm()"
                    class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-600 hover:to-pink-700 transition-all duration-200">
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
                    'cash' => '💵 Tiền mặt',
                    'card' => '💳 Thẻ ngân hàng'
                ];

                $icon = $categoryIcons[$expense['category']] ?? 'fas fa-receipt';
                $color = $categoryColors[$expense['category']] ?? 'gray';
                $paymentLabel = $paymentLabels[$expense['payment_method']] ?? '💵 Tiền mặt';
                ?>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <!-- Thông tin chi tiêu -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-<?php echo $color; ?>-100 text-<?php echo $color; ?>-600 rounded-full flex items-center justify-center">
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($expense['category']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo $paymentLabel; ?> • <?php echo date('d/m/Y H:i', strtotime($expense['expense_date'])); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($expense['description'])): ?>
                                <p class="text-gray-600 text-sm ml-13"><?php echo htmlspecialchars($expense['description']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Số tiền và actions -->
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-2xl font-bold text-red-600"><?php echo number_format($expense['amount'], 0, ',', '.'); ?></p>
                                <p class="text-sm text-gray-500">VNĐ</p>
                            </div>
                            <div class="flex space-x-2">
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
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

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
        document.getElementById('expense-category').focus();

        // Set ngày và giờ hiện tại làm mặc định
        const now = new Date();
        document.getElementById('expense-date').value = now.toISOString().split('T')[0];
        document.getElementById('expense-time').value = now.toTimeString().slice(0, 5);
    }

    /**
     * Ẩn form thêm chi tiêu
     */
    function hideAddExpenseForm() {
        const form = document.getElementById('add-expense-form');
        form.classList.add('hidden');
        document.getElementById('add-expense-form').querySelector('form').reset();
    }

    /**
     * Format số tiền trong input
     */
    document.getElementById('expense-amount').addEventListener('input', function(e) {
        // Loại bỏ ký tự không phải số
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>