{{-- resources/views/superadmin/dashboard/_import.blade.php --}}
<div class="flex items-center gap-3 mb-4">
    <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
    <span class="text-sm sm:text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Import User</span>
    <x-ui.separator class="flex-1" />
</div>

<div class="bg-white border border-[#DEE2E6] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="size-10 sm:size-12 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[20px] sm:text-[24px]">upload_file</span>
            </div>
            <div>
                <p class="text-[12px] sm:text-[13px] font-semibold text-[#1A1C1E] tracking-tight">Import via CSV</p>
                <p class="text-[11px] sm:text-[12px] text-muted-foreground mt-0.5">Upload file CSV untuk menambahkan banyak user</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.users.index') }}" class="!px-2 sm:!px-3 !py-1.5 sm:!py-2">
                <span class="material-symbols-outlined text-[14px] sm:text-[16px]">manage_accounts</span>
                <span class="text-xs sm:text-[12px] hidden sm:inline">Kelola Users</span>
            </x-ui.button>
            <x-ui.button size="sm" onclick="openModal('modalImportUser')" class="!px-2 sm:!px-3 !py-1.5 sm:!py-2">
                <span class="material-symbols-outlined text-[14px] sm:text-[16px]">upload</span>
                <span class="text-xs sm:text-[12px]">Import</span>
            </x-ui.button>
        </div>
    </div>

    {{-- Progress body — selalu render, JS show/hide --}}
    <div id="importProgressBody" class="{{ ($activeImportId ?? session('import_id')) ? '' : 'hidden' }} mt-4 sm:mt-5 pt-4 sm:pt-5 border-t border-[#DEE2E6]">
        <div class="flex items-center justify-between mb-2">
            <p id="importStatusTextBody" class="text-[11px] sm:text-[12px] font-semibold text-[#1A1C1E]">Memproses impor...</p>
            <span id="importPercentTextBody" class="text-[10px] sm:text-[11px] font-bold text-primary">0%</span>
        </div>
        <div class="h-1.5 sm:h-2 bg-[#F8F9FA] rounded-full overflow-hidden border border-[#DEE2E6]">
            <div id="importProgressBarBody" class="h-full bg-primary transition-all duration-500 rounded-full" style="width: 0%"></div>
        </div>
    </div>
</div>