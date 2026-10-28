<x-eoffice::manajemen-ruangan.layout pageTitle="Peminjaman Saya">

    <div x-data="{
        modalBatal: false,
        cancelUrl: '',
        cancelRoomName: '',
        cancelDate: '',
        openCancelModal(url, room, date) {
            this.cancelUrl = url;
            this.cancelRoomName = room;
            this.cancelDate = date;
            this.modalBatal = true;
            document.body.style.overflow = 'hidden';
        },
        closeCancelModal() {
            this.modalBatal = false;
            this.cancelUrl = '';
            document.body.style.overflow = '';
        },
        showDetail: false,
        detailData: {},
        openDetailModal(data) {
            this.detailData = data;
            this.showDetail = true;
            document.body.style.overflow = 'hidden';
        },
        closeDetailModal() {
            this.showDetail = false;
            this.detailData = {};
            document.body.style.overflow = '';
        }
    }" @keydown.escape.window="closeCancelModal(); closeDetailModal();">

        {{-- =================== PAGE HEADER =================== --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between w-full gap-4 mb-6">
            <div class="min-w-0">
                <h1 class="text-[20px] md:text-[22px] font-bold text-gray-900 tracking-tight">Peminjaman Saya</h1>
                <p class="text-[13px] text-gray-500 mt-1 max-w-2xl leading-relaxed">Pantau status pengajuan yang sedang diproses dan kelola jadwal pemakaian ruangan Anda yang akan datang.</p>
            </div>
            <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
                <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0B266E] border border-[#0B266E] rounded-lg text-[12px] font-semibold text-white hover:bg-[#071946] hover:border-[#071946] transition-all whitespace-nowrap shadow-[0_2px_6px_rgba(11,38,110,0.3)] hover:shadow-[0_4px_12px_rgba(11,38,110,0.4)]">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Pinjam Ruang</span>
                </a>
            </div>
        </div>

        {{-- =================== CONTENT AREA =================== --}}
        @if($peminjamans->count() > 0)
            {{-- Sort Hint --}}
            <div class="flex items-center justify-end text-[12px] text-gray-400 mt-1 mb-1.5 px-0.5">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 8h12M3 12h8" />
                    </svg>
                    Diurutkan berdasarkan jadwal terdekat
                </span>
            </div>

            {{-- Cards List --}}
            <div class="flex flex-col gap-3.5">
                @foreach($peminjamans as $booking)
                    @php
                        $isApproved = strtolower($booking->status) === 'disetujui';
                        $tgl = \Carbon\Carbon::parse($booking->tanggal_pinjam);
                        $berkasUrl = $booking->berkas_pendukung ? \Illuminate\Support\Facades\URL::signedRoute('eoffice.peminjaman.user.saya.berkas', ['id' => $booking->id]) : null;

                        $tglStr = is_string($booking->tanggal_pinjam) ? $booking->tanggal_pinjam : $booking->tanggal_pinjam->format('Y-m-d');
                        $waktuMulai = \Carbon\Carbon::parse("{$tglStr} {$booking->jam_mulai}");
                        $bisaDibatalkan = now()->lt($waktuMulai);
                    @endphp

                    <div class="bg-white rounded-xl border border-[#DFE1E7] hover:border-[#CBD5E1] shadow-sm hover:shadow transition-all overflow-hidden flex flex-col md:flex-row md:items-stretch relative"
                         style="border-left: 4px solid {{ $isApproved ? '#10B981' : '#F59E0B' }};">

                        {{-- 1. Date Block (Left) --}}
                        <div class="flex md:flex-col items-center justify-between md:justify-center p-3 sm:p-4 bg-[#F8FAFC] border-b md:border-b-0 md:border-r border-[#E2E8F0] md:w-[105px] shrink-0 text-center gap-2 md:gap-0.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                {{ $tgl->locale('id')->translatedFormat('l') }}
                            </span>
                            <span class="text-[22px] md:text-[28px] font-black text-slate-900 leading-none my-0.5 font-['Inter_Tight']">
                                {{ $tgl->format('d') }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500">
                                {{ $tgl->locale('id')->translatedFormat('M Y') }}
                            </span>

                            {{-- Mobile Status Badge (visible on mobile inside date header) --}}
                            <div class="md:hidden">
                                @if($isApproved)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#FFF9E6] text-[#B45309] border border-[#FFEBB3]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Detail Body (Center) --}}
                        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between min-w-0 gap-3 bg-white">
                            <div>
                                {{-- Room Title & Badges --}}
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h3 class="text-[15px] sm:text-[16px] font-bold text-gray-900 tracking-tight leading-snug">
                                        {{ $booking->ruangan->nama }}
                                    </h3>
                                    @if($booking->ruangan->lokasi)
                                        <span class="inline-flex items-center text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            {{ $booking->ruangan->lokasi }}@if($booking->ruangan->lantai) · Lt. {{ $booking->ruangan->lantai }}@endif
                                        </span>
                                    @endif
                                    @if($booking->created_by && $booking->created_by !== $booking->user_id)
                                        <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md uppercase tracking-wider">
                                            Didaftarkan TU
                                        </span>
                                    @endif
                                </div>

                                {{-- Purpose / Activity --}}
                                <p class="text-[13px] text-gray-600 font-medium leading-relaxed">
                                    {{ $booking->tujuan }}
                                </p>
                            </div>

                            {{-- Bottom Meta Row: Time & Attachment --}}
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[12px] text-slate-500 pt-2.5 border-t border-slate-100">
                                <div class="flex items-center gap-1.5 font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }} WIB</span>
                                </div>

                                @if($berkasUrl)
                                    <a href="{{ $berkasUrl }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#0B266E] bg-blue-50/70 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-md transition-colors"
                                       title="Lihat Berkas">
                                        <svg class="w-3.5 h-3.5 text-[#0B266E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <span>Berkas</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Status & Action Area (Right) --}}
                        <div class="p-3.5 sm:p-5 flex md:flex-col items-center md:items-end justify-between md:justify-center border-t md:border-t-0 md:border-l border-[#E2E8F0] md:w-[200px] shrink-0 bg-slate-50/40 md:bg-white gap-3">
                            {{-- Desktop Status Badge --}}
                            <div class="hidden md:flex flex-col items-end">
                                @if($isApproved)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                    <span class="text-[10px] text-emerald-600 font-medium mt-1">Ruangan Siap Digunakan</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#FFF9E6] text-[#B45309] border border-[#FFEBB3]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu
                                    </span>
                                    <span class="text-[10px] text-amber-600 font-medium mt-1">Dalam Antrean Review</span>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                                {{-- Tombol Detail --}}
                                <button type="button"
                                        @click='openDetailModal({{ json_encode([
                                            "ruangan" => $booking->ruangan->nama,
                                            "lokasi" => $booking->ruangan->lokasi ?? "Gedung Utama",
                                            "lantai" => (string) ($booking->ruangan->lantai ?? "-"),
                                            "kapasitas" => (string) ($booking->ruangan->kapasitas ?? "-"),
                                            "tanggal" => $tgl->locale("id")->translatedFormat("l, d F Y"),
                                            "jam" => \Carbon\Carbon::parse($booking->jam_mulai)->format("H:i") . " - " . \Carbon\Carbon::parse($booking->jam_selesai)->format("H:i") . " WIB",
                                            "tujuan" => $booking->tujuan,
                                            "telepon" => $booking->nomor_telepon ?? "-",
                                            "status" => $booking->status,
                                            "berkas" => $berkasUrl,
                                            "diajukan_pada" => $booking->created_at->locale("id")->translatedFormat("d M Y, H:i"),
                                        ]) }})'
                                        class="h-[34px] px-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-[12px] font-semibold transition-colors flex items-center gap-1.5 shadow-sm cursor-pointer"
                                        title="Lihat Detail Peminjaman">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>

                                {{-- Tombol Batalkan --}}
                                @if(!in_array(strtolower($booking->status), ['dibatalkan', 'ditolak', 'selesai']))
                                    @if($bisaDibatalkan)
                                        <button type="button"
                                                @click="openCancelModal('{{ route('eoffice.peminjaman.user.saya.batal', $booking->id) }}', '{{ addslashes($booking->ruangan->nama) }}', '{{ $tgl->locale('id')->translatedFormat('d M Y') }}')"
                                                class="h-[34px] px-3 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-red-600 text-[12px] font-semibold transition-colors flex items-center gap-1.5 shadow-sm cursor-pointer"
                                                title="Batalkan Peminjaman">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Batal
                                        </button>
                                    @else
                                        <button type="button"
                                                disabled
                                                class="h-[34px] px-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-[12px] font-semibold flex items-center gap-1.5 cursor-not-allowed opacity-60 shadow-none"
                                                title="Kegiatan sedang berlangsung atau telah dimulai. Hubungi Admin jika ingin mengubah jadwal.">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Batal
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        @else
            {{-- Empty State Card (Matched with menu riwayat .mp-card style) --}}
            <div class="mp-card min-h-[380px] sm:min-h-[420px] flex flex-col items-center justify-center text-center p-8 sm:p-12">
                <div class="font-semibold text-[15px] text-gray-900">Belum Ada Peminjaman Aktif</div>
                <div class="text-[13px] text-gray-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                    Anda tidak memiliki jadwal peminjaman ruangan yang sedang menunggu persetujuan atau yang siap digunakan saat ini.
                </div>
            </div>
        @endif

        {{-- =================== DETAIL MODAL =================== --}}
        <div x-show="showDetail" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6"
            aria-labelledby="detail-title" role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="showDetail" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"
                @click="closeDetailModal()"></div>

            {{-- Modal Panel --}}
            <div x-show="showDetail" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-[20px] rounded-b-none sm:rounded-b-[20px] shadow-2xl w-full max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col border-0 sm:border border-gray-100">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-[#0B266E]/10 flex-shrink-0 flex items-center justify-between bg-[#0B266E]/[0.06] z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center text-white">
                            <span class="material-symbols-outlined" style="font-size:20px">event_available</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight" id="detail-title">Detail Reservasi Peminjaman</h3>
                            <p class="text-[10px] text-[#0B266E] font-medium">Informasi lengkap permohonan ruangan aktif</p>
                        </div>
                    </div>
                    <button type="button" @click="closeDetailModal()"
                        class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                        <span class="material-symbols-outlined" style="font-size:20px">close</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 flex-1 overflow-y-auto bg-white">
                    <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-3 shadow-sm">
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2">
                            INFORMASI PEMINJAMAN
                        </h4>
                        <div class="grid grid-cols-[90px_1fr] sm:grid-cols-[100px_1fr] gap-x-2 gap-y-2.5 text-[13px] border-b border-slate-100 pb-3">
                            <div class="text-slate-500 font-medium">Ruangan</div>
                            <div class="text-slate-900 font-bold" x-text="detailData.ruangan"></div>

                            <div class="text-slate-500 font-medium">Lokasi</div>
                            <div class="text-slate-700 font-medium">
                                <span x-text="detailData.lokasi"></span>
                                <span class="text-slate-400">·</span>
                                <span>Lantai <span x-text="detailData.lantai"></span></span>
                            </div>

                            <div class="text-slate-500 font-medium">Tanggal</div>
                            <div class="text-slate-900 font-bold" x-text="detailData.tanggal"></div>

                            <div class="text-slate-500 font-medium">Waktu</div>
                            <div class="text-slate-900 font-bold text-[#0B266E]" x-text="detailData.jam"></div>

                            <div class="text-slate-500 font-medium">Kegiatan</div>
                            <div class="text-slate-800 font-medium leading-snug" x-text="detailData.tujuan"></div>

                            <div class="text-slate-500 font-medium">No. Telepon / WA</div>
                            <div class="text-slate-800 font-medium" x-text="detailData.telepon"></div>

                            <div class="text-slate-500 font-medium pt-1">Berkas</div>
                            <div class="pt-1">
                                <template x-if="detailData.berkas">
                                    <a :href="detailData.berkas" target="_blank"
                                        class="text-[13px] font-bold text-[#0065ff] hover:text-[#0052cc] hover:underline transition-colors inline-block">
                                        Lihat Lampiran
                                    </a>
                                </template>
                                <template x-if="!detailData.berkas">
                                    <span class="text-gray-400 italic text-[12px]">Tidak dilampirkan</span>
                                </template>
                            </div>
                        </div>

                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2 mt-3">
                            STATUS & PENGAJUAN
                        </h4>
                        <div class="grid grid-cols-[90px_1fr] sm:grid-cols-[100px_1fr] gap-x-2 gap-y-2.5 text-[13px]">
                            <div class="text-slate-500 font-medium flex items-center">Status</div>
                            <div class="flex items-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border inline-block"
                                    :class="{
                                        'bg-[#ECFDF5] text-[#047857] border-[#A7F3D0]': detailData.status && detailData.status.toLowerCase() === 'disetujui',
                                        'bg-[#FFF9E6] text-[#B45309] border-[#FFEBB3]': detailData.status && detailData.status.toLowerCase() === 'menunggu'
                                    }"
                                    x-text="detailData.status">
                                </span>
                            </div>

                            <div class="text-slate-500 font-medium">Diajukan Pada</div>
                            <div class="text-slate-700 font-medium" x-text="detailData.diajukan_pada"></div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-end">
                    <button type="button" @click="closeDetailModal()"
                        class="px-4 py-2 bg-[#0B266E] text-white rounded-lg text-xs font-bold hover:bg-[#071946] transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- =================== CANCEL MODAL =================== --}}
        <div x-show="modalBatal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="modalBatal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" 
                aria-hidden="true" @click="closeCancelModal()"></div>

            {{-- Panel --}}
            <div x-show="modalBatal" 
                x-transition:enter="ease-out duration-300"
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
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 text-center mb-2" id="modal-title">Batalkan Peminjaman?</h3>
                    <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan jadwal peminjaman <strong class="text-gray-800" x-text="cancelRoomName"></strong> pada tanggal <strong class="text-gray-800" x-text="cancelDate"></strong>? Data yang telah dibatalkan tidak dapat dikembalikan.
                    </p>
                    
                    <form method="POST" :action="cancelUrl" class="flex gap-3 w-full">
                        @csrf
                        <button type="button" @click="closeCancelModal()"
                            class="w-1/2 flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                            Kembali
                        </button>
                        <button type="submit"
                            class="w-1/2 flex items-center justify-center px-4 py-2.5 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition-colors cursor-pointer shadow-sm shadow-red-200">
                            Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-eoffice::manajemen-ruangan.layout>