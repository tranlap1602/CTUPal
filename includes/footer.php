        </main>
        </div>

        <!-- Footer -->
        <footer class="mt-auto bg-gradient-to-r <?php echo $is_admin ? 'from-blue-500 to-blue-600' : 'from-blue-500 to-blue-600'; ?> w-full">
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

        <button onclick="scrollToTop()"
            class="fixed bottom-6 right-6 <?php echo $is_admin ? 'bg-purple-500 hover:bg-purple-600' : 'bg-blue-500 hover:bg-blue-600'; ?> text-white w-12 h-12 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible"
            id="back-to-top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- Toast.js -->
        <script src="<?php echo $base_path; ?>assets/js/toast.js"></script>

        <div id="confirm-modal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-500 to-red-600">
                        <h3 id="confirm-title" class="text-lg font-semibold text-white">Xác nhận</h3>
                    </div>
                    <div class="p-6">
                        <p id="confirm-message" class="text-gray-700"></p>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-200 flex items-center justify-end bg-gray-50 gap-3">
                        <button type="button" id="confirm-cancel"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-all duration-200 cursor-pointer">Hủy</button>
                        <button type="button" id="confirm-ok"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-500 transition-all duration-200 cursor-pointer">Đồng ý</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showConfirmModal(options) {
                const {
                    title = 'Xác nhận', message = 'Bạn có chắc chắn?', okText = 'Đồng ý', cancelText = 'Hủy'
                } = options || {};
                const modal = document.getElementById('confirm-modal');
                const titleEl = document.getElementById('confirm-title');
                const msgEl = document.getElementById('confirm-message');
                const okBtn = document.getElementById('confirm-ok');
                const cancelBtn = document.getElementById('confirm-cancel');

                titleEl.textContent = title;
                msgEl.textContent = message;
                okBtn.textContent = okText;
                cancelBtn.textContent = cancelText;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                return new Promise((resolve) => {
                    function cleanup(result) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                        okBtn.removeEventListener('click', onOk);
                        cancelBtn.removeEventListener('click', onCancel);
                        modalBackdrop.removeEventListener('click', onBackdrop);
                        resolve(result);
                    }

                    function onOk() {
                        cleanup(true);
                    }

                    function onCancel() {
                        cleanup(false);
                    }

                    function onBackdrop(e) {
                        if (e.target === modal.firstElementChild) cleanup(false);
                    }
                    okBtn.addEventListener('click', onOk);
                    cancelBtn.addEventListener('click', onCancel);

                    const modalBackdrop = modal;
                    modalBackdrop.addEventListener('click', onBackdrop);
                });
            }

            (function autoBindConfirmForms() {
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    const message = form?.dataset?.confirm;
                    if (!message) return;
                    e.preventDefault();
                    showConfirmModal({
                        message,
                        title: 'Xác nhận',
                        okText: 'Đồng ý',
                        cancelText: 'Hủy'
                    }).then((ok) => {
                        if (ok) form.submit();
                    });
                });
            })();

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

            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

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

            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('user-dropdown');
                const dropdownBtn = document.getElementById('user-dropdown-btn');

                if (!dropdownBtn.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                    dropdown.classList.remove('show');
                    document.getElementById('dropdown-arrow').style.transform = 'rotate(0deg)';
                }
            });

            document.addEventListener('click', function(event) {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuBtn = document.getElementById('mobile-menu-btn');

                if (!mobileMenuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    document.getElementById('mobile-menu-icon').className = 'fas fa-bars text-lg';
                }
            });

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