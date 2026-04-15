{{-- resources/views/superadmin/dashboard/_modules.blade.php --}}
<div class="flex items-center gap-3 mb-4">
    <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
    <span class="text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Modul Sistem</span>
    <x-ui.separator class="flex-1" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
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
        <x-ui.card-content class="flex flex-col flex-1 !px-5 !py-5">
            <div class="flex items-center justify-between mb-3.5">
                <x-ui.badge :variant="$variant">{{ str_replace('_', ' ', $moduleKey) }}</x-ui.badge>
                <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit"
                            class="relative w-9 h-5 rounded-full transition-colors {{ $isActive ? 'bg-[#5E53F4]' : 'bg-[#DEE2E6]' }}"
                            title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <div class="absolute top-[3px] left-[3px] w-3.5 h-3.5 rounded-full bg-white shadow-sm transition-transform {{ $isActive ? 'translate-x-4' : '' }}"></div>
                    </button>
                </form>
            </div>
            <h3 class="text-sm font-semibold text-grey-800 mb-1">{{ $module['name'] }}</h3>
            <p class="text-xs text-muted-foreground leading-relaxed line-clamp-2 min-h-[36px]">
                {{ $module['description'] ?? 'Manajemen fungsional untuk modul ' . $module['name'] . '.' }}
            </p>
            <div class="mt-auto pt-3.5 border-t border-border flex items-center justify-between">
                <span class="text-[10px] font-semibold text-grey-300 uppercase tracking-wider">v{{ $module['version'] ?? '1.0' }}</span>
                <a href="{{ route('superadmin.modules') }}" class="text-[11px] font-semibold text-primary hover:text-primary-400 transition-colors">Settings →</a>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    @endforeach
</div>