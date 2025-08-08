<?php
$page_title = 'Lịch học';
$current_page = 'calendar.php';

session_start();
require_once 'config/db.php';
require_once 'config/google.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin user từ session
$user_id = $_SESSION['user_id'];

// Kiểm tra trạng thái kết nối Google Calendar
$isGoogleConnected = isset($_SESSION['google_access_token']);
$selectedCalendar = null;

if ($isGoogleConnected && isset($_SESSION['selected_calendar_id'])) {
    $selectedCalendar = [
        'id' => $_SESSION['selected_calendar_id'],
        'name' => $_SESSION['selected_calendar_name'] ?? 'Lịch đã chọn'
    ];
}

// Xử lý cập nhật id lịch (giữ tương thích với code cũ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calendar_id'])) {
    $calendar_id = trim($_POST['calendar_id']);
    executeQuery("UPDATE users SET google_calendar_id = ? WHERE id = ?", [$calendar_id, $user_id]);
    header('Location: calendar.php?message=' . urlencode('Cập nhật Calendar ID thành công!') . '&type=success');
    exit();
}

// Lấy id lịch (giữ tương thích với code cũ)
$user = fetchOne("SELECT google_calendar_id FROM users WHERE id = ?", [$user_id]);
$current_calendar_id = $user['google_calendar_id'] ?? '';

// header
include 'includes/header.php';
?>
<div class="bg-white rounded-lg shadow-md p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Quản lý lịch học</h1>
                <p class="text-gray-600 mt-2">Tích hợp Google Calendar để quản lý lịch học hiệu quả</p>
            </div>
            <div class="flex space-x-3">
                <?php if ($isGoogleConnected): ?>
                    <?php if ($selectedCalendar): ?>
                        <a href="calendar-events.php"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Xem sự kiện
                        </a>
                        <a href="calendar-list.php"
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Chọn lịch khác
                        </a>
                    <?php else: ?>
                        <a href="calendar-list.php"
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Chọn lịch
                        </a>
                    <?php endif; ?>
                    <a href="google-auth.php?disconnect=1"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                        <i class="fas fa-unlink mr-2"></i>
                        Ngắt kết nối
                    </a>
                <?php else: ?>
                    <a href="google-auth.php"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                        <i class="fab fa-google mr-2"></i>
                        Kết nối Google Calendar
                    </a>
                <?php endif; ?>
                
                <button onclick="openModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
                    <i class="fas fa-cog mr-2"></i>
                    Cài đặt lịch cũ
                </button>
            </div>
        </div>
    </div>

    <?php if ($isGoogleConnected): ?>
        <?php if ($selectedCalendar): ?>
            <!-- Hiển thị thông tin lịch đã chọn -->
            <div class="mb-6 p-6 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-green-800">Đã kết nối Google Calendar</h3>
                            <p class="text-green-700">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Lịch hiện tại: <strong><?php echo htmlspecialchars($selectedCalendar['name']); ?></strong>
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="calendar-events.php" 
                           class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition-all duration-200 text-sm">
                            <i class="fas fa-eye mr-1"></i>
                            Xem sự kiện
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Đã kết nối nhưng chưa chọn lịch -->
            <div class="mb-6 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800">Đã kết nối Google Calendar</h3>
                            <p class="text-yellow-700">Vui lòng chọn lịch "Lịch học CTU" để tiếp tục</p>
                        </div>
                    </div>
                    <a href="calendar-list.php" 
                       class="bg-yellow-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition-all duration-200">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Chọn lịch
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Chưa kết nối Google Calendar -->
        <div class="mb-6 p-8 bg-blue-50 border border-blue-200 rounded-lg text-center">
            <div class="mb-4">
                <i class="fab fa-google text-6xl text-blue-500"></i>
            </div>
            <h3 class="text-xl font-semibold text-blue-800 mb-2">Kết nối Google Calendar</h3>
            <p class="text-blue-700 mb-6 max-w-2xl mx-auto">
                Kết nối với Google Calendar để quản lý lịch học hiệu quả. Bạn có thể xem và chỉnh sửa sự kiện trực tiếp từ ứng dụng.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="google-auth.php" 
                   class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 inline-flex items-center">
                    <i class="fab fa-google mr-2"></i>
                    Kết nối Google Calendar
                </a>
            </div>
            
            <div class="mt-6 p-4 bg-blue-100 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">Tính năng sau khi kết nối:</h4>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Xem danh sách lịch Google Calendar</li>
                    <li>• Chọn lịch "Lịch học CTU"</li>
                    <li>• Xem sự kiện sắp tới</li>
                    <li>• Chỉnh sửa thông tin môn học trực tiếp</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hiển thị lịch nhúng (backward compatibility) -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-700 text-white p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calendar-week text-2xl mr-3"></i>
                    <h2 class="text-xl font-bold ">Google Calendar (Nhúng)</h2>
                </div>
                <div class="text-sm opacity-90">
                    <i class="fas fa-clock mr-1"></i>
                    Múi giờ: Asia/Ho_Chi_Minh
                </div>
            </div>
        </div>

        <?php if ($current_calendar_id): ?>
            <div class="relative">
                <div id="calendar-loading" class="absolute inset-0 bg-gray-100 flex items-center justify-center z-10">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-15 w-15 border-b border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Đang tải lịch...</p>
                    </div>
                </div>

                <!-- Nhúng lịch -->
                <iframe
                    id="google-calendar"
                    src="https://calendar.google.com/calendar/embed?mode=WEEK&wkst=2&src=<?php echo urlencode($current_calendar_id); ?>&ctz=Asia%2FHo_Chi_Minh"
                    style="border:solid 2px #1840c8; width: 100%; height: 900px;"
                    frameborder="0"
                    scrolling="no"
                    onload="hideLoading()">
                </iframe>
            </div>
        <?php else: ?>
            <div class="flex items-center justify-center py-16 px-8">
                <div class="text-center max-w-md">
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-calendar-plus text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có lịch nhúng</h3>
                    <p class="text-gray-500 mb-4">
                        Để xem lịch nhúng, hãy vào cài đặt lịch cũ và nhập Calendar ID.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Cài đặt lịch cũ (backward compatibility) -->
<div id="calendar-settings-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-500">
                <h3 class="text-lg font-semibold text-white">Cài đặt lịch cũ (nhúng)</h3>
            </div>

            <form method="POST" class="p-6 space-y-6">
                <div>
                    <label for="calendar_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-week mr-2"></i>
                        Google Calendar ID
                    </label>
                    <div class="relative">
                        <input type="text" id="calendar_id" name="calendar_id"
                            value="<?php echo htmlspecialchars($current_calendar_id); ?>"
                            placeholder="calendar_id@group.calendar.google.com ..."
                            class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                        <button type="button"
                            onclick="pasteCalendarId()"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-100 hover:bg-blue-200 text-blue-600 px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
                            title="Dán từ clipboard">
                            <i class="fas fa-paste"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Chế độ tương thích
                    </h4>
                    <p class="text-sm text-yellow-700 mb-2">
                        Đây là phương thức cũ để nhúng lịch. Khuyến nghị sử dụng tích hợp Google Calendar API phía trên.
                    </p>
                    <ol class="text-sm text-yellow-700 space-y-1">
                        <li>1. Mở <a href="https://calendar.google.com" target="_blank" class="underline">Google Calendar</a></li>
                        <li>2. Chọn cài đặt</li>
                        <li>3. Chọn "Lịch học CTU" ở mục "Cài đặt lịch học của tôi"</li>
                        <li>4. Cuộn xuống và copy "ID lịch"</li>
                    </ol>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-all duration-200 cursor-pointer">
                        Hủy
                    </button>
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 shadow-lg cursor-pointer ">
                        <span>Lưu cài đặt</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function hideLoading() {
        const loading = document.getElementById('calendar-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    }

    function openModal() {
        const modal = document.getElementById('calendar-settings-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('calendar-settings-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.querySelector('#calendar-settings-modal > div').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    async function pasteCalendarId() {
        try {
            const text = await navigator.clipboard.readText();
            const input = document.getElementById('calendar_id');
            input.value = text;
        } catch (err) {
            const input = document.getElementById('calendar_id');
            input.focus();
        }
    }

    setTimeout(() => {
        hideLoading();
    }, 5000);
</script>
<?php
include 'includes/footer.php';
?>