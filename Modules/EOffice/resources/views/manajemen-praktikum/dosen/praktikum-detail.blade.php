<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum->nama }} / Pengumuman">
    <x-eoffice::manajemen-praktikum.dosen-header :praktikum="$praktikum" />

    {{-- Content: Pengumuman --}}
    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        @forelse($pengumumans as $pg)
            @php
                $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                $initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                $avColors = ['sky', 'navy', 'green', 'yellow', 'violet'];
                $avColor = $avColors[crc32($pg->user?->email ?? '') % count($avColors)];
            @endphp
            <div class="mp-card flex-shrink-0"
                onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
                onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
                <div style="padding:20px;">
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        {{-- Avatar --}}
                        <div class="mp-av {{ $avColor }}" style="flex-shrink:0;margin-top:2px;">
                            {{ $initials }}
                        </div>

                        <div style="flex:1;min-width:0;">
                            {{-- Meta: Nama + Tanggal --}}
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                                <span style="font-size:13px;font-weight:700;color:#111827;">{{ $pg->user?->name ?? '—' }}</span>
                                <span style="font-size:11px;color:#9CA3AF;">
                                    {{ $pg->created_at?->format('d M Y, H:i') ?? '-' }}
                                    @if($pg->updated_at && $pg->updated_at->gt($pg->created_at))
                                        <span style="font-style:italic; margin-left:4px;">(Diedit {{ $pg->updated_at->format('d M Y, H:i') }})</span>
                                    @endif
                                </span>
                            </div>

                            {{-- Judul --}}
                            <h3 style="font-size:15px; font-weight:700; color:#111827; margin:0 0 4px 0; line-height:1.4; padding:0;">
                                {{ $pg->judul }}
                            </h3>

                            {{-- Konten --}}
                            <p style="font-size:13px; color:#374151; margin:0; padding:0; line-height:1.6; white-space:pre-line;">{{ $pg->konten }}</p>

                            {{-- Lampiran --}}
                            @if(!empty($pg->lampiran))
                                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
                                    @foreach($pg->lampiran as $lamp)
                                        @php
                                            $pfPath = isset($lamp['path']) ? $lamp['path'] : $lamp;
                                            $pfName = isset($lamp['name']) ? $lamp['name'] : basename($pfPath);
                                        @endphp
                                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pfPath, 'eoffice') }}" target="_blank"
                                            title="{{ $pfName }}"
                                            style="display:flex; flex-direction:column; width:140px; height:140px; border:1px solid #DFE1E7; border-radius:8px; overflow:hidden; text-decoration:none; background:#fff; transition:transform 0.15s, box-shadow 0.15s;"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            <div style="flex:1; display:flex; align-items:center; justify-content:center; background:#F9FAFB;">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                </svg>
                                            </div>
                                            <div style="background:#293C79; color:#fff; padding:10px 12px; font-size:12px; font-weight:600; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"
                                                title="{{ $pfName }}">
                                                {{ $pfName }}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
                <div style="padding:48px;text-align:center;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                        stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                    </svg>
                    <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Pengumuman</div>
                    <div style="font-size:12px;color:#666D80;">Belum ada pengumuman yang diterbitkan pada praktikum ini.</div>
                </div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($pengumumans->hasPages())
            <div style="padding: 8px 0;">
                {{ $pengumumans->links() }}
            </div>
        @endif
    </div>

</x-eoffice::manajemen-praktikum.layout>