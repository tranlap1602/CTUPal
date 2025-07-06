<!-- 
    File: views/timetable-list.php 
    Mục đích: Hiển thị thời khóa biểu của sinh viên dưới dạng danh sách
    Sử dụng: Include trong timetable.php
-->

<div class="timetable-list-section">
    <!-- Header của view -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-list mr-3 text-blue-500"></i>
            Thời khóa biểu (Danh sách)
        </h3>

        <!-- Thông tin tuần hiện tại -->
        <div class="text-right">
            <p class="text-sm text-gray-600">Tuần hiện tại</p>
            <p class="text-lg font-semibold text-blue-600">
                <?php echo date('d/m/Y', strtotime('monday this week')); ?> -
                <?php echo date('d/m/Y', strtotime('sunday this week')); ?>
            </p>
        </div>
    </div>

    <!-- Hiển thị theo từng ngày -->
    <div class="space-y-6">
        <?php foreach ($days as $day_num => $day_name): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header của ngày -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-4">
                    <h4 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-calendar-day mr-3"></i>
                        <?php echo $day_name; ?>
                        <span class="ml-2 text-sm font-normal opacity-75">
                            (<?php echo isset($schedule[$day_num]) ? count($schedule[$day_num]) : 0; ?> môn học)
                        </span>
                    </h4>
                </div>

                <!-- Nội dung môn học trong ngày -->
                <div class="p-6">
                    <?php if (isset($schedule[$day_num]) && count($schedule[$day_num]) > 0): ?>
                        <div class="space-y-4">
                            <?php 
                            // Sắp xếp môn học theo thời gian
                            usort($schedule[$day_num], function($a, $b) {
                                return strcmp($a['start_time'], $b['start_time']);
                            });
                            
                            foreach ($schedule[$day_num] as $index => $subject): 
                                // Xác định màu sắc cho môn học
                                $colors = [
                                    'bg-blue-50 border-blue-300 text-blue-800',
                                    'bg-green-50 border-green-300 text-green-800',
                                    'bg-purple-50 border-purple-300 text-purple-800',
                                    'bg-red-50 border-red-300 text-red-800',
                                    'bg-yellow-50 border-yellow-300 text-yellow-800',
                                    'bg-indigo-50 border-indigo-300 text-indigo-800',
                                    'bg-pink-50 border-pink-300 text-pink-800',
                                    'bg-orange-50 border-orange-300 text-orange-800'
                                ];
                                $color_class = $colors[$index % count($colors)];
                            ?>
                                <!-- Card môn học -->
                                <div class="<?php echo $color_class; ?> border-l-4 rounded-lg p-4 hover:shadow-md transition-all duration-200 group">
                                    <div class="flex items-center justify-between">
                                        <!-- Thông tin môn học -->
                                        <div class="flex-1">
                                            <div class="flex items-center flex-wrap gap-4">
                                                <!-- Tên môn học -->
                                                <h5 class="text-lg font-semibold group-hover:scale-105 transition-transform">
                                                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                                                </h5>

                                                <!-- Thời gian -->
                                                <div class="flex items-center bg-white/50 rounded-full px-3 py-1">
                                                    <i class="fas fa-clock mr-2 text-sm"></i>
                                                    <span class="text-sm font-medium">
                                                        <?php echo substr($subject['start_time'], 0, 5); ?> - 
                                                        <?php echo substr($subject['end_time'], 0, 5); ?>
                                                    </span>
                                                </div>

                                                <!-- Thời lượng -->
                                                <div class="flex items-center bg-white/50 rounded-full px-3 py-1">
                                                    <i class="fas fa-hourglass-half mr-2 text-sm"></i>
                                                    <span class="text-sm">
                                                        <?php 
                                                        $start = strtotime($subject['start_time']);
                                                        $end = strtotime($subject['end_time']);
                                                        $duration = ($end - $start) / 3600;
                                                        echo number_format($duration, 1) . ' giờ';
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Thông tin bổ sung -->
                                            <div class="mt-3 flex items-center flex-wrap gap-4 text-sm">
                                                <!-- Phòng học -->
                                                <?php if (!empty($subject['classroom'])): ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                                        <span class="font-medium">Phòng:</span>
                                                        <span class="ml-1"><?php echo htmlspecialchars($subject['classroom']); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Giảng viên -->
                                                <?php if (!empty($subject['teacher'])): ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-user mr-2 text-green-500"></i>
                                                        <span class="font-medium">GV:</span>
                                                        <span class="ml-1"><?php echo htmlspecialchars($subject['teacher']); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Ghi chú -->
                                                <?php if (!empty($subject['notes'])): ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>
                                                        <span class="font-medium">Ghi chú:</span>
                                                        <span class="ml-1"><?php echo htmlspecialchars($subject['notes']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Menu action -->
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="editSubject(<?php echo $subject['id']; ?>)"
                                                class="bg-white/70 hover:bg-white text-blue-600 px-3 py-2 rounded-lg transition-colors shadow-sm"
                                                title="Sửa môn học">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteSubject(<?php echo $subject['id']; ?>)"
                                                class="bg-white/70 hover:bg-white text-red-600 px-3 py-2 rounded-lg transition-colors shadow-sm"
                                                title="Xóa môn học">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Ngày không có môn học -->
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-calendar-times text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg mb-2">Không có môn học nào trong ngày này</p>
                            <p class="text-sm">Bạn có thể thêm môn học mới bằng cách click vào nút "Thêm môn học" bên dưới</p>
                            <button onclick="addSubjectToDay('<?php echo $day_num; ?>')"
                                class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center mx-auto transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Thêm môn học
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tổng số môn học -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-blue-600 mb-1">Tổng môn học</h4>
                    <p class="text-2xl font-bold text-blue-800">
                        <?php echo count($timetable_data ?? []); ?>
                    </p>
                </div>
                <div class="bg-blue-200 rounded-full p-3">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Tổng giờ học/tuần -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-green-600 mb-1">Giờ học/tuần</h4>
                    <p class="text-2xl font-bold text-green-800">
                        <?php
                        $total_hours = 0;
                        foreach ($timetable_data ?? [] as $subject) {
                            $start = strtotime($subject['start_time']);
                            $end = strtotime($subject['end_time']);
                            $total_hours += ($end - $start) / 3600;
                        }
                        echo number_format($total_hours, 1);
                        ?>
                    </p>
                </div>
                <div class="bg-green-200 rounded-full p-3">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ngày bận nhất -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-purple-600 mb-1">Ngày bận nhất</h4>
                    <p class="text-lg font-bold text-purple-800">
                        <?php
                        $busiest_day = '';
                        $max_subjects = 0;
                        foreach ($days as $day_num => $day_name) {
                            $subject_count = isset($schedule[$day_num]) ? count($schedule[$day_num]) : 0;
                            if ($subject_count > $max_subjects) {
                                $max_subjects = $subject_count;
                                $busiest_day = $day_name;
                            }
                        }
                        echo $busiest_day ?: 'Không có';
                        ?>
                    </p>
                    <p class="text-sm text-purple-600"><?php echo $max_subjects; ?> môn học</p>
                </div>
                <div class="bg-purple-200 rounded-full p-3">
                    <i class="fas fa-fire text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ngày rảnh nhất -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-yellow-600 mb-1">Ngày rảnh nhất</h4>
                    <p class="text-lg font-bold text-yellow-800">
                        <?php
                        $freest_day = '';
                        $min_subjects = PHP_INT_MAX;
                        foreach ($days as $day_num => $day_name) {
                            $subject_count = isset($schedule[$day_num]) ? count($schedule[$day_num]) : 0;
                            if ($subject_count < $min_subjects) {
                                $min_subjects = $subject_count;
                                $freest_day = $day_name;
                            }
                        }
                        echo $freest_day ?: 'Không có';
                        ?>
                    </p>
                    <p class="text-sm text-yellow-600"><?php echo $min_subjects; ?> môn học</p>
                </div>
                <div class="bg-yellow-200 rounded-full p-3">
                    <i class="fas fa-leaf text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm thêm môn học cho ngày cụ thể
    function addSubjectToDay(dayNum) {
        console.log('Thêm môn học cho ngày:', dayNum);
        // Có thể mở modal hoặc redirect đến trang thêm môn học
        // Tạm thời alert
        alert('Chức năng thêm môn học cho ' + dayNum + ' đang được phát triển!');
    }
</script> 