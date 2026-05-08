{{-- resources/views/superadmin/permission/permissions.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

<style>
    .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
    .perm-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
    .perm-box  { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #F2F3F5; border: 1px solid #D4D5D8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
</style>

@php
    // 1. Mapping manual: Slug Database => Prefix Permission Database
    $slugToPerm = [
        'bank_soal'           => 'banksoal',
        'manajemen_mahasiswa' => 'kemahasiswaan',
        'capstone'            => 'capstone',
        'eoffice'             => 'eoffice'
    ];

    // 2. Mapping Style Modul
    $moduleStyles = [
        'bank_soal'           => ['bg'=>'#FDF6E9','border'=>'#EDD9A3','color'=>'#D97706','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'capstone'            => ['bg'=>'#EFF6FF','border'=>'#BFDBFE','color'=>'#3B82F6','icon'=>'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7'],
        'manajemen_mahasiswa' => ['bg'=>'#ECFDF5','border'=>'#A7F3D0','color'=>'#10B981','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        'eoffice'             => ['bg'=>'#FFF1F2','border'=>'#FECDD3','color'=>'#F43F5E','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
    $defaultStyle = ['bg'=>'#EDE9FE','border'=>'#C4B5FD','color'=>'#8B5CF6','icon'=>'M4 6h16M4 10h16M4 14h16M4 18h16'];

    // 3. Definisi Role List (Dibutuhkan untuk label UI)
    $roleList = [
        'mahasiswa'           => ['label' => 'Mahasiswa',           'color' => '#D97706'],
        'dosen'               => ['label' => 'Dosen',               'color' => '#10B981'],
        'gpm'                 => ['label' => 'GPM',                 'color' => '#0EA5E9'],
        'pengurus_himpunan'   => ['label' => 'Pengurus Himpunan',   'color' => '#8B5CF6'],
        'alumni'              => ['label' => 'Alumni',              'color' => '#64748B'],
        'admin_banksoal'      => ['label' => 'Admin Bank Soal',     'color' => '#3B82F6'],
        'admin_capstone'      => ['label' => 'Admin Capstone',      'color' => '#3B82F6'],
        'admin_eoffice'       => ['label' => 'Admin E-Office',      'color' => '#3B82F6'],
        'admin_kemahasiswaan' => ['label' => 'Admin Kemahasiswaan', 'color' => '#3B82F6'],
        'admin'               => ['label' => 'Admin',               'color' => '#3B82F6'],
        'superadmin'          => ['label' => 'Superadmin',          'color' => '#EF4444'],
    ];

    // 4. Inisialisasi Role Aktif (PENTING: Agar tidak error Undefined Variable)
    $activeRole  = request('role', 'mahasiswa');
    $activeColor = $roleList[$activeRole]['color'] ?? '#64748B';
    $activeLabel = $roleList[$activeRole]['label'] ?? Str::title(str_replace('_',' ',$activeRole));

    $dbModules = \App\Models\SystemModule::orderBy('id')->get();

    // 5. Filter & Paginate Users Berdasarkan Role & Search
    $search        = request('search','');
    $perPagePerm   = (int) request('per_page', 10);
    
    // Gunakan $activeRole yang sudah didefinisikan di atas
    $filteredUsers = $users->filter(fn($u) => $u->roles->pluck('name')->contains($activeRole))
        ->when($search, fn($c) => $c->filter(fn($u) => str_contains(strtolower($u->name), strtolower($search)) || str_contains(strtolower($u->email), strtolower($search))))
        ->values();

    $page    = \Illuminate\Pagination\Paginator::resolveCurrentPage();
    $paged   = new \Illuminate\Pagination\LengthAwarePaginator(
        $filteredUsers->forPage($page, $perPagePerm),
        $filteredUsers->count(),
        $perPagePerm,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    // 6. Mapping Badge Warna untuk Label di Tabel User
    $roleStyleMap = [
        'superadmin'          => ['color'=>'#7C3AED','border'=>'#C4B5FD'],
        'dosen'               => ['color'=>'#059669','border'=>'#6EE7B7'],
        'mahasiswa'           => ['color'=>'#D97706','border'=>'#FCD34D'],
        'gpm'                 => ['color'=>'#0284C7','border'=>'#7DD3FC'],
        'alumni'              => ['color'=>'#7C3AED','border'=>'#C4B5FD'],
        'pengurus_himpunan'   => ['color'=>'#7C3AED','border'=>'#C4B5FD'],
        'admin_banksoal'      => ['color'=>'#92400E','border'=>'#FCD34D'],
        'admin_capstone'      => ['color'=>'#1D4ED8','border'=>'#93C5FD'],
        'admin_eoffice'       => ['color'=>'#047857','border'=>'#6EE7B7'],
        'admin_kemahasiswaan' => ['color'=>'#9D174D','border'=>'#F9A8D4'],
    ];
@endphp

<div class="perm-wrap">
<div class="perm-box">

    {{-- ── Toolbar ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;background:#fff;border-bottom:1px solid #D4D5D8;flex-shrink:0;">
        <a href="{{ route('superadmin.users.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:12px;font-weight:600;color:#475569;background:#fff;border:1px solid #D0D1D5;border-radius:6px;box-shadow:0 1px 2px rgba(0,0,0,.04);text-decoration:none;font-family:'Inter Tight',sans-serif;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
            Kembali
        </a>
        <button onclick="saveBulkPermissions()"
                style="padding:6px 18px;font-size:12px;font-weight:700;color:#fff;background:#1E293B;border:none;border-radius:6px;cursor:pointer;font-family:'Inter Tight',sans-serif;box-shadow:0 1px 2px rgba(0,0,0,.1);">
            Simpan
        </button>
    </div>

    {{-- ── Scrollable body ── --}}
    <div style="flex:1;overflow-y:auto;">

        {{-- ── Panel 1: Role & Permissions ── --}}
        <div style="display:flex;border-bottom:1px solid #D4D5D8;background:#fff;">

            {{-- Kiri --}}
            <div style="width:220px;flex-shrink:0;padding:20px;border-right:1px solid #D4D5D8;background:#fff;">
                <p style="font-size:13px;font-weight:900;color:#1E293B;margin:0 0 5px 0;font-family:'Inter Tight',sans-serif;">Role &amp; Permissions</p>
                <p style="font-size:11px;font-weight:500;color:#64748B;line-height:1.6;margin:0;font-family:'Inter Tight',sans-serif;">Manage roles and module permissions for each user.</p>
            </div>

            {{-- Kanan --}}
            <div style="flex:1;padding:20px 24px;">

                {{-- Dropdown Nama Role — Alpine, posisi fixed agar tidak terpotong --}}
                <p style="font-size:9px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px 0;font-family:'Inter Tight',sans-serif;">
                    Nama Role <span style="color:#EF4444">*</span>
                </p>
                <div style="max-width:240px;margin-bottom:16px;position:relative;" x-data="{ open: false }">
                    <button type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            style="width:100%; height:38px; display:flex; align-items:center; justify-content:space-between; padding:0 14px; background:#fff; border:1px solid #D0D1D5; border-radius:8px; font-size:13px; font-weight:600; color:#334155; cursor:pointer; font-family:'Inter Tight',sans-serif; box-shadow:0 1px 2px rgba(0,0,0,.04); outline:none; box-sizing:border-box;">
                        
                        <span>{{ $activeLabel }}</span>
                        
                        {{-- Ikon panah dikunci mati ukurannya agar tidak melar --}}
                        <svg width="14" height="14" 
                            style="min-width:14px; max-width:14px; min-height:14px; max-height:14px; color:#94A3B8; flex-shrink:0; flex-grow:0; transition:transform .15s;" 
                            :style="open ? 'transform:rotate(180deg)' : ''" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>

                    {{-- Dropdown list --}}
                    <div x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D0D1D5;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:999;padding:4px 0;display:none;max-height:240px;overflow-y:auto;">
                        @foreach($roleList as $slug => $data)
                            <a href="{{ url()->current() }}?role={{ $slug }}&per_page={{ $perPagePerm }}"
                            style="display:flex;align-items:center;gap:8px;padding:8px 14px;font-size:12px;font-weight:{{ $activeRole===$slug ? '700' : '600' }};color:{{ $activeRole===$slug ? '#0F172A' : '#475569' }};background:{{ $activeRole===$slug ? '#F8FAFC' : 'transparent' }};text-decoration:none;font-family:'Inter Tight',sans-serif;transition:background .1s;"
                            onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='{{ $activeRole===$slug ? '#F8FAFC' : 'transparent' }}'">
                                <span style="width:7px;height:7px;border-radius:50%;background:{{ $data['color'] }};flex-shrink:0;"></span>
                                {{ $data['label'] }}
                                @if($activeRole===$slug)
                                    <svg style="width:11px;height:11px;margin-left:auto;flex-shrink:0;" fill="none" stroke="{{ $data['color'] }}" viewBox="0 0 24 24" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Bagian Module Boxes di Panel 1 --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @foreach($dbModules as $mod)
                        @php
                            $mkey    = strtolower($mod->slug);
                            $pPrefix = $slugToPerm[$mkey] ?? $mkey; // Mapping prefix permission
                            $style   = $moduleStyles[$mkey] ?? $defaultStyle;
                            $perms   = \App\Models\Permission::where('name', 'like', $pPrefix.'.%')->get();
                        @endphp
                        <div class="module-box" style="border:1px solid #D4D5D8; border-radius:8px; overflow:hidden; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.04); opacity: {{ $mod->is_active ? '1' : '0.6' }};">
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:9px 13px; border-bottom:1px solid #E8E9EB; background:#FAFAFA;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:{{ $style['bg'] }}; border:1px solid {{ $style['border'] }};">
                                        <svg width="13" height="13" fill="none" stroke="{{ $style['color'] }}" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="{{ $style['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <span style="font-size:12px; font-weight:900; color:#1E293B;">{{ strtoupper($mod->name) }}</span>
                                </div>
                                <label style="display:flex; align-items:center; gap:5px; cursor:{{ $mod->is_active ? 'pointer' : 'not-allowed' }};">
                                    <input type="checkbox" class="module-select-all" data-module-target="bulk_{{ $pPrefix }}" {{ !$mod->is_active ? 'disabled' : '' }}
                                        style="width:14px; height:14px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:inherit; accent-color:var(--c-primary);">
                                    <span style="font-size:9px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em;">Pilih Semua</span>
                                </label>
                            </div>
                            <div style="padding:10px 13px; display:grid; grid-template-columns:1fr 1fr; gap:6px 8px;">
                                @foreach($perms as $perm)
                                    <label style="display:flex; align-items:center; gap:7px; cursor:{{ $mod->is_active ? 'pointer' : 'not-allowed' }};">
                                        <input type="checkbox" class="perm-checkbox" data-module-key="bulk_{{ $pPrefix }}" value="{{ $perm->name }}" {{ !$mod->is_active ? 'disabled' : '' }}
                                            style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:inherit; accent-color:var(--c-primary);">
                                        <span style="font-size:12px; font-weight:600; color:#64748B;">{{ $perm->display_name ?? Str::title(Str::after($perm->name,'.')) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Panel 2: List User ── --}}
        <div style="display:flex;">

            {{-- Kiri --}}
            <div style="width:220px;flex-shrink:0;padding:20px;border-right:1px solid #D4D5D8;background:#fff;">
                <p style="font-size:13px;font-weight:900;color:#1E293B;margin:0 0 5px 0;font-family:'Inter Tight',sans-serif;">List User</p>
                <p style="font-size:11px;font-weight:500;color:#64748B;line-height:1.6;margin:0;font-family:'Inter Tight',sans-serif;">
                    Daftar tabel user dengan role<br>
                    "<span style="font-weight:700;color:{{ $activeColor }}">{{ $activeLabel }}</span>".
                </p>
            </div>

            {{-- Kanan: table area --}}
            <div style="flex:1;padding:16px 24px;background:#fff;display:flex;flex-direction:column;gap:12px;">

                {{-- Toolbar: search + per_page + limit --}}
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">

                    {{-- Left: bulk count (hidden by default) --}}
                    <div id="permBulkBar" style="display:none;align-items:center;gap:8px;">
                        <span style="font-size:11px;font-weight:600;color:#334155;font-family:'Inter Tight',sans-serif;">
                            <span id="permSelectedCount" style="font-weight:700;color:#1E293B;">0</span> user dipilih
                        </span>
                        <button onclick="deselectAllPerm()" style="font-size:11px;font-weight:600;color:#94A3B8;background:none;border:none;cursor:pointer;font-family:'Inter Tight',sans-serif;">
                            Batal
                        </button>
                    </div>
                    <div></div>

                    {{-- Right: search + per_page --}}
                    <form action="{{ url()->current() }}" method="GET" style="display:flex;align-items:center;gap:6px;">
                        <input type="hidden" name="role" value="{{ $activeRole }}">

                        {{-- Per page --}}
                        <div style="position:relative;" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                    style="display:flex;align-items:center;gap:5px;height:30px;padding:0 10px;background:#fff;border:1px solid #D0D1D5;border-radius:6px;font-size:11px;font-weight:600;color:#334155;cursor:pointer;font-family:'Inter Tight',sans-serif;white-space:nowrap;">
                                {{ $perPagePerm }} baris
                                <svg style="width:10px;height:10px;color:#94A3B8;" :style="open?'transform:rotate(180deg)':''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <input type="hidden" name="per_page" id="perPageInput" value="{{ $perPagePerm }}">
                            <div x-show="open" x-cloak
                                 style="position:absolute;bottom:calc(100% + 4px);left:0;background:#fff;border:1px solid #D0D1D5;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.1);z-index:50;padding:3px 0;min-width:80px;display:none;">
                                @foreach([10,25,50,100] as $opt)
                                    <button type="button"
                                            @click="document.getElementById('perPageInput').value = {{ $opt }}; open = false; $el.closest('form').submit()"
                                            style="display:block;width:100%;text-align:left;padding:6px 12px;font-size:11px;font-weight:{{ $perPagePerm==$opt?'700':'500' }};color:{{ $perPagePerm==$opt?'#1E293B':'#475569' }};background:none;border:none;cursor:pointer;font-family:'Inter Tight',sans-serif;">
                                        {{ $opt }} baris
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Search --}}
                        <div style="position:relative;">
                            <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:12px;height:12px;color:#94A3B8;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                                   style="padding:5px 10px 5px 28px;font-size:11px;font-weight:500;border:1px solid #D0D1D5;border-radius:6px;background:#fff;color:#334155;outline:none;width:200px;font-family:'Inter Tight',sans-serif;height:30px;"
                                   onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#D0D1D5'">
                        </div>
                        <button type="submit"
                                style="height:30px;padding:0 14px;font-size:11px;font-weight:700;color:#fff;background:#1E293B;border:none;border-radius:6px;cursor:pointer;font-family:'Inter Tight',sans-serif;">
                            Cari
                        </button>
                    </form>
                </div>

                {{-- Table -- identical style to user management --}}
                <div style="border:1px solid #E5E7EB;border-radius:10px;overflow:hidden;">
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;min-width:680px;">
                            <thead>
                                <tr style="border-bottom:1px solid #E5E7EB;background:#FAFAFA;">
                                    <th style="padding:10px 14px;width:40px;text-align:left;">
                                        <input type="checkbox" id="permSelectAll" 
                                            style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                                    </th>
                                    <th style="padding:10px 10px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;width:40px;">No</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;min-width:180px;">User Name</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;min-width:120px;">Access Role</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Modul</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Permissions</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Status</th>
                                    <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paged as $i => $user)
                                    @php
                                        $no           = ($paged->currentPage()-1)*$paged->perPage()+$i+1;
                                        $isSuspended  = $user->isSuspended();
                                        $isMe         = $user->id === auth()->id();
                                        $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                                        $permc        = $user->directPermissions->count();
                                        $modc         = $isSuperadmin ? 'All' : $user->directPermissions->pluck('name')->map(fn($p)=>explode('.',$p)[0])->unique()->count();
                                        $firstRole    = $user->roles->first();
                                        $rs           = $roleStyleMap[strtolower($firstRole?->name ?? '')] ?? ['color'=>'#64748B','border'=>'#CBD5E1'];
                                        $nameParts    = explode(' ', $user->name);
                                        $initials     = strtoupper(substr($nameParts[0],0,1)).(count($nameParts)>1 ? strtoupper(substr(end($nameParts),0,1)) : '');
                                    @endphp
                                    <tr style="border-bottom:1px solid #F3F4F6;transition:background .1s;{{ $isSuspended ? 'background:#FFF9F9;' : '' }}"
                                        onmouseover="this.style.background='{{ $isSuspended ? '#FFF5F5' : '#FAFAFA' }}'"
                                        onmouseout="this.style.background='{{ $isSuspended ? '#FFF9F9' : 'transparent' }}'">

                                        {{-- Checkbox --}}
                                        <td style="padding:12px 14px;">
                                            @if(!$isMe)
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="perm-user-checkbox"
                                                    style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                                            @else
                                                {{-- Ikon Gembok untuk akun sendiri tetap dipertahankan --}}
                                                <div style="width:15px;height:15px;display:flex;align-items:center;justify-content:center;" title="Akun Anda">
                                                    <svg width="12" height="12" fill="#9CA3AF" viewBox="0 0 24 24"><path d="M12 3c-2.76 0-5 2.24-5 5v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-7c0-1.1-.9-2-2-2h-1V8c0-2.76-2.24-5-5-5zm3 8H9V8c0-1.66 1.34-3 3-3s3 1.34 3 3v3z"/></svg>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- No --}}
                                        <td style="padding:12px 10px;font-size:12px;color:#94A3B8;">{{ $no }}</td>

                                        {{-- User Name --}}
                                        <td style="padding:12px 14px;">
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <div style="width:32px;height:32px;border-radius:8px;border:1px solid #E2E8F0;background:#E2E8F0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#64748B;flex-shrink:0;overflow:hidden;">
                                                    @if($user->avatar_url)
                                                        <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                                                    @else
                                                        {{ $initials }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div style="display:flex;align-items:center;gap:6px;">
                                                        <a href="{{ route('superadmin.users.show', $user->id) }}"
                                                           style="font-size:13px;font-weight:700;color:#1E293B;text-decoration:none;font-family:'Inter Tight',sans-serif;"
                                                           onmouseover="this.style.color='#3B82F6'" onmouseout="this.style.color='#1E293B'">
                                                            {{ $user->name }}
                                                        </a>
                                                        @if($isMe)<span style="font-size:8px;font-weight:700;color:#0B266E;background:rgba(11,38,110,0.07);padding:1px 5px;border-radius:4px;text-transform:uppercase;">YOU</span>@endif
                                                    </div>
                                                    <p style="font-size:11px;color:#94A3B8;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Access Role --}}
                                        <td style="padding:12px 14px;">
                                            @if($firstRole)
                                                <span style="font-size:11px;font-weight:500;color:{{ $rs['color'] }};border:1px solid {{ $rs['border'] }};padding:3px 10px;border-radius:9999px;white-space:nowrap;">
                                                    {{ Str::title(str_replace('_',' ',$firstRole->name)) }}
                                                </span>
                                            @else
                                                <span style="font-size:10px;color:#94A3B8;font-style:italic;">No Role</span>
                                            @endif
                                        </td>

                                        {{-- Modul --}}
                                        <td style="padding:12px 14px;font-size:13px;color:#334155;">{{ $modc }} Modul</td>

                                        {{-- Permissions --}}
                                        <td style="padding:12px 14px;font-size:13px;color:#334155;">{{ $isSuperadmin ? 'All' : $permc }} Permissions</td>

                                        {{-- Status --}}
                                        <td style="padding:12px 14px;">
                                            @if($isSuspended)
                                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;color:#DC2626;border:1px solid #FCA5A5;padding:3px 10px;border-radius:9999px;white-space:nowrap;">
                                                    <span style="width:6px;height:6px;border-radius:50%;background:#DC2626;flex-shrink:0;"></span>Suspend
                                                </span>
                                            @else
                                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;color:#059669;border:1px solid #6EE7B7;padding:3px 10px;border-radius:9999px;white-space:nowrap;">
                                                    <span style="width:6px;height:6px;border-radius:50%;background:#22C55E;flex-shrink:0;"></span>Active
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Action — same dropdown as user management --}}
                                        <td style="padding:12px 14px;text-align:center;">
                                            <div style="position:relative;display:inline-block;" x-data="{ open: false }">
                                                <button type="button" @click="open = !open" @click.outside="open = false"
                                                        style="width:28px;height:28px;border-radius:6px;border:1px solid #E2E8F0;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#94A3B8;transition:all .15s;margin:0 auto;"
                                                        onmouseover="this.style.background='#F8FAFC';this.style.borderColor='#CBD5E1'"
                                                        onmouseout="this.style.background='#fff';this.style.borderColor='#E2E8F0'">
                                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                                </button>

                                                <div x-show="open"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     style="position:absolute;right:0;top:calc(100% + 5px);background:#fff;border:1px solid #E2E8F0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:160px;z-index:40;overflow:hidden;display:none;">
                                                    <div style="padding:5px;">

                                                        {{-- Detail --}}
                                                        <a href="{{ route('superadmin.users.show', $user->id) }}"
                                                           style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:6px;font-size:11px;font-weight:500;color:#475569;text-decoration:none;transition:background .12s;"
                                                           onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            Detail Info
                                                        </a>

                                                        {{-- Edit --}}
                                                        <button type="button"
                                                                onclick="openEditInfo({{ json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email]) }}); open = false"
                                                                style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#475569;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='none'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M11 4H4C2.89 4 2 4.9 2 6V20C2 21.1 2.9 22 4 22H18C19.1 22 20 21.1 20 20V13M18.5 2.5C19.33 2.5 20 3.17 20 4V4C20.83 4 21.5 4.67 21.5 5.5C21.5 6.33 20.83 7 20 7L11 16L7 17L8 13L17 4C17 3.17 17.67 2.5 18.5 2.5Z"/></svg>
                                                            Edit Info
                                                        </button>

                                                        {{-- Permission --}}
                                                        <a href="{{ route('superadmin.permissions') }}?role={{ $activeRole }}"
                                                           style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:6px;font-size:11px;font-weight:500;color:#475569;text-decoration:none;transition:background .12s;"
                                                           onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linejoin="round"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                                            Permission
                                                        </a>

                                                        @if(!$isMe)
                                                            <div style="height:1px;background:#F3F4F6;margin:4px 6px;"></div>

                                                            {{-- Force Logout --}}
                                                            <button type="button"
                                                                    onclick="openForceLogoutModal({ id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}' }); open = false"
                                                                    style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#D97706;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                    onmouseover="this.style.background='#FFFBEB'" onmouseout="this.style.background='none'">
                                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.93L7.87 4.06C6.39 3.66 5 5.21 5 7.27V16.73C5 18.79 6.39 20.34 7.87 19.94L11.07 19.06C12.19 18.76 13 17.42 13 15.86V15.27M11 12H19M19 12L16.5 9.5M19 12L16.5 14.5"/></svg>
                                                                Force Logout
                                                            </button>

                                                            {{-- Suspend / Unsuspend --}}
                                                            @if($isSuspended)
                                                                <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}" style="display:block;">
                                                                    @csrf
                                                                    <button type="submit"
                                                                            style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#059669;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                            onmouseover="this.style.background='#ECFDF5'" onmouseout="this.style.background='none'">
                                                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                        Unsuspend
                                                                    </button>
                                                                </form>
                                                            @elseif(!$isSuperadmin)
                                                                <button type="button"
                                                                        onclick="openSuspendModal({{ json_encode(['id'=>$user->id,'name'=>$user->name]) }}); open = false"
                                                                        style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#DC2626;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                        onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15.5 15.5L12 12M8.5 8.5L12 12M8.5 15.5L12 12M15.5 8.5L12 12M12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12C22 17.52 17.52 22 12 22Z"/></svg>
                                                                    Suspend
                                                                </button>
                                                            @endif

                                                            {{-- Delete --}}
                                                            <button type="button"
                                                                    onclick="openDeleteHybrid({{ json_encode(['id'=>$user->id,'name'=>$user->name]) }}); open = false"
                                                                    style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#DC2626;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                    onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6H5H21M8 6V4C8 3.45 8.45 3 9 3H15C15.55 3 16 3.45 16 4V6M19 6L18.12 19.13C18.05 20.18 17.18 21 16.13 21H7.87C6.82 21 5.95 20.18 5.88 19.13L5 6H19Z"/></svg>
                                                                Hapus User
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="padding:40px;text-align:center;">
                                            <p style="font-size:11px;font-weight:600;color:#94A3B8;text-transform:uppercase;letter-spacing:.06em;font-family:'Inter Tight',sans-serif;">Tidak ada user ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination — same style as user management --}}
                    @if($paged->total() > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-top:1px solid #F3F4F6;flex-wrap:wrap;gap:8px;">
                        <span style="font-size:11px;color:#64748B;font-family:'Inter Tight',sans-serif;">
                            Showing <strong style="color:#1E293B;">{{ $paged->firstItem() }}</strong>
                            to <strong style="color:#1E293B;">{{ $paged->lastItem() }}</strong>
                            of <strong style="color:#1E293B;">{{ $paged->total() }}</strong> results
                        </span>

                        @if($paged->lastPage() > 1)
                        <div style="display:flex;align-items:center;gap:3px;">
                            {{-- Prev --}}
                            @if($paged->currentPage() > 1)
                                <a href="{{ $paged->appends(request()->query())->previousPageUrl() }}"
                                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;color:#64748B;text-decoration:none;transition:all .15s;"
                                   onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                                </a>
                            @else
                                <span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #F3F4F6;border-radius:6px;background:#FAFAFA;color:#D1D5DB;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                                </span>
                            @endif

                            @php
                                $start = max(1, $paged->currentPage()-2);
                                $end   = min($paged->lastPage(), $paged->currentPage()+2);
                            @endphp
                            @if($start > 1)
                                <a href="{{ $paged->appends(request()->query())->url(1) }}" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;font-size:11px;font-weight:500;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">1</a>
                                @if($start > 2)<span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94A3B8;">…</span>@endif
                            @endif
                            @for($p=$start;$p<=$end;$p++)
                                <a href="{{ $paged->appends(request()->query())->url($p) }}"
                                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:11px;font-weight:{{ $p===$paged->currentPage()?'700':'500' }};text-decoration:none;transition:all .15s;{{ $p===$paged->currentPage() ? 'background:#1E293B;color:#fff;border:1px solid #1E293B;' : 'border:1px solid #E2E8F0;background:#fff;color:#64748B;' }}"
                                   @if($p!==$paged->currentPage()) onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'" @endif>
                                    {{ $p }}
                                </a>
                            @endfor
                            @if($end < $paged->lastPage())
                                @if($end < $paged->lastPage()-1)<span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94A3B8;">…</span>@endif
                                <a href="{{ $paged->appends(request()->query())->url($paged->lastPage()) }}" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;font-size:11px;font-weight:500;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">{{ $paged->lastPage() }}</a>
                            @endif

                            {{-- Next --}}
                            @if($paged->currentPage() < $paged->lastPage())
                                <a href="{{ $paged->appends(request()->query())->nextPageUrl() }}"
                                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;color:#64748B;text-decoration:none;transition:all .15s;"
                                   onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                                </a>
                            @else
                                <span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #F3F4F6;border-radius:6px;background:#FAFAFA;color:#D1D5DB;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </div>

    </div>{{-- end scrollable body --}}
</div>{{-- end perm-box --}}
</div>{{-- end perm-wrap --}}

{{-- Bulk save form --}}
<form id="bulk-save-form" method="POST" action="{{ route('superadmin.permissions.bulk-update') }}" style="display:none">
    @csrf
    <input type="hidden" name="role" value="{{ $activeRole }}">
    <div id="bulk-perm-inputs"></div>
    <div id="bulk-user-inputs"></div>
</form>

{{-- Reuse modals dari user management --}}
@include('superadmin.permission._modal_confirm')
@include('superadmin.users._modal_suspend')
@include('superadmin.users._modal_delete_hybrid')
@include('superadmin.users._modal_force_logout')
@include('superadmin.permission._scripts')

</x-sidebar>
</x-app-layout>