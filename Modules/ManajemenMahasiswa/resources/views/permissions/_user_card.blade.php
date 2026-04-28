@php
    $userRoleNames = $user->roles->pluck('name')->map(fn($r) => strtolower($r))->toArray();

    $himpunanPositions = ['ketua_himpunan','ketua_bidang','ketua_unit','staff_himpunan'];
    $isAlumni          = in_array('alumni', $userRoleNames);
    $isPengurus        = count(array_intersect($userRoleNames, $himpunanPositions)) > 0;
    $isMahasiswa       = in_array('mahasiswa', $userRoleNames) && !$isPengurus && !$isAlumni;
    $hasNoRole         = $user->roles->isEmpty();

    // Warna border kartu
    $cardBorder = match(true) {
        $hasNoRole  => 'border-left:4px solid #EF4444;',
        $isPengurus => 'border-left:4px solid #5E53F4;',
        $isAlumni   => 'border-left:4px solid #00C08D;',
        default     => 'border-left:1px solid #DEE2E6;',
    };

    // Avatar
    $avatarBg = match(true) {
        $isPengurus => 'background:#F1E9FF;color:#5E53F4;border:1px solid #D1BFFF;',
        $isAlumni   => 'background:#E7F9F3;color:#00C08D;border:1px solid #B2EBD9;',
        $hasNoRole  => 'background:#FEF2F2;color:#EF4444;border:1px solid #FEE2E2;',
        default     => 'background:#F8F9FA;color:#6C757D;border:1px solid #DEE2E6;',
    };

    // Nama warna untuk role badge
    $roleBadgeStyle = function(string $role): string {
        return match(true) {
            $role === 'ketua_himpunan'       => 'background:#F1E9FF;color:#5E53F4;border:1px solid #D1BFFF;',
            $role === 'ketua_bidang'         => 'background:#EBF1FF;color:#3377FF;border:1px solid #B3CCFF;',
            $role === 'ketua_unit'           => 'background:#EBF1FF;color:#3377FF;border:1px solid #B3CCFF;',
            $role === 'staff_himpunan'       => 'background:#F8F9FA;color:#495057;border:1px solid #DEE2E6;',
            $role === 'alumni'               => 'background:#E7F9F3;color:#00C08D;border:1px solid #B2EBD9;',
            $role === 'mahasiswa'            => 'background:#FFF9E6;color:#FFB800;border:1px solid #FFEBB3;',
            str_starts_with($role, 'admin') => 'background:#F0F5FF;color:#5E53F4;border:1px solid #D1DFFF;',
            default                          => 'background:#F8F9FA;color:#6C757D;border:1px solid #DEE2E6;',
        };
    };

    $roleLabel = function(string $role): string {
        return match($role) {
            'ketua_himpunan'       => 'Ketua Himpunan',
            'ketua_bidang'         => 'Ketua Bidang',
            'ketua_unit'           => 'Ketua Unit',
            'staff_himpunan'       => 'Staff Himpunan',
            'alumni'               => 'Alumni',
            'mahasiswa'            => 'Mahasiswa',
            'pengurus_himpunan'    => 'Pengurus',
            default                => ucwords(str_replace('_', ' ', $role)),
        };
    };

    // Role yang ditampilkan di badge (sembunyikan pengurus_himpunan karena umbrella)
    $displayRoles = $user->roles->filter(fn($r) => $r->name !== 'pengurus_himpunan');

    // NIM / angkatan
    $nim         = $user->student->student_number ?? null;
    $cohortYear  = $user->student->cohort_year ?? null;

    // Apakah actor bisa edit user ini
    $actor       = auth()->user();
    $canEdit     = !in_array('superadmin', $userRoleNames)
                && !in_array('admin', $userRoleNames)
                && !in_array('dosen', $userRoleNames)
                && $actor->id !== $user->id;
@endphp

<div class="user-card"
     data-user-id="{{ $user->id }}"
     data-name="{{ strtolower($user->name) }}"
     style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:2px;box-shadow:0 1px 3px rgba(0,0,0,.04);{{ $cardBorder }}">

    {{-- Header / Toggle --}}
    <button type="button" onclick="mkToggleCard({{ $user->id }})"
        style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:none;border:none;cursor:pointer;text-align:left;transition:background .15s;"
        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'">

        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
            {{-- Avatar --}}
            <div style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;{{ $avatarBg }}">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:13px;font-weight:700;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>

            <div style="flex:1;min-width:0;">
                {{-- Nama & Email --}}
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;flex-wrap:wrap;">
                    <span style="font-weight:700;font-size:13px;color:{{ $hasNoRole ? '#EF4444' : '#1A1C1E' }};letter-spacing:-.01em;">
                        {{ $user->name }}
                    </span>
                    <span style="font-size:11px;color:#ADB5BD;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        | {{ $user->email }}
                    </span>
                    @if($nim)
                        <span style="font-size:10px;color:#6C757D;background:#F8F9FA;border:1px solid #DEE2E6;border-radius:6px;padding:1px 7px;font-weight:600;">
                            {{ $nim }}
                        </span>
                    @endif
                    @if($cohortYear)
                        <span style="font-size:10px;color:#6C757D;background:#F8F9FA;border:1px solid #DEE2E6;border-radius:6px;padding:1px 7px;font-weight:600;">
                            Angkatan {{ $cohortYear }}
                        </span>
                    @endif
                </div>

                {{-- Role Badges --}}
                <div style="display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
                    @forelse($displayRoles as $role)
                        <span style="padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;{{ $roleBadgeStyle($role->name) }}">
                            {{ $roleLabel($role->name) }}
                        </span>
                    @empty
                        <span style="padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;background:#FEF2F2;color:#EF4444;border:1px solid #FEE2E2;font-style:italic;">
                            No Role
                        </span>
                    @endforelse
                </div>
            </div>
        </div>

        @if($canEdit)
            <span class="material-symbols-outlined card-chevron-{{ $user->id }}" style="font-size:18px;color:#DEE2E6;transition:transform .3s;flex-shrink:0;margin-left:8px;">
                expand_more
            </span>
        @endif
    </button>

    {{-- Body --}}
    @if($canEdit)
    <div id="card-body-{{ $user->id }}" style="display:none;border-top:1px solid #e5e7eb;background:#f9fafb;padding:20px 20px 16px;">

        <form method="POST"
              action="{{ route('manajemenmahasiswa.pengguna.update-role', $user->id) }}"
              id="role-form-{{ $user->id }}">
            @csrf

            {{-- Section Header --}}
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <span class="material-symbols-outlined" style="font-size:18px;color:#6C757D;">manage_accounts</span>
                <p style="font-size:10px;font-weight:700;color:#6C757D;text-transform:uppercase;letter-spacing:.15em;margin:0;">Ubah Role</p>
            </div>

            {{-- Hint multi-select --}}
            <p style="font-size:10px;color:#ADB5BD;margin:0 0 10px;font-style:italic;">
                Role <strong>Mahasiswa</strong> selalu dipertahankan saat memberi posisi himpunan.
                Pilih <strong>Mahasiswa Biasa</strong> untuk mencopot semua posisi.
                <strong>Alumni</strong> menggantikan semua role lain.
            </p>

            {{-- Role Options --}}
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">

                {{-- Mahasiswa Biasa — eksklusif --}}
                <label style="cursor:pointer;">
                    <input type="checkbox" name="roles[]" value="mahasiswa"
                        {{ $isMahasiswa ? 'checked' : '' }}
                        class="mk-role-check" data-exclusive="1" style="display:none;">
                    <div class="mk-role-pill {{ $isMahasiswa ? 'mk-role-active' : '' }}"
                        style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;border:1px solid {{ $isMahasiswa ? '#FFB800' : '#DEE2E6' }};background:{{ $isMahasiswa ? '#FFF9E6' : '#fff' }};color:{{ $isMahasiswa ? '#FFB800' : '#6C757D' }};transition:all .2s;"
                        data-active-bg="#FFF9E6" data-active-border="#FFB800" data-active-color="#FFB800">
                        <div style="width:8px;height:8px;border-radius:50%;background:{{ $isMahasiswa ? '#FFB800' : '#DEE2E6' }};transition:background .2s;" class="mk-dot"></div>
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Mahasiswa Biasa</span>
                    </div>
                </label>

                {{-- Assignable Position Roles — dapat dikombinasi --}}
                @foreach($assignableRoles as $role)
                    @if($role->name === 'alumni') @continue @endif
                    @php
                        $isCurrentRole = in_array($role->name, $userRoleNames);
                        [$bg, $border, $color] = match(true) {
                            in_array($role->name, ['ketua_himpunan'])
                                => ['#F1E9FF', '#D1BFFF', '#5E53F4'],
                            in_array($role->name, ['ketua_bidang','ketua_unit'])
                                => ['#EBF1FF', '#B3CCFF', '#3377FF'],
                            default => ['#F8F9FA', '#DEE2E6', '#495057'],
                        };
                    @endphp
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ $isCurrentRole ? 'checked' : '' }}
                            class="mk-role-check" data-exclusive="0" style="display:none;">
                        <div class="mk-role-pill {{ $isCurrentRole ? 'mk-role-active' : '' }}"
                            style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;border:1px solid {{ $isCurrentRole ? $border : '#DEE2E6' }};background:{{ $isCurrentRole ? $bg : '#fff' }};color:{{ $isCurrentRole ? $color : '#6C757D' }};transition:all .2s;"
                            data-active-bg="{{ $bg }}" data-active-border="{{ $border }}" data-active-color="{{ $color }}">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $isCurrentRole ? $color : '#DEE2E6' }};transition:background .2s;" class="mk-dot"></div>
                            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">
                                {{ $roleLabel($role->name) }}
                            </span>
                        </div>
                    </label>
                @endforeach

                {{-- Alumni — eksklusif, hanya jika admin --}}
                @if($assignableRoles->pluck('name')->contains('alumni'))
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="roles[]" value="alumni"
                            {{ $isAlumni ? 'checked' : '' }}
                            class="mk-role-check" data-exclusive="1" style="display:none;">
                        <div class="mk-role-pill {{ $isAlumni ? 'mk-role-active' : '' }}"
                            style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;border:1px solid {{ $isAlumni ? '#B2EBD9' : '#DEE2E6' }};background:{{ $isAlumni ? '#E7F9F3' : '#fff' }};color:{{ $isAlumni ? '#00C08D' : '#6C757D' }};transition:all .2s;"
                            data-active-bg="#E7F9F3" data-active-border="#B2EBD9" data-active-color="#00C08D">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $isAlumni ? '#00C08D' : '#DEE2E6' }};transition:background .2s;" class="mk-dot"></div>
                            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Alumni</span>
                        </div>
                    </label>
                @endif
            </div>

            {{-- Save Button --}}
            <div style="display:flex;justify-content:flex-end;padding-top:12px;border-top:1px solid #e5e7eb;">
                <button type="submit"
                    style="background:#4f46e5;color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;padding:10px 24px;border:none;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;"
                    onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
                    <span class="material-symbols-outlined" style="font-size:16px;">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
