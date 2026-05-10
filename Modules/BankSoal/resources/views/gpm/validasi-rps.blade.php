<x-banksoal::layouts.gpm-master>
    <style>
        /* Animasi untuk background gelap (fade in) */
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Animasi untuk kotak modal (pop up dari bawah/kecil ke ukuran asli) */
        @keyframes modalPopUp {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .animate-backdrop {
            animation: modalFadeIn 0.25s ease-out forwards;
        }

        .animate-popup {
            animation: modalPopUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .gpm-rps-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.75rem;
            border: 1px solid #93c5fd;
            background: #eff6ff;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #1d4ed8;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .gpm-rps-action-btn:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        .gpm-rps-action-btn-lg {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }
    </style>

    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi RPS" subtitle="Pantau riwayat dokumen RPS yang telah direview">
        <x-slot:actions>
            <button type="button" class="gpm-rps-action-btn gpm-rps-action-btn-lg" data-modal-open="modalUploadTemplate">
                <i class="fas fa-file-upload"></i> Upload Template
            </button>
            <button type="button" class="gpm-rps-action-btn gpm-rps-action-btn-lg" data-modal-open="modalTambah">
                <i class="fas fa-calendar-plus"></i> Buat Periode
            </button>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    @if($activePeriode)
        <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 border-l-4 {{ $isPeriodeRunning ? 'border-emerald-500' : 'border-slate-400' }}">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $isPeriodeRunning ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-600' }}">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $activePeriode->judul }}</p>
                        <p class="text-xs text-slate-500">Tenggat: {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($isPeriodeRunning)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            <i class="fas fa-circle mr-2 text-[8px]"></i> Sesi Dibuka
                        </span>
                        <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" data-modal-open="modalCloseSession">
                            <i class="fas fa-power-off"></i> Matikan Sesi
                        </button>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            <i class="fas fa-times-circle mr-2"></i> Sesi Berakhir
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 border-l-4 border-amber-400">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-900">
                            @if($inactivePeriodes->count() > 0)
                                Tidak ada sesi yang aktif
                            @else
                                Belum ada jadwal pengajuan
                            @endif
                        </p>
                        <p class="text-xs text-amber-800">
                            @if($inactivePeriodes->count() > 0)
                                Pilih periode di bawah untuk mengaktifkan sesi
                            @else
                                Tidak ada sesi pengajuan RPS yang ditambahkan saat ini
                            @endif
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    <i class="fas fa-exclamation-circle mr-2"></i> Belum Aktif
                </span>
            </div>
        </div>

        @if($inactivePeriodes->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Periode Tersedia</h3>
                <div class="space-y-3">
                    @foreach($inactivePeriodes as $periode)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $periode->judul }}</p>
                                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d M Y H:i') }} s.d. {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d M Y H:i') }}</p>
                                </div>
                            </div>
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50" data-modal-open="modalOpenSession" data-periode-id="{{ $periode->id }}" data-periode-judul="{{ $periode->judul }}" onclick="setPeriodeData(this)">
                                <i class="fas fa-power-off"></i> Nyalakan Sesi
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <div data-tabs>
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-6 text-sm font-semibold">
                <button type="button" class="pb-2 border-b-2 border-blue-600 text-blue-600" data-tab-target="menunggu" data-tab-active>
                    Menunggu Validasi
                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700 border border-blue-200">{{ $rpsDiajukan->total() }}</span>
                </button>
                <button type="button" class="pb-2 border-b-2 border-transparent text-slate-500" data-tab-target="revisi">
                    Menunggu Revisi
                    <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $rpsRevisi->total() }}</span>
                </button>
                <button type="button" class="pb-2 border-b-2 border-transparent text-slate-500" data-tab-target="disetujui">
                    Disetujui
                    <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $rpsDisetujui->total() }}</span>
                </button>
            </div>
        </div>

        <div data-tab-panel="menunggu">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" data-search-tab="menunggu" placeholder="Cari mata kuliah atau dosen...">
                </div>
                <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Diajukan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rpsDiajukan as $rps)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</div>
                                        <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @forelse($rps->dosens as $dosen)
                                                @php
                                                    $names = explode(' ', $dosen->name);
                                                    $first = $names[0] ?? '';
                                                    $last = $names[array_key_last($names)] ?? '';
                                                    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $initials }}</div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $dosen->name }}</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-500">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $rps->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200">Menunggu</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->id) }}" class="gpm-rps-action-btn">
                                            <i class="fas fa-comment-dots"></i> Review Sekarang
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada RPS yang menunggu validasi</td>
                                </tr>
                            @endforelse
                            <tr class="no-results-message hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada hasil pencarian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $rpsDiajukan->count() }} dari {{ $rpsDiajukan->total() }} entri</span>
                {{ $rpsDiajukan->links() }}
            </div>
        </div>

        <div class="hidden" data-tab-panel="revisi">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" data-search-tab="revisi" placeholder="Cari mata kuliah atau dosen...">
                </div>
                <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Review</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rpsRevisi as $rps)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</div>
                                        <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @forelse($rps->dosens as $dosen)
                                                @php
                                                    $names = explode(' ', $dosen->name);
                                                    $first = $names[0] ?? '';
                                                    $last = $names[array_key_last($names)] ?? '';
                                                    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $initials }}</div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $dosen->name }}</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-500">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $rps->updated_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-semibold text-red-700 border border-red-200">Revisi</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->id) }}" class="gpm-rps-action-btn">
                                            <i class="fas fa-edit"></i> Lihat Catatan
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada RPS yang menunggu revisi</td>
                                </tr>
                            @endforelse
                            <tr class="no-results-message hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada hasil pencarian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $rpsRevisi->count() }} dari {{ $rpsRevisi->total() }} entri</span>
                {{ $rpsRevisi->links() }}
            </div>
        </div>

        <div class="hidden" data-tab-panel="disetujui">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" data-search-tab="disetujui" placeholder="Cari mata kuliah atau dosen...">
                </div>
                <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Disetujui</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rpsDisetujui as $rps)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</div>
                                        <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @forelse($rps->dosens as $dosen)
                                                @php
                                                    $names = explode(' ', $dosen->name);
                                                    $first = $names[0] ?? '';
                                                    $last = $names[array_key_last($names)] ?? '';
                                                    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $initials }}</div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $dosen->name }}</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-500">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $rps->updated_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">Disetujui</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->id) }}" class="gpm-rps-action-btn">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-600">Belum ada RPS yang disetujui</td>
                                </tr>
                            @endforelse
                            <tr class="no-results-message hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada hasil pencarian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $rpsDisetujui->count() }} dari {{ $rpsDisetujui->total() }} entri</span>
                {{ $rpsDisetujui->links() }}
            </div>
        </div>
    </div>

    <div id="modalUploadTemplate" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40 animate-backdrop" data-modal-overlay="modalUploadTemplate"></div>
        <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl animate-popup">
            <form id="formUploadTemplate" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Upload Template RPS</h2>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalUploadTemplate">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">File Template (Word Format) <span class="text-rose-500">*</span></label>
                        <div class="upload-box-modal mt-2 rounded-xl border-2 border-dashed border-slate-200 p-4 text-center cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                            <p class="text-sm text-slate-500">Dragdrop file atau <span class="text-blue-600 underline">pilih file</span></p>
                            <p class="text-xs text-slate-400">Format: .doc, .docx (Maksimal 1 MB)</p>
                            <input type="file" name="dokumen" id="fileInputModal" accept=".doc,.docx" required class="hidden">
                            <div class="file-selected-modal mt-3 hidden">
                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span class="file-name-modal"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Keterangan (Opsional)</label>
                        <textarea class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="keterangan" rows="3" placeholder="Misal: Update struktur template, tambahan BAB, dll..."></textarea>
                    </div>
                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Template baru akan otomatis menjadi versi terbaru yang dapat diunduh dosen.
                    </div>
                    <div id="uploadStatusMessage"></div>
                </div>
                <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" id="btnDeleteInactive">
                        <i class="fas fa-trash"></i> Hapus Versi Lama
                    </button>
                    <div class="flex gap-2">
                        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalUploadTemplate">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700" id="btnSubmitTemplate">Upload Template</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40 animate-backdrop" data-modal-overlay="modalTambah"></div>
        <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl animate-popup">
            <form action="{{ route('banksoal.rps.gpm.periode-rps.store') }}" method="POST">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Buat Jadwal RPS Baru</h2>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalTambah">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Judul Periode <span class="text-rose-500">*</span></label>
                        <input type="text" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="judul" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026">
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Semester <span class="text-rose-500">*</span></label>
                            <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="semester" required>
                                <option value="Ganjil" {{ $currentSemester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ $currentSemester == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Tahun Ajaran <span class="text-rose-500">*</span></label>
                            <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tahun_ajaran" required>
                                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta }}">{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_mulai" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tanggal Selesai (Tenggat) <span class="text-rose-500">*</span></label>
                        <input type="date" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_selesai" required>
                    </div>
                    <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 text-xs text-slate-600">
                        <input class="mt-1" type="checkbox" name="is_active" value="1" checked>
                        <span>
                            <span class="font-semibold text-slate-700">Otomatis aktifkan jadwal ini</span>
                            <span class="block text-[11px] text-slate-500">GPM hanya bisa membuka 1 sesi pengajuan dalam satu waktu. Mencentang ini akan membatalkan sesi lain yang masih aktif.</span>
                        </span>
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                    <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalTambah">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Buat & Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalCloseSession" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40 animate-backdrop" data-modal-overlay="modalCloseSession"></div>
        <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl animate-popup">
            <form action="{{ route('banksoal.rps.gpm.periode-rps.close-session') }}" method="POST">
                @csrf
                <div class="px-5 py-5 text-center">
                    <div class="text-rose-500 mb-3"><i class="fas fa-exclamation-circle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Matikan Sesi Pengajuan?</h3>
                    <p class="text-xs text-slate-500 mt-2">Sesi pengajuan <strong>{{ $activePeriode->judul ?? 'RPS' }}</strong> akan ditutup. Dosen tidak akan bisa lagi mengajukan RPS sampai periode baru diaktifkan.</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalCloseSession">Batal</button>
                        <button type="submit" class="flex-1 rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Matikan Sesi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="modalOpenSession" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40 animate-backdrop" data-modal-overlay="modalOpenSession"></div>
        <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl animate-popup">
            <form action="{{ route('banksoal.rps.gpm.periode-rps.open-session') }}" method="POST">
                @csrf
                <input type="hidden" name="periode_id" id="periodeId">
                <div class="px-5 py-5 text-center">
                    <div class="text-blue-500 mb-3"><i class="fas fa-info-circle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Nyalakan Sesi Pengajuan?</h3>
                    <p class="text-xs text-slate-500 mt-2">Sesi pengajuan <strong id="periodeJudul">RPS</strong> akan diaktifkan. Dosen akan bisa mengajukan RPS sesuai dengan jadwal periode.</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalOpenSession">Batal</button>
                        <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Nyalakan Sesi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function debounce(func, delay) {
                let timeoutId;
                return function (...args) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => func.apply(this, args), delay);
                };
            }

            function searchTable(searchInput, tabId) {
                const searchValue = searchInput.value.toLowerCase().trim();
                const tabContent = document.querySelector(`[data-tab-panel="${tabId}"]`);
                if (!tabContent) return;

                const rows = tabContent.querySelectorAll('table tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let rowText = '';
                    if (cells.length >= 2) {
                        rowText = (cells[0].textContent + ' ' + cells[1].textContent).toLowerCase();
                    } else {
                        rowText = row.textContent.toLowerCase();
                    }

                    if (rowText.includes(searchValue) || searchValue === '') {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                const noResultsMsg = tabContent.querySelector('.no-results-message');
                if (noResultsMsg) {
                    if (visibleCount === 0) {
                        noResultsMsg.classList.remove('hidden');
                    } else {
                        noResultsMsg.classList.add('hidden');
                    }
                }
            }

            const searchInputs = document.querySelectorAll('[data-search-tab]');
            searchInputs.forEach((input) => {
                const tabId = input.getAttribute('data-search-tab');
                input.addEventListener('input', debounce(function () {
                    searchTable(this, tabId);
                }, 300));
            });
        });

        function setPeriodeData(element) {
            const periodeId = element.getAttribute('data-periode-id');
            const periodeJudul = element.getAttribute('data-periode-judul');
            const periodeInput = document.getElementById('periodeId');
            const periodeLabel = document.getElementById('periodeJudul');
            if (periodeInput) periodeInput.value = periodeId;
            if (periodeLabel) periodeLabel.textContent = periodeJudul;
        }

        function closeModalById(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        const uploadBoxModal = document.querySelector('.upload-box-modal');
        const fileInputModal = document.getElementById('fileInputModal');
        const fileSelectedModal = document.querySelector('.file-selected-modal');
        const fileNameModal = document.querySelector('.file-name-modal');
        const formUploadTemplate = document.getElementById('formUploadTemplate');
        const btnSubmitTemplate = document.getElementById('btnSubmitTemplate');
        const uploadStatusMessage = document.getElementById('uploadStatusMessage');

        function preventDefaultsModal(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        if (uploadBoxModal && fileInputModal) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadBoxModal.addEventListener(eventName, preventDefaultsModal, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadBoxModal.addEventListener(eventName, () => {
                    uploadBoxModal.classList.add('border-blue-500', 'bg-blue-50');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadBoxModal.addEventListener(eventName, () => {
                    uploadBoxModal.classList.remove('border-blue-500', 'bg-blue-50');
                }, false);
            });

            uploadBoxModal.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInputModal.files = files;
                updateFileDisplayModal();
            }, false);

            uploadBoxModal.addEventListener('click', () => {
                fileInputModal.click();
            });

            fileInputModal.addEventListener('change', updateFileDisplayModal);
        }

        function updateFileDisplayModal() {
            if (fileInputModal?.files && fileInputModal.files.length > 0) {
                if (fileNameModal) fileNameModal.textContent = fileInputModal.files[0].name;
                fileSelectedModal?.classList.remove('hidden');
            } else {
                fileSelectedModal?.classList.add('hidden');
            }
        }

        if (formUploadTemplate) {
            formUploadTemplate.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(formUploadTemplate);
                uploadStatusMessage.innerHTML = '';
                if (btnSubmitTemplate) {
                    btnSubmitTemplate.disabled = true;
                    btnSubmitTemplate.textContent = 'Uploading...';
                }

                try {
                    const response = await fetch("{{ route('banksoal.rps.gpm.template.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        uploadStatusMessage.innerHTML = `<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700"><i class="fas fa-check-circle mr-2"></i>${data.message}</div>`;
                        formUploadTemplate.reset();
                        fileSelectedModal?.classList.add('hidden');
                        setTimeout(() => {
                            closeModalById('modalUploadTemplate');
                            uploadStatusMessage.innerHTML = '';
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    uploadStatusMessage.innerHTML = `<div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700"><i class="fas fa-exclamation-triangle mr-2"></i>${error.message}</div>`;
                } finally {
                    if (btnSubmitTemplate) {
                        btnSubmitTemplate.disabled = false;
                        btnSubmitTemplate.textContent = 'Upload Template';
                    }
                }
            });
        }

        const btnDeleteInactive = document.getElementById('btnDeleteInactive');
        if (btnDeleteInactive) {
            btnDeleteInactive.addEventListener('click', async () => {
                if (!confirm('Apakah Anda yakin ingin menghapus semua versi template yang tidak aktif?\n\nAksi ini tidak dapat dibatalkan.')) {
                    return;
                }

                btnDeleteInactive.disabled = true;
                const originalHTML = btnDeleteInactive.innerHTML;
                btnDeleteInactive.textContent = 'Menghapus...';

                try {
                    const response = await fetch("{{ route('banksoal.rps.gpm.template.delete-inactive') }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({})
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        uploadStatusMessage.innerHTML = `<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700"><i class="fas fa-check-circle mr-2"></i>${data.message}</div>`;
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    uploadStatusMessage.innerHTML = `<div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700"><i class="fas fa-exclamation-triangle mr-2"></i>${error.message}</div>`;
                } finally {
                    btnDeleteInactive.disabled = false;
                    btnDeleteInactive.innerHTML = originalHTML;
                }
            });
        }
    </script>
</x-banksoal::layouts.gpm-master>