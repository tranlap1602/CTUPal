<?php

/**
 * File: timetable.php
 * Mục đích: Quản lý thời khóa biểu của sinh viên
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Hiển thị, thêm, sửa, xóa thời khóa biểu, import từ file Excel/CSV
 */

// Thiết lập biến cho header
$page_title = 'Thời khóa biểu';
$current_page = 'timetable.php';

// Bắt đầu session và kiểm tra đăng nhập
session_start();
require_once 'config/db.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển về trang login
    header('Location: login.php');
    exit();
}

// Lấy thông tin user hiện tại
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? $_SESSION['username'];

// Lấy thời khóa biểu của user từ database
try {
    $timetable_query = "SELECT * FROM timetable WHERE user_id = ? ORDER BY day_of_week, start_time";
    $timetable_data = fetchAll($timetable_query, [$user_id]);

    // Tổ chức dữ liệu theo ngày trong tuần
    $schedule = [];
    foreach ($timetable_data as $item) {
        $schedule[$item['day_of_week']][] = $item;
    }
} catch (Exception $e) {
    error_log("Timetable error: " . $e->getMessage());
    $schedule = [];
}

// Định nghĩa các ngày trong tuần (Chủ nhật ở cuối)
$days = [
    2 => 'Thứ 2',
    3 => 'Thứ 3',
    4 => 'Thứ 4',
    5 => 'Thứ 5',
    6 => 'Thứ 6',
    7 => 'Thứ 7',
    1 => 'Chủ nhật'
];

// Định nghĩa các giờ học theo từng giờ (7:00 - 18:00)
$time_slots = [];
for ($hour = 7; $hour <= 18; $hour++) {
    $time_slots[] = [
        'hour' => $hour,
        'time' => sprintf('%02d:00', $hour),
        'display' => $hour . ':00'
    ];
}

// Include header
include 'includes/header.php';
?>

<!-- Main content -->
<div class="bg-white rounded-2xl shadow-lg p-8">
    <!-- Các nút chức năng -->
    <div class="flex flex-wrap gap-4 mb-8 justify-center">
        <button onclick="showView('view')"
            id="btn-view"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md">
            <i class="fas fa-calendar-week"></i>
            <span>Xem thời khóa biểu</span>
        </button>

        <button onclick="showView('import')"
            id="btn-import"
            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md">
            <i class="fas fa-file-import"></i>
            <span>Import/Thêm môn học</span>
        </button>

        <button onclick="exportTimetable()"
            class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md">
            <i class="fas fa-download"></i>
            <span>Xuất file</span>
        </button>
    </div>

    <!-- View hiển thị thời khóa biểu -->
    <div id="timetable-view" class="view-container">
        <!-- Hiển thị dạng danh sách -->
        <div id="list-view" class="view-mode">
            <?php include 'views/timetable-list.php'; ?>
        </div>
    </div>

    <!-- View import/thêm thời khóa biểu -->
    <div id="timetable-import" class="view-container hidden">
        <?php include 'views/timetable-import.php'; ?>
    </div>
</div>

<!-- JavaScript cho tương tác -->
<script>
    /**
     * Hàm chuyển đổi giữa các view
     * @param {string} viewType - Loại view ('view' hoặc 'import')
     */
    function showView(viewType) {
        // Lấy các element container
        const viewContainer = document.getElementById('timetable-view');
        const importContainer = document.getElementById('timetable-import');
        const btnView = document.getElementById('btn-view');
        const btnImport = document.getElementById('btn-import');

        if (viewType === 'view') {
            // Hiển thị view xem thời khóa biểu
            viewContainer.classList.remove('hidden');
            importContainer.classList.add('hidden');

            // Cập nhật trạng thái button
            btnView.classList.add('bg-blue-500');
            btnView.classList.remove('bg-gray-300');
            btnImport.classList.add('bg-gray-300');
            btnImport.classList.remove('bg-green-500');
        } else if (viewType === 'import') {
            // Hiển thị view import/thêm
            viewContainer.classList.add('hidden');
            importContainer.classList.remove('hidden');

            // Cập nhật trạng thái button
            btnImport.classList.add('bg-green-500');
            btnImport.classList.remove('bg-gray-300');
            btnView.classList.add('bg-gray-300');
            btnView.classList.remove('bg-blue-500');
        }
    }

    /**
     * Hàm xuất thời khóa biểu
     */
    function exportTimetable() {
        // Tạo dữ liệu để xuất
        const timetableData = {
            weekdays: <?php echo json_encode($days); ?>,
            schedule: <?php echo json_encode($schedule); ?>
        };

        // Tạo nội dung CSV
        let csvContent = "Ngày,Môn học,Thời gian,Phòng,Giảng viên\n";

        Object.entries(timetableData.schedule).forEach(([day, subjects]) => {
            const dayName = timetableData.weekdays[day];
            subjects.forEach(subject => {
                csvContent += `"${dayName}","${subject.subject_name}","${subject.start_time} - ${subject.end_time}","${subject.classroom || ''}","${subject.teacher || ''}"\n`;
            });
        });

        // Tạo và download file
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'timetable.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    /**
     * Hàm xóa môn học
     * @param {number} id - ID của môn học cần xóa
     */
    function deleteSubject(id) {
        if (confirm('Bạn có chắc muốn xóa môn học này?')) {
            // TODO: Implement delete functionality
            alert(`Đã xóa môn học ID: ${id}`);
            location.reload();
        }
    }

    /**
     * Hàm sửa môn học
     * @param {number} id - ID của môn học cần sửa
     */
    function editSubject(id) {
        // TODO: Implement edit functionality
        alert(`Sửa môn học ID: ${id}`);
    }

    /**
     * Hàm thêm môn học vào khung giờ cụ thể
     * @param {number} dayNum - Số ngày trong tuần (1-7)
     * @param {string} startTime - Giờ bắt đầu (format: HH:00)
     * @param {string} endTime - Giờ kết thúc (format: HH:00)
     */
    function addSubjectToSlot(dayNum, startTime, endTime) {
        const dayNames = {
            1: 'Chủ nhật',
            2: 'Thứ 2',
            3: 'Thứ 3',
            4: 'Thứ 4',
            5: 'Thứ 5',
            6: 'Thứ 6',
            7: 'Thứ 7'
        };

        console.log('Thêm môn học:', dayNum, startTime, endTime);

        // Hiển thị modal hoặc form để thêm môn học
        const dayName = dayNames[dayNum] || 'Không xác định';
        const confirmMsg = `Thêm môn học mới cho ${dayName} lúc ${startTime}?\n\nClick OK để tiếp tục.`;

        if (confirm(confirmMsg)) {
            // TODO: Implement add subject functionality - mở form thêm môn học
            alert(`Chức năng thêm môn học đang được phát triển!\n\nThông tin:\n- Ngày: ${dayName}\n- Giờ: ${startTime} - ${endTime}`);
        }
    }

    // Khởi tạo trang với view mặc định
    document.addEventListener('DOMContentLoaded', function() {
        showView('view');
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>