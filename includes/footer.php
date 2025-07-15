        </main>
        <!-- Kết thúc main content wrapper -->

        </div>
        <!-- Kết thúc container chính -->

        <!-- Footer chung cho toàn bộ website -->
        <footer class="mt-auto bg-blue-600 w-full">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <!-- Bên trái: Tên hệ thống -->
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                        <span class="text-white font-medium">StudentManager - Hệ thống quản lý sinh viên</span>
                    </div>

                    <!-- Bên phải: Social links -->
                    <div class="flex items-center space-x-3">
                        <a href="#" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-youtube text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-linkedin-in text-white text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

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
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
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