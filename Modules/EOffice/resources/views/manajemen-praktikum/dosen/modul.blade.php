<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama . ' / Modul' : 'Modul' }}">

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
                                    st = box.scrollTop;
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
                             :style="`transform: scale(${Math.max(1, 1 + (st / 200) * 0.1)}); opacity: ${Math.max(0.3, 1 - (st / 300))}; filter: blur(${Math.min(8, st / 15)}px);`"
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
                    style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 30%, rgba(0,0,0,0) 70%);">
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

        {{-- Content: Modul --}}
        <style>
            .modul-accordion-content {
                max-height: 0;
                opacity: 0;
                overflow: hidden;
                transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
            }

            .modul-accordion-content.is-open {
                max-height: 600px;
                opacity: 1;
                transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
            }
        </style>
        <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
            @forelse($modulList as $item)
                @php $modul = $item['modul']; @endphp
                <div x-data="{ open: false }"
                    style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">

                    {{-- Header (Clickable) --}}
                    <div @click="open = !open"
                        style="padding: 16px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;"
                        onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                        <div>
                            <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">{{ $modul->nama }}
                            </h3>
                            <div style="font-size: 13px; color: #6B7280;">
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
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>

                    {{-- Expanded Content --}}
                    <div class="modul-accordion-content" :class="{ 'is-open': open }">
                        <div style="border-top: 1px solid var(--c-border); padding: 20px 24px 24px; background: #fff;">
                            @if($modul->materi->isEmpty())
                                <div style="font-size: 14px; color: #6B7280; font-style: italic;">Modul belum diunggah</div>
                            @else
                                @if($modul->deskripsi)
                                    <div
                                        style="font-size: 14px; color: #374151; line-height: 1.5; text-align: left; margin: 0 0 20px 0;">
                                        {!! nl2br(e(trim($modul->deskripsi))) !!}
                                    </div>
                                @endif

                                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                    @foreach($modul->materi as $materi)
                                        <a href="{{ $materi->file_path ? app(\App\Services\SupabaseStorage::class)->publicUrl($materi->file_path, 'eoffice') : '#' }}"
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
                                            <div style="background:#293C79; padding:10px 12px; display:flex; align-items:center;">
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
                    </div>

                </div>
            @empty
                <div
                    style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                    <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Modul</div>
                    <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Belum ada modul yang dibuat pada praktikum
                        ini.</div>
                </div>
            @endforelse
        </div>

    @endif

</x-eoffice::manajemen-praktikum.layout>