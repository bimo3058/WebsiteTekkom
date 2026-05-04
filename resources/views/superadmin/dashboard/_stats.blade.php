{{-- resources/views/superadmin/dashboard/_stats.blade.php --}}
@php
$stats = [
    ['label' => 'Total Users', 'value' => $total_users, 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M17 21v-2a4 4 0 0 0-3-3.87M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75M9 7a4 4 0 1 1 0-8 4 4 0 0 1 0 8z', 'iconBg' => '#D1F0F9', 'iconColor' => '#0C4D6E'],
    ['label' => 'Superadmins', 'value' => $total_superadmins, 'icon' => 'M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z', 'iconBg' => 'rgba(11,38,110,0.08)', 'iconColor' => 'var(--c-primary)'],
    ['label' => 'Admin Modul', 'value' => $total_admin_modul, 'icon' => 'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM5.317 15.821A7 7 0 0 1 19 17v1H5v-1a7.002 7.002 0 0 1 .317-2.179ZM14 13l2 2 4-4', 'iconBg' => '#EEF2FF', 'iconColor' => '#4F46E5'],
    ['label' => 'Dosen', 'value' => $total_lecturers, 'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'iconBg' => '#DDF2EE', 'iconColor' => '#287F6E'],
    ['label' => 'GPM', 'value' => $total_gpm, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'iconBg' => '#FADAE1', 'iconColor' => '#95122B'],
    ['label' => 'Mahasiswa', 'value' => $total_students, 'icon' => 'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z', 'iconBg' => '#F9ECCB', 'iconColor' => '#956321'],
];
@endphp

<div class="dash-stats" style="display:grid; grid-template-columns:repeat(6, 1fr); gap:10px; margin-bottom:20px;">
    @foreach($stats as $stat)
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:10px; padding:14px 14px; display:flex; align-items:center; justify-content:space-between; box-shadow:0px 1px 2px rgba(228,229,231,0.24); transition:border-color .15s, box-shadow .15s, transform .15s; cursor:default;"
         onmouseover="this.style.borderColor='var(--c-primary-border)'; this.style.boxShadow='0 4px 12px rgba(11,38,110,0.08)'; this.style.transform='translateY(-1px)'"
         onmouseout="this.style.borderColor='var(--c-border)'; this.style.boxShadow='0px 1px 2px rgba(228,229,231,0.24)'; this.style.transform='none'">
        <div>
            <p style="font-size:9px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:var(--c-fg-muted); margin-bottom:4px;">
                {{ $stat['label'] }}
            </p>
            <p style="font-size:22px; font-weight:700; color:var(--c-fg); line-height:1; letter-spacing:-0.02em;">
                {{ number_format($stat['value']) }}
            </p>
        </div>
        <div style="width:32px; height:34px; border-radius:8px; background:{{ $stat['iconBg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="{{ $stat['iconColor'] }}" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $stat['icon'] }}"/>
            </svg>
        </div>
    </div>
    @endforeach
</div>

<style>
/* Responsive fix agar di layar kecil tidak terlalu sempit */
@media (max-width: 1024px) { .dash-stats { grid-template-columns: repeat(3, 1fr) !important; } }
@media (max-width: 640px) { .dash-stats { grid-template-columns: repeat(2, 1fr) !important; } }
</style>