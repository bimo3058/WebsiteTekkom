<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Modul — Manajemen Praktikum">

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Modul</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($terdaftarDi) · {{ $terdaftarDi->nama }} @endif
        </p>
    </div>
    <div class="mp-page-actions">
        <div style="text-align:right;">
            <div style="font-size:11px;color:#666D80;margin-bottom:2px;">Total Modul</div>
            <div style="font-size:22px;font-weight:700;color:#0D0D12;line-height:1;">{{ $modulList->count() }}</div>
        </div>
    </div>
</div>

{{-- Switcher praktikum jika ikut lebih dari 1 --}}
@if($daftarPraktikan->count() > 1)
<div style="display:flex;flex-wrap:wrap;gap:8px;" class="flex-shrink-0">
    @foreach($daftarPraktikan as $dp)
    <a href="{{ route('eoffice.manprak.mahasiswa.modul.index') }}?praktikum_id={{ $dp->praktikum_id }}"
       class="{{ $dp->praktikum_id === $terdaftarDi?->id ? 'mp-btn primary sm' : 'mp-btn secondary sm' }}"
       style="text-decoration:none;">
        {{ $dp->praktikum?->nama ?? 'Praktikum' }}
    </a>
    @endforeach
</div>
@endif

@if(!$terdaftarDi)

<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Anda belum terdaftar di kelas praktikum manapun.</div>
        <a href="{{ route('eoffice.manprak.mahasiswa.dashboard') }}"
           class="mp-btn ghost sm" style="text-decoration:none;display:inline-block;margin-top:12px;">← Kembali ke Dashboard</a>
    </div>
</div>

@elseif($modulList->isEmpty())

<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang tersedia untuk praktikum ini.</div>
    </div>
</div>

@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Materi Praktikum</span>
    <span class="sec-rule"></span>
</div>

{{-- Daftar Modul --}}
<div style="display:flex;flex-direction:column;gap:12px;" class="flex-1">
    @foreach($modulList as $modul)
    @php
        $asprakList = $modul->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values();
        $jumlahMateri = $modul->materi->count();
    @endphp
    <div class="mp-card flex-shrink-0">
        {{-- Modul header (toggle) --}}
        <button type="button"
                onclick="toggleModul({{ $modul->id }})"
                style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:16px 20px;text-align:left;cursor:pointer;border:none;background:transparent;">
            <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                <div class="mp-stat-icon navy" style="width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    {{ $modul->urutan ?? $loop->iteration }}
                </div>
                <div style="min-width:0;">
                    <div style="font-size:15px;font-weight:700;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $modul->nama }}</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
                        <span style="font-size:11px;color:#666D80;">{{ $jumlahMateri }} materi</span>
                        @if($asprakList->isNotEmpty())
                        <span style="font-size:11px;color:#666D80;">Asprak: {{ $asprakList->join(', ') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                @if($jumlahMateri > 0)
                <span class="mp-badge navy sm">{{ $jumlahMateri }} file</span>
                @endif
                <svg id="chevron-{{ $modul->id }}" width="16" height="16"
                     style="color:#666D80;transition:transform 0.2s;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        {{-- Konten modul --}}
        <div id="modul-content-{{ $modul->id }}" style="display:none;border-top:1px solid #DFE1E7;">
            @if($modul->deskripsi)
            <div style="padding:12px 20px;font-size:12px;color:#666D80;border-bottom:1px solid #DFE1E7;">{{ $modul->deskripsi }}</div>
            @endif

            @if($jumlahMateri === 0)
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada materi untuk modul ini.</div>
            </div>
            @else
            <table class="mp-table">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th>Materi</th>
                        <th>Deskripsi</th>
                        <th>Diunggah</th>
                        <th style="text-align:right;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modul->materi as $materi)
                    @php
                        $iconColor = match(true) {
                            str_contains($materi->tipe_file ?? '', 'pdf') => '#DF1C41',
                            str_contains($materi->tipe_file ?? '', 'word') || str_contains($materi->tipe_file ?? '', 'document') => '#1565C0',
                            str_contains($materi->tipe_file ?? '', 'presentation') || str_contains($materi->tipe_file ?? '', 'powerpoint') => '#E64A19',
                            str_contains($materi->tipe_file ?? '', 'image') => '#0D6B55',
                            default => '#666D80',
                        };
                    @endphp
                    <tr class="mp-tr">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $iconColor }}18;">
                                    <svg width="16" height="16" fill="none" stroke="{{ $iconColor }}" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $materi->judul }}</span>
                            </div>
                        </td>
                        <td>
                            @if($materi->deskripsi)
                            <span style="font-size:11px;color:#666D80;">{{ $materi->deskripsi }}</span>
                            @else
                            <span style="font-size:11px;color:#808897;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:11px;color:#666D80;">{{ $materi->created_at?->diffForHumans() }}</span>
                        </td>
                        <td style="text-align:right;">
                            @if($materi->file_path)
                            <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($materi->file_path, 'eoffice') }}"
                               target="_blank"
                               class="mp-btn primary sm" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                Lihat
                            </a>
                            @else
                            <span class="mp-badge neutral sm">Tidak ada file</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endif

<script>
function toggleModul(id) {
    const content  = document.getElementById('modul-content-' + id);
    const chevron  = document.getElementById('chevron-' + id);
    const isHidden = content.style.display === 'none' || content.style.display === '';
    content.style.display = isHidden ? 'block' : 'none';
    chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
}
</script>

</x-eoffice::manajemen-praktikum.layout>
