<x-manajemenmahasiswa::layouts.mahasiswa>

    <h4 class="fw-bold mb-1">Pengumuman & Informasi</h4>
    <p class="text-muted mb-4">Wadah Informasi untuk Mahasiswa dan Alumni</p>

    <!-- Search + Filter -->
    <div class="d-flex gap-3 mb-4 align-items-center">
        <form method="GET" action="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="d-flex gap-3 align-items-center">
            <input type="text" name="search" class="form-control" placeholder="Cari pengumuman..."
                   value="{{ request('search') }}" style="max-width: 500px;">

            <select name="kategori" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="semua" {{ request('kategori') == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                <option value="akademik" {{ request('kategori') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="himpunan" {{ request('kategori') == 'himpunan' ? 'selected' : '' }}>Himpunan</option>
                <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
            </select>

            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    <!-- List Pengumuman -->
    <div class="d-flex flex-column gap-3">
        @forelse($pengumuman as $item)
            <a href="{{ route('manajemenmahasiswa.pengumuman.show', $item->id) }}"
               class="card shadow-sm border-0 p-3 rounded-4 text-decoration-none" style="transition: transform 0.15s;">
                <h6 class="fw-bold text-dark">📢 {{ $item->judul }}</h6>
                <p class="text-muted mb-2">{{ Str::limit(strip_tags($item->konten), 120) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $item->created_at->translatedFormat('d F Y') }}</small>
                    @if($item->kategori)
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($item->kategori) }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="text-center text-muted py-5">
                <p class="fs-5">📭 Belum ada pengumuman</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($pengumuman->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $pengumuman->withQueryString()->links() }}
        </div>
    @endif

</x-manajemenmahasiswa::layouts.mahasiswa>
