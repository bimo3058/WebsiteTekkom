{{-- resources/views/superadmin/dashboard/_modules.blade.php --}}

<div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
    <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg); white-space:nowrap;">Modul Sistem</span>
    <div style="flex:1; height:1px; background:var(--c-border);"></div>
    <a href="{{ route('superadmin.modules') }}"
       style="font-size:11px; font-weight:600; color:var(--c-primary); text-decoration:none; display:flex; align-items:center; gap:3px; white-space:nowrap; transition:opacity .15s;"
       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
        Kelola
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
    </a>
</div>

{{-- Logic Icons & Colors Sama Seperti Sebelumnya --}}
@php
    $moduleIcons = [
        'bank_soal'           => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9h6m-6 4h6',
        'capstone'            => 'M17.7267 20C19.0393 20 20.0238 18.8454 19.7664 17.6078L18.5184 11.6078C18.3239 10.6729 17.4702 10 16.4787 10H4.92359M17.7267 20H5.9798H5.32879C4.33727 20 3.48358 19.3271 3.28913 18.3922L2.0411 12.3922C1.78368 11.1546 2.76815 10 4.08076 10H4.92359M17.7267 20H18.4795C19.5061 20 20.3792 19.2798 20.5353 18.3041L21.9754 9.30411C22.1692 8.0926 21.1943 7 19.9195 7H15.137C14.4416 7 13.7921 6.6658 13.4063 6.1094L12.5613 4.8906C12.1755 4.3342 11.526 4 10.8306 4H7.53984C6.49082 4 5.60597 4.75107 5.47585 5.75193L4.92359 10',
        'eoffice'             => 'M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15',
        'manajemen_mahasiswa' => 'M17 20.6622V19.5C17 17.2909 15.2091 15.5 13 15.5H11C8.79086 15.5 7 17.2909 7 19.5V20.6622M17 20.6622C19.989 18.9331 22 15.7014 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.7014 4.01099 18.9331 7 20.6622M17 20.6622C15.5291 21.513 13.8214 22 12 22C10.1786 22 8.47087 21.513 7 20.6622M15 9C15 10.6569 13.6569 12 12 12C10.3431 12 9 10.6569 9 9C9 7.34315 10.3431 6 12 6C13.6569 6 15 7.34315 15 9Z',
    ];
    $moduleColors = [
        'bank_soal'           => ['bg' => '#F9ECCB', 'color' => '#956321'],
        'capstone'            => ['bg' => '#D1F0F9', 'color' => '#0C4D6E'],
        'eoffice'             => ['bg' => 'rgba(11,38,110,0.08)', 'color' => 'var(--c-primary)'],
        'manajemen_mahasiswa' => ['bg' => '#DDF2EE', 'color' => '#287F6E'],
    ];
@endphp

{{-- Ubah agar grid-template-columns otomatis 4 --}}
<div class="dash-modules" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; margin-bottom:24px;">
    @foreach($modules as $moduleKey => $module)
    @php
        $isActive = $module['is_active'];
        $icon     = $moduleIcons[$moduleKey] ?? $moduleIcons['bank_soal'];
        $col      = $moduleColors[$moduleKey] ?? ['bg' => 'var(--c-bg)', 'color' => 'var(--c-fg-muted)'];
    @endphp
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:14px; display:flex; flex-direction:column; gap:12px; box-shadow:0px 1px 2px rgba(228,229,231,0.24); transition:border-color .15s, box-shadow .15s;"
         onmouseover="this.style.borderColor='var(--c-primary-border)'; this.style.boxShadow='0 4px 12px rgba(11,38,110,0.08)'"
         onmouseout="this.style.borderColor='var(--c-border)'; this.style.boxShadow='0px 1px 2px rgba(228,229,231,0.24)'">

        <div style="display:flex; align-items:center; justify-content:space-between;">
            <div style="width:34px; height:34px; border-radius:9px; background:{{ $col['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="{{ $col['color'] }}" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icon }}"/>
                </svg>
            </div>
            <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST">
                @csrf
                <button type="submit"
                        title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}"
                        style="position:relative; width:32px; height:18px; border-radius:9999px; border:none; cursor:pointer; background:{{ $isActive ? 'var(--c-primary)' : 'var(--c-border)' }}; transition:background .2s; padding:0;">
                    <div style="position:absolute; top:2px; left:2px; width:14px; height:14px; border-radius:50%; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.15); transition:transform .2s; transform:{{ $isActive ? 'translateX(14px)' : 'none' }};"></div>
                </button>
            </form>
        </div>

        <div>
            <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                <h3 style="font-size:12px; font-weight:700; color:var(--c-fg); line-height:1.3;">{{ $module['name'] }}</h3>
                @if(!$isActive)
                    <span style="font-size:9px; font-weight:600; color:var(--c-fg-muted); background:var(--c-bg); border:1px solid var(--c-border); padding:1px 5px; border-radius:4px;">Off</span>
                @else
                    <span style="font-size:9px; font-weight:600; color:#287F6E; background:#DDF2EE; padding:1px 5px; border-radius:4px;">On</span>
                @endif
            </div>
            <p style="font-size:10px; color:var(--c-fg-muted); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                {{ $module['description'] ?? 'Fungsional modul ' . $module['name'] . '.' }}
            </p>
        </div>

        <div style="padding-top:10px; border-top:1px solid var(--c-border); display:flex; align-items:center; justify-content:space-between; margin-top:auto;">
            <span style="font-size:10px; color:var(--c-fg-muted); font-variant-numeric:tabular-nums;">v{{ $module['version'] ?? '1.0' }}</span>
            <a href="{{ route('superadmin.modules') }}"
               style="font-size:11px; font-weight:600; color:var(--c-primary); text-decoration:none; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                Settings →
            </a>
        </div>
    </div>
    @endforeach
</div>

<style>
/* Fallback grid ke scrollable flex untuk mobile agar tidak bertumpuk */
@media (max-width: 1024px) { 
    .dash-modules { display: flex !important; overflow-x: auto; padding-bottom: 8px; }
    .dash-modules > div { min-width: 200px; flex: 1; }
}
</style>