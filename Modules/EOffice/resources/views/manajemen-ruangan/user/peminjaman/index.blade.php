<x-eoffice::manajemen-ruangan.layout pageTitle="Peminjaman Saya">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Peminjaman Saya</h1>
            <p class="mp-page-sub">Pantau status pengajuan yang sedang diproses dan kelola jadwal pemakaian ruangan Anda
                yang akan datang.</p>
        </div>
        <div>
            <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white text-[13px] font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm hover:shadow focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 gap-1.5 border border-indigo-700/50">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Booking Ruang Baru
            </a>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-body p-6">
            @if(session('success'))
                <div class="mb-4 p-4 text-emerald-800 bg-emerald-50 rounded-lg flex items-center gap-3">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if($peminjamans->count() > 0)
                <!-- Grid Cards instead of Table for better UX -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($peminjamans as $booking)
                        <div
                            class="border border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round">
                                            <path d="M3 3v18h18M19 19V5a2 2 0 00-2-2H7C6 3 5 4 5 5v14M8 7h6M8 11h6M8 15h2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-[15px]">{{ $booking->ruangan->nama }}</h3>
                                        <p class="text-[11px] text-gray-500 font-medium">Req ID:
                                            #BKN-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                                <div>
                                    @if($booking->status == 'menunggu')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @elseif($booking->status == 'disetujui')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-5 space-y-4 text-[13px] text-gray-600 flex-1">
                                <div>
                                    <span
                                        class="block text-[11px] font-bold uppercase text-gray-400 mb-0.5 tracking-wider">Jadwal
                                        Pemakaian</span>
                                    <div class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div
                                        class="text-indigo-600 font-semibold inline-block bg-indigo-50 px-2 py-0.5 rounded mt-1">
                                        {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }} WIB
                                    </div>
                                </div>

                                <div>
                                    <span
                                        class="block text-[11px] font-bold uppercase text-gray-400 mb-0.5 tracking-wider">Tujuan
                                        Peminjaman</span>
                                    <p class="font-medium text-gray-700 leading-relaxed">{{ $booking->tujuan }}</p>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 border-t border-gray-100 flex items-center justify-between rounded-b-xl">
                                @if($booking->berkas_pendukung)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($booking->berkas_pendukung) }}"
                                        target="_blank"
                                        class="text-[12px] font-semibold text-gray-600 hover:text-indigo-600 inline-flex items-center gap-1">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        Lihat Berkas
                                    </a>
                                @else
                                    <span class="text-[12px] italic font-medium text-gray-400 inline-flex items-center gap-1">
                                        Tanpa lampiran berkas
                                    </span>
                                @endif

                                <form method="POST" action="{{ route('eoffice.peminjaman.user.saya.batal', $booking->id) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin secara sukarela membatalkan pengajuan ini?');"
                                    class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="text-[12px] font-bold text-red-600 hover:text-red-700 hover:underline px-2 flex items-center gap-1">
                                        Batalkan Pengajuan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 px-6 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                    <div
                        class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg width="32" height="32" fill="none" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M9 12h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Peminjaman Kosong</h3>
                    <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-6">Anda tidak memiliki pengajuan peminjaman
                        ruangan yang sedang berjalan atau aktif saat ini.</p>
                    <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                        class="mp-btn primary !rounded-full shadow-md shadow-indigo-600/20">Mulai Ajukan Peminjaman</a>
                </div>
            @endif
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>