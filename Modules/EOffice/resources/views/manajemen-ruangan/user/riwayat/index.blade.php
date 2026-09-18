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
                                        <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                            {{ $riwayat->ruangan->nama }}
                                            @if($riwayat->created_by && $riwayat->created_by !== $riwayat->user_id)
                                                <span
                                                    class="bg-gray-100 text-gray-500 border border-gray-200 text-[9px] px-1.5 py-0.5 rounded font-bold tracking-wider whitespace-nowrap"
                                                    title="Didaftarkan oleh Tata Usaha">Didaftarkan TU</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                            title="{{ $riwayat->tujuan }}">
                                            {{ $riwayat->tujuan }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            {{ \Carbon\Carbon::parse($riwayat->tanggal_pinjam)->translatedFormat('d M Y') }}
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
                                            $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                            if (strtolower($riwayat->status) === 'disetujui')
                                                $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                            elseif (strtolower($riwayat->status) === 'ditolak')
                                                $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                            elseif (strtolower($riwayat->status) === 'menunggu')
                                                $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                            elseif (strtolower($riwayat->status) === 'selesai')
                                                $st = ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'];
                                            elseif (strtolower($riwayat->status) === 'dibatalkan')
                                                $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                                
                                            $statusText = strtolower($riwayat->status) === 'disetujui' ? 'selesai' : $riwayat->status;
                                        @endphp
                                        <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($riwayat->berkas_pendukung)
                                            <a href="{{ $riwayat->berkas_url ?? app(\App\Services\SupabaseStorage::class)->getPublicUrl($riwayat->berkas_pendukung, 'eoffice') }}"
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

                {{-- Pagination (Arsip & Rekap Style) --}}
                <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border border-slate-200 rounded-md bg-white overflow-hidden text-[13px] shadow-sm">
                            <span class="px-3 py-1.5 text-slate-600 font-medium border-r border-slate-200 bg-slate-50">Per halaman</span>
                            <select aria-label="Per halaman" onchange="window.location.href=this.value" class="px-2.5 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;">
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        <p class="text-xs font-medium text-slate-600">
                            Menampilkan <span class="font-bold text-slate-800">{{ $riwayats->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $riwayats->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $riwayats->total() }}</span> entri
                        </p>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if ($riwayats->onFirstPage())
                            <button disabled class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ $riwayats->previousPageUrl() }}" class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        <div class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                            @foreach ($riwayats->getUrlRange(max(1, $riwayats->currentPage() - 2), min($riwayats->lastPage(), $riwayats->currentPage() + 2)) as $page => $url)
                                @if ($page == $riwayats->currentPage())
                                    <span class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        @if ($riwayats->hasMorePages())
                            <a href="{{ $riwayats->nextPageUrl() }}" class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <button disabled class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif
                    </div>
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