<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="hover:text-primary transition-colors text-gray-500">Ujian Komprehensif</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">Riwayat Ujian</span>
    @endsection

    
    <style>
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }

        main.overflow-y-auto { overflow: hidden !important; }
        #banksoal-main-content { padding: 0 !important; max-width: 100% !important; height: 100% !important; display: flex; flex-direction: column; }

        .dash-wrap {
            display: flex; flex-direction: column; height: 100%;
            padding: 16px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden; width: 100%; box-sizing: border-box;
        }

        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; width: 100%; box-sizing: border-box;
            padding: 16px 24px;
        }

        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px;
            display: flex; flex-direction: column; gap: 2px;
        }

        .dash-box-body > * {
            flex-shrink: 0;
            width: 100%;
            min-width: 0;
        }

        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .dash-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
            }
            .dash-box {
                border-radius: 10px;
                display: block;
                height: auto;
                overflow: visible;
            }
            .dash-box-header {
                padding: 12px 14px;
                position: sticky; top: 0; z-index: 20;
            }
            .dash-box-body {
                padding: 14px;
                overflow-y: visible;
                display: block;
            }
        }
    </style>

    <div class="dash-wrap">
        <div class="dash-box">

        <!-- Box Header -->
        <div class="dash-box-header">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Riwayat Ujian Komprehensif</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Rekapitulasi nilai akhir mahasiswa yang telah menyelesaikan ujian.</p>
            </div>
            </div>
        </div>

        <!-- Box Body -->
        <div class="dash-box-body">

        {{-- Filter Panel: Alpine.js state is on the outer wrapper div --}}
        <div
            x-data="{
                openFilter: false,
                pendingPeriode: '{{ request('periode_id') }}',
                pendingKeterangan: '{{ request('keterangan') }}',
                activeCount: {{ (request('periode_id') ? 1 : 0) + (request('keterangan') ? 1 : 0) }},
                applyFilters() {
                    const url = new URL(window.location.href);
                    if (this.pendingPeriode) url.searchParams.set('periode_id', this.pendingPeriode);
                    else url.searchParams.delete('periode_id');
                    if (this.pendingKeterangan) url.searchParams.set('keterangan', this.pendingKeterangan);
                    else url.searchParams.delete('keterangan');
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                },
                clearFilters() {
                    this.pendingPeriode = '';
                    this.pendingKeterangan = '';
                    const url = new URL(window.location.href);
                    url.searchParams.delete('periode_id');
                    url.searchParams.delete('keterangan');
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                }
            }"
            @mousedown.outside="openFilter = false"
        >

        {{-- Table Container --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-8 relative">
            {{-- Table Toolbar --}}
            <div class="p-4 sm:px-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <h2 class="text-[15px] font-semibold text-gray-900">Tabel Riwayat Ujian</h2>

                <!-- Filters & Search (Kanan) -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- Search -->
                    <form method="GET" action="{{ route('banksoal.admin.cbt.riwayat') }}" class="relative w-full sm:w-64 flex items-center">
                        @if(request('periode_id'))<input type="hidden" name="periode_id" value="{{ request('periode_id') }}">@endif
                        @if(request('keterangan'))<input type="hidden" name="keterangan" value="{{ request('keterangan') }}">@endif
                        @if(request('per_page'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search mahasiswa..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-primary focus:ring-0 transition-all placeholder:text-gray-400 bg-white text-gray-700">
                    </form>

                    <!-- Filter Button -->
                    <div class="relative">
                        <button @click="openFilter = !openFilter" type="button"
                            class="relative inline-flex items-center gap-2 px-4 py-2 bg-white border rounded-lg text-[13px] font-medium transition-all cursor-pointer"
                            :class="activeCount > 0 ? 'border-primary text-primary bg-primary/5' : 'border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                            <span x-show="activeCount > 0"
                                class="inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold bg-primary text-white rounded-full"
                                x-text="activeCount">
                            </span>
                        </button>

                        {{-- Filter Panel --}}
                        <div x-show="openFilter"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            x-cloak
                            class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

                            {{-- Panel Header --}}
                            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                                <span class="text-[13px] font-semibold text-gray-800">Filter</span>
                                <button @click="openFilter = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Panel Body --}}
                            <div class="px-5 py-4 space-y-4">

                                <!-- Filter Periode Ujian -->
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Periode Ujian</label>
                                    <select x-model="pendingPeriode"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-primary transition-all cursor-pointer bg-white text-gray-700">
                                        <option value="">Semua Periode</option>
                                        @foreach($periodes as $periode)
                                            <option value="{{ $periode->id }}">{{ $periode->nama_periode }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Keterangan -->
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Keterangan</label>
                                    <select x-model="pendingKeterangan"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-primary transition-all cursor-pointer bg-white text-gray-700">
                                        <option value="">Semua Keterangan</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="tidak_lulus">Tidak Lulus</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Panel Footer --}}
                            <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                                <button @click="clearFilters()" type="button"
                                    class="px-4 py-1.5 text-[13px] font-medium text-gray-600 hover:text-gray-800 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                    Clear
                                </button>
                                <button @click="applyFilters()" type="button"
                                    class="px-5 py-1.5 text-[13px] font-semibold bg-primary text-white rounded-lg hover:bg-[#1e2d60] transition-colors shadow-sm cursor-pointer">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(request()->hasAny(['q','periode_id','keterangan']))
                        <a href="{{ route('banksoal.admin.cbt.riwayat') }}"
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 text-[13px] font-medium rounded-lg transition-colors shrink-0">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-[13px] tracking-wide font-semibold">
                            <th class="px-6 py-4 w-10 text-center">No.</th>
                            <th class="px-6 py-4 whitespace-nowrap">NIM</th>
                            <th class="px-6 py-4 whitespace-nowrap">Nama Mahasiswa</th>
                            <th class="px-6 py-4 whitespace-nowrap">Periode Ujian</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Ujian Ke-</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Nilai</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Keterangan</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @forelse($sessions as $index => $session)
                            @php
                                $nim       = $session->user?->student?->student_number ?? '—';
                                $nama      = $session->user?->name ?? '—';
                                $poin      = $session->score;
                                $lulus     = (bool) ($session->lulus ?? false);
                                $ujianKe   = $session->ujian_ke ?? '—';
                                $failedCpls = collect($session->cpl_scores ?? [])
                                    ->where('lulus', false)
                                    ->map(fn ($cpl) => $cpl['kode'].' ('.number_format((float) $cpl['nilai'], 2, '.', '').')')
                                    ->join(', ');
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-gray-400">
                                    {{ $sessions->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-mono whitespace-nowrap">
                                    {{ $nim }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $nama }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[13px] text-gray-800 font-medium whitespace-nowrap">
                                        {{ $session->jadwal?->periode?->nama_periode ?? '—' }}
                                    </p>
                                    @if($session->jadwal)
                                        <p class="text-[11px] text-gray-400 mt-0.5 whitespace-nowrap">
                                            Sesi {{ $session->jadwal->nama_sesi ?? '—' }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[13px] font-medium text-gray-800">
                                        {{ $ujianKe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($poin !== null)
                                        <div style="display:flex; align-items:baseline; justify-content:center; gap:2px;">
                                            <span class="text-[14px] font-bold text-gray-900">{{ (int)$poin }}</span>
                                          
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($poin !== null)
                                        @if($lulus)
                                            <span title="{{ $failedCpls ? 'CPL di bawah 6.0: '.$failedCpls : '' }}" style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:#ECFDF5; color:#059669;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#059669;"></span>
                                                LULUS
                                            </span>
                                        @else
                                            <span title="{{ $failedCpls ? 'CPL di bawah 6.0: '.$failedCpls : '' }}" style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:#FEF2F2; color:#DC2626;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#DC2626;"></span>
                                                TIDAK LULUS
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">Belum dikalkulasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('banksoal.admin.cbt.detail', $session->id) }}"
                                       class="inline-flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:text-primary hover:border-primary px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shadow-sm whitespace-nowrap">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-[13px] font-medium">Belum ada riwayat ujian.</p>
                                        @if(request()->hasAny(['q','periode_id','keterangan']))
                                            <p class="text-gray-400 text-[12px] mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination / Info -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full">
                    <div class="flex items-center gap-2 flex-wrap">
                        <form method="GET" action="{{ route('banksoal.admin.cbt.riwayat') }}" class="flex items-center gap-2">
                            @if(request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                            @if(request('periode_id'))
                                <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">
                            @endif
                            @if(request('keterangan'))
                                <input type="hidden" name="keterangan" value="{{ request('keterangan') }}">
                            @endif

                            <span class="text-[13px] text-gray-700 font-medium whitespace-nowrap">Per page</span>
                            <select name="per_page" onchange="this.form.submit()" class="pl-3 pr-8 py-1.5 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-700 font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer outline-none disabled:bg-gray-50 disabled:cursor-not-allowed">
                                 <option value="5"  {{ request('per_page', 5) == 5  ? 'selected' : '' }}>5</option>
                                 <option value="10" {{ request('per_page', 5) == 10 ? 'selected' : '' }}>10</option>
                                 <option value="25" {{ request('per_page', 5) == 25 ? 'selected' : '' }}>25</option>
                                 <option value="50" {{ request('per_page', 5) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                        @if ($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <span class="text-[13px] text-gray-600 font-medium ml-2">
                            Showing {{ $sessions->firstItem() ?? 0 }} to {{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }} results
                        </span>
                        @endif
                    </div>
                    
                    @if ($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator && $sessions->hasPages())
                    <div class="sm:ml-auto">
                        {{ $sessions->links('pagination::tailwind') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

        </div> {{-- end .dash-box-body --}}
        </div> {{-- end .dash-box --}}

</div>{{-- /end Alpine x-data wrapper --}}

</x-banksoal::layouts.admin>

