<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @push('styles')
        <link href="{{ asset('modules/banksoal/css/dosen-dashboard.css') }}" rel="stylesheet">
    @endpush

    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Dashboard</span>
    @endsection

    <x-banksoal::ui.bank-soal-page class="bs-dashboard-page">
        <x-slot:header>
            <div class="bs-heading-row">
                <div>
                    <div class="bs-heading-label">
                        <h1>Dashboard</h1>
                        <span class="bs-role-badge">Dosen</span>
                    </div>
                    <p>Ringkasan performa bank soal, RPS, dan distribusi soal aktif.</p>
                </div>
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="dosen-management-btn dosen-management-btn-primary">
                    <i class="fas fa-layer-group" aria-hidden="true"></i> Kelola Bank Soal
                </a>
            </div>
        </x-slot:header>

        @if(count($mkTanpaRps) > 0)
            <div class="bs-dashboard-alert" role="status">
                <span class="bs-dashboard-alert-icon"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></span>
                <div class="bs-dashboard-alert-copy" x-data="{
                    items: {{ json_encode($mkTanpaRps) }},
                    limit: 5,
                    get visibleItems() { return this.items.slice(0, this.limit); },
                    get remaining() { return this.items.length - this.limit; }
                }">
                    <strong>Lengkapi RPS mata kuliah</strong>
                    <p>
                        Anda belum mengunggah RPS untuk
                        <template x-for="(mk, index) in visibleItems" :key="index">
                            <span><span x-text="mk"></span><span x-show="index < visibleItems.length - 1 || remaining > 0">, </span></span>
                        </template>
                        <button type="button" x-show="remaining > 0" @click="limit += 5" x-text="'+' + remaining" aria-label="Tampilkan mata kuliah lainnya"></button><span x-show="remaining <= 0">.</span>
                        Unggah RPS untuk mulai mengelola soal mata kuliah tersebut.
                    </p>
                </div>
                <a href="{{ route('banksoal.rps.dosen.index') }}" class="dosen-management-btn">Upload RPS <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        @endif

        @php
            $dashboardStats = [
                ['label' => 'Total Soal', 'value' => $totalSoal, 'icon' => 'fa-layer-group', 'tone' => 'primary'],
                ['label' => 'Disetujui', 'value' => $approved, 'icon' => 'fa-circle-check', 'tone' => 'success'],
                ['label' => 'Dalam Pengajuan', 'value' => $perluReview, 'icon' => 'fa-clock-rotate-left', 'tone' => 'primary'],
                ['label' => 'Revisi / Ditolak', 'value' => $revisi + $ditolak, 'icon' => 'fa-circle-xmark', 'tone' => 'danger'],
            ];
        @endphp
        <div class="bs-dashboard-stats">
            @foreach($dashboardStats as $stat)
                <div class="bs-dashboard-stat">
                    <div class="bs-dashboard-stat-label">
                        <span class="bs-dashboard-stat-icon bs-dashboard-tone-{{ $stat['tone'] }}"><i class="fas {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                        <p>{{ $stat['label'] }}</p>
                    </div>
                    <strong>{{ number_format($stat['value']) }}</strong>
                </div>
            @endforeach
        </div>

        <div class="bs-dashboard-overview">
            <x-banksoal::ui.panel class="bs-dashboard-panel" title="Status Soal" subtitle="Komposisi status soal saat ini" padding="bs-dashboard-panel-body">
                <div class="bs-dashboard-analytics">
                    <div class="bs-dashboard-donut">
                        <svg width="112" height="112" viewBox="0 0 80 80" id="donutChart" role="img" aria-label="Komposisi status soal; rincian tersedia pada legenda"></svg>
                        <div class="bs-dashboard-donut-total">
                            <strong>{{ number_format($totalSoal) }}</strong>
                            <span>Total soal</span>
                        </div>
                    </div>
                    <dl class="bs-dashboard-legend">
                        <div><dt><span class="bs-dashboard-dot bs-dashboard-dot-approved"></span>Disetujui</dt><dd>{{ number_format($approved) }}</dd></div>
                        <div><dt><span class="bs-dashboard-dot bs-dashboard-dot-submitted"></span>Diajukan</dt><dd>{{ number_format($perluReview) }}</dd></div>
                        <div><dt><span class="bs-dashboard-dot bs-dashboard-dot-revision"></span>Revisi</dt><dd>{{ number_format($revisi) }}</dd></div>
                        <div><dt><span class="bs-dashboard-dot bs-dashboard-dot-rejected"></span>Ditolak</dt><dd>{{ number_format($ditolak) }}</dd></div>
                    </dl>
                </div>
            </x-banksoal::ui.panel>

            <x-banksoal::ui.panel class="bs-dashboard-panel" title="Periode Akademik" subtitle="Informasi semester dan mata kuliah aktif" padding="bs-dashboard-panel-body">
                <div class="bs-dashboard-period">
                    <div><span class="bs-dashboard-caption">Periode</span><strong>Semester Berjalan</strong></div>
                    @if(count($mkTanpaRps) > 0)
                        <span class="bs-dashboard-badge bs-dashboard-tone-warning"><i class="fas fa-clock" aria-hidden="true"></i>RPS belum lengkap</span>
                    @else
                        <span class="bs-dashboard-badge bs-dashboard-tone-success"><i class="fas fa-check-circle" aria-hidden="true"></i>RPS terunggah</span>
                    @endif
                </div>
                <div class="bs-dashboard-courses">
                    <span class="bs-dashboard-caption">Mata kuliah aktif</span>
                    <div class="bs-dashboard-course-list" x-data="{
                        mks: {{ json_encode($mataKuliah->map(fn($mk) => $mk->kode)) }},
                        limit: 5,
                        get visible() { return this.mks.slice(0, this.limit); },
                        get rem() { return this.mks.length - this.limit; }
                    }">
                        <template x-for="kode in visible" :key="kode">
                            <span class="bs-dashboard-course" x-text="kode"></span>
                        </template>
                        <button type="button" x-show="rem > 0" @click="limit += 5" class="bs-dashboard-course bs-dashboard-course-more" x-text="'+' + rem" aria-label="Tampilkan mata kuliah lainnya"></button>
                        @if($mataKuliah->isEmpty())
                            <p class="bs-dashboard-muted">Belum ada mata kuliah</p>
                        @endif
                    </div>
                </div>
            </x-banksoal::ui.panel>

            <x-banksoal::ui.panel class="bs-dashboard-panel" title="Profil Dosen" subtitle="Ringkasan profil pengampu" padding="bs-dashboard-panel-body">
                <div class="bs-dashboard-profile">
                    <span class="bs-dashboard-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <p>{{ auth()->user()->lecturer?->employee_number ?? auth()->user()->email }}</p>
                        <p>{{ auth()->user()->lecturer?->department ?? 'Teknik Komputer' }}</p>
                    </div>
                </div>
                <a href="/profile" class="dosen-management-btn bs-dashboard-profile-link">Lihat Profil <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            </x-banksoal::ui.panel>
        </div>

        <div class="bs-dashboard-charts">
            <x-banksoal::ui.panel class="bs-dashboard-panel" title="Distribusi Soal per CPL" subtitle="Berdasarkan Capaian Pembelajaran Lulusan" padding="bs-dashboard-chart-body">
                <x-slot:actions>
                    <a href="{{ route('banksoal.soal.dosen.index') }}" class="bs-dashboard-detail">Detail <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                </x-slot:actions>
                <div class="bs-dashboard-chart-scroll" tabindex="0" role="region" aria-label="Grafik jumlah soal per CPL">
                    <div id="cplChart"></div>
                </div>
            </x-banksoal::ui.panel>

            <x-banksoal::ui.panel class="bs-dashboard-panel" title="Jumlah Soal per Mata Kuliah" subtitle="Distribusi seluruh bank soal dosen" padding="bs-dashboard-chart-body">
                <x-slot:actions>
                    <a href="{{ route('banksoal.soal.dosen.index') }}" class="bs-dashboard-detail">Detail <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                </x-slot:actions>
                <div class="bs-dashboard-chart-scroll" tabindex="0" role="region" aria-label="Grafik jumlah soal per mata kuliah">
                    <div id="mkChart"></div>
                </div>
            </x-banksoal::ui.panel>
        </div>
    </x-banksoal::ui.bank-soal-page>

    <script src="{{ asset('modules/banksoal/js/Banksoal/components/DosenDashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const baseDonutData = @json($donutData);
            const baseCplData   = @json($cplDist);
            const baseMkData    = @json($mkDist);

            setTimeout(() => {
                if (typeof DosenDashboard !== 'undefined') {
                    DosenDashboard.updateDonutChart('donutChart', baseDonutData);
                    DosenDashboard.updateCplBarChart('cplChart', baseCplData);
                    DosenDashboard.updateMkBarChart('mkChart', baseMkData);
                }
            }, 100);
        });
    </script>
    @include('banksoal::partials.dosen.layout-scripts')
</x-banksoal::layouts.dosen-admin>
