<?php

/**
 * File: calendar.php
 * Mục đích: Trang hiển thị Google Calendar tích hợp
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Trang hiển thị lịch Google Calendar với các lớp học và sự kiện
 */

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
$user_name = $_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User';

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
                <p class="text-gray-600">Xem lịch học và các sự kiện quan trọng</p>
            </div>
            <div class="flex space-x-3">
                <a href="index.php"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    Trang chủ
                </a>
            </div>
        </div>

        <!-- Thông tin lịch -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-1">Thông tin lịch học</h3>
                    <p class="text-blue-700 text-sm">
                        Lịch này bao gồm tất cả các lớp học, sự kiện và ngày nghỉ lễ.
                        Bạn có thể xem theo tuần, tháng hoặc năm.
                        Các màu sắc khác nhau đại diện cho các lớp học khác nhau.
                    </p>
                </div>
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

        <!-- Calendar iframe -->
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
                src="https://calendar.google.com/calendar/embed?src=c_061574708019b1d9eb795405af64382400b35e76925c04aad94f8c34cc192934%40group.calendar.google.com&ctz=Asia%2FHo_Chi_Minh"
                style="border:solid 1px #777; width: 100%; height: 900px;"
                frameborder="0"
                scrolling="no"
                onload="hideLoading()">
            </iframe>
        </div>
    </div>

    <!-- Legend section -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-palette text-purple-600 mr-2"></i>
            Chú thích màu sắc
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                <span class="text-sm text-gray-700">Lớp học chính</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-600 rounded mr-3"></div>
                <span class="text-sm text-gray-700">Sự kiện chung</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-700 rounded mr-3"></div>
                <span class="text-sm text-gray-700">Lớp học phụ</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-teal-600 rounded mr-3"></div>
                <span class="text-sm text-gray-700">Thực hành</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-800 rounded mr-3"></div>
                <span class="text-sm text-gray-700">Ngày nghỉ lễ</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-600 rounded mr-3"></div>
                <span class="text-sm text-gray-700">Sự kiện xanh</span>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-lg shadow-lg">
            <div class="text-center">
                <i class="fas fa-download text-3xl mb-3"></i>
                <h3 class="text-lg font-bold mb-2">Xuất lịch</h3>
                <p class="text-green-100 text-sm mb-4">Tải lịch về máy tính</p>
                <button onclick="exportCalendar()" class="bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition-colors duration-200">
                    Xuất ngay
                </button>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow-lg">
            <div class="text-center">
                <i class="fas fa-share-alt text-3xl mb-3"></i>
                <h3 class="text-lg font-bold mb-2">Chia sẻ</h3>
                <p class="text-purple-100 text-sm mb-4">Chia sẻ lịch với bạn bè</p>
                <button onclick="shareCalendar()" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition-colors duration-200">
                    Chia sẻ
                </button>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-lg shadow-lg">
            <div class="text-center">
                <i class="fas fa-cog text-3xl mb-3"></i>
                <h3 class="text-lg font-bold mb-2">Cài đặt</h3>
                <p class="text-orange-100 text-sm mb-4">Tùy chỉnh hiển thị lịch</p>
                <button onclick="openSettings()" class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition-colors duration-200">
                    Cài đặt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Ẩn loading indicator khi iframe đã tải xong
    function hideLoading() {
        const loading = document.getElementById('calendar-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    }

    // Xuất lịch
    function exportCalendar() {
        const calendarUrl = document.getElementById('google-calendar').src;
        const exportUrl = calendarUrl.replace('/embed?', '/export?');
        window.open(exportUrl, '_blank');
    }

    // Chia sẻ lịch
    function shareCalendar() {
        const calendarUrl = document.getElementById('google-calendar').src;
        if (navigator.share) {
            navigator.share({
                title: 'Lịch học của tôi',
                text: 'Xem lịch học và sự kiện của tôi',
                url: calendarUrl
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(calendarUrl).then(() => {
                alert('Đã sao chép link lịch vào clipboard!');
            });
        }
    }

    // Mở cài đặt
    function openSettings() {
        alert('Tính năng cài đặt sẽ được phát triển trong phiên bản tiếp theo!');
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