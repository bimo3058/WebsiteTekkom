<x-eoffice::manajemen-ruangan.layout pageTitle="Arsip">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Arsip</h1>
            <p class="mp-page-sub">Gudang arsip seluruh data histori pengajuan ruangan yang telah berstatus Selesai.</p>
        </div>
    </div>

    @if(session('success'))
        <div
            class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-[12px] mt-6" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);"
        x-data="filterManager()">
        <div
            class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Arsip Peminjaman</h2>

            <form action="{{ route('eoffice.peminjaman.admin.riwayat.index') }}" method="GET"
                class="flex flex-wrap items-center gap-2.5">
                {{-- Search --}}
                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full sm:w-56 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400"
                        placeholder="Search" x-on:input.debounce.700ms="$el.form.submit()">
                </div>

                {{-- Filter Engine Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="h-[38px] px-3.5 bg-white border border-gray-200 rounded-lg flex items-center gap-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filter
                        @if(request()->hasAny(['status', 'ruangan_id', 'start_date', 'end_date']) && array_filter(request()->only(['status', 'ruangan_id', 'start_date', 'end_date'])))
                            <span class="w-2 h-2 rounded-full bg-indigo-500 absolute -top-0.5 -right-0.5"></span>
                        @endif
                    </button>

                    <div x-show="open" @click.outside="open = false" style="display: none;"
                        class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden transform origin-top-right">
                        <div class="p-4 space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Rentang
                                    Waktu</label>
                                <div class="flex gap-2">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-indigo-500">
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 focus:border-indigo-500 relative z-10 w-full bg-white">Ruangan</label>
                                <select name="ruangan_id"
                                    class="w-full text-xs border border-gray-200 rounded block p-2 outline-none">
                                    <option value="">Semua Ruangan</option>
                                    @foreach($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}" {{ request('ruangan_id') == $ruangan->id ? 'selected' : '' }}>{{ $ruangan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Status</label>
                                <select name="status"
                                    class="w-full text-xs border border-gray-200 rounded block p-2 outline-none focus:border-indigo-500">
                                    <option value="">Semua Status</option>
                                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>
                                        Disetujui</option>
                                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>
                                        Ditolak</option>
                                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>
                                        Dibatalkan</option>
                                </select>
                            </div>
                            <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                                <a href="{{ route('eoffice.peminjaman.admin.riwayat.index') }}"
                                    class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 border border-transparent">Reset</a>
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-[#060E2A] rounded-md hover:bg-[#030715] transition-colors cursor-pointer">Terapkan
                                    Filter</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="mp-table-wrap mt-0 border-t-0">
            <table class="mp-table" style="table-layout: auto; width: 100%;">
                <thead>
                    <tr>
                        <th>PENGAJU</th>
                        <th>RUANGAN & TUJUAN</th>
                        <th>WAKTU ACARA</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $pinjam)
                        <tr class="mp-tr">
                            <td>
                                <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                    {{ $pinjam->user->name ?? 'User Tidak Diketahui' }}
                                    @if($pinjam->created_by && $pinjam->created_by !== $pinjam->user_id)
                                        <span
                                            class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider"
                                            title="Didaftarkan oleh Sistem/Admin">By Admin</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    @if(!empty($pinjam->user->external_id))({{ $pinjam->user->external_id }})
                                    @endif{{ $pinjam->nomor_telepon ?: '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-[13px] font-medium text-[#111827]">
                                    {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                </div>
                                <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                    title="{{ $pinjam->tujuan }}">{{ $pinjam->tujuan }}</div>
                                @if($pinjam->berkas_pendukung)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-[#0065ff] hover:text-[#0052cc] transition-colors mt-1">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                        Lihat Berkas
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="text-[13px] font-medium text-[#111827]">
                                    {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    <span class="text-gray-400 mx-1">•</span>
                                    {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                </div>
                            </td>
                            <td>
                                @php
                                    $style = '';
                                    if (strtolower($pinjam->status) === 'disetujui')
                                        $style = 'bg-emerald-100 text-emerald-800';
                                    elseif (strtolower($pinjam->status) === 'ditolak')
                                        $style = 'bg-red-100 text-red-800';
                                    elseif (strtolower($pinjam->status) === 'menunggu')
                                        $style = 'bg-amber-100 text-amber-800';
                                    elseif (strtolower($pinjam->status) === 'selesai')
                                        $style = 'bg-purple-100 text-purple-800';
                                    else
                                        $style = 'bg-gray-100 text-gray-800';
                                @endphp
                                <span
                                    class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full {{ $style }} text-[12px] font-medium tracking-wide">
                                    {{ ucfirst($pinjam->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr class="mp-tr">
                            <td colspan="4" class="py-12 text-center text-gray-500 text-[13px]">Belum ada arsip peminjaman
                                yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div
            class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
            <div class="flex items-center gap-4">
                <div
                    class="flex items-center border border-slate-200 rounded-md bg-white overflow-hidden text-[13px] shadow-sm">
                    <span class="px-3 py-1.5 text-slate-600 font-medium border-r border-slate-200 bg-slate-50">Per
                        halaman</span>
                    <select aria-label="Per halaman" onchange="window.location.href=this.value"
                        class="px-2.5 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat"
                        style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;">
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <p class="text-xs font-medium text-slate-600">
                    Menampilkan <span class="font-bold text-slate-800">{{ $peminjamans->firstItem() ?? 0 }}</span>
                    sampai <span class="font-bold text-slate-800">{{ $peminjamans->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-slate-800">{{ $peminjamans->total() }}</span> entri
                </p>
            </div>

            <div class="flex items-center gap-1.5">
                @if ($peminjamans->onFirstPage())
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $peminjamans->previousPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                <div
                    class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                    @foreach ($peminjamans->getUrlRange(max(1, $peminjamans->currentPage() - 2), min($peminjamans->lastPage(), $peminjamans->currentPage() + 2)) as $page => $url)
                        @if ($page == $peminjamans->currentPage())
                            <span
                                class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                @if ($peminjamans->hasMorePages())
                    <a href="{{ $peminjamans->nextPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script>
        function filterManager() {
            return {};
        }
    </script>
</x-eoffice::manajemen-ruangan.layout>