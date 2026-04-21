{{-- resources/views/superadmin/dashboard/_modules.blade.php --}}
<div class="flex items-center gap-3 mb-3 sm:mb-4">
    <div class="w-1 h-4 sm:h-5 rounded bg-primary shrink-0"></div>
    <span class="text-sm sm:text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Modul Sistem</span>
    <x-ui.separator class="flex-1" />
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 mb-6 sm:mb-8">
    @foreach($modules as $moduleKey => $module)
    @php
        $variant = match($moduleKey) {
            'bank_soal'           => 'warning',
            'capstone'            => 'sky',
            'eoffice'             => 'purple',
            'manajemen_mahasiswa' => 'success',
            default               => 'secondary',
        };
        $isActive = $module['is_active'];
    @endphp
    <x-ui.card class="flex flex-col hover:border-primary-100 hover:shadow-md transition-all">
        <x-ui.card-content class="flex flex-col flex-1 !p-3 sm:!px-5 sm:!py-5">
            <div class="flex flex-wrap items-center justify-between gap-1.5 sm:gap-0 mb-2.5 sm:mb-3">
                <x-ui.badge :variant="$variant" class="!text-[8px] sm:!text-xs leading-none">{{ str_replace('_', ' ', $moduleKey) }}</x-ui.badge>
                <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit"
                            class="relative w-7 sm:w-9 h-4 sm:h-5 rounded-full transition-colors {{ $isActive ? 'bg-[#5E53F4]' : 'bg-[#DEE2E6]' }}"
                            title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <div class="absolute top-[2px] sm:top-[3px] left-[2px] sm:left-[3px] w-3 sm:w-3.5 h-3 sm:h-3.5 rounded-full bg-white shadow-sm transition-transform {{ $isActive ? 'translate-x-3 sm:translate-x-4' : '' }}"></div>
                    </button>
                </form>
            </div>
            
            <h3 class="text-[11px] sm:text-sm font-semibold text-grey-800 mb-1 leading-tight">{{ $module['name'] }}</h3>
            
            <p class="text-[9px] sm:text-xs text-muted-foreground leading-snug line-clamp-2 min-h-[28px] sm:min-h-[36px]">
                {{ $module['description'] ?? 'Manajemen fungsional untuk modul ' . $module['name'] . '.' }}
            </p>
            
            <div class="mt-auto pt-2.5 sm:pt-3 border-t border-border flex items-center justify-between">
                <span class="text-[8px] sm:text-[10px] font-semibold text-grey-300 uppercase tracking-wider">v{{ $module['version'] ?? '1.0' }}</span>
                <a href="{{ route('superadmin.modules') }}" class="text-[9px] sm:text-[11px] font-semibold text-primary hover:text-primary-400 transition-colors">Settings &rarr;</a>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    @endforeach
</div>