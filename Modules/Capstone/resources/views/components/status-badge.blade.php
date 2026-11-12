@props([
    'status' => 'active',
    'pulse' => false,
])
@php
    $cfg = match ($status) {
        'active' => ['color' => '#059669', 'border' => '#6EE7B7', 'dot' => '#22C55E', 'label' => 'Active', 'pulse' => false],
        'suspended' => ['color' => '#DC2626', 'border' => '#FECACA', 'dot' => '#DC2626', 'label' => 'Suspend', 'pulse' => false],
        'online' => ['color' => '#059669', 'border' => '#A7F3D0', 'dot' => '#22C55E', 'label' => 'Online', 'pulse' => true],
        'offline' => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF', 'label' => 'Offline', 'pulse' => false],
        'nonaktif' => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF', 'label' => 'Nonaktif', 'pulse' => false],
        'system' => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => null, 'label' => 'System', 'pulse' => false],
        default => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF', 'label' => ucfirst($status), 'pulse' => false],
    };
    $doPulse = $pulse || $cfg['pulse'];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-3 py-0.5 text-[11px] font-medium']) }} style="color:{{ $cfg['color'] }};border-color:{{ $cfg['border'] }};">
    @if ($cfg['dot'])
        <span aria-hidden="true" style="width:6px;height:6px;border-radius:50%;background:{{ $cfg['dot'] }};flex-shrink:0;{{ $doPulse ? 'animation:pulse-dot 2s infinite;' : '' }}"></span>
    @endif
    {{ $cfg['label'] }}
</span>
