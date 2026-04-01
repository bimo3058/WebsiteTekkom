<x-banksoal::layouts.master>
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-university"></i></div>
        <div class="brand-text">
            <strong>Departemen Teknik Komputer</strong>
            <span>Universitas Wakamsi</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('banksoal.dashboard') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Home</a>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="nav-item active"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Manajemen RPS</a>
        <a href="{{ route('banksoal.soal.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-database"></i></span> Bank Soal</a>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-archive"></i></span> Arsip Soal</a>
    </nav>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="topbar-btn" title="Logout" style="background: none; border: none; padding: 0; cursor: pointer; color: inherit; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-cog"></i>
        </button>
    </form>
    <button class="topbar-btn notif-btn"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
    <div class="user-chip">
        <div class="user-avatar-chip">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->lecturer?->employee_number }}</span>
        </div>
    </div>
</header>

<!-- MAIN -->
<main class="main">

{{-- Flash Messages Snackbar --}}
@if ($message = Session::get('success'))
    <div class="snackbar snackbar-success" role="alert">
        <i class="fas fa-check-circle"></i>
        <span>{{ $message }}</span>
        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="snackbar snackbar-error" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $message }}</span>
        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if ($errors->any())
    <div class="snackbar snackbar-error" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <span style="display: block; margin-bottom: 8px;">Validasi gagal:</span>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
    </div>
@endif

<div class="page-header">
    <h1>Manajemen RPS</h1>
    <p>Lengkapi data rencana pembelajaran semester dan unggah dokumen pendukung.</p>
</div>

{{-- STATUS BANNER DUMMY--}}
<div class="status-bar">
    <div class="status-left">
        <i class="fas fa-exclamation-circle status-icon not-uploaded"></i>
        <div>
            <div class="status-label">Status: Belum Diunggah</div>
            <div class="status-desc">
                Batas akhir pengunggahan RPS untuk Semester Ganjil 2025/2026 adalah 30 Agustus 2025.
            </div>
        </div>
    </div>
    <a href="#" class="panduan-btn">
        <i class="fas fa-circle-question"></i> Panduan Pengisian
    </a>
</div>

{{-- FORM CARD --}}
<div class="form-card">
    <div class="form-card-title">Formulir Rencana Pembelajaran</div>

    <form action="{{ route('banksoal.rps.dosen.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid-2">
            {{-- Mata Kuliah --}}
            <div class="select-wrap">
                <label class="form-label">Mata Kuliah <span style="color: red;">*</span></label>
                <select name="mata_kuliah_id" id="mkSelect" class="form-control" required>
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    @foreach ($mataKuliahs as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)</option>
                    @endforeach
                </select>
            </div>

            {{-- Dosen Pengampu Lain — Multi-select --}}
            <div class="select-wrap">
                <label class="form-label">Dosen Pengampu Lain</label>
                <div id="dosenMs" class="ms-wrapper"
                     data-name="dosen_lain[]"
                     data-placeholder="Pilih mata kuliah terlebih dahulu"
                     data-disabled="true"></div>
                <small class="form-hint">Pilih satu atau lebih dosen pengampu tambahan.</small>
            </div>

            {{-- Semester --}}
            <div class="select-wrap">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-control">
                    <option value="Ganjil" selected>Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>

            {{-- Tahun Ajaran --}}
            <div class="select-wrap">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-control">
                    <option value="2023/2024">2023/2024</option>
                    <option value="2024/2025">2024/2025</option>
                    <option value="2025/2026" selected>2025/2026</option>
                </select>
            </div>

            {{-- CPL — Multi-select --}}
            <div class="select-wrap full">
                <label class="form-label">Capaian Pembelajaran Lulusan (CPL) <span style="color: red;">*</span></label>
                <div id="cplMs" class="ms-wrapper"
                     data-name="cpl_ids[]"
                     data-placeholder="Pilih mata kuliah terlebih dahulu"
                     data-disabled="true"></div>
                <small class="form-hint">CPL akan tersedia setelah mata kuliah dipilih.</small>
            </div>

            {{-- CPMK — Multi-select --}}
            <div class="select-wrap full">
                <label class="form-label">Capaian Pembelajaran Mata Kuliah (CPMK) <span style="color: red;">*</span></label>
                <div id="cpmkMs" class="ms-wrapper"
                     data-name="cpmk_ids[]"
                     data-placeholder="Pilih CPL terlebih dahulu"
                     data-disabled="true"></div>
                <small class="form-hint">CPMK akan tersedia setelah CPL dipilih.</small>
            </div>

            {{-- Upload --}}
            <div class="form-group full">
                <label class="form-label">Dokumen RPS <span style="color: red;">*</span></label>
                <label class="upload-zone" id="uploadZone">
                    <input type="file" name="dokumen" accept=".pdf,.docx" id="fileInput" required>
                    <i class="fas fa-cloud-upload-alt" id="uploadIcon"></i>
                    <strong id="uploadText">Klik untuk unggah atau seret file ke sini</strong>
                    <span id="uploadSub">PDF, DOCX (Maks. 5MB)</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="history.back()">Batal</button>
            <button type="submit" class="btn-primary" id="submitBtn">
                <i class="fas fa-floppy-disk"></i> Simpan RPS
            </button>
        </div>
    </form>
</div>

{{-- HISTORY TABLE --}}
<div class="history-card">
    <div class="history-title">Riwayat Pengunggahan</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tahun/Semester</th>
                    <th>Mata Kuliah</th>
                    <th>Tanggal Unggah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                    <tr>
                        <td>{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>
                        <td>{{ $item->mataKuliah?->nama ?? 'N/A' }} ({{ $item->mataKuliah?->kode ?? 'N/A' }})</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $item->status->badgeClass() }}">
                                {{ $item->status->label() }}
                            </span>
                        </td>
                        <td>
                            @if ($item->dokumen)
                                <a href="javascript:void(0)" onclick="previewDokumen({{ $item->id }}, '{{ $item->mataKuliah?->nama ?? 'Dokumen' }}', '{{ $item->dokumen }}')" class="action-link">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999; padding: 20px;">
                            <i class="fas fa-inbox"></i> Belum ada riwayat pengunggahan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL PREVIEW DOKUMEN -->
<div id="dokumenModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 10px; width: 90%; max-width: 860px; height: 88vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <!-- Header Modal -->
        <div style="padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; flex-shrink: 0;">
            <h3 id="modalTitle" style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Preview Dokumen</h3>
            <button onclick="closeDokumenModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;">×</button>
        </div>
        
        <!-- Content Modal -->
        <div style="flex: 1; overflow: hidden; min-height: 0;">
            <!-- Iframe untuk PDF/Image -->
            <iframe id="dokumenFrame" style="width: 100%; height: 100%; border: none; display: none;"></iframe>
            <!-- Embed untuk OnlyOffice (DOCX) -->
            <div id="dokumenEmbed" style="width: 100%; height: 100%; display: none;"></div>
        </div>
        
        <!-- Footer Modal -->
        <div style="padding: 12px 20px; border-top: 1px solid #eee; text-align: right; background: #f8f9fa; flex-shrink: 0;">
            <button onclick="closeDokumenModal()" style="padding: 10px 24px; background: #007bff; border: none; border-radius: 6px; cursor: pointer; color: white; font-weight: 500; font-size: 14px; transition: all 0.2s;">Tutup</button>
        </div>
    </div>
</div>

</main>

<script>
class MultiSelect {
    constructor(wrapper, options = {}) {
        this.wrapper     = wrapper;
        this.name        = wrapper.dataset.name;
        this.placeholder = wrapper.dataset.placeholder || 'Pilih…';
        this.disabled    = wrapper.dataset.disabled === 'true';
        this.items       = [];
        this.open        = false;
        this.searchVal   = '';
        this.maxWidth  = options.maxWidth|| null;    
        this.hasTooltip  = options.hasTooltip !== false;      
        this.keepOpen    = options.keepOpen === true;          

        this._build();
        this._bindEvents();
    }

    _build() {
        this.wrapper.innerHTML = `
            <div class="ms-trigger${this.disabled ? ' disabled' : ''}">
                <span class="ms-placeholder">${this.placeholder}</span>
                <i class="fas fa-chevron-down ms-chevron"></i>
            </div>
            <div class="ms-dropdown">
                <div class="ms-option-list"></div>
            </div>
        `;
        this.trigger  = this.wrapper.querySelector('.ms-trigger');
        this.dropdown = this.wrapper.querySelector('.ms-dropdown');
        this.list     = this.wrapper.querySelector('.ms-option-list');
    }

    _bindEvents() {
        this.trigger.addEventListener('click', (e) => {
            if (this.disabled) return;
            if (e.target.classList.contains('ms-badge-clear')) return;
            this._toggle();
        });
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) this._close();
        });
    }

    _toggle() { this.open ? this._close() : this._openDropdown(); }

    _openDropdown() {
        this.open = true;
        this.trigger.classList.add('open');
        this.dropdown.classList.add('open');

        const ph = this.trigger.querySelector('.ms-placeholder, .ms-selected-labels');
        if (ph) ph.remove();

        if (!this.trigger.querySelector('.ms-search-input')) {
            const inp = document.createElement('input');
            inp.className    = 'ms-search-input';
            inp.placeholder  = 'Cari…';
            inp.autocomplete = 'off';
            this.trigger.insertBefore(inp, this.trigger.querySelector('.ms-chevron'));
            inp.addEventListener('input', () => { this.searchVal = inp.value; this._renderList(); });
            inp.focus();
        }
        this._renderList();
    }

    _forceClose() {
        this.open = false;
        this.trigger.classList.remove('open');
        this.dropdown.classList.remove('open');
        const inp = this.trigger.querySelector('.ms-search-input');
        if (inp) inp.remove();
        this.searchVal = '';
    }

    _close() {
        if (!this.open) return;
        this._forceClose();
        this._renderTrigger();
    }

    _measureText(text) {
    if (!this._canvas) {
        this._canvas = document.createElement('canvas');
        this._ctx    = this._canvas.getContext('2d');
    }
    const style  = window.getComputedStyle(this.trigger);
    this._ctx.font = `${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;
    return this._ctx.measureText(text).width;
}

_truncateToWidth(text, maxPx) {
    if (this._measureText(text) <= maxPx) return text;
    let truncated = '';
    for (let i = 0; i < text.length; i++) {
        const test = text.slice(0, i + 1) + '…';
        if (this._measureText(test) > maxPx) break;
        truncated = text.slice(0, i + 1);
    }
    return truncated + '…';
}

    _renderTrigger() {
        if (!this.trigger.querySelector('.ms-chevron')) {
            const ch = document.createElement('i');
            ch.className = 'fas fa-chevron-down ms-chevron';
            this.trigger.appendChild(ch);
        }
        const chevron = this.trigger.querySelector('.ms-chevron');

        ['ms-badge', 'ms-placeholder', 'ms-selected-labels'].forEach(cls => {
            this.trigger.querySelectorAll('.' + cls).forEach(el => el.remove());
        });

        if (this.open) return;

        const selected = this.items.filter(i => i.selected);

        if (!selected.length) {
            const ph = document.createElement('span');
            ph.className   = 'ms-placeholder';
            ph.textContent = this.placeholder;
            this.trigger.insertBefore(ph, chevron);
        } else {
            const labelsEl   = document.createElement('span');
            labelsEl.className = 'ms-selected-labels';
            labelsEl.style.minWidth = '0';

            const allLabels  = selected.map(i => i.label).join(', ');
            
            if (this.maxWidth) {
                const reserved = 52 + 24 + 16;
                const available = this.maxWidth - reserved;
                labelsEl.textContent = this._truncateToWidth(allLabels, available);
            } else {
                labelsEl.textContent = allLabels;
            }

            this.trigger.insertBefore(labelsEl, chevron);

            // Badge & tooltip tetap sama
            const badgeEl = document.createElement('span');
            badgeEl.className = 'ms-badge';
            if (this.hasTooltip) {
                badgeEl.setAttribute('data-tooltip', allLabels);
            }
            badgeEl.innerHTML = `${selected.length} <button type="button" class="ms-badge-clear" title="Hapus semua">×</button>`;
            this.trigger.insertBefore(badgeEl, chevron);
            badgeEl.querySelector('.ms-badge-clear').addEventListener('click', (e) => {
                e.stopPropagation();
                this.items.forEach(i => i.selected = false);
                this._renderTrigger();
                this._syncHidden();
                this._emitChange();
            });
        }
    }

    _renderList() {
        const query    = this.searchVal.toLowerCase();
        const filtered = this.items.filter(i => !query || i.label.toLowerCase().includes(query));

        const _state = () => {
            const sel    = this.items.filter(i => i.selected);
            const allSel = sel.length === this.items.length && this.items.length > 0;
            const someSel = sel.length > 0 && !allSel;
            return { sel, allSel, someSel };
        };

        const { sel: selected, allSel, someSel } = _state();

        const sorted = query ? filtered : [
            ...filtered.filter(i => i.selected),
            ...filtered.filter(i => !i.selected),
        ];

        const saClass = allSel ? 'selected' : (someSel ? 'indeterminate' : '');
        let html = `<div class="ms-option select-all ${saClass}" data-action="select-all">
                        <span class="ms-cb"></span><span>Pilih Semua</span>
                    </div>`;

        if (!sorted.length) {
            html += `<div class="ms-no-results">Tidak ada hasil</div>`;
        } else {
            sorted.forEach(item => {
                html += `<div class="ms-option ${item.selected ? 'selected' : ''}" data-id="${item.id}">
                            <span class="ms-cb"></span><span>${item.label}</span>
                         </div>`;
            });
        }

        this.list.innerHTML = html;

        this.list.querySelectorAll('.ms-option').forEach(el => {
            el.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent event bubbling yang bisa trigger document click listener
                
                if (el.dataset.action === 'select-all') {
                    const currentState = _state();
                    this.items.forEach(i => i.selected = !currentState.allSel);
                } else {
                    const id = parseInt(el.dataset.id);
                    const item = this.items.find(i => i.id === id);
                    if (item) item.selected = !item.selected;
                }

                this._renderList();
                this._syncHidden();
                this._emitChange();
                this._syncTriggerBadge();
                
                // Keep dropdown open if configured - IMPORTANT: jangan close jika keepOpen aktif
                if (!this.keepOpen) {
                    this._close();
                }
            });
        });
    }

    _syncTriggerBadge() {
        const selNow  = this.items.filter(i => i.selected);
        const chevron = this.trigger.querySelector('.ms-chevron');
        const inp     = this.trigger.querySelector('.ms-search-input');
        let badge     = this.trigger.querySelector('.ms-badge');

        if (selNow.length) {
            if (badge) {
                if (this.isDosenField) {
                    const remaining = selNow.length - 1;
                    if (remaining > 0) {
                        badge.childNodes[0].textContent = '+' + remaining + ' ';
                    } else {
                        badge.childNodes[0].textContent = '1 ';
                    }
                } else {
                    badge.childNodes[0].textContent = selNow.length + ' ';
                }
            } else {
                badge = document.createElement('span');
                badge.className = 'ms-badge';
                const count = this.isDosenField ? (selNow.length > 1 ? '+' + (selNow.length - 1) : '1') : selNow.length;
                badge.innerHTML = `${count} <button type="button" class="ms-badge-clear" title="Hapus semua">×</button>`;
                this.trigger.insertBefore(badge, inp || chevron);
                badge.querySelector('.ms-badge-clear').addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.items.forEach(i => i.selected = false);
                    this._renderTrigger();
                    this._syncHidden();
                    this._emitChange();
                    this._syncTriggerBadge();
                });
            }
        } else {
            if (badge) badge.remove();
        }
    }

    _syncHidden() {
        this.wrapper.querySelectorAll('input[type="hidden"]').forEach(el => el.remove());
        this.items.filter(i => i.selected).forEach(i => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = this.name;
            inp.value = i.id;
            this.wrapper.appendChild(inp);
        });
    }

    setItems(items, readyPlaceholder) {
        this.items       = items.map(i => ({ ...i, selected: false }));
        this.placeholder = readyPlaceholder || this.placeholder;
        this.disabled    = false;
        this._forceClose();
        this.trigger.classList.remove('disabled');
        this._renderTrigger();
    }

    setLoading(msg = 'Memuat…') {
        this.items       = [];
        this.placeholder = msg;
        this.disabled    = true;
        this._forceClose();
        this.trigger.classList.add('disabled');
        this._renderTrigger();
    }

    setDisabled(placeholder) {
        this.items       = [];
        this.placeholder = placeholder;
        this.disabled    = true;
        this._forceClose();
        this.trigger.classList.add('disabled');
        this._renderTrigger();
    }

    getSelected() { return this.items.filter(i => i.selected).map(i => i.id); }

    _emitChange() {
        this.wrapper.dispatchEvent(new CustomEvent('ms:change', { detail: this.getSelected(), bubbles: true }));
    }
}

/**
 * ═══════════════════════════════════════════════════════════════
 * DOCUMENT PREVIEW MODAL - Functions & Event Handlers
 * ═══════════════════════════════════════════════════════════════
 * 
 * Handles document preview di modal dengan mendukung:
 * - PDF: Render via iframe (inline HTML5 PDF viewer)
 * - Office files (.docx, .xlsx, .pptx): Render via OnlyOffice Viewer
 * 
 * Features:
 * - Auto-detect file format dari extension
 * - Generate publicly accessible URLs dari storage
 * - Handle modal lifecycle: open, preview, close
 * - Keyboard shortcuts: ESC untuk close
 * - Clicking outside modal juga close
 */

/**
 * Logic:
 * 1. Ambil file extension dari dokumenPath
 * 2. Jika Office file (.docx/.xlsx/.pptx):
 *    - Generate public URL dari storage path
 *    - Encode URL untuk safety
 *    - Embed via OnlyOffice Viewer (cloud-based, needs internet)
 * 3. Jika PDF atau format lain:
 *    - Use native iframe dengan server preview endpoint
 *    - Controller will serve file inline (tidak download)
 * 4. Show modal dan prevent body scroll
 */
function previewDokumen(rpsId, mkNama, dokumenPath) {
    const modal = document.getElementById('dokumenModal');
    const iframe = document.getElementById('dokumenFrame');
    const embed = document.getElementById('dokumenEmbed');
    const title = document.getElementById('modalTitle');
    
    // Build preview URL dengan rpsId untuk fetch dari server
    const baseUrl = '{{ route("banksoal.rps.dosen.preview", ["rpsId" => "ID_PLACEHOLDER"]) }}';
    const previewUrl = baseUrl.replace('ID_PLACEHOLDER', rpsId);
    
    // Extract file extension untuk determine preview method
    const fileExt = dokumenPath.split('.').pop().toLowerCase();
    
    // Generate public URL dari storage disk 'bank-soal' dengan format: https://app.local/storage/bank-soal/rps/RPS_xxx.pdf
    const appUrl = '{{ rtrim(env("APP_URL", "http://localhost"), "/") }}';
    const publicFileUrl = appUrl + '/storage/bank-soal/' + dokumenPath;
    
    title.textContent = `Preview: ${mkNama}`;
    
    if (fileExt === 'docx' || fileExt === 'xlsx' || fileExt === 'pptx') {
        // ────── Office Documents: OnlyOffice Viewer ─────────
        // Uses cloud service: https://view.officeapps.live.com
        // Kelebihan: Smooth rendering, good compatibility
        // Kekurangan: Koneksi internet
        
        const encodedUrl = encodeURIComponent(publicFileUrl);
        iframe.style.display = 'none';
        embed.style.display = 'block';
        embed.innerHTML = `<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}" 
                            style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>`;
    } else {
        // Iframe untuk PDF dan format lain
        iframe.style.display = 'block';
        embed.style.display = 'none';
        embed.innerHTML = '';
        iframe.src = previewUrl;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDokumenModal() {
    const modal = document.getElementById('dokumenModal');
    const iframe = document.getElementById('dokumenFrame');
    const embed = document.getElementById('dokumenEmbed');
    
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    iframe.src = '';
    embed.innerHTML = '';
    iframe.style.display = 'none';
    embed.style.display = 'none';
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('dokumenModal');
    if (e.target === modal) {
        closeDokumenModal();
    }
});

// Close modal dengan Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDokumenModal();
    }
});

/**
 * ═══════════════════════════════════════════════════════════════
 * SNACKBAR NOTIFICATION - Functions & Event Handlers
 * ═══════════════════════════════════════════════════════════════
 * Features:
 * - Fixed position di bottom center
 * - Auto-dismiss setelah 6 detik
 * - Manual close button
 * - Smooth slide animations (in & out)
 * - Support multiple concurrent notifications (stack)
 * - Responsive: adapts to mobile screen size
 */
function setupSnackbars() {
    const snackbars = document.querySelectorAll('.snackbar');
    
    snackbars.forEach(snackbar => {
        // Setup close button
        const closeBtn = snackbar.querySelector('.snackbar-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                dismissSnackbar(snackbar);
            });
        }
        
        // Auto dismiss setelah 6 detik
        setTimeout(() => {
            dismissSnackbar(snackbar);
        }, 6000);
    });
}

function dismissSnackbar(snackbar) {
    if (!snackbar) return;
        snackbar.style.animation = 'slideOutDown 0.3s ease-out forwards';
    
    setTimeout(() => {
        if (snackbar.parentElement) {
            snackbar.remove();
        }
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    setupSnackbars();
    
    /**
     * ═════════════════════════════════════════════════════════════
     * Initialize Multi-Select Dropdowns
     * ═════════════════════════════════════════════════════════════
     * Instances:
     * - dosenMs: Dosen pengampu tambahan (optional, multi-select)
     * - cplMs: CPL (required, multi-select)
     * - cpmkMs: CPMK (required, multi-select)
     */
    const dosenMs  = new MultiSelect(document.getElementById('dosenMs'), {maxWidth: 467, keepOpen: true});
    const cplMs    = new MultiSelect(document.getElementById('cplMs'), { maxWidth: 467, keepOpen: true });
    const cpmkMs   = new MultiSelect(document.getElementById('cpmkMs'), { maxWidth: 467, keepOpen: true });
    const mkSelect = document.getElementById('mkSelect');

    /**
     * ═════════════════════════════════════════════════════════
     * CASCADE LOGIC: Mata Kuliah Selection Handler
     * ═════════════════════════════════════════════════════════
     * 
     * Workflow:
     * 1. User select Mata Kuliah -> trigger change event
     * 2. Fetch available Dosen untuk MK ini
     * 3. Fetch available CPL untuk MK ini
     * 4. Reset CPMK (wait untuk CPL selection)
     */
    mkSelect.addEventListener('change', function () {
        const mkId = this.value;
        
        // Validasi: jika tidak ada MK dipilih
        if (!mkId) {
            dosenMs.setDisabled('Pilih mata kuliah terlebih dahulu');
            cplMs.setDisabled('Pilih mata kuliah terlebih dahulu');
            cpmkMs.setDisabled('Pilih CPL terlebih dahulu');
            return;
        }

        dosenMs.setLoading('Memuat dosen…');
        cplMs.setLoading('Memuat CPL…');
        cpmkMs.setDisabled('Pilih CPL terlebih dahulu');

        // Fetch Dosen
        fetch(`{{ route('banksoal.rps.dosen.dosen') }}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    dosenMs.setItems(data.map(d => ({ id: d.id, label: d.name })), 'Pilih dosen');
                } else {
                    dosenMs.setDisabled('Tidak ada dosen terdaftar');
                }
            })
            .catch(err => {
                console.error('Error fetching dosen:', err);
                dosenMs.setDisabled('Error loading dosen');
            });

        // Fetch CPL
        const cplUrl = `{{ url('/bank-soal/rps/dosen/cpl') }}/${mkId}`;
        fetch(cplUrl)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    cplMs.setItems(data.map(c => ({ id: c.id, label: `${c.kode}` })), 'Pilih CPL');
                } else {
                    cplMs.setDisabled('Tidak ada CPL untuk mata kuliah ini');
                }
            })
            .catch(err => {
                console.error('Error fetching CPL:', err);
                cplMs.setDisabled('Error loading CPL');
            });
    });

    /**
     * ═════════════════════════════════════════════════════════
     * CASCADE LOGIC: CPL Selection Handler
     * ═════════════════════════════════════════════════════════
     * 
     * Flow:
     * 1. User select CPL -> emit ms:change event
     * 2. Fetch CPMK yang terkait dengan CPL(s) dipilih
     * 3. Populate cpmkMs dengan data
     */
    document.getElementById('cplMs').addEventListener('ms:change', function (e) {
        const cplIds = e.detail;

        if (!cplIds.length) {
            cpmkMs.setDisabled('Pilih CPL terlebih dahulu');
            return;
        }

        cpmkMs.setLoading('Memuat CPMK…');

        const queryString = cplIds.map(id => `cpl_ids[]=${id}`).join('&');
        const cpmkUrl = `{{ route('banksoal.rps.dosen.cpmk') }}?${queryString}`;
        
        fetch(cpmkUrl)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    cpmkMs.setItems(data.map(c => ({ id: c.id, label: `${c.kode}` })), 'Pilih CPMK');
                } else {
                    cpmkMs.setDisabled('Tidak ada CPMK untuk CPL yang dipilih');
                }
            })
            .catch(err => {
                console.error('Error fetching CPMK:', err);
                cpmkMs.setDisabled('Error loading CPMK');
            });
    });

    /**
     * ═════════════════════════════════════════════════════════
     * FILE UPLOAD DRAG & DROP
     * ═════════════════════════════════════════════════════════
     * 
     * Features:
     * - Drag file ke upload zone untuk select
     * - Visual feedback: border color + background color change
     * - Update filename display setelah file dipilih
     */
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    
    if (uploadZone && fileInput) {
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.style.borderColor = '#3b82f6';
            uploadZone.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.style.borderColor = '#d1d5db';
            uploadZone.style.backgroundColor = '#fff';
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.style.borderColor = '#d1d5db';
            uploadZone.style.backgroundColor = '#fff';
            fileInput.files = e.dataTransfer.files;
            updateFileName(fileInput);
        });

        fileInput.addEventListener('change', () => updateFileName(fileInput));
    }

    function updateFileName(input) {
        if (input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            const uploadText = document.getElementById('uploadText');
            if (uploadText) {
                uploadText.textContent = fileName;
            }
        }
    }

    // ── Submit Handler ───────────────────────────────────────
    const form = document.querySelector('form');
    form?.addEventListener('submit', function (e) {
        const mkId = document.getElementById('mkSelect').value;
        const cplIds = cplMs.getSelected();
        const cpmkIds = cpmkMs.getSelected();
        const fileInput = document.getElementById('fileInput');
        const errors = [];

        // Validasi
        if (!mkId) errors.push('Mata Kuliah harus dipilih');
        if (!cplIds || cplIds.length === 0) errors.push('Minimal satu CPL harus dipilih');
        if (!cpmkIds || cpmkIds.length === 0) errors.push('Minimal satu CPMK harus dipilih');
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) errors.push('File RPS harus diunggah');

        if (errors.length > 0) {
            e.preventDefault();
            alert('Validasi Error:\n\n' + errors.map((e, i) => `${i + 1}. ${e}`).join('\n'));
            return false;
        }

        // Form validation passed — allow form to submit naturally
    });
});
</script>

<style>
/* Snackbar Styling */
.snackbar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    min-width: 300px;
    max-width: 500px;
    padding: 14px 16px;
    padding-right: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    animation: slideUpIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes slideUpIn {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

@keyframes slideOutDown {
    from {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
    to {
        opacity: 0;
        transform: translateX(-50%) translateY(20px);
    }
}

.snackbar-success {
    background-color: #10b981;
    color: #ffffff;
    border-left: 4px solid #059669;
}

.snackbar-error {
    background-color: #ef4444;
    color: #ffffff;
    border-left: 4px solid #dc2626;
}

.snackbar i {
    flex-shrink: 0;
    font-size: 18px;
}

.snackbar span {
    flex: 1;
}

.snackbar ul {
    margin: 0;
    padding-left: 20px;
}

.snackbar-close {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: inherit;
    opacity: 0.8;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s;
}

.snackbar-close:hover {
    opacity: 1;
}

/* Responsive untuk mobile */
@media (max-width: 600px) {
    .snackbar {
        bottom: 16px;
        left: 16px;
        right: 16px;
        transform: none;
        max-width: none;
    }
    
    @keyframes slideUpIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideOutDown {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(20px);
        }
    }
}
</style>

</x-banksoal::layouts.master>