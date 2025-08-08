<?php
/**
 * List View Template (Fallback)
 * Hiển thị danh sách sự kiện đơn giản
 */

if (empty($events)): ?>
    <div class="text-center py-16">
        <div class="text-gray-400 mb-4">
            <i class="fas fa-calendar-day text-6xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Không có sự kiện nào</h3>
        <p class="text-gray-500 mb-4">
            Không tìm thấy sự kiện nào trong khoảng thời gian này.
        </p>
        <div class="flex justify-center space-x-4">
            <a href="add-event.php" 
               class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm sự kiện
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($events as $event): ?>
            <?php 
            $eventId = $event->getId();
            $summary = $event->getSummary() ?: 'Không có tiêu đề';
            $description = $event->getDescription() ?: '';
            $location = $event->getLocation() ?: '';
            
            // Format time
            $start = $event->getStart();
            $end = $event->getEnd();
            
            if ($start->getDate()) {
                // All-day event
                $startDate = new DateTime($start->getDate());
                $timeString = $startDate->format('d/m/Y') . ' (Cả ngày)';
                $colorClass = 'bg-green-100 border-green-300 text-green-800';
            } else {
                // Timed event
                $startTime = new DateTime($start->getDateTime());
                $endTime = new DateTime($end->getDateTime());
                
                if ($startTime->format('Y-m-d') === $endTime->format('Y-m-d')) {
                    // Same day
                    $timeString = $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('H:i');
                } else {
                    // Different days
                    $timeString = $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('d/m/Y H:i');
                }
                $colorClass = 'bg-blue-100 border-blue-300 text-blue-800';
            }
            ?>
            
            <div class="border <?php echo $colorClass; ?> rounded-lg p-6 hover:shadow-lg transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-calendar-check text-lg mt-1"></i>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-2">
                                    <?php echo htmlspecialchars($summary); ?>
                                </h3>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock w-4 mr-2"></i>
                                        <span><?php echo $timeString; ?></span>
                                    </div>
                                    
                                    <?php if ($location): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                                            <span><?php echo htmlspecialchars($location); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($description): ?>
                                        <div class="flex items-start">
                                            <i class="fas fa-sticky-note w-4 mr-2 mt-1"></i>
                                            <span class="line-clamp-2"><?php echo nl2br(htmlspecialchars($description)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ml-4 flex flex-col space-y-2">
                        <a href="edit-event.php?event_id=<?php echo urlencode($eventId); ?>" 
                           class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 text-center text-sm">
                            <i class="fas fa-edit mr-1"></i>
                            Chỉnh sửa
                        </a>
                        
                        <a href="https://calendar.google.com/calendar/u/0/r/eventedit/<?php echo urlencode($eventId); ?>" 
                           target="_blank"
                           class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 text-center text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            Google
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>