<x-eoffice::manajemen-ruangan.layout pageTitle="Peminjaman Saya">

    <div x-data="peminjamanManager()" @keydown.escape.window="closeModal()">
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Peminjaman Saya</h1>
                <p class="mp-page-sub">Pantau status pengajuan yang sedang diproses dan kelola jadwal pemakaian ruangan
                    Anda
                    yang akan datang.</p>
            </div>
            <div>
                <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                    class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[13px] font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm hover:shadow">
                    Pinjam Ruang
                </a>
            </div>
        </div>

        <div class="mp-card" style="margin-top: 15px;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full"
                style="padding-bottom: 20px;">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">
                    Peminjaman Aktif
                </h2>
            </div>

            @if($peminjamans->count() > 0)
                <div class="mp-table-wrap">
                    <table class="mp-table">
                        <thead>
                            <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                                <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">RUANGAN & KEGIATAN</th>
                                <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">WAKTU PEMAKAIAN</th>
                                <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">LAMPIRAN</th>
                                <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">STATUS</th>
                                <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 80px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamans as $booking)
                                <tr class="mp-table-row transition-colors hover:bg-slate-50/50">
                                    <td style="padding:11px 16px;">
                                        <div style="max-width: 220px;">
                                            <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                                <span class="truncate" title="{{ $booking->ruangan->nama }}">{{ $booking->ruangan->nama }}</span>
                                                @if($booking->created_by && $booking->created_by !== $booking->user_id)
                                                    <span
                                                        class="bg-gray-100 text-gray-500 border border-gray-200 text-[9px] px-1.5 py-0.5 rounded font-bold tracking-wider whitespace-nowrap flex-shrink-0"
                                                        title="Didaftarkan oleh Tata Usaha">Didaftarkan TU</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-gray-500 truncate mt-0.5"
                                                title="{{ $booking->tujuan }}">
                                                {{ $booking->tujuan }}
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:11px 16px;">
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->translatedFormat('d M Y') }}
                                            <span class="text-gray-400 mx-1">•</span>
                                            {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }} WIB
                                        </div>
                                    </td>
                                    <td style="padding:11px 16px;">
                                        @if($booking->berkas_pendukung)
                                            <a href="{{ $booking->berkas_url ?? app(\App\Services\SupabaseStorage::class)->getPublicUrl($booking->berkas_pendukung, 'eoffice') }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 text-[12px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
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
                                    <td style="padding:11px 16px;">
                                        @php
                                            $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                            if (strtolower($booking->status) === 'disetujui')
                                                $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                            elseif (strtolower($booking->status) === 'ditolak')
                                                $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                            elseif (strtolower($booking->status) === 'menunggu')
                                                $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                            elseif (strtolower($booking->status) === 'selesai')
                                                $st = ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'];
                                            elseif (strtolower($booking->status) === 'dibatalkan')
                                                $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                        @endphp
                                        <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                    <td style="padding:11px 16px; text-align: center;">
                                        <div class="flex items-center justify-center">
                                            @if(!in_array(strtolower($booking->status), ['dibatalkan', 'ditolak', 'selesai']))
                                                <button type="button"
                                                    @click="openModal('{{ route('eoffice.peminjaman.user.saya.batal', $booking->id) }}')"
                                                    class="h-8 px-3 rounded-md bg-white border border-red-200 text-[12px] font-medium text-red-600 hover:bg-red-50 hover:border-red-300 shadow-sm transition-all focus:ring-2 focus:ring-offset-1 focus:ring-red-100 cursor-pointer">
                                                    Batal
                                                </button>
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

                {{-- PAGINATION CONTAINER --}}
                <div
                    class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 mt-2 rounded-b-[12px]">
                    <div class="flex flex-col md:flex-row items-center gap-4 text-[13px] text-slate-500 w-full md:w-auto">
                        <div class="flex items-center justify-center gap-2 w-full md:w-auto">
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
                                        :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                        <button type="button"
                                            @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                        <button type="button"
                                            @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden md:block w-px h-4 bg-slate-200"></div>

                        <p class="hidden md:block font-medium text-slate-500">
                            Menampilkan <span class="font-bold text-slate-800">{{ $peminjamans->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $peminjamans->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $peminjamans->total() }}</span> entri
                        </p>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 w-full md:w-auto">
                        @if ($peminjamans->onFirstPage())
                            <button disabled
                                class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ $peminjamans->previousPageUrl() }}"
                                class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        @php
                            $startPage = max(1, $peminjamans->currentPage() - 1);
                            $endPage = min($peminjamans->lastPage(), $peminjamans->currentPage() + 1);

                            if ($endPage - $startPage < 2) {
                                if ($startPage == 1) {
                                    $endPage = min($peminjamans->lastPage(), 3);
                                } elseif ($endPage == $peminjamans->lastPage()) {
                                    $startPage = max(1, $peminjamans->lastPage() - 2);
                                }
                            }
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $peminjamans->url(1) }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                            @if($startPage > 2)
                                <span class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                            @endif
                        @endif

                        @foreach ($peminjamans->getUrlRange($startPage, $endPage) as $page => $url)
                            @if ($page == $peminjamans->currentPage())
                                <span
                                    class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($endPage < $peminjamans->lastPage())
                            @if($endPage < $peminjamans->lastPage() - 1)
                                <span class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                            @endif
                            <a href="{{ $peminjamans->url($peminjamans->lastPage()) }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $peminjamans->lastPage() }}</a>
                        @endif

                        @if ($peminjamans->hasMorePages())
                            <a href="{{ $peminjamans->nextPageUrl() }}"
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
                    <div
                        class="text-center py-20 px-6 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Peminjaman Kosong</h3>
                        <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-6">Anda tidak memiliki pengajuan peminjaman ruangan yang sedang berjalan atau aktif saat ini.</p>
                        <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                            class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[14px] font-semibold px-6 py-[11px] rounded-full transition-colors shadow-md shadow-blue-900/10">Mulai
                            Ajukan Peminjaman</a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Custom Alpine Modal for Cancellation --}}
        <div x-show="modalBatal" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="modalBatal" x-transition.opacity
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity cursor-pointer" @click="closeModal()">
            </div>

            <div x-show="modalBatal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-sm overflow-hidden flex flex-col border border-gray-100 text-center">

                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Batalkan Peminjaman?</h3>
                    <p class="text-[13px] text-gray-500 mb-6">Apakah Anda yakin ingin membatalkan pengajuan peminjaman
                        ruangan ini? Data yang telah dibatalkan tidak dapat dikembalikan atau diproses ulang.</p>

                    <form method="POST" :action="cancelUrl">
                        @csrf
                        <div class="flex gap-3 w-full">
                            <button type="button" @click="closeModal()"
                                class="flex-1 py-2 px-4 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition-colors cursor-pointer">Tutup</button>
                            <button type="submit"
                                class="flex-1 py-2 px-4 bg-red-600 text-white rounded-xl font-medium text-sm hover:bg-red-700 shadow-sm hover:shadow-md transition-all cursor-pointer">Ya,
                                Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Init AlpineJS -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('peminjamanManager', () => ({
                modalBatal: false,
                cancelUrl: '',
                openModal(url) {
                    this.cancelUrl = url;
                    this.modalBatal = true;
                    document.body.style.overflow = 'hidden';
                },
                closeModal() {
                    this.modalBatal = false;
                    this.cancelUrl = '';
                    document.body.style.overflow = '';
                }
            }))
        })
    </script>

</x-eoffice::manajemen-ruangan.layout>