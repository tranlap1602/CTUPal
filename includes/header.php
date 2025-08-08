<?php

$page_title = $page_title ?? 'CTUPal';
$current_page = $current_page ?? '';
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? null;
$mssv = $_SESSION['user_mssv'] ?? null;
$user_role = $_SESSION['user_role'] ?? 'user';
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = ($user_role === 'admin');

// Lấy tên hiển thị
$display_name = $user_name ?: ($is_admin ? 'Admin' : 'User');
$display_first_name = explode(' ', trim($display_name))[0];

// Điều hướng
if ($is_admin) {
    $nav_items = [
        'index.php' => ['icon' => 'fas fa-house', 'text' => 'Trang chủ', 'require_login' => true],
        'users.php' => ['icon' => 'fas fa-user-gear', 'text' => 'Quản lý tài khoản', 'require_login' => true]
    ];
    $header_color = 'from-blue-500 to-blue-600';
    $logo_icon = 'fas fa-graduation-cap';
    $logo_text = 'CTUPal';
    $logo_subtext = 'Quản lý hệ thống';
    $avatar_icon = 'fas fa-user-gear';
    $role_text = 'Administrator';
    $active_color = 'blue';
} else {
    $nav_items = [
        'index.php' => ['icon' => 'fas fa-house', 'text' => 'Trang chủ', 'require_login' => true],
        'calendar.php' => ['icon' => 'fas fa-calendar-week', 'text' => 'Lịch học', 'require_login' => true],
        'documents.php' => ['icon' => 'fas fa-file-alt', 'text' => 'Tài liệu', 'require_login' => true],
        'expenses.php' => ['icon' => 'fas fa-wallet', 'text' => 'Chi tiêu', 'require_login' => true],
        'notes.php' => ['icon' => 'fas fa-sticky-note', 'text' => 'Ghi chú', 'require_login' => true]
    ];
    $header_color = 'from-blue-500 to-blue-600';
    $logo_icon = 'fas fa-graduation-cap';
    $logo_text = 'CTUPal';
    $logo_subtext = 'Hệ thống quản lý sinh viên';
    $avatar_icon = 'fas fa-user';
    $role_text = $mssv ?? 'Student';
    $active_color = 'blue';
}

function isActivePage($page, $current_page)
{
    return basename($_SERVER['PHP_SELF']) === $page || $current_page === $page;
}

// breadcrumb
function Breadcrumb($current_page, $is_admin = false)
{
    if ($is_admin) {
        $breadcrumbs = [
            'index.php' => 'Trang chủ',
            'users.php' => 'Quản lý tài khoản'
        ];
    } else {
        $breadcrumbs = [
            'index.php' => 'Trang chủ',
            'calendar.php' => 'Lịch học',
            'documents.php' => 'Tài liệu',
            'expenses.php' => 'Chi tiêu',
            'notes.php' => 'Ghi chú',
            'profile.php' => 'Thông tin cá nhân',
            'login.php' => 'Đăng nhập'
        ];
    }

    $current_file = basename($_SERVER['PHP_SELF']);
    return $breadcrumbs[$current_file] ?? 'Trang không xác định';
}

// Xác định đường dẫn CSS và assets dựa trên vị trí file
$is_admin_page = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$base_path = $is_admin_page ? '../' : '';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo $is_admin ? 'Admin Dashboard' : 'CTUPal'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo $base_path; ?>assets/icon/logo.svg">
    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dropdown-menu {
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.2s ease-out;
        }

        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
        }

        .logo-svg {
            width: 30px;
            height: 30px;
            fill: #fff;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php if ($is_logged_in): ?>
        <header class="bg-gradient-to-r <?php echo $header_color; ?>">
            <div class="max-w-7xl mx-auto">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                                <i class="<?php echo $logo_icon; ?> text-white text-xl"></i>
                            </div>

                            <div class="text-white">
                                <h1 class="text-xl font-bold"><?php echo htmlspecialchars($logo_text); ?></h1>
                                <p class="text-xs hidden sm:block <?php echo $is_admin ? 'text-purple-100' : 'text-blue-100'; ?>"><?php echo htmlspecialchars($logo_subtext); ?></p>
                            </div>
                        </div>
                        <!-- dropdown + notifications -->
                        <div class="flex items-center space-x-4">
                            <!-- Thông báo -->
                            <div class="relative">
                                <button id="notif-btn" title="Thông báo"
                                    class="relative text-white hover:bg-white/10 p-2 rounded-lg transition-colors">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span id="notif-dot" class="absolute top-1 right-2 w-2 h-2 bg-red-500 rounded-full hidden"></span>
                                </button>
                                <!-- Dropdown thông báo -->
                                <div id="notif-dropdown" class="dropdown-menu absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden">
                                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                        <span class="font-semibold text-gray-700">Thông báo</span>
                                        <div class="space-x-4 text-yellow-600">
                                            <button id="notif-mark-all" title="Đánh dấu đã đọc" class="hover:text-yellow-900"><i class="fas fa-check"></i></button>
                                        </div>
                                    </div>
                                    <div id="notif-list" class="max-h-[90vh] min-h-[200px] overflow-y-auto p-3">
                                        <p class="text-center text-gray-500 py-6">Bạn không có thông báo</p>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <button id="user-dropdown-btn" onclick="toggleUserDropdown()"
                                    class="flex items-center space-x-3 text-white hover:bg-white/10 px-3 py-2 rounded-lg transition-all duration-200">
                                    <!-- Avatar -->
                                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="<?php echo $avatar_icon; ?> text-sm"></i>
                                    </div>
                                    <!-- Thông tin user -->
                                    <div class="text-left hidden sm:block">
                                        <p class="font-medium text-sm"><?php echo htmlspecialchars($display_name); ?></p>
                                        <p class="text-xs <?php echo $is_admin ? 'text-purple-200' : 'text-blue-200'; ?>"><?php echo htmlspecialchars($role_text); ?></p>
                                    </div>
                                    <!-- Dropdown arrow -->
                                    <i id="dropdown-arrow" class="fas fa-chevron-down text-sm transition-transform"></i>
                                </button>
                                <!-- Dropdown menu -->
                                <div id="user-dropdown" class="dropdown-menu absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden">
                                    <div class="py-2">
                                        <!-- thông tin user -->
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($display_name); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                                        </div>
                                        <!-- menu items -->
                                        <?php if (!$is_admin): ?>
                                            <a href="<?php echo $base_path; ?>profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-user-circle w-4 mr-3 text-gray-400"></i>
                                                Thông tin cá nhân
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo $base_path; ?>logout.php" class="border-t border-gray-100 flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-4 mr-3 text-red-400"></i>
                                            Đăng xuất
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- điều hướng -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto">
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- điều hướng desktop -->
                    <div class="hidden md:flex space-x-8 h-14">
                        <?php foreach ($nav_items as $page => $item): ?>
                            <?php if ($item['require_login']): ?>
                                <?php $is_active = isActivePage($page, $current_page); ?>
                                <a href="<?php echo $page; ?>"
                                    class="inline-flex items-center px-4 py-2 border-b-2 text-base font-medium transition-all duration-200 <?php echo $is_active
                                                                                                                                                ? "border-{$active_color}-500 bg-{$active_color}-50"
                                                                                                                                                : 'border-transparent hover:border-gray-300'; ?>"
                                    style="<?php echo $is_active ? ($is_admin ? 'color: #2563eb;' : 'color: #2563eb;') : 'color: #6b7280;'; ?>"
                                    onmouseover="<?php echo !$is_active ? 'this.style.color=\'#374151\';' : ''; ?>"
                                    onmouseout="<?php echo !$is_active ? 'this.style.color=\'#6b7280\';' : ''; ?>">
                                    <i class="<?php echo $item['icon']; ?> mr-2"></i>
                                    <?php echo $item['text']; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <!-- điều hướng mobile -->
                    <div class="md:hidden">
                        <div class="flex justify-between items-center h-14">
                            <button id="mobile-menu-btn" onclick="toggleMobileMenu()"
                                class="text-gray-500 hover:text-gray-700 p-2">
                                <i id="mobile-menu-icon" class="fas fa-bars text-lg"></i>
                            </button>

                            <span class="font-medium text-gray-800">
                                <?php echo Breadcrumb($current_page, $is_admin); ?>
                            </span>

                            <div class="w-8"></div>
                        </div>

                        <!-- menu mobile -->
                        <div id="mobile-menu" class="hidden border-t border-gray-200 bg-gray-50">
                            <div class="py-2 space-y-1">
                                <?php foreach ($nav_items as $page => $item): ?>
                                    <?php if ($item['require_login']): ?>
                                        <?php $is_active = isActivePage($page, $current_page); ?>
                                        <a href="<?php echo $page; ?>"
                                            class="block pl-3 pr-4 py-3 border-l-4 text-base font-medium transition-colors <?php echo $is_active
                                                                                                                                ? "bg-{$active_color}-50 border-{$active_color}-500"
                                                                                                                                : 'hover:bg-gray-50'; ?>"
                                            style="<?php echo $is_active ? ($is_admin ? 'color: #6d28d9;' : 'color: #1d4ed8;') : 'color: #4b5563;'; ?>"
                                            onmouseover="<?php echo !$is_active ? 'this.style.color=\'#111827\';' : ''; ?>"
                                            onmouseout="<?php echo !$is_active ? 'this.style.color=\'#4b5563\';' : ''; ?>">
                                            <i class="<?php echo $item['icon']; ?> w-5 mr-3"></i>
                                            <?php echo $item['text']; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

    <?php endif; ?>
    <div class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        <main>