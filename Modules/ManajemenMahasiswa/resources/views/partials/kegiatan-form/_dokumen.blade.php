{{--
    Isi kartu dokumen kegiatan — dipakai bersama form Rencana Proker, Pelaksanaan,
    dan Laporan & Arsip (tambah & edit).

    Tiap dokumen, baru maupun tersimpan, punya dropdown jenis (S-56) supaya
    Proposal tetap dikenali sebagai Proposal walau nama berkasnya acak:
      • dokumen baru  → `dokumen_jenis[]`, sejajar urutannya dengan `dokumen_kegiatan[]`
      • dokumen lama  → `jenis_dokumen_lama[id]`; dokumen sebelum S-56 tampil
                        "Pilih jenis..." dan wajib dipilih saat form disimpan
    Proposal, RAB, Notulensi, dan LPJ hanya boleh satu: begitu dipakai satu baris,
    opsinya dinonaktifkan di baris lain. Server memeriksa ulang di
    RepoMulmedService::pastikanJenisDokumen().

    Param: $existingDokumen (koleksi RepoMulmed, boleh kosong), $tahapRencana (bool),
    $kunciDariRencana (bool, Pelaksanaan & Edit Arsip): dokumen salinan Rencana Proker
    tampil terkunci — tanpa tombol hapus, jenisnya tak bisa diganti (S-59, S-60).
    Butuh markFileForDeletion() dan #deleteFileInputs milik halaman pemakai.
--}}
@php
    $existingDokumen = $existingDokumen ?? collect();
    $kunciDariRencana = $kunciDariRencana ?? false;
    $adaTerkunci     = $kunciDariRencana && $existingDokumen->contains('dari_rencana', true);
    $pilihanJenis    = \Modules\ManajemenMahasiswa\Support\JenisDokumenKegiatan::pilihan($tahapRencana ?? false);
    $maksDokumen     = \Modules\ManajemenMahasiswa\Http\Controllers\KegiatanController::MAKS_DOKUMEN;
    $maksUkuranMb    = intdiv(\Modules\ManajemenMahasiswa\Http\Controllers\KegiatanController::MAKS_UKURAN_DOKUMEN_KB, 1024);
    $iconMap         = ['pdf' => 'PDF', 'doc' => 'DOC', 'docx' => 'DOC', 'xls' => 'XLS', 'xlsx' => 'XLS', 'ppt' => 'PPT', 'pptx' => 'PPT'];
    // Kelas warna ikon: sama dengan teksnya (is-pdf, is-doc, ...).
@endphp
<style>
    .doc-preview-item .doc-jenis {
        width: 200px;
        flex-shrink: 0;
    }
    /* Ikon jenis berkas: kotak berwarna, palet sama dengan kartu dokumen di detail. */
    .doc-preview-item .doc-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3px;
        flex-shrink: 0;
        background: var(--c-surface-muted);
        color: var(--c-fg-muted);
    }
    .doc-preview-item .doc-icon.is-pdf { background: var(--c-error-subtle); color: var(--c-error); }
    .doc-preview-item .doc-icon.is-doc { background: var(--c-sky-subtle); color: var(--c-sky); }
    .doc-preview-item .doc-icon.is-xls { background: var(--c-success-subtle); color: var(--c-success); }
    .doc-preview-item .doc-icon.is-ppt { background: var(--c-warning-subtle); color: var(--c-warning); }
    /* Kolom aksi selebar dua tombol, supaya dropdown jenis sejajar di semua baris
       (dokumen tersimpan punya Unduh + Hapus/gembok, dokumen baru hanya Hapus). */
    .doc-preview-item .doc-aksi {
        width: 72px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }
    .doc-preview-item .doc-terkunci {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--c-primary-subtle);
        color: var(--c-primary);
        cursor: help;
    }
    .doc-preview-item .doc-asal {
        color: var(--c-primary);
        font-weight: 600;
    }
    @media (max-width: 576px) {
        .doc-preview-item { flex-wrap: wrap; }
        .doc-preview-item .doc-jenis { order: 5; width: 100%; }
        /* Di HP dropdown pindah ke baris sendiri; kolom aksi cukup selebar isinya. */
        .doc-preview-item .doc-aksi { width: auto; }
    }
</style>

{{-- Gaya & skrip mkSelect dicetak @once oleh pemakaian pertamanya, sedangkan isi
     <template> di bawah tidak pernah dijalankan browser. Pemakaian pertama karena
     itu harus di luar template: dropdown tersembunyi tanpa name ini tidak terkirim. --}}
<div hidden>
    <x-manajemenmahasiswa::ui.select id="jenisDokumenAset" disabled><option value=""></option></x-manajemenmahasiswa::ui.select>
</div>

<div id="dokumenLamaList">
    @if($existingDokumen->count() > 0)
        <div class="existing-file-label">Dokumen yang sudah diupload</div>
        @if($adaTerkunci)
            <p style="font-size: 12px; color: var(--c-fg-muted); margin: -4px 0 10px;">Dokumen bertanda "Dari Rencana" disalin dari Rencana Proker dan tidak bisa dihapus.</p>
        @endif
        @foreach($existingDokumen as $doc)
            @php
                $ext  = strtolower(pathinfo($doc->nama_file, PATHINFO_EXTENSION));
                $icon = $iconMap[$ext] ?? 'FILE';
                $terkunci      = $kunciDariRencana && $doc->dari_rencana;
                // Salinan lama yang belum berjenis tetap boleh dipilih jenisnya sekali.
                $jenisTerkunci = $terkunci && $doc->jenis_dokumen !== null;
                $jenisTerpilih = $jenisTerkunci ? $doc->jenis_dokumen : old('jenis_dokumen_lama.' . $doc->id, $doc->jenis_dokumen);
            @endphp
            <div class="doc-preview-item" id="existingFile{{ $doc->id }}">
                <span class="doc-icon is-{{ strtolower($icon) }}">{{ $icon }}</span>
                <div class="doc-info">
                    <div class="doc-name" title="{{ $doc->nama_file }}">{{ $doc->nama_file }}</div>
                    <div class="doc-size">{{ strtoupper($ext) }}@if($terkunci) • <span class="doc-asal">Dari Rencana</span>@endif</div>
                </div>
                <div class="doc-jenis">
                    <x-manajemenmahasiswa::ui.select name="jenis_dokumen_lama[{{ $doc->id }}]" id="jenisDokumenLama{{ $doc->id }}" required data-jenis-dokumen
                        :disabled="$jenisTerkunci" :data-terkunci="$jenisTerkunci ? '1' : false">
                        <option value="" hidden>Pilih jenis...</option>
                        @foreach($pilihanJenis as $kode => $label)
                            <option value="{{ $kode }}" @selected($jenisTerpilih === $kode)>{{ $label }}</option>
                        @endforeach
                    </x-manajemenmahasiswa::ui.select>
                </div>
                <div class="doc-aksi">
                    <a href="{{ $doc->url_unduh }}" target="_blank" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" title="Unduh dokumen" aria-label="Unduh dokumen {{ $doc->nama_file }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
                    @if($terkunci)
                        <span class="doc-terkunci" role="img" title="Disalin dari Rencana Proker — tidak bisa dihapus" aria-label="Disalin dari Rencana Proker, tidak bisa dihapus"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    @else
                        <button type="button" class="btn-remove-doc mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon" data-hapus-dokumen="{{ $doc->id }}" title="Hapus dokumen" aria-label="Hapus dokumen {{ $doc->nama_file }}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                    @endif
                </div>
            </div>
        @endforeach
        <div style="margin-bottom: 16px;"></div>
    @endif
</div>

<div class="file-upload-area" id="dokumenUploadArea" role="button" tabindex="0">
    <div class="upload-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg></div>
    <p>Klik atau drag & drop dokumen ke sini</p>
    <small>Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX • Maks: {{ $maksUkuranMb }} MB per file<br>Setelah diunggah, pilih jenis tiap dokumen ({{ implode(', ', $pilihanJenis) }}).</small>
</div>
<input type="file" name="dokumen_kegiatan[]" id="dokumenInput"
       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" multiple hidden>
<div id="dokumenBaruList" style="margin-top: 12px;"></div>

<template id="jenisDokumenTemplate">
    <x-manajemenmahasiswa::ui.select name="dokumen_jenis[]" id="jenisDokumenBaru__N__" required data-jenis-dokumen>
        <option value="" hidden>Pilih jenis...</option>
        @foreach($pilihanJenis as $kode => $label)
            <option value="{{ $kode }}">{{ $label }}</option>
        @endforeach
    </x-manajemenmahasiswa::ui.select>
</template>

<script>
(function () {
    const LABEL      = @json($pilihanJenis);
    const MAKS_SATU  = @json(\Modules\ManajemenMahasiswa\Support\JenisDokumenKegiatan::MAKS_SATU);
    // Batas total dokumen — satu sumber dengan validasi server (KegiatanController::MAKS_DOKUMEN).
    const MAKS_DOKUMEN = {{ $maksDokumen }};
    const DIIZINKAN  = /\.(pdf|docx?|xlsx?|pptx?)$/i;
    const ICON       = { pdf: 'PDF', doc: 'DOC', docx: 'DOC', xls: 'XLS', xlsx: 'XLS', ppt: 'PPT', pptx: 'PPT' };

    const input    = document.getElementById('dokumenInput');
    const area     = document.getElementById('dokumenUploadArea');
    const lamaList = document.getElementById('dokumenLamaList');
    const baruList = document.getElementById('dokumenBaruList');
    const template = document.getElementById('jenisDokumenTemplate');
    if (!input || !area || !baruList || !template) return;

    /** @type {{file: File, jenis: string}[]} */
    let baru = [];
    let nomor = 0;

    const ukuran = bytes => bytes < 1024 ? bytes + ' B'
        : bytes < 1048576 ? (bytes / 1024).toFixed(1) + ' KB'
        : (bytes / 1048576).toFixed(1) + ' MB';

    // Dropdown terkunci (salinan Rencana) tetap dihitung: jenisnya ikut memakai kuota
    // "maks satu" walau tidak bisa diubah. Yang nonaktif karena ditandai hapus tidak.
    const semuaDropdown = () => Array.from(document.querySelectorAll('#dokumenLamaList select[data-jenis-dokumen], #dokumenBaruList select[data-jenis-dokumen]'))
        .filter(s => !s.disabled || s.dataset.terkunci);

    const beriTahu = pesan => window.mkNotify
        ? window.mkNotify({ title: 'Dokumen', message: pesan, variant: 'warning' })
        : window.alert(pesan);

    /** Jenis "maks satu" yang sudah dipakai satu baris dinonaktifkan di baris lain. */
    function perbaruiOpsi() {
        const daftar = semuaDropdown();
        daftar.forEach(sel => {
            Array.from(sel.options).forEach(opt => {
                if (!MAKS_SATU.includes(opt.value)) return;
                const dipakai = daftar.some(lain => lain !== sel && lain.value === opt.value);
                opt.disabled = dipakai;
                opt.textContent = LABEL[opt.value] + (dipakai ? ' (sudah dipilih)' : '');
            });
            sel.dispatchEvent(new CustomEvent('mk-select:refresh'));
        });
    }

    function pasangValidasi(sel) {
        sel.addEventListener('invalid', () => {
            sel.setCustomValidity(sel.value ? '' : 'Pilih jenis dokumen ini.');
            sel.closest('.mk-select')?.classList.add('mk-select--invalid');
        });
        sel.addEventListener('change', () => {
            sel.setCustomValidity('');
            sel.closest('.mk-select')?.classList.remove('mk-select--invalid');
            perbaruiOpsi();
        });
    }

    function sinkron() {
        const dt = new DataTransfer();
        baru.forEach(item => dt.items.add(item.file));
        input.files = dt.files;
    }

    function el(tag, cls, teks) {
        const node = document.createElement(tag);
        if (cls) node.className = cls;
        if (teks !== undefined) node.textContent = teks;
        return node;
    }

    function render() {
        baruList.innerHTML = '';
        baru.forEach((item, i) => {
            const ext = item.file.name.split('.').pop().toLowerCase();
            const row = el('div', 'doc-preview-item');

            const ikon = ICON[ext] || 'FILE';
            row.appendChild(el('span', 'doc-icon is-' + ikon.toLowerCase(), ikon));
            const info = el('div', 'doc-info');
            const nama = el('div', 'doc-name', item.file.name);
            nama.title = item.file.name;
            info.appendChild(nama);
            info.appendChild(el('div', 'doc-size', ukuran(item.file.size) + ' • ' + ext.toUpperCase()));
            row.appendChild(info);

            const slot = el('div', 'doc-jenis');
            slot.innerHTML = template.innerHTML.replaceAll('__N__', String(++nomor));
            const sel = slot.querySelector('select');
            sel.value = item.jenis;
            sel.addEventListener('change', () => { item.jenis = sel.value; });
            pasangValidasi(sel);
            row.appendChild(slot);

            const hapus = el('button', 'btn-remove-doc mk-btn mk-btn--secondary mk-btn--sm mk-btn--icon');
            hapus.type = 'button';
            hapus.title = 'Hapus dokumen';
            hapus.setAttribute('aria-label', 'Hapus dokumen ' + item.file.name);
            hapus.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
            hapus.addEventListener('click', () => {
                baru.splice(i, 1);
                sinkron();
                render();
            });
            const aksi = el('div', 'doc-aksi');
            aksi.appendChild(hapus);
            row.appendChild(aksi);

            baruList.appendChild(row);
        });
        perbaruiOpsi();
    }

    function tambah(files) {
        const sisaLama = lamaList
            ? Array.from(lamaList.querySelectorAll('select[data-jenis-dokumen]')).filter(s => !s.disabled || s.dataset.terkunci).length
            : 0;
        const ditolak = [];
        Array.from(files).forEach(file => {
            if (!DIIZINKAN.test(file.name)) {
                ditolak.push('"' + file.name + '" bukan PDF/DOC/XLS/PPT.');
            } else if (sisaLama + baru.length >= MAKS_DOKUMEN) {
                ditolak.push('Maksimal ' + MAKS_DOKUMEN + ' dokumen; "' + file.name + '" tidak ditambahkan.');
            } else {
                baru.push({ file: file, jenis: '' });
            }
        });
        sinkron();
        render();
        if (ditolak.length) beriTahu(ditolak.join(' '));
    }

    input.addEventListener('change', () => {
        // input.files dibangun ulang dari `baru`, jadi pilihan baru dibaca lebih dulu.
        const dipilih = Array.from(input.files).filter(f => !baru.some(item => item.file === f));
        tambah(dipilih);
    });

    area.addEventListener('click', () => input.click());
    area.addEventListener('keydown', e => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        e.preventDefault();
        input.click();
    });
    ['dragenter', 'dragover'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.remove('dragover'); });
    });
    area.addEventListener('drop', e => tambah(e.dataTransfer.files));

    // Dokumen tersimpan: dropdown-nya dinonaktifkan saat ditandai hapus, supaya tidak
    // ikut terkirim, tidak ikut divalidasi, dan jenisnya bebas dipakai baris lain.
    document.querySelectorAll('[data-hapus-dokumen]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.hapusDokumen;
            markFileForDeletion(Number(id));
            const sel = document.getElementById('jenisDokumenLama' + id);
            if (sel) {
                sel.disabled = true;
                sel.setCustomValidity('');
            }
            perbaruiOpsi();
        });
    });
    document.querySelectorAll('#dokumenLamaList select[data-jenis-dokumen]').forEach(pasangValidasi);

    // mkSelect baru membaca opsi saat Alpine aktif, jadi penandaan awal ditunda ke sana.
    document.addEventListener('alpine:initialized', perbaruiOpsi);
    if (window.Alpine) perbaruiOpsi();
})();
</script>
