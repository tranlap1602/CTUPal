<?php
// Thiết lập biến cho header
$page_title = 'Lịch học';
$current_page = 'calendar.php';

// Bắt đầu session và kiểm tra đăng nhập
session_start();
require_once 'config/db.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin user hiện tại từ session
$user_id = $_SESSION['user_id'];
// Xử lý cập nhật Calendar ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calendar_id'])) {
    $calendar_id = trim($_POST['calendar_id']);
    // Cập nhật trực tiếp vào bảng users
    executeQuery("UPDATE users SET google_calendar_id = ? WHERE id = ?", [$calendar_id, $user_id]);
    // Redirect để tránh form resubmission và hiển thị alert
    header('Location: calendar.php?success=1');
    exit();
}

// Lấy Calendar ID hiện tại của user
$user = fetchOne("SELECT google_calendar_id FROM users WHERE id = ?", [$user_id]);
$current_calendar_id = $user['google_calendar_id'] ?? '';

// Include header
include 'includes/header.php';
?>

<!-- Main content area -->
<div class="bg-white rounded-lg shadow-md p-8">
    <!-- Header section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                    Lịch học của bạn
                </h1>
            </div>
            <div class="flex space-x-3">
                <button onclick="showCalendarSettings()"
                    class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
                    <i class="fas fa-cog mr-2"></i>
                    Cài đặt lịch
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar container -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <!-- Calendar header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calendar-week text-2xl mr-3"></i>
                    <h2 class="text-xl font-bold">Google Calendar</h2>
                </div>
                <div class="text-sm opacity-90">
                    <i class="fas fa-clock mr-1"></i>
                    Múi giờ: Asia/Ho_Chi_Minh
                </div>
            </div>
        </div>

        <!-- Calendar content -->
        <?php if ($current_calendar_id): ?>
            <!-- Hiển thị lịch nếu có Calendar ID -->
            <div class="relative">
                <!-- Loading indicator -->
                <div id="calendar-loading" class="absolute inset-0 bg-gray-100 flex items-center justify-center z-10">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Đang tải lịch...</p>
                    </div>
                </div>

                <!-- Google Calendar iframe -->
                <iframe
                    id="google-calendar"
                    src="https://calendar.google.com/calendar/embed?mode=WEEK&wkst=2&src=<?php echo urlencode($current_calendar_id); ?>&ctz=Asia%2FHo_Chi_Minh"
                    style="border:solid 1px #777; width: 100%; height: 900px;"
                    frameborder="0"
                    scrolling="no"
                    onload="hideLoading()">
                </iframe>
            </div>
        <?php else: ?>
            <!-- Hiển thị hướng dẫn nếu chưa có Calendar ID -->
            <div class="flex items-center justify-center py-16 px-8">
                <div class="text-center max-w-md">
                    <div class="text-gray-400 mb-6">
                        <i class="fas fa-calendar-plus text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-4">Chưa có lịch học</h3>
                    <p class="text-gray-500 mb-6">
                        Để xem lịch học của bạn, hãy cài đặt Google Calendar ID.
                        Bạn có thể sử dụng lịch cá nhân hoặc lịch lớp học.
                    </p>
                    <button onclick="showCalendarSettings()"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Thêm lịch học
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal cài đặt Calendar -->
<div id="calendar-settings-modal" class="hidden fixed inset-0 z-50 bg-black/10 backdrop-blur-sm">
    <div class="flex items-center justify-center p-4 h-full">
        <div class="bg-white rounded-xl max-w-md w-full shadow-xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        Cài đặt lịch
                    </h3>
                    <button onclick="hideCalendarSettings()" class="text-gray-500 hover:text-gray-700 p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="calendar_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2"></i>
                            Google Calendar ID
                        </label>
                        <div class="relative">
                            <input type="text"
                                id="calendar_id"
                                name="calendar_id"
                                value="<?php echo htmlspecialchars($current_calendar_id); ?>"
                                placeholder="your_email@gmail.com hoặc calendar_id@group.calendar.google.com"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <button type="button"
                                onclick="pasteCalendarId()"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-100 hover:bg-blue-200 text-blue-600 px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
                                title="Dán từ clipboard">
                                <i class="fas fa-paste"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Hướng dẫn lấy Calendar ID
                        </h4>
                        <ol class="text-sm text-blue-700 space-y-1">
                            <li>1. Mở <a href="https://calendar.google.com" target="_blank" class="underline">Google Calendar</a></li>
                            <li>2. Chọn cài đặt</li>
                            <li>3. Chọn "Lịch học CTU" ở mục "Cài đặt lịch học của tôi"</li>
                            <li>4. Cuộn xuống và copy "ID lịch"</li>
                        </ol>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button type="button" onclick="hideCalendarSettings()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            Hủy
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Lưu cài đặt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Hiển thị alert thành công khi load trang
        <?php if (isset($_GET['success'])): ?>
            alert('Cập nhật Calendar ID thành công!');
        <?php endif; ?>

        // Ẩn loading indicator khi iframe đã tải xong
        function hideLoading() {
            const loading = document.getElementById('calendar-loading');
            if (loading) {
                loading.style.display = 'none';
            }
        }

        // Hiển thị modal cài đặt
        function showCalendarSettings() {
            document.getElementById('calendar-settings-modal').classList.remove('hidden');
        }

        // Ẩn modal cài đặt
        function hideCalendarSettings() {
            document.getElementById('calendar-settings-modal').classList.add('hidden');
        }

        // Dán Calendar ID từ clipboard
        async function pasteCalendarId() {
            try {
                const text = await navigator.clipboard.readText();
                const input = document.getElementById('calendar_id');
                input.value = text;
            } catch (err) {
                // Fallback cho trình duyệt không hỗ trợ clipboard API
                const input = document.getElementById('calendar_id');
                input.focus();
            }
        }
        // Tự động ẩn loading sau 5 giây nếu iframe không load được
        setTimeout(() => {
            hideLoading();
        }, 5000);
    </script>

    <?php
    // Include footer
    include 'includes/footer.php';
    ?>