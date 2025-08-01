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

            try {
                // Kiểm tra email và mssv đã tồn tại (trừ user hiện tại)
                $existingUser = fetchOne("SELECT id FROM users WHERE (email = ? OR mssv = ?) AND id != ?", [$email, $mssv, $user_id]);
                if ($existingUser) {
                    header('Location: users.php?message=' . urlencode('Email hoặc MSSV đã tồn tại!') . '&type=error');
                    exit();
                } else {
                    executeQuery(
                        "UPDATE users SET name = ?, email = ?, mssv = ?, phone = ?, birthday = ?, is_active = ? WHERE id = ?",
                        [$name, $email, $mssv, $phone, $birthday, $is_active, $user_id]
                    );
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
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$whereClause = "WHERE role = 'user'";
$params = [];

if (!empty($search)) {
    $whereClause .= " AND (name LIKE ? OR email LIKE ? OR mssv LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$totalUsers = fetchOne("SELECT COUNT(*) as count FROM users $whereClause", $params)['count'];
$totalPages = ceil($totalUsers / $limit);

$users = fetchAll(
    "SELECT * FROM users $whereClause ORDER BY created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset),
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
        <button onclick="openAddModal()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Thêm tài khoản
        </button>
    </div>

    <!-- Bảng danh sách -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">Danh sách sinh viên</h2>
                    <p class="text-purple-100 text-sm">Tổng cộng <?php echo $totalUsers; ?> tài khoản</p>
                </div>
            </div>
        </div>

        <!-- Thanh tìm kiếm trong bảng -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="flex max-w-md">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Tìm kiếm theo tên, email, MSSV..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded-r-lg hover:bg-purple-600 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MSSV</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SĐT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày sinh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-purple-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['mssv']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo $user['birthday'] ? date('d/m/Y', strtotime($user['birthday'])) : '-'; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($user['is_active']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hoạt động</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active']; ?>)"
                                    class="text-yellow-600 hover:text-yellow-900 mr-3 transition-colors">
                                    <i class="fas fa-<?php echo $user['is_active'] ? 'lock' : 'unlock'; ?>"></i>
                                </button>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)"
                                    class="text-red-600 hover:text-red-900 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Hiển thị <?php echo $offset + 1; ?> đến <?php echo min($offset + $limit, $totalUsers); ?> trong tổng số <?php echo $totalUsers; ?> tài khoản
                    </div>
                    <div class="flex space-x-2">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
                                class="px-3 py-2 text-sm font-medium rounded-lg <?php echo $i == $page ? 'bg-purple-600 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'; ?> transition-colors">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal thêm tài khoản -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-green-600">
                <h3 class="text-lg font-semibold text-white">Thêm tài khoản mới</h3>
            </div>
            <form method="POST" class="p-6" onsubmit="return validateForm(this)">
                <input type="hidden" name="action" value="add_user">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">MSSV *</label>
                        <input type="text" name="mssv" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu *</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày sinh</label>
                        <input type="date" name="birthday" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa tài khoản -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
                <h3 class="text-lg font-semibold text-white">Sửa tài khoản</h3>
            </div>
            <form method="POST" class="p-6" onsubmit="return validateForm(this)">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="edit_email" required autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">MSSV *</label>
                        <input type="text" name="mssv" id="edit_mssv" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                        <input type="text" name="phone" id="edit_phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày sinh</label>
                        <input type="date" name="birthday" id="edit_birthday" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="edit_is_active" class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Tài khoản hoạt động</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        // Reset form khi mở modal
        document.querySelector('#addModal form').reset();
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(user) {
        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_mssv').value = user.mssv;
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_birthday').value = user.birthday || '';
        document.getElementById('edit_is_active').checked = user.is_active == 1;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Hàm validate form và tránh submit nhiều lần
    function validateForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn.disabled) {
            return false; // Đã submit rồi
        }

        // Disable button để tránh submit nhiều lần
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang xử lý...';

        return true;
    }

    function toggleUserStatus(userId, currentStatus) {
        const action = currentStatus ? 'khóa' : 'mở khóa';
        if (confirm(`Bạn có chắc muốn ${action} tài khoản này?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="user_id" value="${userId}">
                <input type="hidden" name="current_status" value="${currentStatus}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function deleteUser(userId) {
        if (confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="${userId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Đóng modal khi click bên ngoài
    window.onclick = function(event) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        if (event.target == addModal) {
            closeAddModal();
        }
        if (event.target == editModal) {
            closeEditModal();
        }
    }
</script>

<?php include '../includes/footer.php'; ?>