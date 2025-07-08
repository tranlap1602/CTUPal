<?php

/**
 * File: index.php
 * Mục đích: Trang chủ chính của hệ thống StudentManager
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Dashboard hiển thị tổng quan và điều hướng đến các chức năng chính
 */

// Thiết lập biến cho header
$page_title = 'Trang chủ';
$current_page = 'index.php';

// Bắt đầu session và kiểm tra đăng nhập
session_start();
require_once 'config/db.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin user hiện tại từ session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User';

// Lấy thống kê nhanh cho dashboard
try {
    // Đếm số lượng ghi chú chưa hoàn thành
    $notes_count = fetchOne("SELECT COUNT(*) as total FROM notes WHERE user_id = ? AND is_completed = 0", [$user_id])['total'] ?? 0;

    // Tính tổng chi tiêu tháng này
    $current_month = date('Y-m');
    $expenses_total = fetchOne("SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?", [$user_id, $current_month])['total'] ?? 0;

    // Đếm số lượng tài liệu đã upload
    $documents_count = fetchOne("SELECT COUNT(*) as total FROM documents WHERE user_id = ?", [$user_id])['total'] ?? 0;
} catch (Exception $e) {
    // Nếu có lỗi database, đặt giá trị mặc định
    $notes_count = 0;
    $expenses_total = 0;
    $documents_count = 0;
}

// Include header
include 'includes/header.php';
?>

<!-- Main content area -->
<div class="bg-white rounded-lg shadow-md p-8">
    <!-- Thống kê nhanh với grid responsive -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Card thống kê ghi chú -->
        <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Ghi chú cần làm</p>
                    <p class="text-2xl font-bold"><?php echo number_format($notes_count); ?></p>
                </div>
                <i class="fas fa-tasks text-3xl text-green-200"></i>
            </div>
        </div>

        <!-- Card thống kê chi tiêu -->
        <div class="bg-gradient-to-br from-red-400 to-red-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Chi tiêu tháng này</p>
                    <p class="text-2xl font-bold"><?php echo number_format($expenses_total); ?>đ</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-red-200"></i>
            </div>
        </div>

        <!-- Card thống kê tài liệu -->
        <div class="bg-gradient-to-br from-purple-400 to-purple-600 text-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Tài liệu đã lưu</p>
                    <p class="text-2xl font-bold"><?php echo number_format($documents_count); ?></p>
                </div>
                <i class="fas fa-folder text-3xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Cards chức năng chính với grid responsive -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

        <!-- Card Thời khóa biểu -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="text-center">
                <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Thời khóa biểu</h3>
                <p class="text-blue-100 mb-4">Quản lý lịch học của bạn</p>
                <a href="timetable.php"
                    class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition-colors duration-200 inline-block">
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Tài liệu -->
        <div class="bg-gradient-to-br from-green-500 to-green-700 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="text-center">
                <i class="fas fa-file-alt text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Tài liệu</h3>
                <p class="text-green-100 mb-4">Lưu trữ và quản lý tài liệu học tập</p>
                <a href="documents.php"
                    class="bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition-colors duration-200 inline-block">
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Chi tiêu -->
        <div class="bg-gradient-to-br from-red-500 to-red-700 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="text-center">
                <i class="fas fa-wallet text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Chi tiêu</h3>
                <p class="text-red-100 mb-4">Theo dõi chi tiêu cá nhân</p>
                <a href="expenses.php"
                    class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors duration-200 inline-block">
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Ghi chú -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-700 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="text-center">
                <i class="fas fa-sticky-note text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Ghi chú</h3>
                <p class="text-purple-100 mb-4">Ghi chú học tập và cá nhân</p>
                <a href="notes.php"
                    class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition-colors duration-200 inline-block">
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Thông tin cá nhân -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="text-center">
                <i class="fas fa-user-circle text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Thông tin cá nhân</h3>
                <p class="text-indigo-100 mb-4">Cập nhật thông tin tài khoản</p>
                <a href="profile.php"
                    class="bg-white text-indigo-600 px-4 py-2 rounded-lg font-semibold hover:bg-indigo-50 transition-colors duration-200 inline-block">
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>

    <!-- Footer thông tin thêm -->
    <div class="mt-12 text-center text-gray-500">
        <p class="mb-2">
            <i class="fas fa-heart text-red-400"></i>
            Chúc bạn có một ngày học tập hiệu quả!
        </p>
                <p class="text-sm">StudentManager v1.0 - Hệ thống quản lý sinh viên</p>
    </div>
</div>

    <?php
    // Include footer
    include 'includes/footer.php';
    ?>