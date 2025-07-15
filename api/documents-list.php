<?php

/**
 * API LIST DOCUMENTS - PHIÊN BẢN TỐI GIẢN + FILTER
 * File: api/documents-list.php
 * Mục đích: Lấy danh sách tài liệu với filter (Niên luận)
 */

session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận GET!']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy tham số filter
$category = $_GET['category'] ?? '';
$subject = $_GET['subject'] ?? '';

try {
    // Xây dựng câu SQL với filter
    $sql = "SELECT * FROM documents WHERE user_id = ?";
    $params = [$user_id];

    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    if (!empty($subject)) {
        $sql .= " AND subject = ?";
        $params[] = $subject;
    }

    $sql .= " ORDER BY created_at DESC";

    // Lấy danh sách tài liệu
    $documents = fetchAll($sql, $params);

    // Xử lý dữ liệu
    foreach ($documents as &$doc) {
        $doc['file_size_formatted'] = formatFileSize($doc['file_size']);
        $doc['upload_date_formatted'] = date('d/m/Y H:i', strtotime($doc['created_at']));
        $doc['download_url'] = 'api/documents-download.php?id=' . $doc['id'];
        $doc['file_extension'] = $doc['file_type'];
    }

    // Lấy danh sách subjects và categories cho filter
    $subjects_sql = "SELECT DISTINCT subject FROM documents WHERE user_id = ? AND subject IS NOT NULL AND subject != '' ORDER BY subject";
    $subjects = fetchAll($subjects_sql, [$user_id]);
    $subject_list = array_column($subjects, 'subject');

    $categories_sql = "SELECT DISTINCT category FROM documents WHERE user_id = ? AND category IS NOT NULL ORDER BY category";
    $categories = fetchAll($categories_sql, [$user_id]);
    $category_list = array_column($categories, 'category');

    echo json_encode([
        'success' => true,
        'message' => 'Lấy danh sách thành công!',
        'data' => [
            'documents' => $documents,
            'total_count' => count($documents),
            'filters' => [
                'subjects' => $subject_list,
                'categories' => $category_list
            ]
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
