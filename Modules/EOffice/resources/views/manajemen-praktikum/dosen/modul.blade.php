<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="{{ $praktikum ? $praktikum->nama . ' / Modul' : 'Modul' }}">
    <style>
        .mp-app .mp-box-body { gap:15px; }
        .mp-module-section { margin-bottom:14px; }
        .mp-module-history { overflow:hidden; }
        .mp-review-actions .mp-btn { font-size:13px;padding:10px 16px; }
        .mp-module-status { display:inline-flex;align-items:center;padding:3px 9px;border:1px solid currentColor;border-radius:999px;font-size:11px;font-weight:700;text-transform:capitalize; }
        .mp-version-toggle { display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;padding:16px 20px;border:0;background:#fff;text-align:left;cursor:pointer; }
        .mp-version-toggle:hover { background:#FAFBFD; }
        .mp-version-table { min-width:540px;table-layout:fixed; }
        .mp-version-table th { background:#F7F8FA; }
        .mp-version-table td { padding:14px 16px;font-size:13px;overflow-wrap:anywhere;vertical-align:top; }
        .mp-version-note { padding:14px 20px;border-top:1px solid #DFE1E7;background:#FAFBFD;font-size:12px;line-height:1.6;color:#666D80;overflow-wrap:anywhere; }
    </style>

    {{-- Pilih Praktikum --}}
    @if(!$praktikum)

        {{-- Section: Pilih Praktikum --}}
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Pilih Praktikum</span>
            <span class="sec-rule"></span>
        </div>

        <div class="mp-card flex-shrink-0">
            <div style="padding:48px;text-align:center;">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 14px;display:block;">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                </svg>
                <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Pilih Praktikum</div>
                <div style="font-size:13px;color:#666D80;margin-bottom:24px;">Pilih praktikum yang ingin dilihat modulnya.
                </div>

                @if($praktikums->isEmpty())
                    <div style="font-size:13px;color:#808897;">Kamu belum mengampu praktikum apapun.</div>
                @else
                    <div class="flex flex-col gap-2 max-w-sm mx-auto">
                        @foreach($praktikums as $p)
                            <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
                                class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]"
                                style="text-decoration:none;border:1px solid #DFE1E7;">
                                <span style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $p->nama }}</span>
                                <span style="font-size:11px;color:#666D80;">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    @else

        {{-- Sticky Header Wrapper --}}
        <div x-data="{ st: 0 }" x-init="
                    const box = document.querySelector('.mp-box-body');
                    if (box) {
                        let ticking = false;
                        box.addEventListener('scroll', () => {
                            if (!ticking) {
                                window.requestAnimationFrame(() => {
                                    st = Math.max(0, box.scrollTop);
                                    ticking = false;
                                });
                                ticking = true;
                            }
                        });
                    }
                 " class="sticky z-20 bg-white"
            style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border);">

            {{-- Banner / Cover Image Container --}}
            <div
                style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">

                {{-- Cover Placeholder or Image --}}
                <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                    @if($praktikum->cover_path)
                        <img src="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($praktikum->cover_path, 'eoffice') }}"
                             class="w-full h-full object-cover"
                             :style="`transform: scale(${1 + Math.min(1, Math.max(0, st / 200)) * 0.04});`"
                             alt="Cover Praktikum">
                    @else
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896"
                            :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                            <path
                                d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                    @endif
                </div>

                {{-- 1. Base Gradient --}}
                <div class="absolute inset-0 pointer-events-none"
                    style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 60%, rgba(0,0,0,0) 100%);">
                </div>

                {{-- 2. Scrolled Overlay (Gelap + Blur) --}}
                <div class="absolute inset-0 pointer-events-none"
                    style="display:none;" aria-hidden="true">
                </div>

                {{-- Title --}}
                <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
                    <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                        :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                        {{ $praktikum->nama }}
                    </h1>
                </div>
            </div>

            {{-- Tabs --}}
            <div style="display:flex;gap:8px;overflow-x:auto;white-space:nowrap;scrollbar-width:none;">

                <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
                <a href="#"
                    style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Modul</a>
                <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Tugas</a>
                <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi & Nilai</a>
                <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
                <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi
                    Koordinator</a>
            </div>
        </div>

        @if($errors->any())
            <div class="mp-flash mp-flash-error" role="alert" style="border-radius:12px;font-size:13px;line-height:1.6;align-items:flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                <div>
                    <strong style="display:block;margin-bottom:4px;">{{ $errors->has('pengajuan') ? 'Gagal menyetujui pengajuan' : 'Data belum dapat diproses' }}</strong>
                    @foreach($errors->all() as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Review pengajuan struktur modul --}}
        @if($pengajuanAktif)
            <div x-data="{ reviewOpen: false, reviewTitle: '', reviewAction: '', noteRequired: false }" class="mp-card mp-module-section">
                <div class="mp-card-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <span class="mp-card-title">Pengajuan Modul Versi {{ $pengajuanAktif->versi }}</span>
                            <span style="display:inline-flex;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:700;color:#1D4ED8;background:#EFF6FF;border:1px solid #BFDBFE;">Menunggu Keputusan</span>
                        </div>
                        <div style="font-size:12px;color:#666D80;margin-top:5px;">
                            Diajukan oleh {{ $pengajuanAktif->pengaju?->name ?? 'Koordinator' }}
                            @if($pengajuanAktif->diajukan_at) · {{ $pengajuanAktif->diajukan_at->locale('id')->translatedFormat('d M Y, H:i') }} @endif
                        </div>
                    </div>
                    <div class="mp-review-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
                        <button type="button" class="mp-btn secondary" @click="reviewOpen=true;reviewTitle='Revisi';reviewAction='{{ route('eoffice.manprak.dosen.pengajuan-modul.revision', $pengajuanAktif) }}';noteRequired=true">Revisi</button>
                        <button type="button" class="mp-btn" style="color:#DC2626;background:#FFF1F2;border:1px solid #FECDD3;" @click="reviewOpen=true;reviewTitle='Tolak Pengajuan';reviewAction='{{ route('eoffice.manprak.dosen.pengajuan-modul.reject', $pengajuanAktif) }}';noteRequired=true">Tolak</button>
                        <button type="button" class="mp-btn primary" @click="reviewOpen=true;reviewTitle='Setujui Pengajuan';reviewAction='{{ route('eoffice.manprak.dosen.pengajuan-modul.approve', $pengajuanAktif) }}';noteRequired=false">Setujui</button>
                    </div>
                </div>

                @if($pengajuanAktif->catatan_pengajuan)
                    <div style="margin:15px 20px;padding:12px 14px;border:1px solid #DFE1E7;border-radius:9px;background:#F9FAFB;font-size:13px;color:#475569;"><strong>Catatan koordinator:</strong> {{ $pengajuanAktif->catatan_pengajuan }}</div>
                @endif

                <div class="overflow-x-auto">
                    <table class="mp-table">
                        <thead><tr style="background:#F9FAFB;"><th class="mp-th" style="width:110px;">STATUS</th><th class="mp-th">SEBELUM</th><th class="mp-th">SESUDAH</th></tr></thead>
                        <tbody>
                            @foreach($pengajuanAktif->items as $item)
                                @php
                                    $reviewAction = match($item->aksi) { 'tambah' => 'Ditambahkan', 'ubah' => 'Diubah', 'hapus' => 'Dihapus', default => 'Tetap' };
                                    $reviewColor = match($item->aksi) { 'tambah' => '#047857', 'ubah' => '#1D4ED8', 'hapus' => '#DC2626', default => '#475569' };
                                @endphp
                                <tr class="mp-tr">
                                    <td style="padding:14px 16px;"><span style="display:inline-flex;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:700;color:{{ $reviewColor }};background:{{ match($item->aksi) { 'tambah' => '#ECFDF5', 'ubah' => '#EFF6FF', 'hapus' => '#FEF2F2', default => '#F1F5F9' } }};">{{ $reviewAction }}</span></td>
                                    <td style="padding:14px 16px;font-size:13px;color:#475569;">{{ $item->nama_lama ? $item->urutan_lama . '. ' . $item->nama_lama : 'Tidak ada' }}</td>
                                    <td style="padding:14px 16px;font-size:13px;font-weight:600;color:#0D0D12;">{{ $item->nama_baru ? $item->urutan_baru . '. ' . $item->nama_baru : 'Tidak digunakan' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <template x-teleport="body">
                    <div x-show="reviewOpen" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black/45 px-4" @keydown.escape.window="reviewOpen=false">
                        <div x-show="reviewOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.outside="reviewOpen=false" class="bg-white rounded-[14px] shadow-2xl w-full max-w-md overflow-hidden">
                            <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;"><div style="font-size:17px;font-weight:750;color:#0D0D12;" x-text="reviewTitle"></div><div style="font-size:12px;color:#666D80;margin-top:4px;">Keputusan pertama dari salah satu dosen menjadi keputusan yang berlaku.</div></div>
                            <form method="POST" :action="reviewAction">@csrf
                                <div style="padding:20px 22px;"><label style="display:block;font-size:12px;font-weight:700;color:#353849;margin-bottom:7px;">Catatan <span x-show="noteRequired" style="color:#DF1C41;">*</span> <span style="font-weight:500;color:#A4ABB8;">(Maks. 500 karakter)</span></label><textarea class="mp-input" style="display:block;min-height:40px;height:40px;max-height:240px;resize:vertical;overflow-y:auto;" name="catatan" rows="1" maxlength="500" :required="noteRequired" placeholder="Tuliskan catatan keputusan"></textarea></div>
                                <div class="mp-modal-footer" style="padding:14px 22px;border-top:1px solid #DFE1E7;background:#FAFBFC;display:flex;justify-content:flex-end;gap:8px;"><button type="button" class="mp-btn secondary" style="font-size:13px;padding:10px 16px;color:#353849;" @click="reviewOpen=false">Batal</button><button type="submit" class="mp-btn primary" style="font-size:13px;padding:10px 16px;background:#0B266E;color:#FFFFFF;">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        @endif

        {{-- Content: Modul --}}
        <style>
    </style>
        <div style="display: flex; flex-direction: column; gap: 29px;">
            @forelse($modulList as $item)
                @php $modul = $item['modul']; @endphp
                <div x-data="{ open: false }"
                    style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">

                    {{-- Header (Clickable) --}}
                    <div class="mp-module-card-header" @click="open = !open"
                        style="padding: 16px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;"
                        onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                        <div>
                            <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">{{ $modul->nama }}
                            </h3>
                            <div style="font-size:13px; color:#666D80; font-weight:400; margin-bottom:4px;">
                                Asisten:
                                @if($item['asprak']->isNotEmpty())
                                    {{ $item['asprak']->join(', ') }}
                                @else
                                    -
                                @endif
                            </div>

                            @php
                                $firstMateri = $modul->materi->sortBy('created_at')->first();
                                $sudahAdaInteraksi = $firstMateri || ($modul->updated_at->timestamp - $modul->created_at->timestamp > 3);
                                $baseTime = $firstMateri ? $firstMateri->created_at : $modul->updated_at;
                            @endphp

                            @if($sudahAdaInteraksi)
                                <div style="font-size:12px; color:#6B7280; font-weight:400; margin-top:4px;">
                                    {{ \Carbon\Carbon::parse($baseTime)->locale('id')->translatedFormat('d M Y, H:i') }}
                                    @if($modul->updated_at->timestamp - $baseTime->timestamp > 3)
                                        <span style="font-style:italic;">(Diedit
                                            {{ \Carbon\Carbon::parse($modul->updated_at)->locale('id')->translatedFormat('d M Y, H:i') }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>

                    {{-- Expanded Content --}}
                    <x-eoffice::manajemen-praktikum.ui.grade-accordion state="open">
                        <div style="border-top: 1px solid var(--c-border); padding: 20px 24px 24px; background: #fff;">
                            @if($modul->materi->isEmpty())
                                <div style="font-size: 14px; color: #6B7280; font-style: italic;">Modul belum diunggah</div>
                            @else
                                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                    @foreach($modul->materi as $materi)
                                        <a rel="noopener noreferrer" href="{{ $materi->file_path ? app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($materi->file_path, 'eoffice', ($materi->judul ?? basename($materi->file_path))) : '#' }}"
                                            target="_blank"
                                            style="display:flex; flex-direction:column; width:140px; height:140px; border:1px solid #DFE1E7; border-radius:8px; overflow:hidden; text-decoration:none; background:#fff; transition:transform 0.15s, box-shadow 0.15s;"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">

                                            {{-- Top Icon Area --}}
                                            <div
                                                style="flex:1; display:flex; align-items:center; justify-content:center; background:#FAFAFA;">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB"
                                                    stroke-width="1.5" stroke-linecap="round">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                                    <polyline points="14 2 14 8 20 8" />
                                                </svg>
                                            </div>

                                            {{-- Bottom Name Area --}}
                                            <div style="background:#0B266E; padding:10px 12px; display:flex; align-items:center;">
                                                <span
                                                    style="color:#FFF; font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%;"
                                                    title="{{ $materi->judul ?? basename($materi->file_path ?? 'File') }}">
                                                    {{ $materi->judul ?? basename($materi->file_path ?? 'File') }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </x-eoffice::manajemen-praktikum.ui.grade-accordion>

                </div>
            @empty
                <div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
                    <div style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;" aria-hidden="true">
                            <path d="M12 6C10 4 7 3 4 3v15c3 0 6 1 8 3m0-15c2-2 5-3 8-3v15c-3 0-6 1-8 3V6" />
                            <path d="M4 5H2v16h20V5h-2" />
                        </svg>
                        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Modul</div>
                        <div style="font-size:12px;color:#666D80;">Belum ada modul yang dibuat pada praktikum ini.</div>
                    </div>
                </div>
            @endforelse
        </div>

    <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum ?? null" role="dosen" section="modul" />
        <x-eoffice::manajemen-praktikum.ui.module-history :riwayatPengajuan="$riwayatPengajuan" />

    @endif

</x-eoffice::manajemen-praktikum.layout>
