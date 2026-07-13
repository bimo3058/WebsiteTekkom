<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen Ruangan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Manajemen Ruangan</h1>
            <p class="mp-page-sub">Kelola data ruangan fisik yang tersedia untuk dipinjam.</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}" class="mp-btn primary md">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Ruangan
            </a>
        </div>
    </div>

    {{-- Pencarian --}}
    <div style="margin-top: 20px;">
        <form method="GET" action="{{ route('eoffice.peminjaman.admin.ruangan.index') }}"
            style="display:flex; gap:10px; max-width: 400px;">
            <input type="text" name="search" class="mp-input" placeholder="Cari nama ruangan atau lokasi..."
                value="{{ request('search') }}">
            <button type="submit" class="mp-btn secondary md">Cari</button>
        </form>
    </div>

    <div class="mp-card" style="margin-top: 16px;">
        <div class="mp-card-header">
            <h3 class="mp-card-title">Daftar Ruangan</h3>
        </div>
        <div class="mp-card-body">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>NAMA RUANG</th>
                            <th>LOKASI / GEDUNG</th>
                            <th>KAPASITAS</th>
                            <th>FASILITAS UTAMA</th>
                            <th>STATUS</th>
                            <th style="text-align:right;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ruangans as $r)
                            <tr class="mp-tr">
                                <td style="font-weight: 600;">{{ $r->nama }}</td>
                                <td>{{ $r->lokasi }} <br><span style="font-size:11px; color:#A4ABB8;">Lt.
                                        {{ $r->lantai ?? '-' }}</span></td>
                                <td>{{ $r->kapasitas }} Orang</td>
                                <td>
                                    @if(is_array($r->fasilitas) && count($r->fasilitas) > 0)
                                        <span
                                            style="font-size: 12px; color: #666D80;">{{ implode(', ', array_slice($r->fasilitas, 0, 3)) }}
                                            {{ count($r->fasilitas) > 3 ? '...' : '' }}</span>
                                    @else
                                        <span style="font-size: 12px; color: #A4ABB8;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r->is_active)
                                        <span class="mp-badge success sm">Aktif</span>
                                    @else
                                        <span class="mp-badge sm" style="background:#FADAE1; color:#710E21;">Non-aktif</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="{{ route('eoffice.peminjaman.admin.ruangan.edit', $r->id) }}"
                                            class="mp-btn secondary sm">Edit</a>
                                        <form method="POST"
                                            action="{{ route('eoffice.peminjaman.admin.ruangan.destroy', $r->id) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="mp-btn destructive sm" style="padding:4px 8px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 30px; color: #666D80;">
                                    Belum ada data ruangan.<br>
                                    <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}"
                                        style="color:#0B266E; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">+
                                        Tambah Data Pertama</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 16px;">
        {{ $ruangans->links() }}
    </div>

</x-eoffice::manajemen-ruangan.layout>