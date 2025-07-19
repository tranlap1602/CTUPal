<?php

/**
 * File: notes.php
 * Mục đích: Trang quản lý ghi chú học tập và cá nhân
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Ghi chú đơn giản với Tailwind CSS - Phiên bản không AJAX
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

// ==================== VALIDATION FUNCTIONS ====================

function validateNote($data)
{
    $errors = [];

    // Validate title
    if (empty($data['title'])) {
        $errors['title'] = 'Tiêu đề không được để trống';
    } elseif (strlen($data['title']) > 255) {
        $errors['title'] = 'Tiêu đề không được quá 255 ký tự';
    }

    // Validate content
    if (empty($data['content'])) {
        $errors['content'] = 'Nội dung không được để trống';
    } elseif (strlen($data['content']) > 10000) {
        $errors['content'] = 'Nội dung không được quá 10,000 ký tự';
    }

    // Validate category
    $validCategories = ['study', 'personal', 'work', 'idea', 'other'];
    if (!empty($data['category']) && !in_array($data['category'], $validCategories)) {
        $errors['category'] = 'Danh mục không hợp lệ';
    }

    return $errors;
}

function validateNoteId($id, $user_id)
{
    $errors = [];

    if (empty($id) || !is_numeric($id)) {
        $errors['id'] = 'ID ghi chú không hợp lệ';
    } else {
        $existingNote = fetchOne("SELECT id FROM notes WHERE id = ? AND user_id = ?", [$id, $user_id]);
        if (!$existingNote) {
            $errors['id'] = 'Không tìm thấy ghi chú hoặc bạn không có quyền';
        }
    }

    return $errors;
}

// ==================== FORM SUBMIT HANDLING ====================

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $errors = [];
    $success = '';

    try {
        switch ($action) {
            case 'add':
                // Validate input
                $errors = validateNote($_POST);

                if (empty($errors)) {
                    $title = sanitizeInput($_POST['title']);
                    $content = sanitizeInput($_POST['content']);
                    $category = $_POST['category'] ?? 'other';

                    $sql = "INSERT INTO notes (user_id, title, content, category) VALUES (?, ?, ?, ?)";
                    $params = [$user_id, $title, $content, $category];

                    $note_id = insertAndGetId($sql, $params);

                    if ($note_id) {
                        $success = 'Thêm ghi chú thành công';
                    } else {
                        $errors['database'] = 'Không thể thêm ghi chú';
                    }
                }
                break;

            case 'update':
                // Validate note ID
                $idErrors = validateNoteId($_POST['id'] ?? '', $user_id);
                $errors = array_merge($errors, $idErrors);

                // Validate note data
                $noteErrors = validateNote($_POST);
                $errors = array_merge($errors, $noteErrors);

                if (empty($errors)) {
                    $note_id = intval($_POST['id']);
                    $title = sanitizeInput($_POST['title']);
                    $content = sanitizeInput($_POST['content']);
                    $category = $_POST['category'] ?? 'other';

                    $sql = "UPDATE notes SET title = ?, content = ?, category = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
                    $params = [$title, $content, $category, $note_id, $user_id];

                    $result = executeQuery($sql, $params);

                    if ($result) {
                        $success = 'Cập nhật ghi chú thành công';
                    } else {
                        $errors['database'] = 'Không thể cập nhật ghi chú';
                    }
                }
                break;

            case 'delete':
                // Validate note ID
                $errors = validateNoteId($_POST['id'] ?? '', $user_id);

                if (empty($errors)) {
                    $note_id = intval($_POST['id']);

                    $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
                    $result = executeQuery($sql, [$note_id, $user_id]);

                    if ($result) {
                        $success = 'Xóa ghi chú thành công';
                    } else {
                        $errors['database'] = 'Không thể xóa ghi chú';
                    }
                }
                break;
        }
    } catch (Exception $e) {
        $errors['server'] = 'Lỗi server: ' . $e->getMessage();
    }

    // Redirect với messages
    $redirect_url = 'notes.php';

    // Giữ lại filter params
    if (!empty($_GET['category'])) {
        $redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . 'category=' . urlencode($_GET['category']);
    }

    // Add success message
    if (!empty($success)) {
        $redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . 'message=' . urlencode($success) . '&type=success';
    }

    // Add first error message (nếu có)
    if (!empty($errors)) {
        $firstError = reset($errors);
        $redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . 'message=' . urlencode($firstError) . '&type=error';
    }

    header('Location: ' . $redirect_url);
    exit();
}

// ==================== LOAD DATA ====================

// Lấy danh sách ghi chú
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM notes WHERE user_id = ?";
$params = [$user_id];

// Thêm điều kiện lọc theo danh mục
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";

$notes = fetchAll($sql, $params);

// Lấy ghi chú để sửa (nếu có)
$edit_note = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $note_id = intval($_GET['id']);
    $edit_note = fetchOne("SELECT * FROM notes WHERE id = ? AND user_id = ?", [$note_id, $user_id]);

    if (!$edit_note) {
        header('Location: notes.php?message=Không tìm thấy ghi chú&type=error');
        exit();
    }
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