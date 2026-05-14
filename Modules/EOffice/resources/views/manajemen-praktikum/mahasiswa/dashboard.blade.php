<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Mahasiswa — Manajemen Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Banner: Belum terdaftar → input kode --}}
@if($belumTerdaftar)
<div class="rounded-[14px] px-6 py-5 text-white flex-shrink-0"
     style="background:linear-gradient(120deg,#0B266E 0%,#1a3a8f 100%);">
    <div class="text-[18px] font-bold tracking-tight mb-1">Selamat Datang, {{ $name }}!</div>
    <div class="text-[13px] opacity-75 mb-4">Masukkan kode praktikum untuk bergabung ke kelas praktikum Anda.</div>
    <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.masuk') }}" class="flex gap-2 max-w-md">
        @csrf
        <input type="text" name="kode" placeholder="Masukkan kode praktikum..."
               class="flex-1 rounded-[9px] px-4 py-[10px] text-[13px] text-[#0D0D12] border-none focus:outline-none focus:ring-2 focus:ring-white/50"
               value="{{ old('kode') }}" autofocus>
        <button type="submit"
                class="px-5 py-[10px] rounded-[9px] font-semibold text-[13px] border-none cursor-pointer"
                style="background:rgba(255,255,255,0.95); color:#0B266E;">Gabung</button>
    </form>
    @error('kode')<div class="text-[12px] text-red-300 mt-2">{{ $message }}</div>@enderror
</div>

<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-8 text-center flex-shrink-0">
    <div class="text-[#A4ABB8] text-[13px]">Anda belum terdaftar di praktikum manapun.<br>Masukkan kode di atas untuk memulai.</div>
</div>

@else

{{-- Welcome Banner (sudah terdaftar) --}}
<div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
     style="background:linear-gradient(120deg,#7C5309 0%,#D39C3D 100%);">
    <div>
        <div class="text-[18px] font-bold tracking-tight">Halo, {{ $name }}!</div>
        <div class="text-[12px] opacity-75 mt-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($terdaftarDi) · {{ $terdaftarDi->nama }} @endif
        </div>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        @php
            $pct = $absensiStat['total'] > 0 ? round($absensiStat['hadir'] / $absensiStat['total'] * 100) : 0;
        @endphp
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $pct }}%</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Kehadiran</div>
        </div>
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ count($tugasMendatang) }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Tugas Pending</div>
        </div>
    </div>
</div>

{{-- Info Praktikum --}}
@if($terdaftarDi)
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
    <div class="flex items-start justify-between">
        <div>
            <div class="text-[11px] font-semibold text-[#A4ABB8] uppercase tracking-wider mb-1">Praktikum Aktif</div>
            <div class="text-[18px] font-bold text-[#0D0D12]">{{ $terdaftarDi->nama }}</div>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-[12px] text-[#666D80]">Kode: <span class="font-semibold text-[#0B266E]">{{ $terdaftarDi->kode ?? '—' }}</span></span>
                <span class="text-[12px] text-[#666D80]">Dosen: <span class="font-semibold text-[#0D0D12]">{{ $terdaftarDi->dosen?->name ?? '—' }}</span></span>
                @if($terdaftarDi->koordinator)
                <span class="text-[12px] text-[#666D80]">Koor: <span class="font-semibold text-[#0D0D12]">{{ $terdaftarDi->koordinator->name }}</span></span>
                @endif
            </div>
        </div>
        <span class="text-[11px] font-semibold px-2 py-[3px] rounded-full bg-[#DDF2EE] text-[#174E43]">Aktif</span>
    </div>
</div>
@endif

{{-- Progress Kehadiran --}}
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div class="font-bold text-[14px] text-[#0D0D12]">Statistik Kehadiran</div>
            <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $absensiStat['hadir'] }} hadir dari {{ $absensiStat['total'] }} sesi</div>
        </div>
        <div class="text-[24px] font-bold" style="color:{{ $pct >= 75 ? '#40C4AA' : '#DF1C41' }};">{{ $pct }}%</div>
    </div>
    <div class="w-full bg-[#F0F1F4] rounded-full h-[8px]">
        <div class="h-[8px] rounded-full" style="width:{{ $pct }}%; background:{{ $pct >= 75 ? 'linear-gradient(90deg,#40C4AA,#0D6B55)' : 'linear-gradient(90deg,#DF1C41,#f87171)' }};"></div>
    </div>
    @if($pct < 75)
    <div class="mt-2 text-[11px] text-[#DF1C41] font-medium">⚠ Kehadiran di bawah 75% — risiko tidak lulus praktikum</div>
    @endif
</div>

{{-- Bottom Grid: Tugas + Nilai + Pengumuman --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Tugas Mendatang --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[15px] text-[#0D0D12]">Tugas Mendatang</div>
            <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}"
               class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($tugasMendatang as $t)
            @php
                $dl = \Carbon\Carbon::parse($t['deadline']);
                $sisa = now()->diffInDays($dl, false);
                $sudah = $t['sudah_kumpul'];
            @endphp
            <div class="px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $t['judul'] }}</div>
                        <div class="text-[11px] mt-[2px]" style="color:{{ $sisa <= 2 && !$sudah ? '#DF1C41' : '#666D80' }};">
                            Deadline: {{ $dl->format('d M Y, H:i') }}
                        </div>
                    </div>
                    @if($sudah)
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43] flex-shrink-0">Dikumpul</span>
                    @else
                    <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}"
                       class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F9ECCB] text-[#7C5309] no-underline flex-shrink-0 hover:bg-[#f0e0b0]">Kumpul</a>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Tidak ada tugas mendatang 🎉</div>
            @endforelse
        </div>
    </div>

    {{-- Nilai --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[15px] text-[#0D0D12]">Nilai Praktikum</div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($nilaiList as $n)
            <div class="flex items-center justify-between px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                <div class="text-[13px] font-medium text-[#0D0D12]">{{ $n->modul ?? 'Modul' }}</div>
                @php
                    $nilai = $n->nilai_akhir ?? null;
                    $nilaiColor = !$nilai ? '#A4ABB8' : ($nilai >= 75 ? '#40C4AA' : ($nilai >= 50 ? '#D39C3D' : '#DF1C41'));
                @endphp
                <span class="text-[16px] font-bold" style="color:{{ $nilaiColor }};">
                    {{ $nilai ?? '—' }}
                </span>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Nilai belum tersedia.</div>
            @endforelse
        </div>
    </div>

    {{-- Pengumuman --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[15px] text-[#0D0D12]">Pengumuman</div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumuman as $p)
            <div class="px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                <div class="text-[13px] font-semibold text-[#0D0D12]">{{ $p->judul }}</div>
                <div class="text-[12px] text-[#666D80] mt-[2px] line-clamp-2">{{ $p->konten }}</div>
                <div class="text-[11px] text-[#A4ABB8] mt-[5px]">{{ $p->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Belum ada pengumuman.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Status Pendaftaran Asprak --}}
@if($statusAsprak)
<div class="bg-white border border-[#DFE1E7] rounded-[14px] px-5 py-4 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[13px] font-bold text-[#0D0D12]">Status Pendaftaran Asisten Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $statusAsprak->praktikum?->nama ?? '—' }}</div>
    </div>
    @if($statusAsprak->status === 'pending')
    <span class="text-[12px] font-semibold px-3 py-[5px] rounded-full bg-[#F9ECCB] text-[#7C5309]">⏳ Menunggu Review</span>
    @elseif($statusAsprak->status === 'approved')
    <span class="text-[12px] font-semibold px-3 py-[5px] rounded-full bg-[#DDF2EE] text-[#174E43]">✓ Diterima</span>
    @else
    <span class="text-[12px] font-semibold px-3 py-[5px] rounded-full bg-[#FADAE1] text-[#7C1028]">✕ Ditolak</span>
    @endif
</div>
@else
<div class="bg-white border border-[#DFE1E7] rounded-[14px] px-5 py-4 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[13px] font-bold text-[#0D0D12]">Tertarik jadi Asisten Praktikum?</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Daftarkan diri Anda sebagai calon asprak semester ini.</div>
    </div>
    <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index') }}"
       class="text-[13px] font-semibold px-4 py-[8px] rounded-[9px] bg-[#0B266E] text-white no-underline hover:bg-[#0a1f5c]">Daftar Sekarang</a>
</div>
@endif

@endif {{-- end if belumTerdaftar --}}

</x-eoffice::manajemen-praktikum.layout>
