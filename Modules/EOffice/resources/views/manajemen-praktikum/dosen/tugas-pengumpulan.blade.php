<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $tugas->modul?->praktikum?->nama ? $tugas->modul->praktikum->nama . ' / Tugas' : 'Tugas' }}">

    @php
        $praktikum = $tugas->modul?->praktikum;
    @endphp

    {{-- Sticky Header Wrapper --}}
    <div x-data="{ st: 0 }"
         x-init="
            const box = document.querySelector('.mp-box-body');
            if (box) {
                let ticking = false;
                box.addEventListener('scroll', () => {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            st = box.scrollTop;
                            ticking = false;
                        });
                        ticking = true;
                    }
                });
            }
         "
         class="sticky z-20 bg-white" style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border);">
         
        {{-- Banner / Cover Image Container --}}
        <div style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">
            <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896" 
                     :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>
            <div class="absolute inset-0 pointer-events-none" style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);"></div>
            <div class="absolute inset-0 pointer-events-none" :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`"></div>
            <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
                <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                    :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                    {{ $praktikum?->nama ?? 'Praktikum' }}
                </h1>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display: flex; gap: 8px;">
            
            <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
            <a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Tugas</a>
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi dan Nilai</a>
            <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
            <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi Koordinator</a>
        </div>
    </div>

    {{-- Content Area --}}
    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 24px;">
        <div style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
            
            {{-- Card Header: Modul Info --}}
            <div style="padding: 16px 24px; background: #fff; border-bottom: 1px solid var(--c-border); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">Modul {{ $tugas->modul?->urutan }} {{ $tugas->modul?->judul ?? 'Modul' }}</h3>
                    <div style="font-size: 13px; color: #6B7280;">
                        Asisten Praktikum: 
                        @php
                            $aspraks = $tugas->modul?->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values() ?? collect();
                        @endphp
                        {{ $aspraks->isNotEmpty() ? $aspraks->join(', ') : '-' }}
                    </div>
                </div>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(180deg);">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            {{-- Accordion for Tugas --}}
            <div style="border-bottom: 1px solid var(--c-border);">
                
                {{-- Accordion Header --}}
                <div style="padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; background: #F9FAFB;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="font-size: 14px; font-weight: 600; color: #0D0D12;">
                            @php
                                $label = match($tugas->jenis_tugas) {
                                    'tugas_pendahuluan' => 'Tugas Pendahuluan',
                                    'laporan'           => 'Laporan',
                                    'responsi'          => 'Responsi',
                                    'tugas_pengganti'   => 'Tugas Pengganti',
                                    default             => 'Tugas'
                                };
                                $dl = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
                                $mulai = $tugas->created_at;
                                $lewat = $dl && now()->gt($dl);
                            @endphp
                            {{ $label }}
                        </div>
                        @if($lewat)
                            <span class="mp-badge neutral sm" style="font-size: 10px;">Berakhir</span>
                        @else
                            <span class="mp-badge success sm" style="font-size: 10px;"><span class="dot"></span>Aktif</span>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="font-size: 12px; color: #666D80; display: flex; gap: 8px;">
                            @if($dl && $mulai)
                                <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Batas pengumpulan: {{ $mulai->format('d/m/Y, H:i') }} – {{ $dl->format('d/m/Y, H:i') }}</span>
                            @elseif($dl)
                                <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Batas pengumpulan: {{ $dl->format('d/m/Y, H:i') }}</span>
                            @else
                                <span style="color:#A4ABB8;">Tanpa batas waktu</span>
                            @endif
                        </div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(180deg);">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </div>

                {{-- Accordion Body (Details) --}}
                <div style="border-top: 1px solid var(--c-border); padding: 16px 24px; background: #FAFBFC;">
                    @if($tugas->deskripsi)
                        <div style="font-size: 13px; color: #374151; margin-bottom: 16px; line-height: 1.5; white-space: pre-wrap;">{{ $tugas->deskripsi }}</div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
                        <div>
                            @if($tugas->file_path)
                            <a href="{{ Storage::url($tugas->file_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 10px; border: 1px solid var(--c-border); border-radius: 8px; padding: 10px 16px; text-decoration: none; color: #111827; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                    <polyline points="13 2 13 9 20 9"></polyline>
                                </svg>
                                <span style="font-size: 13px; font-weight: 500;">Lampiran Soal</span>
                            </a>
                            @endif
                        </div>
                        <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}" class="mp-btn neutral md" style="background: #111827; color: #fff; border: none; font-weight: 600; text-decoration: none; padding: 8px 24px;">
                            Tutup
                        </a>
                    </div>
                </div>

                {{-- Table Pengumpulan --}}
        @php
            $shiftRowspan = [];
            $kelompokRowspan = [];
            
            $items = $praktikans->all();
            $totalItems = count($items);

            // Calculate rowspan for shift
            $i = 0;
            while ($i < $totalItems) {
                $val = $items[$i]->shift;
                if (empty($val)) {
                    $shiftRowspan[$i] = 1;
                    $i++;
                    continue;
                }
                $count = 1;
                while ($i + $count < $totalItems && $items[$i + $count]->shift === $val) {
                    $count++;
                }
                $shiftRowspan[$i] = $count;
                for ($j = 1; $j < $count; $j++) {
                    $shiftRowspan[$i + $j] = 0;
                }
                $i += $count;
            }

            // Calculate rowspan for kelompok (must match same kelompok AND same shift)
            $i = 0;
            while ($i < $totalItems) {
                $valK = $items[$i]->kelompok;
                $valS = $items[$i]->shift;
                if (empty($valK)) {
                    $kelompokRowspan[$i] = 1;
                    $i++;
                    continue;
                }
                $count = 1;
                while (
                    $i + $count < $totalItems && 
                    $items[$i + $count]->kelompok === $valK && 
                    $items[$i + $count]->shift === $valS
                ) {
                    $count++;
                }
                $kelompokRowspan[$i] = $count;
                for ($j = 1; $j < $count; $j++) {
                    $kelompokRowspan[$i + $j] = 0;
                }
                $i += $count;
            }
        @endphp

        <div style="overflow-x:auto; background: #fff; border-top: 1px solid var(--c-border);">
            <table class="w-full" style="min-width:1250px; font-size:13px; text-align:left; white-space: nowrap; font-family: 'Inter', sans-serif;">
                <thead style="background:#F9FAFB; border-bottom:1px solid #DFE1E7; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #666D80;">
                    <tr>
                        <th class="mp-th text-left" style="padding:10px 16px;width:40px;">NO</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:200px;">Nama Mahasiswa</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:120px;">NIM</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:100px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:100px;border-right:1px solid #DFE1E7;">Shift</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:150px;">Dokumen</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:140px;">Waktu Submit</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:110px;">Nilai</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:90px;">Status</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:150px;">Kirim Revisi</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:150px;">Dokumen Revisi</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:140px;">Waktu Submit Revisi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($praktikans as $idx => $pr)
                    @php
                        $p = $pengumpulan[$pr->id] ?? null;
                        $st = $p ? ($p->status_pengumpulan ?? 'belum_dicek') : 'belum_kumpul';
                        $waktuSubmit = null;
                        $latestRevision = null;
                        if ($p) {
                            $firstSub = $p->riwayat->where('is_revision', false)->sortBy('created_at')->first() ?? $p->riwayat->sortBy('created_at')->first();
                            $waktuSubmit = $firstSub ? $firstSub->created_at : $p->created_at;
                            $latestRevision = $p->riwayat->where('is_revision', true)->sortByDesc('created_at')->first();
                        }
                    @endphp
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                        <td style="padding:12px 16px;vertical-align:middle;color:#808897;font-size:12px;">{{ $idx + 1 }}</td>
                        
                        {{-- Mahasiswa --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                <div class="mp-av yellow" style="width:34px;height:34px;flex-shrink:0;">{{ strtoupper(substr($pr->user?->name ?? 'M', 0, 2)) }}</div>
                                <div style="min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $pr->user?->name ?? '—' }}</div>
                                    <div style="font-size:11px;color:#666D80;">{{ $pr->user?->email }}</div>
                                    @if($p && $p->catatan)
                                    <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="{{ $p->catatan }}">💬 Mhs: {{ Str::limit($p->catatan, 20) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- NIM --}}
                        <td style="padding:12px 16px;vertical-align:middle;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                            {{ $pr->user?->student?->student_number ?? '-' }}
                        </td>

                        {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                        @if($kelompokRowspan[$idx] > 0)
                            <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:16px;font-weight:700;color:#0D0D12;">
                                @if($pr->kelompok)
                                    {{ $pr->kelompok }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                @endif
                            </td>
                        @endif
                        
                        {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                        @if($shiftRowspan[$idx] > 0)
                            <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:16px;font-weight:700;color:#0D0D12;">
                                @if($pr->shift)
                                    {{ $pr->shift }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                @endif
                            </td>
                        @endif

                        {{-- Dokumen --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @if($p && $firstSub && $firstSub->file_path)
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($firstSub->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="{{ basename($firstSub->file_path) }}">
                                    {{ Str::limit(basename($firstSub->file_path), 15) }}
                                </a>
                                @if($firstSub->catatan)
                                <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="{{ $firstSub->catatan }}">💬 Mhs: {{ Str::limit($firstSub->catatan, 20) }}</div>
                            @endif
                        </div>
                            @elseif($p && $p->file_path)
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="{{ basename($p->file_path) }}">
                                    {{ Str::limit(basename($p->file_path), 15) }}
                                </a>
                            </div>
                            @else
                            <span style="font-size:12px;color:#A4ABB8;">Belum mengumpulkan</span>
                            @endif
                        </td>

                        {{-- Waktu Submit --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @if($waktuSubmit)
                            <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                {{ \Carbon\Carbon::parse($waktuSubmit)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }} WIB
                            </span>
                            @else
                            <span style="font-size:12px;color:#999;">—</span>
                            @endif
                        </td>

                        {{-- Nilai (Read Only) --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @php
                                $displayNilai = $p ? $p->nilai : null;
                            @endphp
                            <span style="font-size:12px;font-weight:600;">{{ $displayNilai !== null ? $displayNilai : '—' }}</span>
                        </td>

                        {{-- Status --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @if($st === 'acc')
                            <span class="mp-badge success sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>ACC</span>
                            @elseif($st === 'revisi')
                            <span class="mp-badge error sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Revisi</span>
                            @elseif($st === 'belum_kumpul')
                            <span class="mp-badge neutral sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Belum Kumpul</span>
                            @else
                            <span class="mp-badge warning sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Menunggu</span>
                            @endif
                        </td>

                        {{-- Kirim Revisi (Read Only) --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @if($p && ($p->file_revisi_asprak || $p->catatan_revisi))
                            <div style="padding:6px 8px;background:#FEF2F2;border:1px solid #FEE2E2;border-radius:6px;font-size:11px;width:100%;box-sizing:border-box;">
                                @if($p->file_revisi_asprak)
                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_revisi_asprak, 'eoffice') }}" target="_blank" style="font-weight:600;color:#95122B;text-decoration:none;display:block;word-break:break-all;" title="{{ basename($p->file_revisi_asprak) }}">
                                    📄 {{ Str::limit(basename($p->file_revisi_asprak), 12) }}
                                </a>
                                @endif
                                @if($p->catatan_revisi)
                                <div style="color:#7C1028;font-style:italic;margin-top:2px;word-break:break-word;" title="{{ $p->catatan_revisi }}">💬: {{ Str::limit($p->catatan_revisi, 25) }}</div>
                                @endif
                            </div>
                            @else
                            <span style="font-size:12px;color:#999;">—</span>
                            @endif
                        </td>

                        {{-- Dokumen Revisi --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @if($latestRevision && $latestRevision->file_path)
                            <div style="display:flex;flex-direction:column;gap:2px;">
                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($latestRevision->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0F6E56;text-decoration:none;" title="{{ basename($latestRevision->file_path) }}">
                                    {{ Str::limit(basename($latestRevision->file_path), 15) }}
                                </a>
                                @if($latestRevision->catatan)
                                <div style="font-size:10px;color:#353849;font-style:italic;" title="{{ $latestRevision->catatan }}">💬 Mhs: {{ Str::limit($latestRevision->catatan, 25) }}</div>
                                @endif
                            </div>
                            @else
                            <span style="font-size:12px;color:#999;">—</span>
                            @endif
                        </td>

                        {{-- Waktu Submit Revisi --}}
                        <td style="padding:12px 16px;vertical-align:middle;">
                            @if($latestRevision)
                            <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                {{ \Carbon\Carbon::parse($latestRevision->created_at)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }} WIB
                            </span>
                            @else
                            <span style="font-size:12px;color:#999;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="padding:48px;text-align:center;color:#666D80;">
                            Belum ada praktikan terdaftar.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
            </div>
        </div>
    </div>

</x-eoffice::manajemen-praktikum.layout>