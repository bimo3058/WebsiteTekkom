@php
    $pageTitle = ($praktikum?->nama ?? 'Praktikum') . ' / Pengumuman';
@endphp
<x-eoffice::manajemen-praktikum.layout :pageTitle="$pageTitle">

    @if($praktikumList->isEmpty())
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Pengumuman</h1>
            </div>
        </div>
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    @else
        <x-eoffice::manajemen-praktikum.mhs-header :praktikum="$praktikum" />

        @if($praktikum)

            {{-- Daftar Pengumuman --}}
            <div style="display:flex; flex-direction:column; gap:16px;">
                @forelse($pengumumans as $pg)
                    @php
                        $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                        $initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                        $avColors = ['sky', 'navy', 'green', 'yellow', 'violet'];
                        $avColor = $avColors[crc32($pg->user?->email ?? '') % count($avColors)];
                    @endphp
                    <div style="border:1px solid #DFE1E7; background:#fff; border-radius:14px; padding:15px 24px; position:relative; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition:all 0.2s ease;"
                        onmouseover="this.style.boxShadow='0 4px 14px rgba(17,24,39,0.05)';"
                        onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">

                        {{-- Pengarang --}}
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                {{-- Avatar berwarna dengan inisial --}}
                                <div class="mp-av lg {{ $avColor }}" style="flex-shrink:0;">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:14px; color:#111827;">
                                        {{ $pg->user?->name ?? '—' }}
                                    </div>
                                    <div style="font-size:12px; color:#6B7280;">
                                        {{ $pg->created_at?->format('d M Y, H:i') ?? '-' }}
                                        @if($pg->updated_at && $pg->updated_at->gt($pg->created_at))
                                            <span style="font-style:italic; margin-left:4px;">(Diedit
                                                {{ $pg->updated_at->format('d M Y, H:i') }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Konten --}}
                        <div style="margin:10px 0 0 0;">
                            <h3 style="font-size:15px; font-weight:700; color:#111827; margin:0 0 4px 0; line-height:1.4; padding:0;">
                                {{ $pg->judul }}
                            </h3>
                            <p style="font-size:13px; color:#374151; margin:0; padding:0; line-height:1.6; white-space:pre-line;">{{ $pg->konten }}</p>
                        </div>

                        {{-- Lampiran --}}
                        @if(!empty($pg->lampiran))
                            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
                                @foreach($pg->lampiran as $lamp)
                                    @php
                                        $pfPath = isset($lamp['path']) ? $lamp['path'] : $lamp;
                                        $pfName = isset($lamp['name']) ? $lamp['name'] : basename($pfPath);
                                    @endphp
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pfPath, 'eoffice') }}"
                                        target="_blank" title="{{ $pfName }}"
                                        style="display: flex; flex-direction: column; width: 140px; height: 140px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition:transform 0.15s, box-shadow 0.15s;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                <polyline points="13 2 13 9 20 9"></polyline>
                                            </svg>
                                        </div>
                                        <div style="background: #293C79; color: #fff; padding: 10px 12px; font-size: 13px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                            {{ $pfName }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="mp-card flex-shrink-0" style="min-height:180px;display:flex;align-items:center;justify-content:center;">
                        <div style="padding:36px;text-align:center;">
                            <div style="width:48px;height:48px;border-radius:12px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                    stroke-linecap="round">
                                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                                </svg>
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Pengumuman</div>
                            <div style="font-size:12px;color:#666D80;">Belum ada pengumuman yang diterbitkan pada praktikum ini.</div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengumumans->hasPages())
                <div class="flex-shrink-0" style="padding:8px 0;">{{ $pengumumans->links() }}</div>
            @endif

        @else
            <div class="mp-alert info flex-shrink-0">Silakan pilih praktikum terlebih dahulu untuk melihat pengumuman.</div>
        @endif

    @endif

</x-eoffice::manajemen-praktikum.layout>