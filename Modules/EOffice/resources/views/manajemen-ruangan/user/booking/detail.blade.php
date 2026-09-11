<x-eoffice::manajemen-ruangan.layout pageTitle="Detail Ruangan">

    {{-- =================== BREADCRUMB =================== --}}
    <div class="flex items-center gap-2 text-[12px] text-gray-500 mb-4">
        <a href="{{ route('eoffice.peminjaman.user.booking') }}" class="hover:text-[#0B266E] font-medium transition-colors">
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
                <div class="aspect-video w-full relative bg-gradient-to-br from-blue-50 via-blue-50 to-blue-100 overflow-hidden group">
                    <style>
                        .gallery-slider::-webkit-scrollbar { display: none; }
                        .gallery-slider { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>
                    @if($room->fotos->count() > 0)
                        <div class="absolute inset-0" x-data="{
                            atStart: true,
                            atEnd: false,
                            checkScroll() {
                                const slider = this.$refs.slider;
                                this.atStart = slider.scrollLeft <= 5;
                                this.atEnd = Math.ceil(slider.scrollLeft + slider.clientWidth) >= slider.scrollWidth - 5;
                            },
                            scrollNext() { this.$refs.slider.scrollBy({left: this.$refs.slider.clientWidth, behavior: 'smooth'}) },
                            scrollPrev() { this.$refs.slider.scrollBy({left: -this.$refs.slider.clientWidth, behavior: 'smooth'}) }
                        }" x-init="$nextTick(() => checkScroll())">
                            <div x-ref="slider" @scroll.passive="checkScroll" class="gallery-slider" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; width: 100%; height: 100%;">
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
                                <!-- Prev Button -->
                                <button type="button" x-show="!atStart" @click="scrollPrev" x-transition.opacity style="display:none;"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors cursor-pointer border-0 bg-transparent drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] z-10">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <!-- Next Button -->
                                <button type="button" x-show="!atEnd" @click="scrollNext" x-transition.opacity style="display:none;"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors cursor-pointer border-0 bg-transparent drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] z-10">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <svg class="w-16 h-16 text-blue-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                            </svg>
                            <p class="text-[14px] font-bold text-blue-400 uppercase tracking-wider">Foto Ruangan Segera Hadir</p>
                            <p class="text-[12px] text-blue-300 mt-1 font-medium">Foto akan ditambahkan oleh administrator</p>
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $room->nama }}</h1>
                            <p class="text-[13px] text-gray-500 mt-1">
                                {{ $room->lokasi ?? 'Gedung Utama' }}
                                @if ($room->lantai)
                                    &nbsp;·&nbsp; Lantai {{ $room->lantai }}
                                @endif
                            </p>
                        </div>
                        <span class="flex-shrink-0 bg-white text-[#0B266E] border border-gray-200 text-sm font-bold px-3 py-1.5 rounded-full shadow-sm">
                            {{ $room->kapasitas }} orang
                        </span>
                    </div>
                </div>
            </div>



            {{-- Facilities --}}
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2 class="font-bold text-gray-800 text-[15px]">Fasilitas Tersedia</h2>
                </div>
                <div class="mp-card-body p-5">
                    @if (count($fasilitas) > 0)
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($fasilitas as $fas)
                                <div class="inline-flex items-center px-3 py-1.5 rounded-lg border bg-blue-50 text-blue-700 border-blue-100">
                                    <span class="text-[12px] font-semibold">{{ $fas }}</span>
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
            <div class="rounded-xl border border-[#0B266E] bg-gradient-to-br from-[#0B266E] to-[#1a3c94] text-white overflow-hidden shadow-md">
                <div class="p-5">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-[15px] font-bold mb-1 text-white">Ingin memakai ruangan ini?</h3>
                    <p class="text-[12px] text-blue-100 mb-4 leading-relaxed">Cek kalender jadwal mingguan untuk slot kosong yang tersedia lalu ajukan booking langsung!</p>
                    <a href="{{ $kalenderUrl }}"
                        class="block w-full py-2.5 text-center text-[13px] font-bold bg-white text-[#0B266E] rounded-lg hover:bg-blue-50 transition-colors shadow-sm">
                        Lihat Kalender & Booking
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
                        <div class="p-5">
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
