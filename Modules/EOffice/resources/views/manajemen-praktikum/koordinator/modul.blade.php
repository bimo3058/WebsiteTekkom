<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="Kelola Modul">
    <style>
        .mp-box-body { gap:15px !important; }
        .mp-box-body > .sticky:first-child { margin-bottom:21px !important; }
        .proposal-action { font-size:13px !important; padding:10px 16px !important; }
        .proposal-note { min-height:40px; height:40px; max-height:180px; resize:vertical; overflow-y:auto; }
        .proposal-status { display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;border:1px solid currentColor;font-size:11px;font-weight:700; }
        .proposal-submit-form { align-items:flex-start !important; }
        .proposal-submit-form > button { margin-top:22px; }
        .proposal-add-form input[name="urutan"], form[id^="item-form-"] input[name="urutan"] { min-width:0;padding:8px;font-size:13px; }
        .proposal-add-form label { white-space:nowrap; }
        @media (max-width:768px) { .proposal-submit-form > button { margin-top:0; } }
        @media (max-width: 768px) {
            .proposal-add-form { grid-template-columns: 1fr !important; }
            .proposal-submit-form { align-items: stretch !important; flex-direction: column !important; }
        }
    </style>
    @if(!$praktikum)
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Kelola Modul Praktikum</h1>
                <p class="mp-page-sub">Belum ada praktikum aktif yang dapat dikelola.</p>
            </div>
        </div>
        <div class="mp-alert warning">Anda belum ditugaskan sebagai koordinator praktikum aktif.</div>
    @else
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />

        @php
            $statusMeta = match($praktikum->status_pelaksanaan ?? 'persiapan') {
                'siap_berjalan' => ['Siap Berjalan', '#047857', '#ECFDF5', '#A7F3D0'],
                'berjalan' => ['Berjalan', '#1D4ED8', '#EFF6FF', '#BFDBFE'],
                'selesai' => ['Selesai', '#475569', '#F8FAFC', '#CBD5E1'],
                default => ['Persiapan', '#B45309', '#FFFBEB', '#FDE68A'],
            };
            $proposalMeta = $pengajuanAktif ? match($pengajuanAktif->status) {
                'diajukan' => ['Menunggu Persetujuan', '#1D4ED8', '#EFF6FF', '#BFDBFE'],
                'perlu_revisi' => ['Perlu Revisi', '#B45309', '#FFFBEB', '#FDE68A'],
                default => ['Draft', '#475569', '#F8FAFC', '#CBD5E1'],
            } : null;
            $editable = $pengajuanAktif?->dapatDiedit() && !$praktikum->started_at;
        @endphp

        @if($errors->any())
            <div class="mp-alert error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
        @endif

        <div class="mp-card" style="margin-bottom:14px;">
            <div style="padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                <div>
                    <div style="display:flex;align-items:center;gap:9px;margin-bottom:5px;">
                        <span class="mp-card-title">Status Struktur Modul</span>
                        <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;color:{{ $statusMeta[1] }};background:{{ $statusMeta[2] }};border:1px solid {{ $statusMeta[3] }};">{{ $statusMeta[0] }}</span>
                    </div>
                    <div style="font-size:12px;color:#666D80;line-height:1.6;">
                        @if($praktikum->started_at)
                            Struktur dikunci sejak absensi pertama pada {{ $praktikum->started_at->locale('id')->translatedFormat('d M Y, H:i') }}.
                        @elseif($moduls->total() > 0)
                            {{ $moduls->total() }} modul aktif telah disetujui. Revisi masih dapat diajukan sebelum absensi pertama.
                        @else
                            Susunan modul harus disetujui dosen sebelum digunakan untuk pembagian asisten dan penjadwalan.
                        @endif
                    </div>
                </div>

                @if(!$praktikum->started_at && !$pengajuanAktif)
                    <form method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.create') }}">
                        @csrf
                        <button type="submit" class="mp-btn primary proposal-action" style="display:inline-flex;align-items:center;gap:6px;background:#0B266E;color:#FFFFFF;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            {{ $moduls->total() > 0 ? 'Ajukan Revisi Modul' : 'Susun Modul' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($pengajuanAktif)
            @if(!empty($hambatanPengajuan))
                <div class="mp-flash mp-flash-error" role="alert" style="border-radius:12px;align-items:flex-start;font-size:13px;line-height:1.6;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <div>
                        <strong style="display:block;margin-bottom:4px;">Persetujuan modul belum dapat diproses</strong>
                        @foreach($hambatanPengajuan as $pesan)<div>{{ $pesan }}</div>@endforeach
                        <div style="margin-top:4px;">Status tetap Menunggu Persetujuan. Modul aktif dan penugasan asisten belum berubah.</div>
                    </div>
                </div>
            @endif
            <div class="mp-card" style="margin-bottom:14px;">
                <div class="mp-card-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <span class="mp-card-title">Pengajuan Modul Versi {{ $pengajuanAktif->versi }}</span>
                            <span style="display:inline-flex;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:700;color:{{ $proposalMeta[1] }};background:{{ $proposalMeta[2] }};border:1px solid {{ $proposalMeta[3] }};">{{ $proposalMeta[0] }}</span>
                        </div>
                        <div style="font-size:13px;color:#666D80;margin-top:4px;">
                            @if($pengajuanAktif->praktikumSumber)
                                Dasar: {{ $pengajuanAktif->praktikumSumber->nama }} · {{ $pengajuanAktif->praktikumSumber->semester }} {{ $pengajuanAktif->praktikumSumber->tahun_ajaran }}
                            @else
                                Pengajuan pertama, dibuat tanpa modul periode sebelumnya.
                            @endif
                        </div>
                    </div>
                    @if(!$praktikum->started_at)
                        <form method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.cancel', $pengajuanAktif) }}" onsubmit="return confirm('{{ $pengajuanAktif->status === 'diajukan' ? 'Tarik pengajuan dari dosen?' : 'Batalkan draft ini?' }}')">
                            @csrf
                            <button type="submit" class="mp-btn proposal-action" style="color:#DF1C41;background:#FFF1F2;border:1px solid #FECDD3;">{{ $pengajuanAktif->status === 'diajukan' ? 'Tarik Pengajuan' : 'Batalkan Draft' }}</button>
                        </form>
                    @endif
                </div>

                @if($pengajuanAktif->status === 'perlu_revisi' && $pengajuanAktif->catatan_reviewer)
                    <div style="margin:15px 20px;padding:12px 14px;border:1px solid #DFE1E7;border-radius:9px;background:#F9FAFB;font-size:13px;color:#475569;overflow-wrap:anywhere;">
                        <strong>Catatan dosen:</strong>
                        {{ $pengajuanAktif->catatan_reviewer }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="mp-table">
                        <thead><tr style="background:#F9FAFB;"><th class="mp-th" style="width:90px;">STATUS</th><th class="mp-th">SEBELUM</th><th class="mp-th">SESUDAH</th>@if($editable)<th class="mp-th" style="width:120px;text-align:right;">AKSI</th>@endif</tr></thead>
                        <tbody>
                            @forelse($pengajuanAktif->items as $item)
                                @php
                                    $aksiMeta = match($item->aksi) {
                                        'tambah' => ['Ditambahkan', '#047857', '#ECFDF5'],
                                        'ubah' => ['Diubah', '#1D4ED8', '#EFF6FF'],
                                        'hapus' => ['Dihapus', '#DC2626', '#FEF2F2'],
                                        default => ['Tetap', '#475569', '#F1F5F9'],
                                    };
                                @endphp
                                <tr class="mp-tr">
                                    <td style="padding:14px 16px;vertical-align:top;"><span style="display:inline-flex;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:700;color:{{ $aksiMeta[1] }};background:{{ $aksiMeta[2] }};">{{ $aksiMeta[0] }}</span></td>
                                    <td style="padding:14px 16px;vertical-align:top;">
                                        @if($item->nama_lama)<div style="font-size:13px;font-weight:600;color:#353849;">{{ $item->urutan_lama }}. {{ $item->nama_lama }}</div>@else<span style="font-size:12px;color:#A4ABB8;">Tidak ada</span>@endif
                                    </td>
                                    <td style="padding:14px 16px;vertical-align:top;">
                                        @if($editable && $item->aksi !== 'hapus')
                                            <form id="item-form-{{ $item->id }}" method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.item.update', $item) }}" style="display:grid;grid-template-columns:64px minmax(220px,1fr);gap:8px;">
                                                @csrf @method('PUT')
                                                <input class="mp-input" type="number" name="urutan" min="1" value="{{ $item->urutan_baru }}" required aria-label="Urutan modul">
                                                <input class="mp-input" type="text" name="nama" maxlength="255" value="{{ $item->nama_baru }}" required aria-label="Nama modul">
                                            </form>
                                        @elseif($item->aksi === 'hapus')
                                            <span style="font-size:12px;color:#DC2626;">Tidak digunakan pada periode ini</span>
                                        @else
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $item->urutan_baru }}. {{ $item->nama_baru }}</div>
                                        @endif
                                    </td>
                                    @if($editable)
                                        <td style="padding:14px 16px;vertical-align:top;text-align:right;">
                                            @if($item->aksi === 'hapus')
                                                <form method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.item.restore', $item) }}">@csrf<button type="submit" class="mp-btn secondary" style="padding:8px 11px;font-size:12px;">Pulihkan</button></form>
                                            @else
                                                <div style="display:flex;justify-content:flex-end;gap:6px;">
                                                    <x-eoffice::manajemen-praktikum.ui.action-menu>
                                                    <button form="item-form-{{ $item->id }}" type="submit" class="mp-btn secondary" style="padding:8px 11px;font-size:12px;display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12l4 4v12a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8V3"/></svg>Simpan</button>
                                                    <form method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.item.destroy', $item) }}" onsubmit="return confirm('Hapus modul ini dari rancangan?')">@csrf @method('DELETE')<button type="submit" class="mp-btn" style="padding:8px 10px;color:#DF1C41;background:#FFF1F2;border:1px solid #FECDD3;" aria-label="Hapus modul"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>Hapus Modul</button></form>
                                                    </x-eoffice::manajemen-praktikum.ui.action-menu>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="4" style="padding:34px;text-align:center;font-size:13px;color:#666D80;">Draft masih kosong. Tambahkan modul pertama di bawah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($editable)
                    <div style="padding:18px 20px;border-top:1px solid #DFE1E7;background:#FBFCFD;">
                        <div style="font-size:13px;font-weight:700;color:#0D0D12;margin-bottom:10px;">Tambah Modul</div>
                        <form method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.item.store', $pengajuanAktif) }}" class="proposal-add-form" style="display:grid;grid-template-columns:64px minmax(240px,1fr) auto;gap:10px;align-items:end;">@csrf
                            <div><label style="display:block;font-size:11px;font-weight:700;color:#353849;margin-bottom:6px;">URUTAN <span style="color:#DF1C41;">*</span></label><input class="mp-input" type="number" min="1" name="urutan" value="{{ max(1, (int) $pengajuanAktif->items->where('aksi', '!=', 'hapus')->max('urutan_baru') + 1) }}" required></div>
                            <div><label style="display:block;font-size:11px;font-weight:700;color:#353849;margin-bottom:6px;">NAMA MODUL <span style="color:#DF1C41;">*</span></label><input class="mp-input" type="text" maxlength="255" name="nama" placeholder="Masukkan nama modul" required></div>
                            <button type="submit" class="mp-btn primary proposal-action" style="height:40px;background:#0B266E;color:#FFFFFF;">Tambah Modul</button>
                        </form>
                        <form method="POST" action="{{ route('eoffice.manprak.koor.pengajuan-modul.submit', $pengajuanAktif) }}" class="proposal-submit-form" style="margin-top:18px;padding-top:18px;border-top:1px solid #DFE1E7;display:flex;align-items:end;gap:12px;">@csrf
                            <div style="flex:1;"><label style="display:block;font-size:11px;font-weight:700;color:#353849;margin-bottom:6px;">CATATAN UNTUK DOSEN <span style="font-weight:500;color:#808897;">(Maks. 500 karakter, opsional)</span></label><textarea class="mp-input proposal-note" name="catatan" rows="1" maxlength="500" placeholder="Jelaskan alasan perubahan jika diperlukan"></textarea></div>
                            <button type="submit" class="mp-btn primary proposal-action" style="height:40px;background:#0B266E;color:#FFFFFF;" onclick="return confirm('Kirim susunan modul ini kepada dosen?')">Ajukan ke Dosen</button>
                        </form>
                    </div>
                @else
                    <div style="padding:14px 20px;border-top:1px solid #DFE1E7;background:#F8FAFC;font-size:12px;color:#475569;">Draft dikunci selama menunggu keputusan dosen.</div>
                @endif
            </div>
        @endif

        <div class="mp-card" style="margin-bottom:14px;">
            <div class="mp-card-header" style="display:flex;align-items:center;justify-content:space-between;">
                <div><span class="mp-card-title">Daftar Modul</span><div style="font-size:13px;color:#666D80;margin-top:3px;">Hanya modul yang telah disetujui dosen.</div></div>
                @if($moduls->total() > 0)<a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}" class="mp-btn primary md" style="text-decoration:none;font-size:13px;">Atur Asisten Modul</a>@endif
            </div>
            <div class="overflow-x-auto"><table class="mp-table">
                <thead><tr style="background:#F9FAFB;"><th class="mp-th" style="width:80px;text-align:center;">NO</th><th class="mp-th">NAMA MODUL</th><th class="mp-th">ASISTEN</th><th class="mp-th">KONTEN</th></tr></thead>
                <tbody>
                    @forelse($moduls as $modul)
                        <tr class="mp-tr"><td style="padding:14px 16px;text-align:center;font-size:12px;font-weight:700;color:#666D80;">{{ $modul->urutan }}</td><td style="padding:14px 16px;font-size:13px;font-weight:600;color:#0D0D12;">{{ $modul->nama }}</td><td style="padding:14px 16px;font-size:12px;color:#666D80;">@if($asistenNames = $modul->modulAsprak->pluck('asprak.user.name')->filter()->join(', ')){{ $asistenNames }}@else<span style="color:#DF1C41;">Belum ditentukan</span>@endif</td><td style="padding:14px 16px;font-size:12px;color:#666D80;">{{ $modul->materi->count() }} materi · {{ $modul->tugas->count() }} tugas</td></tr>
                    @empty
                        <tr><td colspan="4" style="padding:42px;text-align:center;"><x-eoffice::manajemen-praktikum.ui.empty-icon name="book" /><div style="font-size:13px;font-weight:600;color:#353849;">Belum ada modul aktif</div><div style="font-size:12px;color:#808897;margin-top:4px;">Susun dan ajukan modul untuk mendapatkan persetujuan dosen.</div></td></tr>
                    @endforelse
                </tbody>
            </table></div>
            @if(isset($moduls) && $moduls instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$moduls" :options="[5, 10, 20]" />
            @endif
        </div>

        <x-eoffice::manajemen-praktikum.ui.module-history :riwayatPengajuan="$riwayatPengajuan" title="Riwayat Pengajuan Modul" :collapsible="true" />
        <x-eoffice::manajemen-praktikum.ui.module-components :praktikum="$praktikum" />
    @endif
    <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum ?? null" role="koor" section="modul" :edit="true" />
</x-eoffice::manajemen-praktikum.layout>
