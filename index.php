<?php

/**
 * File: index.php
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
    $notes_count = fetchOne("SELECT COUNT(*) as total FROM notes WHERE user_id = ?", [$user_id])['total'] ?? 0;

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

<!-- Main content area với background gradient đẹp -->
<div class="min-h-screen bg-white rounded-lg shadow-md p-8">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Card thống kê ghi chú - INDIGO -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-400/10 to-indigo-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Ghi chú của bạn</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($notes_count); ?></p>
                        <p class="text-indigo-600 text-sm mt-1">Tổng số ghi chú</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-sticky-note text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card thống kê chi tiêu - ĐỎ -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
            <div class="absolute inset-0 bg-gradient-to-br from-red-400/10 to-red-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Chi tiêu tháng này</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($expenses_total); ?>đ</p>
                        <p class="text-red-600 text-sm mt-1">Tổng chi tiêu</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-wallet text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card thống kê tài liệu - XANH LÁ -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
            <div class="absolute inset-0 bg-gradient-to-br from-green-400/10 to-green-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Tài liệu đã lưu</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($documents_count); ?></p>
                        <p class="text-green-600 text-sm mt-1">Tổng số tài liệu</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-folder text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards chức năng chính với thiết kế mới -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">

        <!-- Card Lịch học - XANH DƯƠNG -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-700/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-week text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Lịch học</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Xem lịch học và sự kiện quan trọng</p>
                <a href="calendar.php" class="inline-flex items-center justify-center w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem lịch
                </a>
            </div>
        </div>

        <!-- Card Tài liệu - XANH LÁ -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-green-700/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-file-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Tài liệu</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Lưu trữ và quản lý tài liệu học tập</p>
                <a href="documents.php" class="inline-flex items-center justify-center w-full bg-gradient-to-r from-green-500 to-green-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-green-600 hover:to-green-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Chi tiêu - ĐỎ -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-red-700/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-wallet text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Chi tiêu</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Theo dõi chi tiêu cá nhân hàng ngày</p>
                <a href="expenses.php" class="inline-flex items-center justify-center w-full bg-gradient-to-r from-red-500 to-red-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Ghi chú - INDIGO -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-indigo-700/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-sticky-note text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Ghi chú</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Ghi chú học tập và cá nhân</p>
                <a href="notes.php" class="inline-flex items-center justify-center w-full bg-gradient-to-r from-indigo-500 to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-600 hover:to-indigo-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>

        <!-- Card Thông tin cá nhân - XANH DƯƠNG -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-700/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-6 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-circle text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Thông tin cá nhân</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Cập nhật thông tin</p>
                <a href="profile.php" class="inline-flex items-center justify-center w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>

</div>

<?php
// Include footer
include 'includes/footer.php';
?>