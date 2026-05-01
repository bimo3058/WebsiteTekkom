<div>


    <!-- Search & Filter Area -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm text-slate-500 font-medium">
        
        <!-- Show Entries (Kiri) -->
        <div class="flex items-center gap-2">
            <span>Show</span>
            <select wire:model.live="perPage" class="pl-3 pr-8 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-slate-600 cursor-pointer shadow-sm">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>entries</span>
        </div>

        <!-- Search (Kanan) -->
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..." class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 shadow-sm">
        </div>
        
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-200 rounded-[10px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.08)] relative">

        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-[14px] text-slate-700 border-collapse">
                <thead class="bg-slate-100 border-b-2 border-slate-200 text-[12px] font-bold text-slate-700 uppercase tracking-[0.5px]">
                    <tr>
                        <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Nama Periode</th>
                        <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Timeline Pendaftaran</th>
                        <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Rentang Ujian</th>
                        <th scope="col" class="px-4 py-[14px] whitespace-nowrap text-center">Status</th>
                        <th scope="col" class="px-4 py-[14px] whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodes as $periode)
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-200 last:border-b-0">
                        <td class="px-4 py-[14px] whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $periode->nama_periode }}</span>
                        </td>
                        <td class="px-4 py-[14px] whitespace-nowrap text-slate-700">
                            {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-[14px] whitespace-nowrap text-slate-700">
                            @if($periode->tanggal_mulai_ujian && $periode->tanggal_selesai_ujian)
                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                            @else
                                <span class="text-slate-400 italic">Belum diatur</span>
                            @endif
                        </td>
                        <td class="px-4 py-[14px] whitespace-nowrap text-center">
                            @php
                                $now = now();
                                $tglMulai = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
                                $tglSelesai = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();
                            @endphp
                            @if($periode->status === 'selesai')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 tracking-wide">SELESAI</span>
                            @elseif($now->lt($tglMulai))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-bold bg-slate-100 text-slate-500 tracking-wide border border-slate-200">
                                    DRAFT &middot; Buka {{ $periode->tanggal_mulai->translatedFormat('d M') }}
                                </span>
                            @elseif($now->gt($tglSelesai))
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-slate-100 text-slate-600 tracking-wide border border-slate-300">DAFTAR TUTUP</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-bold bg-blue-500 text-white tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    AKTIF &middot; DAFTAR BUKA
                                </span>
                            @endif
                            {{-- Badge sekunder: Pendaftaran ditutup paksa --}}
                            @if($periode->pendaftaran_ditutup_paksa && $periode->status === 'aktif')
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white tracking-wide">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                        DAFTAR DITUTUP
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-[14px] whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                @php
                                    $periodeData = [
                                        'id' => $periode->id,
                                        'nama_periode' => $periode->nama_periode,
                                        'tanggal_mulai' => $periode->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d') : null,
                                        'tanggal_selesai' => $periode->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d') : null,
                                        'tanggal_mulai_ujian' => $periode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->format('Y-m-d') : null,
                                        'tanggal_selesai_ujian' => $periode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->format('Y-m-d') : null,
                                        'status' => $periode->status,
                                        'deskripsi' => $periode->deskripsi,
                                    ];
                                @endphp
                                <button type="button" @click="editData = {{ json_encode($periodeData) }}; editModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#3B82F6] hover:bg-[#2563EB] text-white transition-all" title="Edit">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                {{-- Tombol Tutup Pendaftaran Darurat --}}
                                @if($periode->pendaftaran_terbuka)
                                <button type="button" wire:click="closePendaftaran({{ $periode->id }})" wire:confirm="Tutup pendaftaran untuk periode ini sekarang?\nMahasiswa tidak dapat mendaftar lagi meskipun tanggal belum berakhir." class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#F59E0B] hover:bg-[#D97706] text-white transition-all" title="Tutup Pendaftaran">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </button>
                                @endif
                                
                                @php
                                    $hasPendaftar = \Modules\BankSoal\Models\PendaftarUjian::where('periode_ujian_id', $periode->id)->exists();
                                @endphp
                                @if(!$hasPendaftar)
                                <button type="button" wire:click="deletePeriode({{ $periode->id }})" wire:confirm="Apakah Anda yakin ingin menghapus periode ini? Aksi ini tidak dapat dibatalkan." class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#EF4444] hover:bg-[#DC2626] text-white transition-all" title="Hapus">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="text-slate-400 text-[11px] font-medium italic" title="Periode ini memiliki data pendaftar dan tidak dapat dihapus">Terkunci</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <!-- Empty State Row -->
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center border-b border-transparent">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-50 flex items-center justify-center rounded-full mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 mb-1">Tidak ada data periode</h3>
                                <p class="text-[13px] text-slate-500 max-w-sm">Buat periode ujian komprehensif baru untuk mulai membuka pendaftaran bagi mahasiswa.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($periodes->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $periodes->links() }}
        </div>
        @endif
    </div>
</div>
