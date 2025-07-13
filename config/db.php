<?php

/**
 * Cấu hình kết nối cơ sở dữ liệu
 * File: config/db.php
 */

// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình ứng dụng
define('APP_NAME', 'Student Manager');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/StudentManager');

// Cấu hình upload
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif']);

// Cấu hình session
define('SESSION_LIFETIME', 3600); // 1 giờ

try {
    // Tạo kết nối PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // Tạo kết nối MySQLi (backup)
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Kiểm tra kết nối MySQLi
    if ($conn->connect_error) {
        throw new Exception("Kết nối thất bại: " . $conn->connect_error);
    }

    // Thiết lập charset cho MySQLi
    $conn->set_charset(DB_CHARSET);
} catch (PDOException $e) {
    // Log lỗi và hiển thị thông báo thân thiện
    error_log("Database connection error: " . $e->getMessage());
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}

/**
 * Hàm tiện ích để thực hiện truy vấn an toàn
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
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu");
    }
}

/**
 * Hàm lấy một bản ghi
 */
function fetchOne($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

/**
 * Hàm lấy nhiều bản ghi
 */
function fetchAll($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

/**
 * Hàm insert và trả về ID
 */
function insertAndGetId($query, $params = [])
{
    global $pdo;
    $stmt = executeQuery($query, $params);
    return $pdo->lastInsertId();
}

/**
 * Hàm kiểm tra và tạo thư mục upload cho user
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
 * Hàm validate file upload
 */
function validateUploadFile($file)
{
    $errors = [];

    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Lỗi upload file";
        return $errors;
    }

    // Kiểm tra kích thước file
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "File quá lớn. Kích thước tối đa: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB";
    }

    // Kiểm tra loại file
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ALLOWED_FILE_TYPES)) {
        $errors[] = "Loại file không được phép. Chỉ chấp nhận: " . implode(', ', ALLOWED_FILE_TYPES);
    }

    return $errors;
}

/**
 * Hàm format kích thước file
 */
function formatFileSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Hàm sanitize input
 */
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Hàm kiểm tra quyền truy cập file
 */
function checkFileAccess($filePath, $userId)
{
    // Kiểm tra file có thuộc về user không
    $userDir = UPLOAD_PATH . $userId;
    $realPath = realpath($filePath);
    $realUserDir = realpath($userDir);

    return $realPath && $realUserDir && strpos($realPath, $realUserDir) === 0;
}



// Thiết lập timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    // Thiết lập thời gian hết hạn session
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}
