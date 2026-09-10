<script>
// ── Char counter (judul & deskripsi) ──
function updateCharCount(inputId, countId, max) {
    const el  = document.getElementById(inputId);
    const cnt = document.getElementById(countId);
    if (!el || !cnt) return;
    const len = el.value.length;
    cnt.textContent = len;
    cnt.style.color = len >= max ? '#dc2626' : (len > max * 0.9 ? '#f59e0b' : '#666D80');
}
document.addEventListener('DOMContentLoaded', () => {
    ['judulInput','deskripsiInput'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.dispatchEvent(new Event('input'));
    });
});

// ── Banner Preview ──
function previewBanner(input) {
    const preview = document.getElementById('bannerPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Lightbox ──
function openLightbox(src) {
    const modal = document.getElementById('lightboxModal');
    const img = document.getElementById('lightboxImage');
    img.src = src;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // prevent scrolling
}

function closeLightbox(e) {
    if (e && e.target !== document.getElementById('lightboxModal') && e.target.tagName !== 'BUTTON') {
        // Allow clicking the image to do nothing, but clicking outside closes
        if (e.target.tagName === 'IMG') return;
    }
    const modal = document.getElementById('lightboxModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// ── Searchable Dropdown ──
function showDropdown(dropdownId) {
    document.getElementById(dropdownId).classList.add('show');
}

function filterOptions(inputId, dropdownId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    const dropdown = document.getElementById(dropdownId);
    const options = dropdown.querySelectorAll('.search-select-option');
    let hasVisible = false;

    options.forEach(opt => {
        const name = opt.getAttribute('data-name') || '';
        const secondary = opt.getAttribute('data-nim') || opt.getAttribute('data-nip') || '';
        const match = name.includes(query) || secondary.includes(query);
        opt.style.display = match ? 'block' : 'none';
        if (match) hasVisible = true;
    });

    dropdown.classList.toggle('show', hasVisible);
}

function selectOption(hiddenId, value, inputId, label, dropdownId) {
    document.getElementById(hiddenId).value = value;
    document.getElementById(inputId).value = label;
    document.getElementById(dropdownId).classList.remove('show');
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('.search-select-dropdown').forEach(d => {
        if (!d.parentElement.contains(e.target)) {
            d.classList.remove('show');
        }
    });
});

@if($showDokumentasi)
// ── File Deletion (existing files) ──
function markFileForDeletion(fileId) {
    const el = document.getElementById('existingFile' + fileId);
    if (el) {
        el.style.opacity = '0.3';
        el.style.pointerEvents = 'none';
    }
    const container = document.getElementById('deleteFileInputs');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'hapus_file[]';
    input.value = fileId;
    container.appendChild(input);
}

// ── Multi-File Upload: Foto ──
let fotoFiles = [];

function handleFotoSelect(input) {
    const newFiles = Array.from(input.files);
    newFiles.forEach(file => {
        if (fotoFiles.length >= 10) return;
        fotoFiles.push(file);
    });
    renderFotoPreviews();
    syncFotoInput();
}

function removeFoto(index) {
    fotoFiles.splice(index, 1);
    renderFotoPreviews();
    syncFotoInput();
}

function renderFotoPreviews() {
    const grid = document.getElementById('fotoPreviewGrid');
    grid.innerHTML = '';
    fotoFiles.forEach((file, i) => {
        const item = document.createElement('div');
        item.className = 'file-preview-item';
        const reader = new FileReader();
        reader.onload = function(e) {
            item.innerHTML = `
                <button type="button" class="btn-remove-file" onclick="removeFoto(${i})"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                <img src="${e.target.result}" alt="${file.name}" style="cursor: pointer;" onclick="openLightbox(this.src)" title="Klik untuk memperbesar">
                <div class="file-info">${file.name}<br><span class="file-size">${formatFileSize(file.size)}</span></div>
            `;
        };
        reader.readAsDataURL(file);
        grid.appendChild(item);
    });
}

function syncFotoInput() {
    const dt = new DataTransfer();
    fotoFiles.forEach(f => dt.items.add(f));
    document.getElementById('fotoInput').files = dt.files;
}

// ── Multi-File Upload: Dokumen ──
let dokumenFiles = [];

function handleDokumenSelect(input) {
    const newFiles = Array.from(input.files);
    newFiles.forEach(file => {
        if (dokumenFiles.length >= 10) return;
        dokumenFiles.push(file);
    });
    renderDokumenPreviews();
    syncDokumenInput();
}

function removeDokumen(index) {
    dokumenFiles.splice(index, 1);
    renderDokumenPreviews();
    syncDokumenInput();
}

function renderDokumenPreviews() {
    const list = document.getElementById('dokumenPreviewList');
    list.innerHTML = '';
    dokumenFiles.forEach((file, i) => {
        const ext = file.name.split('.').pop().toLowerCase();
        const icons = { pdf: 'PDF', doc: 'DOC', docx: 'DOC', xls: 'XLS', xlsx: 'XLS', ppt: 'PPT', pptx: 'PPT' };
        const icon = icons[ext] || '\u25A1';
        const item = document.createElement('div');
        item.className = 'doc-preview-item';
        item.innerHTML = `
            <span class="doc-icon">${icon}</span>
            <div class="doc-info">
                <div class="doc-name">${file.name}</div>
                <div class="doc-size">${formatFileSize(file.size)} • ${ext.toUpperCase()}</div>
            </div>
            <button type="button" class="btn-remove-doc" onclick="removeDokumen(${i})"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        `;
        list.appendChild(item);
    });
}

function syncDokumenInput() {
    const dt = new DataTransfer();
    dokumenFiles.forEach(f => dt.items.add(f));
    document.getElementById('dokumenInput').files = dt.files;
}

// ── Helper ──
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

// ── Drag & Drop ──
['fotoUploadArea', 'dokumenUploadArea'].forEach(id => {
    const area = document.getElementById(id);
    if (!area) return;
    ['dragenter', 'dragover'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.remove('dragover'); });
    });
    area.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (id === 'fotoUploadArea') {
            Array.from(files).forEach(f => { if (fotoFiles.length < 10 && f.type.startsWith('image/')) fotoFiles.push(f); });
            renderFotoPreviews();
            syncFotoInput();
        } else {
            Array.from(files).forEach(f => { if (dokumenFiles.length < 10) dokumenFiles.push(f); });
            renderDokumenPreviews();
            syncDokumenInput();
        }
    });
});
@endif

// ── Panitia Multi-Select ──
{{--
    Saat form dikembalikan karena validasi gagal, panitia & jabatannya WAJIB
    dipulihkan dari input terakhir user (old()), bukan dari database. Tanpa ini
    satu error kecil di field lain — mis. Kategori belum dicentang — membuat
    seluruh chip panitia beserta kolom jabatannya lenyap: di form Buat Proker
    jadi kosong total (controller mengirim koleksi kosong), sedangkan di form
    Edit diam-diam balik ke data lama sehingga perubahan panitia yang belum
    tersimpan ikut hilang.

    Dikerjakan di sini, bukan di controller, supaya Rencana Proker (create &
    edit) dan Pelaksanaan Kegiatan (edit) yang berbagi partial ini ikut pulih.
--}}
@php
    $panitiaIdsLama   = old('panitia_ids');
    $panitiaPeranLama = old('panitia_peran', []);
    // old() bernilai null hanya bila form dibuka normal (bukan hasil validasi gagal);
    // array kosong tetap dihormati karena artinya user memang menghapus semua panitia.
    $panitiaTerpilih  = $panitiaIdsLama !== null
        ? $mahasiswaList->whereIn('id', $panitiaIdsLama)
        : $existingPanitia;
@endphp
let selectedPanitia = {};
let initialRoles = {};

@foreach($panitiaTerpilih as $pan)
selectedPanitia['{{ $pan->id }}'] = '{{ addslashes($pan->user->name ?? '') }}';
initialRoles['{{ $pan->id }}'] = '{{ addslashes($panitiaPeranLama[$pan->id] ?? $pan->pivot->peran ?? '') }}';
@endforeach

function focusPanitiaSearch() {
    document.getElementById('panitiaSearchInput').focus();
}

function showPanitiaDropdown() {
    const dropdown = document.getElementById('panitiaDropdown');
    dropdown.classList.add('show');
    filterPanitiaOptions(document.getElementById('panitiaSearchInput').value);
}

function filterPanitiaOptions(query) {
    const q = query.toLowerCase().trim();
    const options = document.querySelectorAll('#panitiaDropdown .panitia-option');
    const empty = document.getElementById('panitiaEmpty');
    let visibleCount = 0;

    options.forEach(opt => {
        const name = opt.getAttribute('data-name-lower') || '';
        const nim  = opt.getAttribute('data-nim') || '';
        const match = !q || name.includes(q) || nim.includes(q);
        opt.style.display = match ? 'flex' : 'none';
        if (match) visibleCount++;
    });

    empty.style.display = visibleCount === 0 ? 'block' : 'none';
    document.getElementById('panitiaDropdown').classList.add('show');
}

function togglePanitia(optEl) {
    const id   = optEl.getAttribute('data-id');
    const name = optEl.getAttribute('data-name');

    if (selectedPanitia[id]) {
        removePanitia(id);
    } else {
        selectedPanitia[id] = name;
        optEl.classList.add('selected');
        renderPanitiaChips();
        updatePanitiaHiddenInputs();
    }

    document.getElementById('panitiaSearchInput').value = '';
    filterPanitiaOptions('');
    document.getElementById('panitiaSearchInput').focus();
}

function removePanitia(id) {
    delete selectedPanitia[id];
    const opt = document.querySelector(`#panitiaDropdown .panitia-option[data-id="${id}"]`);
    if (opt) opt.classList.remove('selected');
    renderPanitiaChips();
    updatePanitiaHiddenInputs();
}

function renderPanitiaChips() {
    const container = document.getElementById('panitiaChipsContainer');
    const searchInput = document.getElementById('panitiaSearchInput');

    container.querySelectorAll('.panitia-chip').forEach(c => c.remove());

    Object.entries(selectedPanitia).forEach(([id, name]) => {
        const chip = document.createElement('span');
        chip.className = 'panitia-chip';
        chip.innerHTML = `
            ${name}
            <button type="button" class="panitia-chip-remove" onclick="removePanitia('${id}')" title="Hapus">×</button>
        `;
        container.insertBefore(chip, searchInput);
    });

    const count = Object.keys(selectedPanitia).length;
    const badge = document.getElementById('panitiaCountBadge');
    if (count > 0) {
        badge.textContent = count + ' dipilih';
        badge.style.display = 'inline';
        document.getElementById('panitiaSearchInput').placeholder = 'Tambah lebih banyak...';
    } else {
        badge.style.display = 'none';
        document.getElementById('panitiaSearchInput').placeholder = 'Cari dan tambah panitia...';
    }
}

function updatePanitiaHiddenInputs() {
    const container = document.getElementById('panitiaHiddenInputs');
    const rolesContainer = document.getElementById('panitiaRolesContainer');

    container.innerHTML = '';

    const existingRoles = {};
    rolesContainer.querySelectorAll('input[type="text"]').forEach(input => {
        existingRoles[input.dataset.id] = input.value;
    });

    rolesContainer.innerHTML = '';

    Object.keys(selectedPanitia).forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'panitia_ids[]';
        input.value = id;
        container.appendChild(input);

        const name = selectedPanitia[id];
        const currentRole = existingRoles[id] !== undefined ? existingRoles[id] : (initialRoles[id] || '');

        const roleDiv = document.createElement('div');
        roleDiv.className = 'd-flex align-items-center gap-3 p-2 border rounded bg-light';
        roleDiv.innerHTML = `
            <div style="flex: 1; font-size: 13px; font-weight: 600; color: #374151;">${name}</div>
            <div style="flex: 2;">
                <input type="text" name="panitia_peran[${id}]" data-id="${id}" class="form-control form-control-sm" placeholder="Masukkan Jabatan (misal: Sekretaris, Bendahara, dll)" value="${currentRole}">
            </div>
        `;
        rolesContainer.appendChild(roleDiv);
    });
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('panitiaSelectWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('panitiaDropdown').classList.remove('show');
    }
});

// ── Dosen Pendamping Multi-Select ──
let selectedDosen = {}; // { id: name }

// Pre-populate dari data yang ada di database (atau input sebelumnya bila gagal validasi)
@foreach($existingDosen as $d)
selectedDosen['{{ $d->id }}'] = '{{ addslashes($d->user->name ?? '') }}';
@endforeach

function focusDosenSearch() {
    document.getElementById('dosenSearchInput').focus();
}

function showDosenDropdown() {
    document.getElementById('dosenDropdown').classList.add('show');
    filterDosenOptions(document.getElementById('dosenSearchInput').value);
}

function filterDosenOptions(query) {
    const q = query.toLowerCase().trim();
    const options = document.querySelectorAll('#dosenDropdown .panitia-option');
    const empty = document.getElementById('dosenEmpty');
    let visibleCount = 0;

    options.forEach(opt => {
        const name = opt.getAttribute('data-name-lower') || '';
        const nip  = opt.getAttribute('data-nip') || '';
        const match = !q || name.includes(q) || nip.includes(q);
        opt.style.display = match ? 'flex' : 'none';
        if (match) visibleCount++;
    });

    empty.style.display = visibleCount === 0 ? 'block' : 'none';
    document.getElementById('dosenDropdown').classList.add('show');
}

function toggleDosen(optEl) {
    const id   = optEl.getAttribute('data-id');
    const name = optEl.getAttribute('data-name');

    if (selectedDosen[id]) {
        removeDosen(id);
    } else {
        selectedDosen[id] = name;
        optEl.classList.add('selected');
        renderDosenChips();
        updateDosenHiddenInputs();
    }

    // Reset pencarian
    document.getElementById('dosenSearchInput').value = '';
    filterDosenOptions('');
    document.getElementById('dosenSearchInput').focus();
}

function removeDosen(id) {
    delete selectedDosen[id];
    const opt = document.querySelector(`#dosenDropdown .panitia-option[data-id="${id}"]`);
    if (opt) opt.classList.remove('selected');
    renderDosenChips();
    updateDosenHiddenInputs();
}

function renderDosenChips() {
    const container   = document.getElementById('dosenChipsContainer');
    const searchInput = document.getElementById('dosenSearchInput');

    container.querySelectorAll('.panitia-chip').forEach(c => c.remove());

    Object.entries(selectedDosen).forEach(([id, name]) => {
        const chip = document.createElement('span');
        chip.className = 'panitia-chip';
        chip.innerHTML = `
            ${name}
            <button type="button" class="panitia-chip-remove" onclick="removeDosen('${id}')" title="Hapus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        `;
        container.insertBefore(chip, searchInput);
    });

    const count = Object.keys(selectedDosen).length;
    const badge = document.getElementById('dosenCountBadge');
    if (count > 0) {
        badge.textContent = count + ' dipilih';
        badge.style.display = 'inline';
        searchInput.placeholder = 'Tambah dosen lain...';
    } else {
        badge.style.display = 'none';
        searchInput.placeholder = 'Cari dan tambah dosen pendamping...';
    }
}

function updateDosenHiddenInputs() {
    const container = document.getElementById('dosenHiddenInputs');
    container.innerHTML = '';

    Object.keys(selectedDosen).forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'dosen_pendamping_ids[]';
        input.value = id;
        container.appendChild(input);
    });
}

// Tutup dropdown dosen saat klik di luar
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('dosenSelectWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('dosenDropdown').classList.remove('show');
    }
});

// Render chips dosen saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    Object.keys(selectedDosen).forEach(id => {
        const opt = document.querySelector(`#dosenDropdown .panitia-option[data-id="${id}"]`);
        if (opt) opt.classList.add('selected');
    });
    renderDosenChips();
    updateDosenHiddenInputs();
});

// ── Toggle Bidang Field based on Kategori (checkbox version) ──
function handleKategoriChange() {
    const checkboxes = document.querySelectorAll('#kategoriGroup input[type="checkbox"]');
    const checked = document.querySelectorAll('#kategoriGroup input[type="checkbox"]:checked');
    const maxKategori = 2;

    checkboxes.forEach(cb => {
        const card = cb.closest('.checkbox-card');
        if (cb.checked) {
            card.classList.add('checked');
        } else {
            card.classList.remove('checked');
        }
    });

    if (checked.length >= maxKategori) {
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                cb.closest('.checkbox-card').classList.add('disabled');
                cb.disabled = true;
            }
        });
    } else {
        checkboxes.forEach(cb => {
            cb.closest('.checkbox-card').classList.remove('disabled');
            cb.disabled = false;
        });
    }

    toggleBidangField();
}

function toggleBidangField() {
    const checked = document.querySelectorAll('#kategoriGroup input[type="checkbox"]:checked');
    const bidangRequired = document.getElementById('bidangRequired');
    const bidangWrapper  = document.getElementById('bidangFieldWrapper');

    // "Hanya Prodi" = ada kategori dicentang DAN semuanya berflag prodi
    let allProdi = checked.length > 0;
    checked.forEach(cb => {
        if (cb.getAttribute('data-is-prodi') !== '1') {
            allProdi = false;
        }
    });
    const isOnlyProdi = allProdi && checked.length > 0;

    if (bidangRequired) bidangRequired.style.display = isOnlyProdi ? 'none' : '';

    // Sembunyikan SELURUH kolom Bidang saat hanya Kegiatan Prodi yang dipilih.
    // Saat disembunyikan, lepas centang bidang agar tidak ikut tersimpan.
    if (bidangWrapper) {
        if (isOnlyProdi) {
            bidangWrapper.style.display = 'none';
            document.querySelectorAll('#bidangGroup input[type="checkbox"]').forEach(inp => {
                if (inp.checked) {
                    inp.checked = false;
                    inp.closest('.checkbox-card').classList.remove('checked');
                }
            });
        } else {
            bidangWrapper.style.display = '';
        }
    }
}

// ── Initialize on page load ──
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.checkbox-card input[type="checkbox"]:checked').forEach(cb => {
        cb.closest('.checkbox-card').classList.add('checked');
    });

    document.querySelectorAll('#bidangGroup input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                this.closest('.checkbox-card').classList.add('checked');
            } else {
                this.closest('.checkbox-card').classList.remove('checked');
            }
        });
    });

    handleKategoriChange();

    // Pre-populate panitia chips dan hidden inputs dari data existing
    Object.keys(selectedPanitia).forEach(id => {
        const opt = document.querySelector(`#panitiaDropdown .panitia-option[data-id="${id}"]`);
        if (opt) opt.classList.add('selected');
    });
    renderPanitiaChips();
    updatePanitiaHiddenInputs();
});
</script>
