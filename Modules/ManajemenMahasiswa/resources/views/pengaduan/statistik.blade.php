{{--
    Statistik Layanan Pengaduan (staf saja). Dua tab sesuai kebutuhan peran:

      Antrian Kerja  → admin kemahasiswaan: apa yang harus dibuka/ditindaklanjuti sekarang.
      Evaluasi Mutu  → GPM: tren, masalah berulang, dan kecepatan penanganan per periode
                       (bahan perbaikan; hanya angka gabungan).

    Grafik dibuat dengan HTML biasa (tanpa pustaka): batang satu warna (--c-primary)
    karena tugasnya membandingkan besaran, bukan membedakan seri. Tiap batang punya
    tooltip; angka yang bisa menunjuk pelapor (angkatan, pihak terlapor) di bawah
    PengaduanStatistikService::MIN_TAMPIL disamarkan menjadi "< 3".
--}}
@php
    use Modules\ManajemenMahasiswa\Services\PengaduanStatistikService as Stat;

    $op = $operasional;
    $ev = $evaluasi;
    $urlTab = fn (string $t) => route('manajemenmahasiswa.pengaduan.statistik', array_filter([
        'tab' => $t,
        'periode' => $t === 'evaluasi' && $periode !== 'semester' ? $periode : null,
    ]));
    $selisih7 = $op['masuk_7_hari'] - $op['masuk_7_sebelumnya'];
@endphp

<x-manajemenmahasiswa::layouts.admin>

@include('manajemenmahasiswa::pengaduan.partials.palette')
@include('manajemenmahasiswa::pengaduan.partials.box-styles')

<style>
    .ps-wrap { display: flex; flex-direction: column; gap: 16px; flex-shrink: 0; font-family: 'Inter Tight', sans-serif; }

    /* ── Tab ── */
    .ps-tabs { display: inline-flex; gap: 4px; padding: 4px; background: var(--c-bg); border: 1px solid var(--c-border); border-radius: 10px; align-self: flex-start; }
    .ps-tab { padding: 6px 14px; border-radius: 7px; font-size: 13px; font-weight: 600; color: var(--c-fg-muted); text-decoration: none !important; white-space: nowrap; }
    .ps-tab:hover { color: var(--c-fg); }
    .ps-tab.is-active { background: #ffffff; color: var(--c-primary); box-shadow: 0 1px 2px rgba(0,0,0,.08); }

    /* ── Stat tile ── */
    .ps-tiles { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .ps-tile { background: #ffffff; border: 1px solid var(--c-border); border-radius: 12px; padding: 14px 16px; min-width: 0; }
    .ps-tile-label { font-size: 12px; font-weight: 500; color: var(--c-fg-muted); }
    .ps-tile-value { font-size: 28px; font-weight: 700; color: var(--c-fg); line-height: 1.2; margin-top: 4px; letter-spacing: -.02em; }
    .ps-tile-value small { font-size: 14px; font-weight: 600; color: var(--c-fg-muted); letter-spacing: 0; }
    .ps-tile-note { font-size: 12px; color: var(--c-fg-muted); margin-top: 2px; line-height: 1.45; }
    /* Penanda perhatian: ikon + teks, tidak pernah warna saja. */
    .ps-flag { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; color: var(--c-warning); }

    /* ── Kartu ── */
    .ps-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .ps-card { background: #ffffff; border: 1px solid var(--c-border); border-radius: 14px; min-width: 0; }
    .ps-card.is-wide { grid-column: 1 / -1; }
    .ps-card-head { padding: 14px 16px; border-bottom: 1px solid var(--c-border); }
    .ps-card-title { font-size: 14px; font-weight: 700; color: var(--c-fg); margin: 0; }
    .ps-card-sub { font-size: 12px; color: var(--c-fg-muted); margin: 2px 0 0; line-height: 1.5; }
    .ps-card-body { padding: 16px; }
    .ps-empty { font-size: 13px; color: var(--c-fg-muted); padding: 8px 0; }

    /* ── Batang horizontal (membandingkan besaran antar kelompok) ── */
    .ps-bars { display: flex; flex-direction: column; gap: 10px; }
    .ps-bar-row { display: grid; grid-template-columns: minmax(110px, 38%) 1fr 44px; align-items: center; gap: 10px; }
    .ps-bar-label { font-size: 12px; color: var(--c-fg-sec); line-height: 1.35; overflow-wrap: anywhere; }
    .ps-bar-track { position: relative; height: 10px; }
    .ps-bar-fill { height: 100%; min-width: 2px; background: var(--c-primary); border-radius: 0 4px 4px 0; }
    .ps-bar-value { font-size: 12px; font-weight: 600; color: var(--c-fg); text-align: right; font-variant-numeric: tabular-nums; }
    .ps-bar-value.is-hidden { font-weight: 500; color: var(--c-fg-muted); }

    /* ── Kolom per bulan (tren) ── */
    .ps-cols { position: relative; display: flex; align-items: flex-end; gap: 6px; height: 170px; padding-top: 20px; border-bottom: 1px solid var(--c-border-strong); }
    .ps-col { position: relative; flex: 1; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; cursor: default; }
    .ps-col-fill { width: min(28px, 70%); background: var(--c-primary); border-radius: 4px 4px 0 0; }
    .ps-col-fill.is-zero { height: 0 !important; }
    .ps-col-val { position: absolute; font-size: 11px; font-weight: 600; color: var(--c-fg); transform: translateY(-16px); font-variant-numeric: tabular-nums; }
    .ps-col:hover .ps-col-fill, .ps-col:focus .ps-col-fill { opacity: .8; }
    .ps-col-labels { display: flex; gap: 6px; margin-top: 6px; }
    .ps-col-labels span { flex: 1; text-align: center; font-size: 11px; color: var(--c-fg-muted); white-space: nowrap; overflow: hidden; }

    /* Tooltip bersama untuk batang & kolom */
    .ps-tip { position: fixed; z-index: 1060; pointer-events: none; padding: 6px 10px; border-radius: 8px; background: #0D0D12; color: #ffffff; font-size: 12px; line-height: 1.4; box-shadow: 0 6px 18px rgba(0,0,0,.18); white-space: nowrap; }
    .ps-tip[hidden] { display: none; }

    /* ── Tabel ── */
    .ps-table { width: 100%; border-collapse: collapse; }
    .ps-table th { text-align: left; padding: 9px 12px; font-size: 11px; font-weight: 600; color: var(--c-fg-muted); background: #FAFAFA; border-bottom: 1px solid var(--c-border); white-space: nowrap; }
    .ps-table td { padding: 11px 12px; font-size: 13px; color: var(--c-fg); border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
    .ps-table tr:last-child td { border-bottom: none; }
    .ps-table .is-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .ps-table a { color: var(--c-fg); font-weight: 600; text-decoration: none; }
    .ps-table a:hover { color: var(--c-primary); }
    .ps-meta { display: block; font-size: 11px; color: var(--c-fg-muted); margin-top: 2px; }
    .ps-scroll { overflow-x: auto; }

    .ps-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .ps-periode { display: flex; align-items: center; gap: 8px; margin: 0; }
    .ps-periode label { font-size: 12px; color: var(--c-fg-muted); white-space: nowrap; }
    .ps-catatan { font-size: 12px; color: var(--c-fg-muted); line-height: 1.55; margin: 0; }

    @media (max-width: 1100px) { .ps-tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 860px) { .ps-grid { grid-template-columns: 1fr; } }
    @media (max-width: 480px) { .ps-tiles { grid-template-columns: 1fr; } .ps-col-labels span:nth-child(even) { visibility: hidden; } }
</style>

<x-manajemenmahasiswa::ui.page-header bordered title="Statistik Pengaduan">
    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="kf-back" title="Kembali" aria-label="Kembali ke daftar pengaduan">
            <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
        </a>
    </x-slot:leading>
    Pemantauan antrian dan bahan evaluasi mutu Layanan Pengaduan.
</x-manajemenmahasiswa::ui.page-header>

<div class="ps-wrap">
    <nav class="ps-tabs" aria-label="Jenis statistik">
        <a href="{{ $urlTab('antrian') }}" class="ps-tab {{ $tab === 'antrian' ? 'is-active' : '' }}" @if($tab === 'antrian') aria-current="page" @endif>Antrian Kerja</a>
        <a href="{{ $urlTab('evaluasi') }}" class="ps-tab {{ $tab === 'evaluasi' ? 'is-active' : '' }}" @if($tab === 'evaluasi') aria-current="page" @endif>Evaluasi Mutu</a>
    </nav>

    @if ($tab === 'antrian')
        {{-- ═══════════════ ANTRIAN KERJA ═══════════════ --}}
        <div class="ps-tiles">
            <div class="ps-tile">
                <div class="ps-tile-label">Belum dibuka</div>
                <div class="ps-tile-value">{{ $op['belum_dibuka'] }}</div>
                <div class="ps-tile-note">dari {{ $op['belum_tercatat'] }} pengaduan yang belum tercatat</div>
            </div>
            <div class="ps-tile">
                <div class="ps-tile-label">Lewat {{ Stat::BATAS_HARI }} hari, belum tercatat</div>
                <div class="ps-tile-value">{{ $op['lewat_batas'] }}</div>
                @if ($op['lewat_batas'] > 0)
                    <div class="ps-flag"><x-manajemenmahasiswa::ui.icon name="alert-triangle" size="13" /> Perlu didahulukan</div>
                @else
                    <div class="ps-tile-note">Tidak ada yang tertunda</div>
                @endif
            </div>
            <div class="ps-tile">
                <div class="ps-tile-label">Perundungan/pelecehan belum tercatat</div>
                <div class="ps-tile-value">{{ $op['ppks_belum'] }}</div>
                @if ($op['ppks_belum'] > 0)
                    <div class="ps-flag"><x-manajemenmahasiswa::ui.icon name="alert-triangle" size="13" /> Pertimbangkan teruskan ke Satgas PPKS</div>
                @else
                    <div class="ps-tile-note">Kategori Tindakan Tidak Menyenangkan</div>
                @endif
            </div>
            <div class="ps-tile">
                <div class="ps-tile-label">Masuk 7 hari terakhir</div>
                <div class="ps-tile-value">{{ $op['masuk_7_hari'] }}</div>
                <div class="ps-tile-note">
                    @if ($selisih7 === 0)
                        Sama dengan 7 hari sebelumnya
                    @else
                        {{ $selisih7 > 0 ? '+' . $selisih7 : $selisih7 }} dibanding 7 hari sebelumnya ({{ $op['masuk_7_sebelumnya'] }})
                    @endif
                </div>
            </div>
        </div>

        <div class="ps-card">
            <div class="ps-card-head">
                <h2 class="ps-card-title">Perlu ditindaklanjuti</h2>
                <p class="ps-card-sub">Pengaduan belum tercatat, dari yang paling lama menunggu.</p>
            </div>
            @if (empty($op['antrian']))
                <div class="ps-card-body"><div class="ps-empty">Semua pengaduan sudah tercatat.</div></div>
            @else
                <div class="ps-scroll">
                    <table class="ps-table">
                        <thead>
                            <tr>
                                <th>Pengaduan</th>
                                <th>Kategori</th>
                                <th>Dibuka</th>
                                <th>Kontak pelapor</th>
                                <th class="is-num">Menunggu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($op['antrian'] as $t)
                                <tr>
                                    <td>
                                        <a href="{{ route('manajemenmahasiswa.pengaduan.show', $t['id']) }}">{{ \Illuminate\Support\Str::limit($t['judul'], 60) }}</a>
                                        <span class="ps-meta">#{{ $t['id'] }}{{ $t['anonim'] ? ' · Anonim' : '' }}</span>
                                    </td>
                                    <td><span class="pgd-pill kategori" title="{{ $t['kategori'] }}">{{ \Illuminate\Support\Str::limit($t['kategori'], 28) }}</span></td>
                                    <td>{{ $t['dibuka'] ? 'Sudah' : 'Belum' }}</td>
                                    <td>{{ $t['kontak'] ? 'Bisa dihubungi' : 'Tidak ada kontak' }}</td>
                                    <td class="is-num">
                                        {{ $t['umur_hari'] }} hari
                                        @if ($t['umur_hari'] > Stat::BATAS_HARI)
                                            <span class="ps-flag" style="display: flex; justify-content: flex-end;"><x-manajemenmahasiswa::ui.icon name="alert-triangle" size="12" /> Lewat batas</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="ps-grid">
            <div class="ps-card">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Belum tercatat per kategori</h2>
                    <p class="ps-card-sub">Untuk meneruskan ke unit yang menangani.</p>
                </div>
                <div class="ps-card-body">
                    @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => $op['belum_per_kategori'], 'satuan' => 'pengaduan'])
                </div>
            </div>
            <div class="ps-card">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Kontak pelapor (belum tercatat)</h2>
                    <p class="ps-card-sub">Pengaduan biasa selalu bisa dihubungi; pengaduan anonim hanya bila pelapor mengisi email.</p>
                </div>
                <div class="ps-card-body">
                    @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => [
                        ['label' => 'Bisa dihubungi', 'nilai' => $op['belum_kontak']['bisa']],
                        ['label' => 'Tidak ada kontak', 'nilai' => $op['belum_kontak']['tidak']],
                    ], 'satuan' => 'pengaduan', 'tampilNol' => true])
                </div>
            </div>
        </div>
    @else
        {{-- ═══════════════ EVALUASI MUTU ═══════════════ --}}
        <div class="ps-toolbar">
            <p class="ps-catatan">Periode: <strong>{{ $ev['label_periode'] }}</strong> · angka gabungan untuk bahan evaluasi, tanpa membuka isi pengaduan.</p>
            <form method="GET" action="{{ route('manajemenmahasiswa.pengaduan.statistik') }}" class="ps-periode">
                <input type="hidden" name="tab" value="evaluasi">
                <label for="psPeriode">Periode</label>
                <x-manajemenmahasiswa::ui.select name="periode" id="psPeriode" :block="false" :minWidth="170" onchange="this.form.submit()">
                    @foreach (Stat::PERIODE as $nilai => $label)
                        <option value="{{ $nilai }}" @selected($periode === $nilai)>{{ $label }}</option>
                    @endforeach
                </x-manajemenmahasiswa::ui.select>
            </form>
        </div>

        <div class="ps-tiles">
            <div class="ps-tile">
                <div class="ps-tile-label">Pengaduan masuk</div>
                <div class="ps-tile-value">{{ $ev['total'] }}</div>
                <div class="ps-tile-note">{{ $ev['label_periode'] }}</div>
            </div>
            <div class="ps-tile">
                <div class="ps-tile-label">Tercatat dalam {{ Stat::BATAS_HARI }} hari</div>
                <div class="ps-tile-value">{{ $ev['persen_tepat'] === null ? '—' : $ev['persen_tepat'] }}@if($ev['persen_tepat'] !== null)<small>%</small>@endif</div>
                <div class="ps-tile-note">{{ $ev['tepat_waktu'] }} dari {{ $ev['dinilai'] }} pengaduan yang sudah bisa dinilai</div>
            </div>
            <div class="ps-tile">
                <div class="ps-tile-label">Rata-rata sampai tercatat</div>
                <div class="ps-tile-value">
                    @if ($ev['rata_hari'] === null)
                        —
                    @else
                        {{ $ev['rata_hari'] < 1 ? '< 1' : str_replace('.', ',', $ev['rata_hari']) }}<small> hari</small>
                    @endif
                </div>
                <div class="ps-tile-note">Sejak dikirim sampai pertama kali ditandai tercatat</div>
            </div>
            <div class="ps-tile">
                <div class="ps-tile-label">Dikirim anonim</div>
                <div class="ps-tile-value">{{ $ev['persen_anonim'] === null ? '—' : $ev['persen_anonim'] }}@if($ev['persen_anonim'] !== null)<small>%</small>@endif</div>
                <div class="ps-tile-note">{{ $ev['anonim'] }} pengaduan · tinggi = pelapor kurang nyaman memakai nama</div>
            </div>
        </div>

        <div class="ps-card">
            <div class="ps-card-head">
                <h2 class="ps-card-title">Pengaduan masuk per bulan</h2>
                <p class="ps-card-sub">Arahkan kursor ke batang untuk melihat jumlahnya.</p>
            </div>
            <div class="ps-card-body">
                @php
                    $bulan = $ev['per_bulan'];
                    $nilaiBulan = array_column($bulan, 'nilai');
                    $maks = max(1, max($nilaiBulan ?: [0]));
                    $idxMaks = array_search(max($nilaiBulan ?: [0]), $nilaiBulan, true);
                @endphp
                @if ($ev['total'] === 0)
                    <div class="ps-empty">Belum ada pengaduan pada periode ini.</div>
                @else
                    <div class="ps-cols" role="img" aria-label="Pengaduan masuk per bulan, {{ $ev['label_periode'] }}">
                        @foreach ($bulan as $i => $b)
                            <div class="ps-col" tabindex="0" data-tip="{{ $b['lengkap'] }}: {{ $b['nilai'] }} pengaduan">
                                {{-- Label angka hanya di bulan tertinggi & bulan terakhir; sisanya lewat tooltip. --}}
                                @if ($b['nilai'] > 0 && ($i === $idxMaks || $loop->last))
                                    <span class="ps-col-val" style="bottom: {{ $b['nilai'] / $maks * 100 }}%;">{{ $b['nilai'] }}</span>
                                @endif
                                <div class="ps-col-fill {{ $b['nilai'] === 0 ? 'is-zero' : '' }}" style="height: {{ $b['nilai'] / $maks * 100 }}%;"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="ps-col-labels" aria-hidden="true">
                        @foreach ($bulan as $b)<span>{{ $b['label'] }}</span>@endforeach
                    </div>
                    <table class="pgd-sr-only">
                        <caption>Pengaduan masuk per bulan</caption>
                        <tr><th>Bulan</th><th>Jumlah</th></tr>
                        @foreach ($bulan as $b)<tr><td>{{ $b['lengkap'] }}</td><td>{{ $b['nilai'] }}</td></tr>@endforeach
                    </table>
                @endif
            </div>
        </div>

        <div class="ps-grid">
            <div class="ps-card">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Per kategori</h2>
                    <p class="ps-card-sub">Bidang layanan yang paling sering dikeluhkan.</p>
                </div>
                <div class="ps-card-body">
                    @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => $ev['per_kategori'], 'satuan' => 'pengaduan'])
                </div>
            </div>

            <div class="ps-card">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Ketersediaan bukti</h2>
                    <p class="ps-card-sub">Seberapa kuat laporan untuk ditindaklanjuti. Laporan tanpa bukti tetap dicatat.</p>
                </div>
                <div class="ps-card-body">
                    @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => $ev['bukti'], 'satuan' => 'pengaduan', 'tampilNol' => true])
                </div>
            </div>

            <div class="ps-card is-wide">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Masalah berulang</h2>
                    <p class="ps-card-sub">Lokasi, objek, mata kuliah, atau unit yang disebut di dua pengaduan atau lebih — kandidat utama perbaikan.</p>
                </div>
                @if (empty($ev['berulang']))
                    <div class="ps-card-body"><div class="ps-empty">Belum ada isian yang muncul lebih dari sekali pada periode ini.</div></div>
                @else
                    <div class="ps-scroll">
                        <table class="ps-table">
                            <thead>
                                <tr><th>Disebut</th><th>Jenis isian</th><th>Kategori terbanyak</th><th class="is-num">Jumlah</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($ev['berulang'] as $r)
                                    <tr>
                                        <td><strong>{{ $r['teks'] }}</strong></td>
                                        <td>{{ $r['isian'] }}</td>
                                        <td>{{ $r['kategori'] }}</td>
                                        <td class="is-num">{{ $r['nilai'] }}×</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="ps-card">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Per angkatan pelapor</h2>
                    <p class="ps-card-sub">Termasuk pengaduan anonim. Angka di bawah {{ Stat::MIN_TAMPIL }} disamarkan.</p>
                </div>
                <div class="ps-card-body">
                    @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => $ev['per_angkatan'], 'satuan' => 'pengaduan'])
                </div>
            </div>

            <div class="ps-card">
                <div class="ps-card-head">
                    <h2 class="ps-card-title">Status pihak terlapor</h2>
                    <p class="ps-card-sub">Kategori Keamanan dan Tindakan Tidak Menyenangkan. Angka di bawah {{ Stat::MIN_TAMPIL }} disamarkan.</p>
                </div>
                <div class="ps-card-body">
                    @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => $ev['pihak_terlapor'], 'satuan' => 'pengaduan'])
                </div>
            </div>
        </div>
    @endif
</div>

<div class="ps-tip" id="psTip" role="tooltip" hidden></div>
<script>
    (function () {
        var tip = document.getElementById('psTip');
        if (!tip) return;

        function tampil(el) {
            tip.textContent = el.dataset.tip;
            tip.hidden = false;
            var r = el.getBoundingClientRect();
            var x = Math.min(window.innerWidth - tip.offsetWidth - 8, Math.max(8, r.left + r.width / 2 - tip.offsetWidth / 2));
            var y = r.top - tip.offsetHeight - 8;
            tip.style.left = x + 'px';
            tip.style.top = (y < 8 ? r.bottom + 8 : y) + 'px';
        }
        function sembunyi() { tip.hidden = true; }

        document.querySelectorAll('[data-tip]').forEach(function (el) {
            el.addEventListener('mouseenter', function () { tampil(el); });
            el.addEventListener('focus', function () { tampil(el); });
            el.addEventListener('mouseleave', sembunyi);
            el.addEventListener('blur', sembunyi);
        });
        window.addEventListener('scroll', sembunyi, true);
    })();
</script>

</x-manajemenmahasiswa::layouts.admin>
