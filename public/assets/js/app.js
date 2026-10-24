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

    // Add lightweight pagination to every data table rendered in the app layout.
    document.querySelectorAll('table').forEach(function(table) {
        const body = table.tBodies[0];
        if (!body) return;

        const rows = Array.from(body.rows);

        const wrapper = table.parentElement;
        const controlsHost = wrapper && wrapper.classList.contains('overflow-x-auto')
            ? (wrapper.parentElement || wrapper)
            : wrapper;
        const controls = document.createElement('div');
        controls.className = 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-0 px-5 py-4 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700';

        const summary = document.createElement('span');
        summary.className = 'whitespace-nowrap';
        const actions = document.createElement('div');
        actions.className = 'flex flex-wrap items-center justify-start sm:justify-end gap-2';

        const pageIndicator = document.createElement('span');
        pageIndicator.className = 'px-2 text-xs font-medium text-gray-600 dark:text-gray-300';

        const pageSize = document.createElement('select');
        pageSize.className = 'px-2.5 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs';
        [10, 25, 50].forEach(function(size) {
            const option = document.createElement('option');
            option.value = size;
            option.textContent = size + ' per page';
            pageSize.appendChild(option);
        });

        const previous = document.createElement('button');
        previous.type = 'button';
        previous.textContent = 'Previous';
        previous.className = 'px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:bg-gray-100 disabled:text-gray-400 dark:disabled:bg-gray-800 dark:disabled:text-gray-600 disabled:cursor-not-allowed';

        const next = document.createElement('button');
        next.type = 'button';
        next.textContent = 'Next';
        next.className = previous.className;

        actions.appendChild(pageSize);
        actions.appendChild(pageIndicator);
        actions.appendChild(previous);
        actions.appendChild(next);
        controls.appendChild(summary);
        controls.appendChild(actions);
        controlsHost.appendChild(controls);

        let currentPage = 1;
        const render = function() {
            const size = Number(pageSize.value);
            const totalPages = Math.max(1, Math.ceil(rows.length / size));
            currentPage = Math.min(currentPage, totalPages);
            const start = (currentPage - 1) * size;

            rows.forEach(function(row, index) {
                row.hidden = index < start || index >= start + size;
            });

            const first = rows.length ? start + 1 : 0;
            const last = Math.min(start + size, rows.length);
            summary.textContent = 'Showing ' + first + '–' + last + ' of ' + rows.length;
            pageIndicator.textContent = 'Page ' + currentPage + ' of ' + totalPages;
            previous.disabled = currentPage === 1;
            next.disabled = currentPage === totalPages;
        };

        pageSize.addEventListener('change', function() {
            currentPage = 1;
            render();
        });
        previous.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                render();
            }
        });
        next.addEventListener('click', function() {
            currentPage++;
            render();
        });
        render();
    });
});
