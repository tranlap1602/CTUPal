<?php
$page_title = 'Ghi chú';
$current_page = 'notes.php';

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? 'User';

function validateNote($data)
{
    $errors = [];

    if (empty($data['title'])) {
        $errors['title'] = 'Tiêu đề không được để trống';
    } elseif (strlen($data['title']) > 255) {
        $errors['title'] = 'Tiêu đề không được quá 255 ký tự';
    }

    if (empty($data['content'])) {
        $errors['content'] = 'Nội dung không được để trống';
    } elseif (strlen($data['content']) > 10000) {
        $errors['content'] = 'Nội dung không được quá 10,000 ký tự';
    }

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
        $existingNote = fetchOne("SELECT note_id FROM notes WHERE note_id = ? AND user_id = ?", [$id, $user_id]);
        if (!$existingNote) {
            $errors['id'] = 'Không tìm thấy ghi chú hoặc bạn không có quyền';
        }
    }

    return $errors;
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $errors = [];
    $success = '';

    try {
        switch ($action) {
            case 'add':
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

                $idErrors = validateNoteId($_POST['id'] ?? '', $user_id);
                $errors = array_merge($errors, $idErrors);

                $noteErrors = validateNote($_POST);
                $errors = array_merge($errors, $noteErrors);

                if (empty($errors)) {
                    $note_id = intval($_POST['id']);
                    $title = sanitizeInput($_POST['title']);
                    $content = sanitizeInput($_POST['content']);
                    $category = $_POST['category'] ?? 'other';

                    $sql = "UPDATE notes SET title = ?, content = ?, category = ?, updated_at = NOW() WHERE note_id = ? AND user_id = ?";
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
                $errors = validateNoteId($_POST['id'] ?? '', $user_id);

                if (empty($errors)) {
                    $note_id = intval($_POST['id']);

                    $sql = "DELETE FROM notes WHERE note_id = ? AND user_id = ?";
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

    $params = [];
    if (!empty($_GET['category'])) $params['category'] = $_GET['category'];

    if (!empty($success)) {
        $params['message'] = $success;
        $params['type'] = 'success';
    } elseif (!empty($errors)) {
        $params['message'] = reset($errors);
        $params['type'] = 'error';
    }

    $redirect_url = 'notes.php';
    if (!empty($params)) {
        $redirect_url .= '?' . http_build_query($params);
    }

    header('Location: ' . $redirect_url);
    exit();
}

// lấy danh sách ghi chú
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM notes WHERE user_id = ?";
$params = [$user_id];

// lọc theo danh mục
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";

$notes = fetchAll($sql, $params);

// Lấy ghi chú để sửa
$edit_note = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $note_id = intval($_GET['id']);
    $edit_note = fetchOne("SELECT * FROM notes WHERE note_id = ? AND user_id = ?", [$note_id, $user_id]);

    if (!$edit_note) {
        header('Location: notes.php?message=Không tìm thấy ghi chú&type=error');
        exit();
    }
}

include 'includes/header.php';
?>

<div class="bg-white rounded-2xl shadow-lg p-8">
    <?php include 'views/notes-view.php'; ?>
</div>

<?php
include 'includes/footer.php';
?>