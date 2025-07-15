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

        <!-- JavaScript chung cho toàn bộ website -->
        <!-- <script src="assets/js/script.js"></script> -->

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
            // Xử lý responsive breakpoints
            function handleResize() {
                const mobileMenu = document.getElementById('mobile-menu');
                if (window.innerWidth >= 768 && !mobileMenu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }

            window.addEventListener('resize', handleResize);

            // Hàm hiển thị modal
            function showModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }

            // Hàm đóng modal
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }
            }

            // Hàm scroll to top
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
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