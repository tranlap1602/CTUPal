        </main>
        </div>

        <!-- Footer -->
        <footer class="mt-auto bg-gradient-to-r <?php echo $is_admin ? 'from-purple-600 to-purple-700' : 'from-blue-500 to-blue-600'; ?> w-full">
            <div class="px-4 sm:px-6 lg:px-8 py-2">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                            <i class="<?php echo $logo_icon; ?> text-white text-xl"></i>
                        </div>
                        <span class="text-white font-medium"><?php echo $logo_text; ?></span>
                    </div>

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

        <!-- Back to top button -->
        <button onclick="scrollToTop()"
            class="fixed bottom-6 right-6 <?php echo $is_admin ? 'bg-purple-500 hover:bg-purple-600' : 'bg-blue-500 hover:bg-blue-600'; ?> text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible"
            id="back-to-top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- Toast.js -->
        <script src="<?php echo $base_path; ?>assets/js/toast.js"></script>

        <!-- JavaScript -->
        <script>
            // Toggle user dropdown
            function toggleUserDropdown() {
                const dropdown = document.getElementById('user-dropdown');
                const arrow = document.getElementById('dropdown-arrow');

                dropdown.classList.toggle('hidden');
                dropdown.classList.toggle('show');

                if (dropdown.classList.contains('show')) {
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    arrow.style.transform = 'rotate(0deg)';
                }
            }

            // Toggle mobile menu
            function toggleMobileMenu() {
                const menu = document.getElementById('mobile-menu');
                const icon = document.getElementById('mobile-menu-icon');

                menu.classList.toggle('hidden');

                if (menu.classList.contains('hidden')) {
                    icon.className = 'fas fa-bars text-lg';
                } else {
                    icon.className = 'fas fa-times text-lg';
                }
            }

            // Back to top functionality
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Show/hide back to top button
            window.addEventListener('scroll', function() {
                const backToTop = document.getElementById('back-to-top');
                if (window.pageYOffset > 300) {
                    backToTop.classList.remove('opacity-0', 'invisible');
                    backToTop.classList.add('opacity-100', 'visible');
                } else {
                    backToTop.classList.add('opacity-0', 'invisible');
                    backToTop.classList.remove('opacity-100', 'visible');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('user-dropdown');
                const dropdownBtn = document.getElementById('user-dropdown-btn');

                if (!dropdownBtn.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                    dropdown.classList.remove('show');
                    document.getElementById('dropdown-arrow').style.transform = 'rotate(0deg)';
                }
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuBtn = document.getElementById('mobile-menu-btn');

                if (!mobileMenuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    document.getElementById('mobile-menu-icon').className = 'fas fa-bars text-lg';
                }
            });

            // Handle window resize
            function handleResize() {
                const mobileMenu = document.getElementById('mobile-menu');
                if (window.innerWidth >= 768 && !mobileMenu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }

            window.addEventListener('resize', handleResize);
        </script>
        </body>

        </html>