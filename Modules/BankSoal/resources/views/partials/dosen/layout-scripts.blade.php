{{--
    Shared Dosen Layout JavaScript
    - Profile dropdown toggle
    - Close dropdown on outside click
    - Sidebar toggle (mobile)
    - Close sidebar on nav item click (mobile)
    - Close sidebar on backdrop click (mobile)
    
    Usage: @include('banksoal::partials.dosen.layout-scripts')
--}}

{{-- Snackbar Notification System --}}
<script src="{{ asset('modules/banksoal/js/Banksoal/shared/Snackbar.js') }}"></script>

<script>
// ═══ Profile Dropdown ═══
function toggleProfileDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    const wrapper = document.querySelector('.user-dropdown-wrapper');
    if (dropdown && wrapper && !wrapper.contains(event.target)) {
        dropdown.classList.remove('active');
    }
});

// ═══ Sidebar & Navigation (Mobile) ═══
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    const body = document.body;

    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            body.classList.toggle('sidebar-open');
            // Close dropdown when sidebar toggled
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) dropdown.classList.remove('active');
        });
    }

    // Close sidebar on nav item click (mobile)
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                body.classList.remove('sidebar-open');
            }
        });
    });

    // Close sidebar on backdrop click (mobile)
    document.addEventListener('click', function(e) {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !sidebarToggle?.contains(e.target)) {
                sidebar.classList.remove('active');
                body.classList.remove('sidebar-open');
            }
        }
    });
});
</script>
