<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen User (Admin Ruangan)">

    {{-- Header --}}
    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Manajemen User</h1>
            <p class="mp-page-sub">Kelola data pengguna terdaftar dari seluruh sistem dan atur hak aksesnya ke dalam
                lingkup ruang dan peminjaman.</p>
        </div>
        <div style="flex-shrink:0;">
            <a href="{{ route('eoffice.peminjaman.admin.user.create') }}" class="mp-btn primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah User Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div
            style="margin-bottom:20px;padding:12px 16px;background:#E7F6EC;color:#036B26;border-radius:12px;font-size:14px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Section title --}}
    <div class="sec-head flex-shrink-0">
        <span class="sec-bar"></span>
        <span class="sec-title">Pencarian User</span>
        <span class="sec-rule"></span>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2 flex-shrink-0">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
            class="mp-input flex-1">
        <button type="submit" class="mp-btn primary sm">Cari</button>
        <a href="{{ route('eoffice.peminjaman.admin.user.index') }}" class="mp-btn secondary sm">Reset</a>
    </form>

    {{-- Section title --}}
    <div class="sec-head flex-shrink-0">
        <span class="sec-bar"></span>
        <span class="sec-title">Data Pengguna Terdaftar</span>
        <span class="sec-rule"></span>
    </div>

    {{-- Table --}}
    <div class="mp-card flex-1 min-h-0">
        <div style="flex-shrink:0;">
            <div
                style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <div class="mp-th flex-1">Nama User</div>
                <div class="mp-th" style="width:220px;">Email</div>
                <div class="mp-th" style="width:180px;">Role System</div>
                <div class="mp-th" style="width:120px;">Status</div>
                <div class="mp-th" style="width:140px;text-align:right;">Aksi</div>
            </div>
        </div>

        <div class="overflow-y-auto flex-1">
            @forelse($users as $u)
                <div class="mp-tr" style="display:flex;align-items:center;padding:12px 20px;">
                    <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                        <div class="mp-av violet flex-shrink-0">
                            {{ strtoupper(substr($u->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">
                                {{ $u->name ?? '—' }}</div>
                            <div style="font-size:11px;color:#666D80;margin-top:2px;">Bergabung:
                                {{ $u->created_at?->format('d M Y') ?? '—' }}</div>
                        </div>
                    </div>
                    <div style="width:220px;font-size:12px;color:#666D80;" class="truncate">{{ $u->email ?? '—' }}</div>
                    <div style="width:180px;font-size:12px;color:#353849;">
                        @if($u->roles->count() > 0)
                            @foreach($u->roles as $role)
                                <span class="mp-badge secondary sm"
                                    style="margin-right:2px;margin-bottom:2px;">{{ $role->name }}</span>
                            @endforeach
                        @else
                            <span style="color:#9BA3AF;">Tidak ada role</span>
                        @endif
                    </div>
                    <div style="width:120px;">
                        @if($u->isSuspended())
                            <span class="mp-badge danger sm">Non-aktif</span>
                        @else
                            <span class="mp-badge success sm">Aktif</span>
                        @endif
                    </div>
                    <div style="width:140px;text-align:right;display:flex;justify-content:flex-end;gap:8px;">
                        <a href="{{ route('eoffice.peminjaman.admin.user.edit', $u->id) }}"
                            class="mp-btn secondary sm">Edit</a>
                        <form action="{{ route('eoffice.peminjaman.admin.user.suspend', $u->id) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin {{ $u->isSuspended() ? 'mengaktifkan' : 'menonaktifkan' }} user ini?');"
                            style="margin:0;">
                            @csrf
                            <button type="submit" class="mp-btn sm {{ $u->isSuspended() ? 'primary' : 'danger' }}">
                                {{ $u->isSuspended() ? 'Aktifkan' : 'Suspend' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="padding:64px 20px;text-align:center;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5"
                        stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <div style="font-size:13px;color:#666D80;">Belum ada user yang cocok dengan pencarian.</div>
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div style="padding:12px 20px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $users->links() }}</div>
        @endif
    </div>

</x-eoffice::manajemen-ruangan.layout>