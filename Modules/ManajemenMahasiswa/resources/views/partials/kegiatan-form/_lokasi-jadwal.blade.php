{{--
    Lokasi + cek jadwal (S-81, usulan Pak Maman L-41: cegah proker bentrok tempat & jam).
    Dipakai form Rencana Proker & Pelaksanaan (_fields).

    "Ruangan kampus" = master Manajemen Ruangan EOffice (sama dengan Capstone/KP/Praktikum);
    statusnya dicek langsung ke EOffice + proker lain semua bidang. Memilih ruangan di sini
    BELUM memesan ruangan. "Luar kampus / daring" = teks bebas seperti dulu.

    Variabel: $proker, $tandaWajib (tanda *), $wajibRealisasi (atribut required).
--}}
@php
    $jadwalSvc = app(\Modules\ManajemenMahasiswa\Services\JadwalRuanganService::class);
    // S-88 (15 Nov 2026): yang meminjam ruangan di EOffice adalah Ketua Pelaksana proker, bukan
    // pengisi form. Peminjamannya bisa diambil ke form ini.
    // S-89: ruangan hanya dari peminjaman DISETUJUI milik Ketua Pelaksana; daftar dimuat ulang
    // lewat JSON saat Ketua Pelaksana di form diganti.
    $ketuaFormId     = old('ketua_pelaksana_id', $proker->ketua_pelaksana_id);
    $userKetua       = $ketuaFormId ? $jadwalSvc->userDariMahasiswa($ketuaFormId) : null;
    $peminjamanKetua = $jadwalSvc->peminjamanKetua($userKetua);
    // S-90: pintasan pinjam ruangan (tab baru) hanya untuk ketua pelaksana sendiri, karena
    // peminjaman tercatat atas nama yang meminjam. Aturan EOffice ditampilkan baca-saja.
    $urlPinjam    = \Illuminate\Support\Facades\Route::has('eoffice.peminjaman.user.booking')
        ? route('eoffice.peminjaman.user.booking') : null;
    $aturanPinjam = $jadwalSvc->aturanPeminjaman();
    $daftarRuangan = $jadwalSvc->daftarRuangan()
        ->map(fn ($r) => $r + ['status' => 'belum_dicek', 'alasan' => null])->values();
    $ruanganAwal = old('ruangan_id', $proker->ruangan_id);
    $lokasiAwal  = old('lokasi', $proker->ruangan_id ? '' : $proker->lokasi);
    // Default: ruangan kampus bila tersedia, kecuali kegiatan lama yang lokasinya teks bebas.
    $jenisAwal = $daftarRuangan->isEmpty() ? 'luar'
        : ($ruanganAwal ? 'kampus' : (filled($lokasiAwal) ? 'luar' : 'kampus'));
@endphp
<div class="mb-3" x-data="mkLokasiJadwal({
        ruangan: @js($daftarRuangan),
        ruanganId: @js($ruanganAwal ? (string) $ruanganAwal : ''),
        jenis: @js($jenisAwal),
        url: @js(route('manajemenmahasiswa.proker.cek-jadwal')),
        kecuali: @js($proker->exists ? $proker->id : null),
        peminjamanKetua: @js($peminjamanKetua),
        adaKetua: @js($userKetua !== null),
        sayaKetua: @js($userKetua !== null && $userKetua === (int) auth()->id()),
        urlPeminjaman: @js(route('manajemenmahasiswa.proker.peminjaman-ketua')),
        tanggalAwal: @js($proker->tanggal_mulai ? \Carbon\Carbon::parse($proker->tanggal_mulai)->toDateString() : ''),
     })">
    <label class="form-label-custom">Lokasi @if($tandaWajib)<span class="required">*</span>@endif</label>

    @if($daftarRuangan->isNotEmpty())
        <div class="mk-lok-seg" role="radiogroup" aria-label="Jenis lokasi">
            <label :class="{ 'aktif': jenis === 'kampus' }"><input type="radio" value="kampus" x-model="jenis"> Ruangan kampus</label>
            <label :class="{ 'aktif': jenis === 'luar' }"><input type="radio" value="luar" x-model="jenis"> Luar kampus / daring</label>
        </div>
    @endif

    {{-- Ruangan kampus (EOffice) --}}
    <div x-show="jenis === 'kampus'" x-cloak>
        <template x-if="terpilih()">
            <div class="mk-lok-status">
                <span class="mm-status" :class="'mm-status--' + nada(terpilih().status)" x-text="labelStatus(terpilih().status)"></span>
                <span x-text="terpilih().alasan || ''"></span>
                <span x-show="kapasitasKurang()" class="mk-lok-warn" x-text="'Kapasitas ' + terpilih().kapasitas + ' < target ' + target + ' peserta'"></span>
            </div>
        </template>
        {{-- S-89: ruangan HANYA dari peminjaman Ketua Pelaksana yang sudah disetujui (semua role). --}}
        <input type="hidden" name="ruangan_id" :value="ruanganId" :disabled="jenis !== 'kampus'">
        <div class="mk-pk">
            <template x-if="!adaKetua">
                <div class="mk-lok-hint">Pilih Ketua Pelaksana dulu untuk melihat peminjaman ruangannya.</div>
            </template>
            <template x-if="adaKetua && peminjamanKetua.length === 0">
                <div class="mk-lok-hint">Belum ada peminjaman ruangan yang disetujui untuk Ketua Pelaksana. Pinjam lewat Manajemen Ruangan EOffice, tunggu disetujui, lalu muat ulang.</div>
            </template>
            <template x-if="adaKetua && peminjamanKetua.length > 0">
                <div class="mk-pk__daftar">
                    <template x-for="p in peminjamanKetua" :key="p.id">
                        <button type="button" class="mk-pk__baris" :class="{ 'is-dipakai': dipakai(p) }" @click="ambilPeminjaman(p)">
                            <span class="mk-pk__utama">
                                <b x-text="p.ruangan"></b> · <span x-text="p.tanggal_label"></span> · <span x-text="p.jam_mulai + '–' + p.jam_selesai"></span>
                                <span class="mm-status" :class="dipakai(p) ? 'mm-status--success' : 'mm-status--neutral'" x-text="dipakai(p) ? 'Dipakai' : 'Pakai'"></span>
                            </span>
                            <span class="mk-pk__tujuan" x-show="p.tujuan" x-text="p.tujuan"></span>
                        </button>
                    </template>
                </div>
            </template>
            <template x-if="adaKetua">
                <div class="mk-pk__aksi">
                    <button type="button" class="mk-pk__tombol" @click="muat()" :disabled="memuat" x-text="memuat ? 'Memuat…' : 'Muat ulang'"></button>
                    @if($urlPinjam)
                        <template x-if="sayaKetua">
                            <a href="{{ $urlPinjam }}" target="_blank" rel="noopener" class="mk-pk__tombol mk-pk__tombol--link">Pinjam ruangan di EOffice ↗</a>
                        </template>
                    @endif
                    <template x-if="!sayaKetua">
                        <span class="mk-lok-hint">Peminjaman ruangan dilakukan oleh Ketua Pelaksana sendiri di EOffice.</span>
                    </template>
                </div>
            </template>
            @if($aturanPinjam)
                <div class="mk-lok-hint">Aturan EOffice: jam {{ $aturanPinjam['jam_buka'] }}–{{ $aturanPinjam['jam_tutup'] }}{{ $aturanPinjam['buka_akhir_pekan'] ? '' : ', Senin–Jumat (bukan hari libur)' }}{{ $aturanPinjam['batas_h_min'] > 0 ? ', diajukan paling lambat H-' . $aturanPinjam['batas_h_min'] : '' }}.</div>
            @endif
            <template x-if="ruanganId && !cocokTersimpan()">
                <div class="mk-lok-hint">Ruangan tersimpan: <b x-text="terpilih()?.nama || 'ruangan terpilih'"></b> — belum tercatat sebagai peminjaman Ketua Pelaksana yang disetujui.</div>
            </template>
        </div>
        <div class="mk-lok-hint">Ruangan diambil dari peminjaman Ketua Pelaksana di Manajemen Ruangan EOffice yang sudah disetujui.@if($tahapRencana ?? false) Proker belum bisa diajukan sebelum ruangan dipinjam dan disetujui.@endif</div>
    </div>

    {{-- Luar kampus / daring --}}
    <div x-show="jenis === 'luar'" x-cloak>
        <input type="text" name="lokasi" class="form-control form-control-custom"
               value="{{ $lokasiAwal }}" id="lokasiInput" maxlength="150"
               :disabled="jenis !== 'luar'"
               @if($wajibRealisasi) :required="jenis === 'luar'" placeholder="Lokasi sebenarnya kegiatan berlangsung" @else placeholder="Mis. Pantai Tirang, Panti Asuhan Sunan Muria, Zoom" @endif
               oninput="updateCharCount('lokasiInput','lokasiCount',150)">
        <div class="mk-lok-hint">Hanya untuk alamat di luar kampus atau daring (mis. Zoom). Ruangan atau gedung kampus Tekkom harus dipilih lewat mode Ruangan kampus.</div>
        <div style="font-size:11px;color:var(--c-fg-muted);text-align:right;margin-top:4px;font-weight:500;"><span id="lokasiCount">0</span>/150 karakter</div>
    </div>

    {{-- Proker lain di tanggal & jam yang sama (semua bidang) --}}
    <div class="mk-bentrok" x-show="bentrok.length" x-cloak role="alert">
        <div class="mk-bentrok__kepala">
            <span class="mk-bentrok__ikon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </span>
            <span class="mk-bentrok__judul" x-text="bentrok.length + ' proker lain berjalan di waktu yang sama'"></span>
        </div>
        <div class="mk-bentrok__daftar">
            <template x-for="b in bentrok" :key="b.id">
                <div class="mk-bentrok__item" :class="{ 'is-ruang-sama': b.ruang_sama }">
                    <span class="mk-bentrok__bidang" x-text="b.bidang"></span>
                    <div class="mk-bentrok__badan">
                        <div class="mk-bentrok__nama" x-text="b.judul"></div>
                        <div class="mk-bentrok__meta">
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><span x-text="b.tanggal"></span></span>
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span x-text="b.jam"></span></span>
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><span x-text="b.lokasi"></span></span>
                        </div>
                    </div>
                    <span class="mk-bentrok__sama" x-show="b.ruang_sama" x-cloak>Ruang sama</span>
                </div>
            </template>
        </div>
        <p class="mk-bentrok__kaki">Peringatan saja, proker tetap boleh disimpan. Proker bidang lain hanya terlihat judul, bidang, jam &amp; lokasinya.</p>
    </div>
</div>

@include('manajemenmahasiswa::partials.kegiatan-form._bentrok-styles')

@once
<style>
    .mk-lok-seg { display: inline-flex; gap: 4px; padding: 3px; border: 1px solid var(--c-border, #DFE1E7); border-radius: 8px; margin-bottom: 8px; flex-wrap: wrap; }
    /* position:relative mengurung radio tersembunyi di dalam label; tanpanya radio menempel ke <body>
       dan saat difokus browser menggulir seluruh halaman (celah kosong di bawah layout). */
    .mk-lok-seg label { position: relative; display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; color: var(--c-fg-sec, #344054); cursor: pointer; margin: 0; }
    .mk-lok-seg label.aktif { background: var(--c-primary, #0B266E); color: #fff; }
    .mk-lok-seg input { position: absolute; opacity: 0; pointer-events: none; }
    .mk-lok-status { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 8px; font-size: 12px; color: var(--c-fg-sec, #475467); }
    .mk-lok-warn { color: #93370D; font-weight: 500; }
    .mk-pk { position: relative; margin-top: 8px; }
    .mk-pk__tombol { font-size: 12px; font-weight: 500; padding: 5px 12px; border: 1px solid var(--c-primary, #0B266E); border-radius: 6px; background: transparent; color: var(--c-primary, #0B266E); cursor: pointer; }
    .mk-pk__tombol:hover { background: rgba(11, 38, 110, .06); }
    .mk-pk__aksi { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
    .mk-pk__aksi .mk-lok-hint { margin: 0; }
    .mk-pk__tombol--link { display: inline-block; text-decoration: none; background: var(--c-primary, #0B266E); color: #fff; }
    .mk-pk__tombol--link:hover { background: var(--c-primary, #0B266E); color: #fff; opacity: .9; }
    .mk-pk__daftar { max-width: 520px; max-height: 260px; overflow-y: auto; margin-bottom: 8px; background: var(--c-card, #fff); border: 1px solid var(--c-border, #DFE1E7); border-radius: 8px; }
    .mk-pk__baris.is-dipakai { background: rgba(11, 38, 110, .08); }
    .mk-pk__baris { display: block; width: 100%; text-align: left; padding: 8px 12px; border: 0; border-bottom: 1px solid var(--c-border, #DFE1E7); background: transparent; font-size: 12px; color: var(--c-fg-sec, #344054); cursor: pointer; }
    .mk-pk__baris:last-child { border-bottom: 0; }
    .mk-pk__baris:hover { background: rgba(11, 38, 110, .05); }
    .mk-pk__utama { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 6px; }
    .mk-pk__tujuan { display: block; margin-top: 2px; font-size: 11px; color: var(--c-fg-muted, #667085); }
    .mk-lok-hint { font-size: 11px; color: var(--c-fg-muted); margin-top: 6px; font-weight: 500; line-height: 1.5; }
</style>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mkLokasiJadwal', (cfg) => ({
            ruangan: cfg.ruangan, ruanganId: cfg.ruanganId, jenis: cfg.jenis,
            bentrok: [], target: 0, timer: null,
            peminjamanKetua: cfg.peminjamanKetua || [], adaKetua: cfg.adaKetua, sayaKetua: !!cfg.sayaKetua, memuat: false,
            tgl: cfg.tanggalAwal || '',
            init() {
                // S-89: daftar peminjaman ikut berubah saat Ketua Pelaksana di form diganti.
                if (window.Alpine && Alpine.store('mkKetua')) {
                    // chip status ruangan (cek) juga ikut ketua yang baru
                    this.$watch('$store.mkKetua.userId', () => this.$nextTick(async () => { await this.muat(); this.cek(); }));
                }
                this.$nextTick(() => this.muat());
                ['tanggal_mulai'].forEach((n) => {
                    const el = this.$root.closest('form')?.querySelector('[name="' + n + '"]');
                    if (el) el.addEventListener('change', () => { this.tgl = el.value; });
                });
                ['tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'target_peserta'].forEach((n) => {
                    const el = this.$root.closest('form')?.querySelector('[name="' + n + '"]');
                    if (el) el.addEventListener('change', () => this.jadwalkan());
                });
                this.$watch('ruanganId', () => this.jadwalkan());
                this.$watch('jenis', () => this.jadwalkan());
                const lok = this.$root.querySelector('[name="lokasi"]');
                if (lok) lok.addEventListener('change', () => this.jadwalkan());
                this.cek();
            },
            nilai(n) { return this.$root.closest('form')?.querySelector('[name="' + n + '"]')?.value || ''; },
            jadwalkan() { clearTimeout(this.timer); this.timer = setTimeout(() => this.cek(), 250); },
            async cek() {
                this.target = Number(this.nilai('target_peserta')) || 0;
                const p = new URLSearchParams({
                    tanggal_mulai: this.nilai('tanggal_mulai'), tanggal_selesai: this.nilai('tanggal_selesai'),
                    jam_mulai: this.nilai('jam_mulai'), jam_selesai: this.nilai('jam_selesai'),
                    ruangan_id: this.jenis === 'kampus' ? this.ruanganId : '',
                    lokasi: this.jenis === 'luar' ? this.nilai('lokasi') : '',
                });
                if (cfg.kecuali) p.set('kecuali', cfg.kecuali);
                if (this.nilai('ketua_pelaksana_id')) p.set('ketua_pelaksana_id', this.nilai('ketua_pelaksana_id'));
                if (!p.get('tanggal_mulai')) { this.bentrok = []; return; }
                try {
                    const r = await fetch(cfg.url + '?' + p, { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const d = await r.json();
                    if (d.ruangan?.length) this.ruangan = d.ruangan;
                    this.bentrok = d.bentrok || [];
                } catch (e) { /* peringatan saja; gagal cek tidak menghalangi simpan */ }
            },
            // S-89: muat peminjaman DISETUJUI milik Ketua Pelaksana yang sedang dipilih di form
            async muat() {
                const id = this.nilai('ketua_pelaksana_id');
                if (!id) { this.adaKetua = false; this.sayaKetua = false; this.peminjamanKetua = []; return; }
                this.memuat = true;
                try {
                    const r = await fetch(cfg.urlPeminjaman + '?' + new URLSearchParams({ ketua_pelaksana_id: id }), { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const d = await r.json();
                    this.adaKetua = !!d.ada_ketua;
                    this.sayaKetua = !!d.saya_ketua;
                    this.peminjamanKetua = d.peminjaman || [];
                } catch (e) { /* daftar lama dibiarkan; tombol Muat ulang bisa dicoba lagi */ }
                finally { this.memuat = false; }
            },
            dipakai(p) { return this.ruanganId === String(p.ruangan_id) && this.tgl === p.tanggal; },
            cocokTersimpan() { return this.peminjamanKetua.some((p) => this.dipakai(p)); },
            // S-88/S-89: isi ruangan + tanggal + jam dari peminjaman Ketua Pelaksana (date = Y-m-d, time = H:i)
            ambilPeminjaman(p) {
                this.jenis = 'kampus';
                this.ruanganId = String(p.ruangan_id);
                this.tgl = p.tanggal;
                const form = this.$root.closest('form');
                const isi = (n, v) => {
                    const el = form?.querySelector('[name="' + n + '"]');
                    if (!el || el.disabled || el.readOnly) return;
                    el.value = v;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                };
                isi('tanggal_mulai', p.tanggal);
                isi('tanggal_selesai', p.tanggal);
                isi('jam_mulai', p.jam_mulai);
                isi('jam_selesai', p.jam_selesai);
                this.cek();
            },
            terpilih() { return this.ruangan.find((r) => String(r.id) === this.ruanganId) || null; },
            kapasitasKurang() { const t = this.terpilih(); return t && t.kapasitas && this.target > t.kapasitas; },
            labelStatus(s) { return ({ tersedia: 'Tersedia', terpakai: 'Terpakai di EOffice', menunggu: 'Sedang diajukan pihak lain', direncanakan: 'Direncanakan proker lain', belum_dicek: 'Belum dicek', dipinjam_ketua: 'Dipinjam Ketua Pelaksana', diajukan_ketua: 'Diajukan Ketua Pelaksana', sebagian_ketua: 'Dipinjam sebagian', ditolak_ketua: 'Peminjaman ditolak' })[s] || s; },
            nada(s) { return ({ tersedia: 'success', terpakai: 'error', menunggu: 'warning', direncanakan: 'warning', dipinjam_ketua: 'success', diajukan_ketua: 'sky', sebagian_ketua: 'warning', ditolak_ketua: 'error' })[s] || 'neutral'; },
        }));
    });
</script>
@endonce
