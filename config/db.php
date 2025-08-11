<?php
//Kết nối database
$host = 'localhost';
$dbname = 'student_manager';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

define('APP_NAME', 'CTUPal');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/CTUPal');

// Cấu hình upload
define('UPLOAD_PATH', realpath(__DIR__ . '/../uploads/') . DIRECTORY_SEPARATOR);
define('MAX_FILE_SIZE', 20 * 1024 * 1024);
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif']);

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $username, $password);
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}

//Hàm truy vấn
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

//lấy một bản ghi
function fetchOne($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetch();
}

//lấy nhiều bản ghi
function fetchAll($query, $params = [])
{
    $stmt = executeQuery($query, $params);
    return $stmt->fetchAll();
}

//insert và trả về ID
function insertAndGetId($query, $params = [])
{
    global $pdo;
    $stmt = executeQuery($query, $params);
    return $pdo->lastInsertId();
}

//kiểm tra và tạo thư mục upload
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

//Hàm validate file upload
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
//làm sạch dữ liệu đầu vào
function sanitizeInput($input)
{
    if (is_string($input)) {
        $input = trim($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $input = str_replace(chr(0), '', $input);
    }
    return $input;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
