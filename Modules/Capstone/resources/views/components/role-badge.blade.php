@props([
    'role' => 'default',
])
@php
    $variants = [
        'superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
        'admin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
        'dosen' => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
        'mahasiswa' => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
        'default' => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]',
    ];
    $classes = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider ' . ($variants[$role] ?? $variants['default']);
@endphp
<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
