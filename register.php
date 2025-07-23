<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ!';
    } elseif (!preg_match('/@student\\.ctu\\.edu\\.vn$/', $email)) {
        $error = 'Chỉ chấp nhận email sinh viên CTU!';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Lấy mssv
        $username = explode('@', $email)[0];
        if (preg_match('/(B[0-9]{7})$/i', $username, $matches)) {
            $mssv = strtoupper($matches[1]);
        } else {
            $error = 'Email không hợp lệ!';
        }
        if (empty($error)) {
            // Kiểm tra email đã tồn tại
            $user = fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            if ($user) {
                $error = 'Email đã được đăng ký!';
            } else {
                // Kiểm tra MSSV đã tồn tại
                $user_mssv = fetchOne("SELECT * FROM users WHERE mssv = ?", [$mssv]);
                if ($user_mssv) {
                    $error = 'MSSV đã được đăng ký!';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    // Tách tên
                    $name = $username;
                    if (preg_match('/^(.*)(B[0-9]{7})$/i', $username, $name_matches)) {
                        $name = trim($name_matches[1]);
                        if ($name === '') {
                            $name = $mssv;
                        }
                    }
                    $sql = "INSERT INTO users (name, email, mssv, password) VALUES (?, ?, ?, ?)";
                    $params = [$name, $email, $mssv, $hash];
                    $user_id = insertAndGetId($sql, $params);
                    if ($user_id) {
                        $success = 'Đăng ký thành công!';
                    } else {
                        $error = 'Có lỗi xảy ra, vui lòng thử lại.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - StudentManager</title>
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
                <p class="text-gray-600">Đăng ký tài khoản</p>
            </div>
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Email CTU">
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Mật khẩu (tối thiểu 6 ký tự)">
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Xác nhận mật khẩu">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Đăng ký tài khoản
                </button>
            </form>
            <div class="mt-6 text-center">
                <a href="login.php" class="text-blue-600 hover:underline">Đã có tài khoản? Đăng nhập</a>
            </div>
        </div>
    </div>
</body>

</html>