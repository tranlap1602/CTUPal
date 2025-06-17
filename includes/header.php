<?php

/**
 * File: includes/header.php
 * Mục đích: Header chung cho toàn bộ website StudentManager - Thiết kế theo ý tưởng CTU e-Learning
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Header với design hiện đại, logo bên trái, thông tin user dropdown bên phải
 * Sử dụng: include 'includes/header.php';
 */

// Đảm bảo session đã được start (nếu chưa)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Thiết lập các biến mặc định nếu chưa được định nghĩa
$page_title = $page_title ?? 'StudentManager';
$current_page = $current_page ?? '';
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$user_name = $_SESSION['user_name'] ?? null;
$is_logged_in = isset($_SESSION['user_id']);

// Lấy tên hiển thị - ưu tiên tên thật, fallback về username
$display_name = $user_name ?: $username ?: 'User';
$display_first_name = explode(' ', trim($display_name))[0]; // Lấy tên đầu tiên để thân thiện hơn

// Định nghĩa navigation items
$nav_items = [
    'index.php' => ['icon' => 'fas fa-home', 'text' => 'Trang chủ', 'require_login' => true],
    'timetable.php' => ['icon' => 'fas fa-calendar-alt', 'text' => 'Thời khóa biểu', 'require_login' => true],
    'documents.php' => ['icon' => 'fas fa-file-alt', 'text' => 'Tài liệu', 'require_login' => true],
    'expenses.php' => ['icon' => 'fas fa-wallet', 'text' => 'Chi tiêu', 'require_login' => true],
    'notes.php' => ['icon' => 'fas fa-sticky-note', 'text' => 'Ghi chú', 'require_login' => true]
];

// Hàm kiểm tra page hiện tại có active không
function isActivePage($page, $current_page)
{
    return basename($_SERVER['PHP_SELF']) === $page || $current_page === $page;
}

// Hàm tạo breadcrumb
function generateBreadcrumb($current_page)
{
    $breadcrumbs = [
        'index.php' => 'Trang chủ',
        'timetable.php' => 'Thời khóa biểu',
        'documents.php' => 'Tài liệu',
        'expenses.php' => 'Chi tiêu',
        'notes.php' => 'Ghi chú',
        'profile.php' => 'Thông tin cá nhân',
        'login.php' => 'Đăng nhập',
        'register.php' => 'Đăng ký'
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

    <!-- Tailwind CSS CDN - Framework CSS utility-first -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Cấu hình custom Tailwind cho toàn bộ website -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    // Custom colors cho StudentManager - Lấy ý tưởng từ CTU e-Learning
                    colors: {
                        'primary': {
                            50: '#e0f4ff',
                            100: '#b3e3ff',
                            200: '#80d1ff',
                            300: '#4dc0ff',
                            400: '#26b2ff',
                            500: '#00afef', // Blue chính
                            600: '#009edb',
                            700: '#008bc7',
                            800: '#0078b3',
                            900: '#005a91'
                        },
                        'secondary': {
                            50: '#f3e5f5',
                            500: '#9c27b0',
                            600: '#8e24aa',
                            700: '#7b1fa2'
                        }
                    },
                    // Custom animations
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        slideDown: {
                            '0%': {
                                transform: 'translateY(-10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        bounceIn: {
                            '0%': {
                                transform: 'scale(0.3)',
                                opacity: '0'
                            },
                            '50%': {
                                transform: 'scale(1.05)'
                            },
                            '70%': {
                                transform: 'scale(0.9)'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">

    <!-- Meta tags for SEO -->
    <meta name="description" content="Hệ thống quản lý sinh viên - StudentManager. Quản lý thời khóa biểu, tài liệu, chi tiêu và ghi chú học tập.">
    <meta name="keywords" content="sinh viên, quản lý, thời khóa biểu, tài liệu, học tập">
    <meta name="author" content="StudentManager Team">

    <!-- Custom CSS cho các hiệu ứng đặc biệt -->
    <style>
        /* Loading animation */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Smooth scroll behavior */
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

        /* Dropdown animation */
        .dropdown-menu {
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.2s ease-out;
        }

        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php if ($is_logged_in): ?>
        <!-- Header chính cho user đã đăng nhập - Thiết kế theo ý tưởng CTU e-Learning -->
        <header class="bg-gradient-to-r from-primary-500 to-primary-600 shadow-lg">
            <div class="max-w-7xl mx-auto">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">

                        <!-- Logo và tên hệ thống bên trái -->
                        <div class="flex items-center space-x-3">
                            <!-- Logo với gradient đẹp -->
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                                <i class="fas fa-graduation-cap text-white text-xl"></i>
                            </div>

                            <!-- Tên hệ thống -->
                            <div class="text-white">
                                <h1 class="text-xl font-bold">StudentManager</h1>
                                <p class="text-primary-100 text-xs hidden sm:block">Hệ thống quản lý sinh viên</p>
                            </div>
                        </div>

                        <!-- Thông tin user và controls bên phải -->
                        <div class="flex items-center space-x-4">
                            <!-- Thông báo (có thể thêm sau) -->
                            <button class="text-white/80 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full text-xs hidden"></span>
                            </button>

                            <!-- Tìm kiếm nhanh (có thể thêm sau) -->
                            <button class="text-white/80 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10 hidden md:block">
                                <i class="fas fa-search text-lg"></i>
                            </button>

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
                                        <p class="text-primary-200 text-xs">Sinh viên</p>
                                    </div>

                                    <!-- Dropdown arrow -->
                                    <i id="dropdown-arrow" class="fas fa-chevron-down text-sm transition-transform"></i>
                                </button>

                                <!-- Dropdown menu -->
                                <div id="user-dropdown" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden">
                                    <div class="py-2">
                                        <!-- User info header -->
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($display_name); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($username); ?></p>
                                        </div>

                                        <!-- Menu items -->
                                        <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-user-circle w-4 mr-3 text-gray-400"></i>
                                            Thông tin cá nhân
                                        </a>

                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-cog w-4 mr-3 text-gray-400"></i>
                                            Cài đặt
                                        </a>

                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-question-circle w-4 mr-3 text-gray-400"></i>
                                            Trợ giúp
                                        </a>

                                        <hr class="my-2">

                                        <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
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
                                    class="<?php echo $is_active
                                                ? 'border-primary-500 text-primary-600 bg-primary-50'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> 
                                      inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition-all duration-200">
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

                            <div class="w-8"></div> <!-- Spacer for centering -->
                        </div>

                        <!-- Mobile menu items -->
                        <div id="mobile-menu" class="hidden border-t border-gray-200 bg-gray-50">
                            <div class="py-2 space-y-1">
                                <?php foreach ($nav_items as $page => $item): ?>
                                    <?php if ($item['require_login']): ?>
                                        <?php $is_active = isActivePage($page, $current_page); ?>
                                        <a href="<?php echo $page; ?>"
                                            class="<?php echo $is_active
                                                        ? 'bg-primary-50 border-primary-500 text-primary-700'
                                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> 
                                              block pl-3 pr-4 py-3 border-l-4 text-base font-medium transition-colors">
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

    <?php else: ?>
        <!-- Header cho user chưa đăng nhập (login/register page) -->
        <header class="text-center py-8 animate-bounce-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full mb-4 shadow-lg">
                <i class="fas fa-graduation-cap text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-800 mb-2">StudentManager</h1>
            <p class="text-gray-600 text-lg">Hệ thống quản lý sinh viên hiện đại</p>

            <!-- Navigation cho guest -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="login.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) === 'login.php'
                                ? 'bg-primary-500 text-white'
                                : 'text-primary-600 hover:bg-primary-50'; ?> 
                          px-6 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="register.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) === 'register.php'
                                ? 'bg-green-500 text-white'
                                : 'text-green-600 hover:bg-green-50'; ?> 
                          px-6 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký</span>
                </a>
            </div>
        </header>
    <?php endif; ?>

    <!-- Main content container -->
    <div class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <main class="animate-fade-in">
            <!-- Content sẽ được include ở đây -->