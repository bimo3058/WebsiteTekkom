<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-[#F8F9FA] p-8">
        <div class="max-w-full mx-auto">

            {{-- Header --}}
            @include('superadmin.audit-logs._header')

            {{-- Floating Bulk Action Bar --}}
            @include('superadmin.audit-logs._bulk_bar')

            {{-- Filter Section --}}
            @include('superadmin.audit-logs._filters')

            {{-- Info Cards (Online & Suspended) --}}
            @include('superadmin.audit-logs._cards')
            
            {{-- Main Table --}}
            @include('superadmin.audit-logs._table')

            {{-- Pagination --}}
            <div id="paginationWrapper" class="mt-8 w-full border-t border-[#DEE2E6] pt-5">
                {{ $logs->links() }}
            </div>

        </div>

        {{-- Modals --}}
        @include('superadmin.users._modal_force_logout')
        @include('superadmin.users._modal_suspend')
        @include('superadmin.audit-logs._modal_bulk_delete')

    </div>

    {{-- Script dipindah ke file terpisah atau tetap di sini --}}
    @include('superadmin.audit-logs._scripts')

</x-sidebar>
</x-app-layout>