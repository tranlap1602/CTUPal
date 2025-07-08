<?php
/**
 * File: api/timetable-api.php
 * Mục đích: API endpoints để xử lý CRUD operations cho thời khóa biểu
 * Tác giả: Student Manager System
 */

session_start();
require_once '../config/db.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Set header cho JSON response
header('Content-Type: application/json; charset=utf-8');

// Lấy method và action từ request
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($action, $user_id);
            break;
        case 'POST':
            handlePostRequest($action, $user_id);
            break;
        case 'PUT':
            handlePutRequest($action, $user_id);
            break;
        case 'DELETE':
            handleDeleteRequest($action, $user_id);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method không được hỗ trợ']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}

/**
 * Xử lý GET requests
 */
function handleGetRequest($action, $user_id) {
    global $pdo;
    
    switch ($action) {
        case 'get-subject':
            $id = $_GET['id'] ?? 0;
            $query = "SELECT * FROM timetable WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id, $user_id]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($subject) {
                echo json_encode(['success' => true, 'data' => $subject]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy môn học']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }
}

/**
 * Xử lý POST requests (thêm mới)
 */
function handlePostRequest($action, $user_id) {
    global $pdo;
    
    switch ($action) {
        case 'add-subject':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate dữ liệu required
            $required_fields = ['subject_name', 'day_of_week', 'start_time', 'end_time'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Thiếu trường: $field"]);
                    return;
                }
            }
            
            // Thêm môn học mới
            $query = "INSERT INTO timetable (user_id, subject_name, subject_code, day_of_week, start_time, end_time, classroom, teacher, notes) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                $user_id,
                $data['subject_name'],
                $data['subject_code'] ?? null,
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $data['classroom'] ?? null,
                $data['teacher'] ?? null,
                $data['notes'] ?? null
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Thêm môn học thành công', 'id' => $pdo->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm môn học']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }
}

/**
 * Xử lý PUT requests (cập nhật)
 */
function handlePutRequest($action, $user_id) {
    global $pdo;
    
    switch ($action) {
        case 'update-subject':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? 0;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID môn học']);
                return;
            }
            
            // Kiểm tra quyền sở hữu
            $check_query = "SELECT id FROM timetable WHERE id = ? AND user_id = ?";
            $check_stmt = $pdo->prepare($check_query);
            $check_stmt->execute([$id, $user_id]);
            
            if (!$check_stmt->fetch()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Không có quyền sửa môn học này']);
                return;
            }
            
            // Cập nhật môn học
            $query = "UPDATE timetable SET 
                      subject_name = ?, subject_code = ?, day_of_week = ?, 
                      start_time = ?, end_time = ?, classroom = ?, teacher = ?, notes = ?,
                      updated_at = CURRENT_TIMESTAMP
                      WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                $data['subject_name'],
                $data['subject_code'] ?? null,
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $data['classroom'] ?? null,
                $data['teacher'] ?? null,
                $data['notes'] ?? null,
                $id,
                $user_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật môn học thành công']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật môn học']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }
}

/**
 * Xử lý DELETE requests
 */
function handleDeleteRequest($action, $user_id) {
    global $pdo;
    
    switch ($action) {
        case 'delete-subject':
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID môn học']);
                return;
            }
            
            // Kiểm tra quyền sở hữu và xóa
            $query = "DELETE FROM timetable WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([$id, $user_id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Xóa môn học thành công']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy môn học hoặc không có quyền xóa']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }
}
?> 