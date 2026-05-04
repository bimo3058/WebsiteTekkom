{{-- resources/views/profile/partials/stats-card.blade.php --}}
@php $user = auth()->user(); @endphp

@if($user->hasRole('mahasiswa') && $user->student)
@php
    $kompreCount   = \Modules\BankSoal\Models\KompreSession::where('user_id', $user->id)->count();
    $kompreBest    = \Modules\BankSoal\Models\KompreSession::where('user_id', $user->id)->max('score') ?? 0;
    $capstoneCount = \Modules\Capstone\Models\CapstoneGroupMember::where('student_id', $user->student->id)
        ->whereHas('group', fn($q) => $q->whereNotIn('status', ['DONE','CANCELLED']))
        ->count();
@endphp
<div class="bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden">
    <div class="px-5 py-3.5 border-b border-[#F0F1F4] flex items-center gap-2">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        <p class="text-[11px] font-semibold text-[#666D80] uppercase tracking-widest">Statistik Akademik</p>
    </div>
    <div class="p-4 grid grid-cols-3 gap-3">
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ $kompreCount }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">Ujian<br>Kompre</p>
        </div>
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ number_format($kompreBest, 0) }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">Skor<br>Terbaik</p>
        </div>
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ $capstoneCount }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">Capstone<br>Aktif</p>
        </div>
    </div>
</div>

@elseif($user->hasRole('dosen') && $user->lecturer)
@php
    $mkCount      = Modules\BankSoal\Models\DosenPengampuMk::where('user_id', $user->id)->distinct('mk_id')->count('mk_id');
    $rpsCount     = Modules\BankSoal\Models\Rps::where('dosen_id', $user->id)->count();
    $capstoneLoad = Modules\Capstone\Models\CapstoneSupervision::where('lecturer_id', $user->lecturer->id)->count();
    $soalCount    = Modules\BankSoal\Models\Pertanyaan::whereHas('rps.dosen', fn($q) => $q->where('dosen_id', $user->id))->count();
@endphp
<div class="bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden">
    <div class="px-5 py-3.5 border-b border-[#F0F1F4] flex items-center gap-2">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        <p class="text-[11px] font-semibold text-[#666D80] uppercase tracking-widest">Statistik Akademik</p>
    </div>
    <div class="p-4 grid grid-cols-2 gap-3">
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ $mkCount }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">Mata Kuliah<br>Diampu</p>
        </div>
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ $rpsCount }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">RPS<br>Diajukan</p>
        </div>
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ $capstoneLoad }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">Capstone<br>Dibimbing</p>
        </div>
        <div class="bg-[#F6F8FA] rounded-xl p-3 text-center">
            <p class="text-[20px] font-bold text-[#0D0D12] leading-tight">{{ $soalCount }}</p>
            <p class="text-[10px] text-[#A4ABB8] font-medium mt-1 leading-tight">Soal<br>Dibuat</p>
        </div>
    </div>
</div>
@endif
