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

        {{-- Upload Button & Delete Button --}}
        <div class="absolute top-4 right-4 pointer-events-auto z-10 flex gap-2" :style="`opacity: ${Math.max(0, 1 - (st / 100))};`">
            @if($praktikum->cover_path)
                <form action="{{ route('eoffice.manprak.koor.praktikum.cover.destroy', $praktikum->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar sampul?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="cursor:pointer; background:rgba(223,28,65,0.9); padding:8px; border-radius:8px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 4px rgba(0,0,0,0.1); backdrop-filter:blur(4px); transition:all 0.2s;" onmouseover="this.style.background='#DF1C41'" onmouseout="this.style.background='rgba(223,28,65,0.9)'" title="Hapus Gambar Sampul">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                            <path d="M10 11v6"></path>
                            <path d="M14 11v6"></path>
                        </svg>
                    </button>
                </form>
            @endif

            <form action="{{ route('eoffice.manprak.koor.praktikum.cover.update', $praktikum->id) }}" method="POST" enctype="multipart/form-data" id="coverUploadForm">
                @csrf
                <label for="cover_upload" style="cursor:pointer; background:rgba(255,255,255,0.9); padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; color:#111827; display:flex; align-items:center; gap:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1); backdrop-filter:blur(4px); transition:all 0.2s;" onmouseover="this.style.background='#ffffff'" onmouseout="this.style.background='rgba(255,255,255,0.9)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    {{ $praktikum->cover_path ? 'Ubah Gambar Sampul' : 'Tambahkan Gambar Sampul' }}
                </label>
                <input type="file" id="cover_upload" name="cover" class="hidden" accept="image/jpeg,image/png,image/jpg" onchange="document.getElementById('coverUploadForm').submit()">
            </form>
        </div>

        {{-- 1. Base Gradient --}}
        <div class="absolute inset-0 pointer-events-none"
            style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.1) 60%, rgba(0,0,0,0) 100%);">
        </div>

        {{-- 2. Scrolled Overlay --}}
        <div class="absolute inset-0 pointer-events-none"
            :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
        </div>

        {{-- Title --}}
        <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
            <h1 class="text-white m-0 tracking-[-0.5px]"
                :style="`transform-origin: left bottom; font-weight: 800; font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                {{ $praktikum->nama ?? 'Praktikum' }}
            </h1>
        </div>
    </div>

    @php
        $currentRoute = request()->route()?->getName();
        $isDetail = $currentRoute == 'eoffice.manprak.koor.praktikum.show';
        $isPeriode = str_contains($currentRoute, 'periode-pendaftaran');
        $isSeleksi = str_contains($currentRoute, 'pendaftaran-asprak');
        $isModul = $currentRoute == 'eoffice.manprak.koor.modul.index';
        $isBagiModul = str_contains($currentRoute, 'bagi-modul');
        $isPraktikan = str_contains($currentRoute, 'praktikan');
        $isNilai = str_contains($currentRoute, 'nilai');

        $activeStyle = "padding:12px 24px; font-weight:600; font-size:14px; color:#293C79; text-decoration:none; border-bottom:2px solid #293C79;";
        $inactiveStyle = "padding:12px 24px; font-weight:500; font-size:14px; color:var(--c-fg-muted); text-decoration:none;";
    @endphp

    {{-- Tabs Koordinator --}}
    <div style="display: flex; gap: 8px; overflow-x: auto; white-space: nowrap; scrollbar-width: none;">

        <a href="{{ route('eoffice.manprak.koor.pendaftaran-asprak.index') }}"
            style="{{ $isSeleksi || $isPeriode ? $activeStyle : $inactiveStyle }}">Seleksi Asisten</a>

        <a href="{{ route('eoffice.manprak.koor.modul.index') }}"
            style="{{ $isModul ? $activeStyle : $inactiveStyle }}">Kelola Modul</a>



        <a href="{{ route('eoffice.manprak.koor.praktikan.index') }}"
            style="{{ $isPraktikan ? $activeStyle : $inactiveStyle }}">Data Praktikan</a>

        <a href="{{ route('eoffice.manprak.koor.nilai.index') }}"
            style="{{ $isNilai ? $activeStyle : $inactiveStyle }}">Absensi & Nilai</a>
    </div>
</div>