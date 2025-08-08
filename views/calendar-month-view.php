<?php
/**
 * Month View Template
 * Hiển thị lịch theo tháng dạng lưới
 */

// Tính toán ngày đầu và cuối tháng
$firstDayOfMonth = clone $currentDate;
$firstDayOfMonth->modify('first day of this month');

$lastDayOfMonth = clone $currentDate;
$lastDayOfMonth->modify('last day of this month');

// Tính toán ngày đầu tuần của tuần chứa ngày đầu tháng
$startDate = clone $firstDayOfMonth;
$dayOfWeek = $firstDayOfMonth->format('N') - 1; // 0 = Monday
$startDate->modify("-{$dayOfWeek} days");

// Tính toán ngày cuối tuần của tuần chứa ngày cuối tháng
$endDate = clone $lastDayOfMonth;
$dayOfWeek = $lastDayOfMonth->format('N') - 1; // 0 = Monday
$endDate->modify("+" . (6 - $dayOfWeek) . " days");

// Tạo mảng tất cả các ngày sẽ hiển thị
$calendarDays = [];
$current = clone $startDate;
while ($current <= $endDate) {
    $calendarDays[] = clone $current;
    $current->modify('+1 day');
}

// Phân loại events theo ngày
$eventsByDay = [];
foreach ($events as $event) {
    $eventStart = $event->getStart();
    if ($eventStart->getDate()) {
        // All-day event
        $eventDate = new DateTime($eventStart->getDate());
    } else {
        // Timed event
        $eventDate = new DateTime($eventStart->getDateTime());
    }
    
    $dayKey = $eventDate->format('Y-m-d');
    if (!isset($eventsByDay[$dayKey])) {
        $eventsByDay[$dayKey] = [];
    }
    $eventsByDay[$dayKey][] = $event;
}

// Tên các thứ trong tuần
$weekDayNames = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
?>

<div class="calendar-month-container">
    <!-- Month Header -->
    <div class="month-header bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg mb-6">
        <div class="text-center">
            <h2 class="text-3xl font-bold"><?php echo $currentDate->format('F Y'); ?></h2>
            <p class="text-lg opacity-90"><?php echo $currentDate->format('Y'); ?></p>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid-container border rounded-lg overflow-hidden">
        <!-- Week Days Header -->
        <div class="grid grid-cols-7 bg-gray-50">
            <?php foreach ($weekDayNames as $dayName): ?>
                <div class="p-4 text-center font-semibold text-gray-700 border-r last:border-r-0">
                    <?php echo $dayName; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Calendar Days -->
        <div class="grid grid-cols-7 gap-0">
            <?php 
            $weeks = array_chunk($calendarDays, 7);
            foreach ($weeks as $week): 
            ?>
                <?php foreach ($week as $day): ?>
                    <?php
                    $dayKey = $day->format('Y-m-d');
                    $isCurrentMonth = $day->format('m') === $currentDate->format('m');
                    $isToday = $dayKey === date('Y-m-d');
                    $dayEvents = $eventsByDay[$dayKey] ?? [];
                    ?>
                    <div class="calendar-cell border-r border-b last:border-r-0 p-2 min-h-[120px] <?php 
                        echo $isCurrentMonth ? 'bg-white' : 'bg-gray-50'; 
                        echo $isToday ? ' today ring-2 ring-blue-500' : '';
                    ?> hover:bg-blue-50 transition-colors duration-200 group cursor-pointer"
                         onclick="navigateToDay('<?php echo $dayKey; ?>')">
                        
                        <!-- Day Number -->
                        <div class="day-number mb-2 flex items-center justify-between">
                            <span class="text-sm font-semibold <?php 
                                echo $isCurrentMonth ? 'text-gray-800' : 'text-gray-400';
                                echo $isToday ? ' bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center' : '';
                            ?>">
                                <?php echo $day->format('j'); ?>
                            </span>
                            
                            <!-- Add Event Button (show on hover) -->
                            <button class="opacity-0 group-hover:opacity-100 bg-blue-500 text-white w-5 h-5 rounded-full text-xs flex items-center justify-center transition-opacity duration-200"
                                    onclick="event.stopPropagation(); addEventOnDay('<?php echo $dayKey; ?>')"
                                    title="Thêm sự kiện">
                                +
                            </button>
                        </div>

                        <!-- Events -->
                        <div class="events-container space-y-1">
                            <?php 
                            $displayedEvents = 0;
                            $maxEvents = 3;
                            foreach ($dayEvents as $event): 
                                if ($displayedEvents >= $maxEvents) break;
                                
                                $eventTitle = $event->getSummary() ?: 'Không có tiêu đề';
                                $eventId = $event->getId();
                                $eventStart = $event->getStart();
                                
                                // Determine event type and time
                                if ($eventStart->getDate()) {
                                    $eventType = 'all-day';
                                    $timeText = '';
                                    $bgColor = 'bg-green-500';
                                } else {
                                    $eventType = 'timed';
                                    $startTime = new DateTime($eventStart->getDateTime());
                                    $timeText = $startTime->format('H:i');
                                    $bgColor = 'bg-blue-500';
                                }
                                
                                $displayedEvents++;
                            ?>
                                <div class="event-item <?php echo $bgColor; ?> text-white text-xs p-1 rounded cursor-pointer hover:opacity-80 transition-opacity duration-200"
                                     onclick="event.stopPropagation(); showEventDetails('<?php echo htmlspecialchars($eventId); ?>')"
                                     title="<?php echo htmlspecialchars($eventTitle); ?>">
                                    <div class="flex items-center">
                                        <?php if ($timeText): ?>
                                            <span class="font-medium mr-1"><?php echo $timeText; ?></span>
                                        <?php endif; ?>
                                        <span class="truncate flex-1"><?php echo htmlspecialchars(mb_substr($eventTitle, 0, 15)); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- More events indicator -->
                            <?php if (count($dayEvents) > $maxEvents): ?>
                                <div class="text-xs text-gray-500 hover:text-blue-500 cursor-pointer"
                                     onclick="event.stopPropagation(); showDayDetails('<?php echo $dayKey; ?>')">
                                    +<?php echo count($dayEvents) - $maxEvents; ?> sự kiện khác
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Month Summary -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- This Month Stats -->
        <div class="bg-white border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                Thống kê tháng này
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tổng sự kiện:</span>
                    <span class="font-semibold"><?php echo count($events); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Sự kiện cả ngày:</span>
                    <span class="font-semibold"><?php 
                        $allDayCount = 0;
                        foreach ($events as $event) {
                            if ($event->getStart()->getDate()) $allDayCount++;
                        }
                        echo $allDayCount;
                    ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Sự kiện có giờ:</span>
                    <span class="font-semibold"><?php echo count($events) - $allDayCount; ?></span>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-clock mr-2 text-green-500"></i>
                Sự kiện sắp tới
            </h3>
            <div class="space-y-2">
                <?php
                $upcomingEvents = [];
                $today = new DateTime();
                foreach ($events as $event) {
                    $eventStart = $event->getStart();
                    $eventDate = $eventStart->getDate() ? 
                        new DateTime($eventStart->getDate()) : 
                        new DateTime($eventStart->getDateTime());
                    
                    if ($eventDate >= $today) {
                        $upcomingEvents[] = ['event' => $event, 'date' => $eventDate];
                    }
                }
                
                // Sort by date
                usort($upcomingEvents, function($a, $b) {
                    return $a['date'] <=> $b['date'];
                });
                
                $upcomingCount = 0;
                foreach (array_slice($upcomingEvents, 0, 5) as $item):
                    $event = $item['event'];
                    $eventDate = $item['date'];
                    $upcomingCount++;
                ?>
                    <div class="text-sm">
                        <div class="font-medium text-gray-800 truncate"><?php echo htmlspecialchars($event->getSummary() ?: 'Không có tiêu đề'); ?></div>
                        <div class="text-gray-500"><?php echo $eventDate->format('d/m/Y'); ?></div>
                    </div>
                <?php endforeach; ?>
                
                <?php if ($upcomingCount === 0): ?>
                    <div class="text-sm text-gray-500">Không có sự kiện sắp tới</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-purple-500"></i>
                Thao tác nhanh
            </h3>
            <div class="space-y-3">
                <button onclick="addEventToday()" 
                        class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Thêm sự kiện hôm nay
                </button>
                <button onclick="goToToday()" 
                        class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Về hôm nay
                </button>
                <a href="calendar-events.php" 
                   class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 block text-center">
                    <i class="fas fa-list mr-2"></i>
                    Xem danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-cell {
    position: relative;
    transition: all 0.2s ease;
}

.calendar-cell:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.calendar-cell.today {
    background: #eff6ff !important;
}

.event-item {
    transition: all 0.2s ease;
}

.event-item:hover {
    transform: scale(1.02);
}
</style>

<script>
function navigateToDay(date) {
    window.location.href = 'calendar-view.php?view=day&date=' + date;
}

function showEventDetails(eventId) {
    window.location.href = 'edit-event.php?event_id=' + encodeURIComponent(eventId);
}

function addEventOnDay(date) {
    window.location.href = 'add-event.php?date=' + date;
}

function showDayDetails(date) {
    window.location.href = 'calendar-view.php?view=day&date=' + date;
}

function addEventToday() {
    const today = new Date().toISOString().split('T')[0];
    window.location.href = 'add-event.php?date=' + today;
}

function goToToday() {
    const today = new Date().toISOString().split('T')[0];
    window.location.href = 'calendar-view.php?view=month&date=' + today;
}
</script>