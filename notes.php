<?php

/**
 * File: notes.php
 * Mục đích: Trang quản lý ghi chú học tập và cá nhân + API endpoints
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Ghi chú, to-do list, nhắc nhở học tập với Tailwind CSS + CRUD API
 */

// Thiết lập biến cho header
$page_title = 'Ghi chú';
$current_page = 'notes.php';

// Bắt đầu session
session_start();
require_once 'config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? $_SESSION['username'];

// API Endpoints - xử lý AJAX requests
if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    try {
        switch ($_GET['api']) {
            case 'get_notes':
                handleGetNotes($user_id);
                break;

            case 'add_note':
                handleAddNote($user_id);
                break;

            case 'update_note':
                handleUpdateNote($user_id);
                break;

            case 'delete_note':
                handleDeleteNote($user_id);
                break;

            case 'search_notes':
                handleSearchNotes($user_id);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'API endpoint không tồn tại']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
    }
    exit();
}

// ==================== API FUNCTIONS ====================

/**
 * Lấy danh sách ghi chú với filter
 */
function handleGetNotes($user_id)
{
    $category = $_GET['category'] ?? '';
    $priority = $_GET['priority'] ?? '';
    $search = $_GET['search'] ?? '';

    $sql = "SELECT * FROM notes WHERE user_id = ?";
    $params = [$user_id];

    // Thêm điều kiện lọc
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    if (!empty($priority)) {
        $sql .= " AND priority = ?";
        $params[] = $priority;
    }

    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR content LIKE ? OR tags LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    $sql .= " ORDER BY created_at DESC";

    $notes = fetchAll($sql, $params);

    // Xử lý tags thành array
    foreach ($notes as &$note) {
        $note['tags'] = !empty($note['tags']) ? explode(',', $note['tags']) : [];
        $note['created_at_formatted'] = date('d/m/Y H:i', strtotime($note['created_at']));
    }

    echo json_encode(['success' => true, 'data' => $notes]);
}

/**
 * Thêm ghi chú mới
 */
function handleAddNote($user_id)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Phương thức không được phép']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    // Validation
    if (empty($input['title']) || empty($input['content'])) {
        echo json_encode(['success' => false, 'message' => 'Tiêu đề và nội dung không được để trống']);
        return;
    }

    $title = sanitizeInput($input['title']);
    $content = sanitizeInput($input['content']);
    $category = $input['category'] ?? 'other';
    $priority = $input['priority'] ?? 'medium';
    $tags = !empty($input['tags']) ? sanitizeInput($input['tags']) : '';

    // Validate category và priority
    $validCategories = ['study', 'personal', 'work', 'idea', 'todo', 'other'];
    $validPriorities = ['low', 'medium', 'high'];

    if (!in_array($category, $validCategories)) {
        $category = 'other';
    }

    if (!in_array($priority, $validPriorities)) {
        $priority = 'medium';
    }

    $sql = "INSERT INTO notes (user_id, title, content, category, priority, tags) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$user_id, $title, $content, $category, $priority, $tags];

    $note_id = insertAndGetId($sql, $params);

    if ($note_id) {
        // Lấy thông tin note vừa tạo
        $newNote = fetchOne("SELECT * FROM notes WHERE id = ?", [$note_id]);
        $newNote['tags'] = !empty($newNote['tags']) ? explode(',', $newNote['tags']) : [];
        $newNote['created_at_formatted'] = date('d/m/Y H:i', strtotime($newNote['created_at']));

        echo json_encode(['success' => true, 'message' => 'Thêm ghi chú thành công', 'data' => $newNote]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể thêm ghi chú']);
    }
}

/**
 * Cập nhật ghi chú
 */
function handleUpdateNote($user_id)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Phương thức không được phép']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID ghi chú không hợp lệ']);
        return;
    }

    $note_id = intval($input['id']);

    // Kiểm tra quyền sở hữu
    $existingNote = fetchOne("SELECT id FROM notes WHERE id = ? AND user_id = ?", [$note_id, $user_id]);
    if (!$existingNote) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy ghi chú hoặc bạn không có quyền']);
        return;
    }

    // Validation
    if (empty($input['title']) || empty($input['content'])) {
        echo json_encode(['success' => false, 'message' => 'Tiêu đề và nội dung không được để trống']);
        return;
    }

    $title = sanitizeInput($input['title']);
    $content = sanitizeInput($input['content']);
    $category = $input['category'] ?? 'other';
    $priority = $input['priority'] ?? 'medium';
    $tags = !empty($input['tags']) ? sanitizeInput($input['tags']) : '';

    // Validate category và priority
    $validCategories = ['study', 'personal', 'work', 'idea', 'todo', 'other'];
    $validPriorities = ['low', 'medium', 'high'];

    if (!in_array($category, $validCategories)) {
        $category = 'other';
    }

    if (!in_array($priority, $validPriorities)) {
        $priority = 'medium';
    }

    $sql = "UPDATE notes SET title = ?, content = ?, category = ?, priority = ?, tags = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
    $params = [$title, $content, $category, $priority, $tags, $note_id, $user_id];

    $result = executeQuery($sql, $params);

    if ($result) {
        // Lấy thông tin note đã cập nhật
        $updatedNote = fetchOne("SELECT * FROM notes WHERE id = ?", [$note_id]);
        $updatedNote['tags'] = !empty($updatedNote['tags']) ? explode(',', $updatedNote['tags']) : [];
        $updatedNote['created_at_formatted'] = date('d/m/Y H:i', strtotime($updatedNote['created_at']));

        echo json_encode(['success' => true, 'message' => 'Cập nhật ghi chú thành công', 'data' => $updatedNote]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật ghi chú']);
    }
}

/**
 * Xóa ghi chú
 */
function handleDeleteNote($user_id)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Phương thức không được phép']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID ghi chú không hợp lệ']);
        return;
    }

    $note_id = intval($input['id']);

    // Kiểm tra quyền sở hữu
    $existingNote = fetchOne("SELECT id FROM notes WHERE id = ? AND user_id = ?", [$note_id, $user_id]);
    if (!$existingNote) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy ghi chú hoặc bạn không có quyền']);
        return;
    }

    $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    $result = executeQuery($sql, [$note_id, $user_id]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Xóa ghi chú thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa ghi chú']);
    }
}

/**
 * Tìm kiếm ghi chú
 */
function handleSearchNotes($user_id)
{
    handleGetNotes($user_id); // Sử dụng lại logic get_notes với tham số search
}

// Include header
include 'includes/header.php';
?>

<!-- Main content -->
<div class="bg-white rounded-2xl shadow-lg p-8">
    <!-- Include notes view -->
    <?php include 'views/notes-view.php'; ?>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>