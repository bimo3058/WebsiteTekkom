{{-- Role badge partial - displays a user's primary role as a styled badge with icons and distinct colors --}}
@php
    $roleUser = $roleUser ?? null;
    if ($roleUser && $roleUser->relationLoaded('roles') && $roleUser->roles->isNotEmpty()) {
        $rolePriority = [
            'superadmin' => 1,
            'admin' => 2,
            'admin_kemahasiswaan' => 3,
            'admin_banksoal' => 4,
            'admin_capstone' => 5,
            'admin_eoffice' => 6,
            'dosen' => 7,
            'gpm' => 8,
            'ketua_himpunan' => 9,
            'wakil_ketua_himpunan' => 10,
            'ketua_bidang' => 11,
            'ketua_unit' => 12,
            'pengurus_himpunan' => 13,
            'staff_himpunan' => 14,
            'alumni' => 15,
            'mahasiswa' => 16,
        ];

        $roleLabels = [
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'admin_kemahasiswaan' => 'Admin',
            'admin_banksoal' => 'Admin',
            'admin_capstone' => 'Admin',
            'admin_eoffice' => 'Admin',
            'dosen' => 'Dosen',
            'gpm' => 'GPM',
            'ketua_himpunan' => 'Ketua HMP',
            'wakil_ketua_himpunan' => 'Wakil Ketua HMP',
            'ketua_bidang' => 'Ketua Bidang',
            'ketua_unit' => 'Ketua Unit',
            'pengurus_himpunan' => 'Pengurus HMP',
            'staff_himpunan' => 'Staff HMP',
            'alumni' => 'Alumni',
            'mahasiswa' => 'Mahasiswa',
        ];

        // Custom colors for each role
        $roleStyles = [
            'superadmin'           => 'background: linear-gradient(135deg, #be123c 0%, #fb7185 100%); color: #fff;',
            'admin'                => 'background: linear-gradient(135deg, #b45309 0%, #fcd34d 100%); color: #fff;',
            'admin_kemahasiswaan'  => 'background: linear-gradient(135deg, #b45309 0%, #fcd34d 100%); color: #fff;',
            'admin_banksoal'       => 'background: linear-gradient(135deg, #b45309 0%, #fcd34d 100%); color: #fff;',
            'admin_capstone'       => 'background: linear-gradient(135deg, #b45309 0%, #fcd34d 100%); color: #fff;',
            'admin_eoffice'        => 'background: linear-gradient(135deg, #b45309 0%, #fcd34d 100%); color: #fff;',
            'dosen'                => 'background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%); color: #fff;',
            'gpm'                  => 'background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); color: #fff;',
            'ketua_himpunan'       => 'background: linear-gradient(135deg, #047857 0%, #34d399 100%); color: #fff;',
            'wakil_ketua_himpunan' => 'background: linear-gradient(135deg, #0e7490 0%, #22d3ee 100%); color: #fff;',
            'ketua_bidang'         => 'background: linear-gradient(135deg, #0369a1 0%, #38bdf8 100%); color: #fff;',
            'ketua_unit'           => 'background: linear-gradient(135deg, #4338ca 0%, #818cf8 100%); color: #fff;',
            'pengurus_himpunan'    => 'background: #ecfdf5; color: #047857; border: 1px solid #10b981;',
            'staff_himpunan'       => 'background: #f0fdf4; color: #15803d; border: 1px solid #22c55e;',
            'alumni'               => 'background: #fff1f2; color: #be123c; border: 1px solid #fb7185;',
            'mahasiswa'            => 'background: #f0f9ff; color: #0369a1; border: 1px solid #0ea5e9;',
        ];

        // Optional icons for specific roles
        $roleIcons = [
            'superadmin' => '<svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="margin-right:3px"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>',
            'admin' => '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:3px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
            'dosen' => '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:3px"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'mahasiswa' => '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:3px"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
        ];

        $primaryRole = $roleUser->roles->sortBy(fn($r) => $rolePriority[$r->name] ?? 99)->first();
        $roleName = $primaryRole->name ?? null;
        $roleLabel = $roleLabels[$roleName] ?? ucfirst(str_replace('_', ' ', $roleName ?? ''));
        $roleStyle = $roleStyles[$roleName] ?? 'background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db;';
        
        // Find suitable icon (defaults for categories if specific not found)
        $icon = $roleIcons[$roleName] ?? '';
        if (!$icon) {
            if (str_contains($roleName, 'admin')) $icon = $roleIcons['admin'];
            elseif (str_contains($roleName, 'himpunan')) $icon = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:3px"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
        }
    } else {
        $roleName = null;
    }
@endphp

@if($roleName)
    <span class="role-badge d-inline-flex align-items-center" style="{{ $roleStyle }} font-size: {{ $badgeSize ?? '10px' }}; font-weight: 700; padding: 2px 8px; border-radius: 6px; white-space: nowrap; line-height: 1; text-transform: uppercase; letter-spacing: 0.02em;">
        {!! $icon !!}
        {{ $roleLabel }}
    </span>
@endif
