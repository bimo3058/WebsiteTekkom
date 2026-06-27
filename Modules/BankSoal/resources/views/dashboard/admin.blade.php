<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <span class="text-slate-800 font-semibold">Dashboard</span>
    @endsection

    {{-- Style Box Wrap khas SITKOM untuk Dashboard (Mengadopsi dari Superadmin) --}}
    <style>
        /* Hilangkan padding default agar wrap bisa full 100vh tanpa scroll ganda */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        
        /* Override layout default admin (main area) */
        main.overflow-y-auto { overflow: hidden !important; }
        #banksoal-main-content { padding: 0 !important; max-width: 100% !important; height: 100% !important; display: flex; flex-direction: column; }

        /* Container luar */
        .dash-wrap {
            display: flex; flex-direction: column; height: 100%;
            padding: 16px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        /* Kotak utama (Box) */
        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden; width: 100%; box-sizing: border-box;
        }

        /* Area Header Box (Fixed di atas kotak) */
        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; width: 100%; box-sizing: border-box;
            padding: 16px 24px;
        }

        /* Area Konten Box (Scrollable) */
        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px;
            display: flex; flex-direction: column; gap: 2px;
        }

        .dash-box-body > * {
            flex-shrink: 0;
            width: 100%;
            min-width: 0;
        }

        /* Opsional: Percantik scrollbar */
        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        /* ── Mobile ── */
        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .dash-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
            }
            .dash-box {
                border-radius: 10px;
                display: block;
                height: auto;
                overflow: visible;
            }
            .dash-box-header {
                padding: 12px 14px;
                position: sticky; top: 0; z-index: 20;
            }
            .dash-box-body {
                padding: 14px;
                overflow-y: visible;
                display: block;
            }
        }
    </style>

    <div class="dash-wrap">
        <div class="dash-box">
            
            {{-- Area Header (Diam) --}}
            <div class="dash-box-header">
                @include('banksoal::dashboard.partials._header')
            </div>

            {{-- Area Konten (Bisa di-scroll) --}}
            <div class="dash-box-body">
                @include('banksoal::dashboard.partials._stats')
                @include('banksoal::dashboard.partials._chart')
            </div>

        </div>
    </div>

</x-banksoal::layouts.admin>
