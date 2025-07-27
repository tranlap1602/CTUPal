<?php
$page_title = 'Quản Lý Chi Tiêu';
$current_page = 'expenses.php';

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $errors = [];
    $success = '';

    try {
        switch ($action) {
            case 'add':
                $category = sanitizeInput($_POST['category']);
                $amount = floatval($_POST['amount']);
                $description = sanitizeInput($_POST['description'] ?? '');
                $expense_date = $_POST['expense_date'];
                $payment_method = $_POST['payment_method'];

                $sql = "INSERT INTO expenses (user_id, category, amount, description, expense_date, payment_method) VALUES (?, ?, ?, ?, ?, ?)";
                $params = [$user_id, $category, $amount, $description, $expense_date, $payment_method];

                $expense_id = insertAndGetId($sql, $params);

                if ($expense_id) {
                    $success = 'Thêm chi tiêu thành công';
                } else {
                    $errors['database'] = 'Không thể thêm chi tiêu';
                }
                break;

            case 'delete':
                $expense_id = intval($_POST['expense_id'] ?? 0);
                if ($expense_id > 0) {
                    $sql = "DELETE FROM expenses WHERE expense_id = ? AND user_id = ?";
                    if (executeQuery($sql, [$expense_id, $user_id])) {
                        $success = 'Xóa chi tiêu thành công';
                    } else {
                        $errors['database'] = 'Không thể xóa chi tiêu';
                    }
                } else {
                    $errors['id'] = 'ID chi tiêu không hợp lệ';
                }
                break;
        }
    } catch (Exception $e) {
        $errors['server'] = 'Lỗi server: ' . $e->getMessage();
    }

    // Redirect với messages
    $params = [];
    if (!empty($_GET['category'])) $params['category'] = $_GET['category'];
    if (!empty($_GET['month'])) $params['month'] = $_GET['month'];
    if (!empty($_GET['payment'])) $params['payment'] = $_GET['payment'];

    if (!empty($success)) {
        $params['message'] = $success;
        $params['type'] = 'success';
    } elseif (!empty($errors)) {
        $params['message'] = reset($errors);
        $params['type'] = 'error';
    }

    $redirect_url = 'expenses.php';
    if (!empty($params)) {
        $redirect_url .= '?' . http_build_query($params);
    }

    header('Location: ' . $redirect_url);
    exit();
}

// Lấy thông tin bộ lọc từ GET
$category_filter = $_GET['category'] ?? '';
$month_filter = $_GET['month'] ?? date('Y-m');
$payment_filter = $_GET['payment'] ?? '';

// Lấy thống kê
try {
    // Thống kê tháng
    $sql_monthly = "SELECT SUM(amount) as total_amount FROM expenses 
                    WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?";
    $stmt = $pdo->prepare($sql_monthly);
    $stmt->execute([$user_id, $month_filter]);
    $monthly_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Chi tiêu hôm nay
    $sql_today = "SELECT SUM(amount) as today_amount FROM expenses 
                  WHERE user_id = ? AND DATE(expense_date) = CURDATE()";
    $stmt = $pdo->prepare($sql_today);
    $stmt->execute([$user_id]);
    $today_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Thống kê tổng chi tiêu theo từng danh mục trong tháng
    $sql_category = "SELECT category, SUM(amount) as total FROM expenses 
                     WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?
                     GROUP BY category";
    $stmt = $pdo->prepare($sql_category);
    $stmt->execute([$user_id, $month_filter]);
    $category_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Thống kê tổng chi tiêu theo từng danh mục trong ngày hôm nay
    $sql_category_today = "SELECT category, SUM(amount) as total FROM expenses 
                          WHERE user_id = ? AND DATE(expense_date) = CURDATE()
                          GROUP BY category";
    $stmt = $pdo->prepare($sql_category_today);
    $stmt->execute([$user_id]);
    $category_today_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// Lấy danh sách chi tiêu với bộ lọc
$where_conditions = ['user_id = ?'];
$params = [$user_id];

if ($category_filter) {
    $where_conditions[] = 'category = ?';
    $params[] = $category_filter;
}

if ($month_filter) {
    $where_conditions[] = 'DATE_FORMAT(expense_date, "%Y-%m") = ?';
    $params[] = $month_filter;
}

if ($payment_filter) {
    $where_conditions[] = 'payment_method = ?';
    $params[] = $payment_filter;
}

$where_clause = implode(' AND ', $where_conditions);

$sql = "SELECT * FROM expenses WHERE $where_clause ORDER BY expense_date DESC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="bg-white rounded-2xl shadow-lg p-8">
    <?php include 'views/expenses-view.php'; ?>
</div>
<?php

include 'includes/footer.php';
?>