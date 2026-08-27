<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Pengumuman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            font-family: 'Inter Tight', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        :root {
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-500: #4f46e5;
            --grey-50: #f9fafb;
            --grey-100: #f3f4f6;
            --grey-200: #e5e7eb;
            --grey-400: #9ca3af;
            --grey-500: #6b7280;
            --grey-700: #374151;
            --grey-800: #1f2937;
            --grey-900: #030712;
            --warning-50: #fef3c7;
            --warning-300: #d97706;
            --info-50: #eff6ff;
            --info-500: #3b82f6;
        }

        .sikape-card {
            background: #fff;
            border: 1px solid var(--grey-200);
            border-radius: 12px;
        }
    </style>
</head>

<body style="background:#f9fafb;" x-data="{ sidebarOpen: false, search: '', openItem: null }">
    <div class="flex h-screen w-full overflow-hidden">

        @include('eoffice::kp.mahasiswa.partials.sidebar')

        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
            @include('eoffice::kp.mahasiswa.partials.topbar', [
                'breadcrumbs' => ['Informasi', 'Pengumuman']
            ])

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                {{-- Heading --}}
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold" style="color:var(--grey-900);">Pengumuman Kerja Praktik</h1>
                    <p class="text-sm mt-1" style="color:var(--grey-500);">Informasi terbaru dan timeline kegiatan Kerja
                        Praktik dari Koordinator.</p>
                </div>

                {{-- Search --}}
                <div class="sikape-card p-4 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" style="color:var(--grey-400);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" x-model="search" placeholder="Cari pengumuman..."
                        class="flex-1 text-sm outline-none bg-transparent" style="color:var(--grey-800);">
                    <button x-show="search" @click="search=''" class="text-xs px-2 py-1 rounded"
                        style="color:var(--grey-400);background:var(--grey-100);">Hapus</button>
                </div>

                {{-- Announcement List --}}
                <div class="space-y-4">
                    @forelse($pengumumanItems as $i => $item)
                        <div x-show="('{{ addslashes($item->judul) }}'.toLowerCase().includes(search.toLowerCase()) || '{{ addslashes($item->konten) }}'.toLowerCase().includes(search.toLowerCase()) || search === '')"
                            class="sikape-card overflow-hidden">
                            <button @click="openItem = openItem === {{ $i }} ? null : {{ $i }}"
                                class="flex flex-col sm:flex-row sm:items-center justify-between w-full p-5 text-left gap-4">
                                <div class="flex items-start sm:items-center gap-3 flex-1 min-w-0">
                                    @if($item->tipe === 'timeline')
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                                            style="background:var(--info-50);color:var(--info-500);">Timeline</span>
                                    @else
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                                            style="background:var(--primary-50);color:var(--primary-500);">Pengumuman</span>
                                    @endif
                                    <div class="flex-1 min-w-0 mt-1 sm:mt-0">
                                        <p class="text-sm font-semibold pr-2" style="color:var(--grey-800);">
                                            {{ $item->judul }}
                                        </p>
                                        <div class="flex items-center flex-wrap gap-2 mt-0.5">
                                            <p class="text-xs" style="color:var(--grey-400);">
                                                {{ \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d F Y, H:i') }}
                                                WIB
                                            </p>
                                            <span class="text-[10px]" style="color:var(--grey-300);">•</span>
                                            <p class="text-xs" style="color:var(--grey-400);">
                                                {{ $item->pembuat->name ?? 'Admin' }}
                                            </p>
                                            @if($item->updated_at->ne($item->created_at))
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded"
                                                    style="background:var(--grey-100);color:var(--grey-500);">Diedit</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center self-end sm:self-auto gap-2">
                                    <span class="text-xs" style="color:var(--grey-400);"
                                        x-text="openItem === {{ $i }} ? 'Tutup' : 'Lihat Detail'"></span>
                                    <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200"
                                        :class="openItem === {{ $i }} ? 'rotate-180' : ''" style="color:var(--grey-400);"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>
                            <div x-show="openItem === {{ $i }}" x-collapse x-cloak>
                                <div class="px-5 pb-5 border-t" style="border-color:var(--grey-100);">
                                    <div class="text-sm leading-relaxed pt-4 whitespace-pre-line"
                                        style="color:var(--grey-600);">
                                        {!! nl2br(e($item->konten)) !!}
                                    </div>
                                    @if($item->lampiran)
                                        <div class="mt-5 p-3 rounded-xl border flex items-center justify-between gap-4 max-w-md hover:shadow-sm transition-all"
                                            style="background:var(--primary-50);border-color:var(--primary-100);">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                                    style="background:var(--grey-0);color:var(--primary-500);">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold" style="color:var(--grey-900);">Lampiran Dokumen
                                                    </p>
                                                    <p class="text-[10px] mt-0.5" style="color:var(--grey-500);">Klik untuk
                                                        melihat detail file</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('eoffice.kp.mahasiswa.pengumuman.lampiran', $item->id) }}"
                                                target="_blank"
                                                class="px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition-colors"
                                                style="background:var(--primary-500);color:white;">
                                                Buka File
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="sikape-card p-12 text-center" x-show="search === ''">
                            <svg class="w-12 h-12 mx-auto mb-3" style="color:var(--grey-300);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="text-sm font-semibold" style="color:var(--grey-500);">Belum ada pengumuman</p>
                            <p class="text-xs mt-1" style="color:var(--grey-400);">Pengumuman terbaru dari Koordinator KP
                                akan muncul di sini.</p>
                        </div>
                    @endforelse

                    {{-- Empty state for search --}}
                    <div x-show="search && search.length > 0" class="hidden last:block">
                        <div class="sikape-card p-12 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3" style="color:var(--grey-300);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-semibold" style="color:var(--grey-500);">Pengumuman tidak ditemukan
                            </p>
                            <p class="text-xs mt-1" style="color:var(--grey-400);">Tidak ada pengumuman yang cocok
                                dengan pencarian Anda.</p>
                        </div>
                    </div>

                </div>

            </main>
        </div>
    </div>
</body>

</html>