<?php
/**
 * API DOWNLOAD DOCUMENT - PHIÊN BẢN TỐI GIẢN
 * File: api/documents-download.php
 * Mục đích: Tải về tài liệu (Niên luận)
 */

session_start();
require_once '../config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    die('Chưa đăng nhập!');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die('Chỉ chấp nhận GET!');
}

$user_id = $_SESSION['user_id'];
$document_id = $_GET['id'] ?? null;

if (empty($document_id) || !is_numeric($document_id)) {
    die('ID không hợp lệ!');
}

try {
    // Lấy thông tin tài liệu
    $sql = "SELECT * FROM documents WHERE id = ? AND user_id = ?";
    $document = fetchOne($sql, [$document_id, $user_id]);
    
    if (!$document) {
        die('Không tìm thấy tài liệu!');
    }
    
    // Kiểm tra file tồn tại
    $file_path = "../" . $document['file_path'];
    if (!file_exists($file_path)) {
        die('File không tồn tại!');
    }
    
    // Thiết lập headers download
    $file_name = $document['file_name'];
    $file_size = filesize($file_path);
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Content-Length: ' . $file_size);
    header('Cache-Control: no-cache');
    
    // Gửi file
    readfile($file_path);
    exit();
    
} catch (Exception $e) {
    die('Lỗi: ' . $e->getMessage());
}
