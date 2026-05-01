{{-- resources/views/superadmin/dashboard/_modules.blade.php --}}
<div class="flex items-center gap-2 mb-3">
    <span class="text-xs font-bold text-foreground tracking-tight uppercase">Modul Sistem</span>
    <div class="flex-1 h-px bg-border"></div>
    <a href="{{ route('superadmin.modules') }}" class="text-[11px] text-primary hover:text-primary-400 font-medium transition-colors flex items-center gap-1">
        Kelola
        {{-- arrow-narrow-right --}}
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 12H4M20 12L14 6M20 12L14 18"/>
        </svg>
    </a>
</div>

@php
    $moduleIcons = [
        'bank_soal'           => 'M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z',
        'capstone'            => 'M17.7267 20C19.0393 20 20.0238 18.8454 19.7664 17.6078L18.5184 11.6078C18.3239 10.6729 17.4702 10 16.4787 10H4.92359M17.7267 20H5.9798H5.32879C4.33727 20 3.48358 19.3271 3.28913 18.3922L2.0411 12.3922C1.78368 11.1546 2.76815 10 4.08076 10H4.92359M17.7267 20H18.4795C19.5061 20 20.3792 19.2798 20.5353 18.3041L21.9754 9.30411C22.1692 8.0926 21.1943 7 19.9195 7H15.137C14.4416 7 13.7921 6.6658 13.4063 6.1094L12.5613 4.8906C12.1755 4.3342 11.526 4 10.8306 4H7.53984C6.49082 4 5.60597 4.75107 5.47585 5.75193L4.92359 10',
        'eoffice'             => 'M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15',
        'manajemen_mahasiswa' => 'M17 20.6622V19.5C17 17.2909 15.2091 15.5 13 15.5H11C8.79086 15.5 7 17.2909 7 19.5V20.6622M17 20.6622C19.989 18.9331 22 15.7014 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.7014 4.01099 18.9331 7 20.6622M17 20.6622C15.5291 21.513 13.8214 22 12 22C10.1786 22 8.47087 21.513 7 20.6622M15 9C15 10.6569 13.6569 12 12 12C10.3431 12 9 10.6569 9 9C9 7.34315 10.3431 6 12 6C13.6569 6 15 7.34315 15 9Z',
    ];
    $moduleColors = [
        'bank_soal'           => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
        'capstone'            => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600'],
        'eoffice'             => ['bg' => 'bg-primary-50', 'text' => 'text-primary'],
        'manajemen_mahasiswa' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 mb-6 sm:mb-8">
    @foreach($modules as $moduleKey => $module)
    @php
        $isActive = $module['is_active'];
        $icon     = $moduleIcons[$moduleKey] ?? $moduleIcons['bank_soal'];
        $col      = $moduleColors[$moduleKey] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600'];
    @endphp
    <div class="bg-white border border-border rounded-xl p-4 flex flex-col gap-3 hover:border-primary-100 transition-colors">
        
        {{-- Top row: icon + toggle --}}
        <div class="flex items-center justify-between">
            <div class="w-8 h-8 rounded-lg {{ $col['bg'] }} {{ $col['text'] }} flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icon }}"/>
                </svg>
            </div>
            <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST">
                @csrf
                <button type="submit"
                        class="relative w-8 h-4.5 rounded-full transition-colors {{ $isActive ? 'bg-primary' : 'bg-border' }}"
                        style="height: 18px; width: 32px;"
                        title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
                    <div class="absolute top-[2px] left-[2px] w-3.5 h-3.5 rounded-full bg-white shadow-sm transition-transform {{ $isActive ? 'translate-x-[14px]' : '' }}"></div>
                </button>
            </form>
        </div>

        {{-- Info --}}
        <div>
            <div class="flex items-center gap-1.5 mb-1">
                <h3 class="text-[12px] font-semibold text-foreground leading-tight">{{ $module['name'] }}</h3>
                @if(!$isActive)
                    <span class="text-[9px] font-semibold text-muted-foreground bg-muted px-1.5 py-0.5 rounded">Off</span>
                @endif
            </div>
            <p class="text-[10px] text-muted-foreground leading-relaxed line-clamp-2">
                {{ $module['description'] ?? 'Fungsional modul ' . $module['name'] . '.' }}
            </p>
        </div>

        {{-- Footer --}}
        <div class="pt-2 border-t border-border flex items-center justify-between mt-auto">
            <span class="text-[9px] font-medium text-muted-foreground">v{{ $module['version'] ?? '1.0' }}</span>
            <a href="{{ route('superadmin.modules') }}" class="text-[10px] font-semibold text-primary hover:text-primary-400 transition-colors">
                Settings →
            </a>
        </div>
    </div>
    @endforeach
</div>