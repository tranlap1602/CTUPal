// Lưu lịch sử toast vào localStorage (tối đa 10 mục)
function saveToastToHistory(message, type) {
    const storageKey = 'recent_toasts';
    let list = [];
    try {
        const raw = localStorage.getItem(storageKey);
        list = raw ? JSON.parse(raw) : [];
        if (!Array.isArray(list)) list = [];
    } catch (_) {
        list = [];
    }
    try {
        list.unshift({ message, type, time: Date.now() });
        const limited = list.slice(0, 10);
        localStorage.setItem(storageKey, JSON.stringify(limited));
    } catch (_) {
        // ignore quota or storage errors
    }
}

// Lấy n toast gần nhất
function getRecentToasts(limit = 3) {
    const storageKey = 'recent_toasts';
    try {
        const raw = localStorage.getItem(storageKey);
        const list = raw ? JSON.parse(raw) : [];
        return Array.isArray(list) ? list.slice(0, limit) : [];
    } catch (_) {
        return [];
    }
}

// Expose ra window để nơi khác sử dụng
window.getRecentToasts = getRecentToasts;

function showToast(message, type = 'success', record = true) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="${icon}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);
    // Lưu lịch sử nếu cần
    if (record !== false) {
        saveToastToHistory(message, type);
    }
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const type = urlParams.get('type') || 'success';

    if (message) {
        showToast(decodeURIComponent(message), type);
        // Clean up URL
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('message');
        newUrl.searchParams.delete('type');
        window.history.replaceState({}, '', newUrl);
    }
});

// Gán rõ vào window
window.showToast = window.showToast || showToast;