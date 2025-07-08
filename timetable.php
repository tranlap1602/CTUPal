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

/**
 * Lấy ngày bắt đầu học kì sớm nhất từ dữ liệu thời khóa biểu
 */
function getAcademicStartDate($user_id)
{
    global $pdo;

    $query = "SELECT MIN(start_date) as earliest_start_date FROM timetable WHERE user_id = ? AND start_date IS NOT NULL";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['earliest_start_date']) {
        return new DateTime($result['earliest_start_date']);
    }

    // Nếu không có dữ liệu, trả về đầu tháng 9 năm hiện tại (năm học thường bắt đầu tháng 9)
    $currentYear = date('Y');
    $currentMonth = date('m');

    // Nếu đang ở tháng 1-6, có thể là học kì 2, sử dụng tháng 9 năm trước
    if ($currentMonth <= 6) {
        $currentYear--;
    }

    return new DateTime($currentYear . '-09-01');
}

/**
 * Tính tuần học thuật dựa trên ngày bắt đầu học kì
 */
function getAcademicWeekInfo($user_id, $weekOffset = 0)
{
    $academicStartDate = getAcademicStartDate($user_id);

    // Tính ngày thứ 2 của tuần bắt đầu học kì
    $academicStartMonday = clone $academicStartDate;
    $dayOfWeek = $academicStartDate->format('N'); // 1 = Monday, 7 = Sunday
    if ($dayOfWeek != 1) {
        $academicStartMonday->modify('-' . ($dayOfWeek - 1) . ' days');
    }

    // Tính tuần hiện tại với offset
    $currentDate = new DateTime();
    $targetMonday = clone $currentDate;
    $currentDayOfWeek = $currentDate->format('N');
    if ($currentDayOfWeek != 1) {
        $targetMonday->modify('-' . ($currentDayOfWeek - 1) . ' days');
    }
    $targetMonday->modify($weekOffset . ' weeks');

    // Tính số tuần đã trôi qua kể từ khi bắt đầu học kì
    $diff = $academicStartMonday->diff($targetMonday);
    $academicWeekNumber = floor($diff->days / 7) + 1;

    // Đảm bảo tuần học thuật không âm
    if ($academicWeekNumber < 1) {
        $academicWeekNumber = 1;
    }

    $targetSunday = clone $targetMonday;
    $targetSunday->modify('+6 days');

    return [
        'academic_week_number' => $academicWeekNumber,
        'week_start' => $targetMonday->format('Y-m-d'),
        'week_end' => $targetSunday->format('Y-m-d'),
        'week_range' => $targetMonday->format('d/m/Y') . ' - ' . $targetSunday->format('d/m/Y'),
        'academic_start_date' => $academicStartDate->format('Y-m-d')
    ];
}

// Lấy tham số tuần từ URL
$weekOffset = isset($_GET['week_offset']) ? intval($_GET['week_offset']) : 0;

// Lấy thời khóa biểu của user từ database theo tuần
try {
    // Lấy thông tin tuần học thuật
    $weekInfo = getAcademicWeekInfo($user_id, $weekOffset);
    $weekStart = $weekInfo['week_start'];
    $weekEnd = $weekInfo['week_end'];

    // Query với filter theo phạm vi tuần
    $timetable_query = "SELECT t.*, 
                               DATE_FORMAT(t.start_date, '%d/%m/%Y') as formatted_start_date,
                               DATE_FORMAT(t.end_date, '%d/%m/%Y') as formatted_end_date
                        FROM timetable t 
                        WHERE t.user_id = ? 
                        AND (
                            -- Kiểm tra xem tuần hiện tại có nằm trong phạm vi không
                            (t.start_date <= ? AND t.end_date >= ?) OR
                            -- Hoặc môn học không có phạm vi tuần (dữ liệu cũ)
                            (t.start_date IS NULL AND t.end_date IS NULL)
                        )
                        ORDER BY t.day_of_week, t.start_time";

    $timetable_data = fetchAll($timetable_query, [$user_id, $weekEnd, $weekStart]);

    // Tổ chức dữ liệu theo ngày trong tuần
    $schedule = [];
    foreach ($timetable_data as $item) {
        $schedule[$item['day_of_week']][] = $item;
    }

    // Thông tin tuần hiện tại
    $currentWeekInfo = [
        'week_number' => $weekInfo['academic_week_number'],
        'academic_year' => date('Y', strtotime($weekInfo['academic_start_date'])),
        'week_range' => $weekInfo['week_range'],
        'week_offset' => $weekOffset
    ];
} catch (Exception $e) {
    error_log("Timetable error: " . $e->getMessage());
    $schedule = [];

    // Sử dụng tuần học thuật cho trường hợp lỗi
    try {
        $fallbackWeekInfo = getAcademicWeekInfo($user_id, 0);
        $currentWeekInfo = [
            'week_number' => $fallbackWeekInfo['academic_week_number'],
            'academic_year' => date('Y', strtotime($fallbackWeekInfo['academic_start_date'])),
            'week_range' => $fallbackWeekInfo['week_range'],
            'week_offset' => 0
        ];
    } catch (Exception $fallbackError) {
        // Nếu không thể tính tuần học thuật, sử dụng fallback cơ bản
        $currentWeekInfo = [
            'week_number' => 1,
            'academic_year' => date('Y'),
            'week_range' => date('d/m/Y', strtotime('monday this week')) . ' - ' . date('d/m/Y', strtotime('sunday this week')),
            'week_offset' => 0
        ];
    }
}

// Định nghĩa các ngày trong tuần (Chủ nhật ở cuối)
// Sử dụng array có thứ tự cố định từ thứ 2 đến chủ nhật
$days_order = [2, 3, 4, 5, 6, 7, 1]; // Thứ tự hiển thị
$days_names = [
    1 => 'Chủ nhật',
    2 => 'Thứ 2',
    3 => 'Thứ 3',
    4 => 'Thứ 4',
    5 => 'Thứ 5',
    6 => 'Thứ 6',
    7 => 'Thứ 7'
];
$days = [];
foreach ($days_order as $day_num) {
    $days[$day_num] = $days_names[$day_num];
}



// Include header
include 'includes/header.php';
?>

<!-- Main content -->
<div class="bg-white rounded-lg shadow-md p-8">
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

    <!-- Modal sửa môn học -->
    <div id="edit-subject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header modal -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-edit mr-2 text-blue-500"></i>
                        Sửa môn học
                    </h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Form sửa -->
                <form id="edit-subject-form" class="space-y-4">
                    <input type="hidden" id="edit-subject-id" name="id">

                    <!-- Tên môn học -->
                    <div>
                        <label for="edit-subject-name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-book mr-1 text-blue-500"></i>Tên môn học *
                        </label>
                        <input type="text" id="edit-subject-name" name="subject_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Mã môn học -->
                    <div>
                        <label for="edit-subject-code" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-hashtag mr-1 text-blue-500"></i>Mã môn học
                        </label>
                        <input type="text" id="edit-subject-code" name="subject_code"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Ngày trong tuần -->
                    <div>
                        <label for="edit-day-of-week" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-day mr-1 text-blue-500"></i>Ngày trong tuần *
                        </label>
                        <select id="edit-day-of-week" name="day_of_week" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="2">Thứ 2</option>
                            <option value="3">Thứ 3</option>
                            <option value="4">Thứ 4</option>
                            <option value="5">Thứ 5</option>
                            <option value="6">Thứ 6</option>
                            <option value="7">Thứ 7</option>
                            <option value="1">Chủ nhật</option>
                        </select>
                    </div>

                    <!-- Thời gian -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="edit-start-time" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-clock mr-1 text-blue-500"></i>Giờ bắt đầu *
                            </label>
                            <input type="time" id="edit-start-time" name="start_time" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="edit-end-time" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-clock mr-1 text-blue-500"></i>Giờ kết thúc *
                            </label>
                            <input type="time" id="edit-end-time" name="end_time" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Phòng học -->
                    <div>
                        <label for="edit-classroom" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>Phòng học
                        </label>
                        <input type="text" id="edit-classroom" name="classroom"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ví dụ: A101, B203, Lab1, ...">
                    </div>

                    <!-- Giảng viên -->
                    <div>
                        <label for="edit-teacher" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-1 text-blue-500"></i>Giảng viên
                        </label>
                        <input type="text" id="edit-teacher" name="teacher"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                                        <!-- Ghi chú -->
                    <div>
                        <label for="edit-notes" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-sticky-note mr-1 text-blue-500"></i>Ghi chú
                        </label>
                        <textarea id="edit-notes" name="notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Nhập ghi chú..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition-colors">
                            Hủy
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors flex items-center">
                            <i class="fas fa-save mr-1"></i>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Các nút chức năng - Di chuyển xuống dưới -->
    <div class="flex flex-wrap gap-4 mt-8 justify-center border-t border-gray-200 pt-6">
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
            <span>Import từ .ics</span>
        </button>

        <button onclick="exportTimetable()"
            class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md">
            <i class="fas fa-download"></i>
            <span>Xuất file</span>
        </button>
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
    async function deleteSubject(id) {
        if (confirm('Bạn có chắc muốn xóa môn học này?')) {
            try {
                const response = await fetch(`api/timetable-api.php?action=delete-subject&id=${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    location.reload();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Lỗi khi xóa môn học', 'error');
            }
        }
    }

    /**
     * Hàm sửa môn học - Mở modal edit
     * @param {number} id - ID của môn học cần sửa
     */
    async function editSubject(id) {
        try {
            // Lấy thông tin môn học từ API
            const response = await fetch(`api/timetable-api.php?action=get-subject&id=${id}`);
            const result = await response.json();

            if (result.success) {
                // Điền thông tin vào form
                populateEditForm(result.data);
                // Hiển thị modal
                document.getElementById('edit-subject-modal').classList.remove('hidden');
            } else {
                showNotification(result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Lỗi khi tải thông tin môn học', 'error');
        }
    }

    /**
     * Điền thông tin vào form edit
     * @param {object} data - Dữ liệu môn học
     */
    function populateEditForm(data) {
        document.getElementById('edit-subject-id').value = data.id;
        document.getElementById('edit-subject-name').value = data.subject_name || '';
        document.getElementById('edit-subject-code').value = data.subject_code || '';
        document.getElementById('edit-day-of-week').value = data.day_of_week || '';
        document.getElementById('edit-start-time').value = data.start_time || '';
        document.getElementById('edit-end-time').value = data.end_time || '';
        document.getElementById('edit-classroom').value = data.classroom || '';
        document.getElementById('edit-teacher').value = data.teacher || '';
        document.getElementById('edit-notes').value = data.notes || '';
    }

    /**
     * Đóng modal edit
     */
    function closeEditModal() {
        document.getElementById('edit-subject-modal').classList.add('hidden');
        // Reset form
        document.getElementById('edit-subject-form').reset();
    }



    /**
     * Hiển thị thông báo
     * @param {string} message - Nội dung thông báo
     * @param {string} type - Loại thông báo (success, error, info)
     */
    function showNotification(message, type = 'info') {
        // Tạo element thông báo
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm transition-all duration-300 transform translate-x-full`;

        // Màu sắc theo loại
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white'
        };

        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };

        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="${icons[type]} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Hiện thông báo
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    // Xử lý submit form edit
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('edit-subject-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('api/timetable-api.php?action=update-subject', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeEditModal();
                    location.reload();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Lỗi khi cập nhật môn học', 'error');
            }
        });
    });



    // Khởi tạo trang với view mặc định
    document.addEventListener('DOMContentLoaded', function() {
        showView('view');
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>