<x-dynamic-component :component="$isAdmin ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="fw-bold mb-1">Layanan Pengaduan</h4>
            <p class="text-muted mb-0">Sampaikan keluhan secara terarah (opsional anonim) dan pantau jawabannya.</p>
        </div>

        <a href="{{ route('manajemenmahasiswa.pengaduan.create') }}" class="btn btn-primary">
            Buat Pengaduan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Judul</th>
                            <th style="width: 200px;">Kategori</th>
                            @if($isAdmin)
                                <th style="width: 220px;">Pelapor</th>
                            @endif
                            <th style="width: 140px;">Status</th>
                            <th style="width: 180px;">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengaduan as $item)
                            @php
                                $judul = data_get($item, 'data_template.judul') ?? '-';
                                $kategori = $item->kategori;
                                $status = $item->status;

                                $badge = match($status) {
                                    'dijawab' => 'success',
                                    'dibaca'  => 'info',
                                    default   => 'secondary',
                                };

                                $pelaporLabel = 'Anonim';
                                if (!$item->is_anonim) {
                                    $pelaporLabel = $isAdmin
                                        ? (optional($item->pelapor)->name ?? '—')
                                        : 'Anda';
                                }
                            @endphp

                            <tr>
                                <td class="text-muted">#{{ $item->id }}</td>
                                <td>
                                    <a class="text-decoration-none fw-semibold" href="{{ route('manajemenmahasiswa.pengaduan.show', $item->id) }}">
                                        {{ Str::limit($judul, 70) }}
                                    </a>
                                    @if($item->is_anonim)
                                        <div><small class="text-muted">Anonim</small></div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', ucfirst($kategori)) }}</span>
                                </td>
                                @if($isAdmin)
                                    <td>
                                        <span class="text-muted">{{ $pelaporLabel }}</span>
                                    </td>
                                @endif
                                <td>
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td class="text-muted">
                                    {{ optional($item->created_at)->translatedFormat('d F Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="text-center text-muted py-5">
                                    Belum ada pengaduan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengaduan->hasPages())
                <div class="mt-3 d-flex justify-content-center">
                    {{ $pengaduan->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

</x-dynamic-component>
