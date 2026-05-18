<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="text-slate-500 hover:text-primary transition-colors">Validasi RPS</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">RPS Disetujui</span>
    @endsection
    
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="RPS Disetujui" subtitle="Lihat hasil akhir RPS yang telah divalidasi">
        <x-slot:actions>
            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <div class="mb-6 grid gap-4 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-primary">Mata Kuliah</div>
            <div class="text-lg font-bold text-slate-900">{{ $rps->mk_nama }}</div>
            <div class="mt-2 text-sm text-slate-600">{{ $rps->kode }} &middot; Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 text-[11px] font-semibold uppercase tracking-wider text-primary">Dosen Pengampu</div>
            <div class="flex flex-wrap gap-2">
                @forelse($dosenPengampu as $dosen)
                    <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary border border-primary/20">
                        {{ $dosen->name }}
                    </span>
                @empty
                    <span class="text-sm text-slate-500">Tidak ada dosen terdata</span>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 text-[11px] font-semibold uppercase tracking-wider text-primary">CPL / CPMK Terkoneksi</div>
            <div class="space-y-3 max-h-40 overflow-y-auto pr-1">
                @if($cplCpmkMappings->isNotEmpty())
                    @forelse($cplCpmkMappings as $cplId => $rows)
                        @php $firstRow = $rows->first(); @endphp
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-sm font-semibold text-slate-900">{{ $firstRow->cpl_kode }}</div>
                            <div class="mt-2 space-y-2">
                                @foreach($rows as $row)
                                    <div class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs text-slate-700">
                                        <span class="font-semibold text-emerald-700">{{ $row->cpmk_kode }}</span>
                                        <div class="mt-1 text-[11px] text-slate-500">{{ $row->cpmk_deskripsi }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada pemetaan CPL/CPMK untuk MK ini.</div>
                    @endforelse
                @else
                    <div class="text-sm text-slate-500">Belum ada pemetaan CPL/CPMK untuk MK ini.</div>
                @endif
            </div>
        </div>
    </div>

    @php
        $statusClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
        $iconClass = 'bg-emerald-100 text-emerald-600';
    @endphp

    <div id="statusBanner" class="mb-6 rounded-2xl border p-4 {{ $statusClass }}">
        <div class="flex items-start gap-3">
            <div id="statusIcon" class="flex h-8 w-8 items-center justify-center rounded-full font-semibold {{ $iconClass }}">✓</div>
            <div class="text-sm">
                <p class="font-semibold">Status: <span id="statusText">Disetujui</span></p>
                <p class="text-xs">
                    Mata Kuliah: {{ $rps->mk_nama }} ({{ $rps->kode }}) &bull; Diserahkan oleh:
                    @php
                        $dosensList = !empty($rps->dosens_list) ? array_map('trim', explode(',', $rps->dosens_list)) : [];
                    @endphp
                    @forelse($dosensList as $index => $dosenItem)
                        @php
                            $parts = explode('|', $dosenItem, 3);
                            $dosenName = $parts[1] ?? $dosenItem;
                        @endphp
                        @if(!empty($dosenName))
                            {{ $dosenName }}{{ $index < count($dosensList) - 1 ? ', ' : '' }}
                        @endif
                    @empty
                        -
                    @endforelse
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="xl:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-rose-500"></i> {{ basename($rps->dokumen) }}
                    </div>
                    <div class="flex items-center gap-2 text-slate-500">
                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.preview', ['rpsId' => $rps->rps_id]) }}" target="_blank" class="rounded-md p-1 hover:bg-slate-200" title="Buka PDF"><i class="fas fa-external-link-alt"></i></a>
                    </div>
                </div>
                <div class="flex-1 bg-slate-100 relative"> 
                    <iframe
                     id="pdfFrame"
                     src="{{ route('banksoal.rps.gpm.validasi-rps.preview', ['rpsId' => $rps->rps_id]) }}"
                     loading="eager"
                     title="PDF Preview RPS"
                      class="absolute inset-0 w-full h-full border-0">
                 </iframe>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col">
                <div class="text-sm font-semibold text-emerald-700 flex items-center gap-2 mb-4">
                    <i class="fas fa-check-circle text-emerald-500"></i> Ringkasan Penilaian
                </div>
                
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm text-center">
                    <p class="text-sm font-semibold text-emerald-800 mb-1">Skor Evaluasi</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ isset($existingReview) ? $existingReview->nilai_akhir : '0' }}<span class="text-lg text-emerald-400">/{{ $totalBobot }}</span></p>
                </div>

                @if(isset($existingReview) && !empty($existingReview->catatan))
                <div class="mt-2 mb-4">
                    <label class="text-xs font-semibold text-slate-600">Catatan GPM</label>
                    <div class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                        {{ $existingReview->catatan }}
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <p class="text-[11px] font-semibold text-primary uppercase tracking-wider mb-3">History Log</p>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                    @forelse($history as $item)
                        <div class="relative pl-4">
                            <span class="absolute left-0 top-1.5 h-2 w-2 rounded-full {{ $loop->first ? 'bg-primary' : 'bg-amber-400' }}"></span>
                            <p class="text-xs font-semibold text-slate-700">{{ ucfirst($item->action) }}</p>
                            <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y - H:i') }}</p>
                            @if($item->description)
                                <p class="text-[11px] text-slate-500">{{ $item->description }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">Belum ada riwayat aktivitas</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-banksoal::layouts.gpm-master>
