<x-eoffice::manajemen-ruangan.layout pageTitle="Detail Ruangan">

    {{-- =================== BREADCRUMB =================== --}}
    <div class="flex items-center gap-2 text-[12px] text-gray-500 mb-4">
        <a href="{{ route('eoffice.peminjaman.user.booking') }}" class="hover:text-indigo-600 font-medium transition-colors">
            Katalog Ruangan
        </a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-800 font-semibold">{{ $room->nama }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- =================== LEFT COLUMN =================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Photo Area / Actual Photo --}}
            <div class="mp-card overflow-hidden">
                <div class="aspect-video w-full relative flex items-center justify-center bg-gradient-to-br from-indigo-100 via-indigo-50 to-blue-50">
                    <style>
                        .gallery-slider::-webkit-scrollbar { display: none; }
                        .gallery-slider { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>
                    @if($room->fotos->count() > 0)
                        <div class="gallery-slider" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; width: 100%; height: 100%;">
                            @foreach($room->fotos as $foto)
                                <div style="flex: 0 0 100%; width: 100%; height: 100%; position: relative; scroll-snap-align: start;">
                                    <img src="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($foto->path_foto) }}" alt="Foto {{ $room->nama }}" class="w-full h-full object-cover">
                                    @if($room->fotos->count() > 1)
                                        <div class="absolute top-3 left-3 bg-black/50 text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm">
                                            {{ $loop->iteration }} / {{ $room->fotos->count() }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($room->fotos->count() > 1)
                            <!-- Swipe hint -->
                            <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-black/20 to-transparent pointer-events-none flex items-center justify-end pr-2 text-white/60">
                                <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <svg class="w-16 h-16 text-indigo-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                            </svg>
                            <p class="text-sm font-semibold text-indigo-400 uppercase tracking-wider">Foto Ruangan Segera Hadir</p>
                            <p class="text-[11px] text-indigo-300 mt-1">Foto akan ditambahkan oleh administrator</p>
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $room->nama }}</h1>
                            <p class="text-[13px] text-gray-500 mt-1">
                                🏢 {{ $room->lokasi ?? 'Gedung Utama' }}
                                @if ($room->lantai)
                                    &nbsp;·&nbsp; Lantai {{ $room->lantai }}
                                @endif
                            </p>
                        </div>
                        <span class="flex-shrink-0 bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-bold px-3 py-1.5 rounded-full">
                            👥 {{ $room->kapasitas }} orang
                        </span>
                    </div>
                </div>
            </div>

            {{-- Room Details Grid --}}
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2 class="font-bold text-gray-800 text-[15px]">Informasi Ruangan</h2>
                </div>
                <div class="mp-card-body p-5">
                    <div class="grid grid-cols-3 divide-x divide-gray-100">
                        <div class="pr-6 text-center">
                            <p class="text-3xl mb-1.5">🏢</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Gedung</p>
                            <p class="text-[14px] font-bold text-gray-800">{{ $room->lokasi ?? 'Gedung Utama' }}</p>
                        </div>
                        <div class="px-6 text-center">
                            <p class="text-3xl mb-1.5">📐</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Lantai</p>
                            <p class="text-[14px] font-bold text-gray-800">{{ $room->lantai ?? '–' }}</p>
                        </div>
                        <div class="pl-6 text-center">
                            <p class="text-3xl mb-1.5">👥</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Kapasitas</p>
                            <p class="text-[14px] font-bold text-gray-800">{{ $room->kapasitas }} orang</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Facilities --}}
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2 class="font-bold text-gray-800 text-[15px]">Fasilitas Tersedia</h2>
                </div>
                <div class="mp-card-body p-5">
                    @php
                        $facilityIcons = [
                            'Proyektor' => ['icon' => '📽️', 'color' => 'bg-purple-50 text-purple-700 border-purple-100'],
                            'AC' => ['icon' => '❄️', 'color' => 'bg-blue-50 text-blue-700 border-blue-100'],
                            'Whiteboard' => ['icon' => '🖊️', 'color' => 'bg-gray-50 text-gray-700 border-gray-200'],
                            'WiFi' => ['icon' => '📶', 'color' => 'bg-indigo-50 text-indigo-700 border-indigo-100'],
                            'Sound System' => ['icon' => '🔊', 'color' => 'bg-amber-50 text-amber-700 border-amber-100'],
                            'CCTV' => ['icon' => '📷', 'color' => 'bg-red-50 text-red-700 border-red-100'],
                            'Meja' => ['icon' => '🪑', 'color' => 'bg-orange-50 text-orange-700 border-orange-100'],
                            'Komputer' => ['icon' => '💻', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                        ];
                    @endphp

                    @if (count($fasilitas) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($fasilitas as $fas)
                                @php $fInfo = $facilityIcons[$fas] ?? ['icon' => '✅', 'color' => 'bg-gray-50 text-gray-700 border-gray-200']; @endphp
                                <div class="flex items-center gap-2.5 p-3 rounded-xl border {{ $fInfo['color'] }}">
                                    <span class="text-xl">{{ $fInfo['icon'] }}</span>
                                    <span class="text-[13px] font-semibold">{{ $fas }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 italic text-sm">Belum ada fasilitas yang tercatat untuk ruangan ini.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- =================== RIGHT COLUMN =================== --}}
        <div class="space-y-5">

            {{-- CTA Card --}}
            <div class="mp-card bg-gradient-to-br from-indigo-600 to-indigo-700 text-white overflow-hidden">
                <div class="p-5">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-[15px] font-bold mb-1">Ingin memakai ruangan ini?</h3>
                    <p class="text-[12px] text-indigo-200 mb-4 leading-relaxed">Cek kalender jadwal mingguan untuk slot kosong yang tersedia lalu ajukan booking langsung!</p>
                    <a href="{{ $kalenderUrl }}"
                        class="block w-full py-2.5 text-center text-[13px] font-bold bg-white text-indigo-700 rounded-lg hover:bg-indigo-50 transition-colors shadow-sm">
                        📅 Lihat Kalender & Booking
                    </a>
                </div>
            </div>

            {{-- Upcoming Bookings --}}
            <div class="mp-card">
                <div class="mp-card-header flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 text-[14px]">Jadwal 7 Hari ke Depan</h2>
                    <span class="text-[11px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $upcomingBookings->count() }} booking
                    </span>
                </div>
                <div class="mp-card-body">
                    @if ($upcomingBookings->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach ($upcomingBookings as $bk)
                                <div class="py-3 px-4 flex items-start gap-3">
                                    <div class="mt-0.5">
                                        @if ($bk->status == 'disetujui')
                                            <span class="w-2 h-2 rounded-full bg-red-500 block mt-1.5"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-amber-400 block mt-1.5"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[12px] font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($bk->tanggal_pinjam)->translatedFormat('D, d M') }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($bk->jam_mulai)->format('H:i') }} –
                                            {{ \Carbon\Carbon::parse($bk->jam_selesai)->format('H:i') }} WIB
                                        </p>
                                        @if ($bk->status == 'disetujui')
                                            <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wide bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Terisi</span>
                                        @else
                                            <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wide bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center px-4">
                            <p class="text-2xl mb-2">🟢</p>
                            <p class="text-[13px] font-bold text-gray-700 mb-0.5">Kosong</p>
                            <p class="text-[11px] text-gray-400">Tidak ada booking dalam 7 hari ke depan!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Back to Catalog --}}
            <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                class="flex items-center justify-center gap-2 w-full py-2.5 text-[13px] font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                ← Kembali ke Katalog
            </a>
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>
