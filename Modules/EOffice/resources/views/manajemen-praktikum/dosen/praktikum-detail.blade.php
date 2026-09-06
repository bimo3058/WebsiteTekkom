<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum->nama }} / Pengumuman">
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
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896"
                    :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                    <path
                        d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                </svg>
            </div>

            {{-- 1. Base Gradient (Tampil penuh, tidak berubah) --}}
            <div class="absolute inset-0 pointer-events-none"
                style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);">
            </div>

            {{-- 2. Scrolled Overlay (Gelap + Blur) - Transparansi memudar seiring scroll --}}
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

            <a href="#"
                style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Pengumuman</a>
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}"
                style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
            <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}"
                style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Tugas</a>
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}"
                style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi
                dan Nilai</a>
            <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}"
                style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
            <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}"
                style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi
                Koordinator</a>
        </div>
    </div>

    {{-- Content: Pengumuman --}}
    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        @forelse($pengumumans as $pg)
            @php
                $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                $initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                $isSistem = $pg->tipe_sistem !== null;
            @endphp
            <div
                style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; padding: 16px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">

                {{-- User Info & Timestamp --}}
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div
                        style="width: 32px; height: 32px; border-radius: 50%; background: #D1D5DB; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #4B5563; font-size: 12px;">
                        @if($isSistem)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                            </svg>
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: #111827;">
                            {{ $isSistem ? 'Sistem' : ($pg->user?->name ?? '—') }}
                        </div>
                        <div style="font-size: 11px; color: #6B7280; margin-top: 1px;">
                            {{ $pg->created_at?->format('d M Y, H:i') ?? '-' }}
                            @if($pg->updated_at && $pg->updated_at->gt($pg->created_at))
                                <span style="font-style:italic; margin-left:4px; color:#A4ABB8;">(Diedit
                                    {{ $pg->updated_at->format('d M Y, H:i') }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Heading & Body --}}
                <h3 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 3px 0;">{{ $pg->judul }}</h3>
                <div style="font-size: 13px; color: #374151; line-height: 1.5;">{{ $pg->konten }}
                </div>

                {{-- Lampiran --}}
                @if(!empty($pg->lampiran))
                    <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:16px;">
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
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
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
        @empty
            <div
                style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Pengumuman</div>
                <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Belum ada pengumuman yang diterbitkan pada
                    praktikum ini.</div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($pengumumans->hasPages())
            <div style="padding: 16px 0;">
                {{ $pengumumans->links() }}
            </div>
        @endif
    </div>

</x-eoffice::manajemen-praktikum.layout>