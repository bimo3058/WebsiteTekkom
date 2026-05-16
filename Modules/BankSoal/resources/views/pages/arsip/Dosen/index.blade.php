<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Arsip Soal</span>
    @endsection

    <style>
        :root {
            --navy: #0B266E;
            --navy-light: rgba(11, 38, 110, 0.1);
        }
        .bg-navy { background-color: var(--navy); }
        .text-navy { color: var(--navy); }
        .border-navy { border-color: var(--navy); }
        .shadow-navy { --tw-shadow-color: rgba(11, 38, 110, 0.2); }
        
        @keyframes popup {
            0% { opacity: 0; transform: scale(0.95) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-popup {
            animation: popup 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Loading Spinner */
        #global-loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--navy-light);
            border-top: 4px solid var(--navy);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Global Loader Overlay -->
    <div id="global-loader">
        <div class="flex flex-col items-center gap-3">
            <div class="spinner"></div>
            <p class="text-sm font-bold text-navy">Memproses data...</p>
        </div>
    </div>

    <x-banksoal::notification.alerts />

    <x-banksoal::ui.page-header title="Arsip Soal Dosen" subtitle="Kelola riwayat penarikan dan arsip final dokumen ujian Anda.">
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-xl bg-navy px-5 py-2.5 font-bold text-white shadow-lg shadow-navy/20 transition-all hover:opacity-90 active:scale-95">
                        <i class="fas fa-plus-circle"></i> Tambah Arsip <i class="fas fa-chevron-down text-[10px] ml-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-slate-100 rounded-2xl border border-slate-100 bg-white shadow-xl z-50 overflow-hidden">
                        <div class="py-1">
                            <button type="button" class="group flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-navy" onclick="openModal('uploadPdfModal')">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-500 group-hover:bg-rose-100">
                                    <i class="fas fa-file-pdf"></i>
                                </span>
                                <div>
                                    <p>Upload PDF</p>
                                    <p class="text-[10px] text-slate-400 font-normal">Format .pdf standar</p>
                                </div>
                            </button>
                            <button type="button" class="group flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-emerald-700" onclick="openModal('uploadCsvModal')">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 group-hover:bg-emerald-100">
                                    <i class="fas fa-file-csv"></i>
                                </span>
                                <div>
                                    <p>Upload CSV/Excel</p>
                                    <p class="text-[10px] text-slate-400 font-normal">Import massal soal</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <x-banksoal::ui.stat-card label="Total Arsip" :value="$stats['total_arsip']" icon="fa-archive" tone="blue" />
        <x-banksoal::ui.stat-card label="Riwayat Penarikan" :value="$stats['total_penarikan']" icon="fa-history" tone="amber" />
        <x-banksoal::ui.stat-card label="Mata Kuliah" :value="$stats['mata_kuliah']" icon="fa-book" tone="indigo" />
    </div>

    <div class="space-y-8 flex flex-col">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden order-1" x-data="{ expandedGroups: [] }">
            <div class="px-8 py-6 border-b border-slate-100 bg-white">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-navy text-white shadow-lg shadow-navy/20">
                                    <i class="fas fa-archive text-sm"></i>
                                </span>
                                Daftar Arsip Final
                            </h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-navy/5 text-navy text-xs font-bold">{{ $stats['total_arsip'] }} Arsip</span>
                    </div>

                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <form action="{{ route('banksoal.arsip.dosen.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full" id="filterForm">
                            <div class="relative w-full md:w-96">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari nama arsip atau MK..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-navy/5 focus:border-navy transition-all outline-none">
                            </div>

                            <div class="relative w-full md:w-auto" x-data="{ filterOpen: false }" @click.away="filterOpen = false">
                                <button @click="filterOpen = !filterOpen" type="button" class="flex items-center justify-center gap-2 w-full md:w-auto px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all">
                                    <i class="fas fa-filter text-slate-400"></i>
                                    Filter
                                    <i class="fas fa-chevron-down text-[10px] transition-transform" :class="filterOpen ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute left-0 md:right-0 md:left-auto mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Tahun Ajaran</label>
                                        <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                                            @foreach($availableYears as $year)
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                                <input type="checkbox" name="years[]" value="{{ $year }}" {{ in_array($year, (array)request('years')) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-navy focus:ring-navy transition-all">
                                                <span class="text-sm text-slate-700 group-hover:text-navy transition-colors">{{ $year }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-slate-100">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Semester</label>
                                        <div class="space-y-2">
                                            @foreach(['Ganjil', 'Genap'] as $sem)
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                                <input type="checkbox" name="semesters[]" value="{{ $sem }}" {{ in_array($sem, (array)request('semesters')) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-navy focus:ring-navy transition-all">
                                                <span class="text-sm text-slate-700 group-hover:text-navy transition-colors">{{ $sem }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex gap-2 pt-2">
                                        <button type="button" @click="filterOpen = false" class="flex-1 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">Tutup</button>
                                        <button type="submit" class="flex-1 py-2 rounded-lg bg-navy text-white text-xs font-bold hover:opacity-90 shadow-md shadow-navy/20 transition-all">Terapkan</button>
                                    </div>
                                </div>
                            </div>

                            @if($filters['search'] || request('years') || request('semesters'))
                            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-rose-500 hover:text-rose-700 text-xs font-bold underline px-2">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <th class="w-12 px-8 py-4"></th>
                                <th class="px-4 py-4">Mata Kuliah</th>
                                <th class="px-8 py-4">Jumlah Arsip</th>
                                <th class="px-8 py-4">Status</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $arsipGroups = $arsipPaginated->groupBy('mk_id'); ?>
                            <?php if($arsipGroups->isNotEmpty()): ?>
                                <?php foreach($arsipGroups as $mkId => $items): ?>
                                <?php $first = $items->first(); ?>
                                <tr class="hover:bg-slate-50/50 cursor-pointer transition-colors group" @click="expandedGroups.includes({{ $mkId }}) ? expandedGroups = expandedGroups.filter(i => i !== {{ $mkId }}) : expandedGroups.push({{ $mkId }})">
                                    <td class="px-8 py-5 text-center">
                                        <i class="fas fa-chevron-right text-slate-300 transition-transform duration-300" :class="expandedGroups.includes({{ $mkId }}) ? 'rotate-90 text-navy' : ''"></i>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-navy group-hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-book text-sm"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-900 text-base">{{ $first->mataKuliah->nama }}</span>
                                                <span class="text-xs text-slate-400 font-medium tracking-wide uppercase">{{ $first->mataKuliah->kode }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full bg-navy/5 text-navy text-xs font-bold">{{ $items->count() }} Versi</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-600 border border-emerald-100 uppercase tracking-wider">
                                            <i class="fas fa-check-circle text-[8px]"></i> Aktif
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="flex -space-x-2">
                                                <?php foreach($items->unique('dosen_id')->take(3) as $a): ?>
                                                    <div class="h-8 w-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 overflow-hidden shadow-sm" title="{{ $a->dosen->name ?? 'Dosen' }}">
                                                        {{ substr($a->dosen->name ?? 'D', 0, 1) }}
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <i class="fas fa-ellipsis-v text-slate-300 group-hover:text-slate-600 transition-colors p-2"></i>
                                        </div>
                                    </td>
                                </tr>

                                <?php foreach($items as $arsip): ?>
                                <?php
                                    preg_match('/\((.*?)\)/', $arsip->nama_arsip, $matches);
                                    $categoryAbbr = $matches[1] ?? (strpos($arsip->nama_arsip, 'UTS') !== false ? 'UTS' : (strpos($arsip->nama_arsip, 'UAS') !== false ? 'UAS' : 'Arsip'));
                                ?>
                                <tr x-show="expandedGroups.includes({{ $mkId }})" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-50/30">
                                    <td class="px-8 py-0"></td>
                                    <td colspan="4" class="px-4 py-3">
                                        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:border-navy/30 transition-colors">
                                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                                                <div class="flex flex-col">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pengarsip</p>
                                                    <div class="flex items-center gap-2">
                                                        <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-inner">
                                                            {{ substr($arsip->dosen->name ?? 'D', 0, 1) }}
                                                        </div>
                                                        <p class="text-sm font-bold text-slate-800 truncate" title="{{ $arsip->dosen->name ?? '-' }}">
                                                            {{ $arsip->dosen->name ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kategori</p>
                                                    <span class="inline-flex w-fit px-2.5 py-0.5 rounded-lg bg-navy/5 text-navy text-[10px] font-bold border border-navy/10">
                                                        {{ $categoryAbbr }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Periode</p>
                                                    <p class="text-sm text-slate-700 font-medium">{{ $arsip->tahun_akademik }} - <span class="text-navy text-xs">{{ $arsip->semester }}</span></p>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Statistik</p>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-list-ol text-[10px] text-slate-300"></i> {{ $arsip->jumlah_soal }}</span>
                                                        <span class="text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-star text-[10px] text-slate-300"></i> {{ number_format($arsip->total_bobot, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-end gap-2 relative" x-data="{ menuOpen: false }">
                                                    <button @click="menuOpen = !menuOpen" class="p-2 rounded-xl border border-slate-200 text-slate-400 hover:text-navy transition-all">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div x-show="menuOpen" @click.away="menuOpen = false" class="absolute right-0 top-12 w-48 bg-white rounded-xl border border-slate-100 shadow-xl z-50 p-2 space-y-1 text-left">
                                                        <a href="{{ route('banksoal.arsip.dosen.show', $arsip->id) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-navy transition-all">
                                                            <i class="fas fa-external-link-alt w-4"></i> Buka Detail
                                                        </a>
                                                        <form action="{{ route('banksoal.arsip.dosen.destroy', $arsip->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')" class="block">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-rose-500 hover:bg-rose-50 transition-all">
                                                                <i class="fas fa-trash-alt w-4"></i> Hapus Arsip
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center text-slate-500">
                                    Tidak ada data arsip yang ditemukan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                    {{ $arsipPaginated->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

        @if($penarikanPending->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden order-2 mt-8">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </span>
                    <h3 class="font-bold text-slate-900">Riwayat Penarikan (Pending)</h3>
                </div>
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">{{ $penarikanPending->count() }} Item</span>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/30 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <th class="px-8 py-4">Detail Penarikan</th>
                                <th class="px-8 py-4">Mata Kuliah</th>
                                <th class="px-8 py-4">Waktu</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($penarikanPending as $penarikan)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 group-hover:text-navy transition-colors">{{ $penarikan->nama_ekstraksi }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-[10px] font-bold text-amber-600 border border-amber-100 uppercase">{{ $penarikan->tipe_ujian }}</span>
                                            @if($penarikan->metode_ujian === 'offline')
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-bold text-slate-600 border border-slate-200 uppercase">Offline</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-700">{{ $penarikan->mataKuliah->nama }}</span>
                                        <span class="text-xs text-slate-400">{{ $penarikan->mataKuliah->kode }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm text-slate-600">{{ $penarikan->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 relative" x-data="{ menuOpen: false }">
                                        <button @click="menuOpen = !menuOpen" class="p-2 rounded-xl border border-slate-200 text-slate-400 hover:text-navy transition-all">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div x-show="menuOpen" @click.away="menuOpen = false" class="absolute right-0 top-12 w-48 bg-white rounded-xl border border-slate-100 shadow-xl z-50 p-2 space-y-1 text-left">
                                            <a href="{{ route('banksoal.arsip.dosen.penarikan.edit', $penarikan->id) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-navy hover:bg-slate-50 transition-all">
                                                <i class="fas fa-file-export w-4"></i> Konversi
                                            </a>
                                            <form action="{{ route('banksoal.arsip.dosen.penarikan.destroy', $penarikan->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat penarikan ini?')" class="block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-rose-500 hover:bg-rose-50 transition-all">
                                                    <i class="fas fa-trash-alt w-4"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modals -->
    <x-banksoal::ui.modal id="uploadPdfModal" title="Unggah PDF Arsip" subtitle="Dokumen akan diarsipkan sebagai format PDF standar.">
        <form action="{{ route('banksoal.arsip.dosen.upload-pdf') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
            @csrf
            <div class="space-y-5">
                <div class="p-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center hover:border-navy transition-all cursor-pointer group" onclick="document.getElementById('pdf_file').click()">
                    <div class="h-16 w-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-pdf text-3xl text-rose-500"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Klik atau Tarik File PDF</p>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Maksimal 50MB</p>
                    <input type="file" id="pdf_file" name="pdf_file" class="hidden" accept="application/pdf" onchange="updateFileName(this, 'pdf_name_display')">
                </div>
                <div id="pdf_name_display" class="hidden animate-popup">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold">
                        <i class="fas fa-check-circle"></i>
                        <span class="file-name"></span>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-500 hover:bg-slate-50" onclick="closeModal('uploadPdfModal')">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-navy text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all">Upload Sekarang</button>
            </div>
        </form>
    </x-banksoal::ui.modal>

    <x-banksoal::ui.modal id="uploadCsvModal" title="Import Massal via CSV/Excel" subtitle="Gunakan template resmi untuk menghindari kesalahan format data.">
        <form action="{{ route('banksoal.arsip.dosen.upload-csv') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
            @csrf
            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 rounded-2xl bg-amber-50 border border-amber-100">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <i class="fas fa-download text-sm"></i>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-amber-900">Unduh Template</p>
                            <p class="text-[10px] text-amber-700">Pastikan data sesuai kolom yang tersedia.</p>
                        </div>
                    </div>
                    <a href="{{ route('banksoal.soal.dosen.export-csv') }}" class="px-4 py-2 rounded-xl bg-white border border-amber-200 text-amber-700 text-xs font-bold hover:bg-amber-100 transition-all shadow-sm">
                        Download .xlsx
                    </a>
                </div>

                <div class="p-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center hover:border-navy transition-all cursor-pointer group" onclick="document.getElementById('csv_file').click()">
                    <div class="h-16 w-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-excel text-3xl text-emerald-500"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Klik atau Tarik File Spreadsheet</p>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Format: CSV, XLS, XLSX • Maks 50MB</p>
                    <input type="file" id="csv_file" name="csv_file" class="hidden" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="updateFileName(this, 'csv_name_display')">
                </div>
                <div id="csv_name_display" class="hidden animate-popup">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold">
                        <i class="fas fa-check-circle"></i>
                        <span class="file-name"></span>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-500 hover:bg-slate-50" onclick="closeModal('uploadCsvModal')">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-navy text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all">Import Sekarang</button>
            </div>
        </form>
    </x-banksoal::ui.modal>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        function updateFileName(input, displayId) {
            const display = document.getElementById(displayId);
            const nameSpan = display.querySelector('.file-name');
            if (input.files.length > 0) {
                nameSpan.textContent = input.files[0].name;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        }
        function showLoader() {
            document.getElementById('global-loader').style.display = 'flex';
        }
        function hideLoader() {
            document.getElementById('global-loader').style.display = 'none';
        }
        document.getElementById('filterForm').addEventListener('submit', showLoader);
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', showLoader);
        });
    </script>
</x-banksoal::layouts.dosen-admin>
