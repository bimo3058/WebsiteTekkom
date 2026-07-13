<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Pengumpulan Tugas">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Detail Pengumpulan</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">{{ $tugas->judul }} · {{ $tugas->modul?->praktikum?->nama }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.dosen.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;">
            ← Kembali ke Daftar Tugas
        </a>
    </div>
</div>

{{-- Info Tugas --}}
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Informasi Tugas</span>
    </div>
    <div style="padding:16px 20px;display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        <div>
            <div style="font-size:11px;font-weight:600;color:#808897;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Judul</div>
            <div style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $tugas->judul }}</div>
        </div>
        <div>
            <div style="font-size:11px;font-weight:600;color:#808897;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Modul</div>
            <div style="font-size:13px;color:#353849;">{{ $tugas->modul?->nama ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:11px;font-weight:600;color:#808897;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Deadline</div>
            @php $dl = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null; @endphp
            <div style="font-size:13px;color:#353849;">
                {{ $dl ? $dl->locale('id')->isoFormat('D MMM YYYY, HH:mm') : 'Tanpa deadline' }}
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="mp-stats-grid cols-3 flex-shrink-0">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/></svg>
        </div>
        <div class="mp-stat-label">Total Pengumpul</div>
        <div class="mp-stat-value">{{ $pengumpulan->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="mp-stat-label">Sudah Dinilai</div>
        <div class="mp-stat-value">{{ $pengumpulan->whereNotNull('nilai')->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="mp-stat-label">Belum Dinilai</div>
        <div class="mp-stat-value">{{ $pengumpulan->whereNull('nilai')->count() }}</div>
    </div>
</div>

{{-- Daftar Pengumpulan --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pengumpulan</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $pengumpulan->count() }} mahasiswa</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">File</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Dikumpul</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengumpulan as $pk)
                @php
                    $mhs        = $pk->daftarPraktikan?->user;
                    $nameParts  = explode(' ', $mhs?->name ?? 'MH');
                    $initials   = strtoupper(substr($nameParts[0] ?? 'M', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'H', 0, 1));
                    $avColors   = ['sky','navy','green','yellow','violet'];
                    $avColor    = $avColors[crc32($mhs?->email ?? '') % count($avColors)];
                    $statusColor = match($pk->status ?? '') {
                        'acc'    => ['bg' => '#DDF2EE', 'c' => '#174E43', 'label' => 'ACC'],
                        'revisi' => ['bg' => '#FADAE1', 'c' => '#710E21', 'label' => 'Revisi'],
                        default  => ['bg' => '#F9ECCB', 'c' => '#5B3D1E', 'label' => 'Menunggu'],
                    };
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;">{{ $mhs?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $mhs?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($pk->file_path)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pk->file_path, 'eoffice') }}" target="_blank"
                           class="mp-btn secondary sm" style="text-decoration:none;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            Unduh
                        </a>
                        @else
                        <span style="font-size:12px;color:#A4ABB8;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:12px;color:#666D80;">
                        {{ $pk->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;
                                     font-size:11px;font-weight:600;background:{{ $statusColor['bg'] }};color:{{ $statusColor['c'] }};">
                            {{ $statusColor['label'] }}
                        </span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($pk->nilai !== null)
                        <span style="font-size:16px;font-weight:700;color:#0B266E;">{{ $pk->nilai }}</span>
                        @else
                        <span style="font-size:12px;color:#A4ABB8;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:48px;text-align:center;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                             stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div style="font-size:13px;color:#666D80;">Belum ada mahasiswa yang mengumpulkan tugas ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>