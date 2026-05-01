{{-- resources/views/superadmin/dashboard/_import.blade.php --}}
<div class="flex items-center gap-2 mb-3">
    <span class="text-xs font-bold text-foreground tracking-tight uppercase">Import User</span>
    <div class="flex-1 h-px bg-border"></div>
</div>

<div class="bg-white border border-border rounded-xl p-4 sm:p-5 mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            {{-- upload-01 icon --}}
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 14V6M12 6L14.5 8.5M12 6L9.5 8.5M4 18H20"/>
                </svg>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-foreground">Import via CSV</p>
                <p class="text-[11px] text-muted-foreground mt-0.5">Tambahkan banyak user sekaligus dari file CSV</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- users icon --}}
            <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.users.index') }}">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.1072 8.86495C6.4829 6.90273 7.81438 5.28 9.59848 4.49991C9.07975 3.60326 8.11027 3 6.99988 3C5.34303 3 3.99988 4.34315 3.99988 6C3.99988 7.34598 4.88629 8.48493 6.1072 8.86495ZM6.08454 11.0095C3.80371 11.159 2 13.0564 2 15.375C2 15.7202 2.27982 16 2.625 16H5.02185C5.60567 15.0881 6.41092 14.3317 7.36176 13.8064C6.71341 13.0173 6.2625 12.0598 6.08454 11.0095ZM18.9782 16H21.375C21.7202 16 22 15.7202 22 15.375C22 13.0563 20.1963 11.159 17.9154 11.0095C17.7375 12.0598 17.2866 13.0173 16.6383 13.8064C17.5891 14.3316 18.3944 15.0881 18.9782 16ZM17.8928 8.86489C19.1136 8.48481 19.9999 7.34591 19.9999 6C19.9999 4.34315 18.6567 3 16.9999 3C15.8895 3 14.9201 3.60322 14.4013 4.49984C16.1855 5.27989 17.517 6.90264 17.8928 8.86489Z"
                        fill="currentColor"/>
                    <path d="M12 13C13.6568 13 15 11.6569 15 10C15 8.34315 13.6568 7 12 7C10.3431 7 8.99998 8.34315 8.99998 10C8.99998 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M16.5 20H7.50001C7.22386 20 6.99999 19.7761 7 19.5C7.00006 17.567 8.56708 16 10.5001 16H13.5001C15.4331 16 17.0001 17.567 17 19.5C17 19.7762 16.7761 20 16.5 20Z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="hidden sm:inline">Kelola Users</span>
            </x-ui.button>

            <x-ui.button size="sm" onclick="openModal('modalImportUser')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 14V6M12 6L14.5 8.5M12 6L9.5 8.5M4 18H20"/>
                </svg>
                Import
            </x-ui.button>
        </div>
    </div>

    {{-- Progress bar --}}
    <div id="importProgressBody" class="{{ ($activeImportId ?? session('import_id')) ? '' : 'hidden' }} mt-4 pt-4 border-t border-border">
        <div class="flex items-center justify-between mb-1.5">
            <p id="importStatusTextBody" class="text-[11px] font-semibold text-foreground">Memproses impor...</p>
            <span id="importPercentTextBody" class="text-[11px] font-bold text-primary tabular-nums">0%</span>
        </div>
        <div class="h-1.5 bg-muted rounded-full overflow-hidden">
            <div id="importProgressBarBody" class="h-full bg-primary transition-all duration-500 rounded-full" style="width: 0%"></div>
        </div>
    </div>
</div>