<!-- Data Table Card -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4 w-12 text-center">#</th>
                    <th scope="col" class="px-6 py-4">NIM</th>
                    <th scope="col" class="px-6 py-4">Nama Mahasiswa</th>
                    <th scope="col" class="px-6 py-4">Semester</th>
                    <th scope="col" class="px-6 py-4">Target Wisuda</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pendaftars as $index => $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 text-center text-slate-400 text-xs">
                            {{ $pendaftars->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 font-mono font-semibold text-slate-700 text-xs">{{ $item->nim }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $item->nama_lengkap }}</td>
                        <td class="px-6 py-4 text-slate-600">Semester {{ $item->semester_aktif }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $item->target_wisuda ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if ($item->status_pendaftaran === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Disetujui
                                </span>
                            @elseif ($item->status_pendaftaran === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Detail --}}
                                <button
                                    type="button"
                                    title="Lihat Detail"
                                    onclick="openDetailModal({
                                        nim: '{{ $item->nim }}',
                                        nama: '{{ addslashes($item->nama_lengkap) }}',
                                        semester: '{{ $item->semester_aktif }}',
                                        target_wisuda: '{{ addslashes($item->target_wisuda ?? '-') }}',
                                        status: '{{ $item->status_pendaftaran }}',
                                        tanggal: '{{ $item->created_at->translatedFormat('d F Y, H:i') }}',
                                        catatan: '{{ addslashes($item->catatan_admin ?? '-') }}',
                                        dosen1: '{{ addslashes($item->dosenPembimbing1->name ?? '-') }}',
                                        dosen2: '{{ addslashes($item->dosenPembimbing2->name ?? '-') }}'
                                    })"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </button>

                                {{-- Approve --}}
                                @if ($item->status_pendaftaran !== 'approved')
                                    <form method="POST" action="{{ route('banksoal.pendaftaran.updateStatus', $item->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_pendaftaran" value="approved">
                                        <button type="submit" title="Setujui" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 text-xs font-semibold transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Setuju
                                        </button>
                                    </form>
                                @endif

                                {{-- Reject --}}
                                @if ($item->status_pendaftaran !== 'rejected')
                                    <form method="POST" action="{{ route('banksoal.pendaftaran.updateStatus', $item->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_pendaftaran" value="rejected">
                                        <button type="submit" title="Tolak" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Tolak
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('banksoal.pendaftaran.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Hapus pendaftar {{ addslashes($item->nama_lengkap) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center border-b border-transparent bg-white">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 flex items-center justify-center rounded-2xl mb-4 border border-slate-100 shadow-sm">
                                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-800 tracking-tight">Belum Ada Pendaftar</h3>
                                <p class="text-[13px] text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                                    @if (request('periode_id'))
                                        Tidak ada data pendaftar yang cocok dengan filter yang dipilih.
                                    @else
                                        Pilih periode ujian di atas untuk menampilkan data pendaftar.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[13px] text-slate-500">
        <div>
            Menampilkan
            <strong>{{ $pendaftars instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pendaftars->total() : $totalCount }}</strong>
            keseluruhan data pendaftar
        </div>
        @if ($pendaftars instanceof \Illuminate\Pagination\LengthAwarePaginator && $pendaftars->hasPages())
            <div class="flex gap-2">
                {{-- Previous --}}
                @if ($pendaftars->onFirstPage())
                    <span class="px-3.5 py-1.5 border border-slate-200 bg-white rounded-lg text-slate-300 cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $pendaftars->previousPageUrl() }}" class="px-3.5 py-1.5 border border-slate-200 bg-white rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">Previous</a>
                @endif
                {{-- Next --}}
                @if ($pendaftars->hasMorePages())
                    <a href="{{ $pendaftars->nextPageUrl() }}" class="px-3.5 py-1.5 border border-slate-200 bg-white rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">Next</a>
                @else
                    <span class="px-3.5 py-1.5 border border-slate-200 bg-white rounded-lg text-slate-300 cursor-not-allowed">Next</span>
                @endif
            </div>
        @else
            <div class="flex gap-2">
                <span class="px-3.5 py-1.5 border border-slate-200 bg-white rounded-lg text-slate-300 cursor-not-allowed">Previous</span>
                <span class="px-3.5 py-1.5 border border-slate-200 bg-white rounded-lg text-slate-300 cursor-not-allowed">Next</span>
            </div>
        @endif
    </div>
</div>
