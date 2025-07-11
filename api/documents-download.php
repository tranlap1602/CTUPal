<?php

/**
 * API DOWNLOAD DOCUMENT
 * File: api/documents-download.php
 * Mục đích: Tải về tài liệu (chỉ được tải tài liệu của chính mình)
 * Tác giả: Student Manager System
 * Ngày tạo: 2025-01-29
 * 
 * Chức năng:
 * - Kiểm tra quyền sở hữu tài liệu
 * - Kiểm tra file tồn tại
 * - Trả về file để download
 * - Log hoạt động download
 */

// Bắt đầu session và kết nối database
session_start();
require_once '../config/db.php';

// Hàm hiển thị lỗi (không phải JSON vì đây là download)
function showError($message)
{
    http_response_code(404);
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Lỗi</title>
    <meta charset='utf-8'>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2 class='error'>Lỗi!</h2>
    <p>$message</p>
    <a href='../documents.php'>← Quay lại trang tài liệu</a>
</body>
</html>";
    exit();
}

// BƯỚC 1: Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    showError('Bạn chưa đăng nhập!');
}

// BƯỚC 2: Kiểm tra method GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    showError('Chỉ chấp nhận method GET!');
}

// Lấy thông tin user
$user_id = $_SESSION['user_id'];

// BƯỚC 3: Lấy ID tài liệu cần download
$document_id = $_GET['id'] ?? null;

// Kiểm tra ID có hợp lệ không
if (empty($document_id) || !is_numeric($document_id)) {
    showError('ID tài liệu không hợp lệ!');
}

// BƯỚC 4: Lấy thông tin tài liệu từ database
try {
    $sql = "SELECT * FROM documents WHERE id = ? AND user_id = ?";
    $document = fetchOne($sql, [$document_id, $user_id]);

    if (!$document) {
        showError('Không tìm thấy tài liệu hoặc bạn không có quyền truy cập!');
    }
} catch (Exception $e) {
    showError('Lỗi truy vấn database: ' . $e->getMessage());
}

// BƯỚC 5: Kiểm tra file có tồn tại không
$file_path = "../" . $document['file_path'];

if (!file_exists($file_path)) {
    showError('File không tồn tại trên server!');
}

// BƯỚC 6: Lấy thông tin file
$file_size = filesize($file_path);
$file_name = $document['file_name']; // Tên gốc của file
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// BƯỚC 7: Xác định MIME type
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'txt' => 'text/plain',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed'
];

$mime_type = $mime_types[$file_extension] ?? 'application/octet-stream';

// BƯỚC 8: Log hoạt động download
try {
    logActivity($user_id, 'DOWNLOAD_DOCUMENT', "Downloaded: {$document['title']} (ID: $document_id)");
} catch (Exception $e) {
    // Không dừng download nếu log lỗi
    error_log("Log error: " . $e->getMessage());
}

// BƯỚC 9: Thiết lập headers cho download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . $file_size);
header('Content-Transfer-Encoding: binary');
header('Cache-Control: private, no-transform, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// BƯỚC 10: Đọc và gửi file
// Dùng readfile() cho file nhỏ hoặc fpassthru() cho file lớn
if ($file_size < 50 * 1024 * 1024) { // File < 50MB
    readfile($file_path);
} else {
    // File lớn: đọc từng chunk để tránh memory limit
    $handle = fopen($file_path, 'rb');
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 8192); // Đọc 8KB mỗi lần
            ob_flush();
            flush();
        }
        fclose($handle);
    } else {
        showError('Không thể đọc file!');
    }
}

exit();
