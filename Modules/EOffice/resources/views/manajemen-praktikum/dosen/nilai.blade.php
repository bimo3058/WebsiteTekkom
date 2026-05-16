<x-eoffice::manajemen-praktikum.layout pageTitle="Nilai Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)
<div class="mp-card flex-shrink-0" style="padding:40px;text-align:center;">
    <div style="font-size:32px;margin-bottom:12px;">📊</div>
    <div style="font-size:15px;font-weight:600;color:var(--c-fg);margin-bottom:4px;">Pilih Praktikum</div>
    <div style="font-size:13px;color:var(--c-fg-muted);margin-bottom:20px;">Pilih praktikum yang ingin dilihat nilainya.</div>
    @if($praktikums->isEmpty())
        <div style="font-size:13px;color:var(--c-fg-placeholder);">Kamu belum mengampu praktikum apapun.</div>
    @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
               class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]" style="text-decoration:none;border:1px solid var(--c-border);">
                <span style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $p->nama }}</span>
                <span style="font-size:11px;color:var(--c-fg-muted);">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
    @endif
</div>
@else

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Nilai Praktikum</h1>
        <p class="mp-page-sub">{{ $praktikum->nama }} · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.dosen.nilai.index', '0') }}" class="mp-btn secondary md" style="text-decoration:none;">Ganti Praktikum</a>
        <form method="POST" action="{{ route('eoffice.manprak.dosen.nilai.approve', $praktikum->id) }}">
            @csrf
            <button type="submit" class="mp-btn primary md"
                onclick="return confirm('Setujui dan publikasikan semua nilai di praktikum ini?')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Setujui & Publikasikan Semua
            </button>
        </form>
    </div>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <div>
            <span class="mp-card-title">Rekapitulasi Nilai Mahasiswa</span>
            <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $nilaiList->count() }} mahasiswa terdaftar</div>
        </div>
    </div>

    @if($nilaiList->isEmpty())
    <div style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada mahasiswa terdaftar di praktikum ini.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">Mahasiswa</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Nilai Tugas</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Nilai Absensi</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Nilai Akhir</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nilaiList as $row)
                <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                    <td style="padding:12px 20px;">
                        <div style="font-weight:500;color:var(--c-fg);">{{ $row['mahasiswa']?->name ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);">{{ $row['mahasiswa']?->email ?? '' }}</div>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!is_null($row['nilai_tugas']))
                            <span style="font-weight:600;color:var(--c-fg);">{{ number_format($row['nilai_tugas'], 1) }}</span>
                        @else
                            <span style="color:var(--c-fg-placeholder);">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!is_null($row['nilai_absensi']))
                            <span style="font-weight:600;color:var(--c-fg);">{{ number_format($row['nilai_absensi'], 1) }}</span>
                        @else
                            <span style="color:var(--c-fg-placeholder);">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!is_null($row['nilai_akhir']))
                            @php $na = $row['nilai_akhir']; @endphp
                            @if($na >= 80)
                                <span class="mp-badge success sm">{{ number_format($na, 1) }}</span>
                            @elseif($na >= 60)
                                <span class="mp-badge warning sm">{{ number_format($na, 1) }}</span>
                            @else
                                <span class="mp-badge error sm">{{ number_format($na, 1) }}</span>
                            @endif
                        @else
                            <span style="color:var(--c-fg-placeholder);">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($row['dipublikasikan'])
                            <span class="mp-badge success sm">Dipublikasikan</span>
                        @elseif($row['disetujui_dosen'])
                            <span class="mp-badge sky sm">Disetujui</span>
                        @else
                            <span class="mp-badge neutral sm">Menunggu</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
