{{--
    Input bukti dukung (khusus PDF). Pilihan berkas ditampilkan sebagai daftar ikon
    berikut tombol hapus dan pratinjau pop-up, seperti "Ajukan Riwayat Kegiatan" di Verifikasi Kegiatan.
    Bukti opsional di semua kategori. Bukti yang sudah terunggah (saat kembali dari
    Konfirmasi) bisa diganti dengan memilih berkas baru, atau dibuang lewat centang bukti_hapus.
    Param: $buktiPendingItems (bukti yang sudah terunggah, opsional).
--}}
@include('manajemenmahasiswa::pengaduan.partials.bukti-styles')
@php
    $pendingCount = count($buktiPendingItems ?? []);
    $maxFiles = \Modules\ManajemenMahasiswa\Support\PengaduanBukti::MAX_FILES;
    $maxKb = \Modules\ManajemenMahasiswa\Support\PengaduanBukti::MAX_KB;
    $maxMb = $maxKb / 1024;
@endphp
<label class="pgd-label" for="buktiInput">Berkas PDF <span class="is-opt">(opsional)</span></label>
{{-- Input bawaan disembunyikan (tetap bisa difokus keyboard): tampilannya selalu
     menulis "Tidak ada file yang dipilih", padahal berkas terpilih sudah didaftar di bawah. --}}
<input type="file" class="bk-file-native" name="bukti[]" id="buktiInput" accept="application/pdf,.pdf" multiple>
<label class="bk-picker" for="buktiInput">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    Pilih Berkas PDF
</label>
<div class="pgd-help">
    PDF saja, maks. {{ $maxFiles }} berkas &times; {{ $maxMb }} MB. Foto/screenshot gabungkan ke PDF.
</div>
{{-- Hanya saat identitas disembunyikan (form-fields menyalakannya lewat data-pgd-anon-only):
     tanpa centang itu identitas pelapor memang sudah diketahui pengelola. --}}
<div class="pgd-help" data-pgd-anon-only @unless (old('is_anonim')) hidden @endunless style="color: var(--c-warning); font-weight: 600;">
    Pastikan isi PDF tidak memuat nama atau NIM Anda.
</div>
<div class="bk-list" id="buktiPreview"></div>
<div class="bk-error" id="buktiError" role="alert"></div>
@error('bukti') <div class="pgd-error">{{ $message }}</div> @enderror
@error('bukti.*') <div class="pgd-error">{{ $message }}</div> @enderror
@if ($pendingCount > 0)
    <div class="pgd-help" style="margin-top: 12px; color: var(--c-primary); font-weight: 600;">
        {{ $pendingCount }} berkas sudah terunggah. Pilih berkas baru untuk menggantinya.
    </div>
    @include('manajemenmahasiswa::pengaduan.partials.bukti-items', ['items' => $buktiPendingItems])
    <label class="pgd-help" style="display: inline-flex; align-items: center; gap: 8px; margin-top: 8px; cursor: pointer;">
        <input type="checkbox" name="bukti_hapus" value="1" style="width: 15px; height: 15px; margin: 0; accent-color: var(--c-primary);">
        Jangan lampirkan berkas ini
    </label>
@endif
<script>
    (function () {
        var input = document.getElementById('buktiInput');
        var list = document.getElementById('buktiPreview');
        var errBox = document.getElementById('buktiError');
        if (!input || !list) return;

        var MAX_FILES = {{ $maxFiles }};
        var MAX_BYTES = {{ $maxKb }} * 1024;
        var ALLOWED = /\.pdf$/i;
        var files = [];

        function fmtSize(b) {
            return b >= 1048576 ? (b / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';
        }

        function sync() {
            var dt = new DataTransfer();
            files.forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;
        }

        function el(tag, cls, text) {
            var node = document.createElement(tag);
            if (cls) node.className = cls;
            if (text !== undefined) node.textContent = text;
            return node;
        }

        function render() {
            list.innerHTML = '';
            files.forEach(function (file, idx) {
                // Berkas ini belum ada di server, jadi pop-up dibuka dari blob URL yang
                // dibuat saat diklik (dilepas lagi oleh penampil saat ditutup).
                var row = el('div', 'bk-row');
                row.title = 'Lihat isi berkas';
                row.setAttribute('role', 'button');
                row.tabIndex = 0;
                row.style.cursor = 'pointer';
                row.addEventListener('click', function (e) {
                    if (e.target.closest('.bk-remove') || !window.bkOpenViewer) return;
                    window.bkOpenViewer(URL.createObjectURL(file), file.name);
                });
                row.addEventListener('keydown', function (e) {
                    // e.target !== row: tombol hapus di dalamnya punya aksi sendiri.
                    if (e.target !== row || (e.key !== 'Enter' && e.key !== ' ')) return;
                    e.preventDefault();
                    row.click();
                });
                var text = el('span', 'bk-text');
                var name = el('span', 'bk-name', file.name);
                name.title = file.name;
                text.appendChild(name);
                text.appendChild(el('span', 'bk-sub', 'Dokumen PDF · ' + fmtSize(file.size)));
                var remove = el('button', 'bk-remove');
                remove.type = 'button';
                remove.title = 'Hapus berkas';
                remove.innerHTML = '&times;';
                remove.addEventListener('click', function () {
                    files.splice(idx, 1);
                    errBox.textContent = '';
                    sync();
                    render();
                });
                row.appendChild(el('span', 'bk-icon pdf', 'PDF'));
                row.appendChild(text);
                row.appendChild(remove);
                list.appendChild(row);
            });
        }

        input.addEventListener('change', function () {
            var problems = [];
            Array.from(input.files).forEach(function (f) {
                var dup = files.some(function (x) { return x.name === f.name && x.size === f.size && x.lastModified === f.lastModified; });
                if (dup) return;
                if (!ALLOWED.test(f.name)) problems.push('"' + f.name + '" bukan berkas PDF.');
                else if (f.size > MAX_BYTES) problems.push('"' + f.name + '" melebihi {{ $maxMb }} MB.');
                else if (files.length >= MAX_FILES) problems.push('Maksimal ' + MAX_FILES + ' berkas; "' + f.name + '" tidak ditambahkan.');
                else files.push(f);
            });
            errBox.textContent = problems.join(' ');
            sync();
            render();
        });
    })();
</script>
