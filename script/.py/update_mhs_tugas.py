import re

path = r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\mahasiswa\tugas.blade.php"
with open(path, "r", encoding="utf-8") as f:
    content = f.read()

# We want to replace everything from <div class="sec-head"> to the end (before </x-eoffice...>)
# We will construct the new body

new_body = """
    {{-- CSS untuk Accordion --}}
    <style>
        .tugas-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }
        .tugas-accordion-content.is-open {
            max-height: 1200px; /* Increased for upload form */
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        @forelse($modulList as $item)
            @php 
                $modul = $item['modul']; 
                $tugasList = $item['tugas'];
            @endphp
            
            <div x-data="{ modulOpen: false }" style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
                
                {{-- Card Header: Modul Info --}}
                <div @click="modulOpen = !modulOpen" style="padding: 16px 24px; background: #fff; border-bottom: 1px solid var(--c-border); cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">{{ $modul->nama }}</h3>
                        <div style="font-size: 13px; color: #6B7280;">
                            Asisten: 
                            @if($item['asprak']->isNotEmpty())
                                {{ $item['asprak']->join(', ') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="modulOpen ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                {{-- List of Tugas as Accordions --}}
                <div class="modul-accordion-content" :class="{ 'is-open': modulOpen }">
                    <div style="display: flex; flex-direction: column;">
                    @php
                        $requiredTypes = [
                            'tugas_pendahuluan' => 'Tugas Pendahuluan',
                            'laporan'           => 'Laporan',
                            'responsi'          => 'Responsi',
                            'tugas_pengganti'   => 'Tugas Pengganti'
                        ];
                    @endphp

                    @foreach($requiredTypes as $type => $label)
                        @php
                            $t = $tugasList->firstWhere('jenis_tugas', $type);
                            
                            $dlAC  = $t && $t->deadline ? \Carbon\Carbon::parse($t->deadline) : null;
                            $dlACC = $t && $t->deadline_acc ? \Carbon\Carbon::parse($t->deadline_acc) : null;
                            
                            $lewat = $dlAC && now()->gt($dlAC);
                            $lewatMutlak = ($dlACC ?? $dlAC) && now()->gt($dlACC ?? $dlAC);
                            
                            $sisaAC = $dlAC ? now()->diffInDays($dlAC, false) : null;

                            $pengumpulan = $t ? $t->pengumpulan : null;
                            $sudahKumpul = !is_null($pengumpulan);
                            $statusTugas = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';

                            $isLate = false;
                            if ($sudahKumpul && $pengumpulan->riwayat) {
                                $firstSubmission = $pengumpulan->riwayat->sortBy('created_at')->first();
                                if ($firstSubmission && $dlAC && \Carbon\Carbon::parse($firstSubmission->created_at)->gt($dlAC)) {
                                    $isLate = true;
                                }
                            }

                            $oldFiles = [];
                            if ($t && $t->file_path) {
                                $oldFiles = json_decode($t->file_path, true) ?? [];
                                if (!is_array($oldFiles)) $oldFiles = [$t->file_path];
                            }
                        @endphp
                        
                        <div x-data="{ open: false, showUpload: false }" style="border-bottom: 1px solid var(--c-border);">
                            
                            {{-- Accordion Header --}}
                            <div @click="open = !open" style="padding: 14px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                    <div style="font-size: 14px; font-weight: 600; color: #0D0D12;">{{ $label }}</div>
                                    @if($t)
                                        @if($statusTugas === 'acc')
                                            <span class="mp-badge success sm"><span class="dot"></span>ACC{{ $pengumpulan?->nilai ? ' — Nilai: ' . $pengumpulan->nilai : '' }}</span>
                                        @elseif($statusTugas === 'revisi')
                                            <span class="mp-badge error sm"><span class="dot"></span>Perlu Revisi</span>
                                        @elseif($statusTugas === 'belum_dicek')
                                            <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Penilaian</span>
                                        @elseif($lewatMutlak)
                                            <span class="mp-badge error sm"><span class="dot"></span>Waktu Habis</span>
                                        @elseif($lewat)
                                            <span class="mp-badge warning sm"><span class="dot"></span>AC Terlewat</span>
                                        @elseif($sisaAC !== null && $sisaAC <= 2)
                                            <span class="mp-badge warning sm"><span class="dot"></span>Segera!</span>
                                        @else
                                            <span class="mp-badge neutral sm"><span class="dot"></span>Belum Dikumpul</span>
                                        @endif

                                        @if($isLate)
                                            <span class="mp-badge error sm" style="background:#FFF0F2;color:#DF1C41;border:1px solid #DF1C41;">Terlambat</span>
                                        @endif
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    @if($t)
                                    <div style="font-size: 12px; color: #666D80; display: flex; gap: 8px;">
                                        @if($dlAC && $dlACC)
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">AC: {{ $dlAC->format('d/m/Y, H:i') }} | ACC: {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                        @elseif($dlACC)
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">ACC: {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                        @elseif($dlAC)
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">AC: {{ $dlAC->format('d/m/Y, H:i') }}</span>
                                        @else
                                            <span style="color:#A4ABB8;">Tanpa batas waktu</span>
                                        @endif
                                    </div>
                                    @endif
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>

                            {{-- Accordion Body --}}
                            <div class="tugas-accordion-content" :class="{ 'is-open': open }">
                                <div style="border-top: 1px solid var(--c-border); padding: 16px 24px; background: #FAFBFC;">
                                    
                                    @if($t)
                                        @if($t->deskripsi)
                                            <div style="font-size: 13px; color: #374151; margin-bottom: 16px; line-height: 1.5; white-space: pre-wrap;">{{ $t->deskripsi }}</div>
                                        @endif
                                        
                                        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px;">
                                            @foreach($oldFiles as $idx => $f)
                                                @php 
                                                    $pathStr = is_array($f) && isset($f['path']) ? $f['path'] : $f;
                                                    $baseName = is_array($f) && isset($f['original_name']) ? $f['original_name'] : pathinfo($pathStr, PATHINFO_BASENAME);
                                                @endphp
                                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pathStr, 'eoffice') }}"
                                                    target="_blank" title="{{ $baseName }}"
                                                    style="display: flex; flex-direction: column; width: 120px; height: 120px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition:transform 0.15s, box-shadow 0.15s;"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                    <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                            stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                        </svg>
                                                    </div>
                                                    <div style="background: #293C79; color: #fff; padding: 8px 10px; font-size: 11px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $baseName }}">
                                                        {{ $baseName }}
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>

                                        {{-- File yang Dikumpulkan & Riwayat --}}
                                        @if($sudahKumpul && $pengumpulan?->file_path)
                                            <div class="mb-4 p-3 rounded-[8px]" style="background:#fff;border:1px solid #DFE1E7;">
                                                <div style="font-size:11px;font-weight:700;color:#353849;margin-bottom:6px;display:flex;align-items:center;justify-content:space-between;">
                                                    <span>File yang Dikumpulkan:</span>
                                                    <span style="font-size:10px;font-weight:500;color:#666D80;">Update: {{ \Carbon\Carbon::parse($pengumpulan->updated_at)->locale('id')->format('d M Y, H:i') }}</span>
                                                </div>

                                                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_path, 'eoffice') }}"
                                                        target="_blank"
                                                        style="font-size:12px;font-weight:700;color:#0B266E;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                            <polyline points="7 10 12 15 17 10" />
                                                            <line x1="12" y1="15" x2="12" y2="3" />
                                                        </svg>
                                                        Unduh File Terbaru
                                                    </a>

                                                    {{-- Dropdown Riwayat --}}
                                                    @if($pengumpulan->riwayat && $pengumpulan->riwayat->isNotEmpty())
                                                        <div x-data="{ openRiwayat: false }" style="position:relative;">
                                                            <button type="button" @click="openRiwayat = !openRiwayat" class="mp-btn secondary sm" style="font-size:11px;padding:4px 8px;display:inline-flex;align-items:center;gap:2px;">
                                                                Riwayat Berkas ({{ $pengumpulan->riwayat->count() }})
                                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                                    <path d="M6 9l6 6 6-6" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="openRiwayat" @click.away="openRiwayat = false" style="position:absolute;top:100%;right:0;background:#fff;border:1px solid #DFE1E7;border-radius:8px;padding:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:100;min-width:240px;display:flex;flex-direction:column;gap:6px;margin-top:4px;">
                                                                @foreach($pengumpulan->riwayat as $index => $r)
                                                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($r->file_path, 'eoffice') }}" target="_blank" style="font-size:11px;color:#353849;text-decoration:none;display:flex;flex-direction:column;padding:6px;border-radius:6px;transition:background .1s;text-align:left;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background=''">
                                                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                                                            <span style="font-weight:700;color:#0B266E;">#{{ $pengumpulan->riwayat->count() - $index }} {{ $r->is_revision ? 'Revisi' : 'Pertama' }}</span>
                                                                            <span style="font-size:9px;color:#888;">{{ $r->created_at->format('H:i') }}</span>
                                                                        </div>
                                                                        @if($r->catatan)
                                                                            <span style="font-size:10px;color:#666D80;margin-top:2px;font-style:italic;" class="truncate">💬 {{ $r->catatan }}</span>
                                                                        @endif
                                                                        <span style="font-size:9px;color:#A4ABB8;margin-top:2px;">{{ $r->created_at->locale('id')->format('d M Y') }}</span>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Catatan Revisi dari Asisten Praktikum --}}
                                        @if($sudahKumpul && ($pengumpulan?->catatan_revisi || $pengumpulan?->file_revisi_asprak))
                                            <div class="mb-4 p-3 rounded-[8px]" style="background:#FADAE1;border:1px solid #DF1C41;">
                                                <div style="font-size:11px;font-weight:700;color:#7C1028;margin-bottom:2px;">Catatan Revisi dari Asisten:</div>
                                                @if($pengumpulan->catatan_revisi)
                                                    <div style="font-size:12px;color:#7C1028;margin-bottom:6px;">{{ $pengumpulan->catatan_revisi }}</div>
                                                @endif
                                                @if($pengumpulan->file_revisi_asprak)
                                                    <div style="margin-top:6px;">
                                                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_revisi_asprak, 'eoffice') }}" target="_blank" style="font-size:11px;font-weight:700;color:#95122B;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                <polyline points="7 10 12 15 17 10" />
                                                                <line x1="12" y1="15" x2="12" y2="3" />
                                                            </svg>
                                                            Unduh File Lampiran Revisi
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Action & Upload Form --}}
                                        @if(isset($daftarPraktikan) && $daftarPraktikan?->praktikum?->is_active)
                                            @if((!$sudahKumpul && !$lewatMutlak) || ($statusTugas === 'revisi' && !$lewatMutlak))
                                                <button type="button" @click="showUpload = !showUpload" class="mp-btn primary sm" style="text-decoration: none; padding: 8px 16px; margin-bottom: 12px;">
                                                    {{ $statusTugas === 'revisi' ? 'Kirim Ulang' : 'Upload Pengumpulan' }}
                                                </button>

                                                <div x-show="showUpload" x-transition class="pt-4" style="border-top:1px solid #DFE1E7;">
                                                    @if($statusTugas === 'revisi')
                                                        <div style="font-size:12px;font-weight:600;color:#D39C3D;margin-bottom:12px;">Kirim ulang file perbaikan:</div>
                                                        <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kirim-ulang', $t->id) }}" enctype="multipart/form-data">
                                                            @csrf
                                                    @else
                                                        <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kumpul', $t->id) }}" enctype="multipart/form-data">
                                                            @csrf
                                                    @endif
                                                            <div class="flex flex-col gap-3">
                                                                <div>
                                                                    <label class="block mb-1" style="font-size:12px;font-weight:600;color:#353849;">File Tugas <span style="color:#DF1C41;">*</span></label>
                                                                    <input type="file" name="file" required class="mp-input w-full">
                                                                    <div style="font-size:11px;color:#666D80;margin-top:4px;">Format: PDF, DOCX, ZIP, RAR (maks. 10MB)</div>
                                                                </div>
                                                                <div>
                                                                    <label class="block mb-1" style="font-size:12px;font-weight:600;color:#353849;">Catatan (opsional)</label>
                                                                    <textarea name="catatan" rows="2" placeholder="Catatan untuk asisten..." class="mp-input w-full" style="resize:none;"></textarea>
                                                                </div>
                                                                <div class="flex gap-2">
                                                                    <button type="button" @click="showUpload = false" class="mp-btn secondary md">Batal</button>
                                                                    <button type="submit" class="mp-btn primary md">
                                                                        {{ $statusTugas === 'revisi' ? 'Kirim Perbaikan' : 'Kumpulkan' }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                </div>
                                            @endif
                                        @endif
                                        
                                    @else
                                        <div style="font-size: 13px; color: #6B7280; font-style: italic;">{{ $label }} belum diunggah.</div>
                                    @endif

                                </div>
                            </div>

                        </div>
                    @endforeach
                    </div>
                </div>

            </div>
        @empty
            <div style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Modul & Tugas</div>
                <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Belum ada data yang dapat ditampilkan.</div>
            </div>
        @endforelse
    </div>

</x-eoffice::manajemen-praktikum.layout>
"""

import sys
head_content = content[:content.find('<div class="sec-head">')]
if '<div class="sec-head">' not in content:
    print("Could not find <div class='sec-head'>")
    sys.exit(1)

with open(path, "w", encoding="utf-8") as f:
    f.write(head_content + new_body)

print("Updated mahasiswa/tugas.blade.php successfully")
