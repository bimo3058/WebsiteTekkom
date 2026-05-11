<!-- Data Table Card -->
<div
    class="bg-white border border-slate-200 rounded-[10px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.08)] relative">
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left text-[14px] text-slate-700 border-collapse">
            <thead
                class="bg-slate-100 border-b-2 border-slate-200 text-[12px] font-bold text-slate-700 uppercase tracking-[0.5px]">
                <tr>
                    <th scope="col" class="px-4 py-[14px] w-12 text-center whitespace-nowrap">No</th>
                    <th scope="col" class="px-4 py-[14px] whitespace-nowrap">NIM</th>
                    <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Nama Mahasiswa</th>
                    <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Semester</th>
                    <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Target Wisuda</th>
                    <th scope="col" class="px-4 py-[14px] whitespace-nowrap">Status</th>
                    <th scope="col" class="px-4 py-[14px] text-center whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendaftars as $index => $item)
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-200 last:border-b-0">
                        <td class="px-4 py-[14px] text-center text-slate-400 text-xs whitespace-nowrap">
                            {{ $pendaftars->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-[14px] font-medium text-slate-800 whitespace-nowrap">
                            {{ $item->nim }}
                        </td>
                        <td class="px-4 py-[14px] font-medium text-slate-800 whitespace-nowrap">{{ $item->nama_lengkap }}
                        </td>
                        <td class="px-4 py-[14px] text-slate-600 whitespace-nowrap">Semester {{ $item->semester_aktif }}
                        </td>
                        <td class="px-4 py-[14px] text-slate-500 whitespace-nowrap">{{ $item->target_wisuda ?? '-' }}</td>
                        <td class="px-4 py-[14px] whitespace-nowrap">
                            @if ($item->status_pendaftaran === 'approved')
                                <span
                                    class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-[0_1px_2px_rgba(0,0,0,0.05)] uppercase tracking-wider">
                                    DISETUJUI
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border border-amber-200 bg-amber-50 text-amber-700 shadow-[0_1px_2px_rgba(0,0,0,0.05)] uppercase tracking-wider">
                                    PENDING
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-[14px] whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Detail — selalu tampil --}}
                                <button type="button" title="Lihat Detail" onclick="openDetailModal({
                                                    nim: '{{ $item->nim }}',
                                                    nama: '{{ addslashes($item->nama_lengkap) }}',
                                                    semester: '{{ $item->semester_aktif }}',
                                                    target_wisuda: '{{ addslashes($item->target_wisuda ?? '-') }}',
                                                    status: '{{ $item->status_pendaftaran }}',
                                                    tanggal: '{{ $item->created_at->translatedFormat('d F Y, H:i') }}',
                                                    dosen1: '{{ addslashes($item->dosenPembimbing1->name ?? '-') }}',
                                                    dosen2: '{{ addslashes($item->dosenPembimbing2->name ?? '-') }}'
                                                })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-transparent text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>

                                @if ($item->status_pendaftaran !== 'approved')
                                    {{-- Approve — hanya tampil jika belum approved --}}
                                    <form method="POST" action="{{ route('banksoal.pendaftaran.updateStatus', $item->id) }}"
                                        class="inline"
                                        onsubmit="return confirm('Setujui pendaftaran {{ addslashes($item->nama_lengkap) }}? Aksi ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_pendaftaran" value="approved">
                                        <button type="submit" title="Setujui"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-transparent text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Tolak & Hapus — gabungan reject + delete --}}
                                    <form method="POST" action="{{ route('banksoal.pendaftaran.destroy', $item->id) }}"
                                        class="inline"
                                        onsubmit="return confirm('Tolak dan hapus pendaftar {{ addslashes($item->nama_lengkap) }}? Data akan dihapus dari daftar.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Tolak & Hapus"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-transparent text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center border-b border-transparent bg-white">
                            <div class="flex flex-col items-center justify-center">
                                <div
                                    class="w-16 h-16 bg-slate-50 flex items-center justify-center rounded-2xl mb-4 border border-slate-100 shadow-sm">
                                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
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

    @if ($pendaftars instanceof \Illuminate\Pagination\LengthAwarePaginator && $pendaftars->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-white">
            {{ $pendaftars->links() }}
        </div>
    @endif
</div>