<?php
$page_title = 'Quản lý tài khoản';
$current_page = 'users.php';

session_start();
require_once '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$message = '';
$error = '';

// Xử lý các action
if ($_POST) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_user':
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $mssv = sanitizeInput($_POST['mssv']);
            $phone = sanitizeInput($_POST['phone']);
            $password = $_POST['password'];
            $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;

            // Validate
            if (empty($name) || empty($email) || empty($mssv) || empty($password)) {
                header('Location: users.php?message=' . urlencode('Vui lòng điền đầy đủ thông tin bắt buộc!') . '&type=error');
                exit();
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header('Location: users.php?message=' . urlencode('Email không hợp lệ!') . '&type=error');
                exit();
            } elseif (!preg_match('/@student\.ctu\.edu\.vn$/', $email)) {
                header('Location: users.php?message=' . urlencode('Chỉ chấp nhận email sinh viên CTU!') . '&type=error');
                exit();
            } else {
                try {
                    // Kiểm tra email và mssv đã tồn tại
                    $existingUser = fetchOne("SELECT id FROM users WHERE email = ? OR mssv = ?", [$email, $mssv]);
                    if ($existingUser) {
                        header('Location: users.php?message=' . urlencode('Email hoặc MSSV đã tồn tại!') . '&type=error');
                        exit();
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        insertAndGetId(
                            "INSERT INTO users (name, email, mssv, phone, password, birthday, role) VALUES (?, ?, ?, ?, ?, ?, 'user')",
                            [$name, $email, $mssv, $phone, $hashedPassword, $birthday]
                        );
                        header('Location: users.php?message=' . urlencode('Thêm tài khoản thành công!') . '&type=success');
                        exit();
                    }
                } catch (Exception $e) {
                    header('Location: users.php?message=' . urlencode('Có lỗi xảy ra: ' . $e->getMessage()) . '&type=error');
                    exit();
                }
            }
            break;

        case 'update_user':
            $user_id = $_POST['user_id'];
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $mssv = sanitizeInput($_POST['mssv']);
            $phone = sanitizeInput($_POST['phone']);
            $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $password = $_POST['password'] ?? '';

            try {
                // kiểm tra email CTU
                if (!preg_match('/@student\.ctu\.edu\.vn$/', $email)) {
                    header('Location: users.php?message=' . urlencode('Chỉ chấp nhận email sinh viên CTU!') . '&type=error');
                    exit();
                }

                // Kiểm tra email và mssv đã tồn tại (trừ user hiện tại)
                $existingUser = fetchOne("SELECT id FROM users WHERE (email = ? OR mssv = ?) AND id != ?", [$email, $mssv, $user_id]);
                if ($existingUser) {
                    header('Location: users.php?message=' . urlencode('Email hoặc MSSV đã tồn tại!') . '&type=error');
                    exit();
                } else {
                    // Nếu có mật khẩu mới thì cập nhật, không thì giữ nguyên
                    if (!empty($password)) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        executeQuery(
                            "UPDATE users SET name = ?, email = ?, mssv = ?, phone = ?, birthday = ?, is_active = ?, password = ? WHERE id = ?",
                            [$name, $email, $mssv, $phone, $birthday, $is_active, $hashedPassword, $user_id]
                        );
                    } else {
                        executeQuery(
                            "UPDATE users SET name = ?, email = ?, mssv = ?, phone = ?, birthday = ?, is_active = ? WHERE id = ?",
                            [$name, $email, $mssv, $phone, $birthday, $is_active, $user_id]
                        );
                    }
                    header('Location: users.php?message=' . urlencode('Cập nhật tài khoản thành công!') . '&type=success');
                    exit();
                }
            } catch (Exception $e) {
                header('Location: users.php?message=' . urlencode('Có lỗi xảy ra: ' . $e->getMessage()) . '&type=error');
                exit();
            }
            break;

        case 'delete_user':
            $user_id = $_POST['user_id'];
            try {
                executeQuery("DELETE FROM users WHERE id = ? AND role != 'admin'", [$user_id]);
                header('Location: users.php?message=' . urlencode('Xóa tài khoản thành công!') . '&type=success');
                exit();
            } catch (Exception $e) {
                header('Location: users.php?message=' . urlencode('Có lỗi xảy ra: ' . $e->getMessage()) . '&type=error');
                exit();
            }
            break;

        case 'toggle_status':
            $user_id = $_POST['user_id'];
            $current_status = $_POST['current_status'];
            $new_status = $current_status ? 0 : 1;

            try {
                executeQuery("UPDATE users SET is_active = ? WHERE id = ? AND role != 'admin'", [$new_status, $user_id]);
                $message_text = $new_status ? 'Mở khóa tài khoản thành công!' : 'Khóa tài khoản thành công!';
                header('Location: users.php?message=' . urlencode($message_text) . '&type=success');
                exit();
            } catch (Exception $e) {
                header('Location: users.php?message=' . urlencode('Có lỗi xảy ra: ' . $e->getMessage()) . '&type=error');
                exit();
            }
            break;
    }
}

// Lấy danh sách users
$search = $_GET['search'] ?? '';

$whereClause = "WHERE role = 'user'";
$params = [];

if (!empty($search)) {
    $whereClause .= " AND (name LIKE ? OR email LIKE ? OR mssv LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$users = fetchAll(
    "SELECT * FROM users $whereClause ORDER BY created_at DESC",
    $params
);

include '../includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-8">
    <!-- Header trang -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Quản lý tài khoản sinh viên</h1>
        </div>
        <button onclick="openAddModal()"
            class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-lg cursor-pointer">
            <i class="fas fa-plus mr-2"></i>Thêm người dùng
        </button>
    </div>

    <!-- Bảng danh sách -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gradient-to-br from-blue-200 to-blue-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold">Danh sách sinh viên</h2>
                </div>
                <!-- Thanh tìm kiếm -->
                <div class="flex items-center">
                    <form method="GET" class="flex w-full sm:w-auto">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Tìm kiếm theo tên, email, MSSV..."
                            class="flex-1 sm:w-64 px-3 sm:px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                        <button type="submit" class="bg-blue-500 text-white px-3 sm:px-4 py-2 rounded-r-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">MSSV</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">SĐT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Ngày sinh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Ngày tạo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap border-r border-gray-200">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['mssv']); ?></div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap border-r border-gray-200">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                <div class="text-sm text-gray-900"><?php echo $user['birthday'] ? date('d/m/Y', strtotime($user['birthday'])) : '-'; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                <?php if ($user['is_active']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hoạt động</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                <div class="text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border-r border-gray-200 cursor-pointer">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active']; ?>)"
                                    class="text-yellow-600 hover:text-yellow-900 mr-3 transition-colors cursor-pointer">
                                    <i class="fas fa-<?php echo $user['is_active'] ? 'lock' : 'unlock'; ?>"></i>
                                </button>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)"
                                    class="text-red-600 hover:text-red-900 transition-colors cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


    </div>
</div>

<!-- Modal thêm và sửa tài khoản -->
<div id="userModal" class="fixed inset-0 bg-black/10 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
                <h3 class="text-lg font-semibold text-white" id="modalTitle">Thêm tài khoản mới</h3>
            </div>
            <form method="POST" class="p-6" onsubmit="return validateForm(this)">
                <input type="hidden" name="action" id="modalAction" value="add_user">
                <input type="hidden" name="user_id" id="modal_user_id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên</label>
                        <input type="text" name="name" id="modal_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="modal_email" required autocomplete="off"
                            pattern="[a-zA-Z0-9._%+-]+@student\.ctu\.edu\.vn"
                            title="Chỉ chấp nhận email sinh viên CTU (@student.ctu.edu.vn)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">MSSV</label>
                        <input type="text" name="mssv" id="modal_mssv" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                        <input type="text" name="phone" id="modal_phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div id="passwordField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu</label>
                        <input type="password" name="password" id="modal_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày sinh</label>
                        <input type="date" name="birthday" id="modal_birthday" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200">
                    </div>

                    <div id="statusField" class="hidden">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="modal_is_active" class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Tài khoản hoạt động</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeUserModal()"
                        class="bg-red-500 text-white px-4 py-2 border rounded-lg font-semibold hover:bg-red-600 cursor-pointer transition-all duration-200">
                        Đóng
                    </button>
                    <button type="submit" id="submitBtn"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 cursor-pointer">
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    //thêm tài khoản
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Thêm tài khoản mới';
        document.getElementById('modalAction').value = 'add_user';
        document.getElementById('submitBtn').textContent = 'Thêm';
        document.getElementById('passwordField').classList.remove('hidden');
        document.getElementById('modal_password').required = true;
        document.getElementById('statusField').classList.add('hidden');

        document.querySelector('#userModal form').reset();
        document.getElementById('modal_user_id').value = '';

        // Hiển thị modal
        document.getElementById('userModal').classList.remove('hidden');
    }
    //sửa tài khoản
    function openEditModal(user) {
        document.getElementById('modalTitle').textContent = 'Sửa tài khoản';
        document.getElementById('modalAction').value = 'update_user';
        document.getElementById('submitBtn').textContent = 'Cập nhật';

        document.getElementById('passwordField').classList.remove('hidden');
        document.getElementById('modal_password').required = false;
        document.getElementById('modal_password').value = '';

        document.getElementById('statusField').classList.remove('hidden');

        // Điền dữ liệu
        document.getElementById('modal_user_id').value = user.id;
        document.getElementById('modal_name').value = user.name;
        document.getElementById('modal_email').value = user.email;
        document.getElementById('modal_mssv').value = user.mssv;
        document.getElementById('modal_phone').value = user.phone || '';
        document.getElementById('modal_birthday').value = user.birthday || '';
        document.getElementById('modal_is_active').checked = user.is_active == 1;

        // Hiển thị modal
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    // thêm hàm validate email CTU
    function validateCTUEmail(email) {
        const ctuEmailPattern = /@student\.ctu\.edu\.vn$/;
        return ctuEmailPattern.test(email);
    }

    // cập nhật hàm validateForm
    function validateForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn.disabled) {
            return false;
        }

        // kiểm tra email CTU
        const emailInput = form.querySelector('input[name="email"]');
        const email = emailInput.value.trim();

        if (!validateCTUEmail(email)) {
            // Dùng modal xác nhận kiểu toast thay cho alert
            showToast && showToast('Chỉ chấp nhận email sinh viên CTU!', 'error');
            emailInput.focus();
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang xử lý...';

        return true;
    }

    function toggleUserStatus(userId, currentStatus) {
        const action = currentStatus ? 'khóa' : 'mở khóa';
        showConfirmModal({
            message: `Bạn có chắc muốn ${action} tài khoản này?`
        }).then((ok) => {
            if (!ok) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="user_id" value="${userId}">
                <input type="hidden" name="current_status" value="${currentStatus}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    }

    function deleteUser(userId) {
        showConfirmModal({
            message: 'Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!'
        }).then((ok) => {
            if (!ok) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="${userId}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    }
</script>

<?php include '../includes/footer.php'; ?>