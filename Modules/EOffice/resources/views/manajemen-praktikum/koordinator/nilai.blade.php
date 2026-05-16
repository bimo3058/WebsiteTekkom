<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi & Nilai">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Absensi & Nilai</h1>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }}</p>
    </div>
    @if($praktikum && ($nilaiList ?? collect())->isNotEmpty())
    <div class="mp-page-actions">
        <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.approve') }}">
            @csrf
            <button class="mp-btn success md">Setujui Nilai</button>
        </form>
    </div>
    @endif
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Rekap Nilai Praktikan</span>
        <form method="GET">
            <select name="modul_id" onchange="this.form.submit()" class="mp-input mp-select">
                <option value="">Semua Modul</option>
                @foreach($moduls ?? [] as $m)
                <option value="{{ $m->id }}" {{ ($modulFilter ?? '') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <table class="w-full" style="font-size:13px;">
        <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
            <tr>
                <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Tugas</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Absensi</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Akhir</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($nilaiList as $n)
            <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                <td style="padding:12px 16px;">
                    <div style="font-weight:600;color:var(--c-fg);">{{ $n->daftarPraktikan?->user?->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $n->daftarPraktikan?->user?->email }}</div>
                </td>
                <td style="padding:12px 16px;font-weight:600;color:var(--c-fg);">{{ $n->nilai_tugas ?? '-' }}</td>
                <td style="padding:12px 16px;font-weight:600;color:var(--c-fg);">{{ $n->nilai_absensi ?? '-' }}</td>
                <td style="padding:12px 16px;font-weight:700;color:var(--c-fg);">{{ $n->nilai_akhir ?? '-' }}</td>
                <td style="padding:12px 16px;">
                    @if($n->disetujui_koor)
                    <span class="mp-badge success sm">Disetujui Koor</span>
                    @else
                    <span class="mp-badge warning sm">Draft</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada nilai.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
