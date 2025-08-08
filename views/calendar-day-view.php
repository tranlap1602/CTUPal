<?php
/**
 * Day View Template
 * Hiển thị lịch theo ngày với timeline chi tiết
 */

// Phân loại events theo giờ
$eventsByHour = [];
$allDayEvents = [];

foreach ($events as $event) {
    $eventStart = $event->getStart();
    if ($eventStart->getDate()) {
        // All-day event
        $allDayEvents[] = $event;
    } else {
        // Timed event
        $startTime = new DateTime($eventStart->getDateTime());
        $hour = $startTime->format('H');
        if (!isset($eventsByHour[$hour])) {
            $eventsByHour[$hour] = [];
        }
        $eventsByHour[$hour][] = $event;
    }
}

// Tạo time slots (5:00 - 23:00)
$timeSlots = [];
for ($hour = 5; $hour <= 23; $hour++) {
    $timeSlots[] = $hour;
}
?>

<div class="calendar-day-container max-w-4xl mx-auto">
    <!-- Date Header -->
    <div class="day-header bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg mb-6">
        <div class="text-center">
            <h2 class="text-3xl font-bold"><?php echo $currentDate->format('d'); ?></h2>
            <p class="text-lg"><?php echo $currentDate->format('l, F Y'); ?></p>
        </div>
    </div>

    <!-- All-day Events Section -->
    <?php if (!empty($allDayEvents)): ?>
        <div class="all-day-section mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-calendar-day mr-2"></i>
                Sự kiện cả ngày
            </h3>
            <div class="space-y-2">
                <?php foreach ($allDayEvents as $event): ?>
                    <?php
                    $eventTitle = $event->getSummary() ?: 'Không có tiêu đề';
                    $eventId = $event->getId();
                    $eventLocation = $event->getLocation();
                    $eventDescription = $event->getDescription();
                    ?>
                    <div class="bg-green-100 border border-green-300 rounded-lg p-4 hover:shadow-md transition-all duration-200 cursor-pointer"
                         onclick="showEventDetails('<?php echo htmlspecialchars($eventId); ?>')">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-green-800"><?php echo htmlspecialchars($eventTitle); ?></h4>
                                <?php if ($eventLocation): ?>
                                    <p class="text-sm text-green-700 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?php echo htmlspecialchars($eventLocation); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($eventDescription): ?>
                                    <p class="text-sm text-green-600 mt-2 line-clamp-2">
                                        <?php echo htmlspecialchars($eventDescription); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="ml-4">
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                    Cả ngày
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Timeline -->
    <div class="timeline-container border rounded-lg overflow-hidden">
        <?php foreach ($timeSlots as $hour): ?>
            <?php
            $timeLabel = sprintf('%02d:00', $hour);
            $currentTime = new DateTime();
            $isCurrentHour = ($currentDate->format('Y-m-d') === $currentTime->format('Y-m-d') && 
                            $hour === (int)$currentTime->format('H'));
            ?>
            <div class="time-slot border-b last:border-b-0 <?php echo $isCurrentHour ? 'bg-blue-50' : 'bg-white'; ?> hover:bg-gray-50 transition-colors duration-200 group">
                <div class="flex">
                    <!-- Time Label -->
                    <div class="w-20 p-4 border-r bg-gray-50 flex flex-col items-center justify-center">
                        <div class="time-label font-medium <?php echo $isCurrentHour ? 'text-blue-600' : 'text-gray-600'; ?>">
                            <?php echo $timeLabel; ?>
                        </div>
                        <?php if ($isCurrentHour): ?>
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1"></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Event Content -->
                    <div class="flex-1 p-4 relative min-h-[80px]">
                        <?php if (isset($eventsByHour[$hour])): ?>
                            <?php foreach ($eventsByHour[$hour] as $event): ?>
                                <?php
                                $eventTitle = $event->getSummary() ?: 'Không có tiêu đề';
                                $eventId = $event->getId();
                                $eventLocation = $event->getLocation();
                                $eventDescription = $event->getDescription();
                                $startTime = new DateTime($event->getStart()->getDateTime());
                                $endTime = new DateTime($event->getEnd()->getDateTime());
                                
                                // Calculate event duration and position
                                $startMinute = (int)$startTime->format('i');
                                $duration = $endTime->getTimestamp() - $startTime->getTimestamp();
                                $durationMinutes = $duration / 60;
                                ?>
                                <div class="event-block bg-blue-100 border-l-4 border-blue-500 rounded-r-lg p-3 mb-2 hover:shadow-md transition-all duration-200 cursor-pointer"
                                     onclick="showEventDetails('<?php echo htmlspecialchars($eventId); ?>')">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-900 mb-1"><?php echo htmlspecialchars($eventTitle); ?></h4>
                                            <div class="text-sm text-blue-700 mb-2">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo $startTime->format('H:i'); ?> - <?php echo $endTime->format('H:i'); ?>
                                                <span class="text-blue-600 ml-2">(<?php echo floor($durationMinutes / 60); ?>h <?php echo $durationMinutes % 60; ?>m)</span>
                                            </div>
                                            <?php if ($eventLocation): ?>
                                                <div class="text-sm text-blue-600 mb-1">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    <?php echo htmlspecialchars($eventLocation); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($eventDescription): ?>
                                                <div class="text-sm text-blue-600 line-clamp-2">
                                                    <i class="fas fa-sticky-note mr-1"></i>
                                                    <?php echo htmlspecialchars($eventDescription); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4 flex flex-col space-y-1">
                                            <button class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 transition-colors duration-200"
                                                    onclick="event.stopPropagation(); editEvent('<?php echo htmlspecialchars($eventId); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Empty slot - show add button on hover -->
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button class="text-gray-400 hover:text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition-all duration-200 w-full text-left text-sm"
                                        onclick="addEventAt('<?php echo $currentDate->format('Y-m-d'); ?>', '<?php echo $timeLabel; ?>')">
                                    <i class="fas fa-plus mr-2"></i>
                                    Thêm sự kiện lúc <?php echo $timeLabel; ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.event-block {
    position: relative;
    transition: all 0.2s ease;
}

.event-block:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.time-slot {
    position: relative;
}

.time-slot::before {
    content: '';
    position: absolute;
    left: 80px;
    top: 0;
    right: 0;
    height: 1px;
    background: #e5e7eb;
}
</style>

<script>
function showEventDetails(eventId) {
    window.location.href = 'edit-event.php?event_id=' + encodeURIComponent(eventId);
}

function editEvent(eventId) {
    window.location.href = 'edit-event.php?event_id=' + encodeURIComponent(eventId);
}

function addEventAt(date, time) {
    const startDateTime = date + 'T' + time;
    window.location.href = 'add-event.php?start=' + encodeURIComponent(startDateTime);
}
</script>