<?php
$page_title = 'Lịch học';
$current_page = 'calendar.php';

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin user
$user_id = $_SESSION['user_id'];
// cập nhật id lịch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calendar_id'])) {
    $calendar_id = trim($_POST['calendar_id']);
    executeQuery("UPDATE users SET gg_cal_id = ? WHERE id = ?", [$calendar_id, $user_id]);
    header('Location: calendar.php?message=' . urlencode('Cập nhật Calendar ID thành công!') . '&type=success');
    exit();
}

// lấy id lịch
$user = fetchOne("SELECT gg_cal_id FROM users WHERE id = ?", [$user_id]);
$current_calendar_id = $user['gg_cal_id'] ?? '';

// header
include 'includes/header.php';
?>
<div class="bg-white rounded-lg shadow-md p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Quản lý lịch học</h1>
            </div>
            <div class="flex space-x-3">
                <button onclick="openModal()"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 cursor-pointer transition-all duration-200 flex items-center space-x-2 shadow-lg">
                    <i class="fas fa-cog mr-2"></i>
                    Cài đặt lịch
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-700 text-white p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calendar-week text-2xl mr-3"></i>
                    <h2 class="text-xl font-bold ">Google Calendar</h2>
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
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có lịch học</h3>
                    <p class="text-gray-500 mb-4">
                        Để có thể xem lịch học của bạn, hãy vào cài đặt lịch và làm theo hướng dẫn.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Cài đặt lịch -->
<div id="calendar-settings-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-500">
                <h3 class="text-lg font-semibold text-white">Cài đặt lịch</h3>
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

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">Hướng dẫn lấy Calendar ID
                    </h4>
                    <ol class="text-sm text-blue-700 space-y-1">
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