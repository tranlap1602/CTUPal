function showToast(message, type = 'success') {
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