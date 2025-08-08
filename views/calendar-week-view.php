<?php
/**
 * Week View Template
 * Hiển thị lịch theo tuần với các cột ngày
 */

// Tính toán tuần hiện tại
$startOfWeek = clone $currentDate;
$dayOfWeek = $currentDate->format('N') - 1; // 0 = Monday
$startOfWeek->modify("-{$dayOfWeek} days");

// Tạo mảng 7 ngày trong tuần
$weekDays = [];
for ($i = 0; $i < 7; $i++) {
    $day = clone $startOfWeek;
    $day->modify("+{$i} days");
    $weekDays[] = $day;
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

// Tạo time slots (6:00 - 22:00)
$timeSlots = [];
for ($hour = 6; $hour <= 22; $hour++) {
    $timeSlots[] = sprintf('%02d:00', $hour);
}
?>

<div class="calendar-week-container">
    <!-- Header với các ngày trong tuần -->
    <div class="week-header bg-gray-50 border rounded-lg mb-4">
        <div class="grid grid-cols-8 gap-0">
            <div class="p-4 text-center font-semibold text-gray-600 border-r">
                Giờ
            </div>
            <?php foreach ($weekDays as $day): ?>
                <div class="p-4 text-center border-r last:border-r-0 <?php echo $day->format('Y-m-d') === date('Y-m-d') ? 'bg-blue-50 text-blue-800' : ''; ?>">
                    <div class="font-semibold"><?php echo $day->format('D'); ?></div>
                    <div class="text-lg <?php echo $day->format('Y-m-d') === date('Y-m-d') ? 'bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto' : ''; ?>">
                        <?php echo $day->format('j'); ?>
                    </div>
                    <div class="text-sm text-gray-500"><?php echo $day->format('M'); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="week-grid border rounded-lg overflow-hidden">
        <?php foreach ($timeSlots as $timeSlot): ?>
            <div class="grid grid-cols-8 gap-0 border-b last:border-b-0">
                <!-- Time Label -->
                <div class="p-3 bg-gray-50 border-r text-sm font-medium text-gray-600 flex items-center justify-center">
                    <?php echo $timeSlot; ?>
                </div>
                
                <!-- Day Columns -->
                <?php foreach ($weekDays as $day): ?>
                    <div class="p-2 border-r last:border-r-0 min-h-[60px] relative bg-white hover:bg-gray-50 transition-colors duration-200 group">
                        <!-- Events for this time slot -->
                        <?php
                        $dayKey = $day->format('Y-m-d');
                        if (isset($eventsByDay[$dayKey])) {
                            foreach ($eventsByDay[$dayKey] as $event) {
                                $eventStart = $event->getStart();
                                $eventEnd = $event->getEnd();
                                
                                // Check if event is in this time slot
                                if (!$eventStart->getDate()) { // Timed event
                                    $startTime = new DateTime($eventStart->getDateTime());
                                    $endTime = new DateTime($eventEnd->getDateTime());
                                    
                                    $slotTime = DateTime::createFromFormat('H:i', $timeSlot);
                                    $slotTime->setDate($day->format('Y'), $day->format('m'), $day->format('d'));
                                    
                                    $nextSlotTime = clone $slotTime;
                                    $nextSlotTime->modify('+1 hour');
                                    
                                    if ($startTime < $nextSlotTime && $endTime > $slotTime) {
                                        $eventTitle = $event->getSummary() ?: 'Không có tiêu đề';
                                        $eventId = $event->getId();
                                        $eventLocation = $event->getLocation();
                                        ?>
                                        <div class="event-item text-xs p-1 mb-1 rounded cursor-pointer" 
                                             onclick="showEventDetails('<?php echo htmlspecialchars($eventId); ?>')"
                                             title="<?php echo htmlspecialchars($eventTitle . ($eventLocation ? ' - ' . $eventLocation : '')); ?>">
                                            <div class="font-medium truncate"><?php echo htmlspecialchars($eventTitle); ?></div>
                                            <div class="text-xs opacity-80"><?php echo $startTime->format('H:i'); ?></div>
                                        </div>
                                        <?php
                                    }
                                } else { // All-day event
                                    if ($timeSlot === '06:00') { // Show all-day events only in first slot
                                        $eventTitle = $event->getSummary() ?: 'Không có tiêu đề';
                                        $eventId = $event->getId();
                                        ?>
                                        <div class="event-item bg-green-500 text-xs p-1 mb-1 rounded cursor-pointer" 
                                             onclick="showEventDetails('<?php echo htmlspecialchars($eventId); ?>')"
                                             title="<?php echo htmlspecialchars($eventTitle); ?>">
                                            <div class="font-medium truncate"><?php echo htmlspecialchars($eventTitle); ?></div>
                                            <div class="text-xs opacity-80">Cả ngày</div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                        
                        <!-- Add event button (show on hover) -->
                        <button class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 bg-blue-500 text-white w-5 h-5 rounded-full text-xs flex items-center justify-center transition-opacity duration-200"
                                onclick="addEventAt('<?php echo $day->format('Y-m-d'); ?>', '<?php echo $timeSlot; ?>')"
                                title="Thêm sự kiện">
                            +
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function showEventDetails(eventId) {
    window.location.href = 'edit-event.php?event_id=' + encodeURIComponent(eventId);
}

function addEventAt(date, time) {
    const startDateTime = date + 'T' + time;
    window.location.href = 'add-event.php?start=' + encodeURIComponent(startDateTime);
}
</script>