<?php

/**
 * API UPLOAD DOCUMENTS - PHIÊN BẢN TỐI GIẢN
 * File: api/documents-upload.php
 * Mục đích: Upload tài liệu (Niên luận)
 */

session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit();
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận POST!']);
    exit();
}

// Kiểm tra có file upload không
if (!isset($_FILES['document_file']) || empty($_FILES['document_file']['name'][0])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn file!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? 'other';
$subject = trim($_POST['subject'] ?? '');

// Danh sách file types được phép
$allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
$max_size = 10 * 1024 * 1024; // 10MB

// Tạo thư mục cho user
$user_folder = "../uploads/documents/$user_id";
if (!file_exists($user_folder)) {
    mkdir($user_folder, 0755, true);
}

// Xử lý upload
$uploaded_files = [];
$files = $_FILES['document_file'];

for ($i = 0; $i < count($files['name']); $i++) {
    $file_name = $files['name'][$i];
    $file_tmp = $files['tmp_name'][$i];
    $file_size = $files['size'][$i];

    if (empty($file_name)) continue;

    // Kiểm tra kích thước
    if ($file_size > $max_size) {
        echo json_encode(['success' => false, 'message' => "File '$file_name' quá lớn! Tối đa 10MB."]);
        exit();
    }

    // Kiểm tra loại file
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => "File '$file_name' không được hỗ trợ!"]);
        exit();
    }

    // Tạo tên file mới
    $timestamp = date('Ymd_His');
    $random = rand(1000, 9999);
    $safe_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
    $new_filename = $safe_filename . '_' . $timestamp . '_' . $random . '.' . $file_extension;
    $file_path = "$user_folder/$new_filename";

    // Di chuyển file
    if (!move_uploaded_file($file_tmp, $file_path)) {
        echo json_encode(['success' => false, 'message' => "Không thể lưu file: $file_name"]);
        exit();
    }

    // Lưu vào database
    $relative_path = "uploads/documents/$user_id/$new_filename";
    $document_title = !empty($title) ? $title : pathinfo($file_name, PATHINFO_FILENAME);

    try {
        $sql = "INSERT INTO documents (user_id, title, description, file_name, file_path, file_size, file_type, category, subject) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $document_id = insertAndGetId($sql, [
            $user_id,
            $document_title,
            $description,
            $file_name,
            $relative_path,
            $file_size,
            $file_extension,
            $category,
            $subject
        ]);

        if ($document_id) {
            $uploaded_files[] = [
                'id' => $document_id,
                'name' => $file_name,
                'size' => $file_size
            ];
        }
    } catch (Exception $e) {
        unlink($file_path); // Xóa file nếu lưu DB thất bại
        echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
        exit();
    }
}

if (empty($uploaded_files)) {
    echo json_encode(['success' => false, 'message' => 'Không có file nào được upload!']);
    exit();
}

echo json_encode([
    'success' => true,
    'message' => count($uploaded_files) . ' file đã được upload thành công!',
    'files' => $uploaded_files
]);
