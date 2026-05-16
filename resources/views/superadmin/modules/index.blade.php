{{-- resources/views/superadmin/modules/index.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <style>
        /* Hilangkan padding default agar wrap bisa full 100vh */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }

        /* Container luar */
        .mod-wrap {
            display: flex; flex-direction: column; height: calc(100vh - 60px);
            padding: 20px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        /* Kotak utama (Box) */
        .mod-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }

        /* Area Header Box (Fixed di atas kotak) */
        .mod-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
            padding: 16px 24px;
        }

        /* Area Konten Box (Scrollable) */
        .mod-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px; display: flex; flex-direction: column; gap: 16px;
            background: var(--c-bg);
        }

        /* Responsive grid */
        .mod-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (min-width: 768px)  { .mod-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1280px) { .mod-grid { grid-template-columns: repeat(4, 1fr); } }

        /* ── Mobile: scroll natively ── */
        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .mod-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 8px;
            }
            .mod-box {
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
                border-radius: 10px;
            }
            .mod-box-header {
                padding: 12px 14px;
                position: sticky;
                top: 52px;
                z-index: 10;
            }
            .mod-box-body {
                overflow-y: visible !important;
                flex: none !important;
                padding: 12px 14px;
            }
        }
    </style>

    <div class="mod-wrap">
        <div class="mod-box">

            {{-- Header (diam/fixed) --}}
            <div class="mod-box-header">

                {{-- Title row --}}
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <h1 style="font-size:16px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2; margin:0;">System Modules</h1>
                        <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
                            Total <span style="color:var(--c-primary); font-weight:600;">{{ $modules->count() }}</span> modul terintegrasi dalam ekosistem
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
                <div class="mod-grid">
                    @foreach($modules as $module)
                        @include('superadmin.modules._card', ['module' => $module])
                    @endforeach
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