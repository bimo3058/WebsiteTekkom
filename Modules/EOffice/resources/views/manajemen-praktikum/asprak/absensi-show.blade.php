<x-eoffice::manajemen-praktikum.layout pageTitle="Isi Absensi">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi {{ $modul->nama }}</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">{{ $modul->praktikum?->nama }} · {{ $praktikans->count() }} praktikan</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.absensi.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

{{-- Pilih Modul --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-2 flex-wrap flex-shrink-0">
    @foreach($moduls as $m)
    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
       class="{{ $m->id === $modul->id ? 'mp-btn primary md' : 'mp-btn secondary md' }}"
       style="text-decoration:none;">{{ $m->nama }}</a>
    @endforeach
</div>

{{-- Form Absensi --}}
<form method="POST" action="{{ route('eoffice.manprak.asprak.absensi.store', $modul->id) }}" class="mp-card flex-shrink-0">
    @csrf
    <div class="mp-card-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <label style="font-size:12px;font-weight:600;color:#353849;">Tanggal</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="mp-input">
        </div>
        <div class="right">
            <button class="mp-btn primary md">Simpan Absensi</button>
        </div>
    </div>

    <table class="mp-table">
        <thead>
            <tr style="background:#F9FAFB;">
                <th style="padding:10px 16px;text-align:left;">Mahasiswa</th>
                <th style="padding:10px 16px;text-align:center;">Hadir</th>
                <th style="padding:10px 16px;text-align:center;">Izin</th>
                <th style="padding:10px 16px;text-align:center;">Tidak Hadir</th>
                <th style="padding:10px 16px;text-align:left;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
        @forelse($praktikans as $p)
            @php $row = $absensi[$p->id] ?? null; @endphp
            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                <td style="padding:12px 16px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="mp-av yellow">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                        <div>
                            <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '-' }}</div>
                            <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                        </div>
                    </div>
                </td>
                @foreach(['hadir','izin','tidak_hadir'] as $status)
                <td style="padding:12px 16px;text-align:center;">
                    <input type="radio" name="absensi[{{ $p->id }}][status]" value="{{ $status }}"
                           style="accent-color:#0B266E;" {{ ($row?->status ?? 'hadir') === $status ? 'checked' : '' }}>
                </td>
                @endforeach
                <td style="padding:12px 16px;">
                    <input name="absensi[{{ $p->id }}][keterangan]" value="{{ $row?->keterangan }}"
                           class="mp-input w-full" style="font-size:12px;padding:4px 8px;" placeholder="Opsional">
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <div style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>
                        </svg>
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan.</div>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</form>

</x-eoffice::manajemen-praktikum.layout>
