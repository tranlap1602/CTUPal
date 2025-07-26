<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$page_title = $page_title ?? 'StudentManager';
$current_page = $current_page ?? '';
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? null;
$mssv = $_SESSION['user_mssv'] ?? null;
$is_logged_in = isset($_SESSION['user_id']);

// Lấy tên hiển thị
$display_name = $user_name ?: 'User';
$display_first_name = explode(' ', trim($display_name))[0];

// navigation items
$nav_items = [
    'index.php' => ['icon' => 'fas fa-home', 'text' => 'Trang chủ', 'require_login' => true],
    'calendar.php' => ['icon' => 'fas fa-calendar-week', 'text' => 'Lịch học', 'require_login' => true],
    'documents.php' => ['icon' => 'fas fa-file-alt', 'text' => 'Tài liệu', 'require_login' => true],
    'expenses.php' => ['icon' => 'fas fa-wallet', 'text' => 'Chi tiêu', 'require_login' => true],
    'notes.php' => ['icon' => 'fas fa-sticky-note', 'text' => 'Ghi chú', 'require_login' => true]
];

function isActivePage($page, $current_page)
{
    return basename($_SERVER['PHP_SELF']) === $page || $current_page === $page;
}

// Hàm tạo breadcrumb
function generateBreadcrumb($current_page)
{
    $breadcrumbs = [
        'index.php' => 'Trang chủ',
        'calendar.php' => 'Lịch học',
        'documents.php' => 'Tài liệu',
        'expenses.php' => 'Chi tiêu',
        'notes.php' => 'Ghi chú',
        'profile.php' => 'Thông tin cá nhân',
        'login.php' => 'Đăng nhập'
    ];

    $current_file = basename($_SERVER['PHP_SELF']);
    return $breadcrumbs[$current_file] ?? 'Trang không xác định';
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - StudentManager</title>
    <link rel="stylesheet" href="src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="assets/icon/logo.svg">
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
        <header class="bg-gradient-to-r from-blue-500 to-blue-600">
            <div class="max-w-7xl mx-auto">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                                <svg class="logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z" />
                                </svg>
                            </div>

                            <div class="text-white">
                                <h1 class="text-xl font-bold">StudentManager</h1>
                                <p class="text-blue-100 text-xs hidden sm:block">Hệ thống quản lý sinh viên</p>
                            </div>
                        </div>
                        <!-- user -->
                        <div class="flex items-center space-x-4">
                            <!-- User dropdown -->
                            <div class="relative">
                                <button id="user-dropdown-btn" onclick="toggleUserDropdown()"
                                    class="flex items-center space-x-3 text-white hover:bg-white/10 px-3 py-2 rounded-lg transition-all duration-200">
                                    <!-- Avatar -->
                                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <!-- Thông tin user -->
                                    <div class="text-left hidden sm:block">
                                        <p class="font-medium text-sm"><?php echo htmlspecialchars($display_name); ?></p>
                                        <p class="text-blue-200 text-xs"><?php echo htmlspecialchars($mssv); ?></p>
                                    </div>
                                    <!-- Dropdown arrow -->
                                    <i id="dropdown-arrow" class="fas fa-chevron-down text-sm transition-transform"></i>
                                </button>
                                <!-- Dropdown menu -->
                                <div id="user-dropdown" class="dropdown-menu absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden">
                                    <div class="py-2">
                                        <!-- User info header -->
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($display_name); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                                        </div>
                                        <!-- Menu items -->
                                        <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-user-circle w-4 mr-3 text-gray-400"></i>
                                            Thông tin cá nhân
                                        </a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-question-circle w-4 mr-3 text-gray-400"></i>
                                            Trợ giúp
                                        </a>
                                        <a href="logout.php" class="border-t border-gray-100 flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
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
        <!-- Navigation bar -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto">
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- Desktop navigation -->
                    <div class="hidden md:flex space-x-8 h-14">
                        <?php foreach ($nav_items as $page => $item): ?>
                            <?php if ($item['require_login']): ?>
                                <?php $is_active = isActivePage($page, $current_page); ?>
                                <a href="<?php echo $page; ?>"
                                    class="inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition-all duration-200 <?php echo $is_active
                                                                                                                                                ? 'border-blue-500 bg-blue-50'
                                                                                                                                                : 'border-transparent hover:border-gray-300'; ?>"
                                    style="<?php echo $is_active ? 'color: #2563eb;' : 'color: #6b7280;'; ?>"
                                    onmouseover="<?php echo !$is_active ? 'this.style.color=\'#374151\';' : ''; ?>"
                                    onmouseout="<?php echo !$is_active ? 'this.style.color=\'#6b7280\';' : ''; ?>">
                                    <i class="<?php echo $item['icon']; ?> mr-2"></i>
                                    <?php echo $item['text']; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <!-- Mobile navigation -->
                    <div class="md:hidden">
                        <div class="flex justify-between items-center h-14">
                            <button id="mobile-menu-btn" onclick="toggleMobileMenu()"
                                class="text-gray-500 hover:text-gray-700 p-2">
                                <i id="mobile-menu-icon" class="fas fa-bars text-lg"></i>
                            </button>

                            <span class="font-medium text-gray-800">
                                <?php echo generateBreadcrumb($current_page); ?>
                            </span>

                            <div class="w-8"></div>
                        </div>

                        <!-- Mobile menu items -->
                        <div id="mobile-menu" class="hidden border-t border-gray-200 bg-gray-50">
                            <div class="py-2 space-y-1">
                                <?php foreach ($nav_items as $page => $item): ?>
                                    <?php if ($item['require_login']): ?>
                                        <?php $is_active = isActivePage($page, $current_page); ?>
                                        <a href="<?php echo $page; ?>"
                                            class="block pl-3 pr-4 py-3 border-l-4 text-base font-medium transition-colors <?php echo $is_active
                                                                                                                                ? 'bg-blue-50 border-blue-500'
                                                                                                                                : 'hover:bg-gray-50'; ?>"
                                            style="<?php echo $is_active ? 'color: #1d4ed8;' : 'color: #4b5563;'; ?>"
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