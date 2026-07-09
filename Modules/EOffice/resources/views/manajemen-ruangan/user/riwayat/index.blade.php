<x-eoffice::manajemen-ruangan.layout pageTitle="Riwayat Peminjaman">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Riwayat Peminjaman</h1>
            <p class="mp-page-sub">Daftar arsip seluruh pengajuan peminjaman ruangan Anda yang telah selesai, ditolak, atau dibatalkan.</p>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-body p-6">

            @if($riwayats->count() > 0)
                <div class="space-y-4">
                    @foreach($riwayats as $riwayat)
                        <div class="border border-gray-200 rounded-xl bg-white flex flex-col md:flex-row overflow-hidden hover:shadow-sm transition-shadow">
                            
                            {{-- Info Kiri --}}
                            <div class="p-5 flex-1 border-b md:border-b-0 md:border-r border-gray-100 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                                    @if($riwayat->status == 'ditolak') bg-red-50 text-red-500
                                    @else bg-gray-100 text-gray-500 @endif">
                                    @if($riwayat->status == 'ditolak')
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                    @elseif($riwayat->status == 'disetujui')
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-gray-900 text-[15px]">{{ $riwayat->ruangan->nama }}</h3>
                                        @if($riwayat->status == 'ditolak')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">Ditolak</span>
                                        @elseif($riwayat->status == 'disetujui')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">Selesai</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-200 text-gray-700">Dibatalkan</span>
                                        @endif
                                    </div>
                                    <p class="text-[12px] text-gray-500">Diajukan pada: {{ $riwayat->created_at->translatedFormat('d F Y, H:i') }}</p>
                                    <div class="mt-2 text-[13px] text-gray-600">
                                        <span class="font-semibold">Jadwal:</span> {{ \Carbon\Carbon::parse($riwayat->tanggal_pinjam)->translatedFormat('l, d F Y') }} <br>
                                        <span class="font-semibold">Waktu:</span> {{ \Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($riwayat->jam_selesai)->format('H:i') }} WIB <br>
                                        <span class="font-semibold">Tujuan:</span> {{ $riwayat->tujuan }}
                                    </div>
                                </div>
                            </div>

                            {{-- Info Kanan (Alasan & File) --}}
                            <div class="p-5 md:w-1/3 bg-gray-50 flex flex-col justify-center">
                                @if($riwayat->status == 'ditolak')
                                    <div class="mb-3">
                                        <span class="block text-[11px] font-bold uppercase text-red-400 mb-1 tracking-wider">Alasan Penolakan</span>
                                        <p class="text-[13px] font-medium text-gray-700 italic border-l-2 border-red-300 pl-2">
                                            "{{ $riwayat->alasan_penolakan ?? 'Tidak memenuhi syarat peminjaman.' }}"
                                        </p>
                                    </div>
                                @elseif($riwayat->status == 'disetujui')
                                    <div class="mb-3">
                                        <span class="block text-[11px] font-bold uppercase text-emerald-500 mb-1 tracking-wider">Keterangan</span>
                                        <p class="text-[13px] font-medium text-emerald-800">Kegiatan telah berhasil dilaksanakan.</p>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <span class="block text-[11px] font-bold uppercase text-gray-400 mb-1 tracking-wider">Keterangan</span>
                                        <p class="text-[13px] font-medium text-gray-600">Dibatalkan oleh peminjam.</p>
                                    </div>
                                @endif

                                <div class="mt-auto">
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($riwayat->berkas_pendukung) }}" target="_blank" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 group">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" class="group-hover:translate-x-0.5 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Lihat Arsip Proposal
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 px-6 border border-gray-200 rounded-2xl bg-white shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg width="32" height="32" fill="none" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Riwayat Kosong</h3>
                    <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-6">Belum ada riwayat peminjaman yang ditolak atau selesai pada akun Anda.</p>
                </div>
            @endif

        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>
