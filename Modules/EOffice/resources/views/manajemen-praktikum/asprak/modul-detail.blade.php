<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">{{ $modul->nama }}</h1>
            @if($isAssigned)
            <span class="mp-badge success sm"><span class="dot"></span>Diampu Anda</span>
            @endif
            @if($modul->kode_modul)
            <span class="mp-badge navy sm" style="font-family:monospace;letter-spacing:0.1em;">{{ $modul->kode_modul }}</span>
            @endif
        </div>
        <p class="mp-page-sub">
            {{ $modul->praktikum?->nama }} &middot; Urutan {{ $modul->urutan }} &middot; {{ $modul->jadwal_minggu ?? 'Jadwal belum diisi' }}
        </p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.modul.index') }}" class="mp-btn secondary md" style="text-decoration:none;">← Kembali</a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Materi</div>
            <div class="mp-stat-value">{{ $modul->materi->count() }}</div>
        </div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Tugas</div>
            <div class="mp-stat-value">{{ $modul->tugas->count() }}</div>
        </div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Asprak</div>
            <div class="mp-stat-value">{{ $modul->modulAsprak->count() }}</div>
        </div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <div class="mp-stat-label">Praktikan</div>
            <div class="mp-stat-value">{{ $daftarPraktikan->count() }}</div>
        </div>
    </div>
</div>

{{-- Deskripsi --}}
@if($modul->deskripsi)
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="font-weight:700;font-size:13px;color:#0D0D12;margin-bottom:8px;">Deskripsi Modul</div>
    <div style="font-size:13px;color:#353849;line-height:1.6;">{{ $modul->deskripsi }}</div>
</div>
@endif

<div style="display:grid;grid-template-columns:340px 1fr;gap:14px;flex:1;min-height:0;">

    {{-- Kolom Kiri: Edit + Asprak Pengampu --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Edit Modul --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div class="sec-head" style="margin-bottom:16px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Edit Modul</span>
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.asprak.modul.update', $modul->id) }}"
                  style="display:flex;flex-direction:column;gap:12px;">
                @csrf @method('PUT')
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Nama Modul</label>
                    <input name="nama" value="{{ $modul->nama }}" required class="mp-input" style="width:100%;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Urutan</label>
                        <input type="number" name="urutan" value="{{ $modul->urutan }}" min="1" required class="mp-input" style="width:100%;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Jadwal</label>
                        <input name="jadwal_minggu" value="{{ $modul->jadwal_minggu }}" class="mp-input" style="width:100%;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="mp-input" style="width:100%;">{{ $modul->deskripsi }}</textarea>
                </div>
                <button class="mp-btn primary md">Simpan Perubahan</button>
            </form>
        </div>

        {{-- Asprak Pengampu Modul ini --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div class="sec-head" style="margin-bottom:12px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Asprak Pengampu</span>
            </div>
            @forelse($modul->modulAsprak as $ma)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #DFE1E7;">
                <div class="mp-av green">{{ strtoupper(substr($ma->asprak?->user?->name ?? 'A', 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ma->asprak?->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:#666D80;">{{ $ma->asprak?->user?->email ?? '' }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:13px;color:#808897;">Belum ada asprak pengampu.</div>
            @endforelse
        </div>

    </div>

    {{-- Kolom Kanan: Materi, Tugas, Absensi Ringkasan --}}
    <div style="display:flex;flex-direction:column;gap:14px;overflow-y:auto;">

        {{-- Materi --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Materi Modul</span>
                <div class="right">
                    <a href="{{ route('eoffice.manprak.asprak.materi.index') }}"
                       class="mp-btn ghost sm" style="text-decoration:none;">Kelola Materi →</a>
                </div>
            </div>
            @forelse($modul->materi as $materi)
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                    <div class="mp-stat-icon navy" style="width:32px;height:32px;border-radius:8px;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-weight:600;font-size:13px;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $materi->judul }}</div>
                        <div style="font-size:11px;color:#666D80;">{{ $materi->created_at?->format('d M Y') }}</div>
                    </div>
                </div>
                @if($materi->file_path)
                <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="mp-btn primary sm flex-shrink-0" style="text-decoration:none;">Unduh</a>
                @endif
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada materi.</div>
            @endforelse
        </div>

        {{-- Tugas --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Tugas Modul</span>
                <div class="right">
                    <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
                       class="mp-btn ghost sm" style="text-decoration:none;">Kelola Tugas →</a>
                </div>
            </div>
            @forelse($modul->tugas as $tugas)
            @php
                $totalKumpul   = $tugas->pengumpulan->count();
                $totalDinilai  = $tugas->pengumpulan->where('status_pengumpulan', 'dinilai')->count();
                $totalPending  = $tugas->pengumpulan->where('status_pengumpulan', 'belum_dicek')->count();
            @endphp
            <div class="mp-tr" style="padding:12px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-weight:600;font-size:13px;color:#0D0D12;">{{ $tugas->judul }}</div>
                        <div style="font-size:11px;color:#666D80;margin-top:2px;">
                            Deadline: {{ $tugas->deadline?->format('d M Y H:i') ?? 'Tanpa deadline' }}
                        </div>
                    </div>
                    @if($tugas->is_published)
                    <span class="mp-badge success sm flex-shrink-0"><span class="dot"></span>Dipublikasikan</span>
                    @else
                    <span class="mp-badge neutral sm flex-shrink-0">Draft</span>
                    @endif
                </div>
                <div style="display:flex;gap:12px;margin-top:8px;font-size:11px;color:#666D80;">
                    <span>{{ $totalKumpul }} pengumpulan</span>
                    <span style="{{ $totalPending > 0 ? 'color:#D39C3D;font-weight:600;' : '' }}">{{ $totalPending }} belum dinilai</span>
                    <span>{{ $totalDinilai }} sudah dinilai</span>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada tugas.</div>
            @endforelse
        </div>

        {{-- Ringkasan Absensi --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Rekap Absensi</span>
                <div class="right">
                    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $modul->id) }}"
                       class="mp-btn ghost sm" style="text-decoration:none;">Kelola Absensi →</a>
                </div>
            </div>
            @php
                $absensiGroup = $modul->absensi->groupBy('tanggal');
            @endphp
            @forelse($absensiGroup as $tgl => $absList)
            @php
                $hadir = $absList->where('status','hadir')->count();
                $total = $absList->count();
                $pct   = $total > 0 ? round($hadir / $total * 100) : 0;
            @endphp
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="font-size:13px;font-weight:500;color:#0D0D12;">
                    {{ \Carbon\Carbon::parse($tgl)->isoFormat('dddd, D MMMM YYYY') }}
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:12px;color:#666D80;">{{ $hadir }}/{{ $total }} hadir</span>
                    <div style="width:60px;background:#F0F1F4;border-radius:999px;height:6px;">
                        <div style="height:6px;border-radius:999px;background:#0B266E;width:{{ $pct }}%"></div>
                    </div>
                    @if($pct >= 75)
                    <span class="mp-badge success sm">{{ $pct }}%</span>
                    @else
                    <span class="mp-badge error sm">{{ $pct }}%</span>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada data absensi.</div>
            @endforelse
        </div>

    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
