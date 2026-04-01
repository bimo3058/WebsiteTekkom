{{-- profile/partials/stats-card.blade.php --}}
@php $user = auth()->user(); @endphp

@if($user->hasRole('mahasiswa') && $user->student)
@php
    $kompreCount = \App\Models\BsKompreSession::where('user_id', $user->id)->count();
    $kompreBest  = \App\Models\BsKompreSession::where('user_id', $user->id)->max('score') ?? 0;
    // Jumlah capstone grup aktif (status bukan DONE/CANCELLED)
    $capstoneCount = \Modules\Capstone\Models\CapstoneGroupMember::where('student_id', $user->student->id)
        ->whereHas('group', fn($q) => $q->whereNotIn('status', ['DONE','CANCELLED']))
        ->count();
@endphp
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">bar_chart</span>
        <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Statistik Akademik</h3>
    </div>
    <div class="p-4 grid grid-cols-3 gap-3">
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $kompreCount }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Ujian<br>Kompre</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ number_format($kompreBest, 0) }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Skor<br>Terbaik</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $capstoneCount }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Capstone<br>Aktif</p>
        </div>
    </div>
</div>

@elseif($user->hasRole('dosen') && $user->lecturer)
@php
    $mkCount      = \App\Models\BsDosenPengampuMk::where('user_id', $user->id)->distinct('mk_id')->count('mk_id');
    $rpsCount     = \App\Models\BsRps::where('dosen_id', $user->id)->count();
    $capstoneLoad = \Modules\Capstone\Models\CapstoneSupervision::where('lecturer_id', $user->lecturer->id)->count();
    $soalCount    = \App\Models\BsPertanyaan::whereHas('rps.dosen', fn($q) => $q->where('dosen_id', $user->id))->count();
@endphp
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
        <span class="material-symbols-outlined text-purple-500 text-[18px]">bar_chart</span>
        <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Statistik Akademik</h3>
    </div>
    <div class="p-4 grid grid-cols-2 gap-3">
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $mkCount }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Mata Kuliah<br>Diampu</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $rpsCount }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">RPS<br>Diajukan</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $capstoneLoad }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Capstone<br>Dibimbing</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $soalCount }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Soal<br>Dibuat</p>
        </div>
    </div>
</div>

@elseif($user->hasRole('superadmin'))
@php
    $totalUsers     = \App\Models\User::whereNull('deleted_at')->count();
    $suspended      = \App\Models\User::whereNotNull('suspended_at')->count();
    $loginToday     = \App\Models\AuditLog::where('action', 'LOGIN')->whereDate('created_at', today())->count();
    $activeModules  = \App\Models\SystemModule::where('is_active', true)->count();
@endphp
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
        <span class="material-symbols-outlined text-red-500 text-[18px]">monitoring</span>
        <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Statistik Sistem</h3>
    </div>
    <div class="p-4 grid grid-cols-2 gap-3">
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $totalUsers }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Total<br>User Aktif</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold {{ $suspended > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $suspended }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">User<br>Suspended</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $loginToday }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Login<br>Hari Ini</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-3 text-center">
            <p class="text-xl font-bold text-slate-800">{{ $activeModules }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight">Modul<br>Aktif</p>
        </div>
    </div>
</div>
@endif