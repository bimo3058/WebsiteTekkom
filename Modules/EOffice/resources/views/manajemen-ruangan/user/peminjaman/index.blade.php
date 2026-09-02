<x-eoffice::manajemen-ruangan.layout pageTitle="Peminjaman Saya">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Peminjaman Saya</h1>
            <p class="mp-page-sub">Pantau status pengajuan yang sedang diproses dan kelola jadwal pemakaian ruangan Anda
                yang akan datang.</p>
        </div>
        <div>
            <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[13px] font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm hover:shadow">
                Pinjam Ruang
            </a>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-body">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-[12px]">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Peminjaman Anda</h2>
            </div>

            @if($peminjamans->count() > 0)
                <div class="mp-table-wrap">
                    <table class="mp-table" style="table-layout: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>RUANGAN & TUJUAN</th>
                                <th>JADWAL PEMAKAIAN</th>
                                <th>LAMPIRAN</th>
                                <th>STATUS</th>
                                <th style="width: 100px; text-align: right;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamans as $booking)
                                <tr class="mp-tr">
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            {{ $booking->ruangan->nama }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                            title="{{ $booking->tujuan }}">
                                            {{ $booking->tujuan }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->translatedFormat('d F Y') }}
                                            <span class="text-gray-400 mx-1">•</span>
                                            {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }} WIB
                                        </div>
                                    </td>
                                    <td>
                                        @if($booking->berkas_pendukung)
                                            <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($booking->berkas_pendukung) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 text-[12px] font-medium text-primary-500 hover:text-primary-500 transition-colors">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            <span class="text-[12px] italic text-gray-400">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $style = '';
                                            if (strtolower($booking->status) === 'disetujui')
                                                $style = 'bg-[#E8F8F2] text-[#166534]';
                                            elseif (strtolower($booking->status) === 'ditolak')
                                                $style = 'bg-[#FDF2F2] text-[#991B1B]';
                                            elseif (strtolower($booking->status) === 'menunggu')
                                                $style = 'bg-[#FFF9ED] text-[#A77B2E]';
                                            else
                                                $style = 'bg-[#F1F5F9] text-[#1E293B]';
                                        @endphp
                                        <span
                                            class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full {{ $style }} text-[12px] font-medium tracking-wide">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="flex items-center justify-end">
                                            @if(!in_array(strtolower($booking->status), ['dibatalkan', 'ditolak', 'selesai']))
                                                <form method="POST"
                                                    action="{{ route('eoffice.peminjaman.user.saya.batal', $booking->id) }}"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin secara sukarela membatalkan pengajuan ini?');"
                                                    class="m-0">
                                                    @csrf
                                                    <button type="submit"
                                                        class="h-8 px-3 rounded-md bg-white border border-red-200 text-[12px] font-medium text-red-600 hover:bg-red-50 hover:border-red-300 shadow-sm transition-all focus:ring-2 focus:ring-offset-1 focus:ring-red-100">
                                                        Batal
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[12px] italic text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6">
                    <div class="text-center py-20 px-6 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                        <div
                            class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <svg width="32" height="32" fill="none" stroke="#9CA3AF" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M9 12h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Peminjaman Kosong</h3>
                        <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-6">Anda tidak memiliki pengajuan peminjaman
                            ruangan yang sedang berjalan atau aktif saat ini.</p>
                        <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                            class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[14px] font-semibold px-6 py-[11px] rounded-full transition-colors shadow-md shadow-primary-500/10">Mulai
                            Ajukan Peminjaman</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>