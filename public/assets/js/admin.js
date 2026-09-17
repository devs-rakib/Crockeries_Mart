document.addEventListener('DOMContentLoaded', function () {

    /* ── Sidebar Toggle (mobile) ── */
    const sidebar        = document.getElementById('adminSidebar');
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }

    /* ── Sidebar submenu toggle rotation ── */
    document.querySelectorAll('.sidebar-nav .nav-link[data-bs-toggle="collapse"]').forEach(function (link) {
        link.addEventListener('click', function () {
            const icon = this.querySelector('.nav-toggle-icon');
            if (icon) {
                setTimeout(function () {
                    icon.classList.toggle('rotated', link.getAttribute('aria-expanded') === 'true');
                }, 50);
            }
        });
    });

    /* ── Auto-dismiss flash alerts after 5s ── */
    document.querySelectorAll('.admin-flash .alert').forEach(function (alert) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

});
