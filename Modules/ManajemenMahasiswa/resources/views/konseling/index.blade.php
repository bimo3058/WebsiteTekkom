{{--
    Catatan Konseling — buku catatan GPM (dosen konseling). Mahasiswa tetap
    menghubungi dosen lewat chat seperti biasa; halaman ini hanya tempat dosen
    mencatat sesi. Identitas mahasiswa diketik bebas, tidak tertaut ke akun.

    Tambah & edit memakai satu modal yang diisi JavaScript; detail memakai
    modal baca. Tampilan mengikuti kit SITKOM (partials/sitkom-ui).
--}}
<x-manajemenmahasiswa::layouts.admin>

@include('manajemenmahasiswa::partials.sitkom-ui')

@php
    $adaFilter = $filters['q'] !== '';
    $hariIni = now()->toDateString();
@endphp

<style>
    .ksl-search { position: relative; width: min(220px, calc(100vw - 200px)); min-width: 120px; }
    .ksl-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--c-fg-placeholder); }
    .ksl-search input {
        background: #ffffff; border: 1px solid var(--c-border); border-radius: 8px;
        height: 34px; padding-left: 34px; font-size: 12px; font-weight: 500; width: 100%; color: var(--c-fg);
    }
    .ksl-search input:focus { border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle); outline: none; }

    .ksl-card { background: #ffffff; border: 1px solid var(--c-border); border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .ksl-toolbar {
        display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
        padding: 14px 16px; border-bottom: 1px solid var(--c-border);
    }
    .ksl-toolbar h2 { font-size: 14px; font-weight: 700; color: var(--c-fg); margin: 0; }
    .ksl-toolbar form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 0; }

    .ksl-nama { font-weight: 600; color: var(--c-fg); }
    .ksl-sub { font-size: 11px; color: var(--c-fg-muted); margin-top: 1px; }
    .ksl-tgl { white-space: nowrap; color: var(--c-fg-sec); }
    .ksl-table .mk-menu.ksl-menu-fixed { position: fixed; z-index: 1050; }
    /* Di layar sempit tabel digeser ke samping, bukan diperas sampai nama patah per kata. */
    .ksl-table { min-width: 640px; }

    /* public/js/mobile-navigation.js menandai setiap <form> ber-display flex di dalam
       konten sebagai toolbar (data-mobile-toolbar) lalu merapatkan anaknya ke kanan.
       Modal ini memakai <form> sebagai .modal-content, jadi di HP judul & isinya ikut
       menyempit ke kanan. Kembalikan ke susunan kolom modal biasa. */
    body[data-mobile-shell] #kslForm[data-mobile-toolbar] {
        flex-wrap: nowrap !important;
        gap: 0 !important;
        align-items: stretch;
    }

    .ksl-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .ksl-grid .full { grid-column: 1 / -1; }
    @media (max-width: 576px) { .ksl-grid { grid-template-columns: 1fr; } }
    .ksl-hint { font-size: 11px; color: var(--c-fg-muted); margin-top: 4px; }
    .ksl-err { font-size: 11px; color: var(--c-error); margin-top: 4px; font-weight: 500; }

    .ksl-detail dt { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--c-fg-muted); margin-bottom: 4px; }
    .ksl-detail dd { font-size: 13px; color: var(--c-fg); margin-bottom: 16px; }
    .ksl-detail .ksl-isi {
        white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.6;
        background: var(--c-bg); border: 1px solid var(--c-border); border-radius: 8px; padding: 12px 14px;
    }
</style>

{{-- ── Header ── --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Catatan Konseling">
    Total <span style="color: var(--c-primary); font-weight: 600;">{{ number_format($ringkasan['total']) }}</span> catatan
    · <span style="font-weight: 600;">{{ $ringkasan['bulan_ini'] }}</span> bulan ini

    <x-slot:actions>
        <button type="button" class="mk-btn mk-btn--primary" onclick="kslBukaTambah()">
            <x-manajemenmahasiswa::ui.icon name="plus" size="16" /> Tambah Catatan
        </button>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" />

<div class="ksl-card">
    <div class="ksl-toolbar">
        <h2>Daftar Catatan</h2>
        <form method="GET" action="{{ route('manajemenmahasiswa.konseling.index') }}" id="kslFilterForm">
            @if(request()->filled('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <div class="ksl-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama atau NIM…">
            </div>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="mm-table ksl-table">
            <thead>
                <tr>
                    <th style="width: 56px;">No</th>
                    <th>Tanggal</th>
                    <th>Mahasiswa</th>
                    <th>Dicatat oleh</th>
                    <th style="width: 72px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($catatan as $index => $item)
                    @php
                        $identitas = collect([$item->nim, $item->angkatan ? 'Angkatan ' . $item->angkatan : null])->filter()->implode(' · ');
                        $payload = [
                            'id'             => $item->id,
                            'nama_mahasiswa' => $item->nama_mahasiswa,
                            'nim'            => $item->nim,
                            'angkatan'       => $item->angkatan,
                            'tanggal'        => optional($item->tanggal)->toDateString(),
                            'tanggal_label'  => optional($item->tanggal)->translatedFormat('d F Y'),
                            'catatan'        => $item->catatan,
                            'pencatat'       => optional($item->pencatat)->name,
                            'update_url'     => route('manajemenmahasiswa.konseling.update', $item->id),
                        ];
                    @endphp
                    <tr>
                        <td style="color: var(--c-fg-muted);">{{ $catatan->firstItem() + $index }}</td>
                        <td class="ksl-tgl">{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                        <td>
                            <a href="javascript:void(0)" class="ksl-nama" style="text-decoration: none;"
                               data-catatan="{{ json_encode($payload) }}" onclick="kslBukaDetail(this)">{{ $item->nama_mahasiswa }}</a>
                            @if($identitas !== '')
                                <div class="ksl-sub">{{ $identitas }}</div>
                            @endif
                        </td>
                        <td style="color: var(--c-fg-sec);">{{ optional($item->pencatat)->name ?? '—' }}</td>
                        <td style="text-align: center;">
                            <div style="position: relative; display: inline-block;" x-data="kslAksiMenu"
                                 @scroll.window.capture="open = false" @resize.window="open = false">
                                <button type="button" @click="toggle($el)" @click.outside="open = false" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Aksi">
                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                </button>
                                <div x-show="open" x-cloak :style="posisi" class="mk-menu ksl-menu-fixed" style="display: none;"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100">
                                    <button type="button" class="mk-menu-item" data-catatan="{{ json_encode($payload) }}" @click="open = false; kslBukaDetail($el)">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        Lihat Detail
                                    </button>
                                    <button type="button" class="mk-menu-item" data-catatan="{{ json_encode($payload) }}" @click="open = false; kslBukaEdit($el)">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        Edit
                                    </button>
                                    <div class="mk-menu-sep"></div>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.konseling.destroy', $item->id) }}"
                                          data-nama="{{ $item->nama_mahasiswa }}" onsubmit="return kslKonfirmasiHapus(this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mk-menu-item">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6M14 11v6"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 60px 24px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <span style="color: #D1D5DB;"><x-manajemenmahasiswa::ui.icon name="heart" size="40" /></span>
                                @if($adaFilter)
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted); margin: 0;">Tidak ada catatan yang cocok</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder); margin: 0;">Coba kata kunci lain.</p>
                                    <a href="{{ route('manajemenmahasiswa.konseling.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm mt-1">Reset pencarian</a>
                                @else
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted); margin: 0;">Belum ada catatan konseling</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder); margin: 0;">Klik "Tambah Catatan" setelah bertemu mahasiswa.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('manajemenmahasiswa::partials.table-footer', ['paginator' => $catatan])
</div>

{{-- ── Modal tambah / edit ── --}}
<div class="modal fade" id="kslFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <form method="POST" id="kslForm" class="modal-content" action="{{ route('manajemenmahasiswa.konseling.store') }}">
            @csrf
            <input type="hidden" name="_method" id="kslMethod" value="POST" disabled>
            <input type="hidden" name="_id" id="kslId" value="">

            <x-manajemenmahasiswa::ui.modal-header subtitle="Catatan hanya terlihat oleh GPM">
                <x-slot:icon><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></x-slot:icon>
                <span id="kslFormJudul">Tambah Catatan Konseling</span>
            </x-manajemenmahasiswa::ui.modal-header>

            <div class="modal-body">
                <div class="ksl-grid">
                    <div class="full">
                        <label class="form-label-custom" for="kslNama">Nama Mahasiswa <span class="required">*</span></label>
                        <input type="text" id="kslNama" name="nama_mahasiswa" maxlength="150" required
                               class="form-control-custom @error('nama_mahasiswa') is-invalid @enderror" placeholder="Nama lengkap mahasiswa">
                        @error('nama_mahasiswa') <div class="ksl-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label-custom" for="kslNim">NIM</label>
                        <input type="text" id="kslNim" name="nim" maxlength="30"
                               class="form-control-custom @error('nim') is-invalid @enderror" placeholder="Opsional">
                        @error('nim') <div class="ksl-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label-custom" for="kslAngkatan">Angkatan</label>
                        <input type="number" id="kslAngkatan" name="angkatan" min="1990" max="{{ now()->year }}"
                               class="form-control-custom @error('angkatan') is-invalid @enderror" placeholder="Opsional, mis. 2022">
                        @error('angkatan') <div class="ksl-err">{{ $message }}</div> @enderror
                    </div>
                    <div class="full">
                        <label class="form-label-custom" for="kslTanggal">Tanggal Konseling <span class="required">*</span></label>
                        <input type="date" id="kslTanggal" name="tanggal" max="{{ $hariIni }}" required
                               class="form-control-custom @error('tanggal') is-invalid @enderror" style="max-width: 240px;">
                        @error('tanggal') <div class="ksl-err">{{ $message }}</div> @enderror
                    </div>
                    <div class="full">
                        <label class="form-label-custom" for="kslCatatan">Catatan</label>
                        <textarea id="kslCatatan" name="catatan" rows="5" maxlength="5000"
                                  class="form-control-custom @error('catatan') is-invalid @enderror"
                                  style="font-weight: 500; resize: vertical;"
                                  placeholder="Ringkasan masalah, saran yang diberikan, rencana berikutnya… (opsional)"></textarea>
                        <div class="ksl-hint">Disimpan terenkripsi.</div>
                        @error('catatan') <div class="ksl-err">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="mk-btn mk-btn--secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="mk-btn mk-btn--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal detail ── --}}
<div class="modal fade" id="kslDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <x-manajemenmahasiswa::ui.modal-header subtitle="Catatan hanya terlihat oleh GPM">
                <x-slot:icon><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></x-slot:icon>
                Detail Catatan Konseling
            </x-manajemenmahasiswa::ui.modal-header>
            <div class="modal-body">
                <dl class="ksl-detail" style="margin: 0;">
                    <dt>Mahasiswa</dt>
                    <dd><span id="kslDNama" style="font-weight: 600;"></span><div class="ksl-sub" id="kslDIdentitas"></div></dd>
                    <dt>Tanggal</dt>
                    <dd id="kslDTanggal"></dd>
                    <dt>Catatan</dt>
                    <dd><div class="ksl-isi" id="kslDCatatan"></div></dd>
                    <dt>Dicatat oleh</dt>
                    <dd id="kslDPencatat" style="margin-bottom: 0;"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="mk-btn mk-btn--secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="mk-btn mk-btn--primary" id="kslDEdit">Edit</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const STORE_URL = @json(route('manajemenmahasiswa.konseling.store'));
        const HARI_INI = @json($hariIni);
        const TAHUN_INI = {{ now()->year }};
        let detailAktif = null;

        const $ = id => document.getElementById(id);
        const modal = id => bootstrap.Modal.getOrCreateInstance($(id));
        const baca = el => JSON.parse(el.dataset.catatan || '{}');

        function isiForm(d, mode) {
            const edit = mode === 'edit';
            $('kslFormJudul').textContent = edit ? 'Edit Catatan Konseling' : 'Tambah Catatan Konseling';
            $('kslForm').action = edit ? d.update_url : STORE_URL;
            $('kslMethod').value = edit ? 'PUT' : 'POST';
            $('kslMethod').disabled = !edit;
            $('kslId').value = edit ? (d.id || '') : '';
            $('kslNama').value = d.nama_mahasiswa || '';
            $('kslNim').value = d.nim || '';
            $('kslAngkatan').value = d.angkatan || '';
            $('kslTanggal').value = d.tanggal || HARI_INI;
            $('kslCatatan').value = d.catatan || '';
            batasiAngkatan();
        }

        function bersihkanError() {
            document.querySelectorAll('#kslForm .ksl-err').forEach(e => e.remove());
            document.querySelectorAll('#kslForm .is-invalid').forEach(e => e.classList.remove('is-invalid'));
        }

        // Batas atas angkatan mengikuti tahun tanggal konseling (tak mungkin lebih
        // baru dari tahun ini). Server tetap memeriksa ulang aturan yang sama.
        function batasiAngkatan() {
            const tahun = parseInt(($('kslTanggal').value || HARI_INI).slice(0, 4), 10);
            $('kslAngkatan').max = Math.min(tahun || TAHUN_INI, TAHUN_INI);
        }
        $('kslTanggal').addEventListener('change', batasiAngkatan);

        window.kslBukaTambah = function () {
            bersihkanError();
            isiForm({}, 'tambah');
            modal('kslFormModal').show();
        };

        window.kslBukaEdit = function (el) {
            bersihkanError();
            isiForm(baca(el), 'edit');
            modal('kslFormModal').show();
        };

        window.kslBukaDetail = function (el) {
            const d = detailAktif = baca(el);
            const identitas = [d.nim, d.angkatan ? 'Angkatan ' + d.angkatan : null].filter(Boolean).join(' · ');
            $('kslDNama').textContent = d.nama_mahasiswa || '-';
            $('kslDIdentitas').textContent = identitas;
            $('kslDTanggal').textContent = d.tanggal_label || '-';
            $('kslDCatatan').textContent = d.catatan || 'Tidak ada catatan.';
            $('kslDPencatat').textContent = d.pencatat || '—';
            modal('kslDetailModal').show();
        };

        $('kslDEdit').addEventListener('click', function () {
            if (!detailAktif) return;
            modal('kslDetailModal').hide();
            bersihkanError();
            isiForm(detailAktif, 'edit');
            modal('kslFormModal').show();
        });

        window.kslKonfirmasiHapus = function (form) {
            return mkConfirmSubmit(form, 'Catatan konseling "' + (form.dataset.nama || '-') + '" akan dihapus.', {
                title: 'Hapus Catatan?', variant: 'danger', confirmText: 'Ya, Hapus'
            });
        };

        document.querySelector('.ksl-search input')?.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); $('kslFilterForm').submit(); }
        });

        // Validasi gagal: buka lagi modalnya dengan isian yang tadi diketik.
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const lama = @json(old());
                const idLama = lama._id || '';
                isiForm(Object.assign({}, lama, {
                    id: idLama,
                    update_url: idLama ? @json(url('manajemen-mahasiswa/konseling')) + '/' + idLama : null,
                }), idLama ? 'edit' : 'tambah');
                modal('kslFormModal').show();
            });
        @endif
    })();

    // Menu aksi "⋯" per baris — posisi fixed dihitung saat dibuka supaya tidak
    // terpotong pembungkus tabel (pola sama dengan daftar Pengaduan).
    document.addEventListener('alpine:init', () => {
        Alpine.data('kslAksiMenu', () => ({
            open: false,
            posisi: {},
            toggle(tombol) {
                this.open = !this.open;
                if (!this.open) return;
                const r = tombol.getBoundingClientRect();
                const layar = document.documentElement;
                const keAtas = layar.clientHeight - r.bottom < 150 && r.top > 150;
                this.posisi = {
                    right: (layar.clientWidth - r.right) + 'px',
                    top: keAtas ? 'auto' : (r.bottom + 5) + 'px',
                    bottom: keAtas ? (layar.clientHeight - r.top + 5) + 'px' : 'auto',
                };
            },
        }));
    });
</script>
</x-manajemenmahasiswa::layouts.admin>
