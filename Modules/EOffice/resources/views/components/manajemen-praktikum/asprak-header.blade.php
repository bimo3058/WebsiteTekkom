@props(['praktikum', 'activeTab' => ''])

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
        $activeStyle = "padding:12px 24px; font-weight:600; font-size:14px; color:#293C79; text-decoration:none; border-bottom:2px solid #293C79;";
        $inactiveStyle = "padding:12px 24px; font-weight:500; font-size:14px; color:var(--c-fg-muted); text-decoration:none;";

        $tabs = [
            ['id' => 'pengumuman', 'label' => 'Pengumuman', 'route' => route('eoffice.manprak.asprak.pengumuman.index')],
            ['id' => 'modul', 'label' => 'Modul', 'route' => route('eoffice.manprak.asprak.modul.index')],
            ['id' => 'tugas', 'label' => 'Tugas', 'route' => route('eoffice.manprak.asprak.tugas.index')],
            ['id' => 'absensi', 'label' => 'Absensi & Nilai', 'route' => route('eoffice.manprak.asprak.absensi.index')],
            ['id' => 'praktikan', 'label' => 'Daftar Praktikan', 'route' => route('eoffice.manprak.asprak.daftar-praktikan.index')],
        ];
    @endphp

    {{-- Tabs Asprak --}}
    <div style="display: flex; gap: 8px; overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
        @foreach($tabs as $tab)
            <a href="{{ $tab['route'] }}" style="{{ $activeTab === $tab['id'] ? $activeStyle : $inactiveStyle }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>
