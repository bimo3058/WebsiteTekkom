{{-- resources/views/components/ui/badge.blade.php --}}
@props([
    'variant' => 'default',
])

@php
    $base = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2';

    $variants = [
        'default'     => 'bg-primary text-primary-foreground hover:bg-primary/80',
        'secondary'   => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/80',
        'outline'     => 'text-foreground border border-border',
        'success'     => 'bg-success-50 text-success-300 border border-success-200/30',
        'warning'     => 'bg-warning-50 text-warning-300 border border-warning-200/30',
        'sky'         => 'bg-sky-50 text-sky-300 border border-sky-200/30',
        'purple'      => 'bg-primary-50 text-primary-400 border border-primary-200/30',

        // ── Role variants — warna sama persis dengan tabel user management ──
        'role-superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border border-[#D1BFFF] uppercase tracking-wider',
        'role-dosen'      => 'bg-[#E7F9F3] text-[#00C08D] border border-[#B2EBD9] uppercase tracking-wider',
        'role-mahasiswa'  => 'bg-[#FFF9E6] text-[#FFB800] border border-[#FFEBB3] uppercase tracking-wider',
        'role-default'    => 'bg-[#F0F5FF] text-[#5E53F4] border border-[#D1DFFF] uppercase tracking-wider',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>