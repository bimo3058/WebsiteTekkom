{{-- resources/views/superadmin/users/show.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <style>
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .show-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 12px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
        .show-box { display: flex; flex-direction: column; flex: 1; background: #fff; border: 1px solid #D4D5D8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
        
        .badge-outline { display: inline-flex; align-items:center; gap:5px; padding:2px 10px; border-radius:99px; font-size:10px; font-weight:700; text-transform: capitalize; }
        .dot { width: 5px; height: 5px; border-radius: 50%; }
        
        .info-label { width: 160px; font-size: 13px; color: #94A3B8; flex-shrink: 0; font-weight: 500; }
        .info-value { font-size: 13px; font-weight: 600; color: #334155; }
        
        .module-card { border: 1px solid #E2E8F0; border-radius: 10px; overflow: hidden; background: #F8FAFC; }
    </style>

    <div class="show-wrap">
        <div class="show-box">
            {{-- ── Header / Toolbar ── --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#fff;border-bottom:1px solid #D4D5D8;flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <a href="{{ route('superadmin.users.index') }}"
                       style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;color:#475569;background:#fff;border:1px solid #D0D1D5;border-radius:8px;box-shadow:0 1px 2px rgba(0,0,0,.05);text-decoration:none;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h1 style="font-size:14px; font-weight:800; color:#1E293B; margin:0; text-transform:uppercase; letter-spacing:0.02em;">Detail User</h1>
                </div>

                <div style="display:flex;gap:10px;">
                    @if($user->suspended_at)
                        <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="padding:8px 16px;font-size:11px;font-weight:700;color:#fff;background:#10B981;border:none;border-radius:8px;cursor:pointer;">Unsuspend Akun</button>
                        </form>
                    @else
                        <button type="button" onclick="openSuspendModal({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                style="padding:8px 16px;font-size:11px;font-weight:700;color:#fff;background:#EF4444;border:none;border-radius:8px;cursor:pointer;">Suspend Akun</button>
                    @endif
                    <a href="{{ route('superadmin.users.edit', $user) }}" 
                       style="padding:8px 20px;font-size:11px;font-weight:700;color:#fff;background:#1E293B;border:none;border-radius:8px;text-decoration:none;">Edit Profil</a>
                </div>
            </div>

            {{-- ── Main Content Area ── --}}
            <div style="flex:1; overflow-y:auto; display:flex; flex-direction:column;">
                
                {{-- ── Profile Section ── --}}
                <div style="padding:20px; border-bottom:1px solid #d1d1d1; flex-shrink:0;">
                    <div style="display:flex; gap:20px; align-items:flex-start;">
                        
                        @php
                            $colors = ['#EEF2FF','#F0FDF4','#FFFBEB','#FEF2F2','#F5F3FF','#FDF2F9'];
                            $textColors = ['#4338CA','#15803D','#B45309','#B91C1C','#6D28D9','#BE185D'];
                            $colorIndex = $user->id % count($colors);
                            $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');
                            
                            $identityLabel = 'ID User';
                            $identityValue = $user->external_id;
                            
                            if($user->student) { 
                                $identityLabel = 'NIM'; 
                                $identityValue = $user->student->student_number; 
                            } elseif($user->lecturer) { 
                                $identityLabel = 'NIP/NIK'; 
                                $identityValue = $user->lecturer->employee_number; 
                            }
                        @endphp

                        <div style="width:84px; height:84px; border-radius:50%; background:{{ $colors[$colorIndex] }}; display:flex; align-items:center; justify-content:center; border:1.5px solid #F1F5F9; flex-shrink:0; overflow:hidden;">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <span style="font-size:28px; font-weight:800; color:{{ $textColors[$colorIndex] }};">{{ $initials }}</span>
                            @endif
                        </div>

                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:12px; margin-bottom:4px;">
                                <h2 style="font-size:20px; font-weight:800; color:#1E293B; margin:0; letter-spacing:-0.02em;">{{ $user->name }}</h2>
                                
                                <div style="display:flex; gap:6px;">
                                    @if($user->isSuspended())
                                        <div class="badge-outline" style="border:1px solid #EF4444; color:#EF4444;">
                                            <span class="dot" style="background:#EF4444;"></span> Suspended
                                        </div>
                                    @else
                                        <div class="badge-outline" style="border:1px solid #10B981; color:#10B981;">
                                            <span class="dot" style="background:#10B981;"></span> Active
                                        </div>
                                    @endif

                                    <div class="badge-outline" style="border:1px solid {{ $user->is_online ? '#3B82F6' : '#94A3B8' }}; color:{{ $user->is_online ? '#3B82F6' : '#94A3B8' }};">
                                        <span class="dot" style="background:{{ $user->is_online ? '#3B82F6' : '#94A3B8' }};"></span> {{ $user->is_online ? 'Online' : 'Offline' }}
                                    </div>
                                </div>
                            </div>
                            
                            <p style="font-size:14px; font-weight:500; color:#64748B; margin:0 0 16px 0;">{{ $identityLabel }}: {{ $identityValue }}</p>

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px 40px; max-width: 900px;">
                                <div style="display:flex; align-items:center;">
                                    <span class="info-label">Alamat Email</span>
                                    <span class="info-value">{{ $user->email }}</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span class="info-label">Access Role</span>
                                    <div style="display:flex; flex-wrap:wrap; gap:5px;">
                                        @forelse($user->roles as $role)
                                            @php
                                                $roleColors = [
                                                    'superadmin'        => ['text'=>'#0B266E', 'border'=>'#0B266E'],
                                                    'dosen'             => ['text'=>'#059669', 'border'=>'#059669'],
                                                    'mahasiswa'         => ['text'=>'#92400E', 'border'=>'#92400E'],
                                                    'gpm'               => ['text'=>'#0284C7', 'border'=>'#0284C7'],
                                                    'pengurus_himpunan' => ['text'=>'#6D28D9', 'border'=>'#6D28D9'],
                                                    'alumni'            => ['text'=>'#475569', 'border'=>'#475569'],
                                                ];
                                                $rc = $roleColors[$role->name] ?? ['text'=>'#475569','border'=>'#475569'];
                                            @endphp
                                            <span style="display:inline-flex; padding:2px 10px; font-size:11px; font-weight:700; color:{{ $rc['text'] }}; border:1px solid {{ $rc['border'] }}; background:transparent; border-radius:99px; text-transform:capitalize; white-space:nowrap;">
                                                {{ str_replace('_', ' ', $role->name) }}
                                            </span>
                                        @empty
                                            <span style="font-size:12px; color:#94A3B8; font-style:italic;">Tidak ada role</span>
                                        @endforelse
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span class="info-label">Nomor WhatsApp</span>
                                    <span class="info-value">{{ $user->whatsapp ?? '-' }}</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span class="info-label">Tanggal Daftar</span>
                                    <span class="info-value">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span class="info-label">Angkatan</span>
                                    <span class="info-value">{{ $user->student->cohort_year ?? '-' }}</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span class="info-label">Aktivitas Terakhir</span>
                                    <span class="info-value">{{ $user->last_login ? $user->last_login->diffForHumans() : 'Belum Pernah Login' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Role & Permissions Section (Pushed to Bottom) ── --}}
                <div style="display:flex; flex:1;">
                    <div style="width:240px; padding:20px; border-right:1px solid #d1d1d1; background:#fff;">
                        <h3 style="font-size:14px; font-weight:800; color:#1E293B; margin:0 0 8px 0;">Role & Permissions</h3>
                        <p style="font-size:12px; font-weight:500; color:#64748B; line-height:1.6; margin:0;">
                            Daftar izin modul yang dikunci (view-only). Untuk mengubah izin, silakan klik tombol Edit Profil.
                        </p>
                    </div>

                    <div style="flex:1; padding:20px;">
                        @php
                            $dbModules = \App\Models\SystemModule::orderBy('id')->get();

                            // Mapping slug DB → prefix permission — identik dengan edit.blade.php
                            $slugToPerm = [
                                'bank_soal'           => 'banksoal',
                                'manajemen_mahasiswa' => 'kemahasiswaan',
                                'capstone'            => 'capstone',
                                'eoffice'             => 'eoffice',
                            ];

                            // Style kartu per modul — identik dengan edit.blade.php
                            $moduleStyles = [
                                'bank_soal'           => ['bg'=>'#FDF6E9','border'=>'#EDD9A3','color'=>'#D97706','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                'capstone'            => ['bg'=>'#EFF6FF','border'=>'#BFDBFE','color'=>'#3B82F6','icon'=>'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7'],
                                'manajemen_mahasiswa' => ['bg'=>'#ECFDF5','border'=>'#A7F3D0','color'=>'#10B981','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                'eoffice'             => ['bg'=>'#FFF1F2','border'=>'#FECDD3','color'=>'#F43F5E','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                            ];
                            $defaultStyle = ['bg'=>'#EDE9FE','border'=>'#C4B5FD','color'=>'#8B5CF6','icon'=>'M4 6h16M4 10h16M4 14h16M4 18h16'];
                        @endphp

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                            @foreach($dbModules as $mod)
                                @php
                                    $mkey    = $mod->slug;
                                    $pPrefix = $slugToPerm[$mkey] ?? $mkey;
                                    $style   = $moduleStyles[$mkey] ?? $defaultStyle;
                                    $perms   = \App\Models\Permission::where('name', 'like', $pPrefix . '.%')->get();
                                @endphp
                                <div class="module-card" style="opacity: {{ $mod->is_active ? '1' : '0.55' }};">
                                    {{-- Header kartu dengan warna & ikon modul --}}
                                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:#FAFAFA; border-bottom:1px solid #E2E8F0;">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div style="width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:{{ $style['bg'] }}; border:1px solid {{ $style['border'] }};">
                                                <svg width="12" height="12" fill="none" stroke="{{ $style['color'] }}" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="{{ $style['icon'] }}"/>
                                                </svg>
                                            </div>
                                            <span style="font-size:11px; font-weight:800; color:#1E293B; text-transform:uppercase; letter-spacing:0.03em;">{{ $mod->name }}</span>
                                        </div>
                                        <span style="font-size:9px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.05em;">View Only</span>
                                    </div>

                                    {{-- Daftar permission — pakai permissions yang sudah eager-loaded, bukan hasPermissionTo() agar tidak N+1 --}}
                                    <div style="padding:14px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                        @forelse($perms as $perm)
                                            @php
                                                $hasPerm = $user->permissions->contains('name', $perm->name);
                                            @endphp
                                            <div style="display:flex; align-items:center; gap:8px; opacity: {{ $hasPerm ? '1' : '0.3' }}">
                                                <input type="checkbox" {{ $hasPerm ? 'checked' : '' }} disabled
                                                    style="width:14px; height:14px; accent-color:{{ $style['color'] }}; cursor:not-allowed;">
                                                <span style="font-size:12px; font-weight:600; color:#475569;">
                                                    {{ $perm->display_name ?? Str::title(Str::after($perm->name, '.')) }}
                                                </span>
                                            </div>
                                        @empty
                                            <p style="font-size:11px; color:#94A3B8; grid-column:span 2; margin:0;">
                                                Tidak ada permission terdaftar.
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('superadmin.users._modal_suspend')

</x-sidebar>
</x-app-layout>