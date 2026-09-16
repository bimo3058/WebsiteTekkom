@php
    $userRoleNames = $user->roles->pluck('name')->map(fn($r) => strtolower($r))->toArray();

    $himpunanPositions = ['ketua_himpunan','ketua_bidang','ketua_unit','staff_himpunan'];
    $isAlumni          = in_array('alumni', $userRoleNames);
    $isPengurus        = count(array_intersect($userRoleNames, $himpunanPositions)) > 0;
    $isMahasiswa       = in_array('mahasiswa', $userRoleNames) && !$isPengurus && !$isAlumni;
    $hasNoRole         = $user->roles->isEmpty();

    // Palet token SITKOM: [background, border, teks]
    $tone = [
        'primary' => ['#EEF1F8', '#5C78B8', '#0B266E'],
        'sky'     => ['#D1F0F9', '#7FC5DE', '#0C4D6E'],
        'success' => ['#DDF2EE', '#8FCFC2', '#287F6E'],
        'warning' => ['#F9ECCB', '#E0C078', '#956321'],
        'error'   => ['#FADAE1', '#F09FB2', '#DF1C41'],
        'neutral' => ['#F6F8FA', '#DFE1E7', '#666D80'],
    ];

    $avatarTone = match(true) {
        $isPengurus => $tone['primary'],
        $isAlumni   => $tone['success'],
        $hasNoRole  => $tone['error'],
        default     => $tone['neutral'],
    };

    $roleTone = function (string $role) use ($tone): array {
        return match(true) {
            $role === 'ketua_himpunan'                     => $tone['primary'],
            in_array($role, ['ketua_bidang','ketua_unit'])  => $tone['sky'],
            $role === 'staff_himpunan'                      => $tone['neutral'],
            $role === 'alumni'                              => $tone['success'],
            $role === 'mahasiswa'                           => $tone['warning'],
            str_starts_with($role, 'admin')                 => $tone['primary'],
            default                                         => $tone['neutral'],
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

    $nim        = $user->student->student_number ?? null;
    $cohortYear = $user->student->cohort_year ?? null;

    // Apakah actor bisa edit user ini
    $actor   = auth()->user();
    $canEdit = !in_array('superadmin', $userRoleNames)
            && !in_array('admin', $userRoleNames)
            && !in_array('dosen', $userRoleNames)
            && $actor->id !== $user->id;

    $rowNo = $rowNo ?? null;
@endphp

<tr class="mp-row" data-user-id="{{ $user->id }}">

    {{-- No --}}
    <td class="mp-td mp-td-no">{{ $rowNo }}</td>

    {{-- Pengguna --}}
    <td class="mp-td mp-td-user">
        <div class="mp-user-cell">
            <div class="mp-avatar"
                 style="background:{{ $avatarTone[0] }};border:1px solid {{ $avatarTone[1] }};color:{{ $avatarTone[2] }};">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="">
                @else
                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div style="min-width:0;">
                <p class="mp-user-name" @if($hasNoRole) style="color:#DF1C41;" @endif>{{ $user->name }}</p>
                <p class="mp-user-email">{{ $user->email }}</p>
            </div>
        </div>
    </td>

    {{-- NIM --}}
    <td class="mp-td mp-td-mono">{{ $nim ?? '—' }}</td>

    {{-- Angkatan --}}
    <td class="mp-td mp-td-mono">{{ $cohortYear ?? '—' }}</td>

    {{-- Role --}}
    <td class="mp-td">
        <div class="mp-badges">
            @forelse($displayRoles as $role)
                @php $t = $roleTone($role->name); @endphp
                <span class="mp-role-badge" style="background:{{ $t[0] }};color:{{ $t[2] }};">
                    {{ $roleLabel($role->name) }}
                </span>
            @empty
                <span class="mp-role-badge" style="background:#FADAE1;color:#DF1C41;">No Role</span>
            @endforelse
        </div>
    </td>

    {{-- Aksi --}}
    <td class="mp-td mp-td-action">
        @if($canEdit)
            <button type="button" class="mp-row-action" onclick="mkToggleCard({{ $user->id }})"
                    title="Ubah role pengguna ini">
                <svg class="card-chevron-{{ $user->id }}" width="13" height="13" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
                <span>Ubah Role</span>
            </button>
        @else
            <span class="mp-locked" title="Role pengguna ini tidak dapat diubah dari sini">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </span>
        @endif
    </td>
</tr>

{{-- Baris form: muncul saat tombol "Ubah Role" ditekan --}}
@if($canEdit)
<tr id="card-body-{{ $user->id }}" class="mp-edit-row" data-name="{{ strtolower($user->name) }}" style="display:none;">
    <td colspan="6" class="mp-edit-cell">

        <form method="POST"
              action="{{ route('manajemenmahasiswa.pengguna.update-role', $user->id) }}"
              id="role-form-{{ $user->id }}">
            @csrf

            <div class="uc-section-header">
                <span class="uc-section-label">Ubah Role — {{ $user->name }}</span>
            </div>

            <p class="uc-hint">
                Role <strong>Mahasiswa</strong> selalu dipertahankan saat memberi posisi himpunan.
                Pilih <strong>Mahasiswa Biasa</strong> untuk mencopot semua posisi.
                <strong>Alumni</strong> menggantikan semua role lain.
            </p>

            <div class="uc-role-options">

                {{-- Mahasiswa Biasa — eksklusif --}}
                @php $t = $tone['warning']; @endphp
                <label>
                    <input type="checkbox" name="roles[]" value="mahasiswa"
                        {{ $isMahasiswa ? 'checked' : '' }}
                        class="mk-role-check" data-exclusive="1" style="display:none;">
                    <div class="mk-role-pill {{ $isMahasiswa ? 'mk-role-active' : '' }}"
                        style="border-color:{{ $isMahasiswa ? $t[1] : '#DFE1E7' }};background:{{ $isMahasiswa ? $t[0] : '#fff' }};color:{{ $isMahasiswa ? $t[2] : '#666D80' }};"
                        data-active-bg="{{ $t[0] }}" data-active-border="{{ $t[1] }}" data-active-color="{{ $t[2] }}">
                        <div class="mk-dot" style="background:{{ $isMahasiswa ? $t[2] : '#C1C7CF' }};"></div>
                        <span>Mahasiswa Biasa</span>
                    </div>
                </label>

                {{-- Assignable Position Roles — dapat dikombinasi --}}
                @foreach($assignableRoles as $role)
                    @if($role->name === 'alumni') @continue @endif
                    @php
                        $isCurrentRole = in_array($role->name, $userRoleNames);
                        [$bg, $border, $color] = $roleTone($role->name);
                    @endphp
                    <label>
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ $isCurrentRole ? 'checked' : '' }}
                            class="mk-role-check" data-exclusive="0" style="display:none;">
                        <div class="mk-role-pill {{ $isCurrentRole ? 'mk-role-active' : '' }}"
                            style="border-color:{{ $isCurrentRole ? $border : '#DFE1E7' }};background:{{ $isCurrentRole ? $bg : '#fff' }};color:{{ $isCurrentRole ? $color : '#666D80' }};"
                            data-active-bg="{{ $bg }}" data-active-border="{{ $border }}" data-active-color="{{ $color }}">
                            <div class="mk-dot" style="background:{{ $isCurrentRole ? $color : '#C1C7CF' }};"></div>
                            <span>{{ $roleLabel($role->name) }}</span>
                        </div>
                    </label>
                @endforeach

                {{-- Alumni — eksklusif, hanya jika admin --}}
                @if($assignableRoles->pluck('name')->contains('alumni'))
                    @php $t = $tone['success']; @endphp
                    <label>
                        <input type="checkbox" name="roles[]" value="alumni"
                            {{ $isAlumni ? 'checked' : '' }}
                            class="mk-role-check" data-exclusive="1" style="display:none;">
                        <div class="mk-role-pill {{ $isAlumni ? 'mk-role-active' : '' }}"
                            style="border-color:{{ $isAlumni ? $t[1] : '#DFE1E7' }};background:{{ $isAlumni ? $t[0] : '#fff' }};color:{{ $isAlumni ? $t[2] : '#666D80' }};"
                            data-active-bg="{{ $t[0] }}" data-active-border="{{ $t[1] }}" data-active-color="{{ $t[2] }}">
                            <div class="mk-dot" style="background:{{ $isAlumni ? $t[2] : '#C1C7CF' }};"></div>
                            <span>Alumni</span>
                        </div>
                    </label>
                @endif
            </div>

            <div class="uc-actions">
                <button type="button" class="mp-btn-outline" onclick="mkToggleCard({{ $user->id }})">Batal</button>
                <button type="submit" class="mp-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </td>
</tr>
@endif
