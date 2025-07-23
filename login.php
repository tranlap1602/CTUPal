<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
// Tự động đăng nhập nếu có cookie user_id
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

$error = '';

if ($_POST) {
    // Lấy dữ liệu từ form và làm sạch
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Kiểm tra dữ liệu đầu vào
    if (!empty($login_id) && !empty($password)) {
        try {
            // Truy vấn database để tìm user với email hoặc mssv
            $user = fetchOne(
                "SELECT * FROM users WHERE (email = ? OR mssv = ?)",
                [$login_id, $login_id]
            );

            // Kiểm tra xem user có tồn tại không
            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công - tạo session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mssv'] = $user['mssv'];
                // Ghi nhớ đăng nhập
                if ($remember) {
                    setcookie('user_id', $user['id'], time() + (86400 * 3), "/");
                }
                // Chuyển hướng về trang chủ
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = 'Email/MSSV hoặc mật khẩu không đúng!';
            }
        } catch (Exception $e) {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại sau!';
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - StudentManager</title>
    <link rel="stylesheet" href="src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="assets/icon/logo.svg">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full mb-4 shadow-lg">
                    <i class="fas fa-graduation-cap text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">StudentManager</h1>
                <p class="text-gray-600">Hệ thống quản lý sinh viên</p>
            </div>
            <!-- Hiển thị thông báo lỗi -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <!-- Form đăng nhập -->
            <form method="POST" class="space-y-6">
                <!--Email/MSSV -->
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="login_id"
                            name="login_id"
                            required
                            value="<?php echo isset($_POST['login_id']) ? htmlspecialchars($_POST['login_id']) : ''; ?>"
                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Email hoặc MSSV">
                    </div>
                </div>
                <!-- Mật khẩu -->
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Mật khẩu">
                    </div>
                </div>
                <!-- Ghi nhớ đăng nhập -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="mr-2"
                        <?php if (isset($_POST['remember'])) echo 'checked'; ?>>
                    <label for="remember" class="text-gray-600">Ghi nhớ đăng nhập</label>
                </div>
                <!-- Nút đăng nhập -->
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Đăng nhập
                </button>
            </form>
            <div class="mt-6 text-center">
                <a href="register.php" class="text-blue-600 hover:underline">Chưa có tài khoản? Đăng ký</a>
            </div>
        </div>
    </div>
</body>
</html> 