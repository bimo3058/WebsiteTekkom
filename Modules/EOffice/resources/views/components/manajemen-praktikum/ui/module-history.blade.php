@props(['riwayatPengajuan', 'title' => 'Riwayat Perubahan Modul', 'collapsible' => false])
@once
<style>
.mp-module-history { overflow:hidden; }
.mp-module-history .mp-module-status { display:inline-flex;align-items:center;padding:3px 9px;border:1px solid currentColor;border-radius:999px;font-size:11px;font-weight:700;text-transform:capitalize; }
.mp-module-history .mp-version-toggle { display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;padding:16px 20px;border:0;background:#fff;text-align:left;cursor:pointer; }
.mp-module-history .mp-version-toggle:hover { background:#FAFBFD; }
.mp-module-history .mp-version-table { min-width:540px;table-layout:fixed; }
.mp-module-history .mp-version-table th { background:#F7F8FA; }
.mp-module-history .mp-version-table td { padding:14px 16px;font-size:13px;overflow-wrap:anywhere;vertical-align:top; }
.mp-module-history .mp-version-note { padding:14px 20px;border-top:1px solid #DFE1E7;background:#FAFBFD;font-size:12px;line-height:1.6;color:#666D80;overflow-wrap:anywhere; }
</style>
@endonce
        @if($riwayatPengajuan->isNotEmpty())
            <div class="mp-card mp-module-section mp-module-history" x-data="{historyOpen:{{ $collapsible ? 'false' : 'true' }}}">
                @if($collapsible)
                    <button type="button" class="mp-version-toggle" @click="historyOpen=!historyOpen" :aria-expanded="historyOpen"><span class="mp-card-title">{{ $title }}</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" :style="historyOpen ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s"><path d="m6 9 6 6 6-6"/></svg></button>
                @else
                    <div class="mp-card-header"><span class="mp-card-title">{{ $title }}</span></div>
                @endif
                <x-eoffice::manajemen-praktikum.ui.grade-accordion state="historyOpen"><div>
                @foreach($riwayatPengajuan as $riwayat)
                    @php
                        $historyColor = match($riwayat->status) { 'disetujui' => '#059669', 'ditolak' => '#DF1C41', 'perlu_revisi' => '#B45309', default => '#666D80' };
                        $historyBg = match($riwayat->status) { 'disetujui' => '#ECFDF5', 'ditolak' => '#FFF1F2', 'perlu_revisi' => '#FFFBEB', default => '#F8FAFC' };
                    @endphp
                    <div x-data="{ open: false }" @if(!$loop->first) style="border-top:1px solid #DFE1E7;" @endif>
                        <button type="button" class="mp-version-toggle" @click="open=!open" :aria-expanded="open" aria-controls="module-version-{{ $riwayat->id }}">
                            <div>
                                <div style="display:flex;align-items:center;gap:9px;flex-wrap:wrap;">
                                    <span style="font-size:13px;font-weight:700;color:#0D0D12;">Versi {{ $riwayat->versi }}</span>
                                    <span class="mp-module-status" style="color:{{ $historyColor }};background:{{ $historyBg }};">{{ str_replace('_', ' ', $riwayat->status) }}</span>
                                </div>
                                <div style="font-size:12px;color:#666D80;margin-top:4px;">{{ $riwayat->status === 'dibatalkan' ? ($riwayat->diajukan_at ? 'Pengajuan ditarik oleh koordinator' : 'Draft dibatalkan sebelum diajukan') : ($riwayat->reviewer?->name ?? '-') }} · {{ $riwayat->direview_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}</div>
                            </div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="flex-shrink:0;transition:transform .2s;" :style="open ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <x-eoffice::manajemen-praktikum.ui.grade-accordion state="open"><div id="module-version-{{ $riwayat->id }}">
                            <div class="overflow-x-auto" style="border-top:1px solid #DFE1E7;">
                                <table class="mp-table mp-version-table">
                                    <thead><tr><th class="mp-th" style="width:130px;">STATUS</th><th class="mp-th">SEBELUM</th><th class="mp-th">SESUDAH</th></tr></thead>
                                    <tbody>
                                        @forelse($riwayat->items as $item)
                                            @php
                                                $changeLabel = match($item->aksi) { 'tambah' => 'Ditambahkan', 'ubah' => 'Diubah', 'hapus' => 'Dihapus', default => 'Tetap' };
                                                $changeColor = match($item->aksi) { 'tambah' => '#059669', 'ubah' => '#0B266E', 'hapus' => '#DF1C41', default => '#666D80' };
                                            @endphp
                                            <tr class="mp-tr">
                                                <td><span style="display:inline-flex;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:700;color:{{ $changeColor }};background:{{ match($item->aksi) { 'tambah' => '#ECFDF5', 'ubah' => '#EFF6FF', 'hapus' => '#FEF2F2', default => '#F1F5F9' } }};">{{ $changeLabel }}</span></td>
                                                <td style="color:#666D80;">{{ $item->nama_lama ? $item->urutan_lama . '. ' . $item->nama_lama : 'Tidak ada' }}</td>
                                                <td style="color:#353849;font-weight:600;">{{ $item->nama_baru ? $item->urutan_baru . '. ' . $item->nama_baru : 'Tidak digunakan' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" style="text-align:center;color:#666D80;">Draft ini belum memiliki modul.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($riwayat->catatan_pengajuan)<div class="mp-version-note"><strong>Catatan koordinator:</strong> {{ $riwayat->catatan_pengajuan }}</div>@endif
                            @if($riwayat->catatan_reviewer)<div class="mp-version-note"><strong>Catatan dosen:</strong> {{ $riwayat->catatan_reviewer }}</div>@endif
                        </div></x-eoffice::manajemen-praktikum.ui.grade-accordion>
                    </div>
                @endforeach
                </div></x-eoffice::manajemen-praktikum.ui.grade-accordion>
            </div>
        @endif
