<x-eoffice::manajemen-praktikum.layout pageTitle="Nilai Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)

{{-- Section: Pilih Praktikum --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 14px;display:block;">
            <rect x="3" y="3" width="18" height="18" rx="3"/>
            <path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Pilih Praktikum</div>
        <div style="font-size:13px;color:#666D80;margin-bottom:24px;">Pilih praktikum yang ingin dilihat nilainya.</div>

        @if($praktikums->isEmpty())
        <div style="font-size:13px;color:#808897;">Kamu belum mengampu praktikum apapun.</div>
        @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
               class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]" style="text-decoration:none;border:1px solid #DFE1E7;">
                <span style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $p->nama }}</span>
                <span style="font-size:11px;color:#666D80;">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

@else

{{-- Page Header --}}
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

{{-- Section: Rekapitulasi --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Rekapitulasi Nilai Mahasiswa</span>
    <span class="sec-rule"></span>
    @if(!$nilaiList->isEmpty())
    <span class="mp-badge navy sm">{{ $nilaiList->count() }} mahasiswa</span>
    @endif
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Nilai</span>
        <div class="right" style="font-size:11px;color:#666D80;">{{ $nilaiList->count() }} mahasiswa terdaftar</div>
    </div>

    @if($nilaiList->isEmpty())
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
            <rect x="3" y="3" width="18" height="18" rx="3"/>
            <path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
        <div style="font-size:13px;color:#666D80;">Belum ada mahasiswa terdaftar di praktikum ini.</div>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
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
                @php
                    $namaParts = explode(' ', $row['mahasiswa']?->name ?? 'UN');
                    $initials = strtoupper(substr($namaParts[0] ?? 'U', 0, 1) . substr($namaParts[1] ?? $namaParts[0] ?? 'N', 0, 1));
                    $avColors = ['sky','navy','green','yellow','violet','red'];
                    $avColor = $avColors[crc32($row['mahasiswa']?->email ?? '') % count($avColors)];
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:500;color:#0D0D12;">{{ $row['mahasiswa']?->name ?? '-' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $row['mahasiswa']?->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!is_null($row['nilai_tugas']))
                            <span style="font-weight:600;color:#0D0D12;">{{ number_format($row['nilai_tugas'], 1) }}</span>
                        @else
                            <span style="color:#808897;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!is_null($row['nilai_absensi']))
                            <span style="font-weight:600;color:#0D0D12;">{{ number_format($row['nilai_absensi'], 1) }}</span>
                        @else
                            <span style="color:#808897;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if(!is_null($row['nilai_akhir']))
                            @php $na = $row['nilai_akhir']; @endphp
                            @if($na >= 80)
                                <span class="mp-badge success sm"><span class="dot"></span>{{ number_format($na, 1) }}</span>
                            @elseif($na >= 60)
                                <span class="mp-badge warning sm"><span class="dot"></span>{{ number_format($na, 1) }}</span>
                            @else
                                <span class="mp-badge error sm"><span class="dot"></span>{{ number_format($na, 1) }}</span>
                            @endif
                        @else
                            <span style="color:#808897;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($row['dipublikasikan'])
                            <span class="mp-badge success sm"><span class="dot"></span>Dipublikasikan</span>
                        @elseif($row['disetujui_dosen'])
                            <span class="mp-badge sky sm"><span class="dot"></span>Disetujui</span>
                        @else
                            <span class="mp-badge neutral sm"><span class="dot"></span>Menunggu</span>
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
