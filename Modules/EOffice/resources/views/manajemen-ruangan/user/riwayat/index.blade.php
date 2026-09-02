<x-eoffice::manajemen-ruangan.layout pageTitle="Riwayat Peminjaman">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Riwayat Peminjaman</h1>
            <p class="mp-page-sub">Daftar arsip seluruh pengajuan peminjaman ruangan Anda yang telah selesai, ditolak,
                atau dibatalkan.</p>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-body">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-[12px]">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Arsip Peminjaman</h2>
            </div>

            @if($riwayats->count() > 0)
                <div class="mp-table-wrap">
                    <table class="mp-table" style="table-layout: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>RUANGAN & TUJUAN</th>
                                <th>JADWAL PEMAKAIAN</th>
                                <th>KETERANGAN</th>
                                <th>STATUS</th>
                                <th style="width: 120px; text-align: right;">LAMPIRAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayats as $riwayat)
                                <tr class="mp-tr">
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            {{ $riwayat->ruangan->nama }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                            title="{{ $riwayat->tujuan }}">
                                            {{ $riwayat->tujuan }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            {{ \Carbon\Carbon::parse($riwayat->tanggal_pinjam)->translatedFormat('d F Y') }}
                                            <span class="text-gray-400 mx-1">•</span>
                                            {{ \Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($riwayat->jam_selesai)->format('H:i') }} WIB
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Diajukan: {{ $riwayat->created_at->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if(strtolower($riwayat->status) == 'ditolak')
                                            <div class="text-[12px] font-medium text-red-600 max-w-[200px] truncate"
                                                title="{{ $riwayat->alasan_penolakan ?? 'Tidak memenuhi syarat' }}">
                                                {{ $riwayat->alasan_penolakan ?? 'Tidak memenuhi syarat' }}
                                            </div>
                                        @elseif(strtolower($riwayat->status) == 'disetujui')
                                            <div class="text-[12px] font-medium text-emerald-600">
                                                Telah terlaksana
                                            </div>
                                        @else
                                            <div class="text-[12px] font-medium text-gray-500">
                                                Dibatalkan
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $style = '';
                                            if (strtolower($riwayat->status) === 'disetujui')
                                                $style = 'bg-[#ECF9F7] text-[#267666]';
                                            elseif (strtolower($riwayat->status) === 'ditolak')
                                                $style = 'bg-[#FEF2F2] text-[#B91C1C]';
                                            elseif (strtolower($riwayat->status) === 'menunggu')
                                                $style = 'bg-[#FFF9ED] text-[#A77B2E]';
                                            else
                                                $style = 'bg-[#ECEFF3] text-[#0D0D12]';
                                        @endphp
                                        <span
                                            class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full {{ $style }} text-[12px] font-medium tracking-wide">
                                            @if(strtolower($riwayat->status) === 'disetujui') Selesai @else
                                            {{ ucfirst($riwayat->status) }} @endif
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($riwayat->berkas_pendukung)
                                            <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($riwayat->berkas_pendukung) }}"
                                                target="_blank"
                                                class="inline-flex items-center justify-end gap-1.5 text-[12px] font-medium text-primary-500 hover:text-primary-500 transition-colors">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Proposal
                                            </a>
                                        @else
                                            <span class="text-[12px] italic text-gray-400">Tanpa arsip</span>
                                        @endif
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
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Riwayat Kosong</h3>
                        <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-0">Belum ada riwayat peminjaman yang ditolak
                            atau selesai pada akun Anda.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>