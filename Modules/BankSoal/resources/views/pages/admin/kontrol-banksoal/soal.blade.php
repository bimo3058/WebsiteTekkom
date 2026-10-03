<x-banksoal::layouts.admin :kontrol-banksoal="true">
    @section('breadcrumbs')
        <span>Kontrol Bank Soal</span><span class="mx-2 text-slate-300">/</span><span class="text-slate-800">Cetak Soal</span>
    @endsection
    <div class="dosen-page-wrap">
        <div class="dosen-page-box">
            @include('banksoal::pages.admin.kontrol-banksoal._header', ['title' => 'Cetak Soal Ujian', 'active' => 'soal', 'description' => 'Kelola permintaan cetak soal ujian offline dari dosen.'])
            <div class="dosen-page-body">
                @if(session('success'))<div class="control-alert" role="status">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="control-alert control-alert-error" role="alert">{{ session('error') }}</div>@endif
                <section class="bs-section control-section">
                    <div class="bs-section-heading">
                        <div><h2>Antrean cetak <span class="control-count">{{ number_format($antreanCetak->total()) }} permintaan</span></h2><p>Preview soal, cetak dokumen, lalu tandai permintaan yang selesai.</p></div>
                        <span class="control-badge">Ujian offline</span>
                    </div>
                    <form action="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" method="GET" class="bs-toolbar control-toolbar" id="filterForm" onsubmit="window.showLoader?.();">
                        <div class="bs-search">
                            <label for="searchSoal" class="control-label">Pencarian</label>
                            <div class="control-search-field">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7" /><path d="m16 16 5 5" /></svg>
                                <input id="searchSoal" type="text" name="searchSoal" value="{{ request('searchSoal') }}" placeholder="Cari agenda atau mata kuliah...">
                            </div>
                        </div>
                        <x-banksoal::ui.control-filter name="filterStatus" label="Status cetak" :value="request('filterStatus', '')"
                            :options="[['value' => '', 'label' => 'Semua status'], ['value' => 'pending', 'label' => 'Menunggu dicetak'], ['value' => 'diproses', 'label' => 'Sedang diproses'], ['value' => 'selesai', 'label' => 'Sudah dicetak']]" />
                        <button type="submit" class="dosen-management-btn dosen-management-btn-primary">Terapkan</button>
                        @if(request()->filled('searchSoal') || request()->filled('filterStatus'))
                            <a href="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" class="dosen-management-btn">Reset</a>
                        @endif
                    </form>
                    @if($antreanCetak->count())
                        <div class="control-table-scroll" tabindex="0" role="region" aria-label="Daftar permintaan cetak soal">
                            <table class="bs-table">
                                <thead><tr><th scope="col">Agenda ujian</th><th scope="col">Mata kuliah / Dosen</th><th scope="col">Diajukan pada</th><th scope="col">Status cetak</th><th scope="col">Aksi</th></tr></thead>
                                <tbody>
                                    @foreach($antreanCetak as $item)
                                        <tr>
                                            <td><div class="control-title">{{ $item->nama_ekstraksi }}</div><span class="control-meta">{{ $item->tahun_akademik }} · Semester {{ ucfirst($item->semester) }}</span></td>
                                            <td><div class="control-title">{{ $item->mataKuliah?->nama ?? 'Mata kuliah tidak tersedia' }}</div><span class="control-meta">{{ $item->dosen?->name ?? 'Dosen tidak tersedia' }}</span></td>
                                            <td class="control-date">{{ $item->created_at->format('d M Y') }}<span class="control-meta">{{ $item->created_at->format('H:i') }} WIB</span></td>
                                            <td>
                                                @switch($item->status_cetak)
                                                    @case('pending')<span class="control-badge control-badge-pending">Menunggu</span>@break
                                                    @case('diproses')<span class="control-badge control-badge-processing">Diproses</span>@break
                                                    @case('selesai')<span class="control-badge control-badge-success">Selesai</span>@break
                                                    @default<span class="control-badge">{{ ucfirst($item->status_cetak ?? 'Tidak diketahui') }}</span>
                                                @endswitch
                                            </td>
                                            <td><div class="control-actions">
                                                <a href="{{ route('banksoal.admin.kontrol-banksoal.soal.cetak', $item->id) }}" target="_blank" rel="noopener" class="dosen-management-btn" aria-label="Cetak {{ $item->nama_ekstraksi }} (tab baru)">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 9V3h12v6M6 18H3V9h18v9h-3M6 14h12v7H6zM17 12h1" /></svg>Cetak
                                                </a>
                                                @if($item->status_cetak !== 'selesai')
                                                    <form action="{{ route('banksoal.admin.kontrol-banksoal.soal.tandai-selesai', $item->id) }}" method="POST" onsubmit="if (!confirm('Tandai bahwa berkas fisik soal ini sudah tercetak dan siap dibagikan?')) return false; window.showLoader?.();">
                                                        @csrf
                                                        <button type="submit" class="dosen-management-btn dosen-management-btn-primary" aria-label="Tandai {{ $item->nama_ekstraksi }} selesai dicetak">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>Selesai
                                                        </button>
                                                    </form>
                                                @endif
                                            </div></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <footer class="bs-pagination">
                            <span>Menampilkan {{ $antreanCetak->firstItem() }}–{{ $antreanCetak->lastItem() }} dari {{ number_format($antreanCetak->total()) }} permintaan</span>
                            <nav class="pagination-list" aria-label="Halaman antrean cetak">
                                @if($antreanCetak->onFirstPage())<button class="pagination-btn" disabled aria-label="Halaman sebelumnya">‹</button>
                                @else<a class="pagination-btn" href="{{ $antreanCetak->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">‹</a>@endif
                                @php
                                    $visiblePages = collect([1, $antreanCetak->lastPage(), $antreanCetak->currentPage() - 1, $antreanCetak->currentPage(), $antreanCetak->currentPage() + 1])
                                        ->filter(fn ($page) => $page >= 1 && $page <= $antreanCetak->lastPage())->unique()->sort()->values();
                                @endphp
                                @foreach($visiblePages as $page)
                                    @if($loop->index && $page - $visiblePages[$loop->index - 1] > 1)<span class="pagination-ellipsis" aria-hidden="true">…</span>@endif
                                    @if($page === $antreanCetak->currentPage())<span class="pagination-btn active" aria-current="page">{{ $page }}</span>
                                    @else<a class="pagination-btn" href="{{ $antreanCetak->url($page) }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>@endif
                                @endforeach
                                @if($antreanCetak->hasMorePages())<a class="pagination-btn" href="{{ $antreanCetak->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">›</a>
                                @else<button class="pagination-btn" disabled aria-label="Halaman berikutnya">›</button>@endif
                            </nav>
                        </footer>
                    @else
                        <div class="control-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M6 9V3h12v6M6 18H3V9h18v9h-3M6 14h12v7H6z" /></svg>
                            @if(request()->filled('searchSoal') || request()->filled('filterStatus'))
                                <h3>Tidak ada permintaan yang cocok</h3><p>Coba kata kunci lain atau reset filter yang digunakan.</p>
                                <a href="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" class="dosen-management-btn">Reset filter</a>
                            @else
                                <h3>Belum ada antrean cetak</h3><p>Permintaan cetak soal dari dosen akan muncul di sini.</p>
                            @endif
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-banksoal::layouts.admin>
