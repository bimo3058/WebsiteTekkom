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

        <div class="settings-wrap"
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
                             <but       ton type="button"
                                        class="settings-nav-btn"
                                        :class="tab==='{{ $t['key'] }}' ? 'active' : ''"
                                        @click="tab='{{ $t['key'] }}'">
                                <span class="material-symbols-outlined">{{ $t['icon'] }}</span>
                                        {{ $t['label'] }}
                                    </button>
                        @endforeach
                </na    v>
      
                      {{-- Content --}}
            <div class="settings-content">

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
        </div>
            </div>
            </div>
            
            @include('profile.partials.settings-modals')
            
            <script>
        function submitActiveForm() {
    const tab = document.querySelector('[x-data]').__x.$data.tab;
        if (tab === 'general')  document.getElementById('form-general').submit();
    if (tab === 'password') document.getElementById('form-password').submit();
}
</script>

</x-sidebar>
</x-app-layout>