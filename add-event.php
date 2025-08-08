<?php
/**
 * Add Event Page
 * Thêm sự kiện mới vào Google Calendar
 */

$page_title = 'Thêm sự kiện mới';
$current_page = 'add-event.php';

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

$error = '';
$success = '';

// Lấy thông tin từ URL nếu có
$defaultDate = $_GET['date'] ?? date('Y-m-d');
$defaultStart = $_GET['start'] ?? '';

// Parse default start time if provided
$defaultStartDate = $defaultDate;
$defaultStartTime = '09:00';
$defaultEndTime = '10:00';

if ($defaultStart) {
    $startDateTime = new DateTime($defaultStart);
    $defaultStartDate = $startDateTime->format('Y-m-d');
    $defaultStartTime = $startDateTime->format('H:i');
    
    $endDateTime = clone $startDateTime;
    $endDateTime->modify('+1 hour');
    $defaultEndTime = $endDateTime->format('H:i');
}

// Xử lý form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service = getCalendarService();
        
        if (!$service) {
            throw new Exception('Không thể kết nối đến Google Calendar. Vui lòng đăng nhập lại.');
        }
        
        // Lấy dữ liệu từ form
        $title = trim($_POST['title']);
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $isAllDay = isset($_POST['all_day']);
        
        if (empty($title)) {
            throw new Exception('Vui lòng nhập tiêu đề sự kiện');
        }
        
        // Tạo event object
        $event = new Google\Service\Calendar\Event();
        $event->setSummary($title);
        
        if ($description) {
            $event->setDescription($description);
        }
        
        if ($location) {
            $event->setLocation($location);
        }
        
        // Xử lý thời gian
        if ($isAllDay) {
            // All-day event
            $start = new Google\Service\Calendar\EventDateTime();
            $start->setDate($startDate);
            $start->setTimeZone('Asia/Ho_Chi_Minh');
            $event->setStart($start);
            
            // End date for all-day events should be the next day
            $endDateTime = new DateTime($endDate);
            $endDateTime->modify('+1 day');
            $end = new Google\Service\Calendar\EventDateTime();
            $end->setDate($endDateTime->format('Y-m-d'));
            $end->setTimeZone('Asia/Ho_Chi_Minh');
            $event->setEnd($end);
        } else {
            // Timed event
            $startTime = $_POST['start_time'];
            $endTime = $_POST['end_time'];
            
            if (empty($startTime) || empty($endTime)) {
                throw new Exception('Vui lòng nhập thời gian bắt đầu và kết thúc');
            }
            
            $startDateTime = $startDate . 'T' . $startTime . ':00';
            $endDateTime = $endDate . 'T' . $endTime . ':00';
            
            // Validate time
            $start_dt = new DateTime($startDateTime);
            $end_dt = new DateTime($endDateTime);
            
            if ($end_dt <= $start_dt) {
                throw new Exception('Thời gian kết thúc phải sau thời gian bắt đầu');
            }
            
            $start = new Google\Service\Calendar\EventDateTime();
            $start->setDateTime($startDateTime);
            $start->setTimeZone('Asia/Ho_Chi_Minh');
            $event->setStart($start);
            
            $end = new Google\Service\Calendar\EventDateTime();
            $end->setDateTime($endDateTime);
            $end->setTimeZone('Asia/Ho_Chi_Minh');
            $event->setEnd($end);
        }
        
        // Tạo sự kiện
        $createdEvent = $service->events->insert($_SESSION['selected_calendar_id'], $event);
        
        $success = 'Thêm sự kiện thành công!';
        
        // Chuyển hướng về calendar view
        header('Location: calendar-view.php?view=week&date=' . $startDate . '&message=' . urlencode($success) . '&type=success');
        exit();
        
    } catch (Exception $e) {
        error_log('Add Event Error: ' . $e->getMessage());
        $error = 'Lỗi khi thêm sự kiện: ' . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Thêm sự kiện mới</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Tạo sự kiện trong lịch: <?php echo htmlspecialchars($_SESSION['selected_calendar_name'] ?? 'Lịch đã chọn'); ?>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="calendar-view.php?view=week&date=<?php echo $defaultStartDate; ?>" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại lịch
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
        </div>
    <?php endif; ?>

    <!-- Event Form -->
    <form method="POST" class="space-y-6">
        <!-- Event Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-heading mr-2"></i>
                Tiêu đề sự kiện *
            </label>
            <input type="text" id="title" name="title" required
                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                   placeholder="Nhập tiêu đề sự kiện..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
        </div>

        <!-- All Day Event Toggle -->
        <div class="flex items-center">
            <input type="checkbox" id="all_day" name="all_day" 
                   <?php echo isset($_POST['all_day']) ? 'checked' : ''; ?>
                   onchange="toggleAllDay(this.checked)"
                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
            <label for="all_day" class="ml-2 text-sm font-medium text-gray-700">
                Sự kiện cả ngày
            </label>
        </div>

        <!-- Date and Time -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Start Date/Time -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Bắt đầu
                </label>
                <div class="space-y-2">
                    <input type="date" name="start_date" required
                           value="<?php echo htmlspecialchars($_POST['start_date'] ?? $defaultStartDate); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    <input type="time" name="start_time" id="start_time"
                           value="<?php echo htmlspecialchars($_POST['start_time'] ?? $defaultStartTime); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                </div>
            </div>

            <!-- End Date/Time -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Kết thúc
                </label>
                <div class="space-y-2">
                    <input type="date" name="end_date" required
                           value="<?php echo htmlspecialchars($_POST['end_date'] ?? $defaultStartDate); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    <input type="time" name="end_time" id="end_time"
                           value="<?php echo htmlspecialchars($_POST['end_time'] ?? $defaultEndTime); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                </div>
            </div>
        </div>

        <!-- Location -->
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Địa điểm
            </label>
            <input type="text" id="location" name="location"
                   value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                   placeholder="Nhập địa điểm tổ chức..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-sticky-note mr-2"></i>
                Mô tả
            </label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Nhập mô tả chi tiết về sự kiện..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 resize-vertical"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="calendar-view.php?view=week&date=<?php echo $defaultStartDate; ?>"
               class="px-6 py-2 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200">
                Hủy
            </a>
            <button type="submit"
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-save mr-2"></i>
                <span>Tạo sự kiện</span>
            </button>
        </div>
    </form>
</div>

<!-- Quick Event Templates -->
<div class="bg-white rounded-lg shadow-md p-8 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-magic mr-2"></i>
        Mẫu sự kiện nhanh
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button type="button" onclick="applyTemplate('class')"
                class="p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-left">
            <div class="font-medium text-blue-600">Lớp học</div>
            <div class="text-sm text-gray-600">Môn học, phòng học, thời gian 90 phút</div>
        </button>
        <button type="button" onclick="applyTemplate('exam')"
                class="p-4 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-all duration-200 text-left">
            <div class="font-medium text-red-600">Thi/Kiểm tra</div>
            <div class="text-sm text-gray-600">Kỳ thi, phòng thi, thời gian 120 phút</div>
        </button>
        <button type="button" onclick="applyTemplate('meeting')"
                class="p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all duration-200 text-left">
            <div class="font-medium text-green-600">Họp/Seminar</div>
            <div class="text-sm text-gray-600">Cuộc họp, hội thảo, thời gian 60 phút</div>
        </button>
    </div>
</div>

<script>
function toggleAllDay(isAllDay) {
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    
    if (isAllDay) {
        startTime.style.display = 'none';
        endTime.style.display = 'none';
        startTime.removeAttribute('required');
        endTime.removeAttribute('required');
    } else {
        startTime.style.display = 'block';
        endTime.style.display = 'block';
        startTime.setAttribute('required', 'required');
        endTime.setAttribute('required', 'required');
    }
}

function applyTemplate(type) {
    const titleField = document.getElementById('title');
    const locationField = document.getElementById('location');
    const descriptionField = document.getElementById('description');
    const allDayField = document.getElementById('all_day');
    const startTimeField = document.getElementById('start_time');
    const endTimeField = document.getElementById('end_time');
    
    // Reset all day
    allDayField.checked = false;
    toggleAllDay(false);
    
    switch (type) {
        case 'class':
            titleField.value = 'Lớp học: ';
            locationField.value = 'Phòng ';
            descriptionField.value = 'Môn học: \nGiảng viên: \nGhi chú: ';
            // Set 90 minutes duration
            if (startTimeField.value) {
                const start = new Date('2000-01-01T' + startTimeField.value);
                const end = new Date(start.getTime() + 90 * 60000); // 90 minutes
                endTimeField.value = end.toTimeString().substr(0, 5);
            }
            break;
            
        case 'exam':
            titleField.value = 'Thi: ';
            locationField.value = 'Phòng thi ';
            descriptionField.value = 'Môn thi: \nHình thức: \nTài liệu: \nGhi chú: ';
            // Set 120 minutes duration
            if (startTimeField.value) {
                const start = new Date('2000-01-01T' + startTimeField.value);
                const end = new Date(start.getTime() + 120 * 60000); // 120 minutes
                endTimeField.value = end.toTimeString().substr(0, 5);
            }
            break;
            
        case 'meeting':
            titleField.value = 'Họp: ';
            locationField.value = 'Phòng họp ';
            descriptionField.value = 'Chủ đề: \nNgười tham gia: \nNội dung: ';
            // Set 60 minutes duration
            if (startTimeField.value) {
                const start = new Date('2000-01-01T' + startTimeField.value);
                const end = new Date(start.getTime() + 60 * 60000); // 60 minutes
                endTimeField.value = end.toTimeString().substr(0, 5);
            }
            break;
    }
    
    // Focus on title field
    titleField.focus();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    toggleAllDay(document.getElementById('all_day').checked);
});
</script>

<?php include 'includes/footer.php'; ?>