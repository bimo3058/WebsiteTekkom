@props(['praktikum'])

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
     " class="sticky z-20 bg-white transition-all duration-300"
    style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border); margin-bottom: 16px;">

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
            style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);">
        </div>

        {{-- 2. Scrolled Overlay --}}
        <div class="absolute inset-0 pointer-events-none"
            :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
        </div>

        {{-- Title --}}
        <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
            <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                {{ $praktikum->nama ?? 'Praktikum' }}
            </h1>
        </div>
    </div>

    @php
        $currentRoute = request()->route()?->getName();
        $isPengumuman = $currentRoute == 'eoffice.manprak.dosen.praktikum.show';
        $isModul = $currentRoute == 'eoffice.manprak.dosen.modul.index' || str_contains($currentRoute, 'dosen.modul.');
        $isTugas = $currentRoute == 'eoffice.manprak.dosen.tugas.index' || str_contains($currentRoute, 'dosen.tugas.');
        $isNilai = $currentRoute == 'eoffice.manprak.dosen.nilai.index';
        $isAnggota = str_contains($currentRoute, 'dosen.asprak.');
        $isSeleksi = $currentRoute == 'eoffice.manprak.dosen.pendaftaran-koor.index';
    @endphp

    {{-- Tabs --}}
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}"
            style="padding: 12px 24px; font-weight: {{ $isPengumuman ? '600' : '500' }}; font-size: 14px; color: {{ $isPengumuman ? '#293C79' : 'var(--c-fg-muted)' }}; text-decoration: none; {!! $isPengumuman ? 'border-bottom: 2px solid #293C79;' : '' !!}">Pengumuman</a>
        
        <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}"
            style="padding: 12px 24px; font-weight: {{ $isModul ? '600' : '500' }}; font-size: 14px; color: {{ $isModul ? '#293C79' : 'var(--c-fg-muted)' }}; text-decoration: none; {!! $isModul ? 'border-bottom: 2px solid #293C79;' : '' !!}">Modul</a>
        
        <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}"
            style="padding: 12px 24px; font-weight: {{ $isTugas ? '600' : '500' }}; font-size: 14px; color: {{ $isTugas ? '#293C79' : 'var(--c-fg-muted)' }}; text-decoration: none; {!! $isTugas ? 'border-bottom: 2px solid #293C79;' : '' !!}">Tugas</a>
        
        <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}"
            style="padding: 12px 24px; font-weight: {{ $isNilai ? '600' : '500' }}; font-size: 14px; color: {{ $isNilai ? '#293C79' : 'var(--c-fg-muted)' }}; text-decoration: none; {!! $isNilai ? 'border-bottom: 2px solid #293C79;' : '' !!}">Absensi & Nilai</a>
        
        <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}"
            style="padding: 12px 24px; font-weight: {{ $isAnggota ? '600' : '500' }}; font-size: 14px; color: {{ $isAnggota ? '#293C79' : 'var(--c-fg-muted)' }}; text-decoration: none; {!! $isAnggota ? 'border-bottom: 2px solid #293C79;' : '' !!}">Anggota</a>
        
        <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}"
            style="padding: 12px 24px; font-weight: {{ $isSeleksi ? '600' : '500' }}; font-size: 14px; color: {{ $isSeleksi ? '#293C79' : 'var(--c-fg-muted)' }}; text-decoration: none; {!! $isSeleksi ? 'border-bottom: 2px solid #293C79;' : '' !!}">Seleksi Koordinator</a>
    </div>
</div>
