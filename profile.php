<?php

/**
 * File: profile.php
 * Mục đích: Trang cập nhật thông tin cá nhân sinh viên
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Chỉ truy cập được khi đã đăng nhập, cho phép cập nhật name, password, phone, birthday
 */

// Thiết lập biến cho header
$page_title = 'Thông tin cá nhân';
$current_page = 'profile.php';

// Bắt đầu session
session_start();
require_once 'config/db.php';

// Kiểm tra đăng nhập - REQUIRED
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Lấy thông tin user hiện tại
$user_id = $_SESSION['user_id'];
$user = fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);

if (!$user) {
    session_destroy();
    header('Location: login.php?error=user_not_found');
    exit();
}

// Khởi tạo biến thông báo
$success = '';
$error = '';

// Xử lý khi user submit form cập nhật
if ($_POST) {
    // Lấy dữ liệu từ form
    $name = trim($_POST['name'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $birthday = $_POST['birthday'] ?? '';

    // Validation
    $errors = [];

    // Kiểm tra tên (bắt buộc)
    if (empty($name)) {
        $errors[] = 'Vui lòng nhập họ và tên!';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
    }

    // Kiểm tra mật khẩu (nếu muốn đổi)
    $update_password = false;
    if (!empty($new_password)) {
        // Kiểm tra mật khẩu hiện tại
        if (empty($current_password)) {
            $errors[] = 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu mới!';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Mật khẩu hiện tại không đúng!';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
        } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $errors[] = 'Mật khẩu mới phải chứa cả chữ cái và số!';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Xác nhận mật khẩu mới không khớp!';
        } else {
            $update_password = true;
        }
    }

    // Kiểm tra số điện thoại (không bắt buộc)
    if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ (10-11 chữ số)!';
    }

    // Kiểm tra ngày sinh (không bắt buộc)
    if (!empty($birthday)) {
        $birthday_date = DateTime::createFromFormat('Y-m-d', $birthday);
        if (!$birthday_date) {
            $errors[] = 'Ngày sinh không hợp lệ!';
        } else {
            // Kiểm tra tuổi hợp lý (16-100 tuổi)
            $today = new DateTime();
            $age = $today->diff($birthday_date)->y;
            if ($age < 16 || $age > 100) {
                $errors[] = 'Tuổi phải từ 16 đến 100!';
            }
        }
    }

    // Nếu không có lỗi, cập nhật thông tin
    if (empty($errors)) {
        try {
            // Chuẩn bị query update
            if ($update_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET name = ?, password = ?, phone = ?, birthday = ? WHERE id = ?";
                $params = [$name, $hashed_password, $phone ?: null, $birthday ?: null, $user_id];
            } else {
                $query = "UPDATE users SET name = ?, phone = ?, birthday = ? WHERE id = ?";
                $params = [$name, $phone ?: null, $birthday ?: null, $user_id];
            }

            $result = executeQuery($query, $params);

            if ($result) {
                // Cập nhật thành công
                $success = 'Cập nhật thông tin thành công!';

                // Cập nhật lại thông tin user để hiển thị
                $user = fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);

                // Cập nhật session name nếu có thay đổi
                $_SESSION['user_name'] = $user['name'];
            } else {
                $errors[] = 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại!';
            }
        } catch (Exception $e) {

            $errors[] = 'Có lỗi hệ thống. Vui lòng thử lại sau!';
        }
    }

    // Nếu có lỗi, hiển thị lỗi đầu tiên
    if (!empty($errors)) {
        $error = $errors[0];
    }
}

// Include header
include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Main content grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- User info card (bên trái) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="text-center">

                    <!-- Avatar -->
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">
                            <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 2)); ?>
                        </span>
                    </div>

                    <!-- User info display -->
                    <h3 class="text-xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($user['name'] ?? 'Chưa cập nhật'); ?></h3>
                    <p class="text-gray-600 text-sm mb-4">MSSV: <?php echo htmlspecialchars(strtoupper($user['mssv']) ?? 'N/A'); ?></p>

                    <!-- Stats -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </span>
                            <span class="text-sm font-medium text-gray-800">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-phone mr-2"></i>Điện thoại
                            </span>
                            <span class="text-sm font-medium text-gray-800">
                                <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Chưa cập nhật'; ?>
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-birthday-cake mr-2"></i>Sinh nhật
                            </span>
                            <span class="text-sm font-medium text-gray-800">
                                <?php echo $user['birthday'] ? date('d/m/Y', strtotime($user['birthday'])) : 'Chưa cập nhật'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update form (bên phải) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">

                <!-- Form header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
                        <i class="fas fa-edit mr-3 text-blue-500"></i>
                        Cập nhật thông tin
                    </h2>
                    <p class="text-gray-600">Chỉ cập nhật những thông tin bạn muốn thay đổi</p>
                </div>

                <!-- Thông báo thành công -->
                <?php if ($success): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3 text-lg"></i>
                            <span><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Thông báo lỗi -->
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form cập nhật -->
                <form method="POST" class="space-y-6" id="profile-form">

                    <!-- Họ và tên -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Họ và tên *
                        </label>
                        <input type="text"
                            id="name"
                            name="name"
                            required
                            value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Nhập họ và tên đầy đủ">
                        <div class="mt-1 text-xs text-gray-500">
                            Có thể cập nhật từ tên tự động thành họ tên đầy đủ
                        </div>
                    </div>

                    <!-- Đổi mật khẩu section -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-key mr-2 text-blue-500"></i>
                            Đổi mật khẩu (tùy chọn)
                        </h3>

                        <!-- Mật khẩu hiện tại -->
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Mật khẩu hiện tại
                            </label>
                            <div class="relative">
                                <input type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 pr-12"
                                    placeholder="Nhập mật khẩu hiện tại">
                                <button type="button"
                                    onclick="togglePassword('current_password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i id="current_password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Mật khẩu mới -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mật khẩu mới
                                </label>
                                <div class="relative">
                                    <input type="password"
                                        id="new_password"
                                        name="new_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 pr-12"
                                        placeholder="Mật khẩu mới (ít nhất 6 ký tự)">
                                    <button type="button"
                                        onclick="togglePassword('new_password')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i id="new_password-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Xác nhận mật khẩu mới
                                </label>
                                <div class="relative">
                                    <input type="password"
                                        id="confirm_password"
                                        name="confirm_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 pr-12"
                                        placeholder="Nhập lại mật khẩu mới">
                                    <button type="button"
                                        onclick="togglePassword('confirm_password')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i id="confirm_password-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Để trống nếu không muốn đổi mật khẩu. Mật khẩu mới phải chứa cả chữ cái và số.
                        </div>
                    </div>

                    <!-- Số điện thoại -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-blue-500"></i>Số điện thoại
                        </label>
                        <input type="tel"
                            id="phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="0123456789 (tùy chọn)">
                        <div class="mt-1 text-xs text-gray-500">
                            Số điện thoại 10-11 chữ số để liên hệ khi cần thiết
                        </div>
                    </div>

                    <!-- Ngày sinh -->
                    <div>
                        <label for="birthday" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake mr-2 text-blue-500"></i>Ngày sinh
                        </label>
                        <input type="date"
                            id="birthday"
                            name="birthday"
                            value="<?php echo $user['birthday'] ?? ''; ?>"
                            max="<?php echo date('Y-m-d', strtotime('-16 years')); ?>"
                            min="<?php echo date('Y-m-d', strtotime('-100 years')); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <div class="mt-1 text-xs text-gray-500">
                            Thông tin này giúp cải thiện trải nghiệm sử dụng hệ thống
                        </div>
                    </div>

                    <!-- Nút cập nhật -->
                    <div class="flex items-center justify-between pt-6">
                        <a href="index.php" class="text-gray-600 hover:text-gray-800 transition-colors duration-200 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Quay lại trang chủ
                        </a>

                        <button type="submit"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 focus:ring-4 focus:ring-blue-200 transition-all duration-200 transform hover:scale-[1.02] flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Cập nhật thông tin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript cho profile page -->
<script>
    /**
     * Toggle hiện/ẩn mật khẩu
     */
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    /**
     * Validation thời gian thực
     */
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const currentPassword = document.getElementById('current_password');

        // Kiểm tra mật khẩu mới khớp
        function checkPasswordMatch() {
            if (newPassword.value && confirmPassword.value) {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Mật khẩu không khớp');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        }

        // Validation khi nhập mật khẩu mới
        newPassword.addEventListener('input', function() {
            // Nếu nhập mật khẩu mới thì bắt buộc nhập mật khẩu hiện tại
            if (this.value.length > 0) {
                currentPassword.required = true;
                currentPassword.closest('div').querySelector('label').innerHTML =
                    'Mật khẩu hiện tại <span class="text-red-500">*</span>';
            } else {
                currentPassword.required = false;
                currentPassword.closest('div').querySelector('label').innerHTML = 'Mật khẩu hiện tại';
            }
            checkPasswordMatch();
        });

        confirmPassword.addEventListener('input', checkPasswordMatch);

        // Xử lý submit form
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;

            // Hiện loading state
            submitBtn.innerHTML = '<div class=\'inline-block align-middle animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-2\'></div>Đang cập nhật...';
            submitBtn.disabled = true;

            // Reset nếu có lỗi validation
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });

        // Auto-hide alerts
        <?php if ($success || $error): ?>
            setTimeout(() => {
                const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        <?php endif; ?>
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>