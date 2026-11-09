<script>
(function () {
    if (window.mkPermManagerLoaded) return;
    window.mkPermManagerLoaded = true;

    // ── Toggle baris form ubah role ───────────────────────────────────────────
    window.mkToggleCard = function (userId) {
        var body    = document.getElementById('card-body-' + userId);
        var chevron = document.querySelector('.card-chevron-' + userId);
        if (!body) return;

        var isHidden = body.style.display === 'none' || body.style.display === '';
        // baris form adalah <tr>, jadi harus table-row — bukan block
        body.style.display = isHidden ? 'table-row' : 'none';
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
            pill.style.borderColor = '#DFE1E7';
            pill.style.color       = '#666D80';
            if (dot) dot.style.background = '#C1C7CF';
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

        var form = e.target;

        var checked = Array.from(form.querySelectorAll('input[name="roles[]"]:checked'));
        if (checked.length === 0) {
            e.preventDefault();
            mkNotify({
                title: 'Role Belum Dipilih',
                message: 'Pilih minimal satu role.',
                variant: 'warning',
            });
            return;
        }

        var values = checked.map(function (cb) { return cb.value; });

        if (values.includes('mahasiswa')) {
            // Dialog modul bersifat asinkron, jadi submit selalu ditahan dulu lalu form
            // dikirim sendiri bila disetujui. form.submit() tidak memicu ulang listener ini.
            e.preventDefault();

            var holder   = form.closest('[data-name]');
            var userName = (holder && holder.getAttribute('data-name')) || 'pengguna ini';

            mkConfirm({
                title: 'Kembalikan ke Mahasiswa Biasa',
                message: 'Yakin ingin mengembalikan ' + userName + ' ke Mahasiswa Biasa?\nSemua role himpunan akan dicabut.',
                confirmText: 'Ya, Kembalikan',
            }).then(function (ok) { if (ok) form.submit(); });
        }
    });
})();
</script>
