{{-- resources/views/superadmin/modules/index.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    @include('superadmin.modules._style')

    <div class="mod-wrap">
        <div class="mod-box">

            {{-- Header (diam/fixed) --}}
            <div class="mod-box-header">

                {{-- Title row --}}
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <h1 style="font-size:16px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2; margin:0;">System Modules</h1>
                        <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
                            Kelola status dan konfigurasi modul sistem.
                        </p>
                    </div>
                    <a href="{{ route('superadmin.dashboard') }}"
                       style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s; flex-shrink:0;"
                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                        Dashboard
                    </a>
                </div>
            </div>

            {{-- Body (scrollable) --}}
            <div class="mod-box-body">
                @if(session('success'))<div class="mod-alert" role="status">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="mod-alert mod-alert-error" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="mod-alert mod-alert-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <div class="mod-grid">
                    @forelse($modules as $module)
                        @include('superadmin.modules._card', ['module' => $module])
                    @empty<div class="mod-empty">Belum ada modul terdaftar.</div>@endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Modals diletakkan di luar wrap agar overlay-nya menutupi layar penuh --}}
    @foreach($modules as $module)
        @include('superadmin.modules._modal_manage', ['module' => $module])
    @endforeach

    <script>
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        }
        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="modal-"]').forEach(m => { m.style.display = 'none'; });
                document.body.style.overflow = '';
            }
        });
    </script>

</x-sidebar>
</x-app-layout>