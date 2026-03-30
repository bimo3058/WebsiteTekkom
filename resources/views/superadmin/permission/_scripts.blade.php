{{-- resources/views/superadmin/permission/_scripts.blade.php --}}
<script>
/** ── TOGGLE CARD ── **/
function toggleCard(userId) {
    const body = document.getElementById('card-body-' + userId);
    const chevron = document.querySelector('.card-chevron-' + userId);
    if (!body) return;
    const isHidden = body.classList.contains('hidden');
    body.classList.toggle('hidden');
    if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

/** ── AUTOPILOT PERMISSION (REVISED) ── **/
document.addEventListener('change', function(e) {
    if (!e.target.classList.contains('role-checkbox')) return;

    const card = e.target.closest('.user-card');
    const activeCheckboxes = Array.from(card.querySelectorAll('.role-checkbox:checked'));
    
    // Ambil daftar nama role yang aktif
    const activeRoles = activeCheckboxes.map(cb => cb.dataset.roleName);
    
    // Cek apakah user memiliki role akademik (dari DB via data-is-academic) atau superadmin
    const isAcademicRole = activeCheckboxes.some(cb => cb.dataset.isAcademic === '1' || cb.dataset.roleName === 'superadmin');

    card.querySelectorAll('.module-box').forEach(box => {
        const moduleSlug = box.dataset.moduleSlug;
        const allowedRoles = JSON.parse(box.dataset.allAllowedRoles);
        
        // Modul diizinkan jika: User punya Role Akademik ATAU Role Admin yang spesifik untuk modul tersebut
        const isAllowed = isAcademicRole || activeRoles.some(r => allowedRoles.includes(r));
        
        const lockIcon = box.querySelector('.lock-icon');
        const selectAll = box.querySelector('.select-all-container');
        const checkboxes = box.querySelectorAll('.perm-checkbox');

        if (isAllowed) {
            // UNLOCK UI
            box.classList.remove('opacity-40', 'grayscale', 'pointer-events-none');
            lockIcon?.classList.add('hidden');
            selectAll?.classList.remove('hidden');

            if (isAcademicRole) {
                // DEFAULT UNTUK MAHASISWA/DOSEN/GPM: Aktifkan SEMUA permission
                checkboxes.forEach(cb => cb.checked = true);
            } else {
                // DEFAULT UNTUK ADMIN MODUL: Aktifkan hanya View & Edit (Delete dikosongkan)
                checkboxes.forEach(cb => {
                    const name = cb.dataset.perm.toLowerCase();
                    cb.checked = (
                        name.includes('view')  || 
                        name.includes('index') || 
                        name.includes('read')  || 
                        name.includes('edit')  || 
                        name.includes('update')
                    );
                });
            }
        } else {
            // LOCK UI: Jika role yang dipilih bukan pemilik modul ini
            box.classList.add('opacity-40', 'grayscale', 'pointer-events-none');
            lockIcon?.classList.remove('hidden');
            selectAll?.classList.add('hidden');
            
            // Kosongkan semua centang karena akses dicabut
            checkboxes.forEach(cb => cb.checked = false);
        }
    });
});

/** ── FORM SUBMIT RECONCILIATION (FIXED) ── **/
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form.id?.startsWith('perm-form-')) return;
    
    e.preventDefault(); // Cegah submit dulu

    // Bersihkan hidden input lama
    form.querySelectorAll('input[name="unchecked_permissions[]"]').forEach(el => el.remove());

    // Kirim SEMUA checkbox yang tidak dicentang dan modulnya tidak terkunci
    form.querySelectorAll('input.perm-checkbox').forEach(cb => {
        const isLocked = cb.closest('.module-box')?.classList.contains('pointer-events-none');
        if (!cb.checked && !isLocked) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'unchecked_permissions[]';
            hidden.value = cb.dataset.perm;
            form.appendChild(hidden);
        }
    });

    form.submit(); // Submit manual setelah semua hidden input ditambahkan
});

/** ── SELECT ALL & VIEW DEPENDENCY ── **/
document.addEventListener('change', function(e) {
    const cb = e.target;
    if (cb.classList.contains('module-select-all')) {
        const target = cb.dataset.moduleTarget;
        document.querySelectorAll(`.perm-checkbox[data-module-key="${target}"]`).forEach(p => p.checked = cb.checked);
    }
    
    if (cb.classList.contains('master-view-cb') && !cb.checked) {
        const key = cb.dataset.moduleKey;
        document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"][data-is-view="0"]`).forEach(p => p.checked = false);
    }
});
</script>