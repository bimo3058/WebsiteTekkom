{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <x-sidebar :user="auth()->user()">

        @php
            $user = auth()->user();
            $namaDepan = explode(' ', $user->name)[0];
            $nim = $user->student?->student_number ?? '';
            $isDefaultPw = $user->password &&
                \Illuminate\Support\Facades\Hash::check($namaDepan . $nim, $user->password);
        @endphp

        <style>
            .sitkom-content {
                padding: 0 !important;
                display: flex;
                flex-direction: column;
                flex: 1;
                overflow: hidden;
            }

            .settings-wrap {
                display: flex;
                flex-direction: column;
                height: calc(100vh - 60px);
                padding: 10px;
                box-sizing: border-box;
                font-family: 'Inter Tight', sans-serif;
            }

            .settings-box {
                display: flex;
                flex-direction: column;
                flex: 1;
                min-height: 0;
                background: #fff;
                border: 1px solid var(--c-border);
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                width: 100%;
                box-sizing: border-box;
            }

            .settings-box-header {
                background: #fff;
                border-bottom: 1px solid var(--c-border);
                flex-shrink: 0;
                width: 100%;
                box-sizing: border-box;
                padding: 14px 24px;
            }

            .settings-box-body {
                flex: 1;
                overflow-y: auto;
                display: flex;
                min-height: 0;
            }

            /* ── Mobile: scroll natively ── */
            @media (max-width: 767px) {
                .sitkom-content {
                    padding: 8px 8px 80px !important;
                    display: block !important;
                    overflow: visible !important;
                }
                .settings-wrap {
                    height: auto !important;
                    min-height: 0 !important;
                    padding: 0;
                }
                .settings-box {
                    flex: none !important;
                    min-height: 0 !important;
                    overflow: visible !important;
                    border-radius: 10px;
                }
                .settings-box-header {
                    padding: 10px 14px;
                    position: sticky;
                    top: 52px;
                    z-index: 10;
                }
                .settings-box-body {
                    overflow-y: visible !important;
                    flex-direction: column !important;
                    display: block !important;
                }
            }

            .settings-nav {
                width: 200px;
                flex-shrink: 0;
                border-right: 1px solid var(--c-border);
                padding: 16px 8px;
                display: flex;
                flex-direction: column;
                gap: 2px;
                background: #fff;
            }

            .settings-nav-btn {
                display: flex;
                align-items: center;
                gap: 10px;
                width: 100%;
                padding: 9px 12px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 500;
                color: #64748b;
                background: transparent;
                border: none;
                cursor: pointer;
                text-align: left;
                transition: background .15s, color .15s;
            }

            .settings-nav-btn:hover {
                background: #f8fafc;
                color: #1e293b;
            }

            .settings-nav-btn.active {
                background: #f1f5f9;
                color: #0f172a;
                font-weight: 600;
            }

            .settings-nav-btn .material-symbols-outlined {
                font-size: 18px;
            }

            .settings-content {
                flex: 1;
                overflow-y: auto;
                padding: 28px 32px;
                box-sizing: border-box;
            }

            .panel-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2px;
            }

            .panel-sub {
                font-size: 13px;
                color: #64748b;
                margin-bottom: 24px;
            }

            .field-label {
                display: block;
                font-size: 13px;
                font-weight: 500;
                color: #374151;
                margin-bottom: 6px;
            }

            .field-input {
                width: 100%;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 9px 12px;
                font-size: 13px;
                color: #0f172a;
                background: #fff;
                outline: none;
                box-sizing: border-box;
                transition: border-color .15s, box-shadow .15s;
            }

            .field-input:focus {
                border-color: #5E53F4;
                box-shadow: 0 0 0 3px rgba(94, 83, 244, .08);
            }

            .field-input.readonly {
                background: #f8fafc;
                color: #94a3b8;
                cursor: not-allowed;
            }

            .toggle-track {
                width: 40px;
                height: 22px;
                border-radius: 99px;
                display: flex;
                align-items: center;
                padding: 2px;
            }

            .toggle-track.on {
                background: #1E1B4B;
            }

            .toggle-track.off {
                background: #cbd5e1;
            }

            .toggle-thumb {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
                transition: transform .2s;
            }

            .toggle-track.on .toggle-thumb {
                transform: translateX(18px);
            }

            .str-bar {
                height: 4px;
                flex: 1;
                border-radius: 99px;
                background: #e2e8f0;
                transition: background .3s;
            }
        </style>

        <div class="settings-wrap" id="settings-root"
            x-data="{ tab: '{{ session('status') === 'password-updated' ? 'password' : 'general' }}' }">
            <div class="settings-box">

                {{-- ── Header ── --}}
                <div class="settings-box-header">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="material-symbols-outlined"
                                style="font-size:20px;color:#5E53F4;">settings</span>
                            <span style="font-size:15px;font-weight:700;color:#0f172a;">Settings</span>
                        </div>
                        <button type="button" onclick="submitActiveForm()"
                            style="padding:7px 18px;background:#1E1B4B;color:#fff;font-size:13px;font-weight:600;border-radius:8px;border:none;cursor:pointer;transition:background .15s;"
                            onmouseover="this.style.background='#2d2a5e'" onmouseout="this.style.background='#1E1B4B'">
                            Save Changes
                        </button>
                    </div>
                </div>

                {{-- ── Body: Nav + Content ── --}}
                <div class="settings-box-body">

                    {{-- Sidebar Nav --}}
                    <nav class="settings-nav">
                        @foreach([
                                        ['key' => 'general', 'label' => 'Umum', 'icon' => 'person'],
                                        ['key' => 'password', 'label' => 'Password', 'icon' => 'lock'],
                                        ['key' => 'tema', 'label' => 'Tema & Bahasa', 'icon' => 'palette'],
                                        ['key' => 'notifikasi', 'label' => 'Notifikasi', 'icon' => 'notifications'],
                                    ] as $t)
                            <button type="button"
                                class="settings-nav-btn"
                                :class="tab==='{{ $t['key'] }}' ? 'active' : ''"
                                @click="tab='{{ $t['key'] }}'">
                                <span class="material-symbols-outlined">{{ $t['icon'] }}</span>
                                {{ $t['label'] }}
                            </button>
                        @endforeach
                    </nav>
      
                      {{-- Content --}}
            <div class="settings-content">

<<<<<<< HEAD
                {{-- ── GRID UTAMA: 3 kolom ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr_290px] gap-4 items-start pb-6">

                    {{-- ── KOLOM 1: Avatar + Info + Stats + Aktivitas ── --}}
                    <div class="flex flex-col gap-4 h-full">

                        {{-- Avatar card --}}
                        <div class="flex-1 bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden flex flex-col">
                            <div class="flex flex-col items-center pt-6 pb-5 px-5">
                                @php
                                    $user     = auth()->user();
                                    $name     = $user->name;
                                    $initials = strtoupper(substr($name, 0, 1));
                                    $sp       = strpos($name, ' ');
                                    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
                                @endphp

                                {{-- Avatar --}}
                                <div class="relative group mb-3">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold bg-[#E8EDF7] text-[#0B266E] border-2 border-white shadow-sm overflow-hidden">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" id="currentAvatar" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            <span id="avatarInitials">{{ $initials }}</span>
                                        @endif
                                    </div>
                                    <button type="button" onclick="openManagePhotoModal()"
                                        class="absolute -bottom-1 -right-1 w-6 h-6 bg-white border border-[#DFE1E7] rounded-full flex items-center justify-center shadow-sm cursor-pointer hover:border-[#0B266E] hover:text-[#0B266E] transition-all z-10 text-[#808897]">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-[14px] font-bold text-[#0D0D12] text-center leading-tight">{{ $user->name }}</p>
                                <p class="text-[12px] text-[#808897] mt-1 text-center truncate w-full px-2">{{ $user->email }}</p>

                                {{-- Role badges --}}
                                <div class="mt-3 flex flex-wrap gap-1 justify-center">
                                    @foreach($user->roles as $role)
                                    <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full border
                                        @if(str_contains($role->name,'superadmin')) bg-[#FADAE1] text-[#710E21] border-[#ED8296]/30
                                        @elseif(str_contains($role->name,'dosen'))  bg-[#E8EDF7] text-[#0B266E] border-[#8FA3D1]/30
                                        @elseif(str_contains($role->name,'mahasiswa')) bg-[#DDF2EE] text-[#174E43] border-[#40C4AA]/30
                                        @else bg-[#F9ECCB] text-[#5B3D1E] border-[#D39C3D]/30 @endif">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Data akademik --}}
                            <div class="border-t border-[#F0F1F4] px-5 py-4 h-full">
                                <p class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-widest mb-3">Data Akademik</p>
                                <div class="space-y-0">
                                    @if($user->hasRole('mahasiswa') && $user->student)
                                        @include('profile.partials._info-row', ['label' => 'NIM',      'value' => $user->student->student_number])
                                        @include('profile.partials._info-row', ['label' => 'Angkatan', 'value' => $user->student->cohort_year])
                                    @elseif($user->hasRole('dosen') && $user->lecturer)
                                        @include('profile.partials._info-row', ['label' => 'NIP', 'value' => $user->lecturer->employee_number])
                                    @endif
                                    @include('profile.partials._info-row', [
                                        'label' => 'Akses Terakhir',
                                        'value' => $user->last_login?->diffForHumans() ?? 'Baru saja'
                                    ])
                                </div>

                                <div class="mt-4 pt-4 border-t border-[#F0F1F4]">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                        </svg>
                                        <p class="text-[11px] font-semibold text-[#A4ABB8] uppercase tracking-widest">Jejak Aktivitas</p>
                                    </div>
                                    <div>
                                        @include('profile.partials.activity-log')
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stats card --}}
                        @include('profile.partials.stats-card')

                    </div>

                    {{-- ── KOLOM 2: Pengaturan Akun ── --}}
                    <div class="bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden flex flex-col h-full">

                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-[#F0F1F4] flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#E8EDF7] flex items-center justify-center">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <h3 class="text-[12px] font-semibold text-[#0D0D12] tracking-tight">Pengaturan Akun</h3>
                            <span class="ml-auto text-[10px] font-semibold text-[#287F6E] bg-[#DDF2EE] border border-[#40C4AA]/30 px-2 py-0.5 rounded-full">Bisa diubah</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- ── KOLOM 3: Keamanan + Hapus Akun ── --}}
                    <div class="flex flex-col gap-4">

                        {{-- Password --}}
                        <div class="bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden flex flex-col">
                            <div class="px-5 py-4 border-b border-[#F0F1F4] flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#E8EDF7] flex items-center justify-center">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </div>
                                <h3 class="text-[12px] font-semibold text-[#0D0D12] tracking-tight">Keamanan</h3>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
=======
                          <div       x-show="tab==='general'" x-transition.opacity.duration.150ms>
                            @include('profile.partials.settings-panel-general')
                </div>
      
                  <div         x-show="tab==='password'" x-transition.opacity.duration.150ms>
                            @include('profile.partials.settings-panel-password')
                </div>

                        <div         x-show="tab==='tema'" x-transition.opacity.duration.150ms>
                            @include('profile.partials.settings-panel-tema')
                </div>

                                <div x-show="tab==='notifikasi'" x-transition.opacity.duration.150ms>
                            @include('profile.partials.settings-panel-notifikasi')
                        </div>

                    </div>
>>>>>>> d6381d8f518da8a9d88010bb391d0a2195d0578d
        </div>
            </div>
            </div>
            
            @include('profile.partials.settings-modals')
            
            <script>
        function submitActiveForm() {
            // Target wrapper settings secara spesifik by ID (bukan querySelector('[x-data]')
            // yang bisa menangkap elemen Alpine lain seperti dropdown phone code)
            const el = document.getElementById('settings-root');
            const tab = (el && window.Alpine) ? Alpine.$data(el).tab : 'general';

            if (tab === 'general')  document.getElementById('form-general').submit();
            if (tab === 'password') document.getElementById('form-password').submit();
        }
</script>

</x-sidebar>
</x-app-layout>