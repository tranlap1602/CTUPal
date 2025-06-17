<?php

/**
 * File: register.php
 * Mục đích: Trang đăng ký tài khoản sinh viên CTU
 * Tác giả: [Tên sinh viên]
 * Ngày tạo: [Ngày]
 * Mô tả: Form đăng ký đơn giản, tự động tách MSSV và tên từ email CTU
 */

// Thiết lập biến cho header
$page_title = 'Đăng ký tài khoản';
$current_page = 'register.php';

// Bắt đầu session
session_start();
require_once 'config/db.php';

// Nếu user đã đăng nhập, chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Khởi tạo biến thông báo
$success = '';
$error = '';
$form_data = [];

/**
 * Hàm tách MSSV và tên từ email CTU
 * Format: huyb2204945@student.ctu.edu.vn → name = Huy, mssv = b2204945
 */
function extractStudentInfo($email)
{
    // Kiểm tra email có đúng định dạng CTU không: [tên]b[7 số]@student.ctu.edu.vn
    if (!preg_match('/^([a-zA-Z]+)(b[0-9]{7})@student\.ctu\.edu\.vn$/', $email, $matches)) {
        return false;
    }

    $name_part = $matches[1]; // huy
    $mssv_part = $matches[2]; // b2204945

    // Capitalize tên
    $name = ucfirst(strtolower($name_part));

    return [
        'name' => $name,
        'mssv' => $mssv_part,
        'username' => $email // Sử dụng email làm username
    ];
}

// Xử lý khi user submit form đăng ký
if ($_POST) {
    // Lấy và làm sạch dữ liệu từ form
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $agree_terms = isset($_POST['agree_terms']);

    // Lưu dữ liệu form để hiển thị lại khi có lỗi
    $form_data = [
        'email' => $email,
        'phone' => $phone,
        'agree_terms' => $agree_terms
    ];

    // Validation dữ liệu
    $errors = [];

    // Kiểm tra email
    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ!';
    } elseif (!preg_match('/^[a-zA-Z]+b[0-9]{7}@student\.ctu\.edu\.vn$/', $email)) {
        $errors[] = 'Email phải có định dạng sinh viên CTU: [tên]b[7 số]@student.ctu.edu.vn!';
    }

    // Tách thông tin sinh viên từ email
    $student_info = false;
    if (empty($errors)) {
        $student_info = extractStudentInfo($email);
        if (!$student_info) {
            $errors[] = 'Không thể tách thông tin từ email. Vui lòng kiểm tra định dạng!';
        }
    }

    // Kiểm tra mật khẩu
    if (empty($password)) {
        $errors[] = 'Vui lòng nhập mật khẩu!';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Mật khẩu phải chứa cả chữ cái và số!';
    }

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        $errors[] = 'Mật khẩu xác nhận không khớp!';
    }

    // Kiểm tra số điện thoại (nếu có nhập)
    if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ (10-11 chữ số)!';
    }

    // Kiểm tra đồng ý điều khoản
    if (!$agree_terms) {
        $errors[] = 'Bạn phải đồng ý với điều khoản sử dụng!';
    }

    // Nếu không có lỗi validation, kiểm tra trùng lặp và tạo tài khoản
    if (empty($errors) && $student_info) {
        try {
            // Kiểm tra email đã tồn tại
            $check_email = fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            if ($check_email) {
                $errors[] = 'Email này đã được đăng ký!';
            }

            // Kiểm tra MSSV đã tồn tại
            $check_mssv = fetchOne("SELECT id FROM users WHERE mssv = ?", [$student_info['mssv']]);
            if ($check_mssv) {
                $errors[] = 'MSSV này đã được đăng ký!';
            }

            // Kiểm tra username đã tồn tại (username = email)
            $check_username = fetchOne("SELECT id FROM users WHERE username = ?", [$student_info['username']]);
            if ($check_username) {
                $errors[] = 'Username này đã được sử dụng!';
            }

            // Nếu không có lỗi trùng lặp, tạo tài khoản
            if (empty($errors)) {
                // Mã hóa mật khẩu
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user mới vào database
                $insert_query = "INSERT INTO users (name, email, mssv, username, password, phone) 
                                VALUES (?, ?, ?, ?, ?, ?)";

                $result = executeQuery($insert_query, [
                    $student_info['name'],
                    $email,
                    $student_info['mssv'],
                    $student_info['username'],
                    $hashed_password,
                    $phone ?: null // Nếu phone rỗng thì lưu NULL
                ]);

                if ($result) {
                    // Đăng ký thành công
                    $success = "Đăng ký thành công! Tài khoản của bạn:<br>
                               <strong>Tên:</strong> {$student_info['name']}<br>
                               <strong>MSSV:</strong> {$student_info['mssv']}<br>
                               <strong>Email:</strong> {$email}<br>
                               Bạn có thể đăng nhập ngay bây giờ.";

                    // Log hoạt động
                    $new_user_id = $conn->insert_id;
                    logActivity($new_user_id, 'Đăng ký tài khoản thành công - MSSV: ' . $student_info['mssv']);

                    // Reset form data
                    $form_data = [];
                } else {
                    $errors[] = 'Có lỗi xảy ra khi tạo tài khoản. Vui lòng thử lại!';
                }
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = 'Có lỗi hệ thống. Vui lòng thử lại sau!';
        }
    }

    // Nếu có lỗi, hiển thị thông báo lỗi đầu tiên
    if (!empty($errors)) {
        $error = $errors[0];
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Main register content -->
<div class="max-w-md mx-auto">

    <!-- Form container -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-gray-200">

        <!-- Form header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-2xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Đăng ký tài khoản</h2>
            <p class="text-gray-600">Dành cho sinh viên Đại học Cần Thơ</p>
        </div>

        <!-- Hiển thị thông báo thành công -->
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 animate-bounce-in">
                <div class="flex items-start">
                    <i class="fas fa-check-circle mr-3 text-lg mt-1"></i>
                    <div>
                        <div><?php echo $success; ?></div>
                        <div class="mt-3">
                            <a href="login.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 inline-flex items-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thông báo lỗi -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 animate-bounce-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hướng dẫn email format -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Định dạng email sinh viên CTU
            </h4>
            <div class="text-sm text-blue-700 space-y-1">
                <p><strong>Ví dụ:</strong> huyb2204945@student.ctu.edu.vn</p>
                <p class="text-xs">→ Tên: <strong>Huy</strong>, MSSV: <strong>b2204945</strong></p>
                <p class="text-xs mt-2"><i class="fas fa-lightbulb mr-1"></i><strong>Quy tắc:</strong> [tên]b[7 số]@student.ctu.edu.vn</p>
            </div>
        </div>

        <!-- Form đăng ký -->
        <form method="POST" class="space-y-6" id="register-form">

            <!-- Email CTU -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-blue-500"></i>Email sinh viên CTU *
                </label>
                <input type="email"
                    id="email"
                    name="email"
                    required
                    value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                    placeholder="huyb2204945@student.ctu.edu.vn">
                <div class="mt-1 text-xs text-gray-500">
                    Email phải có định dạng: [tên]b[7 số]@student.ctu.edu.vn
                </div>
            </div>

            <!-- Mật khẩu -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-500"></i>Mật khẩu *
                </label>
                <div class="relative">
                    <input type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 pr-12"
                        placeholder="Mật khẩu (ít nhất 6 ký tự)">
                    <button type="button"
                        onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    Mật khẩu phải chứa ít nhất 6 ký tự, bao gồm chữ cái và số
                </div>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-500"></i>Xác nhận mật khẩu *
                </label>
                <div class="relative">
                    <input type="password"
                        id="confirm_password"
                        name="confirm_password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 pr-12"
                        placeholder="Nhập lại mật khẩu">
                    <button type="button"
                        onclick="togglePassword('confirm_password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="confirm_password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Số điện thoại -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone mr-2 text-blue-500"></i>Số điện thoại
                </label>
                <input type="tel"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                    placeholder="0123456789 (tùy chọn)">
                <div class="mt-1 text-xs text-gray-500">
                    Số điện thoại 10-11 chữ số (không bắt buộc)
                </div>
            </div>

            <!-- Checkbox đồng ý điều khoản -->
            <div class="flex items-start">
                <input type="checkbox"
                    id="agree_terms"
                    name="agree_terms"
                    required
                    <?php echo ($form_data['agree_terms'] ?? false) ? 'checked' : ''; ?>
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded mt-1">
                <label for="agree_terms" class="ml-2 block text-sm text-gray-700">
                    Tôi đồng ý với
                    <a href="#" onclick="showTermsModal()" class="text-green-600 hover:text-green-800 underline">
                        Điều khoản sử dụng
                    </a>
                    và
                    <a href="#" onclick="showPrivacyModal()" class="text-green-600 hover:text-green-800 underline">
                        Chính sách bảo mật
                    </a> *
                </label>
            </div>

            <!-- Nút đăng ký -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-emerald-700 focus:ring-4 focus:ring-green-200 transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center space-x-2">
                <i class="fas fa-user-plus"></i>
                <span>Đăng ký tài khoản</span>
            </button>
        </form>

        <!-- Link đăng nhập -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Đã có tài khoản?
                <a href="login.php" class="text-green-600 hover:text-green-800 font-medium transition-colors duration-200">
                    Đăng nhập ngay
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Modal Điều khoản sử dụng -->
<div id="terms-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl mx-4 max-h-96 overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Điều khoản sử dụng</h3>
                <button onclick="closeModal('terms-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4 text-sm text-gray-600">
                <p><strong>1. Đối tượng sử dụng:</strong> Hệ thống chỉ dành cho sinh viên Đại học Cần Thơ có email @student.ctu.edu.vn hợp lệ.</p>
                <p><strong>2. Tài khoản:</strong> Bạn chịu trách nhiệm bảo mật thông tin đăng nhập và mọi hoạt động trên tài khoản.</p>
                <p><strong>3. Nội dung:</strong> Bạn chịu trách nhiệm về nội dung bạn tải lên hệ thống.</p>
                <p><strong>4. Bảo mật:</strong> Chúng tôi cam kết bảo vệ thông tin cá nhân theo chính sách bảo mật.</p>
                <p><strong>5. Thay đổi:</strong> Điều khoản có thể được cập nhật mà không cần thông báo trước.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chính sách bảo mật -->
<div id="privacy-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl mx-4 max-h-96 overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Chính sách bảo mật</h3>
                <button onclick="closeModal('privacy-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4 text-sm text-gray-600">
                <p><strong>Thu thập thông tin:</strong> Chúng tôi chỉ thu thập thông tin từ email CTU chính thức của bạn.</p>
                <p><strong>Sử dụng thông tin:</strong> Thông tin được sử dụng để cải thiện trải nghiệm học tập.</p>
                <p><strong>Bảo vệ dữ liệu:</strong> Dữ liệu được mã hóa và lưu trữ an toàn theo tiêu chuẩn bảo mật.</p>
                <p><strong>Chia sẻ thông tin:</strong> Chúng tôi không chia sẻ thông tin với bên thứ ba.</p>
                <p><strong>Quyền của bạn:</strong> Bạn có quyền truy cập, sửa đổi thông tin trong trang Profile.</p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript cho register page -->
<script>
    /**
     * Toggle hiện/ẩn mật khẩu
     */
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    /**
     * Hiển thị modal
     */
    function showTermsModal() {
        document.getElementById('terms-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function showPrivacyModal() {
        document.getElementById('privacy-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Đóng modal
     */
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    /**
     * Preview thông tin được tách từ email
     */
    function previewStudentInfo() {
        const emailInput = document.getElementById('email');
        const email = emailInput.value.trim();

        // Regex để tách thông tin: [tên]b[7 số]@student.ctu.edu.vn
        const match = email.match(/^([a-zA-Z]+)(b[0-9]{7})@student\.ctu\.edu\.vn$/);

        if (match) {
            const name = match[1].charAt(0).toUpperCase() + match[1].slice(1).toLowerCase();
            const mssv = match[2];

            // Hiển thị preview (có thể thêm một div để show preview)
            console.log(`Tên: ${name}, MSSV: ${mssv}`);
        }
    }

    /**
     * Validation thời gian thực
     */
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('register-form');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        // Kiểm tra định dạng email CTU
        email.addEventListener('input', function() {
            const emailValue = this.value.trim();
            if (emailValue && !emailValue.match(/^[a-zA-Z]+b[0-9]{7}@student\.ctu\.edu\.vn$/)) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
                if (emailValue) {
                    this.classList.add('border-green-500');
                    previewStudentInfo();
                }
            }
        });

        // Kiểm tra mật khẩu khớp
        function checkPasswordMatch() {
            if (password.value && confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Mật khẩu không khớp');
                    confirmPassword.classList.add('border-red-500');
                    confirmPassword.classList.remove('border-green-500');
                } else {
                    confirmPassword.setCustomValidity('');
                    confirmPassword.classList.remove('border-red-500');
                    confirmPassword.classList.add('border-green-500');
                }
            }
        }

        password.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);

        // Xử lý submit form
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;

            // Hiện loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang đăng ký...';
            submitBtn.disabled = true;

            // Reset nếu có lỗi validation
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });

        // Auto-hide success/error messages
        <?php if ($success || $error): ?>
            setTimeout(() => {
                const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 8000); // 8 giây cho success message vì có nhiều thông tin
        <?php endif; ?>
    });

    // Đóng modal với ESC hoặc click outside
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('terms-modal');
            closeModal('privacy-modal');
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.id === 'terms-modal') closeModal('terms-modal');
        if (e.target.id === 'privacy-modal') closeModal('privacy-modal');
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>