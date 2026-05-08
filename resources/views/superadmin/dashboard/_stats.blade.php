{{-- resources/views/superadmin/dashboard/_stats.blade.php --}}
@php
$stats = [
    ['label' => 'Total User',   'value' => $total_users,         'icon' => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z'],
    ['label' => 'Superadmin',   'value' => $total_superadmins,   'icon' => 'M4 6h16v14H4zM9 6V4h6v2M12 12v4M10 14h4'],
    ['label' => 'Admin Modul',  'value' => $total_admin_modul,   'icon' => 'M9 11a4 4 0 100-8 4 4 0 000 8zM3 20a6 6 0 0112 0M14 12l2 2 3-3'],
    ['label' => 'Dosen',        'value' => $total_lecturers,     'icon' => 'M9 11a4 4 0 100-8 4 4 0 000 8zM3 20a6 6 0 0112 0'],
    ['label' => 'GPM',          'value' => $total_gpm,           'icon' => 'M9 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM2.5 20a6.5 6.5 0 0113 0M17 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z'],
    ['label' => 'Mahasiswa',    'value' => $total_students,      'icon' => 'M9 11a4 4 0 100-8 4 4 0 000 8zM3 20a6 6 0 0112 0'],
];
@endphp

<div class="dash-stats" style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:24px;">
    @foreach($stats as $stat)
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
         onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
         onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
            <div style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">{{ $stat['label'] }}</p>
        </div>
        <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
            {{ number_format($stat['value']) }}
        </p>
    </div>
    @endforeach
</div>

<style>
@media (max-width: 1280px) { .dash-stats { grid-template-columns: repeat(3, 1fr) !important; } }
@media (max-width: 640px)  { .dash-stats { grid-template-columns: repeat(2, 1fr) !important; } }
</style>
