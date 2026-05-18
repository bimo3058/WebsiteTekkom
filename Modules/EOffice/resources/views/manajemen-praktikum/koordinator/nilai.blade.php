<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi & Nilai">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi & Nilai</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    @if($praktikum && ($nilaiList ?? collect())->isNotEmpty())
    <div class="mp-page-actions">
        <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.approve') }}">
            @csrf
            <button class="mp-btn primary md">Setujui Nilai</button>
        </form>
    </div>
    @endif
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Rekap Nilai Praktikan</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Rekap Nilai Praktikan</span>
        <div class="right">
            <form method="GET">
                <select name="modul_id" onchange="this.form.submit()" class="mp-input mp-select">
                    <option value="">Semua Modul</option>
                    @foreach($moduls ?? [] as $m)
                    <option value="{{ $m->id }}" {{ ($modulFilter ?? '') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <table class="mp-table">
        <thead>
            <tr style="background:#F9FAFB;">
                <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Nilai Tugas</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Nilai Absensi</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Nilai Akhir</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($nilaiList as $n)
            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                <td style="padding:12px 16px;">
                    <div class="flex items-center gap-[10px]">
                        <div class="mp-av yellow">{{ strtoupper(substr($n->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}</div>
                        <div>
                            <div style="font-weight:600;color:#0D0D12;">{{ $n->daftarPraktikan?->user?->name ?? '-' }}</div>
                            <div style="font-size:11px;color:#666D80;">{{ $n->daftarPraktikan?->user?->email }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding:12px 16px;font-weight:600;color:#0D0D12;">{{ $n->nilai_tugas ?? '-' }}</td>
                <td style="padding:12px 16px;font-weight:600;color:#0D0D12;">{{ $n->nilai_absensi ?? '-' }}</td>
                <td style="padding:12px 16px;font-weight:700;color:#0B266E;">{{ $n->nilai_akhir ?? '-' }}</td>
                <td style="padding:12px 16px;">
                    @if($n->disetujui_koor)
                    <span class="mp-badge navy sm"><span class="dot"></span>Disetujui</span>
                    @else
                    <span class="mp-badge warning sm"><span class="dot"></span>Draft</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <div style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada nilai.</div>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
