{{-- resources/views/components/ui/user-avatar.blade.php --}}
@props([
    'user'      => null,   // User model instance atau null
    'size'      => 'sm',   // 'xs'=28px, 'sm'=32px, 'md'=36px, 'lg'=40px
    'onlineDot' => false,  // tampilkan dot hijau online
    'suspended' => false,  // mode suspended: grayscale + error color
    'gradient'  => null,   // override background dengan gradient string
])

@php
    $sizes = [
        'xs' => ['px' => 28, 'font' => '10px', 'icon' => 12, 'dot' => '7px',  'border' => '1.5px'],
        'sm' => ['px' => 32, 'font' => '11px', 'icon' => 14, 'dot' => '8px',  'border' => '1.5px'],
        'md' => ['px' => 36, 'font' => '12px', 'icon' => 14, 'dot' => '9px',  'border' => '1.5px'],
        'lg' => ['px' => 40, 'font' => '13px', 'icon' => 16, 'dot' => '10px', 'border' => '2px'],
    ];
    $s = $sizes[$size] ?? $sizes['sm'];

    $isSuperadmin = $user
        && $user->relationLoaded('roles')
        && $user->roles->pluck('name')->contains('superadmin');

    // Initials dari nama user
    $initials = '';
    if ($user) {
        $parts    = explode(' ', trim($user->name));
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
    }

    // Palet gradient colorful — auto-assign dari user ID
    $gradientPalette = [
        'linear-gradient(135deg,#D39C3D,#956321)',
        'linear-gradient(135deg,#5C78B8,#0B266E)',
        'linear-gradient(135deg,#40C4AA,#287F6E)',
        'linear-gradient(135deg,#ED8296,#95122B)',
        'linear-gradient(135deg,#8B5CF6,#5B21B6)',
        'linear-gradient(135deg,#34D399,#059669)',
        'linear-gradient(135deg,#F59E0B,#D97706)',
        'linear-gradient(135deg,#60A5FA,#2563EB)',
    ];
    $autoGradient = $gradientPalette[($user?->id ?? 0) % count($gradientPalette)];

    // Tentukan style wrapper
    if ($gradient) {
        // Gradient eksplisit dari caller
        $wrapStyle   = "background:{$gradient};";
        $textColor   = 'color:#fff;';
        $filterStyle = '';
    } elseif ($suspended) {
        $wrapStyle   = 'background:var(--c-error-subtle); border:1px solid #ED8296; opacity:0.7;';
        $textColor   = 'color:var(--c-error);';
        $filterStyle = 'filter:grayscale(0.5);';
    } elseif ($isSuperadmin) {
        $wrapStyle   = 'background:rgba(11,38,110,0.07); border:' . $s['border'] . ' solid rgba(11,38,110,0.15);';
        $textColor   = 'color:#0B266E;';
        $filterStyle = '';
    } else {
        // Colorful gradient otomatis berdasarkan user ID
        $wrapStyle   = "background:{$autoGradient};";
        $textColor   = 'color:#fff;';
        $filterStyle = '';
    }

    $showDot = $onlineDot && ($user?->is_online ?? false) && !$suspended;
@endphp

<div style="position:relative; flex-shrink:0;">
    <div style="width:{{ $s['px'] }}px; height:{{ $s['px'] }}px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:{{ $s['font'] }}; font-weight:700; {{ $wrapStyle }} {{ $filterStyle }}">
        @if($user?->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="avatar" loading="lazy" decoding="async"
                 style="width:100%; height:100%; object-fit:cover;">
        @elseif($isSuperadmin && !$gradient && !$suspended)
            {{-- Shield icon untuk superadmin --}}
            <svg width="{{ $s['icon'] }}" height="{{ $s['icon'] }}" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" stroke-width="1.8" stroke-linejoin="round"
                 style="{{ $textColor }}">
                <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
            </svg>
        @elseif($user)
            <span style="{{ $textColor }}">{{ $initials ?: '?' }}</span>
        @else
            {{-- Unknown/System user --}}
            <svg width="{{ $s['icon'] }}" height="{{ $s['icon'] }}" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" stroke-width="1.8" style="{{ $textColor }}">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        @endif
    </div>
    @if($showDot)
        <span style="position:absolute; bottom:-1px; right:-1px; width:{{ $s['dot'] }}; height:{{ $s['dot'] }}; border-radius:50%; background:#22C55E; border:{{ $s['border'] }} solid #fff;"></span>
    @endif
</div>
