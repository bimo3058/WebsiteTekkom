<x-banksoal::layouts.admin :kontrol-banksoal="true">
    @section('breadcrumbs')
        <span>Kontrol Bank Soal</span><span class="mx-2 text-slate-300">/</span><span class="text-slate-800">Dokumen RPS</span>
    @endsection
    <div class="dosen-page-wrap">
        <div class="dosen-page-box">
            @include('banksoal::pages.admin.kontrol-banksoal._header', ['title' => 'Kontrol RPS', 'active' => 'rps', 'description' => 'Tinjau dan unduh dokumen RPS yang telah disetujui.'])
            <div class="dosen-page-body">
                <section class="bs-section control-section" id="approvedRps"
                    data-api="{{ route('banksoal.api.v1.admin.rps.approved.index') }}"
                    data-preview="{{ route('banksoal.admin.kontrol-banksoal.rps.preview', ['rpsId' => '__ID__']) }}"
                    data-download="{{ route('banksoal.admin.kontrol-banksoal.rps.download', ['rpsId' => '__ID__']) }}">
                    <div class="bs-section-heading">
                        <div><h2>Dokumen RPS <span id="rpsCount" class="control-count" aria-live="polite">—</span></h2><p>Dokumen yang telah melalui persetujuan GPM.</p></div>
                        <span class="control-badge control-badge-success">Disetujui</span>
                    </div>
                    <div class="bs-toolbar control-toolbar" x-data @banksoal-filter-change="window.ApprovedRps?.filter()">
                        <div class="bs-search">
                            <label for="searchInput" class="control-label">Pencarian</label>
                            <div class="control-search-field">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7" /><path d="m16 16 5 5" /></svg>
                                <input id="searchInput" type="text" placeholder="Cari mata kuliah atau nama file..." autocomplete="off">
                            </div>
                        </div>
                        <x-banksoal::ui.control-filter name="tahun_ajaran" input-id="tahunAjaranSelect" label="Tahun ajaran" :options="[['value' => '', 'label' => 'Semua tahun ajaran']]" />
                        <button id="resetRpsFilter" type="button" class="dosen-management-btn">Reset</button>
                    </div>
                    <div id="rpsLoading" class="control-empty" role="status">Memuat dokumen RPS…</div>
                    <div id="rpsError" class="control-empty" role="alert" hidden>
                        <h3>Dokumen belum dapat dimuat</h3><p>Periksa koneksi Anda, lalu coba lagi.</p>
                        <button type="button" id="retryRps" class="dosen-management-btn">Coba lagi</button>
                    </div>
                    <div id="rpsTableContainer" class="control-table-scroll" tabindex="0" role="region" aria-label="Daftar dokumen RPS" hidden>
                        <table class="bs-table">
                            <thead><tr><th scope="col">Mata kuliah</th><th scope="col">Dokumen</th><th scope="col">Disetujui pada</th><th scope="col">Aksi</th></tr></thead>
                            <tbody id="rpsTableBody"></tbody>
                        </table>
                    </div>
                    <div id="rpsEmptyState" class="control-empty" role="status" hidden>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M14 2H6v20h12V6zM14 2v5h5M9 12h6M9 16h4" /></svg>
                        <h3 id="rpsEmptyTitle">Belum ada dokumen RPS</h3><p id="rpsEmptyDescription">RPS yang telah disetujui akan muncul di sini.</p>
                    </div>
                    <footer id="rpsPagination" class="bs-pagination" hidden>
                        <span id="rpsSummary" aria-live="polite"></span>
                        <nav id="rpsPaginationList" class="pagination-list" aria-label="Halaman dokumen RPS"></nav>
                    </footer>
                </section>
            </div>
        </div>
    </div>
    @push('scripts')
        <script defer src="{{ asset('modules/banksoal/js/Banksoal/admin/approved-rps.js') }}?v={{ filemtime(public_path('modules/banksoal/js/Banksoal/admin/approved-rps.js')) }}"></script>
    @endpush
</x-banksoal::layouts.admin>
