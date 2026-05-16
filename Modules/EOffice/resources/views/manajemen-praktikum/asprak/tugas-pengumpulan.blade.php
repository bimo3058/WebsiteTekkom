<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumpulan Tugas">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">{{ $tugas->judul }}</h1>
        <p class="mp-page-sub">{{ $tugas->modul?->nama }} - {{ $tugas->modul?->praktikum?->nama }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Pengumpulan</span>
    </div>
    <table class="w-full" style="font-size:13px;">
        <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
            <tr>
                <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                <th class="mp-th text-left" style="padding:10px 16px;">File</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Nilai</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($pengumpulan as $p)
            @php $st = $p->status_pengumpulan ?? 'belum_dicek'; @endphp
            <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                <td style="padding:12px 16px;">
                    <div style="font-weight:600;color:var(--c-fg);">{{ $p->daftarPraktikan?->user?->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $p->created_at?->format('d M Y H:i') }}</div>
                    @if($p->catatan)<div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $p->catatan }}</div>@endif
                </td>
                <td style="padding:12px 16px;">
                    @if($st === 'acc')
                    <span class="mp-badge success sm">ACC</span>
                    @elseif($st === 'revisi')
                    <span class="mp-badge error sm">Revisi</span>
                    @else
                    <span class="mp-badge warning sm">Belum Dicek</span>
                    @endif
                </td>
                <td style="padding:12px 16px;">
                    @if($p->file_path)
                    <a href="{{ Storage::url($p->file_path) }}" target="_blank"
                       style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;" class="hover:underline">Unduh</a>
                    @else
                    <span style="font-size:12px;color:var(--c-fg-placeholder);">-</span>
                    @endif
                </td>
                <td style="padding:12px 16px;font-weight:700;color:var(--c-fg);">{{ $p->nilai ?? '—' }}</td>
                <td style="padding:12px 16px;">
                    <div class="flex gap-2 items-center">
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $p->id) }}" class="flex gap-1">
                            @csrf
                            <input type="number" name="nilai" min="0" max="100" value="{{ $p->nilai }}"
                                   class="mp-input" style="width:72px;font-size:12px;padding:4px 8px;">
                            <button class="mp-btn ghost sm">ACC</button>
                        </form>
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $p->id) }}" x-data="{ catatan: '' }">
                            @csrf
                            <input type="hidden" name="catatan_revisi" :value="catatan">
                            <button type="button"
                                    @click="catatan = prompt('Catatan revisi:'); if(catatan) $el.closest('form').submit()"
                                    class="mp-btn warning sm">Revisi</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada pengumpulan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</x-eoffice::manajemen-praktikum.layout>
