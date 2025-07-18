<!-- Quản lý Chi tiêu - Đơn giản hóa cho niên luận cơ sở -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Quản lý chi tiêu</h3>
            <p class="text-gray-600">Theo dõi chi tiêu cá nhân</p>
        </div>
        <button onclick="showAddExpenseForm()"
            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fas fa-plus mr-2"></i>Thêm chi tiêu
        </button>
    </div>

    <!-- Thống kê đơn giản -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Tổng chi tiêu tháng -->
        <div class="bg-red-500 text-white p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Tổng chi tiêu tháng này</p>
                    <p class="text-xl font-bold" id="monthly-total">0 VNĐ</p>
                </div>
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>

        <!-- Chi tiêu hôm nay -->
        <div class="bg-blue-500 text-white p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Chi tiêu hôm nay</p>
                    <p class="text-xl font-bold" id="today-total">0 VNĐ</p>
                </div>
                <i class="fas fa-calendar-day text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Form thêm chi tiêu -->
    <div id="add-expense-form" class="hidden bg-gray-50 border rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-bold">Thêm chi tiêu mới</h4>
            <button onclick="hideAddExpenseForm()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="expense-form" class="space-y-4">
            <!-- Danh mục và số tiền -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục *</label>
                    <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Chọn danh mục</option>
                        <option value="food">Ăn uống</option>
                        <option value="transport">Di chuyển</option>
                        <option value="education">Học tập</option>
                        <option value="entertainment">Giải trí</option>
                        <option value="shopping">Mua sắm</option>
                        <option value="health">Y tế</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tiền (VNĐ) *</label>
                    <input type="number" name="amount" min="0" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập số tiền...">
                </div>
            </div>

            <!-- Ngày và phương thức -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày *</label>
                    <input type="date" name="expense_date" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phương thức *</label>
                    <select name="payment_method" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Tiền mặt</option>
                        <option value="card">Thẻ ngân hàng</option>
                    </select>
                </div>
            </div>

            <!-- Ghi chú -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                <textarea name="description" rows="2" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Ghi chú thêm..."></textarea>
            </div>

            <!-- Nút submit -->
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideAddExpenseForm()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Thêm chi tiêu
                </button>
            </div>
        </form>
    </div>

    <!-- Bộ lọc đơn giản -->
    <div class="bg-white border rounded-lg p-4">
        <div class="flex space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lọc theo danh mục</label>
                <select id="filter-category" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tất cả danh mục</option>
                    <option value="food">Ăn uống</option>
                    <option value="transport">Di chuyển</option>
                    <option value="education">Học tập</option>
                    <option value="entertainment">Giải trí</option>
                    <option value="shopping">Mua sắm</option>
                    <option value="health">Y tế</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lọc theo tháng</label>
                <input type="month" id="filter-month" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>
    </div>

    <!-- Danh sách chi tiêu -->
    <div class="bg-white border rounded-lg p-4">
        <h4 class="text-lg font-semibold mb-4">Danh sách chi tiêu</h4>
        <div id="expenses-list">
            <p class="text-gray-500 text-center py-8">Chưa có chi tiêu nào. Hãy thêm chi tiêu đầu tiên!</p>
        </div>
    </div>
</div>

<!-- JavaScript đơn giản -->
<script>
    function showAddExpenseForm() {
        document.getElementById('add-expense-form').classList.remove('hidden');
        document.querySelector('input[name="expense_date"]').value = new Date().toISOString().split('T')[0];
    }

    function hideAddExpenseForm() {
        document.getElementById('add-expense-form').classList.add('hidden');
        document.getElementById('expense-form').reset();
    }

    // Format số tiền chỉ cho phép số
    document.querySelector('input[name="amount"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>