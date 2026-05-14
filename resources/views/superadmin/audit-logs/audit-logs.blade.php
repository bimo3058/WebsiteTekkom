{{-- resources/views/superadmin/audit-logs/audit-logs.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <style>
        .sitkom-content {
            padding: 0 !important;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .audit-wrap {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 60px);
            padding: 10px;
            box-sizing: border-box;
            font-family: 'Inter Tight', sans-serif;
        }

        .audit-box {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }

        .audit-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
            padding: 16px 24px;
        }

        .audit-box-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: #FAFAFA;
        }

        /* ── Mobile: scroll natively ── */
        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .audit-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
            }
            .audit-box {
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
                border-radius: 10px;
            }
            .audit-box-header {
                padding: 12px 14px;
                position: sticky;
                top: 52px;
                z-index: 10;
            }
            .audit-box-body {
                overflow-y: visible !important;
                flex: none !important;
                padding: 12px 14px;
            }
        }
    </style>

    <div class="audit-wrap">
        <div class="audit-box">

            {{-- Header --}}
            <div class="audit-box-header">
                @include('superadmin.audit-logs._header')
            </div>

            {{-- Body --}}
            <div class="audit-box-body">
                @include('superadmin.audit-logs._bulk_bar')
                @include('superadmin.audit-logs._cards')
                @include('superadmin.audit-logs._filters')
                @include('superadmin.audit-logs._table')

                {{-- Pagination --}}
                <div id="paginationWrapper">
                    {{-- handled inside _table card --}}
                </div>
            </div>

        </div>
    </div>

    {{-- Modals --}}
    @include('superadmin.users._modal_force_logout')
    @include('superadmin.users._modal_suspend')
    @include('superadmin.audit-logs._modal_bulk_delete')

    @include('superadmin.audit-logs._scripts')

</x-sidebar>
</x-app-layout>