{{-- resources/views/superadmin/users/_actions.blade.php --}}
{{-- SATU-SATUNYA sumber helper aksi user (Edit / Force Logout / Suspend / Delete).
     Setiap helper memvalidasi ID numerik sebelum membangun URL, sehingga
     "/superadmin/users/undefined/..." tidak akan pernah di-request
     (penyebab SQLSTATE[22P02] invalid input syntax for type bigint). --}}
<script>
(function () {
    function validUserId(v) {
        var n = Number(v);
        return Number.isInteger(n) && n > 0 ? n : null;
    }

    function userActionUrl(id, suffix) {
        return '/superadmin/users/' + encodeURIComponent(id) + suffix;
    }

    function showModalById(id) {
        if (typeof window.openModal === 'function') { window.openModal(id); return; }
        var el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
            if (!el.style.display || el.style.display === 'none') el.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    window.openEditInfo = function (data) {
        var id = data ? validUserId(data.id) : null;
        if (!id) return;
        window.location.href = userActionUrl(id, '/edit');
    };

    window.openForceLogoutModal = function (data) {
        var id = data ? validUserId(data.id) : null;
        if (!id) return;
        var form = document.getElementById('formForceLogout');
        if (form) form.action = userActionUrl(id, '/force-logout');
        var nameEl = document.getElementById('logoutTargetName');
        if (nameEl) nameEl.textContent = (data && data.name) || '';
        showModalById('modalForceLogout');
    };

    window.openSuspendModal = function (data) {
        var id = data ? validUserId(data.id) : null;
        if (!id) return;
        var form = document.getElementById('formSuspend');
        if (form) form.action = userActionUrl(id, '/suspend');
        // Dua kemungkinan ID target nama (halaman user vs audit logs)
        var nameEl = document.getElementById('suspendUserName') || document.getElementById('suspendTargetName');
        if (nameEl) nameEl.textContent = (data && data.name) || '';
        showModalById('modalSuspend');
    };

    window.openDeleteHybrid = function (data) {
        var id = data ? validUserId(data.id) : null;
        if (!id) return;
        var form = document.getElementById('formDeleteHybrid');
        if (form) {
            form.action = userActionUrl(id, '/destroy');
            // Bersihkan sisa hidden ids[] dari mode bulk agar tidak terbawa
            form.querySelectorAll('.bulk-ids-input').forEach(function (el) { el.remove(); });
        }
        var nameEl = document.getElementById('deleteTargetName');
        if (nameEl) nameEl.textContent = (data && data.name) || '';
        showModalById('modalDeleteHybrid');
    };
})();
</script>
