<!-- 
    File: views/timetable-list.php 
    Mục đích: Hiển thị thời khóa biểu của sinh viên dưới dạng danh sách
    Sử dụng: Include trong timetable.php
-->

<div class="timetable-list-section">
    <!-- Header của view -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center mb-4">
            <i class="fas fa-list mr-3 text-blue-500"></i>
            Thời khóa biểu
        </h3>

        <!-- Navigation tuần -->
        <div class="flex items-center justify-center space-x-3 bg-gray-50 rounded-lg p-3 border border-gray-200">

            <!-- Nút về tuần hiện tại -->
            <button onclick="goToCurrentWeek()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md flex items-center transition-colors shadow-sm text-sm"
                title="Về tuần hiện tại">
                <i class="fas fa-home mr-1"></i>
                Hôm nay
            </button>
            <!-- Nút tuần trước -->
            <button onclick="changeWeek(-1)"
                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md flex items-center transition-colors shadow-sm text-sm"
                title="Tuần trước">
                <i class="fas fa-chevron-left mr-1"></i>
            </button>

            <!-- Thông tin tuần hiện tại -->
            <div class="text-center bg-blue-50 rounded-md px-3 py-1.5 border border-blue-200 flex-1 max-w-xs">
                <p class="text-xs text-blue-600 font-medium">
                    Tuần <span id="current-week-number"><?php echo $currentWeekInfo['week_number']; ?></span>
                </p>
                <p class="text-sm font-semibold text-blue-800" id="current-week-range">
                    <?php echo $currentWeekInfo['week_range']; ?>
                </p>
            </div>

            <!-- Nút tuần sau -->
            <button onclick="changeWeek(1)"
                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md flex items-center transition-colors shadow-sm text-sm"
                title="Tuần sau">
                <i class="fas fa-chevron-right ml-1"></i>
            </button>

        </div>
    </div>

    <!-- Hiển thị theo từng ngày -->
    <div class="space-y-5">
        <?php 
        // Xác định ngày hiện tại (1=Chủ nhật, 2=Thứ 2, ..., 7=Thứ 7)
        $current_day_of_week = date('w') + 1; // date('w') trả về 0-6, chúng ta cần 1-7
        $is_current_week = ($currentWeekInfo['week_offset'] == 0); // Chỉ đánh dấu khi ở tuần hiện tại
        
        foreach ($days as $day_num => $day_name): 
            $is_today = ($is_current_week && $day_num == $current_day_of_week);
            $header_class = $is_today 
                ? "bg-gradient-to-r from-orange-500 to-red-600 text-white px-4 py-2 ring-4 ring-orange-200" 
                : "bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2";
            $container_class = $is_today 
                ? "bg-white rounded-lg shadow-lg border-2 border-orange-400 overflow-hidden transform scale-105" 
                : "bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden";
        ?>
            <div class="<?php echo $container_class; ?>">
                <!-- Header của ngày -->
                <div class="<?php echo $header_class; ?>">
                    <h4 class="text-base font-semibold flex items-center">
                        <i class="fas fa-calendar-day mr-2 text-sm"></i>
                        <?php echo $day_name; ?>
                        <?php if ($is_today): ?>
                            <span class="ml-2 bg-white/20 text-xs px-2 py-0.5 rounded-full font-medium">
                                Hôm nay
                            </span>
                        <?php endif; ?>
                    </h4>
                </div>

                <!-- Nội dung môn học trong ngày -->
                <div class="p-6">
                    <?php if (isset($schedule[$day_num]) && count($schedule[$day_num]) > 0): ?>
                        <div class="space-y-4">
                            <?php
                            // Sắp xếp môn học theo thời gian
                            usort($schedule[$day_num], function ($a, $b) {
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
                                            <div class="flex items-center flex-wrap gap-2">
                                                <!-- Tên môn học -->
                                                <h5 class="text-base font-semibold group-hover:scale-105 transition-transform">
                                                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                                                </h5>

                                                <!-- Thời gian -->
                                                <div class="flex items-center bg-white/50 rounded-full px-2 py-1">
                                                    <i class="fas fa-clock mr-1 text-xs"></i>
                                                    <span class="text-xs font-medium">
                                                        <?php echo substr($subject['start_time'], 0, 5); ?> -
                                                        <?php echo substr($subject['end_time'], 0, 5); ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Thông tin bổ sung -->
                                            <div class="mt-2 flex items-center flex-wrap gap-2 text-xs">
                                                <!-- Phòng học -->
                                                <?php if (!empty($subject['classroom'])): ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i>
                                                        <span class="font-medium">Phòng:</span>
                                                        <span class="ml-1"><?php echo htmlspecialchars($subject['classroom']); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Ghi chú -->
                                                <?php if (!empty($subject['notes'])): ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-sticky-note mr-1 text-yellow-500"></i>
                                                        <span class="font-medium">Ghi chú:</span>
                                                        <span class="ml-1"><?php echo htmlspecialchars($subject['notes']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Menu action -->
                                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="editSubject(<?php echo $subject['id']; ?>)"
                                                class="bg-white/70 hover:bg-white text-blue-600 px-2 py-1 rounded-lg transition-colors shadow-sm"
                                                title="Sửa môn học">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button onclick="deleteSubject(<?php echo $subject['id']; ?>)"
                                                class="bg-white/70 hover:bg-white text-red-600 px-2 py-1 rounded-lg transition-colors shadow-sm"
                                                title="Xóa môn học">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Ngày không có môn học -->
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-3 text-gray-300"></i>
                            <p class="text-base mb-2">Không có môn học</p>
                            <button onclick="showView('import')"
                                class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i>
                                Thêm môn học
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Biến global cho tuần hiện tại
    let currentWeekOffset = <?php echo $currentWeekInfo['week_offset']; ?>;
    let academicYear = <?php echo $currentWeekInfo['academic_year']; ?>;
    let currentAcademicWeek = <?php echo $currentWeekInfo['week_number']; ?>;

    /**
     * Cập nhật hiển thị thông tin tuần
     */
    function updateWeekDisplay() {
        // Highlight nút "Hôm nay" nếu đang ở tuần hiện tại
        const homeButton = document.querySelector('button[onclick="goToCurrentWeek()"]');
        if (currentWeekOffset === 0) {
            homeButton.classList.add('bg-blue-600');
            homeButton.classList.remove('bg-blue-500');
        } else {
            homeButton.classList.add('bg-blue-500');
            homeButton.classList.remove('bg-blue-600');
        }

        // Lưu state vào localStorage
        localStorage.setItem('timetable_week_offset', currentWeekOffset);

        // Chỉ load dữ liệu nếu thực sự cần thiết (không phải lần đầu load trang)
        if (window.pageLoaded) {
            loadWeekData();
        }
    }

    /**
     * Chuyển tuần
     */
    function changeWeek(direction) {
        currentWeekOffset += direction;
        updateWeekDisplay();
    }

    /**
     * Về tuần hiện tại
     */
    function goToCurrentWeek() {
        currentWeekOffset = 0;
        updateWeekDisplay();
    }

    /**
     * Hiển thị thông báo chuyển tuần
     */
    function showWeekChangeNotification(direction) {
        let message = '';
        if (direction === 0) {
            message = 'Đã chuyển về tuần hiện tại';
        } else if (direction === 1) {
            message = 'Đã chuyển đến tuần sau';
        } else if (direction === -1) {
            message = 'Đã chuyển về tuần trước';
        }

        // Tạo notification popup
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-calendar-check mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    /**
     * Load dữ liệu theo tuần
     */
    function loadWeekData() {
        const weekOffset = currentWeekOffset;
        console.log('Loading data for week offset:', weekOffset);

        // Hiển thị loading
        showLoadingState(true);

        // Gọi API để lấy dữ liệu tuần (đường dẫn từ root)
        const apiUrl = `./includes/get-timetable-by-week.php?week_offset=${weekOffset}`;
        console.log('API URL:', apiUrl);

        fetch(apiUrl)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('API response:', data);
                if (data.success) {
                    console.log('Schedule data:', data.data.schedule);
                    // Cập nhật dữ liệu timetable
                    updateTimetableDisplay(data.data);
                } else {
                    console.error('Error loading week data:', data.message);
                    showNotification('Lỗi khi tải dữ liệu: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                showNotification('Lỗi kết nối mạng: ' + error.message, 'error');
            })
            .finally(() => {
                showLoadingState(false);
            });
    }

    /**
     * Cập nhật hiển thị thời khóa biểu
     */
    function updateTimetableDisplay(data) {
        const timetableContainer = document.querySelector('.timetable-list-section');
        if (!timetableContainer) return;

        // Cập nhật thông tin tuần
        const weekInfo = data.week_info;
        document.getElementById('current-week-number').textContent = weekInfo.week_number;
        document.getElementById('current-week-range').textContent = weekInfo.week_range;

        // Cập nhật URL mà không reload trang
        const url = new URL(window.location);
        url.searchParams.set('week_offset', weekInfo.week_offset);
        window.history.pushState({}, '', url.toString());

        // Cập nhật currentWeekOffset
        const oldOffset = currentWeekOffset;
        currentWeekOffset = weekInfo.week_offset;

        // Cập nhật highlight nút "Hôm nay"
        const homeButton = document.querySelector('button[onclick="goToCurrentWeek()"]');
        if (homeButton) {
            if (currentWeekOffset === 0) {
                homeButton.classList.add('bg-blue-600');
                homeButton.classList.remove('bg-blue-500');
            } else {
                homeButton.classList.add('bg-blue-500');
                homeButton.classList.remove('bg-blue-600');
            }
        }

        // Cập nhật DOM trực tiếp với dữ liệu mới
        updateScheduleDOM(data.schedule);

        // Hiển thị thông báo thành công
        const direction = weekInfo.week_offset === 0 ? 0 : (weekInfo.week_offset > oldOffset ? 1 : -1);
        showWeekChangeNotification(direction);

        // Thông báo debug thành công
        console.log('Timetable updated successfully for week:', weekInfo.week_number + ' (Năm học ' + weekInfo.academic_year + ')');
    }

    /**
     * Cập nhật DOM với dữ liệu schedule mới
     */
    function updateScheduleDOM(schedule) {
        // Thứ tự ngày cố định: Thứ 2 -> Chủ nhật
        const daysOrder = [2, 3, 4, 5, 6, 7, 1];
        const daysNames = {
            1: 'Chủ nhật',
            2: 'Thứ 2',
            3: 'Thứ 3',
            4: 'Thứ 4',
            5: 'Thứ 5',
            6: 'Thứ 6',
            7: 'Thứ 7'
        };

        // Tìm container chứa các ngày
        const daysContainer = document.querySelector('.space-y-5');
        if (!daysContainer) {
            console.error('Cannot find days container with class .space-y-5');
            console.log('Available containers:', document.querySelectorAll('[class*="space-y"]'));
            return;
        }

        console.log('Found days container:', daysContainer);
        console.log('Schedule data to update:', schedule);

        // Xóa nội dung cũ
        daysContainer.innerHTML = '';

        // Tạo lại nội dung cho từng ngày theo thứ tự cố định
        daysOrder.forEach(dayNum => {
            const dayName = daysNames[dayNum];
            const daySubjects = schedule[dayNum] || [];
            console.log(`Day ${dayNum} (${dayName}):`, daySubjects);
            const dayHtml = createDayHTML(dayNum, dayName, daySubjects);
            daysContainer.insertAdjacentHTML('beforeend', dayHtml);
        });

        console.log('DOM updated successfully');
    }

    /**
     * Tạo HTML cho một ngày
     */
    function createDayHTML(dayNum, dayName, subjects) {
        const subjectCount = subjects.length;
        
        // Xác định ngày hiện tại
        const currentDayOfWeek = new Date().getDay() + 1; // 1=Chủ nhật, 2=Thứ 2, etc.
        const isCurrentWeek = currentWeekOffset === 0;
        const isToday = isCurrentWeek && dayNum == currentDayOfWeek;
        
        // CSS classes dựa trên việc có phải hôm nay không
        const headerClass = isToday 
            ? "bg-gradient-to-r from-orange-500 to-red-600 text-white px-4 py-2 ring-4 ring-orange-200"
            : "bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2";
        const containerClass = isToday 
            ? "bg-white rounded-lg shadow-lg border-2 border-orange-400 overflow-hidden transform scale-105"
            : "bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden";

        let subjectsHTML = '';

        if (subjectCount > 0) {
            // Sắp xếp môn học theo thời gian
            subjects.sort((a, b) => a.start_time.localeCompare(b.start_time));

            const colors = [
                'bg-blue-50 border-blue-300 text-blue-800',
                'bg-green-50 border-green-300 text-green-800',
                'bg-purple-50 border-purple-300 text-purple-800',
                'bg-red-50 border-red-300 text-red-800',
                'bg-yellow-50 border-yellow-300 text-yellow-800',
                'bg-indigo-50 border-indigo-300 text-indigo-800',
                'bg-pink-50 border-pink-300 text-pink-800',
                'bg-orange-50 border-orange-300 text-orange-800'
            ];

            subjects.forEach((subject, index) => {
                const colorClass = colors[index % colors.length];
                const startTime = subject.start_time.substring(0, 5);
                const endTime = subject.end_time.substring(0, 5);

                subjectsHTML += `
                    <div class="${colorClass} border-l-4 rounded-lg p-4 hover:shadow-md transition-all duration-200 group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-2">
                                    <h5 class="text-base font-semibold group-hover:scale-105 transition-transform">
                                        ${escapeHtml(subject.subject_name)}
                                    </h5>
                                    <div class="flex items-center bg-white/50 rounded-full px-2 py-1">
                                        <i class="fas fa-clock mr-1 text-xs"></i>
                                        <span class="text-xs font-medium">${startTime} - ${endTime}</span>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center flex-wrap gap-2 text-xs">
                                    ${subject.classroom ? `
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-1 text-red-500"></i>
                                            <span class="font-medium">Phòng:</span>
                                            <span class="ml-1">${escapeHtml(subject.classroom)}</span>
                                        </div>
                                    ` : ''}
                                    ${subject.notes ? `
                                        <div class="flex items-center">
                                            <i class="fas fa-sticky-note mr-1 text-yellow-500"></i>
                                            <span class="font-medium">Ghi chú:</span>
                                            <span class="ml-1">${escapeHtml(subject.notes)}</span>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="editSubject(${subject.id})"
                                    class="bg-white/70 hover:bg-white text-blue-600 px-2 py-1 rounded-lg transition-colors shadow-sm"
                                    title="Sửa môn học">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deleteSubject(${subject.id})"
                                    class="bg-white/70 hover:bg-white text-red-600 px-2 py-1 rounded-lg transition-colors shadow-sm"
                                    title="Xóa môn học">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            subjectsHTML = `
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-calendar-times text-3xl mb-3 text-gray-300"></i>
                    <p class="text-base mb-2">Không có môn học</p>
                    <button onclick="addSubjectToDay('${dayNum}')"
                        class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center mx-auto transition-colors text-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Thêm môn học
                    </button>
                </div>
            `;
        }

        return `
            <div class="${containerClass}">
                <div class="${headerClass}">
                    <h4 class="text-base font-semibold flex items-center">
                        <i class="fas fa-calendar-day mr-2 text-sm"></i>
                        ${dayName}
                        ${isToday ? '<span class="ml-2 bg-white/20 text-xs px-2 py-0.5 rounded-full font-medium">Hôm nay</span>' : ''}
                    </h4>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        ${subjectsHTML}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Escape HTML để tránh XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    }

    /**
     * Hiển thị trạng thái loading
     */
    function showLoadingState(isLoading) {
        const buttons = document.querySelectorAll('button[onclick*="changeWeek"], button[onclick*="goToCurrentWeek"]');
        buttons.forEach(button => {
            button.disabled = isLoading;
            if (isLoading) {
                button.style.opacity = '0.5';
                button.style.cursor = 'not-allowed';
            } else {
                button.style.opacity = '1';
                button.style.cursor = 'pointer';
            }
        });

        // Thêm loading indicator trên schedule
        const daysContainer = document.querySelector('.space-y-5');
        if (daysContainer) {
            if (isLoading) {
                // Thêm overlay loading
                const loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'loading-overlay';
                loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
                loadingOverlay.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-2"></i>
                        <p class="text-gray-600">Đang tải lịch học...</p>
                    </div>
                `;

                // Đảm bảo container có position relative
                daysContainer.style.position = 'relative';
                daysContainer.appendChild(loadingOverlay);
            } else {
                // Xóa loading overlay
                const loadingOverlay = document.getElementById('loading-overlay');
                if (loadingOverlay) {
                    loadingOverlay.remove();
                }
            }
        }
    }

    /**
     * Hiển thị thông báo
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }



    // Khởi tạo khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        // Đánh dấu trang đã load để tránh vòng lặp
        setTimeout(() => {
            window.pageLoaded = true;
        }, 100);

        // Chỉ cập nhật display nếu có sự khác biệt với localStorage
        const savedOffset = localStorage.getItem('timetable_week_offset');
        if (savedOffset !== null && parseInt(savedOffset) !== currentWeekOffset) {
            // Nếu có sự khác biệt, cập nhật currentWeekOffset và reload trang
            const url = new URL(window.location);
            url.searchParams.set('week_offset', savedOffset);
            window.location.href = url.toString();
            return;
        }

        console.log('Timetable navigation initialized');
        console.log('Current week offset:', currentWeekOffset);
    });
</script>