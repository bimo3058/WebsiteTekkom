@if(isset($kpTimeline))
<section class="eo-timeline-panel" aria-labelledby="eo-kp-timeline-title" x-data="{ selectedStep: null }">
    <header class="eo-timeline-header">
        <div>
            <h2 id="eo-kp-timeline-title">Timeline Kerja Praktik</h2>
            <p>{{ $kpTimeline['period'] }}</p>
        </div>
        <div class="eo-timeline-actions">
            @if($dashboardRole === 'mahasiswa')<span class="eo-status-label">{{ $kpTimeline['status'] ?? 'Belum terdaftar' }}</span>@endif
            <a href="{{ route($kpTimeline['route']) }}" class="eo-service-link">Lihat KP <span aria-hidden="true">&rarr;</span></a>
        </div>
    </header>
    <ol class="eo-timeline-steps" aria-label="Jadwal tahapan KP">
        @foreach($kpTimeline['steps'] as $step)
            <li class="eo-timeline-step eo-step-{{ $step['state'] }}">
                <button type="button" class="eo-step-trigger"
                        @click="selectedStep = selectedStep === {{ $loop->index }} ? null : {{ $loop->index }}"
                        :aria-expanded="selectedStep === {{ $loop->index }}"
                        aria-controls="eo-kp-detail-{{ $loop->index }}"
                        title="{{ $step['title'] }} &middot; {{ $step['label'] }} &middot; {{ $step['start'] }} &middot; {{ $step['end'] }}">
                    <span class="eo-step-icon" :class="{ 'eo-step-selected': selectedStep === {{ $loop->index }} }" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            @switch($loop->index)
                                @case(0)
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h5"/>
                                    @break
                                @case(1)
                                    <path d="M9 5H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-4M9 2h6v5H9zM7 12h10M7 17h7"/>
                                    @break
                                @case(2)
                                    <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v3h4v-3"/>
                                    @break
                                @default
                                    <path d="m2 9 10-5 10 5-10 5-10-5zM6 11v6c4 3 8 3 12 0v-6M22 9v7"/>
                            @endswitch
                        </svg>
                    </span>
                    <span class="eo-step-title">{{ $step['title'] }}</span>
                    <span class="eo-step-status">{{ $step['state'] === 'active' ? 'Berlangsung' : $step['label'] }}</span>
                    @if($step['end'] !== 'Belum ditentukan')<span class="eo-step-date">s.d. {{ $step['end'] }}</span>@endif
                </button>
            </li>
        @endforeach
    </ol>
    <div class="eo-timeline-hint">
        <span>Pilih tahap untuk detail jadwal.</span>
        @foreach($kpTimeline['steps'] as $step)
            @if($step['deadline'])<span class="eo-step-deadline">{{ $step['title'] }} &middot; {{ $step['deadline'] }}</span>@endif
        @endforeach
    </div>
    @foreach($kpTimeline['steps'] as $step)
        <div id="eo-kp-detail-{{ $loop->index }}" class="eo-step-detail" x-show="selectedStep === {{ $loop->index }}" x-cloak>
            <div class="eo-step-detail-heading"><strong>{{ $step['title'] }}</strong><span class="eo-status-label">{{ $step['label'] }}</span></div>
            <dl class="eo-step-dates"><div><dt>Mulai</dt><dd>{{ $step['start'] }}</dd></div><div><dt>Batas akhir</dt><dd>{{ $step['end'] }}</dd></div></dl>
            <p>{{ $step['description'] }}</p>
            @if($step['state'] === 'ended')<p class="eo-detail-note">Jadwal telah berakhir; penyelesaian KP mengikuti validasi persyaratan.</p>@endif
        </div>
    @endforeach
    @if($dashboardRole === 'mahasiswa')
        <details class="eo-timeline-notices"><summary>Langkah berikutnya</summary><p>{{ $kpTimeline['next'] }}</p></details>
    @endif
    @if(($kpTimelineNotices ?? collect())->isNotEmpty())
        <details class="eo-timeline-notices"><summary>Pengumuman ({{ $kpTimelineNotices->count() }})</summary>
            @foreach($kpTimelineNotices as $notice)<article><h3>{{ $notice->judul }}</h3><p>{{ $notice->konten }}</p></article>@endforeach
        </details>
    @endif
</section>
@endif
