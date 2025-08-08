<?php
/**
 * Enhanced Calendar View Page
 * Hiển thị lịch với các chế độ xem ngày/tuần/tháng và điều hướng
 */

$page_title = 'Lịch học tương tác';
$current_page = 'calendar-view.php';

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

// Lấy tham số từ URL
$view = $_GET['view'] ?? 'week'; // day, week, month
$date = $_GET['date'] ?? date('Y-m-d');

// Validate view
if (!in_array($view, ['day', 'week', 'month'])) {
    $view = 'week';
}

// Validate và xử lý date
try {
    $currentDate = new DateTime($date);
} catch (Exception $e) {
    $currentDate = new DateTime();
}

$events = [];
$error = '';
$calendarName = $_SESSION['selected_calendar_name'] ?? 'Lịch đã chọn';

// Tính toán khoảng thời gian dựa trên view
function calculateTimeRange($date, $view) {
    $startDate = clone $date;
    $endDate = clone $date;
    
    switch ($view) {
        case 'day':
            $endDate->modify('+1 day');
            break;
        case 'week':
            // Đầu tuần là thứ 2
            $dayOfWeek = $startDate->format('N') - 1; // 0 = Monday
            $startDate->modify("-{$dayOfWeek} days");
            $endDate = clone $startDate;
            $endDate->modify('+7 days');
            break;
        case 'month':
            $startDate->modify('first day of this month');
            $endDate = clone $startDate;
            $endDate->modify('last day of this month')->modify('+1 day');
            break;
    }
    
    return [$startDate, $endDate];
}

// Lấy sự kiện từ Google Calendar
try {
    $service = getCalendarService();
    
    if (!$service) {
        throw new Exception('Không thể kết nối đến Google Calendar. Vui lòng đăng nhập lại.');
    }
    
    list($timeMin, $timeMax) = calculateTimeRange($currentDate, $view);
    
    $eventsList = $service->events->listEvents($_SESSION['selected_calendar_id'], [
        'timeMin' => $timeMin->format('c'),
        'timeMax' => $timeMax->format('c'),
        'maxResults' => 100,
        'singleEvents' => true,
        'orderBy' => 'startTime'
    ]);
    
    $events = $eventsList->getItems();
    
} catch (Exception $e) {
    error_log('Calendar View Error: ' . $e->getMessage());
    $error = 'Lỗi khi tải sự kiện: ' . $e->getMessage();
}

// Hàm tạo navigation URLs
function getNavigationUrl($view, $date, $direction) {
    $currentDate = new DateTime($date);
    
    switch ($view) {
        case 'day':
            $modifier = $direction === 'prev' ? '-1 day' : '+1 day';
            break;
        case 'week':
            $modifier = $direction === 'prev' ? '-1 week' : '+1 week';
            break;
        case 'month':
            $modifier = $direction === 'prev' ? '-1 month' : '+1 month';
            break;
    }
    
    $currentDate->modify($modifier);
    return 'calendar-view.php?view=' . $view . '&date=' . $currentDate->format('Y-m-d');
}

// Hàm format thời gian cho header
function getViewTitle($view, $date) {
    $currentDate = new DateTime($date);
    
    switch ($view) {
        case 'day':
            return $currentDate->format('d/m/Y - l');
        case 'week':
            $startOfWeek = clone $currentDate;
            $dayOfWeek = $currentDate->format('N') - 1;
            $startOfWeek->modify("-{$dayOfWeek} days");
            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('+6 days');
            return 'Tuần ' . $startOfWeek->format('d/m') . ' - ' . $endOfWeek->format('d/m/Y');
        case 'month':
            return $currentDate->format('F Y');
    }
}

include 'includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <!-- Header với điều khiển -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Lịch học tương tác</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <?php echo htmlspecialchars($calendarName); ?>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="calendar-list.php" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-list mr-2"></i>
                    Chọn lịch
                </a>
                <a href="add-event.php" 
                   class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-plus mr-2"></i>
                    Thêm sự kiện
                </a>
            </div>
        </div>
        
        <!-- Navigation Controls -->
        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center space-x-2">
                <a href="<?php echo getNavigationUrl($view, $date, 'prev'); ?>" 
                   class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition-all duration-200">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="calendar-view.php?view=<?php echo $view; ?>&date=<?php echo date('Y-m-d'); ?>" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all duration-200">
                    Hôm nay
                </a>
                <a href="<?php echo getNavigationUrl($view, $date, 'next'); ?>" 
                   class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition-all duration-200">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="text-lg font-semibold text-gray-800">
                <?php echo getViewTitle($view, $date); ?>
            </div>
            
            <!-- View Mode Buttons -->
            <div class="flex items-center space-x-1 bg-white rounded-lg p-1 border">
                <a href="calendar-view.php?view=day&date=<?php echo $date; ?>" 
                   class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 <?php echo $view === 'day' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                    Ngày
                </a>
                <a href="calendar-view.php?view=week&date=<?php echo $date; ?>" 
                   class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 <?php echo $view === 'week' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                    Tuần
                </a>
                <a href="calendar-view.php?view=month&date=<?php echo $date; ?>" 
                   class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 <?php echo $view === 'month' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                    Tháng
                </a>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php else: ?>
        <!-- Calendar View Content -->
        <div id="calendar-content" class="calendar-<?php echo $view; ?>">
            <?php 
            // Include the appropriate view template
            $viewFile = "views/calendar-{$view}-view.php";
            if (file_exists($viewFile)) {
                include $viewFile;
            } else {
                // Fallback to simple list view
                include 'views/calendar-list-view.php';
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- Calendar Styles -->
<style>
.calendar-day {
    max-width: 800px;
    margin: 0 auto;
}

.calendar-week {
    overflow-x: auto;
}

.calendar-month {
    overflow-x: auto;
}

.event-item {
    background: #3b82f6;
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin: 1px 0;
    cursor: pointer;
    transition: all 0.2s;
}

.event-item:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: #e5e7eb;
    border: 1px solid #e5e7eb;
}

.calendar-cell {
    background: white;
    min-height: 120px;
    padding: 8px;
    border: 1px solid #f3f4f6;
}

.calendar-cell.other-month {
    background: #f9fafb;
    color: #9ca3af;
}

.calendar-cell.today {
    background: #eff6ff;
    border-color: #3b82f6;
}

.time-slot {
    border-bottom: 1px solid #e5e7eb;
    min-height: 60px;
    padding: 8px;
    position: relative;
}

.time-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}
</style>

<?php include 'includes/footer.php'; ?>