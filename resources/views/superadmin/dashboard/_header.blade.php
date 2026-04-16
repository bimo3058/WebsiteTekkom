{{-- resources/views/superadmin/dashboard/_header.blade.php --}}
<nav class="flex items-center gap-1.5 text-[13px] text-muted-foreground mb-1">
    <a href="/" class="hover:text-primary transition-colors">Home</a>
    <span class="text-grey-200">/</span>
    <span class="text-grey-700 font-medium">Dashboard</span>
</nav>

<div class="flex items-start justify-between mb-7 gap-4 flex-wrap">
    <div class="flex items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground tracking-tight mt-1 mb-0.5">Superadmin Dashboard</h1>
            <p class="text-sm text-muted-foreground">
                Selamat datang kembali, <strong class="text-primary font-semibold">{{ auth()->user()->name }}</strong>
            </p>
        </div>

        {{-- Import Progress Compact — di sebelah judul --}}
        <div id="importProgressContainer"
            data-import-id="{{ $activeImportId ?? session('import_id') ?? '' }}"
            class="hidden items-center gap-2 bg-white border border-[#DEE2E6] rounded-xl px-3 py-2 shadow-sm">
            <span class="material-symbols-outlined text-[#5E53F4] text-[16px] animate-spin shrink-0">sync</span>
            <span id="importPercentText" class="text-[12px] font-bold text-[#5E53F4] tabular-nums">0%</span>
            <button type="button" id="btnCancelImportHeader"
                class="shrink-0 p-0.5 text-[#ADB5BD] hover:text-rose-500 transition-colors rounded"
                title="Batalkan impor">
                <span class="material-symbols-outlined text-[14px]">close</span>
            </button>
        </div>
    </div>

    <div class="flex gap-2.5 items-center flex-wrap">
        <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.audit-logs') }}">
            <span class="material-symbols-outlined text-[16px]">history</span>
            Audit Logs
        </x-ui.button>
        <x-ui.button size="sm" as="a" href="{{ route('superadmin.permissions') }}">
            <span class="material-symbols-outlined text-[16px]">shield_person</span>
            Manage Permissions
        </x-ui.button>
    </div>
</div>