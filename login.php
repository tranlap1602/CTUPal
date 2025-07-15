<?php

/**
 * File: login.php
 * Mục đích: Trang đăng nhập hệ thống StudentManager
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Xử lý đăng nhập user, validation form, quản lý session
 */

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

// Xử lý khi user gửi form đăng nhập
if ($_POST) {
    // Lấy dữ liệu từ form và làm sạch
    $username = trim($_POST['username']);
    $password = $_POST['password'];

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



                // Chuyển hướng về trang chủ
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = 'Email/MSSV hoặc mật khẩu không đúng!';
            }
        } catch (Exception $e) {
            // Xử lý lỗi database

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

    <!-- Tailwind CSS compiled -->
    <link rel="stylesheet" href="src/output.css">

    <!-- Font Awesome cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full mx-4">
        <!-- Form container -->
        <div class="bg-white rounded-lg shadow-lg p-8">

            <!-- Logo và tên web -->
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

                <!-- Input email/MSSV -->
                <div>
                    <!-- <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Email CTU hoặc MSSV
                    </label> -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="username"
                            name="username"
                            required
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Email hoặc MSSV">
                    </div>
                </div>

                <!-- Input mật khẩu -->
                <div>
                    <!-- <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mật khẩu
                    </label> -->
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

                <!-- Nút đăng nhập -->
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Đăng nhập
                </button>

            </form>
        </div>
    </div>

</body>

</html>