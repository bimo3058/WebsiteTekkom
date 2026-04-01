<x-app-layout>
<x-sidebar :user="auth()->user()">
    {{-- 
        Menambahkan 'h-screen overflow-hidden' untuk mengunci layar utama 
        dan 'scrollbar-hide' (opsional) untuk estetika.
    --}}
    <div class="h-screen overflow-hidden bg-slate-50 flex flex-col">
        
        {{-- Area Scrollable Internal --}}
        <div class="flex-grow overflow-y-auto p-4 sm:p-6 custom-scrollbar">
            <div class="max-w-full mx-auto">

                {{-- Header --}}
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            <span class="w-1 h-5 bg-blue-600 rounded-full"></span>
                            Profil Akun
                        </h2>
                        <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-0.5">
                            Kelola identitas dan keamanan akses Anda
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-amber-700">
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        <p class="text-[11px] font-medium tracking-tight">Data SSO tidak dapat diubah via aplikasi ini</p>
                    </div>
                </div>

                {{-- Grid Layout --}}
                <div class="grid grid-cols-1 lg:grid-cols-[270px_1fr_290px] gap-4 items-stretch pb-6">

                    {{-- KOLOM 1 --}}
                    <div class="flex flex-col h-full">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full">
                            <div class="flex flex-col items-center pt-6 pb-4 px-4">
                                @php
                                    $name     = auth()->user()->name;
                                    $initials = strtoupper(substr($name, 0, 1));
                                    $sp       = strpos($name, ' ');
                                    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
                                    $rc = auth()->user()->hasRole('superadmin') ? 'bg-red-100 text-red-700'
                                        : (auth()->user()->hasRole('dosen')     ? 'bg-purple-100 text-purple-700'
                                        : (auth()->user()->hasRole('mahasiswa') ? 'bg-blue-100 text-blue-700'
                                        : 'bg-amber-100 text-amber-700'));
                                @endphp
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold {{ $rc }} shadow-inner">
                                    {{ $initials }}
                                </div>
                                <p class="mt-3 text-[14px] font-bold text-slate-800 text-center leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-400 mt-1 text-center truncate w-full px-2">{{ auth()->user()->email }}</p>
                                
                                <div class="mt-3 flex flex-wrap gap-1 justify-center">
                                    @foreach(auth()->user()->roles as $role)
                                    <span class="text-[9px] font-black px-2 py-0.5 rounded-lg border uppercase tracking-tighter
                                        @if(str_contains($role->name,'superadmin')) bg-red-50 text-red-600 border-red-100
                                        @elseif(str_contains($role->name,'dosen'))  bg-purple-50 text-purple-600 border-purple-100
                                        @elseif(str_contains($role->name,'mahasiswa')) bg-blue-50 text-blue-600 border-blue-100
                                        @else bg-amber-50 text-amber-600 border-amber-100 @endif">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="px-4 pb-6 border-t border-slate-100 pt-5 space-y-5 flex-grow">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Data Akademik</p>
                                    <div class="space-y-1">
                                        @if(auth()->user()->hasRole('mahasiswa') && auth()->user()->student)
                                            @include('profile.partials._info-row', ['label' => 'NIM', 'value' => auth()->user()->student->student_number])
                                            @include('profile.partials._info-row', ['label' => 'Angkatan', 'value' => auth()->user()->student->cohort_year])
                                        @elseif(auth()->user()->hasRole('dosen') && auth()->user()->lecturer)
                                            @include('profile.partials._info-row', ['label' => 'NIP', 'value' => auth()->user()->lecturer->employee_number])
                                        @endif
                                        @include('profile.partials._info-row', [
                                            'label' => 'Akses Terakhir',
                                            'value' => auth()->user()->last_login?->diffForHumans() ?? 'Baru saja'
                                        ])
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-slate-50">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Jejak Aktivitas</p>
                                    @include('profile.partials.activity-log')
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM 2 --}}
                    <div class="flex flex-col h-full">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full">
                            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]">manage_accounts</span>
                                <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Pengaturan Akun</h3>
                                <span class="ml-auto text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">Bisa diubah</span>
                            </div>
                            <div class="p-6 flex-grow">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM 3 --}}
                    <div class="flex flex-col gap-4 h-full">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col flex-grow">
                            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                                <span class="material-symbols-outlined text-purple-600 text-[18px]">lock_person</span>
                                <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Keamanan</h3>
                            </div>
                            <div class="p-5 flex-grow">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-sidebar>
</x-app-layout>