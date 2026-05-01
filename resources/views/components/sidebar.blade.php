@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen = in_array('dosen', $userRoles);
    $isMahasiswa = in_array('mahasiswa', $userRoles);
    $currentRoute = request()->route()->getName();

    $name = $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false) {
        $initials .= strtoupper(substr($name, $sp + 1, 1));
    }

    $themeClass = match(true) {
        $isSuperadmin => 'bg-red-50 text-red-600 border-red-100',
        $isDosen      => 'bg-purple-50 text-purple-600 border-purple-100',
        $isMahasiswa  => 'bg-blue-50 text-blue-600 border-blue-100',
        default       => 'bg-slate-50 text-slate-600 border-slate-100'
    };

    $rc = $themeClass;

    // ── Figma Icon Paths (single-path icons, langsung pakai prop icon="...") ──
    $iconDashboard = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iconShield    = "M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z";
    $iconAuditLog  = "M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z";
    $iconBankSoal  = "M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z";
    $iconCapstone  = "M17.7267 20C19.0393 20 20.0238 18.8454 19.7664 17.6078L18.5184 11.6078C18.3239 10.6729 17.4702 10 16.4787 10H4.92359M17.7267 20H5.9798H5.32879C4.33727 20 3.48358 19.3271 3.28913 18.3922L2.0411 12.3922C1.78368 11.1546 2.76815 10 4.08076 10H4.92359M17.7267 20H18.4795C19.5061 20 20.3792 19.2798 20.5353 18.3041L21.9754 9.30411C22.1692 8.0926 21.1943 7 19.9195 7H15.137C14.4416 7 13.7921 6.6658 13.4063 6.1094L12.5613 4.8906C12.1755 4.3342 11.526 4 10.8306 4H7.53984C6.49082 4 5.60597 4.75107 5.47585 5.75193L4.92359 10";
    $iconEOffice   = "M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15";
    $iconGear      = "M6.78883 3.18702L9.45314 2.08342C10.0104 1.85259 10.6493 2.11723 10.8801 2.6745C11.0637 3.11762 11.5233 3.38148 12.0029 3.38184C12.4828 3.38219 12.9361 3.11793 13.1197 2.67459C13.3506 2.11727 13.9895 1.85261 14.5469 2.08346L17.211 3.187C17.7683 3.41784 18.033 4.05676 17.8021 4.61407C17.6185 5.0574 17.7523 5.56484 18.0918 5.90389C18.4312 6.24278 18.9429 6.38129 19.386 6.19774C19.9433 5.96691 20.5822 6.23155 20.813 6.78882L21.9166 9.45314C22.1474 10.0104 21.8828 10.6493 21.3255 10.8801C20.8824 11.0637 20.6185 11.5234 20.6182 12.003C20.6178 12.4828 20.8821 12.9362 21.3254 13.1198C21.8827 13.3507 22.1473 13.9896 21.9165 14.5469L20.813 17.2111C20.5821 17.7684 19.9432 18.033 19.3859 17.8022C18.9426 17.6186 18.4351 17.7523 18.0961 18.0918C17.7572 18.4312 17.6187 18.9429 17.8023 19.386C18.0331 19.9433 17.7685 20.5821 17.2112 20.813L14.5469 21.9166C13.9896 22.1474 13.3507 21.8828 13.1199 21.3255C12.9363 20.8824 12.4766 20.6185 11.997 20.6182C11.5171 20.6178 11.0637 20.8821 10.8801 21.3254C10.6492 21.8827 10.0103 22.1474 9.45297 21.9165L6.78887 20.813C6.23152 20.5822 5.96686 19.9432 6.19772 19.3859C6.38136 18.9425 6.24769 18.4351 5.90812 18.096C5.56872 17.7571 5.05713 17.6187 4.61402 17.8022C4.05674 18.0331 3.41786 17.7684 3.18703 17.2112L2.08343 14.5469C1.8526 13.9896 2.11723 13.3507 2.67451 13.1199C3.11762 12.9363 3.38149 12.4766 3.38185 11.997C3.3822 11.5171 3.11794 11.0638 2.67458 10.8801C2.11724 10.6493 1.85257 10.0103 2.08343 9.45299L3.18693 6.78891C3.41779 6.23157 4.05675 5.9669 4.61409 6.19776C5.05745 6.38141 5.56487 6.24771 5.90395 5.90813C6.24284 5.56874 6.38129 5.05713 6.19775 4.61401C5.96692 4.05674 6.23155 3.41785 6.78883 3.18702Z";
    $iconFlash     = "M5.2 12.4L13 2L12 10H18C18.824 10 19.2944 10.9408 18.8 11.6L11 22L12 14H6C5.17595 14 4.70557 13.0592 5.2 12.4Z";
    $iconProfile   = "M17 20.6622V19.5C17 17.2909 15.2091 15.5 13 15.5H11C8.79086 15.5 7 17.2909 7 19.5V20.6622M17 20.6622C19.989 18.9331 22 15.7014 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.7014 4.01099 18.9331 7 20.6622M17 20.6622C15.5291 21.513 13.8214 22 12 22C10.1786 22 8.47087 21.513 7 20.6622M15 9C15 10.6569 13.6569 12 12 12C10.3431 12 9 10.6569 9 9C9 7.34315 10.3431 6 12 6C13.6569 6 15 7.34315 15 9Z";
    $iconLogout    = "M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238";
    $iconKompre    = "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z";

    // Helper untuk warna icon multi-path
    $activeColor   = 'text-[#5E53F4]';
    $inactiveColor = 'text-[#ADB5BD] group-hover:text-[#1A1C1E]';
@endphp

<div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' }"
     x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))"
     class="flex h-screen overflow-hidden font-['Inter_Tight']">

    {{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="hidden md:flex flex-col fixed inset-y-0 left-0 z-50 bg-white border-r border-slate-200 transition-all duration-300 ease-in-out">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-4 border-b border-slate-100 bg-white">
            <div class="flex items-center w-full" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-primary-100">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round">
                            <path d="{{ $iconFlash }}"/>
                        </svg>
                    </div>
                    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="text-slate-800 font-semibold text-sm tracking-tight whitespace-nowrap">{{ config('app.name') }}</span>
                        <p class="text-slate-400 text-[10px] font-semibold uppercase tracking-widest leading-none mt-0.5">
                            @if($isSuperadmin) Superadmin
                            @elseif($isDosen) Dosen Portal
                            @elseif($isMahasiswa) Mahasiswa
                            @else Portal
                            @endif
                        </p>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" x-show="sidebarOpen"
                        class="text-slate-400 hover:text-primary-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>
            </div>
            <button x-show="!sidebarOpen" @click="sidebarOpen = !sidebarOpen"
                    class="text-slate-400 hover:text-primary-600 transition-colors p-1 rounded-full absolute -right-3 top-5 bg-white border border-slate-200 shadow-sm z-50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-6 overflow-y-auto custom-scrollbar">
            <div class="space-y-6">

                {{-- UTAMA --}}
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">Utama</p>
                    <div class="space-y-1">
                        @if($isSuperadmin)
                            <x-sidebar-link href="{{ route('superadmin.dashboard') }}"
                                icon="{{ $iconDashboard }}"
                                label="Dashboard"
                                active="{{ str_contains($currentRoute, 'dashboard') }}"/>
                        @else
                            <x-sidebar-link href="{{ route('dashboard') }}"
                                icon="{{ $iconDashboard }}"
                                label="Dashboard"
                                active="{{ $currentRoute === 'dashboard' }}"/>
                        @endif
                    </div>
                </div>

                {{-- KENDALI (Superadmin Only) --}}
                @if($isSuperadmin)
                @php
                    $usersActive = str_contains($currentRoute, 'users');
                @endphp
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">Kendali</p>
                    <div class="space-y-1">

                        {{-- User Management — users-03 punya 3 path, render manual --}}
                        <a href="{{ route('superadmin.users.index') }}"
                           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group mb-1 {{ $usersActive ? 'bg-[#F1E9FF] text-[#5E53F4] font-semibold shadow-sm' : 'text-[#6C757D] hover:text-[#1A1C1E] hover:bg-[#F8F9FA]' }}"
                           :class="!sidebarOpen ? 'justify-center px-2' : ''">
                            <div class="flex items-center" :class="!sidebarOpen ? 'justify-center w-full' : 'gap-3'">
                                <svg class="w-5 h-5 flex-shrink-0 transition-all group-hover:scale-110 {{ $usersActive ? $activeColor : $inactiveColor }}"
                                     fill="none" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.1072 8.86495C6.4829 6.90273 7.81438 5.28 9.59848 4.49991C9.07975 3.60326 8.11027 3 6.99988 3C5.34303 3 3.99988 4.34315 3.99988 6C3.99988 7.34598 4.88629 8.48493 6.1072 8.86495ZM6.08454 11.0095C3.80371 11.159 2 13.0564 2 15.375C2 15.7202 2.27982 16 2.625 16H5.02185C5.60567 15.0881 6.41092 14.3317 7.36176 13.8064C6.71341 13.0173 6.2625 12.0598 6.08454 11.0095ZM18.9782 16H21.375C21.7202 16 22 15.7202 22 15.375C22 13.0563 20.1963 11.159 17.9154 11.0095C17.7375 12.0598 17.2866 13.0173 16.6383 13.8064C17.5891 14.3316 18.3944 15.0881 18.9782 16ZM17.8928 8.86489C19.1136 8.48481 19.9999 7.34591 19.9999 6C19.9999 4.34315 18.6567 3 16.9999 3C15.8895 3 14.9201 3.60322 14.4013 4.49984C16.1855 5.27989 17.517 6.90264 17.8928 8.86489Z"
                                        fill="currentColor"/>
                                    <path d="M12 13C13.6568 13 15 11.6569 15 10C15 8.34315 13.6568 7 12 7C10.3431 7 8.99998 8.34315 8.99998 10C8.99998 11.6569 10.3431 13 12 13Z"
                                        stroke="currentColor" stroke-width="2"/>
                                    <path d="M16.5 20H7.50001C7.22386 20 6.99999 19.7761 7 19.5C7.00006 17.567 8.56708 16 10.5001 16H13.5001C15.4331 16 17.0001 17.567 17 19.5C17 19.7762 16.7761 20 16.5 20Z"
                                        stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span x-show="sidebarOpen" class="text-sm tracking-tight whitespace-nowrap overflow-hidden font-medium">User Management</span>
                            </div>
                            @if($usersActive)
                                <div x-show="sidebarOpen" class="ml-auto size-1.5 rounded-full bg-[#5E53F4] animate-in fade-in zoom-in duration-300"></div>
                            @endif
                        </a>

                        <x-sidebar-link href="{{ route('superadmin.permissions') }}"
                            icon="{{ $iconShield }}"
                            label="Permissions"
                            active="{{ str_contains($currentRoute, 'permissions') }}"/>

                        <x-sidebar-link href="{{ route('superadmin.audit-logs') }}"
                            icon="{{ $iconAuditLog }}"
                            label="Audit Logs"
                            active="{{ str_contains($currentRoute, 'audit-logs') }}"/>
                    </div>
                </div>
                @endif

                {{-- AKADEMIK --}}
                @php
                    $mkActive = str_contains($currentRoute, 'manajemen-mahasiswa') || str_contains($currentRoute, 'mk.');
                @endphp
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">Akademik</p>
                    <div class="space-y-1">

                        @if($isSuperadmin || $isDosen || $isMahasiswa)
                            <x-sidebar-link
                                :href="$isMahasiswa ? route('komprehensif.mahasiswa.dashboard') : route('banksoal.dashboard')"
                                icon="{{ $isMahasiswa ? $iconKompre : $iconBankSoal }}"
                                :label="$isMahasiswa ? 'Uji Komprehensif' : ($isDosen ? 'Manajemen RPS / Bank Soal' : 'Bank Soal')"
                                :active="str_contains($currentRoute, 'banksoal') || str_contains($currentRoute, 'komprehensif')"/>
                        @endif

                        <x-sidebar-link
                            :href="route('capstone.dashboard')"
                            icon="{{ $iconCapstone }}"
                            label="Capstone & TA"
                            :active="str_contains($currentRoute, 'capstone')"/>

                        {{-- Manajemen Mahasiswa — users-01 punya 3 path, render manual --}}
                        <a href="{{ route('manajemenmahasiswa.dashboard') }}"
                           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group mb-1 {{ $mkActive ? 'bg-[#F1E9FF] text-[#5E53F4] font-semibold shadow-sm' : 'text-[#6C757D] hover:text-[#1A1C1E] hover:bg-[#F8F9FA]' }}"
                           :class="!sidebarOpen ? 'justify-center px-2' : ''">
                            <div class="flex items-center" :class="!sidebarOpen ? 'justify-center w-full' : 'gap-3'">
                                <svg class="w-5 h-5 flex-shrink-0 transition-all group-hover:scale-110 {{ $mkActive ? $activeColor : $inactiveColor }}"
                                     fill="none" viewBox="0 0 24 24">
                                    <path d="M14.8574 10.8344C15.5799 9.73304 16.0001 8.41559 16.0001 7C16.0001 5.58441 15.5799 4.26696 14.8574 3.16558C15.2195 3.05785 15.603 3 16 3C18.2092 3 20 4.79086 20 7C20 9.20914 18.2092 11 16 11C15.603 11 15.2195 10.9422 14.8574 10.8344ZM17.8741 21C17.9563 20.6804 18.0001 20.3453 18.0001 20V19C18.0001 17.1081 17.3434 15.3696 16.2455 14H17C19.7615 14 22 16.2386 22 19V20C22 20.5523 21.5523 21 21 21H17.8741Z"
                                        fill="currentColor"/>
                                    <path d="M10 14H8C5.23858 14 3 16.2386 3 19V20C3 20.5523 3.44772 21 4 21H14C14.5523 21 15 20.5523 15 20V19C15 16.2386 12.7614 14 10 14Z"
                                        stroke="currentColor" stroke-width="2"/>
                                    <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z"
                                        stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span x-show="sidebarOpen" class="text-sm tracking-tight whitespace-nowrap overflow-hidden font-medium">
                                    {{ $isMahasiswa ? 'Forum Mahasiswa' : 'Manajemen Mahasiswa' }}
                                </span>
                            </div>
                            @if($mkActive)
                                <div x-show="sidebarOpen" class="ml-auto size-1.5 rounded-full bg-[#5E53F4] animate-in fade-in zoom-in duration-300"></div>
                            @endif
                        </a>

                        <x-sidebar-link
                            href="{{ route('eoffice.dashboard') }}"
                            icon="{{ $iconEOffice }}"
                            label="E-Office"
                            active="{{ str_contains($currentRoute, 'eoffice') || str_contains($currentRoute, 'eo.') }}"/>

                    </div>
                </div>

                {{-- SISTEM (Superadmin Only) --}}
                @if($isSuperadmin)
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">Sistem</p>
                    <div class="space-y-1">
                        <x-sidebar-link href="{{ route('superadmin.modules') }}"
                            icon="{{ $iconGear }}"
                            label="Modul Sistem"
                            active="{{ str_contains($currentRoute, 'modules') }}"/>

                        <x-sidebar-link href="/pulse"
                            icon="{{ $iconFlash }}"
                            label="System Monitor"
                            target="_blank"
                            active="{{ request()->is('pulse*') }}"/>
                    </div>
                </div>
                @endif

            </div>
        </nav>

        {{-- User Footer --}}
        <div class="border-t border-slate-100 p-3 space-y-1 bg-white">
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-100" :class="!sidebarOpen ? 'justify-center' : ''">
                <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center font-semibold text-xs shadow-sm overflow-hidden border-2 border-white {{ $rc }} bg-white">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                </div>
                <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                    <p class="text-slate-800 text-[13px] font-semibold truncate tracking-tight">{{ $user->name }}</p>
                    <p class="text-slate-400 text-[10px] font-semibold uppercase tracking-wider truncate">
                        {{ implode(' / ', array_map('ucfirst', $userRoles)) }}
                    </p>
                </div>
            </div>

            <div class="pt-1 space-y-1">
                <x-sidebar-link
                    href="{{ route('profile.edit') }}"
                    icon="{{ $iconProfile }}"
                    label="Profil"
                    active="{{ str_contains($currentRoute, 'profile') }}"/>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group mb-1 text-[#6C757D] hover:text-red-600 hover:bg-red-50"
                            :class="!sidebarOpen ? 'justify-center px-2' : 'gap-3'">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all group-hover:scale-110 text-[#ADB5BD] group-hover:text-red-600"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $iconLogout }}"/>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm tracking-tight whitespace-nowrap font-sans">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main
        :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'"
        class="flex-1 overflow-y-auto bg-[#F8F9FA] transition-all duration-300 ease-in-out">
        <div class="p-8">
            {{ $slot }}
        </div>
        <footer class="border-t border-slate-200 px-8 py-5 bg-white">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-slate-400 text-[11px] font-medium tracking-tight">
                    &copy; {{ date('Y') }} <span class="text-slate-800 font-semibold">{{ config('app.name') }}</span>. All rights reserved.
                </p>
                <p class="text-slate-300 text-[10px] font-semibold uppercase tracking-[0.2em]">LuminHR System v2.0</p>
            </div>
        </footer>
    </main>

    <x-sidebar-mobile :user="$user" />
</div>