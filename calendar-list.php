<?php
/**
 * Calendar List Page
 * Hiển thị danh sách lịch và cho phép chọn "Lịch học CTU"
 */

$page_title = 'Chọn lịch Google Calendar';
$current_page = 'calendar-list.php';

session_start();
require_once 'config/db.php';
require_once 'config/google.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra kết nối Google
if (!isset($_SESSION['google_access_token'])) {
    header('Location: calendar.php?message=' . urlencode('Vui lòng kết nối Google Calendar trước!') . '&type=warning');
    exit();
}

$calendars = [];
$error = '';

try {
    $service = getCalendarService();
    
    if (!$service) {
        throw new Exception('Không thể kết nối đến Google Calendar. Vui lòng đăng nhập lại.');
    }
    
    // Lấy danh sách lịch
    $calendarList = $service->calendarList->listCalendarList();
    $calendars = $calendarList->getItems();
    
} catch (Exception $e) {
    error_log('Calendar List Error: ' . $e->getMessage());
    $error = 'Lỗi khi tải danh sách lịch: ' . $e->getMessage();
}

// Xử lý chọn lịch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calendar_id'])) {
    $calendarId = $_POST['calendar_id'];
    $calendarName = $_POST['calendar_name'];
    
    // Lưu lịch đã chọn vào session
    $_SESSION['selected_calendar_id'] = $calendarId;
    $_SESSION['selected_calendar_name'] = $calendarName;
    
    header('Location: calendar-events.php?message=' . urlencode('Đã chọn lịch: ' . $calendarName) . '&type=success');
    exit();
}

include 'includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Chọn Google Calendar</h1>
                <p class="text-gray-600 mt-2">Chọn lịch "Lịch học CTU" hoặc lịch mà bạn muốn sử dụng</p>
            </div>
            <div class="flex space-x-3">
                <a href="calendar.php" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
                <a href="google-auth.php?disconnect=1" 
                   class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-unlink mr-2"></i>
                    Ngắt kết nối
                </a>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <div class="text-center">
            <a href="google-auth.php" 
               class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 inline-flex items-center">
                <i class="fas fa-redo mr-2"></i>
                Thử kết nối lại
            </a>
        </div>
    <?php elseif (empty($calendars)): ?>
        <div class="text-center py-16">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-calendar-times text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Không tìm thấy lịch nào</h3>
            <p class="text-gray-500 mb-4">
                Vui lòng tạo lịch "Lịch học CTU" trong Google Calendar trước khi sử dụng.
            </p>
            <a href="https://calendar.google.com" target="_blank" 
               class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 inline-flex items-center">
                <i class="fas fa-external-link-alt mr-2"></i>
                Mở Google Calendar
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($calendars as $calendar): ?>
                <?php 
                $calendarId = $calendar->getId();
                $calendarName = $calendar->getSummary();
                $isPrimary = $calendar->getPrimary();
                $accessRole = $calendar->getAccessRole();
                
                // Chỉ hiển thị lịch có quyền chỉnh sửa
                if (!in_array($accessRole, ['owner', 'writer'])) {
                    continue;
                }
                
                // Highlight lịch có tên chứa "CTU" hoặc "lịch học"
                $isRecommended = stripos($calendarName, 'ctu') !== false || 
                               stripos($calendarName, 'lịch học') !== false ||
                               stripos($calendarName, 'học tập') !== false;
                ?>
                <div class="bg-white border <?php echo $isRecommended ? 'border-green-300 bg-green-50' : 'border-gray-200'; ?> rounded-lg p-6 hover:shadow-lg transition-all duration-200">
                    <?php if ($isRecommended): ?>
                        <div class="flex items-center justify-between mb-3">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                <i class="fas fa-star mr-1"></i>
                                Được đề xuất
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 <?php echo $isPrimary ? 'text-blue-500' : 'text-gray-500'; ?>"></i>
                            <?php echo htmlspecialchars($calendarName); ?>
                        </h3>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <?php if ($isPrimary): ?>
                                <span class="flex items-center">
                                    <i class="fas fa-home mr-1 text-blue-500"></i>
                                    Lịch chính
                                </span>
                            <?php endif; ?>
                            
                            <span class="flex items-center">
                                <i class="fas fa-user-edit mr-1 text-green-500"></i>
                                <?php echo ucfirst($accessRole); ?>
                            </span>
                        </div>
                    </div>
                    
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="calendar_id" value="<?php echo htmlspecialchars($calendarId); ?>">
                        <input type="hidden" name="calendar_name" value="<?php echo htmlspecialchars($calendarName); ?>">
                        
                        <button type="submit" 
                                class="w-full <?php echo $isRecommended ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600'; ?> text-white py-2 px-4 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-check mr-2"></i>
                            Chọn lịch này
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-800 mb-2">Lưu ý quan trọng:</h4>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Chọn lịch "Lịch học CTU" nếu bạn đã tạo sẵn</li>
                        <li>• Chỉ hiển thị lịch mà bạn có quyền chỉnh sửa</li>
                        <li>• Sau khi chọn, bạn có thể xem và chỉnh sửa sự kiện trong lịch</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>