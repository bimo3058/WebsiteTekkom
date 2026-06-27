<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Asisten Praktikum">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Asisten Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Lihat asisten praktikum per mata praktikum · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <form method="GET" class="flex gap-2 items-center">
            <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select" style="min-width:220px;">
                @foreach($praktikumList as $prak)
                <option value="{{ $prak->id }}" {{ ($prak->id == $praktikum?->id) ? 'selected' : '' }}>
                    {{ $prak->nama }}
                    @if($prak->kode) ({{ $prak->kode }}) @endif
                    · {{ $prak->semester }} {{ $prak->tahun_ajaran }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Info strip: praktikum terpilih --}}
@if($praktikum)
<div class="mp-alert info flex-shrink-0" style="display:flex;align-items:center;gap:12px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
        <path d="M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z"/>
    </svg>
    <div style="flex:1;min-width:0;">
        <span style="font-size:13px;font-weight:600;color:var(--c-fg,#0D0D12);">{{ $praktikum->nama }}</span>
        @if($praktikum->kode)
        <span style="font-size:11px;font-family:monospace;color:#0B266E;background:rgba(11,38,110,0.08);padding:1px 6px;border-radius:4px;margin-left:6px;">{{ $praktikum->kode }}</span>
        @endif
        <span style="font-size:12px;color:#666D80;margin-left:8px;">· {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</span>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        @if($praktikum->dosen)
        <span style="font-size:12px;color:#666D80;">Dosen: <strong>{{ $praktikum->dosen->name }}</strong></span>
        @endif
        @if($praktikum->koordinator)
        <span style="margin-left:8px;font-size:12px;color:#666D80;">Koor: <strong>{{ $praktikum->koordinator->name }}</strong></span>
        @endif
    </div>
    <div>
        @php
            $statusColor = $praktikum->status === 'aktif' ? 'success' : 'neutral';
        @endphp
        <span class="mp-badge {{ $statusColor }} sm"><span class="dot"></span>{{ ucfirst($praktikum->status) }}</span>
    </div>
</div>
@endif

{{-- Section title + count --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Asprak</span>
    <span class="sec-rule"></span>
    @if($praktikum)
    <span class="mp-badge neutral sm">{{ $aspraks->count() }} asprak</span>
    @endif
</div>

{{-- Table --}}
<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">

    {{-- Table header --}}
    <div style="flex-shrink:0; overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:720px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap; width:36px;">#</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap;">Asprak</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap; width:120px;">NIM</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap; width:90px;">Angkatan</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap; width:200px;">Modul Ditugaskan</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap; width:110px;">Status Koor</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted,#666D80); white-space:nowrap; width:90px;">Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aspraks as $i => $asprak)
                @php
                    $user       = $asprak->user;
                    $nameParts  = explode(' ', $user?->name ?? 'AS');
                    $initials   = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
                    $avColors   = ['sky', 'navy', 'green', 'yellow', 'violet'];
                    $avColor    = $avColors[crc32($user?->email ?? '') % count($avColors)];

                    // NIM: coba ambil dari student record
                    $student    = $user ? \App\Models\Student::where('user_id', $user->id)->first() : null;
                    $nim        = $student?->student_number ?? '—';
                    $angkatan   = $student?->cohort_year ?? '—';

                    // Modul yang ditugaskan ke asprak ini
                    $modulNames = $asprak->modulAsprak?->pluck('modul.nama')->filter()->values() ?? collect();

                    // Cek apakah asprak ini juga terdaftar sebagai koor di praktikum yang sama
                    $isKoor = \Modules\EOffice\Models\Praktikum::where('koor_id', $asprak->user_id)
                        ->where('id', $asprak->praktikum_id)
                        ->exists();
                @endphp
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">

                    {{-- No --}}
                    <td style="padding:14px 16px; font-size:12px; color:#A4ABB8; text-align:center;">{{ $i + 1 }}</td>

                    {{-- Asprak (nama + email) --}}
                    <td style="padding:14px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }} flex-shrink-0">{{ $initials }}</div>
                            <div style="min-width:0;">
                                <div style="font-size:13px; font-weight:600; color:var(--c-fg,#0D0D12); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:220px;">
                                    {{ $user?->name ?? '—' }}
                                </div>
                                <div style="font-size:11px; color:var(--c-fg-muted,#666D80);">{{ $user?->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- NIM --}}
                    <td style="padding:14px 16px;">
                        <span style="font-size:12px; font-family:monospace; color:#353849;">{{ $nim }}</span>
                    </td>

                    {{-- Angkatan --}}
                    <td style="padding:14px 16px; text-align:center;">
                        <span style="font-size:12px; font-weight:600; color:var(--c-fg,#0D0D12);">{{ $angkatan }}</span>
                    </td>

                    {{-- Modul ditugaskan --}}
                    <td style="padding:14px 16px;">
                        @if($modulNames->isNotEmpty())
                        <div class="flex flex-wrap gap-[4px]">
                            @foreach($modulNames as $mn)
                            <span style="font-size:10px; font-weight:600; padding:2px 7px; border-radius:4px; background:rgba(11,38,110,0.08); color:#0B266E; white-space:nowrap;">
                                {{ $mn }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <span style="font-size:12px; color:#A4ABB8;">Belum ditugaskan</span>
                        @endif
                    </td>

                    {{-- Status Koor --}}
                    <td style="padding:14px 16px; text-align:center;">
                        @if($isKoor)
                        <span class="mp-badge warning sm"><span class="dot"></span>Juga Koor</span>
                        @else
                        <span style="font-size:11px; color:#A4ABB8;">—</span>
                        @endif
                    </td>

                    {{-- Bergabung --}}
                    <td style="padding:14px 16px; text-align:center; font-size:11px; color:#666D80; white-space:nowrap;">
                        {{ $asprak->created_at?->format('d M Y') ?? '—' }}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:64px 20px; text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <div style="font-size:13px; font-weight:600; color:#666D80;">
                            @if($praktikum)
                                Belum ada asisten praktikum di <strong>{{ $praktikum->nama }}</strong>.
                            @else
                                Pilih praktikum terlebih dahulu.
                            @endif
                        </div>
                        <div style="font-size:12px; color:#A4ABB8; margin-top:4px;">Asprak dapat diterima melalui menu Pendaftaran Asprak.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer: summary --}}
    @if($aspraks->isNotEmpty())
    <div style="padding:10px 16px; border-top:1px solid var(--c-border,#DFE1E7); background:#FAFAFA; flex-shrink:0; display:flex; align-items:center; gap:12px;">
        <span style="font-size:12px; color:#666D80;">
            Total <strong>{{ $aspraks->count() }}</strong> asprak
        </span>
        @php $koorCount = $aspraks->filter(fn($a) => \Modules\EOffice\Models\Praktikum::where('koor_id', $a->user_id)->where('id', $a->praktikum_id)->exists())->count(); @endphp
        @if($koorCount > 0)
        <span style="font-size:12px; color:#D39C3D;">· <strong>{{ $koorCount }}</strong> juga berperan sebagai Koordinator</span>
        @endif

    </div>
    @endif

</div>

{{-- Empty state jika tidak ada praktikum sama sekali --}}
@if($praktikumList->isEmpty())
<div class="mp-card flex-shrink-0">
    <div style="padding:64px 20px; text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z"/>
        </svg>
        <div style="font-size:13px; font-weight:600; color:#666D80;">Belum ada praktikum yang tersedia.</div>
        <div style="margin-top:10px;">
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn primary sm">Tambah Praktikum</a>
        </div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>