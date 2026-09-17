<section aria-labelledby="eo-services-title">
    <div class="eo-section-heading">
        <div><h2 id="eo-services-title" class="eo-section-title">Ringkasan Layanan</h2><p>Status penting dari empat layanan E-Office, sesuai akses Anda.</p></div>
        <span class="eo-updated">Diperbarui saat halaman dimuat · {{ now()->format('H:i') }} WIB</span>
    </div>
    <div class="eo-overview-grid">
        @foreach($dashboardServices ?? [] as $serviceKey => $service)
            <article class="eo-overview-card" id="eo-service-{{ $serviceKey }}">
                <header class="eo-overview-heading">
                    <span class="eo-service-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ ['surat' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h5', 'ruangan' => 'M3 21h18M5 21V3h14v18M9 7h1M14 7h1M9 11h1M14 11h1M10 21v-6h4v6', 'kp' => $iKP, 'praktikum' => $iPraktikum][$serviceKey] }}"/>
                        </svg>
                    </span>
                    <div><h3>{{ $service['title'] }}</h3><p>{{ $service['scope'] }}</p></div>
                </header>
                <dl class="eo-service-metrics">
                    @foreach($service['metrics'] as $label => $value)
                        <div><dt>{{ $label }}</dt><dd>{{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}</dd></div>
                    @endforeach
                </dl>
                <div class="eo-service-records">
                    <h4>{{ $serviceKey === 'ruangan' ? 'Agenda dan status pengajuan' : 'Ringkasan terbaru' }}</h4>
                    @forelse($service['entries'] as $entry)
                        <div class="eo-service-record">
                            <div><strong>{{ $entry['title'] }}</strong><p>{{ $entry['detail'] }}</p></div>
                            <span class="eo-status-label">{{ $entry['status'] }}</span>
                        </div>
                    @empty
                        <p class="eo-empty-copy">{{ empty($service['metrics']) ? 'Data layanan belum tersedia.' : 'Belum ada data untuk ditampilkan.' }}</p>
                    @endforelse
                </div>
                <footer>
                    @if($service['note'])<p>{{ $service['note'] }}</p>@endif
                    @if($service['route'])
                        <a class="eo-service-link" href="{{ route($service['route']) }}">Buka {{ $service['title'] }} <span aria-hidden="true">→</span></a>
                    @else
                        <span class="eo-empty-copy">Halaman layanan belum tersedia</span>
                    @endif
                </footer>
            </article>
            @if($serviceKey === 'kp' && isset($kpTimeline))
                <div class="eo-priority-timeline">
                    @include('eoffice::dashboard._kp-timeline')
                </div>
            @endif
        @endforeach
    </div>
</section>
