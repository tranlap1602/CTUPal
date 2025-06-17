        </main>
        <!-- Kết thúc main content wrapper -->

        <!-- Footer chung cho toàn bộ website -->
        <footer class="mt-12 bg-gradient-to-r from-primary-500 to-primary-600">
            <div class="py-6">
                <!-- Footer content chính -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Cột 1: Thông tin về StudentManager -->
                    <div>
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                            <h3 class="text-base font-bold text-white">StudentManager</h3>
                        </div>
                        <p class="text-primary-100 text-sm">
                            Hệ thống quản lý sinh viên hiện đại
                        </p>
                    </div>

                    <!-- Cột 2: Links nhanh - Cột 1 -->
                    <div>
                        <h3 class="text-base font-semibold text-white mb-3">Liên kết nhanh</h3>
                        <ul class="space-y-1">
                            <?php if ($is_logged_in): ?>
                                <li><a href="index.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-home w-4 mr-2"></i>Trang chủ</a></li>
                                <li><a href="timetable.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-calendar-alt w-4 mr-2"></i>Thời khóa biểu</a></li>
                                <li><a href="documents.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-file-alt w-4 mr-2"></i>Tài liệu</a></li>
                            <?php else: ?>
                                <li><a href="login.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-sign-in-alt w-4 mr-2"></i>Đăng nhập</a></li>
                                <li><a href="register.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-user-plus w-4 mr-2"></i>Đăng ký</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Cột 3: Links nhanh - Cột 2 -->
                    <div>
                        <h3 class="text-base font-semibold text-white mb-3">Chức năng</h3>
                        <ul class="space-y-1">
                            <?php if ($is_logged_in): ?>
                                <li><a href="expenses.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-wallet w-4 mr-2"></i>Chi tiêu</a></li>
                                <li><a href="notes.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-sticky-note w-4 mr-2"></i>Ghi chú</a></li>
                                <li><a href="profile.php" class="text-primary-100 hover:text-white transition-colors text-sm flex items-center">
                                        <i class="fas fa-user w-4 mr-2"></i>Thông tin cá nhân</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Footer bottom -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 pt-4 border-t border-primary-400/30 text-center">
                    <p class="text-primary-100 text-sm">
                        <i class="fas fa-copyright mr-1"></i>
                        2024 StudentManager. Được phát triển bởi sinh viên Khoa Công nghệ thông tin.
                    </p>
                </div>
            </div>
        </footer>

        </div>
        <!-- Kết thúc container chính -->

        <!-- Modal Hướng dẫn sử dụng -->
        <div id="help-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl mx-4 max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-question-circle mr-2 text-blue-500"></i>
                            Hướng dẫn sử dụng
                        </h3>
                        <button onclick="closeModal('help-modal')" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">📅 Thời khóa biểu</h4>
                            <p>Quản lý lịch học hàng tuần, thêm môn học mới, import từ file Excel.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">📄 Tài liệu</h4>
                            <p>Upload và lưu trữ tài liệu học tập, hỗ trợ nhiều định dạng file.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">💰 Chi tiêu</h4>
                            <p>Theo dõi các khoản chi tiêu cá nhân, phân loại theo danh mục.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">📝 Ghi chú</h4>
                            <p>Tạo và quản lý các ghi chú học tập, to-do list cá nhân.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Liên hệ -->
        <div id="contact-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-md mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            Liên hệ hỗ trợ
                        </h3>
                        <button onclick="closeModal('contact-modal')" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-envelope w-5 mr-3 text-blue-500"></i>
                            <span>support@studentmanager.edu.vn</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone w-5 mr-3 text-green-500"></i>
                            <span>0123.456.789</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt w-5 mr-3 text-red-500"></i>
                            <span>Khoa CNTT, Đại học ABC</span>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg mt-4">
                            <p class="text-blue-800 text-xs">
                                <i class="fas fa-info-circle mr-1"></i>
                                Thời gian hỗ trợ: Thứ 2 - Thứ 6, 8:00 - 17:00
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript cho các chức năng tương tác -->
        <script>
            // Toggle user dropdown menu
            function toggleUserDropdown() {
                const dropdown = document.getElementById('user-dropdown');
                const arrow = document.getElementById('dropdown-arrow');

                if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    setTimeout(() => dropdown.classList.add('show'), 10);
                    arrow.classList.add('rotate-180');
                } else {
                    dropdown.classList.remove('show');
                    arrow.classList.remove('rotate-180');
                    setTimeout(() => dropdown.classList.add('hidden'), 200);
                }
            }

            // Toggle mobile menu
            function toggleMobileMenu() {
                const menu = document.getElementById('mobile-menu');
                const icon = document.getElementById('mobile-menu-icon');

                if (menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    menu.classList.add('hidden');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            // Đóng dropdown khi click bên ngoài
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('user-dropdown');
                const dropdownBtn = document.getElementById('user-dropdown-btn');
                const arrow = document.getElementById('dropdown-arrow');

                if (!dropdown.contains(event.target) && !dropdownBtn.contains(event.target)) {
                    dropdown.classList.remove('show');
                    arrow.classList.remove('rotate-180');
                    setTimeout(() => dropdown.classList.add('hidden'), 200);
                }
            });



            // Hiệu ứng loading cho các nút
            function addLoadingState(button) {
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner loading-spinner mr-2"></i>Đang xử lý...';
                button.disabled = true;

                return function() {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                };
            }

            // Notification system (có thể mở rộng sau)
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 animate-slide-down ${
                    type === 'success' ? 'bg-green-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' :
                    type === 'warning' ? 'bg-yellow-500 text-white' :
                    'bg-blue-500 text-white'
                }`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${
                            type === 'success' ? 'fa-check-circle' :
                            type === 'error' ? 'fa-exclamation-circle' :
                            type === 'warning' ? 'fa-exclamation-triangle' :
                            'fa-info-circle'
                        } mr-2"></i>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Tự động xóa sau 5 giây
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }

            // Xử lý responsive breakpoints
            function handleResize() {
                const mobileMenu = document.getElementById('mobile-menu');
                if (window.innerWidth >= 768 && !mobileMenu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }

            window.addEventListener('resize', handleResize);

            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                // Ctrl + / để focus vào tìm kiếm (nếu có)
                if (event.ctrlKey && event.key === '/') {
                    event.preventDefault();
                    const searchInput = document.querySelector('input[type="search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }

                // Escape để đóng dropdown
                if (event.key === 'Escape') {
                    const dropdown = document.getElementById('user-dropdown');
                    if (!dropdown.classList.contains('hidden')) {
                        toggleUserDropdown();
                    }
                }
            });

            // Smooth scroll cho các anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            console.log('🎓 StudentManager loaded successfully!');
        </script>

        <!-- Back to top button -->
        <button onclick="scrollToTop()"
            class="fixed bottom-6 right-6 bg-blue-500 hover:bg-blue-600 text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible"
            id="back-to-top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <script>
            // Hiển thị/ẩn nút back to top
            window.addEventListener('scroll', function() {
                const backToTop = document.getElementById('back-to-top');
                if (window.pageYOffset > 300) {
                    backToTop.classList.remove('opacity-0', 'invisible');
                } else {
                    backToTop.classList.add('opacity-0', 'invisible');
                }
            });
        </script>

        </body>

        </html>

        <?php
        /**
         * File: includes/footer.php
         * Mục đích: Footer chung cho toàn bộ website StudentManager
         * Tác giả: [Tên sinh viên] 
         * Ngày tạo: [Ngày]
         * Mô tả: Chứa footer, scripts, modals để tái sử dụng
         * Sử dụng: include 'includes/footer.php';
         */
        ?>