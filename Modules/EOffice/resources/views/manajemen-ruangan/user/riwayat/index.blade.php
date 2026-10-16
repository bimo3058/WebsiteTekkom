<x-eoffice::manajemen-ruangan.layout pageTitle="Riwayat Peminjaman">
    <div x-data="{ 
    showDetail: false, 
    selected: {},
    showDelete: false,
    deleteId: null
}" @open-detail.window="
    selected = $event.detail; 
    showDetail = true; 
    document.body.style.overflow = 'hidden';
" @close-detail.window="
    showDetail = false; 
    document.body.style.overflow = '';
">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between w-full gap-4 mb-5">
            <div class="min-w-0">
                <h1 class="text-[20px] md:text-[22px] font-bold text-gray-900 tracking-tight">Riwayat Peminjaman</h1>
                <p class="text-[13px] text-gray-500 mt-1 max-w-2xl leading-relaxed">Daftar arsip seluruh pengajuan peminjaman ruangan Anda yang telah selesai, ditolak, atau dibatalkan.</p>
            </div>
        </div>

        <div class="mp-card min-h-[380px] sm:min-h-[420px]" style="margin-top: 15px;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">
                    Riwayat Peminjaman
                </h2>
            </div>

            @if($riwayats->count() > 0)
                <div class="mp-table-wrap">
                    <table class="mp-table">
                        <thead>
                            <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    RUANGAN & KEGIATAN</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    WAKTU PEMAKAIAN</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    KETERANGAN</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    STATUS</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 80px;">
                                    AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayats as $riwayat)
                                                <tr class="mp-table-row transition-colors hover:bg-slate-50/50">
                                                    <td style="padding:11px 16px;">
                                                        <div style="max-width: 220px;">
                                                            <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                                                <span class="truncate"
                                                                    title="{{ $riwayat->ruangan->nama }}">{{ $riwayat->ruangan->nama }}</span>
                                                                @if($riwayat->created_by && $riwayat->created_by !== $riwayat->user_id)
                                                                    <span
                                                                        class="bg-gray-100 text-gray-500 border border-gray-200 text-[9px] px-1.5 py-0.5 rounded font-bold tracking-wider whitespace-nowrap flex-shrink-0"
                                                                        title="Didaftarkan oleh Tata Usaha">Didaftarkan TU</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-[11px] text-gray-500 truncate mt-0.5"
                                                                title="{{ $riwayat->tujuan }}">
                                                                {{ $riwayat->tujuan }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="padding:11px 16px;">
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
                                                    <td style="padding:11px 16px;">
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
                                                    <td style="padding:11px 16px;">
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
                                                        <span
                                                            style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                                            {{ $statusText }}
                                                        </span>
                                                    </td>
                                                    <td style="padding:11px 16px; text-align: center;">
                                                        <div class="relative inline-flex flex-col items-center justify-center w-full"
                                                            x-data="{ showDropdown: false }"
                                                            :class="{'z-50': showDropdown, 'z-[1]': !showDropdown}">

                                                            <button type="button" @click="showDropdown = !showDropdown"
                                                                @click.away="showDropdown = false"
                                                                class="inline-flex items-center justify-center w-[32px] h-[32px] rounded-lg border border-[#E2E8F0] bg-white text-[#64748B] hover:bg-[#F8FAFC] transition-colors cursor-pointer">
                                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                                                    <circle cx="5" cy="12" r="1.5"></circle>
                                                                    <circle cx="12" cy="12" r="1.5"></circle>
                                                                    <circle cx="19" cy="12" r="1.5"></circle>
                                                                </svg>
                                                            </button>

                                                            @php
                                                                $isBottomRow = $loop->count > 2 && $loop->iteration >= $loop->count - 1;
                                                            @endphp
                                                            <div x-show="showDropdown" x-cloak
                                                                x-transition:enter="transition ease-out duration-100"
                                                                x-transition:enter-start="opacity-0 scale-95"
                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                x-transition:leave="transition ease-in duration-75"
                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                x-transition:leave-end="opacity-0 scale-95"
                                                                class="absolute right-0 bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5 w-[140px] {{ $isBottomRow ? 'bottom-full mb-2 origin-bottom-right' : 'top-full mt-2 origin-top-right' }}">
                                                                <button type="button" @click='showDropdown = false; $dispatch("open-detail", {{ json_encode([
                                    "ruangan" => $riwayat->ruangan->nama,
                                    "tujuan" => $riwayat->tujuan,
                                    "tanggal" => \Carbon\Carbon::parse($riwayat->tanggal_pinjam)->translatedFormat("l, d F Y"),
                                    "jam" => \Carbon\Carbon::parse($riwayat->jam_mulai)->format("H:i") . " - " . \Carbon\Carbon::parse($riwayat->jam_selesai)->format("H:i") . " WIB",
                                    "status" => $riwayat->status,
                                    "alasan" => $riwayat->alasan_penolakan ?? "",
                                    "berkas" => $riwayat->berkas_pendukung ? \Illuminate\Support\Facades\URL::signedRoute('eoffice.peminjaman.user.saya.berkas', ['id' => $riwayat->id]) : null,
                                    "diajukan_pada" => $riwayat->created_at->translatedFormat("d M Y, H:i"),
                                    "nama" => auth()->user()->name
                                ]) }})' class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer">
                                                                    <svg class="w-[14px] h-[14px] text-gray-600" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                        </path>
                                                                    </svg>
                                                                    Detail
                                                                </button>
                                                                <button type="button"
                                                                    @click='showDropdown = false; showDelete = true; deleteId = {{ $riwayat->id }}'
                                                                    class="w-full text-left px-2.5 py-1.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer mt-1">
                                                                    <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </div>
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
                            Menampilkan <span class="font-bold text-slate-800">{{ $riwayats->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $riwayats->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $riwayats->total() }}</span> entri
                        </p>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 w-full md:w-auto">
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
                                <span class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
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
                                <span class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
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
                <div class="flex-1 flex flex-col items-center justify-center text-center px-6 py-16">
                    <div class="font-semibold text-[15px] text-gray-900">Belum Ada Riwayat Peminjaman</div>
                    <div class="text-[13px] text-gray-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                        Arsip pengajuan peminjaman ruangan yang telah selesai, ditolak, atau dibatalkan akan otomatis tercatat di sini.
                    </div>
                </div>
            @endif
        </div>

        {{-- Detail Modal --}}
        <div x-show="showDetail" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- 1. BACKDROP OVERLAY -->
            <div x-show="showDetail" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"
                @click="$dispatch('close-detail')"></div>

            <!-- 2. MODAL PANEL -->
            <div x-show="showDetail" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-[20px] rounded-b-none sm:rounded-b-[20px] shadow-2xl w-full max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col border-0 sm:border border-gray-100">

                <!-- Modal Header -->
                <div
                    class="px-6 py-4 border-b border-[#0B266E]/10 flex-shrink-0 flex items-center justify-between bg-[#0B266E]/[0.06] z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center">
                            <span class="material-symbols-outlined text-white" style="font-size:20px">description</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Detail Riwayat Peminjaman</h3>
                            <p class="text-[10px] text-[#0B266E] font-medium">Riwayat peminjaman dari <span class="font-bold"
                                    x-text="selected.nama || '{{ auth()->user()->name }}'"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('close-detail')"
                        class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                        <span class="material-symbols-outlined" style="font-size:20px">close</span>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 flex-1 overflow-y-auto bg-white">
                    <!-- Ringkasan Informasi -->
                    <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-3 shadow-sm">

                        <!-- Informasi Peminjaman -->
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2">
                            INFORMASI PEMINJAMAN
                        </h4>
                        <div class="grid grid-cols-[80px_1fr] sm:grid-cols-[90px_1fr] gap-x-2 gap-y-2 text-[13px] mb-3 border-b border-slate-100 pb-3">
                            <div class="text-slate-500 font-medium">Ruangan</div>
                            <div class="text-slate-800 font-bold" x-text="selected.ruangan"></div>

                            <div class="text-slate-500 font-medium">Tanggal</div>
                            <div class="text-slate-800 font-bold" x-text="selected.tanggal"></div>

                            <div class="text-slate-500 font-medium">Waktu</div>
                            <div class="text-slate-800 font-bold" x-text="selected.jam"></div>

                            <div class="text-slate-500 font-medium">Kegiatan</div>
                            <div class="text-slate-800 font-medium leading-snug" x-text="selected.tujuan"></div>

                            <div class="text-slate-500 font-medium pt-1">Berkas</div>
                            <div class="pt-1">
                                <template x-if="selected.berkas">
                                    <a :href="selected.berkas" target="_blank"
                                        class="text-[13px] font-bold text-[#0065ff] hover:text-[#0052cc] hover:underline transition-colors mt-0.5 inline-block">
                                        Lihat Berkas
                                    </a>
                                </template>
                                <template x-if="!selected.berkas">
                                    <span class="text-gray-400 italic text-[12px]">Tidak dilampirkan</span>
                                </template>
                            </div>
                        </div>

                        <!-- Status & Riwayat -->
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2 mt-3">
                            STATUS & RIWAYAT
                        </h4>
                        <div class="grid grid-cols-[80px_1fr] sm:grid-cols-[90px_1fr] gap-x-2 gap-y-2 text-[13px]">
                            <div class="text-slate-500 font-medium flex items-center">Status Akhir</div>
                            <div class="flex items-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border inline-block"
                                    :class="{
                                        'bg-[#ECFDF5] text-[#047857] border-[#A7F3D0]': selected.status && selected.status.toLowerCase() === 'disetujui',
                                        'bg-[#FFF1F2] text-[#9D174D] border-[#FECDD3]': selected.status && (selected.status.toLowerCase() === 'ditolak' || selected.status.toLowerCase() === 'dibatalkan'),
                                        'bg-[#FFF9E6] text-[#B45309] border-[#FFEBB3]': selected.status && selected.status.toLowerCase() === 'menunggu',
                                        'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]': selected.status && selected.status.toLowerCase() === 'selesai'
                                    }"
                                    x-text="selected.status ? (selected.status.toLowerCase() === 'disetujui' ? 'SELESAI' : selected.status) : ''">
                                </span>
                            </div>

                            <template x-if="selected.status && (selected.status.toLowerCase() === 'ditolak' || selected.status.toLowerCase() === 'dibatalkan')">
                                <div class="col-span-full grid grid-cols-[80px_1fr] sm:grid-cols-[90px_1fr] gap-x-2 gap-y-2">
                                    <div class="text-slate-500 font-medium" x-text="selected.status.toLowerCase() === 'dibatalkan' ? 'Alasan Batal' : 'Alasan Tolak'"></div>
                                    <div class="text-red-600 font-semibold leading-relaxed" 
                                        x-text="selected.alasan || 'Tidak ada keterangan tambahan.'"></div>
                                </div>
                            </template>

                            <div class="text-slate-500 font-medium">Diajukan</div>
                            <div class="text-slate-800 font-medium" x-text="selected.diajukan_pada"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <div x-show="showDelete" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- 1. BACKDROP OVERLAY (Latar Belakang Gelap) -->
            <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" @click="showDelete = false">
            </div>

            <!-- 2. MODAL PANEL (Kotak Utama Modal) -->
            <div x-show="showDelete" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-md max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-hidden">

                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 text-center mb-2" id="modal-title">Hapus Riwayat?</h3>
                    <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">Riwayat ini akan dihapus dari
                        daftar Anda, namun data peminjaman tetap tersimpan di arsip kampus.</p>

                    <form :action="`{{ url('eoffice/peminjaman/user/riwayat') }}/${deleteId}/hide`" method="POST"
                        class="flex gap-3 w-full">
                        @csrf
                        <button type="button" @click="showDelete = false"
                            class="w-1/2 flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-1/2 flex items-center justify-center px-4 py-2.5 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition-colors cursor-pointer shadow-sm shadow-red-200">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>