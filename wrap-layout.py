import sys

target_file = r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\dosen\pendaftaran-koor.blade.php"

with open(target_file, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the layout tag and header
old_header = """<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Koordinator">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
        <p class="mp-page-sub">Review pendaftar koordinator sesuai praktikum yang Anda ampu</p>
    </div>
</div>"""

new_header = """<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama . ' / Seleksi Koordinator' : 'Seleksi Koordinator' }}">

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
            <div style="font-size:13px;color:#666D80;margin-bottom:24px;">Pilih praktikum untuk mengelola seleksi koordinator.</div>

            @if(empty($praktikumList) || (isset($praktikumList) && count($praktikumList) == 0))
            <div style="font-size:13px;color:#808897;">Kamu belum mengampu praktikum apapun.</div>
            @else
            <div class="flex flex-col gap-2 max-w-sm mx-auto">
                @foreach($praktikumList as $p)
                <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $p->id]) }}"
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
         class="sticky z-20 bg-white" style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border); margin-bottom: 24px;">
         
        {{-- Banner / Cover Image Container --}}
        <div style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">
            
            {{-- Cover Placeholder or Image --}}
            <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896" 
                     :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>

            {{-- 1. Base Gradient (Tampil penuh, tidak berubah) --}}
            <div class="absolute inset-0 pointer-events-none"
                 style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 30%, rgba(0,0,0,0) 70%);">
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
            <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
            <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Tugas</a>
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi dan Nilai</a>
            <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
            <a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Seleksi Koordinator</a>
        </div>
    </div>"""

content = content.replace(old_header, new_header)

# Add @endif at the very end before closing the layout component
old_end = "</x-eoffice::manajemen-praktikum.layout>"
new_end = "@endif\n</x-eoffice::manajemen-praktikum.layout>"
if content.endswith(old_end):
    content = content[:-len(old_end)] + new_end
elif old_end in content:
    content = content.replace(old_end, new_end)
else:
    # If not found, just append
    content += "\n@endif\n</x-eoffice::manajemen-praktikum.layout>"

with open(target_file, "w", encoding="utf-8") as f:
    f.write(content)

print("Wrapped successfully!")
