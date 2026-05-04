{{-- resources/views/superadmin/audit-logs/audit-logs.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <div style="min-height:100vh=; background:var(--c-bg); font-family:var(--font-sans);">
        <div style="max-width:100%; padding:2px 24px 56px;">

            @include('superadmin.audit-logs._header')
            @include('superadmin.audit-logs._bulk_bar')
            @include('superadmin.audit-logs._filters')
            @include('superadmin.audit-logs._cards')
            @include('superadmin.audit-logs._table')

            {{-- Pagination --}}
            <div id="paginationWrapper" style="margin-top:24px; padding-top:20px; border-top:1px solid var(--c-border);">
                {{ $logs->links() }}
            </div>

        </div>

        {{-- Modals --}}
        @include('superadmin.users._modal_force_logout')
        @include('superadmin.users._modal_suspend')
        @include('superadmin.audit-logs._modal_bulk_delete')

    </div>

    @include('superadmin.audit-logs._scripts')

</x-sidebar>
</x-app-layout>