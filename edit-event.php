<?php
/**
 * Edit Event Page
 * Chỉnh sửa sự kiện trong Google Calendar
 */

$page_title = 'Chỉnh sửa sự kiện';
$current_page = 'edit-event.php';

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

// Kiểm tra event_id
if (!isset($_GET['event_id'])) {
    header('Location: calendar-events.php?message=' . urlencode('Không tìm thấy sự kiện!') . '&type=error');
    exit();
}

$eventId = $_GET['event_id'];
$event = null;
$error = '';
$success = '';

try {
    $service = getCalendarService();
    
    if (!$service) {
        throw new Exception('Không thể kết nối đến Google Calendar. Vui lòng đăng nhập lại.');
    }
    
    // Lấy thông tin sự kiện
    $event = $service->events->get($_SESSION['selected_calendar_id'], $eventId);
    
} catch (Exception $e) {
    error_log('Get Event Error: ' . $e->getMessage());
    header('Location: calendar-events.php?message=' . urlencode('Lỗi khi tải sự kiện: ' . $e->getMessage()) . '&type=error');
    exit();
}

// Xử lý cập nhật sự kiện
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $allDay = isset($_POST['all_day']);
        
        // Validation
        if (empty($summary)) {
            throw new Exception('Tên môn học không được để trống');
        }
        
        if (!$allDay) {
            if (empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)) {
                throw new Exception('Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc');
            }
        } else {
            if (empty($startDate) || empty($endDate)) {
                throw new Exception('Vui lòng nhập ngày bắt đầu và kết thúc');
            }
        }
        
        // Cập nhật thông tin sự kiện
        $event->setSummary($summary);
        $event->setDescription($description);
        $event->setLocation($location);
        
        // Cập nhật thời gian
        $start = new Google\Service\Calendar\EventDateTime();
        $end = new Google\Service\Calendar\EventDateTime();
        
        if ($allDay) {
            // Sự kiện cả ngày
            $start->setDate($startDate);
            $end->setDate(date('Y-m-d', strtotime($endDate . ' +1 day'))); // End date phải +1 ngày cho all-day events
        } else {
            // Sự kiện có thời gian cụ thể
            $startDateTime = $startDate . 'T' . $startTime . ':00';
            $endDateTime = $endDate . 'T' . $endTime . ':00';
            
            $start->setDateTime($startDateTime);
            $start->setTimeZone('Asia/Ho_Chi_Minh');
            
            $end->setDateTime($endDateTime);
            $end->setTimeZone('Asia/Ho_Chi_Minh');
        }
        
        $event->setStart($start);
        $event->setEnd($end);
        
        // Gửi cập nhật lên Google Calendar
        $updatedEvent = $service->events->update($_SESSION['selected_calendar_id'], $eventId, $event);
        
        $success = 'Cập nhật sự kiện thành công!';
        
        // Chuyển hướng sau 2 giây
        echo "<script>
            setTimeout(function() {
                window.location.href = 'calendar-events.php?message=" . urlencode('Cập nhật sự kiện thành công!') . "&type=success';
            }, 2000);
        </script>";
        
    } catch (Exception $e) {
        error_log('Update Event Error: ' . $e->getMessage());
        $error = 'Lỗi khi cập nhật sự kiện: ' . $e->getMessage();
    }
}

// Chuẩn bị dữ liệu form
$summary = $event->getSummary() ?? '';
$description = $event->getDescription() ?? '';
$location = $event->getLocation() ?? '';

$start = $event->getStart();
$end = $event->getEnd();

$isAllDay = $start->getDate() !== null;

if ($isAllDay) {
    $startDate = $start->getDate();
    $endDate = date('Y-m-d', strtotime($end->getDate() . ' -1 day')); // Trừ 1 ngày vì Google +1
    $startTime = '';
    $endTime = '';
} else {
    $startDateTime = new DateTime($start->getDateTime());
    $endDateTime = new DateTime($end->getDateTime());
    
    $startDate = $startDateTime->format('Y-m-d');
    $startTime = $startDateTime->format('H:i');
    $endDate = $endDateTime->format('Y-m-d');
    $endTime = $endDateTime->format('H:i');
}

include 'includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa sự kiện</h1>
                <p class="text-gray-600 mt-2">Cập nhật thông tin môn học và lịch học</p>
            </div>
            <div class="flex space-x-3">
                <a href="calendar-events.php" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($success); ?>
            <div class="mt-2">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Đang chuyển hướng...
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tên môn học -->
            <div class="md:col-span-2">
                <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-book mr-2"></i>
                    Tên môn học *
                </label>
                <input type="text" 
                       id="summary" 
                       name="summary" 
                       value="<?php echo htmlspecialchars($summary); ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Ví dụ: Lập trình Web">
            </div>

            <!-- Phòng học -->
            <div class="md:col-span-2">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Phòng học
                </label>
                <input type="text" 
                       id="location" 
                       name="location" 
                       value="<?php echo htmlspecialchars($location); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Ví dụ: A1.101">
            </div>

            <!-- Checkbox cả ngày -->
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" 
                           id="all_day" 
                           name="all_day" 
                           <?php echo $isAllDay ? 'checked' : ''; ?>
                           onchange="toggleTimeFields()"
                           class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Sự kiện cả ngày</span>
                </label>
            </div>

            <!-- Ngày bắt đầu -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2"></i>
                    Ngày bắt đầu *
                </label>
                <input type="date" 
                       id="start_date" 
                       name="start_date" 
                       value="<?php echo $startDate; ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Giờ bắt đầu -->
            <div id="start_time_field">
                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-2"></i>
                    Giờ bắt đầu *
                </label>
                <input type="time" 
                       id="start_time" 
                       name="start_time" 
                       value="<?php echo $startTime; ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Ngày kết thúc -->
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2"></i>
                    Ngày kết thúc *
                </label>
                <input type="date" 
                       id="end_date" 
                       name="end_date" 
                       value="<?php echo $endDate; ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Giờ kết thúc -->
            <div id="end_time_field">
                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-2"></i>
                    Giờ kết thúc *
                </label>
                <input type="time" 
                       id="end_time" 
                       name="end_time" 
                       value="<?php echo $endTime; ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Ghi chú -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>
                    Ghi chú
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Thông tin bổ sung về môn học..."><?php echo htmlspecialchars($description); ?></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="calendar-events.php" 
               class="px-6 py-2 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200">
                Hủy
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center">
                <i class="fas fa-save mr-2"></i>
                Cập nhật sự kiện
            </button>
        </div>
    </form>
</div>

<script>
function toggleTimeFields() {
    const allDayCheckbox = document.getElementById('all_day');
    const startTimeField = document.getElementById('start_time_field');
    const endTimeField = document.getElementById('end_time_field');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    if (allDayCheckbox.checked) {
        startTimeField.style.display = 'none';
        endTimeField.style.display = 'none';
        startTimeInput.removeAttribute('required');
        endTimeInput.removeAttribute('required');
    } else {
        startTimeField.style.display = 'block';
        endTimeField.style.display = 'block';
        startTimeInput.setAttribute('required', 'required');
        endTimeInput.setAttribute('required', 'required');
    }
}

// Khởi tạo trạng thái ban đầu
document.addEventListener('DOMContentLoaded', function() {
    toggleTimeFields();
});
</script>

<?php include 'includes/footer.php'; ?>