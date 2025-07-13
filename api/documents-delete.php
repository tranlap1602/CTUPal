<?php

/**
 * API DELETE DOCUMENT
 * File: api/documents-delete.php
 * Mục đích: Xóa tài liệu của user (chỉ được xóa tài liệu của chính mình)
 * Tác giả: Student Manager System
 * Ngày tạo: 2025-01-29
 * 
 * Chức năng:
 * - Kiểm tra quyền sở hữu tài liệu
 * - Xóa file từ server
 * - Xóa record từ database
 * - Trả về kết quả JSON
 */

// Bắt đầu session và kết nối database
session_start();
require_once '../config/db.php';

// Đặt header để trả về JSON
header('Content-Type: application/json');

// Hàm trả về lỗi
function returnError($message)
{
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit();
}

// Hàm trả về thành công
function returnSuccess($message, $data = null)
{
    $response = [
        'success' => true,
        'message' => $message
    ];
    if ($data) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// BƯỚC 1: Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    returnError('Bạn chưa đăng nhập!');
}

// BƯỚC 2: Kiểm tra method POST hoặc DELETE
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    returnError('Chỉ chấp nhận method POST hoặc DELETE!');
}

// Lấy thông tin user
$user_id = $_SESSION['user_id'];

// BƯỚC 3: Lấy ID tài liệu cần xóa
$document_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_id = $_POST['id'] ?? null;
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Đọc dữ liệu từ body cho DELETE request
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $document_id = $data['id'] ?? $_GET['id'] ?? null;
}

// Kiểm tra ID có hợp lệ không
if (empty($document_id) || !is_numeric($document_id)) {
    returnError('ID tài liệu không hợp lệ!');
}

// BƯỚC 4: Lấy thông tin tài liệu từ database
try {
    $sql = "SELECT * FROM documents WHERE id = ? AND user_id = ?";
    $document = fetchOne($sql, [$document_id, $user_id]);

    if (!$document) {
        returnError('Không tìm thấy tài liệu hoặc bạn không có quyền xóa!');
    }
} catch (Exception $e) {
    returnError('Lỗi truy vấn database: ' . $e->getMessage());
}

// BƯỚC 5: Xóa file từ server
$file_path = "../" . $document['file_path'];
$file_deleted = false;

if (file_exists($file_path)) {
    if (unlink($file_path)) {
        $file_deleted = true;
    } else {
    }
} else {
    // File không tồn tại (có thể đã bị xóa trước đó)
    $file_deleted = true;
}

// BƯỚC 6: Xóa record từ database
try {
    $delete_sql = "DELETE FROM documents WHERE id = ? AND user_id = ?";
    executeQuery($delete_sql, [$document_id, $user_id]);



    // BƯỚC 8: Trả về kết quả thành công
    returnSuccess('Xóa tài liệu thành công!', [
        'deleted_document' => [
            'id' => $document['id'],
            'title' => $document['title'],
            'file_name' => $document['file_name']
        ],
        'file_deleted' => $file_deleted
    ]);
} catch (Exception $e) {
    returnError('Lỗi khi xóa tài liệu: ' . $e->getMessage());
}
