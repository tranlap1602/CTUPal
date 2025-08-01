<?php
$page_title = 'Dashboard';
$current_page = 'index.php';

session_start();
require_once '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Lấy thống kê
$totalUsers = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user'")['count'];
$activeUsers = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user' AND is_active = 1")['count'];
$inactiveUsers = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user' AND is_active = 0")['count'];
$recentUsers = fetchAll("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5");

include '../includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <!-- Header trang -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
        <p class="text-gray-600">Quản lý hệ thống CTUPal</p>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Tổng số sinh viên -->
        <div class="bg-gradient-to-br from-blue-200 to-blue-50 rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Tổng số sinh viên</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($totalUsers); ?></p>
                        <p class="text-blue-600 text-sm mt-1">Tài khoản đã đăng ký</p>
                    </div>
                    <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center shadow">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tài khoản hoạt động -->
        <div class="bg-gradient-to-br from-green-200 to-green-50 rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Tài khoản hoạt động</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($activeUsers); ?></p>
                        <p class="text-green-600 text-sm mt-1">Đang sử dụng</p>
                    </div>
                    <div class="w-16 h-16 bg-green-500 rounded-xl flex items-center justify-center shadow">
                        <i class="fas fa-user-check text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tài khoản bị khóa -->
        <div class="bg-gradient-to-br from-red-200 to-red-50 rounded-2xl shadow-md hover:shadow-xl transform transition duration-300 hover:scale-105">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Tài khoản bị khóa</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($inactiveUsers); ?></p>
                        <p class="text-red-600 text-sm mt-1">Đã bị vô hiệu hóa</p>
                    </div>
                    <div class="w-16 h-16 bg-red-500 rounded-xl flex items-center justify-center shadow">
                        <i class="fas fa-user-times text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sinh viên mới đăng ký -->
    <div class="bg-white rounded-2xl shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Sinh viên mới đăng ký</h2>
            <p class="text-gray-600 text-sm">Danh sách 5 sinh viên đăng ký gần đây nhất</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MSSV</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đăng ký</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($recentUsers as $user): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['mssv']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($user['is_active']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Bị khóa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>