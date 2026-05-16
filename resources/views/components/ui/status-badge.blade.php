{{-- resources/views/components/ui/status-badge.blade.php --}}
@props([
    'status' => 'active', // 'active' | 'suspended' | 'online' | 'offline' | 'system'
    'pulse'  => false,    // animasi pulse untuk dot (khusus online)
])

@php
    $cfg = match($status) {
        'active'    => ['color' => '#059669', 'border' => '#6EE7B7', 'dot' => '#22C55E',  'label' => 'Active',   'pulse' => false],
        'suspended' => ['color' => '#DC2626', 'border' => '#FECACA', 'dot' => '#DC2626',  'label' => 'Suspend',  'pulse' => false],
        'online'    => ['color' => '#059669', 'border' => '#A7F3D0', 'dot' => '#22C55E',  'label' => 'Online',   'pulse' => true],
        'offline'   => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF',  'label' => 'Offline',  'pulse' => false],
        'nonaktif'  => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF',  'label' => 'Nonaktif', 'pulse' => false],
        'system'    => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => null,        'label' => 'System',   'pulse' => false],
        default     => ['color' => '#6B7280', 'border' => '#E5E7EB', 'dot' => '#9CA3AF',  'label' => ucfirst($status), 'pulse' => false],
    };

    $doPulse = $pulse || $cfg['pulse'];
@endphp

<span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:{{ $cfg['color'] }}; border:1px solid {{ $cfg['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
    @if($cfg['dot'])
        <span style="width:6px; height:6px; border-radius:50%; background:{{ $cfg['dot'] }}; flex-shrink:0;{{ $doPulse ? ' animation:pulse-dot 2s infinite;' : '' }}"></span>
    @endif
    {{ $cfg['label'] }}
</span>
