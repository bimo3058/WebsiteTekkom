{{-- resources/views/components/ui/role-badge.blade.php --}}
@props([
    'role' => '',   // nama role (string)
    'size' => 'sm', // 'xs' (9px) | 'sm' (11px, default)
])

@php
    // Fill style: bg + text + border tipis
    $styleMap = [
        'superadmin'          => ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'],
        'dosen'               => ['bg' => '#E7F9F3', 'color' => '#00966E', 'border' => '#B2EBD9'],
        'mahasiswa'           => ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'],
        'gpm'                 => ['bg' => '#D1F0F9', 'color' => '#0C4D6E', 'border' => '#BAE6FD'],
        'alumni'              => ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'],
        'admin_banksoal'      => ['bg' => '#FDF6E9', 'color' => '#92400E', 'border' => '#FDE68A'],
        'admin_capstone'      => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#BFDBFE'],
        'admin_eoffice'       => ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'],
        'admin_kemahasiswaan' => ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'],
    ];

    $key = strtolower(trim($role));
    $st  = $styleMap[$key] ?? ['bg' => '#F0F5FF', 'color' => '#3B4FC2', 'border' => '#C7D2FE'];

    // Label tampilan yang lebih readable
    $label = match($key) {
        'superadmin'          => 'Super Admin',
        'admin_banksoal'      => 'Admin SIBASO',
        'admin_capstone'      => 'Admin SICATA',
        'admin_eoffice'       => 'Admin SIMENMA',
        'admin_kemahasiswaan' => 'Admin SIPERKOM',
        default               => ucfirst(str_replace('_', ' ', $role)),
    };

    $fontSize   = $size === 'xs' ? '9px'  : '11px';
    $padding    = $size === 'xs' ? '2px 8px' : '3px 12px';
    $fontWeight = $size === 'xs' ? '700' : '600';
@endphp

<span {{ $attributes->merge(['style' => "font-size:{$fontSize}; font-weight:{$fontWeight}; color:{$st['color']}; background:{$st['bg']}; border:1px solid {$st['border']}; padding:{$padding}; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;"]) }}>
    {{ $label }}
</span>
