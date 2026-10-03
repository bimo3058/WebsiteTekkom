@php
    // Nomor urut awal — halaman kategori meneruskan offset paginator.
    $startIndex = $startIndex ?? 0;
    $emptyText  = $emptyText ?? 'Tidak ada pengguna';
@endphp

<div class="mp-table-wrap">
    <table class="mp-table">
        <thead>
            <tr>
                <th class="mp-th mp-th-no">No</th>
                <th class="mp-th" style="min-width:220px;">Pengguna</th>
                <th class="mp-th" style="min-width:110px;">NIM</th>
                <th class="mp-th" style="min-width:90px;">Angkatan</th>
                <th class="mp-th" style="min-width:180px;">Role</th>
                <th class="mp-th mp-th-action">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                @include('manajemenmahasiswa::permissions._user_row', [
                    'user'            => $user,
                    'assignableRoles' => $assignableRoles,
                    'rowNo'           => $startIndex + $index + 1,
                ])
            @empty
                <tr>
                    <td colspan="6" class="mp-empty-cell">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <p class="mp-empty-title">{{ $emptyText }}</p>
                        <p class="mp-empty-sub">Coba ubah kata kunci atau filter pencarian</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
