{{-- resources/views/superadmin/dashboard/_header.blade.php --}}
<nav class="flex items-center gap-1.5 text-[11px] text-muted-foreground mb-3">
    <a href="/" class="hover:text-foreground transition-colors">Home</a>
    <span>/</span>
    <span class="text-foreground font-medium">Dashboard</span>
</nav>

<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 sm:mb-8 gap-4">
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-foreground tracking-tight">Superadmin Dashboard</h1>
            <p class="text-xs text-muted-foreground mt-0.5">
                Selamat datang, <span class="text-foreground font-medium">{{ auth()->user()->name }}</span>
            </p>
        </div>

        {{-- Import Progress Compact --}}
        <div id="importProgressContainer"
            data-import-id="{{ $activeImportId ?? session('import_id') ?? '' }}"
            class="hidden items-center gap-2 bg-white border border-border rounded-lg px-3 py-1.5">
            {{-- sync icon --}}
            <svg class="w-3.5 h-3.5 text-primary animate-spin shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span id="importPercentText" class="text-[11px] font-bold text-primary tabular-nums">0%</span>
            <button type="button" id="btnCancelImportHeader"
                class="shrink-0 text-muted-foreground hover:text-destructive transition-colors"
                title="Batalkan impor">
                {{-- minus / close --}}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="flex gap-2 items-center">
        {{-- Audit Logs — notification-text-square --}}
        <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.audit-logs') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                <path d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z"/>
            </svg>
            <span class="hidden sm:inline">Audit Logs</span>
        </x-ui.button>

        {{-- Permissions — shield --}}
        <x-ui.button size="sm" as="a" href="{{ route('superadmin.permissions') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round">
                <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
            </svg>
            <span class="hidden sm:inline">Permissions</span>
        </x-ui.button>
    </div>
</div>