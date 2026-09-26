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
                <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 mt-2 rounded-b-[12px]">
                    <div class="flex items-center gap-4 text-[13px] text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">Per halaman</span>
                            <div x-data="{ 
                                open: false, 
                                selectedVal: '{{ request('per_page', 10) }}',
                                selectItem(val, url) {
                                    this.selectedVal = val;
                                    this.open = false;
                                    window.location.href = url;
                                }
                            }" class="relative" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center justify-between px-3 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm gap-2 min-w-[64px] transition-colors">
                                    <span x-text="selectedVal"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                        :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 bottom-full mb-1 w-full bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden"
                                    style="display: none;">
                                    <div class="p-1">
                                        <button type="button"
                                            @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                        <button type="button"
                                            @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                        <button type="button"
                                            @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-px h-4 bg-slate-200"></div>

                        <p class="font-medium text-slate-500">
                            Menampilkan <span class="font-bold text-slate-800">{{ $riwayats->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $riwayats->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $riwayats->total() }}</span> entri
                        </p>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if ($riwayats->onFirstPage())
                            <button disabled
                                class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ $riwayats->previousPageUrl() }}"
                                class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        @php
                            $startPage = max(1, $riwayats->currentPage() - 1);
                            $endPage = min($riwayats->lastPage(), $riwayats->currentPage() + 1);

                            if ($endPage - $startPage < 2) {
                                if ($startPage == 1) {
                                    $endPage = min($riwayats->lastPage(), 3);
                                } elseif ($endPage == $riwayats->lastPage()) {
                                    $startPage = max(1, $riwayats->lastPage() - 2);
                                }
                            }
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $riwayats->url(1) }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                            @if($startPage > 2)
                                <span
                                    class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                            @endif
                        @endif

                        @foreach ($riwayats->getUrlRange($startPage, $endPage) as $page => $url)
                            @if ($page == $riwayats->currentPage())
                                <span
                                    class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($endPage < $riwayats->lastPage())
                            @if($endPage < $riwayats->lastPage() - 1)
                                <span
                                    class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                            @endif
                            <a href="{{ $riwayats->url($riwayats->lastPage()) }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $riwayats->lastPage() }}</a>
                        @endif

                        @if ($riwayats->hasMorePages())
                            <a href="{{ $riwayats->nextPageUrl() }}"
                                class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <button disabled
                                class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
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
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Riwayat Kosong</h3>
                        <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-0">Belum ada riwayat peminjaman yang ditolak atau selesai pada akun Anda.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>