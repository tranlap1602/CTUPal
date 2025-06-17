<?php

/**
 * File: expenses.php
 * Mục đích: Trang quản lý chi tiêu cá nhân sinh viên
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Theo dõi thu chi, ngân sách, thống kê chi tiêu với Tailwind CSS
 */

// Thiết lập biến cho header
$page_title = 'Chi tiêu';
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

// Include header
include 'includes/header.php';
?>

<!-- Main content -->
<div class="bg-white rounded-2xl shadow-lg p-8">
    <!-- Include expenses view -->
    <?php include 'views/expenses-view.php'; ?>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>