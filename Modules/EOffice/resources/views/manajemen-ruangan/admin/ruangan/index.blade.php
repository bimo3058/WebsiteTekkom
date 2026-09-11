<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen Ruangan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Manajemen Ruangan</h1>
            <p class="mp-page-sub">Kelola data ruangan fisik yang tersedia untuk dipinjam.</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}" class="mp-btn primary md">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Ruangan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 rounded-lg mb-4 mt-4 flex items-center gap-3">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-[12px] mt-6" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Ruangan</h2>

            <form action="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" method="GET"
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
                        placeholder="Search" x-data x-on:input.debounce.700ms="$el.form.submit()">
                </div>
            </form>
        </div>

        <div class="mp-table-wrap">
            <table class="mp-table" style="table-layout: auto; width: 100%;">
                <thead>
                    <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">NAMA RUANG</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">LOKASI / GEDUNG</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">KAPASITAS</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">FASILITAS UTAMA</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">STATUS</th>
                        <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ruangans as $r)
                        <tr class="mp-tr">
                            <td style="font-weight: 600;">{{ $r->nama }}</td>
                            <td>{{ $r->lokasi }} <br><span style="font-size:11px; color:#A4ABB8;">Lt.
                                    {{ $r->lantai ?? '-' }}</span></td>
                            <td>{{ $r->kapasitas }} Orang</td>
                            <td>
                                @if(is_array($r->fasilitas) && count($r->fasilitas) > 0)
                                    <span
                                        style="font-size: 12px; color: #666D80;">{{ implode(', ', array_slice($r->fasilitas, 0, 3)) }}
                                        {{ count($r->fasilitas) > 3 ? '...' : '' }}</span>
                                @else
                                    <span style="font-size: 12px; color: #A4ABB8;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($r->is_active)
                                    <span class="mp-badge success sm">Aktif</span>
                                @else
                                    <span class="mp-badge sm" style="background:#FADAE1; color:#710E21;">Non-aktif</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div class="relative inline-block" x-data="{ open: false }">
                                    <button @click="open = !open" @click.outside="open = false"
                                        class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="19" cy="12" r="2"></circle>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                        style="display: none;"
                                        class="absolute right-0 top-full mt-1.5 bg-white border border-gray-200 rounded-[10px] shadow-[0_8px_24px_rgba(0,0,0,.1)] min-w-[160px] z-[40] overflow-hidden p-1.5">
                                        <a href="{{ route('eoffice.peminjaman.admin.ruangan.edit', $r->id) }}"
                                            class="w-full flex items-center gap-2 px-2.5 py-[7px] rounded-md text-[13px] font-medium text-gray-600 hover:bg-gray-100 transition-colors no-underline">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4C2.89 4 2 4.9 2 6V20C2 21.1 2.9 22 4 22H18C19.1 22 20 21.1 20 20V13M18.5 2.5C19.33 2.5 20 3.17 20 4V4C20.83 4 21.5 4.67 21.5 5.5C21.5 6.33 20.83 7 20 7L11 16L7 17L8 13L17 4C17 3.17 17.67 2.5 18.5 2.5Z"></path>
                                            </svg>
                                            Edit Info
                                        </a>
                                        <div class="h-[1px] bg-gray-100 my-1 mx-1.5"></div>
                                        <form method="POST"
                                            action="{{ route('eoffice.peminjaman.admin.ruangan.destroy', $r->id) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');"
                                            style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full flex items-center gap-2 px-2.5 py-[7px] rounded-md text-[13px] font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors border-0 bg-transparent cursor-pointer text-left">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                                Hapus Ruangan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px; color: #666D80;">
                                Belum ada data ruangan.<br>
                                <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}"
                                    style="color:#0B266E; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">+
                                    Tambah Data Pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (Arsip & Rekap Style) --}}
        <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
            <div class="flex items-center gap-4">
                <div class="flex items-center border border-slate-200 rounded-md bg-white overflow-hidden text-[13px] shadow-sm">
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
                    Menampilkan <span class="font-bold text-slate-800">{{ $ruangans->firstItem() ?? 0 }}</span>
                    sampai <span class="font-bold text-slate-800">{{ $ruangans->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-slate-800">{{ $ruangans->total() }}</span> entri
                </p>
            </div>

            <div class="flex items-center gap-1.5">
                @if ($ruangans->onFirstPage())
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $ruangans->previousPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                <div class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                    @foreach ($ruangans->getUrlRange(max(1, $ruangans->currentPage() - 2), min($ruangans->lastPage(), $ruangans->currentPage() + 2)) as $page => $url)
                        @if ($page == $ruangans->currentPage())
                            <span class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                @if ($ruangans->hasMorePages())
                    <a href="{{ $ruangans->nextPageUrl() }}"
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

</x-eoffice::manajemen-ruangan.layout>