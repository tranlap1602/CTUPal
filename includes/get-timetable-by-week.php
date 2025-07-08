<?php

/**
 * File: get-timetable-by-week.php
 * Mục đích: API endpoint để lấy dữ liệu thời khóa biểu theo tuần
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Bắt đầu session
session_start();
require_once '../config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

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

// Lấy tham số tuần
$weekOffset = isset($_GET['week_offset']) ? intval($_GET['week_offset']) : 0;
$user_id = $_SESSION['user_id'];

try {
    // Lấy thông tin tuần học thuật
    $weekInfo = getAcademicWeekInfo($user_id, $weekOffset);
    $weekStart = $weekInfo['week_start'];
    $weekEnd = $weekInfo['week_end'];

    // Query lấy dữ liệu thời khóa biểu cho tuần được chọn
    $query = "SELECT t.*, 
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

    $timetable_data = fetchAll($query, [$user_id, $weekEnd, $weekStart]);

    // Tổ chức dữ liệu theo ngày trong tuần
    $schedule = [];
    foreach ($timetable_data as $item) {
        $schedule[$item['day_of_week']][] = $item;
    }

    // Tính thông tin tuần
    $weekNumber = $weekInfo['academic_week_number'];
    $academicYear = date('Y', strtotime($weekInfo['academic_start_date']));
    $weekRange = $weekInfo['week_range'];

    // Thống kê
    $totalSubjects = count($timetable_data);
    $totalHours = 0;
    foreach ($timetable_data as $item) {
        $start = new DateTime($item['start_time']);
        $end = new DateTime($item['end_time']);
        $totalHours += ($end->getTimestamp() - $start->getTimestamp()) / 3600;
    }

    // Trả về dữ liệu
    echo json_encode([
        'success' => true,
        'data' => [
            'schedule' => $schedule,
            'week_info' => [
                'week_number' => $weekNumber,
                'academic_year' => $academicYear,
                'week_range' => $weekRange,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'week_offset' => $weekOffset
            ],
            'statistics' => [
                'total_subjects' => $totalSubjects,
                'total_hours' => round($totalHours, 1),
                'average_hours_per_day' => round($totalHours / 7, 1)
            ]
        ]
    ]);
} catch (Exception $e) {
    error_log("Error getting timetable by week: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()
    ]);
}
