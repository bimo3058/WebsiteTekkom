{{-- resources/views/superadmin/dashboard/_import.blade.php --}}
<div class="flex items-center gap-3 mb-4">
    <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
    <span class="text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Import User</span>
    <x-ui.separator class="flex-1" />
</div>

<div class="bg-white border border-[#DEE2E6] rounded-2xl p-6 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="size-12 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">upload_file</span>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-[#1A1C1E] tracking-tight">Import via CSV</p>
                <p class="text-[12px] text-muted-foreground mt-0.5">Upload file CSV untuk menambahkan banyak user sekaligus ke sistem.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.users.index') }}">
                <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                Kelola Users
            </x-ui.button>
            <x-ui.button size="sm" onclick="openModal('modalImportUser')">
                <span class="material-symbols-outlined text-[16px]">upload</span>
                Import Sekarang
            </x-ui.button>
        </div>
    </div>

    {{-- Progress body — selalu render, JS show/hide --}}
    <div id="importProgressBody" class="{{ ($activeImportId ?? session('import_id')) ? '' : 'hidden' }} mt-5 pt-5 border-t border-[#DEE2E6]">
        <div class="flex items-center justify-between mb-2">
            <p id="importStatusTextBody" class="text-[12px] font-semibold text-[#1A1C1E]">Memproses impor...</p>
            <span id="importPercentTextBody" class="text-[11px] font-bold text-primary">0%</span>
        </div>
        <div class="h-2 bg-[#F8F9FA] rounded-full overflow-hidden border border-[#DEE2E6]">
            <div id="importProgressBarBody" class="h-full bg-primary transition-all duration-500 rounded-full" style="width: 0%"></div>
        </div>
    </div>
</div>