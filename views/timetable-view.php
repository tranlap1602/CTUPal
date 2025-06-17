<!-- 
    File: views/timetable-view.php 
    Mục đích: Hiển thị thời khóa biểu của sinh viên dưới dạng bảng
    Sử dụng: Include trong timetable.php
-->

<div class="timetable-section">
    <!-- Header của view -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-calendar-week mr-3 text-blue-500"></i>
            Thời khóa biểu của tôi
        </h3>

        <!-- Thông tin tuần hiện tại -->
        <div class="text-right">
            <p class="text-sm text-gray-600">Tuần hiện tại</p>
            <p class="text-lg font-semibold text-blue-600">
                <?php echo date('d/m/Y', strtotime('monday this week')); ?> -
                <?php echo date('d/m/Y', strtotime('friday this week')); ?>
            </p>
        </div>
    </div>

    <!-- Bảng thời khóa biểu responsive -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <table class="w-full min-w-4xl">
            <!-- Header bảng -->
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                <tr>
                    <th class="px-4 py-4 text-left font-semibold min-w-32">
                        <i class="fas fa-clock mr-2"></i>Thời gian
                    </th>
                    <?php foreach ($days as $day_num => $day_name): ?>
                        <th class="px-4 py-4 text-center font-semibold min-w-40">
                            <i class="fas fa-calendar-day mr-2"></i><?php echo $day_name; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <!-- Nội dung bảng -->
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($time_slots as $slot_index => $slot): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Cột thời gian -->
                        <td class="px-4 py-6 bg-gray-50 font-medium text-gray-700 border-r border-gray-200">
                            <div class="text-center">
                                <div class="text-sm font-semibold"><?php echo $slot['start']; ?></div>
                                <div class="text-xs text-gray-500">-</div>
                                <div class="text-sm font-semibold"><?php echo $slot['end']; ?></div>
                            </div>
                        </td>

                        <!-- Cột cho mỗi ngày trong tuần -->
                        <?php foreach ($days as $day_num => $day_name): ?>
                            <td class="px-4 py-6 text-center align-top">
                                <?php
                                // Tìm môn học trong khung giờ này
                                $subject_found = false;
                                if (isset($schedule[$day_num])) {
                                    foreach ($schedule[$day_num] as $subject) {
                                        $subject_start = substr($subject['start_time'], 0, 5);
                                        $subject_end = substr($subject['end_time'], 0, 5);

                                        // Kiểm tra xem môn học có nằm trong khung giờ này không
                                        if ($subject_start <= $slot['end'] && $subject_end >= $slot['start']) {
                                            $subject_found = true;

                                            // Xác định màu sắc cho môn học
                                            $colors = [
                                                'bg-blue-100 border-blue-300 text-blue-800',
                                                'bg-green-100 border-green-300 text-green-800',
                                                'bg-purple-100 border-purple-300 text-purple-800',
                                                'bg-red-100 border-red-300 text-red-800',
                                                'bg-yellow-100 border-yellow-300 text-yellow-800',
                                                'bg-indigo-100 border-indigo-300 text-indigo-800'
                                            ];
                                            $color_class = $colors[array_rand($colors)];
                                ?>
                                            <!-- Card môn học -->
                                            <div class="<?php echo $color_class; ?> border-2 rounded-lg p-3 cursor-pointer hover:shadow-md transition-all duration-200 group">
                                                <!-- Tên môn học -->
                                                <div class="font-semibold text-sm mb-1 group-hover:scale-105 transition-transform">
                                                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                                                </div>

                                                <!-- Thời gian -->
                                                <div class="text-xs opacity-75 mb-1">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <?php echo $subject_start . ' - ' . $subject_end; ?>
                                                </div>

                                                <!-- Phòng học (nếu có) -->
                                                <?php if (!empty($subject['classroom'])): ?>
                                                    <div class="text-xs opacity-75 mb-1">
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                        <?php echo htmlspecialchars($subject['classroom']); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Giảng viên (nếu có) -->
                                                <?php if (!empty($subject['teacher'])): ?>
                                                    <div class="text-xs opacity-75">
                                                        <i class="fas fa-user mr-1"></i>
                                                        <?php echo htmlspecialchars($subject['teacher']); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Menu action nhỏ -->
                                                <div class="mt-2 flex justify-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button onclick="editSubject(<?php echo $subject['id']; ?>)"
                                                        class="text-xs bg-white/50 hover:bg-white/80 px-2 py-1 rounded transition-colors"
                                                        title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteSubject(<?php echo $subject['id']; ?>)"
                                                        class="text-xs bg-white/50 hover:bg-white/80 px-2 py-1 rounded transition-colors"
                                                        title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                    <?php
                                            break; // Chỉ hiển thị môn học đầu tiên trong khung giờ
                                        }
                                    }
                                }

                                // Nếu không có môn học nào trong khung giờ này
                                if (!$subject_found):
                                    ?>
                                    <!-- Ô trống có thể click để thêm môn học -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 min-h-20 flex items-center justify-center text-gray-400 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition-all duration-200 cursor-pointer group"
                                        onclick="addSubjectToSlot('<?php echo $day_num; ?>', '<?php echo $slot['start']; ?>', '<?php echo $slot['end']; ?>')">
                                        <div class="text-center">
                                            <i class="fas fa-plus text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                            <div class="text-xs">Thêm môn</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Chú thích và thống kê -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chú thích -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Chú thích
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-3"></div>
                    <span>Môn học chính</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 border-2 border-dashed border-gray-300 rounded mr-3"></div>
                    <span>Ô trống - Click để thêm môn học</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-mouse-pointer mr-3 text-blue-500"></i>
                    <span>Hover để xem thao tác chỉnh sửa</span>
                </div>
            </div>
        </div>

        <!-- Thống kê nhanh -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>
                Thống kê
            </h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        <?php echo count($timetable_data ?? []); ?>
                    </div>
                    <div class="text-gray-600">Tổng môn học</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">
                        <?php
                        $total_hours = 0;
                        foreach ($timetable_data ?? [] as $subject) {
                            $start = strtotime($subject['start_time']);
                            $end = strtotime($subject['end_time']);
                            $total_hours += ($end - $start) / 3600;
                        }
                        echo number_format($total_hours, 1);
                        ?>
                    </div>
                    <div class="text-gray-600">Giờ học/tuần</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Các nút hành động -->
    <div class="mt-8 flex flex-wrap gap-4 justify-center">
        <button onclick="editTimetable()"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg">
            <i class="fas fa-edit"></i>
            <span>Chỉnh sửa thời khóa biểu</span>
        </button>

        <button onclick="exportTimetable()"
            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg">
            <i class="fas fa-file-export"></i>
            <span>Xuất ra Excel</span>
        </button>

        <button onclick="printTimetable()"
            class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-md hover:shadow-lg">
            <i class="fas fa-print"></i>
            <span>In thời khóa biểu</span>
        </button>
    </div>
</div>

<!-- JavaScript functions for this view -->
<script>
    /**
     * Hàm thêm môn học vào khung giờ cụ thể
     */
    function addSubjectToSlot(dayOfWeek, startTime, endTime) {
        // Chuyển sang tab import và điền sẵn thông tin
        showView('import');

        // Đợi DOM cập nhật rồi điền form
        setTimeout(() => {
            const daySelect = document.getElementById('day-of-week');
            const startInput = document.getElementById('start-time');
            const endInput = document.getElementById('end-time');

            if (daySelect) daySelect.value = dayOfWeek;
            if (startInput) startInput.value = startTime;
            if (endInput) endInput.value = endTime;

            // Focus vào tên môn học
            const subjectInput = document.getElementById('subject-name');
            if (subjectInput) subjectInput.focus();
        }, 100);
    }

    /**
     * Hàm chỉnh sửa môn học
     */
    function editSubject(subjectId) {
        if (confirm('Chức năng chỉnh sửa môn học đang được phát triển. Bạn muốn tiếp tục?')) {
            // TODO: Implement edit subject functionality
            console.log('Edit subject ID:', subjectId);
        }
    }

    /**
     * Hàm xóa môn học
     */
    function deleteSubject(subjectId) {
        if (confirm('Bạn có chắc chắn muốn xóa môn học này?')) {
            // TODO: Implement delete subject functionality
            console.log('Delete subject ID:', subjectId);
            alert('Chức năng xóa môn học đang được phát triển!');
        }
    }

    /**
     * Hàm in thời khóa biểu
     */
    function printTimetable() {
        if (confirm('Bạn muốn in thời khóa biểu?')) {
            window.print();
        }
    }
</script>