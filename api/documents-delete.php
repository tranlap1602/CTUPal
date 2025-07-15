<?php

/**
 * API DELETE DOCUMENT - PHIÊN BẢN TỐI GIẢN
 * File: api/documents-delete.php
 * Mục đích: Xóa tài liệu (Niên luận)
 */

session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận POST!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$document_id = $_POST['id'] ?? null;

if (empty($document_id) || !is_numeric($document_id)) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ!']);
    exit();
}

try {
    // Lấy thông tin tài liệu
    $sql = "SELECT * FROM documents WHERE id = ? AND user_id = ?";
    $document = fetchOne($sql, [$document_id, $user_id]);

    if (!$document) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài liệu!']);
        exit();
    }

    // Xóa file từ server
    $file_path = "../" . $document['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Xóa từ database
    $delete_sql = "DELETE FROM documents WHERE id = ? AND user_id = ?";
    executeQuery($delete_sql, [$document_id, $user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Xóa tài liệu thành công!'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
