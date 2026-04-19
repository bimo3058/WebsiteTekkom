{{-- resources/views/superadmin/dashboard/_header.blade.php --}}
<nav class="flex items-center gap-1.5 text-[11px] sm:text-[13px] text-muted-foreground mb-1">
    <a href="/" class="hover:text-primary transition-colors">Home</a>
    <span class="text-grey-200">/</span>
    <span class="text-grey-700 font-medium">Dashboard</span>
</nav>

<div class="flex flex-col sm:flex-row sm:items-start justify-between mb-5 sm:mb-7 gap-4">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-foreground tracking-tight mt-1 mb-0.5">Superadmin Dashboard</h1>
            <p class="text-xs sm:text-sm text-muted-foreground">
                Selamat datang kembali, <strong class="text-primary font-semibold">{{ auth()->user()->name }}</strong>
            </p>
        </div>

        {{-- Import Progress Compact — di sebelah judul --}}
        <div id="importProgressContainer"
            data-import-id="{{ $activeImportId ?? session('import_id') ?? '' }}"
            class="hidden items-center gap-2 bg-white border border-[#DEE2E6] rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 shadow-sm">
            <span class="material-symbols-outlined text-[#5E53F4] text-[14px] sm:text-[16px] animate-spin shrink-0">sync</span>
            <span id="importPercentText" class="text-[11px] sm:text-[12px] font-bold text-[#5E53F4] tabular-nums">0%</span>
            <button type="button" id="btnCancelImportHeader"
                class="shrink-0 p-0.5 text-[#ADB5BD] hover:text-rose-500 transition-colors rounded"
                title="Batalkan impor">
                <span class="material-symbols-outlined text-[12px] sm:text-[14px]">close</span>
            </button>
        </div>
    </div>

    <div class="flex gap-2 items-center flex-wrap">
        <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.audit-logs') }}" class="!px-2 sm:!px-3 !py-1.5 sm:!py-2">
            <span class="material-symbols-outlined text-[14px] sm:text-[16px]">history</span>
            <span class="text-xs sm:text-[12px] hidden sm:inline">Audit Logs</span>
        </x-ui.button>
        <x-ui.button size="sm" as="a" href="{{ route('superadmin.permissions') }}" class="!px-2 sm:!px-3 !py-1.5 sm:!py-2">
            <span class="material-symbols-outlined text-[14px] sm:text-[16px]">shield_person</span>
            <span class="text-xs sm:text-[12px] hidden sm:inline">Manage Permissions</span>
        </x-ui.button>
    </div>
</div>