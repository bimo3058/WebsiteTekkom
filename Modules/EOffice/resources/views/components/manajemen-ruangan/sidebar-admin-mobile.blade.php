@props(['user', 'rawSettings', 'currentRoute'])

@php
    $name = $user?->name ?? 'Admin';
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false)
        $initials .= strtoupper(substr($name, $sp + 1, 1));

    $isSuperadmin = $user && $user->hasRole('superadmin');
    
    $sb_adm_klg = $isSuperadmin || filter_var($rawSettings['sb_admin_kalenderglobal'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_jad = $isSuperadmin || filter_var($rawSettings['sb_admin_jadwalakademik'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_evt = $isSuperadmin || filter_var($rawSettings['sb_admin_event'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_set = $isSuperadmin || filter_var($rawSettings['sb_admin_persetujuan'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_ars = $isSuperadmin || filter_var($rawSettings['sb_admin_arsip'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_fas = $isSuperadmin || filter_var($rawSettings['sb_admin_manajemenfasilitas'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_rua = $isSuperadmin || filter_var($rawSettings['sb_admin_manajemenruangan'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_adm_pgt = $isSuperadmin || filter_var($rawSettings['sb_admin_pengaturan'] ?? true, FILTER_VALIDATE_BOOLEAN);

    // icons
    $iHome = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iCal = "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z";
    $iJadwalAkademik = "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253";
    $iPersetujuan = "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2";
    $iList = "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2";
    $iArsip = "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z";
    $iBook = "M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z";
    $iFasilitas = "M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z";
    $iKey = "M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L6.5 21.5H3v-3.5l1.5-1.5v-2l1.5-1.5 2-2 2.257-2.257A6 6 0 1121 9z";
    $iPengaturan = "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z";
    $iLogout = "M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.94L7.87 4.06C6.39 3.66 5 5.21 5 7.27v9.46C5 18.79 6.39 20.34 7.87 19.94l3.2-.87C12.19 18.76 13 17.42 13 15.86v-.59M11 12h8M19 12l-2.5-2.72M19 12l-2.5 2.72";
    $iBack = "M19 12H5M5 12l7-7M5 12l7 7";
@endphp

{{-- ── MOBILE BOTTOM NAV (ADMIN) ────────────────────── --}}
<div x-data="{ show: window.innerWidth < 768, openMenu: false, startY: 0 }"
    x-init="window.addEventListener('resize', () => show = window.innerWidth < 768)" x-show="show"
    style="display: none;"
    class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-[0_-4px_24px_rgba(0,0,0,0.06)] font-['Inter_Tight']">

    {{-- More Menu Drawer (Lainnya) --}}
    <div x-show="openMenu" class="fixed inset-0 z-[60] font-['Inter_Tight']" style="display: none;">
        {{-- Backdrop --}}
        <div @click="openMenu = false" x-show="openMenu"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/40"></div>

        {{-- Drawer --}}
        <div x-show="openMenu" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full" @touchstart="startY = $event.touches[0].clientY"
            @touchend="if ($event.changedTouches[0].clientY - startY > 80) openMenu = false"
            class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl max-h-[85vh] flex flex-col">

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-2 shrink-0">
                <div class="w-10 h-1.5 bg-slate-200 rounded-full"></div>
            </div>

            {{-- User Info --}}
            <div class="flex items-center gap-3 px-5 pb-4 border-b border-slate-100 shrink-0">
                <div class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm overflow-hidden text-white shadow-sm" style="background:linear-gradient(135deg,#1F2937,#111827);">
                    <span>{{ $initials }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-slate-800 truncate leading-tight">{{ $name }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate mt-0.5">
                        {{ $user?->email ?? 'Admin' }}
                    </p>
                </div>
            </div>

            {{-- Menu Items (Scrollable) --}}
            <div class="overflow-y-auto px-4 py-4 space-y-5 custom-scrollbar">

                {{-- Grup: Sistem Ruangan (Sisa yang tidak muat di navbar) --}}
                @if($sb_adm_jad || $sb_adm_evt || $sb_adm_ars)
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">
                        Sistem Ruangan</p>
                    <div class="space-y-1">
                        @if($sb_adm_jad)
                        <a href="{{ route('eoffice.peminjaman.admin.jadwal-akademik.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.jadwal-akademik') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.jadwal-akademik') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iJadwalAkademik }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Jadwal Akademik</span>
                        </a>
                        @endif

                        @if($sb_adm_evt)
                        <a href="{{ route('eoffice.peminjaman.admin.jadwal-internal.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.jadwal-internal') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.jadwal-internal') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iList }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Event & Maintenance</span>
                        </a>
                        @endif
                        
                        @if($sb_adm_ars)
                        <a href="{{ route('eoffice.peminjaman.admin.riwayat.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.riwayat') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.riwayat') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iArsip }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Arsip & Rekap</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Grup: Master Data --}}
                @if($sb_adm_fas || $sb_adm_rua)
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">
                        Master Data</p>
                    <div class="space-y-1">
                        @if($sb_adm_rua)
                        <a href="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.ruangan') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.ruangan') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iBook }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Manajemen Ruangan</span>
                        </a>
                        @endif

                        @if($sb_adm_fas)
                        <a href="{{ route('eoffice.peminjaman.admin.fasilitas.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.fasilitas') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.fasilitas') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iFasilitas }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Manajemen Fasilitas</span>
                        </a>
                        @endif

                        <a href="{{ route('eoffice.peminjaman.admin.hak-akses.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.hak-akses') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.hak-akses') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iKey }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Hak Akses Menu</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Grup: Sistem Web --}}
                @if($sb_adm_pgt)
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">
                        Sistem Web</p>
                    <div class="space-y-1">
                        <a href="{{ route('eoffice.peminjaman.admin.pengaturan.index') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'admin.pengaturan') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'admin.pengaturan') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iPengaturan }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Pengaturan Operasional</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Grup: Back to App --}}
                <div>
                    <div class="space-y-1 mt-4 border-t border-slate-100 pt-4">
                        <a href="{{ route('eoffice.dashboard') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iBack }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Kembali ke E-Office Utama</span>
                        </a>
                    </div>
                </div>

                {{-- Grup: Akun --}}
                <div>
                    <div class="space-y-1">
                        <form method="POST" action="{{ route('logout') }}" data-no-loader>
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $iLogout }}" />
                                </svg>
                                <span class="text-[13px] font-semibold">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Safe area padding bottom --}}
                <div class="h-6"></div>
            </div>
        </div>
    </div>

    {{-- Bottom Nav Bar (Menu Utama Mobile) --}}
    <div class="flex items-center justify-around px-2 py-1.5 safe-area-pb bg-white relative z-10">

        {{-- 1. Dashboard --}}
        <a href="{{ route('eoffice.peminjaman.dashboard') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ str_contains($currentRoute, 'peminjaman.dashboard') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'peminjaman.dashboard') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iHome }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Home</span>
        </a>

        {{-- 2. Persetujuan --}}
        @if($sb_adm_set)
        <a href="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ str_contains($currentRoute, 'admin.persetujuan') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'admin.persetujuan') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iPersetujuan }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Setuju</span>
        </a>
        @endif

        {{-- 3. Kalender --}}
        @if($sb_adm_klg)
        <a href="{{ route('eoffice.peminjaman.admin.kalender-global.index') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ str_contains($currentRoute, 'admin.kalender') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'admin.kalender') ? 'text-[#0B266E]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iCal }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Kalender</span>
        </a>
        @endif

        {{-- 4. Lainnya (Membuka Drawer) --}}
        <button @click="openMenu = true"
            class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all text-[#ADB5BD]">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Lainnya</span>
        </button>

    </div>
</div>

{{-- Spacer agar konten utama tidak tertutup bottom nav di mobile --}}
<div class="md:hidden h-16 w-full shrink-0"></div>
