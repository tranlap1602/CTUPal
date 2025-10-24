<?php

/**
 * File cấu hình Database - MẪU
 * 
 * Copy file này thành db.php và điều chỉnh thông tin phù hợp với môi trường của bạn
 * cp config/db.example.php config/db.php
 */

// ===== CẤU HÌNH DATABASE =====
$host = 'localhost';           // Địa chỉ MySQL server (localhost hoặc IP)
$dbname = 'student_manager';   // Tên database
$username = 'root';            // Username MySQL
$password = '';                // Password MySQL
$charset = 'utf8mb4';

// ===== CẤU HÌNH ỨNG DỤNG =====
define('APP_NAME', 'CTUPal');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/CTUPal');  // URL truy cập ứng dụng (không có / cuối)

// ===== CẤU HÌNH UPLOAD =====
define('UPLOAD_PATH', realpath(__DIR__ . '/../uploads/') . DIRECTORY_SEPARATOR);
define('MAX_FILE_SIZE', 20 * 1024 * 1024);  // 20MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif']);

// ===== KẾT NỐI DATABASE =====
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}

// ===== CÁC HÀM TIỆN ÍCH =====

/**
 * Thực thi truy vấn SQL
 */
function executeQuery($query, $params = [])
{
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $e->getMessage());
    }
}

/**
 * Lấy một bản ghi
 */
function fetchOne($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

/**
 * Lấy nhiều bản ghi
 */
function fetchAll($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

/**
 * Insert và trả về ID
 */
function insertAndGetId($query, $params = [])
{
    global $pdo;
    $stmt = executeQuery($query, $params);
    return $pdo->lastInsertId();
}

/**
 * Kiểm tra và tạo thư mục upload cho user
 */
function createUserUploadDir($userId)
{
    $userDir = UPLOAD_PATH . $userId;
    if (!is_dir($userDir)) {
        if (!mkdir($userDir, 0755, true)) {
            throw new Exception("Không thể tạo thư mục upload");
        }
    }
    return $userDir;
}

/**
 * Validate file upload
 */
function validateUploadFile($file)
{
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Lỗi upload file";
        return $errors;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "File quá lớn. Kích thước tối đa: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB";
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ALLOWED_FILE_TYPES)) {
        $errors[] = "Loại file không được phép. Chỉ chấp nhận: " . implode(', ', ALLOWED_FILE_TYPES);
    }

    return $errors;
}

/**
 * Kiểm tra quyền truy cập file
 */
function checkFileAccess($filePath, $userId)
{
    $userDir = UPLOAD_PATH . $userId;
    $realPath = realpath($filePath);
    $realUserDir = realpath($userDir);

    return $realPath && $realUserDir && strpos($realPath, $realUserDir) === 0;
}

/**
 * Làm sạch dữ liệu đầu vào
 */
function sanitizeInput($input)
{
    if (is_string($input)) {
        $input = trim($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $input = str_replace(chr(0), '', $input);
    }
    return $input;
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
