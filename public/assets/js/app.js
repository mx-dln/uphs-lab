// Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    if (overlay) overlay.classList.toggle('hidden');
}

// User Menu Toggle
function toggleUserMenu() {
    document.getElementById('userMenu').classList.toggle('hidden');
}

// Dark Mode Toggle
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    document.cookie = 'dark_mode=' + isDark + '; path=/; max-age=' + (365 * 24 * 60 * 60);
}

// Close user menu on outside click
document.addEventListener('click', function(e) {
    const menu = document.getElementById('userMenu');
    if (menu && !menu.classList.contains('hidden')) {
        const trigger = e.target.closest('[onclick="toggleUserMenu()"]');
        if (!trigger && !e.target.closest('#userMenu')) {
            menu.classList.add('hidden');
        }
    }
});

// Auto-dismiss flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashes = document.querySelectorAll('[class*="animate-slide-in"]');
    flashes.forEach(flash => {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.3s, transform 0.3s';
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(100px)';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    });
});
