<x-eoffice::manajemen-ruangan.layout pageTitle="Katalog Ruangan">

    {{-- =================== PAGE HEADER =================== --}}
    <div class="mp-page-header">
        <div class="flex flex-col md:flex-row md:items-center justify-between w-full gap-4">
            <div>
                <h1 class="mp-page-title">Katalog Ruangan</h1>
                <p class="mp-page-sub">Temukan ruangan yang sesuai kebutuhanmu. Klik "Detail" untuk info lengkap
                    atau "Jadwal" untuk langsung booking.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                    <input type="text" id="searchInput" onkeyup="filterRooms()"
                        placeholder="Cari nama ruangan..."
                        class="pl-9 pr-4 py-2 text-[13px] w-56 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <a href="{{ route('eoffice.peminjaman.user.kalender') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-[13px] font-semibold border border-indigo-200 hover:bg-indigo-100 transition-colors">
                    Lihat Kalender
                </a>
            </div>
        </div>
    </div>



    {{-- =================== ROOM CARDS GRID =================== --}}
    @if ($ruangans->count() > 0)
        <div id="roomGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($ruangans as $room)
                @php
                    $fasilitas = is_array($room->fasilitas)
                        ? $room->fasilitas
                        : (json_decode($room->fasilitas, true) ?? []);
                    $bookedToday = $room->peminjamans
                        ->whereIn('status', ['menunggu', 'disetujui'])
                        ->count();
                    $facilityIcons = [
                        'Proyektor' => '📽️',
                        'AC' => '❄️',
                        'Whiteboard' => '🖊️',
                        'WiFi' => '📶',
                        'Sound System' => '🔊',
                        'CCTV' => '📷',
                        'Meja' => '🪑',
                        'Komputer' => '💻',
                    ];
                    $detailUrl = route('eoffice.peminjaman.user.booking.ruangan.show', $room->id);
                    $kalenderUrl = route('eoffice.peminjaman.user.kalender', ['ruangan_id' => $room->id]);
                @endphp

                <div class="room-card bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col"
                    data-name="{{ strtolower($room->nama) }}">

                    {{-- Room Image Placeholder / Actual Photo --}}
                    <a href="{{ $detailUrl }}"
                        class="block w-full aspect-video relative border-b border-gray-100 overflow-hidden group bg-gradient-to-br from-indigo-50 to-indigo-100">
                        @if($room->fotos->count() > 0)
                            <img src="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($room->fotos->first()->path_foto) }}" alt="Foto {{ $room->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-indigo-300 mb-1 group-hover:text-indigo-400 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                                </svg>
                                <span class="text-[11px] font-semibold text-indigo-400 tracking-wide uppercase">Foto segera hadir</span>
                            </div>
                        @endif
                        <span class="absolute top-3 right-3 bg-white text-[11px] font-bold text-indigo-700 border border-indigo-200 rounded-full px-2.5 py-0.5 shadow-sm">
                            👥 {{ $room->kapasitas }} orang
                        </span>
                    </a>

                    {{-- Room Info --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-3">
                            <a href="{{ $detailUrl }}" class="hover:text-indigo-700 transition-colors">
                                <h3 class="font-bold text-gray-900 text-[15px] leading-tight">{{ $room->nama }}</h3>
                            </a>
                            <p class="text-[12px] text-gray-500 mt-0.5">
                                🏢 {{ $room->lokasi ?? 'Gedung Utama' }}
                                @if ($room->lantai)
                                    · Lantai {{ $room->lantai }}
                                @endif
                            </p>
                        </div>

                        {{-- Facilities Tags (max 4) --}}
                        @if (count($fasilitas) > 0)
                            <div class="flex flex-wrap gap-1.5 mb-4">
                                @foreach (array_slice($fasilitas, 0, 4) as $fas)
                                    <span
                                        class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $facilityIcons[$fas] ?? '✅' }} {{ $fas }}
                                    </span>
                                @endforeach
                                @if (count($fasilitas) > 4)
                                    <span
                                        class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-500">
                                        +{{ count($fasilitas) - 4 }} lainnya
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="mb-4">
                                <span class="text-[12px] text-gray-400 italic">Tidak ada fasilitas tercatat.</span>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                            <div class="flex items-center gap-2">
                                {{-- Detail Page Link --}}
                                <a href="{{ $detailUrl }}"
                                    class="inline-flex items-center px-4 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[12px] font-semibold transition-colors border border-gray-200">
                                    Detail
                                </a>
                                {{-- Schedule Button --}}
                                <a href="{{ $kalenderUrl }}"
                                    class="inline-flex items-center px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[12px] font-bold transition-colors shadow-sm whitespace-nowrap">
                                    Jadwal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="emptySearch" class="hidden text-center py-16">
            <p class="text-gray-500 font-medium">Tidak ada ruangan yang cocok dengan pencarianmu.</p>
        </div>
    @else
        <div class="text-center py-20 px-6 border border-gray-200 rounded-2xl bg-white mt-4">
            <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Ruangan Aktif</h3>
            <p class="text-[13px] text-gray-500 max-w-xs mx-auto">Admin belum menambahkan atau mengaktifkan ruangan
                apapun. Silakan hubungi administrator.</p>
        </div>
    @endif

    {{-- =================== SEARCH SCRIPT =================== --}}
    <script>
        function filterRooms() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.room-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                if (name.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            const grid = document.getElementById('roomGrid');
            const empty = document.getElementById('emptySearch');
            if (grid) grid.classList.toggle('hidden', visibleCount === 0);
            if (empty) empty.classList.toggle('hidden', visibleCount > 0);
        }
    </script>

</x-eoffice::manajemen-ruangan.layout>