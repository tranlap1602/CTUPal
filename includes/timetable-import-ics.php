<?php

/**
 * File: includes/timetable-import-ics.php
 * Mục đích: Xử lý import file .ics vào database
 * 
 * Chức năng:
 * - Nhận upload file .ics
 * - Sử dụng ICSParser để phân tích
 * - Lưu vào database với giới hạn lặp lại
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type
header('Content-Type: application/json');

session_start();

try {
    require_once '../config/db.php';
    require_once 'ICSParser.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi load file: ' . $e->getMessage()
    ]);
    exit;
}



// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method không được hỗ trợ']);
    exit;
}

// Kiểm tra file upload
if (!isset($_FILES['ics_file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy file upload']);
    exit;
}

if ($_FILES['ics_file']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File quá lớn (vượt quá upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'File quá lớn (vượt quá MAX_FILE_SIZE)',
        UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần',
        UPLOAD_ERR_NO_FILE => 'Không có file nào được upload',
        UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục temp',
        UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file vào disk',
        UPLOAD_ERR_EXTENSION => 'Upload bị chặn bởi extension'
    ];

    $errorCode = $_FILES['ics_file']['error'];
    $errorMessage = $errorMessages[$errorCode] ?? 'Lỗi upload không xác định';

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit;
}

$uploadedFile = $_FILES['ics_file'];
$userId = $_SESSION['user_id'];

// Kiểm tra loại file
$allowedTypes = ['text/calendar', 'text/plain', 'application/octet-stream'];
$fileExtension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));

if ($fileExtension !== 'ics' && !in_array($uploadedFile['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ file .ics']);
    exit;
}

// Kiểm tra kích thước file (tối đa 5MB)
if ($uploadedFile['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File quá lớn (tối đa 5MB)']);
    exit;
}

try {
    // Tạo thư mục upload nếu chưa tồn tại
    $uploadDir = '../uploads/ics/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Tạo tên file unique
    $fileName = uniqid('ics_') . '_' . time() . '.ics';
    $filePath = $uploadDir . $fileName;

    // Di chuyển file upload
    if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
        throw new Exception('Không thể lưu file upload');
    }

    // Phân tích file .ics
    $parser = new ICSParser();
    $events = $parser->parseFile($filePath);

    if (empty($events)) {
        throw new Exception('Không tìm thấy sự kiện nào trong file .ics');
    }

    // Lấy thống kê
    $statistics = $parser->getStatistics();

    // Bắt đầu transaction
    global $pdo;
    $pdo->beginTransaction();

    // Xóa dữ liệu cũ nếu người dùng chọn (tùy chọn)
    $clearOldData = isset($_POST['clear_old_data']) && $_POST['clear_old_data'] === 'true';
    if ($clearOldData) {
        $clearQuery = "DELETE FROM timetable WHERE user_id = ?";
        $clearStmt = $pdo->prepare($clearQuery);
        $clearStmt->execute([$userId]);
    }

    // Chuẩn bị câu lệnh insert với cấu trúc bảng mới (đã tối ưu)
    $insertQuery = "INSERT INTO timetable (user_id, subject_name, day_of_week, start_time, end_time, classroom, teacher, notes, start_date, end_date, ics_uid, recurrence_rule, excluded_dates, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $pdo->prepare($insertQuery);

    // Đếm số lượng import thành công
    $successCount = 0;
    $duplicateCount = 0;
    $errorCount = 0;

    foreach ($events as $event) {
        try {
            // Validate dữ liệu
            if (empty($event['subject_name']) || empty($event['day_of_week']) || empty($event['start_time']) || empty($event['end_time'])) {
                $errorCount++;
                continue;
            }

            // Kiểm tra trùng lặp dựa trên thông tin cơ bản và ics_uid (nếu có)
            if (!empty($event['ics_uid'])) {
                // Kiểm tra theo ics_uid để tránh trùng lặp khi re-import
                $checkQuery = "SELECT COUNT(*) FROM timetable WHERE user_id = ? AND ics_uid = ?";
                $checkStmt = $pdo->prepare($checkQuery);
                $checkStmt->execute([$userId, $event['ics_uid']]);
            } else {
                // Kiểm tra theo thông tin cơ bản
                $checkQuery = "SELECT COUNT(*) FROM timetable WHERE user_id = ? AND subject_name = ? AND day_of_week = ? AND start_time = ? AND end_time = ?";
                $checkStmt = $pdo->prepare($checkQuery);
                $checkStmt->execute([
                    $userId,
                    $event['subject_name'],
                    $event['day_of_week'],
                    $event['start_time'],
                    $event['end_time']
                ]);
            }

            if ($checkStmt->fetchColumn() > 0) {
                $duplicateCount++;
                continue;
            }

            // Insert sự kiện với cấu trúc bảng mới
            $insertStmt->execute([
                $userId,
                $event['subject_name'],
                $event['day_of_week'],
                $event['start_time'],
                $event['end_time'],
                $event['classroom'],
                $event['teacher'],
                $event['notes'],
                $event['start_date'],
                $event['end_date'],
                $event['ics_uid'] ?? null,
                $event['recurrence_rule'] ?? null,
                $event['excluded_dates'] ?? null
            ]);

            $successCount++;
        } catch (Exception $e) {
            $errorCount++;
        }
    }

    // Commit transaction
    $pdo->commit();

    // Xóa file upload
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Ghi log hoạt động
    $logMessage = "Import ICS: {$successCount} thành công, {$duplicateCount} trùng lặp, {$errorCount} lỗi";
    logActivity($userId, 'import_ics', $logMessage);

    // Trả về kết quả
    error_log("SUCCESS: Import completed - Total: " . count($events) . ", Success: $successCount, Duplicate: $duplicateCount, Error: $errorCount");
    echo json_encode([
        'success' => true,
        'message' => 'Import thành công',
        'data' => [
            'total_events' => count($events),
            'success_count' => $successCount,
            'duplicate_count' => $duplicateCount,
            'error_count' => $errorCount,
            'statistics' => $statistics
        ]
    ]);
} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }

    // Xóa file upload nếu có
    if (isset($filePath) && file_exists($filePath)) {
        unlink($filePath);
    }

    error_log("Lỗi import ICS: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi import: ' . $e->getMessage()
    ]);
}
