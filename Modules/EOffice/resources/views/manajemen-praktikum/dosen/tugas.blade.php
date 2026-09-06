<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama . ' / Tugas' : 'Tugas' }}">

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
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 14px;display:block;">
                <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
            </svg>
            <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Pilih Praktikum</div>
            <div style="font-size:13px;color:#666D80;margin-bottom:24px;">Pilih praktikum yang ingin dilihat tugasnya.</div>

            @if($praktikumList->isEmpty())
            <div style="font-size:13px;color:#808897;">Kamu belum mengampu praktikum apapun.</div>
            @else
            <div class="flex flex-col gap-2 max-w-sm mx-auto">
                @foreach($praktikumList as $p)
                <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $p->id]) }}"
                   class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]" style="text-decoration:none;border:1px solid #DFE1E7;">
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
            
            {{-- Cover Placeholder or Image --}}
            <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896" 
                     :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>

            {{-- 1. Base Gradient --}}
            <div class="absolute inset-0 pointer-events-none"
                 style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);">
            </div>

            {{-- 2. Scrolled Overlay (Gelap + Blur) --}}
            <div class="absolute inset-0 pointer-events-none"
                 :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
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
        <div style="display: flex; gap: 8px;">
            
            <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
            <a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Tugas</a>
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi dan Nilai</a>
            <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
            <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi Koordinator</a>
        </div>
    </div>

    {{-- Content: Tugas per Modul --}}
    <style>
        .modul-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }
        .modul-accordion-content.is-open {
            max-height: 1500px;
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
        .tugas-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }
        .tugas-accordion-content.is-open {
            max-height: 600px;
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
                            // Cari tugas berdasarkan jenis_tugas
                            $t = $tugasList->firstWhere('jenis_tugas', $type);
                            
                            $dlAC  = $t && $t->deadline ? \Carbon\Carbon::parse($t->deadline) : null;
                            $dlACC = $t && $t->deadline_acc ? \Carbon\Carbon::parse($t->deadline_acc) : null;
                            $lewat = $dlACC && now()->gt($dlACC);
                            $oldFiles = [];
                            if ($t && $t->file_path) {
                                $oldFiles = json_decode($t->file_path, true) ?? [];
                                if (!is_array($oldFiles))
                                    $oldFiles = [$t->file_path];
                            }
                        @endphp
                        
                        <div x-data="{ open: false }" style="border-bottom: 1px solid var(--c-border);">
                            
                            {{-- Accordion Header --}}
                            <div @click="open = !open" style="padding: 14px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="font-size: 14px; font-weight: 600; color: #0D0D12;">{{ $label }}</div>
                                    @if($t)
                                        @if($lewat)
                                            <span class="mp-badge neutral sm" style="font-size: 10px;">Berakhir</span>
                                        @else
                                            <span class="mp-badge success sm" style="font-size: 10px;"><span class="dot"></span>Aktif</span>
                                        @endif
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    @if($t)
                                    <div style="font-size: 12px; color: #666D80; display: flex; gap: 8px;">
                                        @if($dlAC && $dlACC)
                                            <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline AC: {{ $dlAC->format('d/m/Y, H:i') }} | ACC: {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                        @elseif($dlACC)
                                            <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline ACC: {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                        @elseif($dlAC)
                                            <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline AC: {{ $dlAC->format('d/m/Y, H:i') }}</span>
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
                                        
                                        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
                                            
                                            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                                                @foreach($oldFiles as $idx => $f)
                                                    @php 
                                                        $pathStr = is_array($f) && isset($f['path']) ? $f['path'] : $f;
                                                        $baseName = is_array($f) && isset($f['original_name']) ? $f['original_name'] : pathinfo($pathStr, PATHINFO_BASENAME);
                                                    @endphp
                                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pathStr, 'eoffice') }}"
                                                        target="_blank" title="{{ $baseName }}"
                                                        style="display: flex; flex-direction: column; width: 140px; height: 140px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition:transform 0.15s, box-shadow 0.15s;"
                                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                        <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                                                stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                <polyline points="13 2 13 9 20 9"></polyline>
                                                            </svg>
                                                        </div>
                                                        <div style="background: #293C79; color: #fff; padding: 10px 12px; font-size: 13px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $baseName }}">
                                                            {{ $baseName }}
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>

                                            {{-- Button Lihat Pengumpulan --}}
                                            <a href="{{ route('eoffice.manprak.dosen.tugas.pengumpulan', $t->id) }}" class="mp-btn primary sm" style="text-decoration: none; padding: 8px 16px;">
                                                Lihat Pengumpulan
                                            </a>
                                            
                                        </div>
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

@endif

</x-eoffice::manajemen-praktikum.layout>