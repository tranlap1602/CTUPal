// Các hàm tiện ích chung
document.addEventListener('DOMContentLoaded', function () {
    // Khởi tạo các sự kiện
    initializeEvents();

    // Đặt ngày hiện tại cho các input date
    setCurrentDate();
});

function initializeEvents() {
    // Xử lý form submit chung
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });
}

function setCurrentDate() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        if (!input.value) {
            input.value = today;
        }
    });
}

function handleFormSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    console.log('Form data:', Object.fromEntries(formData));
    alert('Chức năng đang được phát triển!');
}

// Hàm hiển thị/ẩn các view trong timetable
function showView(viewType) {
    const viewContainer = document.getElementById('timetable-view');
    const importContainer = document.getElementById('timetable-import');

    if (viewType === 'view') {
        viewContainer.style.display = 'block';
        importContainer.style.display = 'none';
    } else if (viewType === 'import') {
        viewContainer.style.display = 'none';
        importContainer.style.display = 'block';
    }
}



// Hàm quản lý tài liệu
function showUploadForm() {
    const uploadForm = document.getElementById('upload-form');
    uploadForm.style.display = 'block';
}

function hideUploadForm() {
    const uploadForm = document.getElementById('upload-form');
    uploadForm.style.display = 'none';
}

function filterDocuments() {
    const categoryFilter = document.getElementById('filter-category').value;
    const documents = document.querySelectorAll('.document-item');

    documents.forEach(doc => {
        const docCategory = doc.getAttribute('data-category');
        if (!categoryFilter || docCategory === categoryFilter) {
            doc.style.display = 'block';
        } else {
            doc.style.display = 'none';
        }
    });
}

function searchDocuments() {
    const searchTerm = document.getElementById('search-documents').value.toLowerCase();
    const documents = document.querySelectorAll('.document-item');

    documents.forEach(doc => {
        const title = doc.querySelector('h4').textContent.toLowerCase();
        const description = doc.querySelector('.document-description').textContent.toLowerCase();

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            doc.style.display = 'block';
        } else {
            doc.style.display = 'none';
        }
    });
}

function viewDocument(id) {
    alert(`Xem tài liệu ID: ${id}`);
}

function downloadDocument(id) {
    alert(`Tải tài liệu ID: ${id}`);
}

function deleteDocument(id) {
    if (confirm('Bạn có chắc chắn muốn xóa tài liệu này?')) {
        alert(`Xóa tài liệu ID: ${id}`);
    }
}

// Hàm quản lý chi tiêu
function showAddExpenseForm() {
    const expenseForm = document.getElementById('add-expense-form');
    expenseForm.style.display = 'block';
}

function hideAddExpenseForm() {
    const expenseForm = document.getElementById('add-expense-form');
    expenseForm.style.display = 'none';
}

function filterExpenses() {
    const categoryFilter = document.getElementById('filter-expense-category').value;
    const monthFilter = document.getElementById('filter-month').value;
    const expenses = document.querySelectorAll('.expense-item');

    expenses.forEach(expense => {
        const expenseCategory = expense.getAttribute('data-category');
        let showExpense = true;

        if (categoryFilter && expenseCategory !== categoryFilter) {
            showExpense = false;
        }

        // Thêm logic lọc theo tháng ở đây nếu cần

        expense.style.display = showExpense ? 'flex' : 'none';
    });
}

function editExpense(id) {
    alert(`Chỉnh sửa chi tiêu ID: ${id}`);
}

function deleteExpense(id) {
    if (confirm('Bạn có chắc chắn muốn xóa chi tiêu này?')) {
        alert(`Xóa chi tiêu ID: ${id}`);
    }
}

// Hàm quản lý ghi chú
function showAddNoteForm() {
    const noteForm = document.getElementById('add-note-form');
    noteForm.style.display = 'block';
}

function hideAddNoteForm() {
    const noteForm = document.getElementById('add-note-form');
    noteForm.style.display = 'none';
}

function filterNotes() {
    const categoryFilter = document.getElementById('filter-note-category').value;
    const priorityFilter = document.getElementById('filter-priority').value;
    const notes = document.querySelectorAll('.note-item');

    notes.forEach(note => {
        const noteCategory = note.getAttribute('data-category');
        const notePriority = note.getAttribute('data-priority');
        let showNote = true;

        if (categoryFilter && noteCategory !== categoryFilter) {
            showNote = false;
        }

        if (priorityFilter && notePriority !== priorityFilter) {
            showNote = false;
        }

        note.style.display = showNote ? 'block' : 'none';
    });
}

function searchNotes() {
    const searchTerm = document.getElementById('search-notes').value.toLowerCase();
    const notes = document.querySelectorAll('.note-item');

    notes.forEach(note => {
        const title = note.querySelector('h4').textContent.toLowerCase();
        const preview = note.querySelector('.note-preview').textContent.toLowerCase();
        const tags = note.querySelector('.note-tags').textContent.toLowerCase();

        if (title.includes(searchTerm) || preview.includes(searchTerm) || tags.includes(searchTerm)) {
            note.style.display = 'block';
        } else {
            note.style.display = 'none';
        }
    });
}

function viewNote(id) {
    alert(`Xem ghi chú ID: ${id}`);
}

function editNote(id) {
    alert(`Chỉnh sửa ghi chú ID: ${id}`);
}

function deleteNote(id) {
    if (confirm('Bạn có chắc chắn muốn xóa ghi chú này?')) {
        alert(`Xóa ghi chú ID: ${id}`);
    }
}

// Hàm xử lý responsive menu (nếu cần)
function toggleMobileMenu() {
    const nav = document.querySelector('nav ul');
    nav.classList.toggle('mobile-menu-open');
}

// Hàm format số tiền
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Hàm format ngày tháng
function formatDate(date) {
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(new Date(date));
}

// Hàm hiển thị thông báo
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Thêm styles cho notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;

    document.body.appendChild(notification);

    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Thêm CSS animations cho notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style); 