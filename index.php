<?php
$page_title = 'Trang chủ';
$current_page = 'index.php';

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra quyền
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: admin/index.php');
    exit();
}

// lấy thông tin user hiện tại
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Lấy thống kê
try {
    $notes_count = fetchOne("SELECT COUNT(*) as total FROM notes WHERE user_id = ?", [$user_id])['total'] ?? 0;
    $current_month = date('Y-m');
    $expenses_total = fetchOne("SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?", [$user_id, $current_month])['total'] ?? 0;
    $documents_count = fetchOne("SELECT COUNT(*) as total FROM documents WHERE user_id = ?", [$user_id])['total'] ?? 0;
} catch (Exception $e) {
    $notes_count = 0;
    $expenses_total = 0;
    $documents_count = 0;
}

include 'includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Thống kê ghi chú -->
        <div class="bg-gradient-to-br from-indigo-200 to-indigo-50 rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Ghi chú của bạn</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($notes_count); ?></p>
                        <p class="text-indigo-600 text-sm mt-1">Tổng số ghi chú</p>
                    </div>
                    <div class="w-16 h-16 bg-indigo-500 rounded-xl flex items-center justify-center shadow">
                        <i class="fas fa-sticky-note text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê chi tiêu-->
        <div class="bg-gradient-to-br from-red-200 to-red-50 rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Chi tiêu tháng này</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($expenses_total); ?>đ</p>
                        <p class="text-red-600 text-sm mt-1">Tổng chi tiêu</p>
                    </div>
                    <div class="w-16 h-16 bg-red-500 rounded-xl flex items-center justify-center shadow">
                        <i class="fas fa-wallet text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê tài liệu -->
        <div class="bg-gradient-to-br from-green-200 to-green-50 rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Tài liệu đã lưu</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($documents_count); ?></p>
                        <p class="text-green-600 text-sm mt-1">Tổng số tài liệu</p>
                    </div>
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center shadow">
                        <i class="fas fa-folder text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chức năng chính -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <!-- Lịch học -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6 text-center">
                <div class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow">
                    <i class="fas fa-calendar-week text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Lịch học</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Xem lịch học và sự kiện quan trọng</p>
                <a href="calendar.php" class="inline-flex items-center justify-center w-full bg-blue-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-600 transition-all duration-300 shadow">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem lịch
                </a>
            </div>
        </div>
        <!-- Tài liệu -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6 text-center">
                <div class="w-20 h-20 bg-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow">
                    <i class="fas fa-file-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Tài liệu</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Lưu trữ và quản lý tài liệu học tập</p>
                <a href="documents.php" class="inline-flex items-center justify-center w-full bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-600 transition-all duration-300 shadow">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
        <!-- Chi tiêu -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6 text-center">
                <div class="w-20 h-20 bg-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow">
                    <i class="fas fa-wallet text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Chi tiêu</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Theo dõi chi tiêu cá nhân hàng ngày</p>
                <a href="expenses.php" class="inline-flex items-center justify-center w-full bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition-all duration-300 shadow">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
        <!-- Ghi chú -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6 text-center">
                <div class="w-20 h-20 bg-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow">
                    <i class="fas fa-sticky-note text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Ghi chú</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Ghi chú học tập và cá nhân</p>
                <a href="notes.php" class="inline-flex items-center justify-center w-full bg-indigo-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-600 transition-all duration-300 shadow">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
        <!-- Thông tin cá nhân -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6 text-center">
                <div class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow">
                    <i class="fas fa-user-circle text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Thông tin cá nhân</h3>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">Cập nhật thông tin</p>
                <a href="profile.php" class="inline-flex items-center justify-center w-full bg-blue-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-600 transition-all duration-300 shadow">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>

</div>

<?php
include 'includes/footer.php';
?>