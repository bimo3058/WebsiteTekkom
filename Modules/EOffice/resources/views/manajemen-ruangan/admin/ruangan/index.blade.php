<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen Ruangan">
    <div x-data="ruanganManager()">
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Manajemen Ruangan</h1>
                <p class="mp-page-sub">Kelola data ruangan fisik yang tersedia untuk dipinjam.</p>
            </div>
            <div class="mp-page-actions">
                <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}" class="mp-btn primary md cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Ruangan
                </a>
            </div>
        </div>



        <div class="bg-white border border-gray-200 rounded-[12px] mt-6"
            style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div
                class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
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
                            class="w-full sm:w-56 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400 cursor-text"
                            placeholder="Search" x-data x-on:input.debounce.700ms="$el.form.submit()">
                    </div>
                </form>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table" style="table-layout: auto; width: 100%;">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                NAMA RUANG</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                GEDUNG</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                KAPASITAS</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                FASILITAS</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                STATUS</th>
                            <th
                                style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                AKSI</th>
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
                                        @php $fstr = implode(', ', $r->fasilitas); @endphp
                                        <div style="max-width: 140px;">
                                            <div class="truncate" style="font-size: 12px; color: #666D80;" title="{{ $fstr }}">
                                                {{ $fstr }}
                                            </div>
                                        </div>
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
                                    <div class="relative inline-flex flex-col items-center justify-center w-full" x-data="{ open: false }" :class="{'z-50': open, 'z-[1]': !open}">
                                        <button type="button" @click="open = !open"
                                            @click.away="open = false"
                                            class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 p-1.5 rounded-md transition-colors cursor-pointer">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>

                                        <div x-show="open" style="display:none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="origin-top-right absolute right-5 top-0 mt-8 bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5 z-20 w-[140px]">

                                            <a href="{{ route('eoffice.peminjaman.admin.ruangan.edit', $r->id) }}"
                                                class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors no-underline cursor-pointer">
                                                <svg class="w-[14px] h-[14px] text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Edit Info
                                            </a>

                                            <form id="delete-form-{{ $r->id }}" method="POST"
                                                action="{{ route('eoffice.peminjaman.admin.ruangan.destroy', $r->id) }}"
                                                style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="confirmDelete('delete-form-{{ $r->id }}', '{{ addslashes($r->nama) }}')"
                                                    class="w-full text-left px-2.5 py-1.5 mt-0.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer">
                                                    <svg class="w-[14px] h-[14px] text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
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
                                    <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}" class="cursor-pointer"
                                        style="color:#0B266E; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">+
                                        Tambah Data Pertama</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (Arsip & Rekap Style) --}}
            <div
                class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center border border-slate-200 rounded-md bg-white overflow-visible text-[13px] shadow-sm">
                        <span class="px-3 py-1.5 text-slate-600 font-medium border-r border-slate-200 bg-slate-50 shrink-0">Per
                            halaman</span>
                            
                        <div x-data="{ 
                            open: false, 
                            value: '{{ request('per_page', 10) }}', 
                            select(val, url) { 
                                this.value = val; 
                                window.location.href = url; 
                            } 
                        }" class="relative w-[65px]" @click.away="open = false">
                            
                            <button type="button" @click="open = !open" 
                                class="w-full flex items-center justify-between px-2.5 py-1.5 text-slate-900 font-bold bg-white hover:bg-slate-50 focus:outline-none transition-colors rounded-r-md cursor-pointer">
                                <span x-text="value"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" 
                                class="absolute left-0 bottom-full mb-1 w-full min-w-[70px] bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden" style="display: none;">
                                <div class="py-1">
                                    <button type="button" @click="select('10', '{{ request()->fullUrlWithQuery(['per_page' => 10]) }}')" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': value == '10'}">10</button>
                                    <button type="button" @click="select('25', '{{ request()->fullUrlWithQuery(['per_page' => 25]) }}')" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': value == '25'}">25</button>
                                    <button type="button" @click="select('50', '{{ request()->fullUrlWithQuery(['per_page' => 50]) }}')" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': value == '50'}">50</button>
                                </div>
                            </div>
                        </div>
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
                            class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    <div
                        class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                        @foreach ($ruangans->getUrlRange(max(1, $ruangans->currentPage() - 2), min($ruangans->lastPage(), $ruangans->currentPage() + 2)) as $page => $url)
                            @if ($page == $ruangans->currentPage())
                                <span
                                    class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors cursor-pointer">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors cursor-pointer">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>

                    @if ($ruangans->hasMorePages())
                        <a href="{{ $ruangans->nextPageUrl() }}"
                            class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors cursor-pointer">
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

        {{-- Modal Delete Confirmation --}}
        <div x-show="showDeleteModal" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity cursor-pointer" aria-hidden="true"
                @click="showDeleteModal = false"></div>

            {{-- Modal Panel --}}
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">

                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                    <svg class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Ruangan</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus <span class="font-bold text-gray-800"
                        x-text="deleteRoomName"></span>? Sistem akan
                    menonaktifkan ruangan ini secara permanen agar tidak lagi bisa dipinjam oleh pengguna.
                </p>

                <div class="flex items-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="flex-1 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeDelete()"
                        class="flex-1 px-4 py-2.5 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition-colors shadow-sm cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div> <!-- Close Alpine Wrapper -->

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('ruanganManager', () => ({
                showDeleteModal: false,
                pendingDeleteFormId: null,
                deleteRoomName: '',

                confirmDelete(formId, roomName) {
                    this.pendingDeleteFormId = formId;
                    this.deleteRoomName = roomName;
                    this.showDeleteModal = true;
                },

                executeDelete() {
                    if (this.pendingDeleteFormId) {
                        document.getElementById(this.pendingDeleteFormId).submit();
                    }
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>