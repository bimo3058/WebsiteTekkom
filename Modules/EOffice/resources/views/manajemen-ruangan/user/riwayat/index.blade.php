<x-eoffice::manajemen-ruangan.layout pageTitle="Riwayat Peminjaman">
<div x-data="{ 
    showDetail: false, 
    selected: {},
    showDelete: false,
    deleteId: null
}" 
@open-detail.window="
    selected = $event.detail; 
    showDetail = true; 
    document.body.style.overflow = 'hidden';
"
@close-detail.window="
    showDetail = false; 
    document.body.style.overflow = '';
">
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
                                <th style="width: 80px; text-align: center;">AKSI</th>
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
                                    <td style="text-align: center;">
                                        <div class="relative inline-flex flex-col items-center justify-center w-full"
                                            x-data="{ showDropdown: false }"
                                            :class="{'z-50': showDropdown, 'z-[1]': !showDropdown}">
                                            
                                            <button type="button" @click="showDropdown = !showDropdown" @click.away="showDropdown = false"
                                                class="inline-flex items-center justify-center w-[32px] h-[32px] rounded-lg border border-[#E2E8F0] bg-white text-[#64748B] hover:bg-[#F8FAFC] transition-colors cursor-pointer">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle></svg>
                                            </button>

                                                @php
                                                    $isBottomRow = $loop->count > 2 && $loop->iteration >= $loop->count - 1;
                                                @endphp
                                                <div x-show="showDropdown" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                    class="absolute right-0 bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5 w-[140px] {{ $isBottomRow ? 'bottom-full mb-2 origin-bottom-right' : 'top-full mt-2 origin-top-right' }}">
                                                    <button type="button" @click='showDropdown = false; $dispatch("open-detail", {{ json_encode([
                                                        "ruangan" => $riwayat->ruangan->nama,
                                                        "tujuan" => $riwayat->tujuan,
                                                        "tanggal" => \Carbon\Carbon::parse($riwayat->tanggal_pinjam)->translatedFormat("l, d F Y"),
                                                        "jam" => \Carbon\Carbon::parse($riwayat->jam_mulai)->format("H:i") . " - " . \Carbon\Carbon::parse($riwayat->jam_selesai)->format("H:i") . " WIB",
                                                        "status" => $riwayat->status,
                                                        "alasan" => $riwayat->alasan_penolakan ?? "",
                                                        "berkas" => $riwayat->berkas_pendukung ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($riwayat->berkas_pendukung, "eoffice") : null,
                                                        "diajukan_pada" => $riwayat->created_at->translatedFormat("d M Y, H:i")
                                                    ]) }})' class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer">
                                                        <svg class="w-[14px] h-[14px] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        Detail
                                                    </button>
                                                    <button type="button" @click='showDropdown = false; showDelete = true; deleteId = {{ $riwayat->id }}' class="w-full text-left px-2.5 py-1.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer mt-1">
                                                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                                            @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                        <button type="button"
                                            @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                        <button type="button"
                                            @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
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

    {{-- Detail Modal --}}
    <div x-show="showDetail" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- 1. BACKDROP OVERLAY -->
        <div x-show="showDetail" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 transition-opacity" 
            aria-hidden="true" @click="$dispatch('close-detail')"></div>

        <!-- 2. MODAL PANEL -->
        <div x-show="showDetail" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-[550px] bg-white rounded-[20px] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 pt-6 pb-2 flex items-start gap-4 relative">
                <div class="w-12 h-12 bg-[#0B266E] rounded-xl flex items-center justify-center flex-shrink-0 text-white shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="pt-1">
                    <h3 class="text-[17px] font-bold text-slate-800 tracking-tight">Detail Peminjaman</h3>
                    <p class="text-[13px] text-[#0B266E] font-medium mt-0.5">
                        Riwayat Informasi
                    </p>
                </div>
                <button @click="$dispatch('close-detail')" class="absolute top-6 right-6 text-[#0B266E] hover:bg-slate-100 transition-colors p-1.5 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 pb-6 pt-4 overflow-y-auto">
                <div class="border border-slate-200 rounded-xl p-5">
                    
                    <!-- INFORMASI PEMINJAMAN -->
                    <div class="text-[12px] font-bold text-[#5c6e9a] uppercase tracking-wider pb-2.5 border-b border-slate-100 mb-3.5">
                        INFORMASI PEMINJAMAN
                    </div>
                    
                    <div class="space-y-2 mb-5">
                        <div class="flex items-start">
                            <div class="w-[130px] flex-shrink-0 text-[12px] text-[#5c6e9a] font-medium">Ruangan</div>
                            <div class="flex-1 text-[12px] font-bold text-slate-800 break-words" x-text="selected.ruangan"></div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-[130px] flex-shrink-0 text-[12px] text-[#5c6e9a] font-medium">Waktu</div>
                            <div class="flex-1 text-[12px] font-bold text-slate-800 flex items-center gap-2 flex-wrap">
                                <span x-text="selected.tanggal"></span>
                                <span class="bg-[#f0f4ff] text-[#0B266E] px-2 py-0.5 rounded-md text-[11px] font-semibold border border-[#dbe4ff]" x-text="selected.jam"></span>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-[130px] flex-shrink-0 text-[12px] text-[#5c6e9a] font-medium">Kegiatan</div>
                            <div class="flex-1 text-[12px] text-slate-800 font-medium leading-relaxed break-words" x-text="selected.tujuan"></div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-[130px] flex-shrink-0 text-[12px] text-[#5c6e9a] font-medium">Berkas</div>
                            <div class="flex-1">
                                <template x-if="selected.berkas">
                                    <a :href="selected.berkas" target="_blank" class="text-[12px] text-[#0B266E] hover:underline font-semibold italic">
                                        Lihat File Terlampir
                                    </a>
                                </template>
                                <template x-if="!selected.berkas">
                                    <span class="text-[12px] text-slate-400 italic">Tidak dilampirkan</span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- STATUS & RIWAYAT -->
                    <div class="text-[12px] font-bold text-[#5c6e9a] uppercase tracking-wider pb-2.5 border-b border-slate-100 mb-3.5">
                        STATUS & RIWAYAT
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-start">
                            <div class="w-[130px] flex-shrink-0 text-[12px] text-[#5c6e9a] font-medium">Status</div>
                            <div class="flex-1">
                                <span class="text-[12px] font-bold tracking-wide uppercase"
                                    :class="{
                                        'text-emerald-600': selected.status && selected.status.toLowerCase() === 'disetujui',
                                        'text-amber-600': selected.status && selected.status.toLowerCase() === 'menunggu',
                                        'text-rose-600': selected.status && (selected.status.toLowerCase() === 'ditolak' || selected.status.toLowerCase() === 'dibatalkan'),
                                        'text-indigo-600': selected.status && selected.status.toLowerCase() === 'selesai'
                                    }" x-text="selected.status ? (selected.status.toLowerCase() === 'disetujui' ? 'SELESAI' : selected.status) : ''">
                                </span>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-[130px] flex-shrink-0 text-[12px] text-[#5c6e9a] font-medium">Diajukan Pada</div>
                            <div class="flex-1 text-[12px] text-slate-800 font-medium break-words" x-text="selected.diajukan_pada"></div>
                        </div>
                    </div>

                    <!-- Alasan Penolakan -->
                    <template x-if="selected.status && (selected.status.toLowerCase() === 'ditolak' || selected.status.toLowerCase() === 'dibatalkan')">
                        <div class="mt-4 bg-rose-50 border border-rose-100 rounded-xl p-4 flex gap-3">
                            <svg class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <h5 class="text-[12px] font-bold text-rose-800">Catatan Penolakan / Pembatalan</h5>
                                <p class="text-[12px] text-rose-700 mt-1 break-words" x-text="selected.alasan || 'Tidak ada catatan tambahan.'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="showDelete" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- 1. BACKDROP OVERLAY -->
        <div x-show="showDelete" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/60 transition-opacity" 
             aria-hidden="true" @click="showDelete = false"></div>

        <!-- Modal Panel -->
        <div x-show="showDelete" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full p-6 mx-4">
            
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Hapus Riwayat?</h3>
                    <div class="mt-2">
                        <p class="text-[13px] text-gray-500">
                            Riwayat ini hanya akan disembunyikan dari daftar Anda. Data peminjaman tetap akan tersimpan aman dalam Arsip Administrasi Kampus.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form :action="`{{ url('eoffice/peminjaman/user/riwayat') }}/${deleteId}/hide`" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#0B266E] text-base font-medium text-white hover:bg-[#071946] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B266E] sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
                <button type="button" @click="showDelete = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

</x-eoffice::manajemen-ruangan.layout>