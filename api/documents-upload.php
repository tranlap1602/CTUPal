<?php

/**
 * API UPLOAD DOCUMENTS
 * File: api/documents-upload.php
 * Mục đích: Xử lý upload tài liệu từ form
 * Tác giả: Student Manager System
 * Ngày tạo: 2025-01-29
 * 
 * Chức năng:
 * - Nhận file upload từ form
 * - Kiểm tra file hợp lệ (type, size)
 * - Lưu file vào thư mục uploads/documents/{user_id}/
 * - Lưu thông tin vào database
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

// BƯỚC 2: Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    returnError('Chỉ chấp nhận method POST!');
}

// BƯỚC 3: Kiểm tra có file upload không
if (!isset($_FILES['document_file']) || empty($_FILES['document_file']['name'][0])) {
    returnError('Vui lòng chọn file để upload!');
}

// Lấy thông tin user
$user_id = $_SESSION['user_id'];

// Lấy thông tin từ form
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? 'other';
$subject = trim($_POST['subject'] ?? '');

// BƯỚC 4: Title sẽ được tự động tạo từ tên file nếu để trống
// Không cần validate title ở đây nữa

// Danh sách các file type được phép
$allowed_types = [
    'pdf',
    'doc',
    'docx',
    'xls',
    'xlsx',
    'ppt',
    'pptx',
    'txt',
    'jpg',
    'jpeg',
    'png',
    'zip',
    'rar'
];

// Kích thước tối đa: 10MB = 10 * 1024 * 1024 bytes
$max_file_size = 10 * 1024 * 1024;

// BƯỚC 5: Tạo thư mục cho user nếu chưa có
$user_folder = "../uploads/documents/$user_id";
if (!file_exists($user_folder)) {
    if (!mkdir($user_folder, 0755, true)) {
        returnError('Không thể tạo thư mục lưu trữ!');
    }
}

// BƯỚC 6: Xử lý từng file upload
$uploaded_files = [];
$files = $_FILES['document_file'];

// Đếm số file được upload
$file_count = count($files['name']);

for ($i = 0; $i < $file_count; $i++) {
    // Lấy thông tin file thứ i
    $file_name = $files['name'][$i];
    $file_tmp = $files['tmp_name'][$i];
    $file_size = $files['size'][$i];
    $file_error = $files['error'][$i];

    // Bỏ qua file rỗng
    if (empty($file_name)) {
        continue;
    }

    // Kiểm tra lỗi upload
    if ($file_error !== UPLOAD_ERR_OK) {
        returnError("Lỗi upload file: $file_name");
    }

    // Kiểm tra kích thước file
    if ($file_size > $max_file_size) {
        returnError("File '$file_name' quá lớn! Tối đa 10MB.");
    }

    // Lấy phần mở rộng file
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Kiểm tra file type có được phép không
    if (!in_array($file_extension, $allowed_types)) {
        returnError("File '$file_name' không được hỗ trợ! Chỉ chấp nhận: " . implode(', ', $allowed_types));
    }

    // Tạo tên file mới để tránh trùng lặp
    // Format: originalname_YYYYMMDD_HHMMSS_random.ext
    $timestamp = date('Ymd_His');
    $random = rand(1000, 9999);
    $safe_filename = pathinfo($file_name, PATHINFO_FILENAME);
    $safe_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $safe_filename); // Loại bỏ ký tự đặc biệt
    $new_filename = $safe_filename . '_' . $timestamp . '_' . $random . '.' . $file_extension;

    // Đường dẫn đầy đủ để lưu file
    $file_path = "$user_folder/$new_filename";

    // BƯỚC 7: Di chuyển file từ temp sang thư mục đích
    if (!move_uploaded_file($file_tmp, $file_path)) {
        returnError("Không thể lưu file: $file_name");
    }

    // BƯỚC 8: Lưu thông tin vào database
    $relative_path = "uploads/documents/$user_id/$new_filename";

    // Tự động tạo title từ tên file nếu title trống
    $document_title = !empty($title) ? $title : pathinfo($file_name, PATHINFO_FILENAME);

    try {
        $sql = "INSERT INTO documents (user_id, title, description, file_name, file_path, file_size, file_type, category, subject) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $user_id,
            $document_title,     // Title tự động hoặc từ form
            $description,
            $file_name,          // Tên gốc
            $relative_path,      // Đường dẫn đã lưu
            $file_size,          // Kích thước file (bytes)
            $file_extension,     // Phần mở rộng (lưu vào file_type)
            $category,
            $subject
        ];

        $document_id = insertAndGetId($sql, $params);

        if ($document_id) {
            $uploaded_files[] = [
                'id' => $document_id,
                'original_name' => $file_name,
                'saved_name' => $new_filename,
                'size' => $file_size,
                'type' => $file_extension
            ];
        } else {
            returnError("Không thể lưu thông tin file vào database: $file_name");
        }
    } catch (Exception $e) {
        // Xóa file đã upload nếu lưu database thất bại
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        returnError("Lỗi database: " . $e->getMessage());
    }
}

// BƯỚC 9: Trả về kết quả thành công
if (empty($uploaded_files)) {
    returnError('Không có file nào được upload thành công!');
}

returnSuccess(
    count($uploaded_files) . ' file đã được upload thành công!',
    [
        'uploaded_count' => count($uploaded_files),
        'files' => $uploaded_files
    ]
);
