{{--
    Bagian "Akses Kelola" — siapa saja, selain pemilik, yang boleh mengubah
    kegiatan ini. Dipakai bersama:
      • Rencana Proker (create & edit) dan Pelaksanaan (edit), lewat _fields.blade.php
      • Laporan & Arsip (create & edit), yang punya form sendiri

    Hanya dirender untuk pemilik kegiatan & override (KegiatanPolicy::aturAkses).
    Pengelola biasa tidak melihatnya, dan server mengabaikan input ini darinya
    (PengelolaKegiatanService::sync).

    Variabel dari PengelolaKegiatanService::dataForm(): $bolehAturAkses,
    $calonPengelola, $pengelolaTerpilih, $namaPembuat.

    Skripnya sengaja berdiri sendiri — ID & nama fungsi berawalan "pengelola" —
    karena form Arsip tidak memakai _scripts.blade.php. Tampilannya meminjam
    kelas .panitia-* yang tersedia di kedua jenis form.
--}}
@if($bolehAturAkses ?? false)
@php
    // Pengelola yang sudah tidak memegang role pengurus tidak ada di daftar calon,
    // jadi ikut terlepas saat form ini disimpan — route pun sudah menolaknya.
    $pengelolaAwal = $calonPengelola
        ->filter(fn($calon) => in_array($calon['id'], $pengelolaTerpilih, true))
        ->values();
@endphp
<div class="form-card">
    <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> Akses Kelola</div>

    <input type="hidden" name="akses_kelola_dikirim" value="1">

    <p style="font-size: 13px; color: #666D80; margin-bottom: 14px;">
        Pembuat: <strong style="color: #374151;">{{ $namaPembuat }}</strong>.
        Admin dan Ketua Himpunan otomatis bisa mengelola semua kegiatan.
        Tambahkan pengurus lain yang boleh ikut mengubah kegiatan ini.
    </p>

    <label class="form-label-custom">
        Pengelola
        <span style="color: #666D80; font-weight: 400;">(opsional)</span>
    </label>
    <div class="panitia-select-wrapper" id="pengelolaSelectWrapper">
        <div class="panitia-chips-container" id="pengelolaChipsContainer"
             onclick="document.getElementById('pengelolaSearchInput').focus()">
            <input type="text" class="panitia-search-input" id="pengelolaSearchInput"
                   placeholder="Cari pengurus..."
                   autocomplete="off"
                   oninput="filterPengelolaOptions(this.value)"
                   onfocus="showPengelolaDropdown()">
        </div>
        <div class="panitia-dropdown" id="pengelolaDropdown">
            @foreach($calonPengelola as $calon)
                <div class="panitia-option"
                     data-id="{{ $calon['id'] }}"
                     data-name="{{ $calon['nama'] }}"
                     data-name-lower="{{ strtolower($calon['nama']) }}"
                     data-role="{{ $calon['role'] }}"
                     data-bisa-hapus="{{ $calon['bisa_hapus'] ? '1' : '0' }}"
                     onclick="togglePengelola(this)">
                    <div>
                        {{ $calon['nama'] }}
                        <div class="sub-text">{{ $calon['role'] }}</div>
                    </div>
                    <span class="check-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
            @endforeach
            <div class="panitia-empty" id="pengelolaEmpty" style="display:none;">Tidak ada pengurus yang cocok</div>
        </div>
    </div>
    <div class="checkbox-hint">Haknya mengikuti jabatan: Ketua Bidang/Unit yang ditambahkan langsung bisa mengedit sekaligus menghapus, Staff Himpunan hanya bisa mengedit.</div>

    {{-- Baris hak per pengelola + hidden input, di-generate JS --}}
    <div id="pengelolaRolesContainer" class="mt-3 d-flex flex-column gap-2"></div>
</div>

<script>
// ── Akses Kelola: pilih pengurus + hak masing-masing ──
const pengelolaAwal = @json($pengelolaAwal);
let pengelolaTerpilih = {}; // { id: {id, nama, role, bisa_hapus} }
pengelolaAwal.forEach(p => { pengelolaTerpilih[p.id] = p; });

function showPengelolaDropdown() {
    document.getElementById('pengelolaDropdown').classList.add('show');
    filterPengelolaOptions(document.getElementById('pengelolaSearchInput').value);
}

function filterPengelolaOptions(query) {
    const q = query.toLowerCase().trim();
    let terlihat = 0;

    document.querySelectorAll('#pengelolaDropdown .panitia-option').forEach(opt => {
        const cocok = !q || (opt.dataset.nameLower || '').includes(q);
        opt.style.display = cocok ? 'flex' : 'none';
        if (cocok) terlihat++;
    });

    document.getElementById('pengelolaEmpty').style.display = terlihat === 0 ? 'block' : 'none';
    document.getElementById('pengelolaDropdown').classList.add('show');
}

function togglePengelola(opt) {
    const id = opt.dataset.id;

    if (pengelolaTerpilih[id]) {
        delete pengelolaTerpilih[id];
    } else {
        pengelolaTerpilih[id] = {
            id: Number(id),
            nama: opt.dataset.name,
            role: opt.dataset.role,
            bisa_hapus: opt.dataset.bisaHapus === '1',
        };
    }
    renderPengelola();

    const input = document.getElementById('pengelolaSearchInput');
    input.value = '';
    filterPengelolaOptions('');
    input.focus();
}

function hapusPengelola(id) {
    delete pengelolaTerpilih[id];
    renderPengelola();
}

// Nama diisi lewat textContent, bukan innerHTML, supaya nama pengguna tidak
// pernah ditafsirkan sebagai HTML.
function renderPengelola() {
    const chips = document.getElementById('pengelolaChipsContainer');
    const input = document.getElementById('pengelolaSearchInput');
    const baris = document.getElementById('pengelolaRolesContainer');

    chips.querySelectorAll('.panitia-chip').forEach(c => c.remove());
    baris.innerHTML = '';

    document.querySelectorAll('#pengelolaDropdown .panitia-option').forEach(opt => {
        opt.classList.toggle('selected', !!pengelolaTerpilih[opt.dataset.id]);
    });

    Object.values(pengelolaTerpilih).forEach(p => {
        const chip = document.createElement('span');
        chip.className = 'panitia-chip';
        chip.appendChild(document.createTextNode(p.nama + ' '));
        const lepas = document.createElement('button');
        lepas.type = 'button';
        lepas.className = 'panitia-chip-remove';
        lepas.title = 'Hapus';
        lepas.textContent = '×';
        lepas.addEventListener('click', e => { e.stopPropagation(); hapusPengelola(String(p.id)); });
        chip.appendChild(lepas);
        chips.insertBefore(chip, input);

        const row = document.createElement('div');
        row.className = 'd-flex align-items-center gap-3 p-2 border rounded bg-light';

        const nama = document.createElement('div');
        nama.style.cssText = 'flex: 1; font-size: 13px; font-weight: 600; color: #374151;';
        nama.textContent = p.nama;
        const role = document.createElement('div');
        role.style.cssText = 'font-size: 11px; font-weight: 500; color: #666D80;';
        role.textContent = p.role;
        nama.appendChild(role);

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'pengelola_ids[]';
        idInput.value = p.id;

        // Bukan pilihan, melainkan keterangan: hak hapus melekat pada jabatan.
        // Yang menegakkannya KegiatanPolicy::delete, bukan kiriman form ini.
        const hak = document.createElement('span');
        hak.textContent = p.bisa_hapus ? 'Edit & hapus' : 'Boleh edit';
        hak.title = p.bisa_hapus
            ? 'Ketua Bidang/Unit yang ditambahkan otomatis bisa mengedit sekaligus menghapus kegiatan ini'
            : 'Staff Himpunan hanya bisa mengedit, tidak bisa menghapus';
        hak.style.cssText = 'flex: none; font-size: 11px; font-weight: 600; padding: 4px 10px;'
            + 'border-radius: 999px; border: 1px solid; white-space: nowrap;'
            + (p.bisa_hapus
                ? 'background:#FEF2F2; color:#DC2626; border-color:#FECACA;'
                : 'background:#F1F5F9; color:#475569; border-color:#E2E8F0;');

        row.append(nama, hak, idInput);
        baris.appendChild(row);
    });

    input.placeholder = Object.keys(pengelolaTerpilih).length ? 'Tambah pengurus lain...' : 'Cari pengurus...';
}

document.addEventListener('click', function (e) {
    const wrapper = document.getElementById('pengelolaSelectWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('pengelolaDropdown').classList.remove('show');
    }
});

renderPengelola();
</script>
@endif
