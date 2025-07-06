<?php

/**
 * Class ICSParser
 * Phân tích file .ics (iCalendar) và chuyển đổi thành dữ liệu thời khóa biểu
 * 
 * Tác giả: Student Manager System
 * Ngày tạo: 2025-01-29
 * 
 * Chức năng:
 * - Đọc file .ics
 * - Phân tích VEVENT
 * - Xử lý RRULE với giới hạn COUNT để tránh lặp vô hạn
 * - Xử lý EXDATE để loại bỏ các ngày ngoại lệ
 * - Chuyển đổi sang định dạng phù hợp với database
 */
class ICSParser
{

    private $events = [];
    private $timezone = 'Asia/Ho_Chi_Minh';

    /**
     * Khởi tạo parser
     */
    public function __construct()
    {
        // Thiết lập timezone mặc định
        date_default_timezone_set($this->timezone);
    }

    /**
     * Đọc và phân tích file .ics
     * 
     * @param string $filePath Đường dẫn tới file .ics
     * @return array Mảng các sự kiện đã được phân tích
     * @throws Exception Nếu file không tồn tại hoặc lỗi định dạng
     */
    public function parseFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("File .ics không tồn tại: " . $filePath);
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new Exception("Không thể đọc file .ics");
        }

        return $this->parseContent($content);
    }

    /**
     * Phân tích nội dung .ics
     * 
     * @param string $content Nội dung file .ics
     * @return array Mảng các sự kiện
     */
    public function parseContent($content)
    {
        $this->events = [];

        // Chuẩn hóa line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Xử lý line folding (dòng dài được gấp thành nhiều dòng)
        $content = preg_replace('/\n\s+/', '', $content);

        $lines = explode("\n", $content);
        $currentEvent = null;
        $inEvent = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            if ($line === 'BEGIN:VEVENT') {
                $inEvent = true;
                $currentEvent = [
                    'DTSTART' => '',
                    'DTEND' => '',
                    'SUMMARY' => '',
                    'DESCRIPTION' => '',
                    'RRULE' => '',
                    'EXDATE' => [],
                    'UID' => ''
                ];
                continue;
            }

            if ($line === 'END:VEVENT') {
                if ($currentEvent && $inEvent) {
                    $this->processEvent($currentEvent);
                }
                $inEvent = false;
                $currentEvent = null;
                continue;
            }

            if ($inEvent && $currentEvent) {
                $this->parseEventLine($line, $currentEvent);
            }
        }

        return $this->events;
    }

    /**
     * Phân tích một dòng trong VEVENT
     * 
     * @param string $line Dòng cần phân tích
     * @param array &$event Tham chiếu đến sự kiện hiện tại
     */
    private function parseEventLine($line, &$event)
    {
        // Tách property và value
        $colonPos = strpos($line, ':');
        if ($colonPos === false) {
            return;
        }

        $property = substr($line, 0, $colonPos);
        $value = substr($line, $colonPos + 1);

        // Xử lý các property có parameters (như DTSTART;TZID=...)
        $semicolonPos = strpos($property, ';');
        if ($semicolonPos !== false) {
            $property = substr($property, 0, $semicolonPos);
        }

        switch ($property) {
            case 'DTSTART':
                $event['DTSTART'] = $this->parseDateTime($value);
                break;
            case 'DTEND':
                $event['DTEND'] = $this->parseDateTime($value);
                break;
            case 'SUMMARY':
                $event['SUMMARY'] = $this->unescapeText($value);
                break;
            case 'DESCRIPTION':
                $event['DESCRIPTION'] = $this->unescapeText($value);
                break;
            case 'RRULE':
                $event['RRULE'] = $this->parseRRule($value);
                break;
            case 'EXDATE':
                $event['EXDATE'][] = $this->parseDateTime($value);
                break;
            case 'UID':
                $event['UID'] = $value;
                break;
        }
    }

    /**
     * Chuyển đổi datetime từ định dạng iCalendar sang DateTime
     * 
     * @param string $dateTimeString Chuỗi datetime
     * @return DateTime|null
     */
    private function parseDateTime($dateTimeString)
    {
        try {
            // Loại bỏ timezone info nếu có
            $dateTimeString = preg_replace('/^(\d{8}T\d{6}).*$/', '$1', $dateTimeString);

            // Định dạng: YYYYMMDDTHHMMSS
            if (preg_match('/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})$/', $dateTimeString, $matches)) {
                $dateTime = new DateTime();
                $dateTime->setDate($matches[1], $matches[2], $matches[3]);
                $dateTime->setTime($matches[4], $matches[5], $matches[6]);
                return $dateTime;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Phân tích RRULE
     * 
     * @param string $rruleString Chuỗi RRULE
     * @return array Mảng các thành phần RRULE
     */
    private function parseRRule($rruleString)
    {
        $rrule = [];
        $parts = explode(';', $rruleString);

        foreach ($parts as $part) {
            $equalPos = strpos($part, '=');
            if ($equalPos !== false) {
                $key = substr($part, 0, $equalPos);
                $value = substr($part, $equalPos + 1);

                if ($key === 'COUNT') {
                    $rrule[$key] = intval($value);
                } else {
                    $rrule[$key] = $value;
                }
            }
        }

        return $rrule;
    }

    /**
     * Xử lý escape characters trong text
     * 
     * @param string $text Text cần xử lý
     * @return string
     */
    private function unescapeText($text)
    {
        return str_replace(['\,', '\;', '\n', '\\\\'], [',', ';', "\n", '\\'], $text);
    }

    /**
     * Xử lý một sự kiện và tạo ra các instance theo RRULE
     * 
     * @param array $event Sự kiện cần xử lý
     */
    private function processEvent($event)
    {
        if (!$event['DTSTART'] || !$event['DTEND']) {
            return;
        }

        $startDateTime = $event['DTSTART'];
        $endDateTime = $event['DTEND'];

        // Tạo sự kiện cơ bản
        $baseEvent = $this->createTimetableEvent($event, $startDateTime, $endDateTime);

        // Nếu không có RRULE, chỉ thêm sự kiện đơn
        if (empty($event['RRULE'])) {
            $this->events[] = $baseEvent;
            return;
        }

        // Xử lý RRULE với giới hạn COUNT
        $this->processRecurringEvent($event, $baseEvent);
    }

    /**
     * Xử lý sự kiện lặp lại với RRULE
     * 
     * @param array $event Sự kiện gốc
     * @param array $baseEvent Sự kiện cơ bản
     */
    private function processRecurringEvent($event, $baseEvent)
    {
        $rrule = $event['RRULE'];
        $startDateTime = $event['DTSTART'];
        $endDateTime = $event['DTEND'];

        // Kiểm tra loại lặp lại
        if (!isset($rrule['FREQ']) || $rrule['FREQ'] !== 'WEEKLY') {
            // Chỉ hỗ trợ WEEKLY hiện tại
            $this->events[] = $baseEvent;
            return;
        }

        // Lấy số lần lặp từ file .ics
        if (isset($rrule['COUNT'])) {
            $maxCount = $rrule['COUNT'];
            // Chỉ giới hạn nếu COUNT quá lớn (tránh spam)
            if ($maxCount > 1000) {
                $maxCount = 1000; // Giới hạn cực đại để tránh spam
            }
        } else {
            // Nếu không có COUNT, mặc định 52 tuần (1 năm học)
            $maxCount = 52;
        }

        // Tạo danh sách các ngày bị loại trừ
        $excludeDates = [];
        foreach ($event['EXDATE'] as $exDate) {
            if ($exDate) {
                $excludeDates[] = $exDate->format('Y-m-d H:i:s');
            }
        }

        // Tạo các sự kiện lặp lại
        for ($i = 0; $i < $maxCount; $i++) {
            $currentStart = clone $startDateTime;
            $currentEnd = clone $endDateTime;

            // Thêm tuần
            $currentStart->add(new DateInterval('P' . ($i * 7) . 'D'));
            $currentEnd->add(new DateInterval('P' . ($i * 7) . 'D'));

            // Kiểm tra xem ngày có bị loại trừ không
            $currentDateString = $currentStart->format('Y-m-d H:i:s');
            if (in_array($currentDateString, $excludeDates)) {
                continue;
            }

            // Tạo sự kiện cho tuần này
            $weeklyEvent = $this->createTimetableEvent($event, $currentStart, $currentEnd);
            $this->events[] = $weeklyEvent;
        }
    }

    /**
     * Tạo sự kiện thời khóa biểu từ dữ liệu .ics
     * 
     * @param array $event Dữ liệu sự kiện từ .ics
     * @param DateTime $startDateTime Thời gian bắt đầu
     * @param DateTime $endDateTime Thời gian kết thúc
     * @return array Sự kiện thời khóa biểu
     */
    private function createTimetableEvent($event, $startDateTime, $endDateTime)
    {
        // Phân tích SUMMARY để lấy tên môn học và phòng học
        $summary = $event['SUMMARY'];
        $subjectName = $summary;
        $classroom = '';

        // Tách tên môn học và phòng học từ SUMMARY
        // Định dạng: "CT22801 - Tường lửa, TH08DI"
        if (preg_match('/^([^,]+),\s*(.+)$/', $summary, $matches)) {
            $subjectName = trim($matches[1]);
            $classroom = trim($matches[2]);
        }

        // Phân tích DESCRIPTION để lấy thông tin bổ sung
        $description = $event['DESCRIPTION'];
        $teacher = '';
        $notes = '';

        // Tách thông tin từ DESCRIPTION
        // Định dạng: "Tiết 1, phòng TH08DI"
        if (preg_match('/Tiết\s+(\d+),\s*phòng\s+(.+)/', $description, $matches)) {
            $notes = 'Tiết ' . $matches[1];
            if (empty($classroom)) {
                $classroom = trim($matches[2]);
            }
        }

        // Xác định ngày trong tuần cho database (1=Sunday, 2=Monday, ..., 7=Saturday)
        // PHP N: 1=Monday, 2=Tuesday, ..., 7=Sunday
        $phpDayOfWeek = intval($startDateTime->format('N')); // 1-7

        if ($phpDayOfWeek == 7) {
            // Chủ nhật (7) -> 1 trong database
            $dayOfWeek = 1;
        } else {
            // Monday(1) -> 2, Tuesday(2) -> 3, ..., Saturday(6) -> 7
            $dayOfWeek = $phpDayOfWeek + 1;
        }

        return [
            'subject_name' => $subjectName,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startDateTime->format('H:i:s'),
            'end_time' => $endDateTime->format('H:i:s'),
            'classroom' => $classroom,
            'teacher' => $teacher,
            'notes' => $notes,
            'event_date' => $startDateTime->format('Y-m-d'),
            'uid' => $event['UID'] ?? ''
        ];
    }

    /**
     * Lấy danh sách các sự kiện đã phân tích
     * 
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Lấy thống kê về các sự kiện
     * 
     * @return array
     */
    public function getStatistics()
    {
        $totalEvents = count($this->events);
        $subjects = [];
        $days = [];

        foreach ($this->events as $event) {
            $subjects[$event['subject_name']] = ($subjects[$event['subject_name']] ?? 0) + 1;
            $days[$event['day_of_week']] = ($days[$event['day_of_week']] ?? 0) + 1;
        }

        return [
            'total_events' => $totalEvents,
            'subjects' => $subjects,
            'days' => $days
        ];
    }
}
