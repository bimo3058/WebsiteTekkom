{{-- resources/views/superadmin/users/edit.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <style>
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .edit-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 20px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
        .edit-box { display: flex; flex-direction: column; flex: 1; background: #F2F3F5; border: 1px solid #D4D5D8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
        
        .input-group label { display: block; font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
        .input-field { width: 100%; padding: 8px 12px; font-size: 13px; font-weight: 600; color: #334155; background: #fff; border: 1px solid #D0D1D5; border-radius: 8px; outline: none; transition: all 0.2s; font-family: inherit; }
        .input-field:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        
        [x-cloak] { display: none !important; }
    </style>

    @php
        // Mapping slug SystemModule (DB) → prefix nama permission (DB)
        // Slug di DB pakai underscore (bank_soal, manajemen_mahasiswa) sesuai SystemModuleSeeder
        $slugToPerm = [
            'bank_soal'           => 'banksoal',
            'manajemen_mahasiswa' => 'kemahasiswaan',
            'capstone'            => 'capstone',
            'eoffice'             => 'eoffice',
        ];

        $roleList = [
            'mahasiswa'           => ['label' => 'Mahasiswa',           'color' => '#D97706'],
            'dosen'               => ['label' => 'Dosen',               'color' => '#10B981'],
            'gpm'                 => ['label' => 'GPM',                 'color' => '#0EA5E9'],
            'pengurus_himpunan'   => ['label' => 'Pengurus Himpunan',   'color' => '#8B5CF6'],
            'alumni'              => ['label' => 'Alumni',              'color' => '#64748B'],
            'admin_banksoal'      => ['label' => 'Admin Bank Soal',     'color' => '#3B82F6'],
            'admin_capstone'      => ['label' => 'Admin Capstone',      'color' => '#6366F1'],
            'admin_eoffice'       => ['label' => 'Admin E-Office',      'color' => '#F43F5E'],
            'admin_kemahasiswaan' => ['label' => 'Admin Kemahasiswaan', 'color' => '#10B981'],
            'superadmin'          => ['label' => 'Superadmin',          'color' => '#0B266E'],
        ];

        // Style tiap kartu modul — key harus cocok dengan slug DB (underscore)
        $moduleStyles = [
            'bank_soal'           => ['bg'=>'#FDF6E9','border'=>'#EDD9A3','color'=>'#D97706','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            'capstone'            => ['bg'=>'#EFF6FF','border'=>'#BFDBFE','color'=>'#3B82F6','icon'=>'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7'],
            'manajemen_mahasiswa' => ['bg'=>'#ECFDF5','border'=>'#A7F3D0','color'=>'#10B981','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            'eoffice'             => ['bg'=>'#FFF1F2','border'=>'#FECDD3','color'=>'#F43F5E','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        $defaultStyle = ['bg'=>'#EDE9FE','border'=>'#C4B5FD','color'=>'#8B5CF6','icon'=>'M4 6h16M4 10h16M4 14h16M4 18h16'];

        $dbModules = \App\Models\SystemModule::orderBy('id')->get();

        /*
        |----------------------------------------------------------------------
        | FIX 2: Pisahkan data role untuk dua keperluan berbeda:
        |
        | (a) Alpine.js display → pakai NAME (string) agar cocok dengan
        |     $roleList yang juga memakai name sebagai key, dan agar label
        |     serta warna chip bisa di-lookup via window.roleStyles[name].
        |
        | (b) Hidden input form → pakai ID (integer) karena controller
        |     updateRole() memvalidasi: 'roles.*' => ['exists:roles,id']
        |     Kirim roles sebagai ID, bukan name.
        |
        | Solusi: Alpine state tetap menyimpan NAME, tapi saat render hidden
        | input kita lookup ID dari $allRolesById (map name → id).
        |----------------------------------------------------------------------
        */

        // Semua role dari DB, sudah di-eager-load di controller via:
        // $user->load(['roles', 'directPermissions'])
        // $roles = \App\Models\Role::orderBy('name')->get() → dikirim sebagai $roles

        // Role aktif user saat ini sebagai array of NAME (untuk Alpine.js initial state)
        $currentRoleNames = $user->roles->pluck('name')->toArray();
    @endphp

    <form action="{{ route('superadmin.users.update', $user) }}" method="POST" class="edit-wrap"
          x-data="{
              open: false,
              selected: {{ json_encode(old('roles', $currentRoleNames)) }},
              removeRole(role) { this.selected = this.selected.filter(r => r !== role); },
              addRole(role) { if (!this.selected.includes(role)) { this.selected.push(role); } this.open = false; }
          }">
        @csrf
        @method('PATCH')
        
        <div class="edit-box">
            {{-- ── Toolbar ── --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;background:#F2F3F5;border-bottom:1px solid #D4D5D8;flex-shrink:0;">
                <a href="{{ route('superadmin.users.show', $user) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:12px;font-weight:600;color:#475569;background:#fff;border:1px solid #D0D1D5;border-radius:6px;box-shadow:0 1px 2px rgba(0,0,0,.04);text-decoration:none;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                    Kembali
                </a>

                <button type="submit" style="display:inline-flex;align-items:center;padding:7px 20px;font-size:12px;font-weight:700;color:#fff;background:#1E293B;border:none;border-radius:6px;cursor:pointer;font-family:inherit;">
                    Simpan Perubahan
                </button>
            </div>

            {{-- ── Detail User Section ── --}}
            <div style="padding:24px;background:#fff;border-bottom:1px solid #D4D5D8;display:flex;gap:40px;">
                <div style="width:200px;flex-shrink:0;">
                    <h2 style="font-size:13px;font-weight:900;color:#1E293B;margin:0 0 6px 0;">Detail User</h2>
                    <p style="font-size:11px;font-weight:500;color:#64748B;line-height:1.6;margin:0;">Informasi dasar akun pengguna yang akan digunakan untuk keperluan sistem.</p>
                </div>

                <div style="flex:1; display:grid; grid-template-columns: 1fr 1fr; gap:20px 30px;">
                    <div class="input-group">
                        <label>Nama Lengkap <span style="color:#EF4444">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-field">
                    </div>
                    <div class="input-group">
                        <label>Email <span style="color:#EF4444">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-field">
                    </div>

                    <div class="input-group" x-show="selected.includes('dosen') && !selected.includes('mahasiswa')" x-cloak>
                        <label>NIP (Nomor Induk Pegawai) <span style="color:#EF4444">*</span></label>
                        <input type="text" name="employee_number"
                               value="{{ old('employee_number', $user->lecturer->employee_number ?? '') }}"
                               class="input-field" placeholder="Contoh: 198501012010011001">
                        @error('employee_number')
                            <span style="font-size:11px;color:#EF4444;font-weight:600;">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Kolom kanan kosong saat hanya dosen — supaya grid tetap rapi --}}
                    <div x-show="selected.includes('dosen') && !selected.includes('mahasiswa')" x-cloak></div>

                    {{-- ── Baris NIM + Tahun Angkatan (mahasiswa) ── --}}
                    <div class="input-group" x-show="selected.includes('mahasiswa')" x-cloak>
                        <label>NIM (Nomor Induk Mahasiswa) <span style="color:#EF4444">*</span></label>
                        <input type="text" name="student_number"
                               value="{{ old('student_number', $user->student->student_number ?? '') }}"
                               class="input-field" placeholder="Contoh: 22552011001">
                        @error('student_number')
                            <span style="font-size:11px;color:#EF4444;font-weight:600;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->whatsapp) }}" placeholder="08123456789" class="input-field">
                    </div>
                    <div class="input-group" x-show="selected.includes('mahasiswa')" x-cloak>
                        <label>Tahun Angkatan <span style="color:#EF4444">*</span></label>
                        <input type="number" name="cohort_year"
                               value="{{ old('cohort_year', $user->student->cohort_year ?? date('Y')) }}"
                               class="input-field"
                               placeholder="{{ date('Y') }}"
                               min="2000" max="{{ date('Y') + 1 }}">
                        @error('cohort_year')
                            <span style="font-size:11px;color:#EF4444;font-weight:600;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ── Dosen sekaligus Mahasiswa: tampilkan keduanya ── --}}
                    <div class="input-group" x-show="selected.includes('dosen') && selected.includes('mahasiswa')" x-cloak>
                        <label>NIP (Nomor Induk Pegawai) <span style="color:#EF4444">*</span></label>
                        <input type="text" name="employee_number"
                               value="{{ old('employee_number', $user->lecturer->employee_number ?? '') }}"
                               class="input-field" placeholder="Contoh: 198501012010011001">
                        @error('employee_number')
                            <span style="font-size:11px;color:#EF4444;font-weight:600;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── Role & Permissions Section ── --}}
            <div style="display:flex;flex:1;min-height:0;overflow:hidden;">
                <div style="width:200px;flex-shrink:0;padding:24px 20px;border-right:1px solid #D4D5D8;background:#fff;">
                    <h2 style="font-size:13px;font-weight:900;color:#1E293B;margin:0 0 6px 0;">Role & Permissions</h2>
                    <p style="font-size:11px;font-weight:500;color:#64748B;line-height:1.6;margin:0;">Atur peran akses dan izin spesifik modul untuk pengguna ini.</p>
                </div>

                <div style="flex:1;overflow-y:auto;padding:24px;background:#fff;">
                    
                    {{-- Access Role Multiple Dropdown --}}
                    <div style="margin-bottom:24px; max-width: 500px;">
                        <label style="display:block; font-size:9px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.08em; margin-bottom:8px;">Nama Role <span style="color:#EF4444">*</span></label>

                        {{--
                        |--------------------------------------------------------------
                        | FIX 5: Alpine state menyimpan NAME role (string).
                        |
                        | Alasan: $roleList & window.roleStyles keduanya di-key
                        | dengan name (misal: 'mahasiswa'), bukan ID. Menyimpan ID
                        | di Alpine akan membuat lookup label/color gagal.
                        |
                        | $currentRoleNames sudah berisi array of name yang benar
                        | dari relasi $user->roles (eager loaded di controller).
                        |--------------------------------------------------------------
                        --}}
                        <div style="position:relative;" @click.outside="open = false">
                            
                            {{-- Container Area (sebagai tombol utama) --}}
                            {{--
                                Layout: dua kolom flex — kiri (chip area) tumbuh mengisi sisa ruang,
                                kanan (ikon panah) flex-shrink:0 agar selalu di ujung dan tidak pernah
                                tertimpa chip. Chip area sendiri flex-wrap sehingga chip memanjang ke
                                bawah tanpa pernah melewati batas kanan.
                            --}}
                            <div @click="open = !open"
                                 style="min-height:38px; display:flex; align-items:center; gap:8px; padding:4px 10px 4px 8px; background:#fff; border:1px solid #D0D1D5; border-radius:8px; cursor:pointer; box-sizing:border-box;">

                                {{-- Kiri: chip area tumbuh --}}
                                <div style="flex:1; display:flex; align-items:center; flex-wrap:wrap; gap:5px; min-width:0;">

                                    {{-- Placeholder --}}
                                    <div x-show="selected.length === 0"
                                         style="font-size:13px; color:#94A3B8; padding-left:4px; white-space:nowrap;">
                                        Pilih satu atau lebih role...
                                    </div>

                                    {{-- Chip tiap role yang dipilih --}}
                                    <template x-for="role in selected" :key="role">
                                        <div style="display:inline-flex; align-items:center; gap:5px; padding:3px 7px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; font-size:12px; font-weight:600; color:#475569; white-space:nowrap;"
                                             @click.stop>
                                            {{-- Dot warna role --}}
                                            <div style="flex-shrink:0; width:6px; height:6px; border-radius:50%;"
                                                 :style="'background:' + (window.roleStyles[role]?.color || '#64748B')">
                                            </div>
                                            {{-- Label role --}}
                                            <span x-text="window.roleStyles[role]?.label || role"></span>
                                            {{-- Tombol hapus chip --}}
                                            <button type="button" @click="removeRole(role)"
                                                    style="flex-shrink:0; display:flex; align-items:center; justify-content:center; background:transparent; border:none; padding:0; margin:0; cursor:pointer; color:#94A3B8; line-height:1;">
                                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Kanan: ikon panah — flex-shrink:0 agar tidak pernah tergusur --}}
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     stroke-width="2.5" style="flex-shrink:0; color:#94A3B8; transition:transform .2s;"
                                     :style="open ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>

                            {{--
                            |--------------------------------------------------------------
                            | Hidden input mengirim NAME role (bukan ID).
                            |
                            | Controller updateUser() memvalidasi:
                            |   'roles.*' => ['exists:roles,name']
                            | lalu konversi name→id secara internal:
                            |   Role::whereIn('name', $roleNames)->pluck('id')
                            |
                            | Jadi Alpine cukup simpan dan kirim name langsung.
                            |--------------------------------------------------------------
                            --}}
                            <template x-for="role in selected" :key="'hidden_' + role">
                                <input type="hidden" name="roles[]" :value="role">
                            </template>
                            {{-- 
                                FIX: Gunakan x-if bukan x-show agar elemen benar-benar
                                tidak di-render ke DOM (dan tidak ikut terkirim ke server)
                                saat ada role yang dipilih. x-show hanya menyembunyikan
                                secara visual tapi elemen tetap ada di DOM dan ikut submit.
                                Nilai kosong "" akan menyebabkan validasi 'exists:roles,name'
                                gagal dan roles tidak tersimpan sama sekali.
                            --}}
                            <template x-if="selected.length === 0">
                                <input type="hidden" name="roles[]" value="">
                            </template>

                            {{-- Menu Dropdown List --}}
                            <div x-show="open" x-cloak
                                 style="position:absolute; top:calc(100% + 4px); left:0; right:0; z-index:50; background:#fff; border:1px solid #D4D5D8; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); padding:5px; max-height:220px; overflow-y:auto;">

                                {{--
                                    Penting: x-show dan display:flex TIDAK boleh ada di elemen yang sama.
                                    x-show bekerja dengan toggle display:none/block — kalau digabung dengan
                                    display:flex di style inline, saat x-show=true browser set display:block
                                    (bukan flex) sehingga dot dan teks turun ke baris masing-masing.
                                    Solusi: wrapper block pakai x-show, inner div pakai display:flex.
                                --}}
                                @foreach($roleList as $slug => $r)
                                <div x-show="!selected.includes('{{ $slug }}')">
                                    <div @click="addRole('{{ $slug }}')"
                                         style="display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:6px; cursor:pointer; transition:background .15s;"
                                         onmouseover="this.style.background='#F2F3F5'" onmouseout="this.style.background='transparent'">
                                        <div style="flex-shrink:0; width:8px; height:8px; border-radius:50%; background:{{ $r['color'] }};"></div>
                                        <span style="font-size:12px; font-weight:600; color:#475569;">{{ $r['label'] }}</span>
                                    </div>
                                </div>
                                @endforeach

                                <div x-show="selected.length === {{ count($roleList) }}"
                                     style="padding:12px; text-align:center; font-size:11px; font-weight:600; color:#94A3B8;">
                                    Semua role telah dipilih
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Grid Kartu Modul --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                        @foreach($dbModules as $mod)
                            @php
                                $mkey    = $mod->slug; // contoh: 'bank_soal', 'manajemen_mahasiswa'
                                $pPrefix = $slugToPerm[$mkey] ?? $mkey;
                                $style   = $moduleStyles[$mkey] ?? $defaultStyle;

                                /*
                                | FIX 8: Ambil permissions dari DB menggunakan prefix yang benar.
                                | Permission::where('name','like','banksoal.%')
                                */
                                $perms = \App\Models\Permission::where('name', 'like', $pPrefix . '.%')->get();
                            @endphp
                            <div class="module-box" style="border:1px solid #D4D5D8;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.04); opacity: {{ $mod->is_active ? '1' : '0.6' }};">
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 15px;border-bottom:1px solid #E8E9EB;background:#fff;">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;border:1px solid {{ $style['border'] }};background:{{ $style['bg'] }};">
                                            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="{{ $style['color'] }}" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $style['icon'] }}"/>
                                            </svg>
                                        </div>
                                        <span style="font-size:12px;font-weight:900;color:#1E293B;">{{ strtoupper($mod->name) }}</span>
                                    </div>
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                        <input type="checkbox" onclick="toggleAllPerms('{{ $pPrefix }}', this)" {{ !$mod->is_active ? 'disabled' : '' }}
                                            style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                                        <span style="font-size:9px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.06em;">Pilih Semua</span>
                                    </label>
                                </div>
                                <div style="padding:15px;display:grid;grid-template-columns:1fr 1fr;gap:10px 15px;">
                                    @foreach($perms as $perm)
                                        <label style="display:flex;align-items:center;gap:8px;cursor:{{ $mod->is_active ? 'pointer' : 'not-allowed' }};">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" 
                                                {{--
                                                | FIX 9: Cek apakah permission ini ada di directPermissions user.
                                                | $user->directPermissions sudah di-load di controller via
                                                | $user->load(['roles', 'directPermissions']).
                                                | Gunakan ->contains() pada collection yang sudah ada (tanpa query baru).
                                                --}}
                                                {{ $user->directPermissions->contains('name', $perm->name) ? 'checked' : '' }}
                                                {{ !$mod->is_active ? 'disabled' : '' }}
                                                style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:inherit; accent-color:var(--c-primary);">
                                            <span style="font-size:12px;font-weight:600;color:#64748B;">{{ $perm->display_name ?? \Illuminate\Support\Str::title(\Illuminate\Support\Str::after($perm->name, '.')) }}</span>
                                        </label>
                                    @endforeach

                                    @if($perms->isEmpty())
                                        <p style="font-size:11px;color:#94A3B8;grid-column:span 2;margin:0;">
                                            Tidak ada permission terdaftar untuk modul ini.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>

<script>
    // window.roleStyles: dipakai Alpine untuk lookup label & warna chip
    window.roleStyles = @json($roleList);

    function toggleAllPerms(moduleName, masterCheckbox) {
        const checkboxes = document.querySelectorAll(`input[name="permissions[]"][value^="${moduleName}."]`);
        checkboxes.forEach(cb => {
            if (!cb.disabled) {
                cb.checked = masterCheckbox.checked;
            }
        });
    }
</script>

</x-sidebar>
</x-app-layout>