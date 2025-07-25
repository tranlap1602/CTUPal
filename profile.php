<?php
$page_title = 'Thông tin cá nhân';
$current_page = 'profile.php';

include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$user_id = $_SESSION['user_id'];
$user = fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);

if (!$user) {
    session_destroy();
    header('Location: login.php?error=user_not_found');
    exit();
}

$success = '';
$error = '';

// Xử lý form submit
if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $birthday = $_POST['birthday'] ?? '';

    $errors = [];
    // Kiểm tra tên
    if (empty($name)) {
        $errors[] = 'Vui lòng nhập họ và tên!';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
    }

    // Kiểm tra mật khẩu
    $update_password = false;
    if (!empty($new_password)) {
        // Kiểm tra mật khẩu hiện tại
        if (empty($current_password)) {
            $errors[] = 'Vui lòng nhập mật khẩu hiện tại!';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Mật khẩu hiện tại không đúng!';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $errors[] = 'Mật khẩu phải chứa cả chữ cái và số!';
        } elseif (!empty($new_password) && $new_password !== $confirm_password) {
            $errors[] = 'Mật khẩu không khớp!';
        } else {
            $update_password = true;
        }
    }

    // Kiểm tra số điện thoại
    if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ (10-11 chữ số)!';
    }

    // Kiểm tra ngày sinh
    if (!empty($birthday)) {
        $birthday_date = DateTime::createFromFormat('Y-m-d', $birthday);
        if (!$birthday_date) {
            $errors[] = 'Ngày sinh không hợp lệ!';
        }
    }

    // Cập nhật thông tin
    if (empty($errors)) {
        try {
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
                $success = 'Cập nhật thông tin thành công!';
                $user = fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);
                $_SESSION['user_name'] = $user['name'];
            } else {
                $errors[] = 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại!';
            }
        } catch (Exception $e) {
            $errors[] = 'Có lỗi hệ thống. Vui lòng thử lại sau!';
        }
    }
    if (!empty($errors)) {
        $error = $errors[0];
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- User info card-->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="text-center">

                    <!-- Avatar -->
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">
                            <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
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
                                <i class="fas fa-calendar-alt mr-2"></i>Ngày sinh
                            </span>
                            <span class="text-sm font-medium text-gray-800">
                                <?php echo $user['birthday'] ? date('d/m/Y', strtotime($user['birthday'])) : 'Chưa cập nhật'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update form-->
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

                <!-- Form cập nhật -->
                <form method="POST" class="space-y-6" id="profile-form">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Họ và tên
                        </label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200"
                            placeholder="Nhập họ và tên đầy đủ">
                    </div>

                    <div class="m-4">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-key mr-2 text-blue-500"></i>
                            Đổi mật khẩu
                        </h3>

                        <div class="mt-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Mật khẩu hiện tại
                            </label>
                            <div class="relative">
                                <input type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 pr-12"
                                    placeholder="Nhập mật khẩu hiện tại">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Mật khẩu mới
                            </label>
                            <div class="relative">
                                <input type="password"
                                    id="new_password"
                                    name="new_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 pr-12"
                                    placeholder="Mật khẩu mới (ít nhất 6 ký tự)">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Xác nhận mật khẩu mới
                            </label>
                            <div class="relative">
                                <input type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 pr-12"
                                    placeholder="Nhập lại mật khẩu mới">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-blue-500"></i>Số điện thoại
                        </label>
                        <input type="tel"
                            id="phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div>
                        <label for="birthday" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake mr-2 text-blue-500"></i>Ngày sinh
                        </label>
                        <input type="date"
                            id="birthday"
                            name="birthday"
                            value="<?php echo $user['birthday'] ?? ''; ?>"
                            max="<?php echo date('Y-m-d'); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div class="flex items-center justify-between pt-6">
                        <a href="index.php" class="bg-blue-500 py-3 px-6 rounded-lg text-white hover:bg-blue-700 transition-all duration-200 transform hover:scale-[1.02] flex items-center space-x-2">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Quay lại</span>
                        </a>

                        <button type="submit"
                            class="bg-blue-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 transform hover:scale-[1.02] flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Cập nhật</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/toast.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        // Kiểm tra khớp mật khẩu
        form.addEventListener('submit', function(e) {
            if (newPassword.value && confirmPassword.value && newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                showToast('Mật khẩu xác nhận không khớp!', 'error');
                confirmPassword.focus();
            }
        });
    });
    <?php if ($success): ?>
        showToast("<?php echo addslashes($success); ?>", "success");
    <?php endif; ?>
    <?php if ($error): ?>
        showToast("<?php echo addslashes($error); ?>", "error");
    <?php endif; ?>
</script>

<?php
include 'includes/footer.php';
?>