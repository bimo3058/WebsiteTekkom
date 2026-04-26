<script>
(function () {
    if (window.mkPermManagerLoaded) return;
    window.mkPermManagerLoaded = true;

    // ── Toggle expand/collapse card ───────────────────────────────────────────
    window.mkToggleCard = function (userId) {
        var body    = document.getElementById('card-body-' + userId);
        var chevron = document.querySelector('.card-chevron-' + userId);
        if (!body) return;

        var isHidden = body.style.display === 'none' || body.style.display === '';
        body.style.display = isHidden ? 'block' : 'none';
        if (chevron) {
            chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    };

    // ── Sync visual pill dengan state checkbox ────────────────────────────────
    function syncPill(checkbox) {
        var pill = checkbox.closest('label').querySelector('.mk-role-pill');
        var dot  = pill ? pill.querySelector('.mk-dot') : null;
        if (!pill) return;

        if (checkbox.checked) {
            pill.style.background  = pill.dataset.activeBg;
            pill.style.borderColor = pill.dataset.activeBorder;
            pill.style.color       = pill.dataset.activeColor;
            if (dot) dot.style.background = pill.dataset.activeColor;
        } else {
            pill.style.background  = '#fff';
            pill.style.borderColor = '#DEE2E6';
            pill.style.color       = '#6C757D';
            if (dot) dot.style.background = '#DEE2E6';
        }
    }

    // ── Logika eksklusivitas & multi-select ───────────────────────────────────
    document.addEventListener('change', function (e) {
        var target = e.target;
        if (!target.classList.contains('mk-role-check')) return;

        var form        = target.closest('form');
        if (!form) return;

        var allChecks   = Array.from(form.querySelectorAll('.mk-role-check'));
        var isExclusive = target.dataset.exclusive === '1';

        if (target.checked) {
            if (isExclusive) {
                // Uncheck semua yang lain
                allChecks.forEach(function (cb) {
                    if (cb !== target) {
                        cb.checked = false;
                        syncPill(cb);
                    }
                });
            } else {
                // Jika memilih role posisi, uncheck semua yang eksklusif (mahasiswa/alumni)
                allChecks.forEach(function (cb) {
                    if (cb !== target && cb.dataset.exclusive === '1') {
                        cb.checked = false;
                        syncPill(cb);
                    }
                });
            }
        }

        // Jika semua di-uncheck, paksa ceklis mahasiswa biasa sebagai fallback
        var anyChecked = allChecks.some(function (cb) { return cb.checked; });
        if (!anyChecked) {
            var mahasiswaCb = form.querySelector('input[value="mahasiswa"]');
            if (mahasiswaCb) {
                mahasiswaCb.checked = true;
                syncPill(mahasiswaCb);
                return;
            }
        }

        syncPill(target);
    });

    // ── Konfirmasi sebelum submit ─────────────────────────────────────────────
    document.addEventListener('submit', function (e) {
        if (!e.target.id || !e.target.id.startsWith('role-form-')) return;

        var checked = Array.from(e.target.querySelectorAll('input[name="roles[]"]:checked'));
        if (checked.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu role.');
            return;
        }

        var values = checked.map(function (cb) { return cb.value; });

        if (values.includes('mahasiswa')) {
            var userName = e.target.closest('.user-card').getAttribute('data-name') || 'pengguna ini';
            var ok = confirm('Yakin ingin mengembalikan ' + userName + ' ke Mahasiswa Biasa?\nSemua role himpunan akan dicabut.');
            if (!ok) e.preventDefault();
        }
    });
})();
</script>
