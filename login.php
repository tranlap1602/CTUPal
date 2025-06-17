<?php

/**
 * File: login.php
 * Mục đích: Trang đăng nhập hệ thống StudentManager
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Xử lý đăng nhập user, validation form, quản lý session
 */

// Thiết lập biến cho header
$page_title = 'Đăng nhập';
$current_page = 'login.php';

// Bắt đầu session để quản lý đăng nhập
session_start();
require_once 'config/db.php';

// Nếu user đã đăng nhập, chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Khởi tạo biến lưu thông báo lỗi
$error = '';
$success = '';

// Kiểm tra có thông báo từ trang register không
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
}

// Xử lý khi user gửi form đăng nhập
if ($_POST) {
    // Lấy dữ liệu từ form và làm sạch
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember']);

    // Kiểm tra dữ liệu đầu vào
    if (!empty($username) && !empty($password)) {
        try {
            // Truy vấn database để tìm user với email, username hoặc mssv
            $user = fetchOne(
                "SELECT * FROM users WHERE (email = ? OR username = ? OR mssv = ?)",
                [$username, $username, $username]
            );

            // Kiểm tra xem user có tồn tại không
            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công - tạo session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mssv'] = $user['mssv'];

                // Xử lý "Remember Me" nếu được chọn
                if ($remember_me) {
                    // Tạo remember token (có thể implement sau)
                    // setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), "/");
                }

                // Log hoạt động đăng nhập
                logActivity($user['id'], 'Đăng nhập thành công - MSSV: ' . $user['mssv']);

                // Chuyển hướng về trang chủ
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = 'Email/MSSV hoặc mật khẩu không đúng!';
            }
        } catch (Exception $e) {
            // Xử lý lỗi database
            error_log("Login error: " . $e->getMessage());
            $error = 'Có lỗi xảy ra. Vui lòng thử lại sau!';
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Main login content -->
<div class="max-w-md mx-auto">

    <!-- Form container -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-gray-200 animate-bounce-in">

        <!-- Tiêu đề form -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-sign-in-alt text-2xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Đăng nhập</h2>
            <p class="text-gray-600">Nhập thông tin để truy cập hệ thống</p>
        </div>

        <!-- Hiển thị thông báo thành công -->
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center animate-bounce-in">
                <i class="fas fa-check-circle mr-3"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thông báo lỗi -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center animate-bounce-in">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Form đăng nhập -->
        <form method="POST" class="space-y-6" id="login-form">

            <!-- Input email/MSSV -->
            <div class="space-y-2">
                <label for="username" class="block text-sm font-medium text-gray-700">
                    <i class="fas fa-envelope mr-2 text-blue-500"></i>Email CTU hoặc MSSV
                </label>
                <input type="text"
                    id="username"
                    name="username"
                    required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                    placeholder="Email CTU hoặc MSSV (vd: huyb2204945@student.ctu.edu.vn hoặc b2204945)">
                <div class="text-xs text-gray-500 mt-1">
                    Bạn có thể đăng nhập bằng email CTU hoặc chỉ MSSV
                </div>
            </div>

            <!-- Input mật khẩu -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    <i class="fas fa-lock mr-2 text-blue-500"></i>Mật khẩu
                </label>
                <div class="relative">
                    <input type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white pr-12"
                        placeholder="Nhập mật khẩu">

                    <!-- Nút hiện/ẩn mật khẩu -->
                    <button type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i id="password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Checkbox ghi nhớ đăng nhập và quên mật khẩu -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <!-- Link quên mật khẩu -->
                <a href="#" onclick="showForgotPasswordModal()" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    Quên mật khẩu?
                </a>
            </div>

            <!-- Nút đăng nhập -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-200 transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center space-x-2">
                <i class="fas fa-sign-in-alt"></i>
                <span>Đăng nhập</span>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">hoặc</span>
            </div>
        </div>

        <!-- Link đăng ký -->
        <div class="text-center">
            <p class="text-gray-600 text-sm mb-4">
                Chưa có tài khoản StudentManager?
            </p>
            <a href="register.php"
                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-emerald-700 focus:ring-4 focus:ring-green-200 transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center space-x-2">
                <i class="fas fa-user-plus"></i>
                <span>Đăng ký tài khoản mới</span>
            </a>
        </div>

        <!-- Thông tin demo account -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="text-center">
                <p class="text-sm text-blue-800 font-medium mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Tài khoản demo
                </p>
                <div class="space-y-1 text-sm text-blue-700">
                    <p><strong>Admin:</strong> admin / admin123</p>
                    <p><strong>Sinh viên:</strong> student1 / password</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quên mật khẩu -->
<div id="forgot-password-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-key mr-2 text-blue-500"></i>
                    Quên mật khẩu
                </h3>
                <button onclick="closeModal('forgot-password-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email đăng ký
                    </label>
                    <input type="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Nhập email đã đăng ký">
                </div>
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg transition-all duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Gửi link đặt lại mật khẩu
                </button>
            </form>
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    Link đặt lại mật khẩu sẽ được gửi đến email của bạn
                </p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript cho login page -->
<script>
    /**
     * Hàm hiện/ẩn mật khẩu
     */
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            passwordIcon.className = 'fas fa-eye';
        }
    }

    /**
     * Hiển thị modal quên mật khẩu
     */
    function showForgotPasswordModal() {
        document.getElementById('forgot-password-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Đóng modal
     */
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    /**
     * Khởi tạo trang
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Auto focus vào input username
        document.getElementById('username').focus();

        // Xử lý submit form với loading state
        const form = document.getElementById('login-form');
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;

            // Hiển thị loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang đăng nhập...';
            submitBtn.disabled = true;

            // Reset nếu có lỗi validation
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });

        // Auto-hide thông báo sau 5 giây
        <?php if ($error || $success): ?>
            setTimeout(() => {
                const alerts = document.querySelectorAll('.bg-red-50, .bg-green-50');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        <?php endif; ?>
    });

    // Đóng modal với ESC hoặc click outside
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('forgot-password-modal');
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.id === 'forgot-password-modal') {
            closeModal('forgot-password-modal');
        }
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>