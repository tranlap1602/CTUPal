<?php

/**
 * File: notes.php
 * Mục đích: Trang quản lý ghi chú học tập và cá nhân
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Ghi chú, to-do list, nhắc nhở học tập với Tailwind CSS
 */

// Thiết lập biến cho header
$page_title = 'Ghi chú';
$current_page = 'notes.php';

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
    <!-- Include notes view -->
    <?php include 'views/notes-view.php'; ?>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>