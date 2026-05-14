<x-eoffice::manajemen-praktikum.layout pageTitle="Nilai Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)
<div class="bg-white rounded-[14px] border border-[#DFE1E7] px-6 py-10 text-center flex-shrink-0">
    <div class="text-[32px] mb-3">📊</div>
    <div class="text-[15px] font-semibold text-[#353849] mb-1">Pilih Praktikum</div>
    <div class="text-[13px] text-[#666D80] mb-5">Pilih praktikum yang ingin dilihat nilainya.</div>
    @if($praktikums->isEmpty())
        <div class="text-[13px] text-[#A4ABB8]">Kamu belum mengampu praktikum apapun.</div>
    @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
               class="flex items-center justify-between px-4 py-3 rounded-[10px] border border-[#DFE1E7] bg-[#F6F8FA] hover:bg-[#F0E6FA] hover:border-[#9B59B6] no-underline transition-colors">
                <span class="text-[13px] font-medium text-[#0D0D12]">{{ $p->nama }}</span>
                <span class="text-[11px] text-[#666D80]">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
    @endif
</div>
@else

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Nilai Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $praktikum->nama }} · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('eoffice.manprak.dosen.nilai.index', '0') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-[10px] text-[13px] font-semibold text-[#353849] bg-white border border-[#DFE1E7] no-underline hover:bg-[#F6F8FA] transition-colors">
            Ganti Praktikum
        </a>
        <form method="POST" action="{{ route('eoffice.manprak.dosen.nilai.approve', $praktikum->id) }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-[10px] text-[13px] font-semibold text-white cursor-pointer border-none"
                style="background:#6B21A8;"
                onclick="return confirm('Setujui dan publikasikan semua nilai di praktikum ini?')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Setujui & Publikasikan Semua
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-[14px] border border-[#DFE1E7] overflow-hidden flex-shrink-0">
    <div class="px-5 py-4 border-b border-[#DFE1E7]">
        <div class="text-[13px] font-semibold text-[#0D0D12]">Rekapitulasi Nilai Mahasiswa</div>
        <div class="text-[11px] text-[#666D80] mt-[2px]">{{ $nilaiList->count() }} mahasiswa terdaftar</div>
    </div>

    @if($nilaiList->isEmpty())
    <div class="px-5 py-10 text-center text-[13px] text-[#A4ABB8]">Belum ada mahasiswa terdaftar di praktikum ini.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-[#F6F8FA] border-b border-[#DFE1E7]">
                    <th class="text-left px-5 py-3 font-semibold text-[#353849]">Mahasiswa</th>
                    <th class="text-center px-4 py-3 font-semibold text-[#353849]">Nilai Tugas</th>
                    <th class="text-center px-4 py-3 font-semibold text-[#353849]">Nilai Absensi</th>
                    <th class="text-center px-4 py-3 font-semibold text-[#353849]">Nilai Akhir</th>
                    <th class="text-center px-4 py-3 font-semibold text-[#353849]">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F0F1F4]">
                @foreach($nilaiList as $row)
                <tr class="hover:bg-[#F6F8FA] transition-colors">
                    <td class="px-5 py-3">
                        <div class="font-medium text-[#0D0D12]">{{ $row['mahasiswa']?->name ?? '-' }}</div>
                        <div class="text-[11px] text-[#666D80]">{{ $row['mahasiswa']?->email ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if(!is_null($row['nilai_tugas']))
                            <span class="font-semibold text-[#0D0D12]">{{ number_format($row['nilai_tugas'], 1) }}</span>
                        @else
                            <span class="text-[#A4ABB8]">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if(!is_null($row['nilai_absensi']))
                            <span class="font-semibold text-[#0D0D12]">{{ number_format($row['nilai_absensi'], 1) }}</span>
                        @else
                            <span class="text-[#A4ABB8]">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if(!is_null($row['nilai_akhir']))
                            @php
                                $na = $row['nilai_akhir'];
                                $color = $na >= 80 ? '#40C4AA' : ($na >= 60 ? '#D39C3D' : '#DF1C41');
                                $bg    = $na >= 80 ? '#DDF2EE' : ($na >= 60 ? '#F9ECCB' : '#FADAE1');
                            @endphp
                            <span class="inline-block px-2 py-[2px] rounded-full text-[11px] font-bold"
                                  style="background:{{ $bg }}; color:{{ $color }};">
                                {{ number_format($na, 1) }}
                            </span>
                        @else
                            <span class="text-[#A4ABB8]">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($row['dipublikasikan'])
                            <span class="inline-block px-2 py-[2px] rounded-full text-[10px] font-bold bg-[#DDF2EE] text-[#174E43]">Dipublikasikan</span>
                        @elseif($row['disetujui_dosen'])
                            <span class="inline-block px-2 py-[2px] rounded-full text-[10px] font-bold bg-[#D1F0F9] text-[#106A97]">Disetujui</span>
                        @else
                            <span class="inline-block px-2 py-[2px] rounded-full text-[10px] font-bold bg-[#F6F8FA] text-[#A4ABB8]">Menunggu</span>
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