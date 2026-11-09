<x-banksoal::layouts.gpm-master>
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
                        <span class="bs-role-badge">GPM</span>
                    </div>
                    <p>Ringkasan aktivitas penjaminan mutu akademik.</p>
                </div>
                <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="dosen-management-btn dosen-management-btn-primary">
                    <i class="fas fa-check-double" aria-hidden="true"></i> Validasi RPS
                </a>
            </div>
        </x-slot:header>

        @if(($statRpsMenunggu ?? 0) > 0 || ($statBankSoalMenunggu ?? 0) > 0)
            <div class="bs-dashboard-alert" role="status">
                <span class="bs-dashboard-alert-icon"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></span>
                <div class="bs-dashboard-alert-copy">
                    <strong>Antrean Peninjauan Membutuhkan Tindakan</strong>
                    <p>
                        Terdapat <strong>{{ $statRpsMenunggu ?? 0 }} RPS</strong> dan <strong>{{ $statBankSoalMenunggu ?? 0 }} Paket Soal</strong> yang menunggu review Anda.
                    </p>
                </div>
                <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="dosen-management-btn">Tinjau RPS <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        @endif

        @php
            $dashboardStats = [
                ['label' => 'RPS Menunggu Validasi', 'value' => $statRpsMenunggu ?? 0, 'icon' => 'fa-file-alt', 'tone' => 'primary'],
                ['label' => 'Bank Soal Menunggu', 'value' => $statBankSoalMenunggu ?? 0, 'icon' => 'fa-clock-rotate-left', 'tone' => 'warning'],
                ['label' => 'Selesai Direview Bulan Ini', 'value' => $tugasSelesai ?? 0, 'icon' => 'fa-circle-check', 'tone' => 'success'],
            ];
        @endphp
        <div class="bs-dashboard-stats" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
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

        <div class="mt-6">
            <x-banksoal::ui.panel class="bs-dashboard-panel" title="Tugas Prioritas" subtitle="Daftar dokumen yang membutuhkan validasi segera" padding="bs-dashboard-panel-body">
                <x-slot:actions>
                    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="bs-dashboard-detail">Lihat Semua <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                </x-slot:actions>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Tipe Dokumen</th>
                                <th class="px-4 py-3">Mata Kuliah</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($tugasPrioritas as $tugas)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        @if($tugas->tipe_dokumen == 'Bank Soal')
                                            <span class="bs-dashboard-badge bs-dashboard-tone-primary">Bank Soal</span>
                                        @elseif(($tugas->sub_status ?? '') == 'revisi')
                                            <span class="bs-dashboard-badge bs-dashboard-tone-warning">RPS - Revisi</span>
                                        @else
                                            <span class="bs-dashboard-badge bs-dashboard-tone-success">RPS - Diajukan</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-900">{{ $tugas->mk_nama }}</div>
                                        <div class="text-xs text-slate-500">{{ $tugas->mk_kode }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">Menunggu Review</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($tugas->tipe_dokumen == 'Bank Soal')
                                            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="dosen-management-btn dosen-management-btn-primary text-xs py-1.5 px-3">
                                                <i class="fas fa-comment-dots"></i> Review Sekarang
                                            </a>
                                        @else
                                            <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $tugas->rps_id) }}" class="dosen-management-btn dosen-management-btn-primary text-xs py-1.5 px-3">
                                                <i class="fas fa-comment-dots"></i> Review Sekarang
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                        <i class="fas fa-check-circle text-2xl text-slate-300 mb-2"></i>
                                        <p class="font-medium">Semua tugas selesai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-banksoal::ui.panel>
        </div>
    </x-banksoal::ui.bank-soal-page>
</x-banksoal::layouts.gpm-master>