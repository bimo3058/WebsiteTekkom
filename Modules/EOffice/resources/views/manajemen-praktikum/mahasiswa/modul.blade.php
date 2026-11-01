<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$terdaftarDi ?? null" pageTitle="Modul">

    @if(!$terdaftarDi)
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Daftar Modul</h1>
            </div>
        </div>
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    @else
        <x-eoffice::manajemen-praktikum.mhs-header :praktikum="$terdaftarDi" />
    @endif

    @if($modulList->isEmpty())

        <div class="mp-card flex-shrink-0">
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;" aria-hidden="true">
                    <path d="M12 6C10 4 7 3 4 3v15c3 0 6 1 8 3m0-15c2-2 5-3 8-3v15c-3 0-6 1-8 3V6" />
                    <path d="M4 5H2v16h20V5h-2" />
                </svg>
                <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Modul</div>
                <div style="font-size:12px;color:#666D80;">Belum ada modul yang tersedia untuk praktikum
                    ini.</div>
            </div>
        </div>

    @else

        {{-- Content: Modul --}}
        <style>
    </style>
        <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
            @foreach($modulList as $modul)
                @php 
                    $asprakList = $modul->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values();
                @endphp
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
                                @if($asprakList->isNotEmpty())
                                    {{ $asprakList->join(', ') }}
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
                            @if($modul->deskripsi)
                                <div
                                    style="font-size: 14px; color: #374151; line-height: 1.5; text-align: left; margin: 0 0 20px 0;">
                                    {!! nl2br(e(trim($modul->deskripsi))) !!}
                                </div>
                            @endif
                            
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
            @endforeach
        </div>

    @endif

    <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$terdaftarDi ?? null" role="mahasiswa" section="modul" />
</x-eoffice::manajemen-praktikum.layout>
