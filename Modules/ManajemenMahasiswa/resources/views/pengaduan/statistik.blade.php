{{--
    Statistik Layanan Pengaduan (staf saja): bahan evaluasi mutu per periode —
    tren per bulan dan kategori (hanya angka gabungan).

    Grafik dibuat dengan HTML biasa (tanpa pustaka): batang satu warna (--c-primary)
    karena tugasnya membandingkan besaran, bukan membedakan seri. Tiap batang punya
    tooltip.
--}}
@php
    use Modules\ManajemenMahasiswa\Services\PengaduanStatistikService as Stat;

    $ev = $evaluasi;
@endphp

<x-manajemenmahasiswa::layouts.admin>

@include('manajemenmahasiswa::pengaduan.partials.palette')
@include('manajemenmahasiswa::pengaduan.partials.box-styles')

<style>
    .ps-wrap { display: flex; flex-direction: column; gap: 16px; flex-shrink: 0; font-family: 'Inter Tight', sans-serif; }

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

    .ps-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .ps-periode { display: flex; align-items: center; gap: 8px; margin: 0; }
    .ps-periode label { font-size: 12px; color: var(--c-fg-muted); white-space: nowrap; }
    .ps-catatan { font-size: 12px; color: var(--c-fg-muted); line-height: 1.55; margin: 0; }

    @media (max-width: 860px) { .ps-grid { grid-template-columns: 1fr; } }
    @media (max-width: 480px) { .ps-col-labels span:nth-child(even) { visibility: hidden; } }
</style>

<x-manajemenmahasiswa::ui.page-header bordered title="Statistik Pengaduan">
    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="kf-back" title="Kembali" aria-label="Kembali ke daftar pengaduan">
            <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
        </a>
    </x-slot:leading>
    Tren dan kategori pengaduan sebagai bahan evaluasi mutu layanan.
</x-manajemenmahasiswa::ui.page-header>

<div class="ps-wrap">
    <div class="ps-toolbar">
        <p class="ps-catatan">Periode: <strong>{{ $ev['label_periode'] }}</strong> · angka gabungan untuk bahan evaluasi, tanpa membuka isi pengaduan.</p>
        <form method="GET" action="{{ route('manajemenmahasiswa.pengaduan.statistik') }}" class="ps-periode">
            <label for="psPeriode">Periode</label>
            <x-manajemenmahasiswa::ui.select name="periode" id="psPeriode" :block="false" :minWidth="170" onchange="this.form.submit()">
                @foreach (Stat::PERIODE as $nilai => $label)
                    <option value="{{ $nilai }}" @selected($periode === $nilai)>{{ $label }}</option>
                @endforeach
            </x-manajemenmahasiswa::ui.select>
        </form>
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
        <div class="ps-card is-wide">
            <div class="ps-card-head">
                <h2 class="ps-card-title">Per kategori</h2>
                <p class="ps-card-sub">Bidang layanan yang paling sering dikeluhkan.</p>
            </div>
            <div class="ps-card-body">
                @include('manajemenmahasiswa::pengaduan.partials.statistik-bars', ['baris' => $ev['per_kategori'], 'satuan' => 'pengaduan'])
            </div>
        </div>

    </div>
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
