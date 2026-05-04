{{-- resources/views/superadmin/modules/_card.blade.php --}}
@php
    $moduleIcons = [
        'bank_soal'           => 'M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z',
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

    $icon  = $moduleIcons[$module->slug] ?? 'M4 6h16M4 12h16M4 18h16';
    $col   = $moduleColors[$module->slug] ?? ['bg' => 'var(--c-bg)', 'color' => 'var(--c-fg-muted)'];
    $isOn  = $module->is_active;
@endphp

<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0px 1px 2px rgba(228,229,231,0.24); transition:border-color .15s, box-shadow .15s; display:flex; flex-direction:column;"
     onmouseover="this.style.borderColor='var(--c-primary-border)'; this.style.boxShadow='0 4px 12px rgba(11,38,110,0.08)'"
     onmouseout="this.style.borderColor='var(--c-border)'; this.style.boxShadow='0px 1px 2px rgba(228,229,231,0.24)'">
    <div style="padding:16px;">

        {{-- Top: icon + toggle --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
            <div style="width:38px; height:38px; border-radius:10px; background:{{ $col['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="{{ $col['color'] }}" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icon }}"/>
                </svg>
            </div>

            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                {{-- Toggle switch --}}
                <form action="{{ route('superadmin.modules.toggle', $module->slug) }}" method="POST">
                    @csrf
                    <button type="submit"
                            title="{{ $isOn ? 'Nonaktifkan' : 'Aktifkan' }}"
                            style="position:relative; width:32px; height:18px; border-radius:9999px; border:none; cursor:pointer; background:{{ $isOn ? 'var(--c-primary)' : 'var(--c-border)' }}; transition:background .2s; padding:0; flex-shrink:0;">
                        <div style="position:absolute; top:2px; left:2px; width:14px; height:14px; border-radius:50%; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.15); transition:transform .2s; transform:{{ $isOn ? 'translateX(14px)' : 'none' }};"></div>
                    </button>
                </form>
                <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:{{ $isOn ? 'var(--c-primary)' : 'var(--c-fg-muted)' }};">
                    {{ $isOn ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- Info --}}
        <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin-bottom:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $module->name }}</h3>
        <p style="font-size:11px; color:var(--c-fg-muted); line-height:1.5; margin-bottom:14px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:32px;">
            {{ $module->description ?? 'Fungsional modul ' . $module->name . '.' }}
        </p>

        {{-- Meta stats --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:14px;">
            @foreach([
                ['Upload', $module->setting('max_upload', 10) . ' MB'],
                ['Quota',  $module->setting('quota', 5) . ' GB'],
                ['Debug',  $module->setting('debug_mode') ? 'ON' : 'OFF'],
            ] as [$label, $val])
            <div style="background:var(--c-bg); border:1px solid var(--c-border); border-radius:8px; padding:7px 8px;">
                <p style="font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin-bottom:2px;">{{ $label }}</p>
                <p style="font-size:11px; font-weight:700; color:{{ ($label === 'Debug' && $module->setting('debug_mode')) ? 'var(--c-warning)' : 'var(--c-fg-sec)' }};">{{ $val }}</p>
            </div>
            @endforeach
        </div>

        {{-- Action button --}}
        <button onclick="openModal('modal-{{ $module->slug }}')"
                style="width:100%; padding:8px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; cursor:pointer; font-family:inherit; display:flex; align-items:center; justify-content:center; gap:6px; transition:background .15s;"
                onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6.78883 3.18702L9.45314 2.08342C10.0104 1.85259 10.6493 2.11723 10.8801 2.6745C11.0637 3.11762 11.5233 3.38148 12.0029 3.38184C12.4828 3.38219 12.9361 3.11793 13.1197 2.67459C13.3506 2.11727 13.9895 1.85261 14.5469 2.08346L17.211 3.187C17.7683 3.41784 18.033 4.05676 17.8021 4.61407C17.6185 5.0574 17.7523 5.56484 18.0918 5.90389C18.4312 6.24278 18.9429 6.38129 19.386 6.19774C19.9433 5.96691 20.5822 6.23155 20.813 6.78882L21.9166 9.45314C22.1474 10.0104 21.8828 10.6493 21.3255 10.8801C20.8824 11.0637 20.6185 11.5234 20.6182 12.003C20.6178 12.4828 20.8821 12.9362 21.3254 13.1198C21.8827 13.3507 22.1473 13.9896 21.9165 14.5469L20.813 17.2111C20.5821 17.7684 19.9432 18.033 19.3859 17.8022C18.9426 17.6186 18.4351 17.7523 18.0961 18.0918C17.7572 18.4312 17.6187 18.9429 17.8023 19.386C18.0331 19.9433 17.7685 20.5821 17.2112 20.813L14.5469 21.9166C13.9896 22.1474 13.3507 21.8828 13.1199 21.3255C12.9363 20.8824 12.4766 20.6185 11.997 20.6182C11.5171 20.6178 11.0637 20.8821 10.8801 21.3254C10.6492 21.8827 10.0103 22.1474 9.45297 21.9165L6.78887 20.813C6.23152 20.5822 5.96686 19.9432 6.19772 19.3859C6.38136 18.9425 6.24769 18.4351 5.90812 18.096C5.56872 17.7571 5.05713 17.6187 4.61402 17.8022C4.05674 18.0331 3.41786 17.7684 3.18703 17.2112L2.08343 14.5469C1.8526 13.9896 2.11723 13.3507 2.67451 13.1199C3.11762 12.9363 3.38149 12.4766 3.38185 11.997C3.3822 11.5171 3.11794 11.0638 2.67458 10.8801C2.11724 10.6493 1.85257 10.0103 2.08343 9.45299L3.18693 6.78891C3.41779 6.23157 4.05675 5.9669 4.61409 6.19776C5.05745 6.38141 5.56487 6.24771 5.90395 5.90813C6.24284 5.56874 6.38129 5.05713 6.19775 4.61401C5.96692 4.05674 6.23155 3.41785 6.78883 3.18702Z"/>
            </svg>
            Manage
        </button>
    </div>
</div>