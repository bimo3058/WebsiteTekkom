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