<?php
/**
 * Google OAuth2 Authentication Handler
 * Xử lý xác thực Google OAuth2 cho Calendar API
 */

session_start();
require_once 'config/db.php';
require_once 'config/google.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$client = getGoogleClient();

// Xử lý callback từ Google
if (isset($_GET['code'])) {
    try {
        // Lấy access token từ authorization code
        $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($accessToken['error'])) {
            throw new Exception('Lỗi xác thực: ' . $accessToken['error_description']);
        }
        
        // Lưu access token vào session
        $_SESSION['google_access_token'] = $accessToken;
        
        // Chuyển hướng đến trang danh sách lịch
        header('Location: calendar-list.php?message=' . urlencode('Kết nối Google Calendar thành công!') . '&type=success');
        exit();
        
    } catch (Exception $e) {
        error_log('Google OAuth Error: ' . $e->getMessage());
        header('Location: calendar.php?message=' . urlencode('Lỗi kết nối: ' . $e->getMessage()) . '&type=error');
        exit();
    }
}

// Xử lý ngắt kết nối
if (isset($_GET['disconnect'])) {
    // Xóa access token khỏi session
    unset($_SESSION['google_access_token']);
    unset($_SESSION['selected_calendar_id']);
    unset($_SESSION['selected_calendar_name']);
    
    header('Location: calendar.php?message=' . urlencode('Đã ngắt kết nối Google Calendar!') . '&type=info');
    exit();
}

// Nếu không có code, chuyển hướng đến Google OAuth
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
?>