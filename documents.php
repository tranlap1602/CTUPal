<?php
$page_title = 'Tài liệu';
$current_page = 'documents.php';

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý form submit và GET requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['action']) && $_GET['action'] === 'download')) {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $errors = [];
    $success = '';

    try {
        switch ($action) {
            case 'upload':
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $category = $_POST['category'] ?? 'other';
                $subject = trim($_POST['subject'] ?? '');

                // Kiểm tra có file upload không
                if (!isset($_FILES['document_file']) || empty($_FILES['document_file']['name'][0])) {
                    $errors['file'] = 'Vui lòng chọn file!';
                    break;
                }

                // Tạo thư mục cho user
                $user_folder = createUserUploadDir($user_id);

                // Xử lý upload
                $uploaded_files = [];
                $files = $_FILES['document_file'];

                for ($i = 0; $i < count($files['name']); $i++) {
                    $file_name = $files['name'][$i];
                    $file_tmp = $files['tmp_name'][$i];
                    $file_size = $files['size'][$i];

                    if (empty($file_name)) continue;

                    // Kiểm tra kích thước
                    if ($file_size > MAX_FILE_SIZE) {
                        $errors['file'] = "File '$file_name' quá lớn! Tối đa " . (MAX_FILE_SIZE / 1024 / 1024) . "MB.";
                        break;
                    }

                    // Kiểm tra loại file
                    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    if (!in_array($file_extension, ALLOWED_FILE_TYPES)) {
                        $errors['file'] = "File '$file_name' không được hỗ trợ! Chỉ chấp nhận: " . implode(', ', ALLOWED_FILE_TYPES);
                        break;
                    }

                    // Tạo tên file mới
                    $timestamp = date('Ymd_His');
                    $random = rand(1000, 9999);
                    $safe_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
                    $new_filename = $safe_filename . '_' . $timestamp . '_' . $random . '.' . $file_extension;
                    $file_path = "$user_folder/$new_filename";

                    // Di chuyển file
                    if (!move_uploaded_file($file_tmp, $file_path)) {
                        $errors['file'] = "Không thể lưu file: $file_name";
                        break;
                    }

                    // Lưu vào database
                    $relative_path = "uploads/$user_id/$new_filename";
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
                            $uploaded_files[] = $file_name;
                        }
                    } catch (Exception $e) {
                        unlink($file_path);
                        $errors['database'] = 'Lỗi database: ' . $e->getMessage();
                        break;
                    }
                }

                if (empty($errors) && !empty($uploaded_files)) {
                    $success = 'Upload thành công ' . count($uploaded_files) . ' file!';
                }
                break;

            case 'delete':
                $document_id = intval($_POST['document_id'] ?? 0);

                if ($document_id <= 0) {
                    $errors['id'] = 'ID tài liệu không hợp lệ';
                    break;
                }

                // Kiểm tra quyền sở hữu
                $check_sql = "SELECT file_path FROM documents WHERE doc_id = ? AND user_id = ?";
                $document = fetchOne($check_sql, [$document_id, $user_id]);

                if (!$document) {
                    $errors['id'] = 'Không tìm thấy tài liệu hoặc không có quyền xóa!';
                    break;
                }

                // Xóa file vật lý
                $relative_path = $document['file_path'];
                $file_path = UPLOAD_PATH . str_replace('uploads/', '', $relative_path);
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Xóa record từ database
                $delete_sql = "DELETE FROM documents WHERE doc_id = ? AND user_id = ?";
                $result = executeQuery($delete_sql, [$document_id, $user_id]);

                if ($result) {
                    $success = 'Xóa tài liệu thành công!';
                } else {
                    $errors['database'] = 'Không thể xóa tài liệu';
                }
                break;

            case 'download':
                $document_id = intval($_GET['id'] ?? 0);

                if ($document_id <= 0) {
                    die('ID tài liệu không hợp lệ!');
                }

                // Lấy thông tin tài liệu
                $sql = "SELECT * FROM documents WHERE doc_id = ? AND user_id = ?";
                $document = fetchOne($sql, [$document_id, $user_id]);

                if (!$document) {
                    die('Không tìm thấy tài liệu!');
                }

                // Sử dụng UPLOAD_PATH trực tiếp
                $relative_path = $document['file_path'];
                $file_path = UPLOAD_PATH . str_replace('uploads/', '', $relative_path);

                // Kiểm tra file tồn tại
                if (!file_exists($file_path)) {
                    die('File không tồn tại! Đường dẫn: ' . $file_path);
                }

                // Thiết lập headers download
                $file_name = $document['file_name'];
                $file_size = filesize($file_path);

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $file_name . '"');
                header('Content-Length: ' . $file_size);
                header('Cache-Control: no-cache');

                readfile($file_path);
                exit();
                break;
        }
    } catch (Exception $e) {
        $errors['server'] = 'Lỗi server: ' . $e->getMessage();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $params = [];
        if (!empty($_GET['category'])) $params['category'] = $_GET['category'];
        if (!empty($_GET['subject'])) $params['subject'] = $_GET['subject'];

        if (!empty($success)) {
            $params['message'] = $success;
            $params['type'] = 'success';
        } elseif (!empty($errors)) {
            $params['message'] = reset($errors);
            $params['type'] = 'error';
        }

        $redirect_url = 'documents.php';
        if (!empty($params)) {
            $redirect_url .= '?' . http_build_query($params);
        }

        header('Location: ' . $redirect_url);
        exit();
    }
}

// Lấy tham số bộ lọc
$category_filter = $_GET['category'] ?? '';
$subject_filter = $_GET['subject'] ?? '';

// Lấy danh sách tài liệu
$sql = "SELECT * FROM documents WHERE user_id = ?";
$params = [$user_id];

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}

if (!empty($subject_filter)) {
    $sql .= " AND subject = ?";
    $params[] = $subject_filter;
}

$sql .= " ORDER BY created_at DESC";
$documents = fetchAll($sql, $params);

// Lấy danh sách subjects
$subjects_sql = "SELECT DISTINCT subject FROM documents WHERE user_id = ? AND subject IS NOT NULL AND subject != '' ORDER BY subject";
$subjects = fetchAll($subjects_sql, [$user_id]);

include 'includes/header.php';
?>

<div class="bg-white rounded-2xl shadow-lg p-8">
    <?php include 'views/documents-view.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>