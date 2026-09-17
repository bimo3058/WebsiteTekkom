{{-- resources/views/components/ui/user-avatar.blade.php --}}
@props([
    'user'      => null,   // User model instance atau null
    'size'      => 'sm',   // 'xs'=28px, 'sm'=32px, 'md'=36px, 'lg'=40px
    'onlineDot' => false,  // tampilkan dot hijau online
    'suspended' => false,  // mode suspended: grayscale
])

@php
    $sizes = [
        'xs' => ['px' => 28, 'font' => '10px', 'icon' => 12, 'dot' => '7px',  'border' => '1.5px'],
        'sm' => ['px' => 32, 'font' => '11px', 'icon' => 14, 'dot' => '8px',  'border' => '1.5px'],
        'md' => ['px' => 36, 'font' => '12px', 'icon' => 14, 'dot' => '9px',  'border' => '1.5px'],
        'lg' => ['px' => 40, 'font' => '13px', 'icon' => 16, 'dot' => '10px', 'border' => '2px'],
        'xl' => ['px' => 84, 'font' => '28px', 'icon' => 32, 'dot' => '12px', 'border' => '1.5px'],
    ];
    $s = $sizes[$size] ?? $sizes['sm'];

    // Initials dari nama user
    $initials = '';
    if ($user) {
        $parts    = explode(' ', trim($user->name));
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
    }

    // Foto profil atau inisial dengan warna netral yang sama untuk semua pengguna.
    $wrapStyle = 'background:#F3F4F6; border:' . $s['border'] . ' solid #E5E7EB;';
    $textColor = 'color:#6B7280;';
    $filterStyle = $suspended ? 'filter:grayscale(0.5); opacity:0.7;' : '';

    $showDot = $onlineDot && ($user?->is_online ?? false) && !$suspended;
@endphp

<div style="position:relative; flex-shrink:0;">
    <div style="width:{{ $s['px'] }}px; height:{{ $s['px'] }}px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:{{ $s['font'] }}; font-weight:700; {{ $wrapStyle }} {{ $filterStyle }}">
        @if($user?->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="avatar" loading="lazy" decoding="async"
                 style="width:100%; height:100%; object-fit:cover;">
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
