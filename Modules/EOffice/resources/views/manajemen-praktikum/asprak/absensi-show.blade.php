<x-eoffice::manajemen-praktikum.layout pageTitle="Isi Absensi">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Absensi {{ $modul->nama }}</h1>
        <p class="mp-page-sub">{{ $modul->praktikum?->nama }} · {{ $praktikans->count() }} praktikan</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.absensi.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

<div class="flex gap-2 flex-wrap flex-shrink-0">
@foreach($moduls as $m)
    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
       class="{{ $m->id === $modul->id ? 'mp-btn success md' : 'mp-btn secondary md' }}"
       style="text-decoration:none;">{{ $m->nama }}</a>
@endforeach
</div>

<form method="POST" action="{{ route('eoffice.manprak.asprak.absensi.store', $modul->id) }}" class="mp-card flex-shrink-0">
    @csrf
    <div class="mp-card-header">
        <div class="flex items-center gap-3">
            <label style="font-size:12px;font-weight:600;color:var(--c-fg-sub);">Tanggal</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="mp-input">
        </div>
        <button class="mp-btn success md">Simpan Absensi</button>
    </div>
    <table class="w-full" style="font-size:13px;">
        <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
            <tr>
                <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                <th class="mp-th text-center" style="padding:10px 16px;">Hadir</th>
                <th class="mp-th text-center" style="padding:10px 16px;">Izin</th>
                <th class="mp-th text-center" style="padding:10px 16px;">Tidak Hadir</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
        @forelse($praktikans as $p)
            @php $row = $absensi[$p->id] ?? null; @endphp
            <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                <td style="padding:12px 16px;">
                    <div style="font-weight:600;color:var(--c-fg);">{{ $p->user?->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $p->user?->email }}</div>
                </td>
                @foreach(['hadir','izin','tidak_hadir'] as $status)
                <td style="padding:12px 16px;text-align:center;">
                    <input type="radio" name="absensi[{{ $p->id }}][status]" value="{{ $status }}" class="accent-[#40C4AA]" {{ ($row?->status ?? 'hadir') === $status ? 'checked' : '' }}>
                </td>
                @endforeach
                <td style="padding:12px 16px;">
                    <input name="absensi[{{ $p->id }}][keterangan]" value="{{ $row?->keterangan }}" class="mp-input w-full" style="font-size:12px;padding:4px 8px;" placeholder="Opsional">
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada praktikan.</td></tr>
        @endforelse
        </tbody>
    </table>
</form>

</x-eoffice::manajemen-praktikum.layout>
