<x-eoffice::manajemen-praktikum.layout pageTitle="Nilai Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 14px;display:block;">
            <rect x="3" y="3" width="18" height="18" rx="3"/>
            <path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Pilih Praktikum</div>
        <div style="font-size:13px;color:#666D80;margin-bottom:24px;">Pilih praktikum yang ingin dilihat nilainya.</div>

        @if($praktikums->isEmpty())
        <div style="font-size:13px;color:#808897;">Kamu belum mengampu praktikum apapun.</div>
        @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
               class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]" style="text-decoration:none;border:1px solid #DFE1E7;">
                <span style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $p->nama }}</span>
                <span style="font-size:11px;color:#666D80;">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

@else

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi & Nilai</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum->nama }} · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</p>
    </div>
    <div class="mp-page-actions" style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('eoffice.manprak.dosen.nilai.index', '0') }}" class="mp-btn secondary md" style="text-decoration:none;">Ganti Praktikum</a>
        @if($daftarPraktikan->isNotEmpty())
        <a href="{{ route('eoffice.manprak.dosen.nilai.export-csv', ['praktikumId' => $praktikum->id, 'modul_id' => $modulFilter]) }}" class="mp-btn neutral md" style="background:#fff;border:1px solid #DFE1E7;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download CSV
        </a>
        @endif
    </div>
</div>

{{-- Flash Error Notification --}}
@if(session('error'))
<div class="mp-flash mp-flash-error flex-shrink-0" style="margin-bottom: 24px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Flash Success Notification --}}
@if(session('success'))
<div class="mp-flash mp-flash-success flex-shrink-0" style="margin-bottom: 24px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Filter Modul --}}
<div class="mp-card flex-shrink-0" style="margin-bottom: 24px; padding: 14px 18px; display:flex; gap:10px; align-items:center;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;">
        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
    </svg>
    <form method="GET" action="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}" style="margin:0;">
        <select name="modul_id" onchange="this.form.submit()" class="mp-input mp-select" style="min-width:200px;">
            <option value="">Semua Modul</option>
            @foreach($allModuls ?? [] as $m)
            <option value="{{ $m->id }}" {{ ($modulFilter ?? '') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- Render per Modul --}}
@forelse($moduls as $modul)
<div class="mp-card flex-shrink-0" style="margin-bottom: 24px;">
    <div class="mp-card-header" style="background:#F9FAFB; border-bottom:1px solid #EDF0F4; border-radius: 12px 12px 0 0;">
        <span class="mp-card-title" style="font-size:15px; color:#0D0D12;">{{ $modul->nama }}</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="mp-table" style="min-width: 900px;">
            <thead>
                <tr style="background:#fff;">
                    <th class="mp-th text-left" style="padding:12px 16px; width:40px;">No</th>
                    <th class="mp-th text-left" style="padding:12px 16px; min-width:180px;">Nama Praktikan</th>
                    <th class="mp-th text-left" style="padding:12px 16px;">NIM</th>
                    <th class="mp-th text-center" style="padding:12px 16px;width:90px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Klp</th>
                    <th class="mp-th text-center" style="padding:12px 16px;width:70px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-center" style="padding:12px 16px; width:90px;">Kehadiran</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:120px; background:#EEF2FF;">Tugas Pendahuluan</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:100px; background:#F0FDF4;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:100px; background:#FEF9C3;">Laporan</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:100px; background:#FFF7ED;">Responsi</th>
                    <th class="mp-th text-left" style="padding:12px 16px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($daftarPraktikan as $idx => $dp)
                @php
                    $absensi     = $dp->absensi->firstWhere('modul_id', $modul->id);
                    $statusAbsen = $absensi?->status;
                    $njMap       = $nilaiJenisMap[$modul->id][$dp->id] ?? [];
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 16px; color:#666D80; font-size:13px;">{{ $idx + 1 }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="mp-av sky" style="width:28px;height:28px;font-size:11px;">{{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}</div>
                            <div style="font-weight:600;color:#0D0D12;font-size:13px;">{{ $dp->user?->name ?? '-' }}</div>
                        </div>
                    </td>
                    <td style="padding:12px 16px; color:#666D80; font-size:12px;font-family:monospace;font-weight:600;">
                        {{ $dp->user?->student?->student_number ?? $dp->user?->email ?? '-' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">
                        {{ $dp->kelompok ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-right:1px solid #DFE1E7;">
                        {{ $dp->shift ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($statusAbsen === 'hadir')
                            <span class="mp-badge success sm" style="background:#ECFDF5; color:#10B981;">Hadir</span>
                        @elseif($statusAbsen === 'izin')
                            <span class="mp-badge sky sm" style="background:#EFF6FF; color:#3B82F6;">Izin</span>
                        @elseif($statusAbsen === 'tidak_hadir')
                            <span class="mp-badge danger sm" style="background:#FEF2F2; color:#EF4444;">Alpha</span>
                        @else
                            <span style="color:#A4ABB8; font-size:12px;">—</span>
                        @endif
                    </td>
                    {{-- Tugas Pendahuluan --}}
                    <td style="padding:12px 16px;text-align:center;background:#EEF2FF;font-weight:700;color:#4338CA;font-size:13px;">
                        {{ isset($njMap['tugas_pendahuluan']) ? number_format($njMap['tugas_pendahuluan'], 1) : '—' }}
                    </td>
                    {{-- Praktikum --}}
                    <td style="padding:12px 16px;text-align:center;background:#F0FDF4;font-weight:700;color:#15803D;font-size:13px;">
                        {{ isset($njMap['praktikum']) ? number_format($njMap['praktikum'], 1) : '—' }}
                    </td>
                    {{-- Laporan --}}
                    <td style="padding:12px 16px;text-align:center;background:#FEF9C3;font-weight:700;color:#A16207;font-size:13px;">
                        {{ isset($njMap['laporan']) ? number_format($njMap['laporan'], 1) : '—' }}
                    </td>
                    {{-- Responsi --}}
                    <td style="padding:12px 16px;text-align:center;background:#FFF7ED;font-weight:700;color:#C2410C;font-size:13px;">
                        {{ isset($njMap['responsi']) ? number_format($njMap['responsi'], 1) : '—' }}
                    </td>
                    {{-- Keterangan --}}
                    <td style="padding:12px 16px;font-size:12px;color:#666D80;">
                        {{ $absensi?->keterangan ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="padding:32px; text-align:center; color:#666D80; font-size:13px;">Belum ada data praktikan.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="mp-card flex-shrink-0" style="padding:48px; text-align:center;">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang tersedia.</div>
</div>
@endforelse

{{-- Publikasi Nilai Section --}}
@php
    $totalPraktikan = $daftarPraktikan->count();
    $koorCount = 0;
    $dosenCount = 0;
    foreach($daftarPraktikan as $dp) {
        if ($dp->nilai?->disetujui_koor) $koorCount++;
        if ($dp->nilai?->disetujui_dosen) $dosenCount++;
    }
    $allKoorApproved = $totalPraktikan > 0 && $koorCount === $totalPraktikan;
    $allDosenApproved = $totalPraktikan > 0 && $dosenCount === $totalPraktikan;
@endphp

<div class="sec-head" style="margin-top: 32px;">
    <span class="sec-bar" style="background:#6366F1;"></span>
    <span class="sec-title">Publikasi Nilai</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="margin-bottom: 48px;">
    <div style="padding:20px; display:flex; flex-wrap:wrap; gap:20px; align-items:center; justify-content:space-between;">
        
        <div style="flex:1; min-width:300px;">
            <div style="display:flex; gap:24px; margin-bottom:12px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; color:#353849;">
                    <input type="checkbox" disabled {{ $allDosenApproved ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#10B981;">
                    Dosen
                </label>
                <label style="display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; color:#353849;">
                    <input type="checkbox" disabled {{ $allKoorApproved ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#10B981;">
                    Koordinator
                </label>
            </div>
            <p style="font-size:12px; color:#666D80; line-height:1.6; margin:0; max-width:600px;">
                <strong style="color:#0D0D12;">Informasi:</strong> Penyetujuan nilai ini dilakukan oleh dosen dan koor, sehingga apabila keduanya belum menyetujui maka kolom dosen dan koor belum terchecklist, jika salah satu sudah menyetujui berarti salah satunya ada tanda checklist, jika keduanya sudah checklist maka nilai tersebut akan dipublikasikan sehingga mahasiswa dapat melihat.
            </p>
        </div>

        @if(!$allDosenApproved)
        <div style="flex-shrink:0;">
            <form method="POST" action="{{ route('eoffice.manprak.dosen.nilai.approve', $praktikum->id) }}">
                @csrf
                <button type="submit" class="mp-btn primary md" style="background:#6366F1;box-shadow:0 2px 6px rgba(99,102,241,.3);" onmouseover="this.style.background='#4F46E5'" onmouseout="this.style.background='#6366F1'" onclick="return confirm('Publikasi semua nilai di praktikum ini?')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Publikasi (Setujui Nilai)
                </button>
            </form>
        </div>
        @else
        <div style="flex-shrink:0;">
            <span class="mp-badge success md" style="font-size:13px; padding:6px 12px;">
                <span class="dot"></span>Nilai Telah Dipublikasikan
            </span>
        </div>
        @endif

    </div>
</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
