{{--
    Lokasi + cek jadwal (S-81, usulan Pak Maman L-41: cegah proker bentrok tempat & jam).
    Dipakai form Rencana Proker & Pelaksanaan (_fields).

    "Ruangan kampus" = master Manajemen Ruangan EOffice (sama dengan Capstone/KP/Praktikum);
    statusnya dicek langsung ke EOffice + proker lain semua bidang. Memilih ruangan di sini
    BELUM memesan ruangan. "Luar kampus / daring" = teks bebas seperti dulu.

    Variabel: $proker, $tandaWajib (tanda *), $wajibRealisasi (atribut required).
--}}
@php
    $daftarRuangan = app(\Modules\ManajemenMahasiswa\Services\JadwalRuanganService::class)->daftarRuangan()
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
        <select name="ruangan_id" class="form-select form-control-custom mk-select--md" x-model="ruanganId"
                :disabled="jenis !== 'kampus'" @if($wajibRealisasi) :required="jenis === 'kampus'" @endif>
            <option value="">Pilih ruangan…</option>
            <template x-for="r in ruangan" :key="r.id">
                <option :value="String(r.id)" x-text="labelOpsi(r)" :selected="String(r.id) === ruanganId"></option>
            </template>
        </select>
        <template x-if="terpilih()">
            <div class="mk-lok-status">
                <span class="mm-status" :class="'mm-status--' + nada(terpilih().status)" x-text="labelStatus(terpilih().status)"></span>
                <span x-text="terpilih().alasan || ''"></span>
                <span x-show="kapasitasKurang()" class="mk-lok-warn" x-text="'Kapasitas ' + terpilih().kapasitas + ' < target ' + target + ' peserta'"></span>
            </div>
        </template>
        <div class="mk-lok-hint">Sumber: Manajemen Ruangan EOffice. Memilih ruangan di sini <b>belum memesan</b> ruangan — pinjam lewat Manajemen Ruangan.</div>
    </div>

    {{-- Luar kampus / daring --}}
    <div x-show="jenis === 'luar'" x-cloak>
        <input type="text" name="lokasi" class="form-control form-control-custom"
               value="{{ $lokasiAwal }}" id="lokasiInput" maxlength="150"
               :disabled="jenis !== 'luar'"
               @if($wajibRealisasi) :required="jenis === 'luar'" placeholder="Lokasi sebenarnya kegiatan berlangsung" @else placeholder="Mis. Pantai Tirang, Panti Asuhan Sunan Muria, Zoom" @endif
               oninput="updateCharCount('lokasiInput','lokasiCount',150)">
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
    .mk-lok-hint { font-size: 11px; color: var(--c-fg-muted); margin-top: 6px; font-weight: 500; line-height: 1.5; }
</style>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mkLokasiJadwal', (cfg) => ({
            ruangan: cfg.ruangan, ruanganId: cfg.ruanganId, jenis: cfg.jenis,
            bentrok: [], target: 0, timer: null,
            init() {
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
                if (!p.get('tanggal_mulai')) { this.bentrok = []; return; }
                try {
                    const r = await fetch(cfg.url + '?' + p, { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const d = await r.json();
                    if (d.ruangan?.length) this.ruangan = d.ruangan;
                    this.bentrok = d.bentrok || [];
                } catch (e) { /* peringatan saja; gagal cek tidak menghalangi simpan */ }
            },
            terpilih() { return this.ruangan.find((r) => String(r.id) === this.ruanganId) || null; },
            kapasitasKurang() { const t = this.terpilih(); return t && t.kapasitas && this.target > t.kapasitas; },
            labelStatus(s) { return ({ tersedia: 'Tersedia', terpakai: 'Terpakai di EOffice', menunggu: 'Sedang diajukan pihak lain', direncanakan: 'Direncanakan proker lain', belum_dicek: 'Belum dicek' })[s] || s; },
            nada(s) { return ({ tersedia: 'success', terpakai: 'error', menunggu: 'warning', direncanakan: 'warning' })[s] || 'neutral'; },
            labelOpsi(r) {
                const tanda = ({ terpakai: ' — terpakai', menunggu: ' — sedang diajukan', direncanakan: ' — direncanakan proker lain' })[r.status] || '';
                return r.nama + (r.kapasitas ? ' · ' + r.kapasitas + ' kursi' : '') + tanda;
            },
        }));
    });
</script>
@endonce
