<?php

/**
 * API LIST DOCUMENTS
 * File: api/documents-list.php
 * Mục đích: Lấy danh sách tài liệu của user hiện tại
 * Tác giả: Student Manager System
 * Ngày tạo: 2025-01-29
 * 
 * Chức năng:
 * - Lấy danh sách tài liệu theo user_id
 * - Hỗ trợ filter theo category, subject
 * - Hỗ trợ search theo title
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

// BƯỚC 2: Kiểm tra method GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    returnError('Chỉ chấp nhận method GET!');
}

// Lấy thông tin user
$user_id = $_SESSION['user_id'];

// BƯỚC 3: Lấy các tham số filter từ URL
$category = $_GET['category'] ?? '';
$subject = $_GET['subject'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'upload_date';
$order = $_GET['order'] ?? 'DESC';

// BƯỚC 4: Xây dựng câu SQL với điều kiện
$sql = "SELECT * FROM documents WHERE user_id = ?";
$params = [$user_id];

// Thêm điều kiện filter theo category
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Thêm điều kiện filter theo subject
if (!empty($subject)) {
    $sql .= " AND subject LIKE ?";
    $params[] = "%$subject%";
}

// Thêm điều kiện search theo title hoặc description
if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// BƯỚC 5: Thêm sắp xếp
$allowed_sort = ['created_at', 'title', 'category', 'subject', 'file_size'];
$allowed_order = ['ASC', 'DESC'];

// Map sort parameters
$sort_mapping = [
    'upload_date' => 'created_at',  // Backward compatibility
    'created_at' => 'created_at',
    'title' => 'title',
    'category' => 'category',
    'subject' => 'subject',
    'file_size' => 'file_size'
];

$actual_sort = $sort_mapping[$sort] ?? 'created_at';

if (in_array($actual_sort, $allowed_sort) && in_array($order, $allowed_order)) {
    $sql .= " ORDER BY $actual_sort $order";
} else {
    $sql .= " ORDER BY created_at DESC";
}

// BƯỚC 6: Thực hiện truy vấn
try {
    $documents = fetchAll($sql, $params);

    // BƯỚC 7: Xử lý dữ liệu trước khi trả về
    foreach ($documents as &$doc) {
        // Format kích thước file từ database
        $doc['file_size_formatted'] = formatFileSize($doc['file_size'] ?? 0);

        // Kiểm tra file có tồn tại trên disk không
        $full_path = "../" . $doc['file_path'];
        $doc['file_exists'] = file_exists($full_path);

        // Format ngày upload 
        $doc['upload_date_formatted'] = date('d/m/Y H:i', strtotime($doc['created_at']));
        $doc['upload_date'] = $doc['created_at']; // Backward compatibility

        // Tạo URL download
        $doc['download_url'] = 'api/documents-download.php?id=' . $doc['id'];

        // Đảm bảo có đầy đủ thông tin cần thiết
        // (Đã bỏ download_count và is_public)
    }

    // BƯỚC 8: Lấy thống kê đơn giản
    $stats_sql = "SELECT 
                    COUNT(*) as total_documents,
                    category,
                    COUNT(*) as count_by_category
                  FROM documents 
                  WHERE user_id = ? 
                  GROUP BY category";

    $stats = fetchAll($stats_sql, [$user_id]);

    // Tính tổng số documents
    $total_count = 0;
    $category_stats = [];
    foreach ($stats as $stat) {
        $total_count += $stat['count_by_category'];
        $category_stats[$stat['category']] = $stat['count_by_category'];
    }

    // BƯỚC 9: Trả về kết quả
    returnSuccess('Lấy danh sách tài liệu thành công!', [
        'documents' => $documents,
        'total_count' => $total_count,
        'filtered_count' => count($documents),
        'category_stats' => $category_stats,
        'filters' => [
            'category' => $category,
            'subject' => $subject,
            'search' => $search,
            'sort' => $sort,
            'order' => $order
        ]
    ]);
} catch (Exception $e) {
    returnError('Lỗi truy vấn database: ' . $e->getMessage());
}
