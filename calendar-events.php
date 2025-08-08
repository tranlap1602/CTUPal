<?php
/**
 * Calendar Events Page
 * Hiển thị danh sách sự kiện từ lịch đã chọn
 */

$page_title = 'Sự kiện lịch học';
$current_page = 'calendar-events.php';

session_start();
require_once 'config/db.php';
require_once 'config/google.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra kết nối Google và lịch đã chọn
if (!isset($_SESSION['google_access_token']) || !isset($_SESSION['selected_calendar_id'])) {
    header('Location: calendar.php?message=' . urlencode('Vui lòng kết nối và chọn lịch trước!') . '&type=warning');
    exit();
}

$events = [];
$error = '';
$calendarName = $_SESSION['selected_calendar_name'] ?? 'Lịch đã chọn';

try {
    $service = getCalendarService();
    
    if (!$service) {
        throw new Exception('Không thể kết nối đến Google Calendar. Vui lòng đăng nhập lại.');
    }
    
    // Lấy sự kiện từ hôm nay trở đi
    $timeMin = date('c'); // ISO 8601 format
    $timeMax = date('c', strtotime('+30 days')); // 30 ngày tới
    
    $eventsList = $service->events->listEvents($_SESSION['selected_calendar_id'], [
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'maxResults' => 50,
        'singleEvents' => true,
        'orderBy' => 'startTime'
    ]);
    
    $events = $eventsList->getItems();
    
} catch (Exception $e) {
    error_log('Calendar Events Error: ' . $e->getMessage());
    $error = 'Lỗi khi tải sự kiện: ' . $e->getMessage();
}

/**
 * Format thời gian sự kiện
 */
function formatEventTime($event) {
    $start = $event->getStart();
    $end = $event->getEnd();
    
    if ($start->getDate()) {
        // Sự kiện cả ngày
        $startDate = new DateTime($start->getDate());
        return $startDate->format('d/m/Y') . ' (Cả ngày)';
    } else {
        // Sự kiện có thời gian cụ thể
        $startTime = new DateTime($start->getDateTime());
        $endTime = new DateTime($end->getDateTime());
        
        if ($startTime->format('Y-m-d') === $endTime->format('Y-m-d')) {
            // Cùng ngày
            return $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('H:i');
        } else {
            // Khác ngày
            return $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('d/m/Y H:i');
        }
    }
}

/**
 * Lấy màu sắc cho sự kiện dựa trên nội dung
 */
function getEventColor($summary) {
    $summary = strtolower($summary);
    
    if (strpos($summary, 'thi') !== false || strpos($summary, 'kiểm tra') !== false) {
        return 'bg-red-100 border-red-300 text-red-800';
    } elseif (strpos($summary, 'lab') !== false || strpos($summary, 'thực hành') !== false) {
        return 'bg-green-100 border-green-300 text-green-800';
    } elseif (strpos($summary, 'seminar') !== false || strpos($summary, 'thảo luận') !== false) {
        return 'bg-purple-100 border-purple-300 text-purple-800';
    } else {
        return 'bg-blue-100 border-blue-300 text-blue-800';
    }
}

include 'includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Sự kiện lịch học</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <?php echo htmlspecialchars($calendarName); ?>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="calendar-list.php" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Chọn lịch khác
                </a>
                <button onclick="location.reload()" 
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Làm mới
                </button>
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
    <?php elseif (empty($events)): ?>
        <div class="text-center py-16">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-calendar-day text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Không có sự kiện nào</h3>
            <p class="text-gray-500 mb-4">
                Không tìm thấy sự kiện nào trong 30 ngày tới trong lịch này.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="https://calendar.google.com" target="_blank" 
                   class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Thêm sự kiện
                </a>
                <a href="calendar-list.php" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 inline-flex items-center">
                    <i class="fas fa-calendar mr-2"></i>
                    Chọn lịch khác
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($events as $event): ?>
                <?php 
                $eventId = $event->getId();
                $summary = $event->getSummary() ?: 'Không có tiêu đề';
                $description = $event->getDescription() ?: '';
                $location = $event->getLocation() ?: '';
                $colorClass = getEventColor($summary);
                $timeString = formatEventTime($event);
                ?>
                
                <div class="border <?php echo $colorClass; ?> rounded-lg p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-calendar-check text-lg mt-1"></i>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold mb-2">
                                        <?php echo htmlspecialchars($summary); ?>
                                    </h3>
                                    
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock w-4 mr-2"></i>
                                            <span><?php echo $timeString; ?></span>
                                        </div>
                                        
                                        <?php if ($location): ?>
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                                                <span><?php echo htmlspecialchars($location); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($description): ?>
                                            <div class="flex items-start">
                                                <i class="fas fa-sticky-note w-4 mr-2 mt-1"></i>
                                                <span class="line-clamp-2"><?php echo nl2br(htmlspecialchars($description)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ml-4 flex flex-col space-y-2">
                            <a href="edit-event.php?event_id=<?php echo urlencode($eventId); ?>" 
                               class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 text-center text-sm">
                                <i class="fas fa-edit mr-1"></i>
                                Chỉnh sửa
                            </a>
                            
                            <a href="https://calendar.google.com/calendar/u/0/r/eventedit/<?php echo urlencode($eventId); ?>" 
                               target="_blank"
                               class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 text-center text-sm">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Google
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-800 mb-2">Hướng dẫn sử dụng:</h4>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Nhấn "Chỉnh sửa" để cập nhật thông tin sự kiện</li>
                        <li>• Nhấn "Google" để mở sự kiện trong Google Calendar</li>
                        <li>• Hiển thị sự kiện trong 30 ngày tới</li>
                        <li>• Làm mới trang để cập nhật danh sách mới nhất</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php include 'includes/footer.php'; ?>