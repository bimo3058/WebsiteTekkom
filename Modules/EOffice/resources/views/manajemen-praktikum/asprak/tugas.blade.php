<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Tugas">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Kelola Tugas Praktikum</h1>
        <p class="mp-page-sub">Buat tugas dan nilai pengumpulan mahasiswa</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn success md" style="text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
    </div>
</div>

@forelse($tugasList ?? [] as $tugas)
@php
    $dl    = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
    $lewat = $dl && now()->gt($dl);
    $totalKumpul = $tugas->pengumpulan_count ?? 0;
    $acc         = $tugas->pengumpulan_acc_count ?? 0;
    $revisi      = $tugas->pengumpulan_revisi_count ?? 0;
    $belumDicek  = $totalKumpul - $acc - $revisi;
@endphp
<div class="mp-card flex-shrink-0" style="padding:20px;" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <div style="font-size:14px;font-weight:700;color:var(--c-fg);">{{ $tugas->judul }}</div>
                @if($lewat)
                <span class="mp-badge neutral sm">Berakhir</span>
                @else
                <span class="mp-badge success sm">Aktif</span>
                @endif
            </div>
            <div style="font-size:12px;color:var(--c-fg-muted);">
                Modul: <span style="font-weight:600;color:var(--c-fg-sub);">{{ $tugas->modul?->nama ?? '—' }}</span>
                @if($dl)
                · Deadline: <span style="font-weight:600;color:{{ $lewat ? 'var(--c-fg-muted)' : 'var(--c-fg-sub)' }};">{{ $dl->format('d M Y, H:i') }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-4 flex-shrink-0">
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:var(--c-fg);">{{ $totalKumpul }}</div>
                <div style="font-size:10px;color:var(--c-fg-muted);">Dikumpul</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#D39C3D;">{{ max(0,$belumDicek) }}</div>
                <div style="font-size:10px;color:var(--c-fg-muted);">Belum Dicek</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#DF1C41;">{{ $revisi }}</div>
                <div style="font-size:10px;color:var(--c-fg-muted);">Revisi</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#40C4AA;">{{ $acc }}</div>
                <div style="font-size:10px;color:var(--c-fg-muted);">ACC</div>
            </div>
            <button @click="open = !open" class="mp-btn secondary sm" x-text="open ? 'Tutup' : 'Lihat Pengumpulan'"></button>
        </div>
    </div>

    {{-- Pengumpulan Table --}}
    <div x-show="open" x-transition class="mt-4" style="border-top:1px solid var(--c-border-light);padding-top:16px;">
        @php $pengumpulanList = $tugas->pengumpulan ?? collect(); @endphp
        @if($pengumpulanList->isEmpty())
        <div style="padding:20px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada yang mengumpulkan.</div>
        @else
        <div style="border:1px solid var(--c-border);border-radius:10px;overflow:hidden;">
            <div style="display:flex;align-items:center;padding:8px 16px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <div class="mp-th flex-1">Mahasiswa</div>
                <div class="mp-th" style="width:90px;">Status</div>
                <div class="mp-th" style="width:100px;">Waktu Kumpul</div>
                <div class="mp-th" style="width:65px;">File</div>
                <div class="mp-th" style="width:65px;">Nilai</div>
                <div class="mp-th" style="width:200px;">Aksi</div>
            </div>
            @foreach($pengumpulanList as $peng)
            @php $st = $peng->status_pengumpulan ?? 'belum_dicek'; @endphp
            <div class="mp-tr" style="display:flex;align-items:center;padding:10px 16px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex-1 flex items-center gap-2 min-w-0 pr-2">
                    <div style="font-size:13px;font-weight:500;color:var(--c-fg);" class="truncate">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                    @if($peng->is_revision)
                    <span class="mp-badge primary sm">Revisi ke-{{ $peng->is_revision }}</span>
                    @endif
                </div>
                <div style="width:90px;">
                    @if($st === 'acc')
                    <span class="mp-badge success sm">ACC</span>
                    @elseif($st === 'revisi')
                    <span class="mp-badge error sm">Revisi</span>
                    @else
                    <span class="mp-badge warning sm">Belum Dicek</span>
                    @endif
                </div>
                <div style="width:100px;font-size:11px;color:var(--c-fg-muted);">{{ $peng->created_at?->format('d M, H:i') }}</div>
                <div style="width:65px;">
                    @if($peng->file_path)
                    <a href="{{ Storage::url($peng->file_path) }}" target="_blank"
                       style="font-size:11px;font-weight:600;color:var(--c-primary);text-decoration:none;" class="hover:underline">Unduh</a>
                    @else
                    <span style="font-size:11px;color:var(--c-fg-placeholder);">—</span>
                    @endif
                </div>
                <div style="width:65px;">
                    @if($peng->nilai !== null)
                    <span style="font-size:14px;font-weight:700;color:{{ $peng->nilai >= 75 ? '#40C4AA' : ($peng->nilai >= 50 ? '#D39C3D' : '#DF1C41') }};">{{ $peng->nilai }}</span>
                    @else
                    <span style="font-size:12px;color:var(--c-fg-placeholder);">—</span>
                    @endif
                </div>
                <div class="flex gap-1 items-center" style="width:200px;">
                    @if($st !== 'acc')
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $peng->id) }}" class="flex gap-1 items-center">
                        @csrf
                        <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0–100"
                               value="{{ $peng->nilai ?? '' }}"
                               class="mp-input" style="width:68px;font-size:12px;padding:4px 8px;">
                        <button type="submit" class="mp-btn ghost sm">ACC</button>
                    </form>
                    @endif
                    @if($st !== 'revisi' && $st !== 'acc')
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $peng->id) }}" x-data="{ catatan: '' }">
                        @csrf
                        <input type="hidden" name="catatan_revisi" :value="catatan">
                        <button type="button"
                                @click="catatan = prompt('Catatan revisi:'); if(catatan) $el.closest('form').submit()"
                                class="mp-btn warning sm">Revisi</button>
                    </form>
                    @endif
                    @if($st === 'acc')
                    <span style="font-size:11px;color:#40C4AA;font-weight:600;">✓ Selesai</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@empty
<div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
    <div style="text-align:center;color:var(--c-fg-placeholder);">
        <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <div style="font-size:14px;font-weight:500;">Belum ada tugas. Buat tugas baru!</div>
    </div>
</div>
@endforelse

</x-eoffice::manajemen-praktikum.layout>
