<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="text-slate-500 hover:text-primary transition-colors">Validasi RPS</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Review RPS</span>
    @endsection
    @php
        $skorMinimum = \Illuminate\Support\Facades\DB::table('bs_pengaturan')->where('kunci', 'standar_skor_minimum')->value('nilai') ?? 60;
    @endphp
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi RPS" subtitle="Periksa kelengkapan dokumen RPS">
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
                    @if($selectedCpls->isNotEmpty())
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-sm font-semibold text-slate-900 mb-2">CPL Terpilih</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedCpls as $cpl)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 border border-slate-200">
                                        {{ $cpl->kode }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                        <div class="text-sm font-semibold text-amber-900 mb-2">Draft CPMK</div>
                        @if(!empty($draftCpmkItems))
                            <div class="space-y-2">
                                @foreach($draftCpmkItems as $item)
                                    <div class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs text-slate-700">
                                        <span class="font-semibold text-slate-900">{{ $item->cpl_kode }}</span>
                                        <span class="mx-1 text-slate-400">&rarr;</span>
                                        <span class="font-semibold text-amber-700">{{ $item->cpmk_kode }}</span>
                                        <div class="mt-1 text-[11px] text-slate-500">{{ $item->cpmk_deskripsi }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm text-amber-800">Belum ada draft CPMK yang diajukan.</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $status = strtolower($rps->status ?? 'diajukan');
        $statusClass = $status === 'revisi'
            ? 'border-rose-200 bg-rose-50 text-rose-700'
            : ($status === 'disetujui'
                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                : 'border-amber-200 bg-amber-50 text-amber-700');
        $iconClass = $status === 'revisi'
            ? 'bg-rose-100 text-rose-600'
            : ($status === 'disetujui'
                ? 'bg-emerald-100 text-emerald-600'
                : 'bg-amber-100 text-amber-600');
    @endphp

    <div id="statusBanner" class="mb-6 rounded-2xl border p-4 {{ $statusClass }}">
        <div class="flex items-start gap-3">
            <div id="statusIcon" class="flex h-8 w-8 items-center justify-center rounded-full font-semibold {{ $iconClass }}">!</div>
            <div class="text-sm">
                <p class="font-semibold">Status: <span id="statusText">{{ ucfirst($rps->status) }}</span></p>
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

    @if(!empty($rps->catatan))
        <div class="mb-6 rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
            <div class="flex gap-3">
                <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600">
                    <i class="fas fa-comment-dots text-xs"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-sky-900">Catatan Tambahan Pengusul RPS</h4>
                    <p class="mt-1 text-sm text-sky-700 leading-relaxed whitespace-pre-line">{{ $rps->catatan }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="xl:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-rose-500"></i> {{ basename($rps->dokumen) }}
                    </div>
                    <div class="flex items-center gap-2 text-slate-500">
                        <button class="rounded-md p-1 hover:bg-slate-200"><i class="fas fa-search-minus"></i></button>
                        <button class="rounded-md p-1 hover:bg-slate-200"><i class="fas fa-search-plus"></i></button>
                        <button class="rounded-md p-1 hover:bg-slate-200"><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="flex-1 bg-slate-100 relative"> 
                    <iframe
                     id="pdfFrame"
                     src="{{ route('banksoal.rps.gpm.validasi-rps.preview', ['rpsId' => $rps->rps_id]) }}"
                     loading="eager"
                     title="PDF Preview RPS"
                      class="absolute inset-0 w-full h-full border-0" 
                      onload="handleIframeLoad()"
                      onerror="handleIframeError()">
                 </iframe>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col">
                <div class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fas fa-clipboard-check text-primary"></i> Form Penilaian GPM
                </div>

                <form id="validasiForm" method="POST" class="flex-1 flex flex-col">
                    @csrf
                    <input type="hidden" name="rps_id" value="{{ $rps->rps_id }}">
                    <input type="hidden" name="action" id="actionInput" value="">
                    <div class="flex-1 overflow-y-auto pr-4 max-h-[320px]">
                        @forelse($parameters as $index => $param)
                            <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                <p class="text-sm font-semibold text-slate-700">{{ $index + 1 }}. {{ $param->aspek }} <span class="text-primary">({{ $param->bobot }} poin)</span></p>
                                <div class="mt-3 flex gap-6 text-sm text-slate-600">
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-primary transition-colors">
                                        <input type="radio" name="parameter_{{ $param->id }}" value="1" data-bobot="{{ $param->bobot }}" class="w-4 h-4 text-primary border-slate-300 focus:ring-primary" onchange="hitungSkor()" required> Sesuai
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-rose-600 transition-colors">
                                        <input type="radio" name="parameter_{{ $param->id }}" value="0" data-bobot="{{ $param->bobot }}" class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="hitungSkor()" required> Tidak Sesuai
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">Tidak ada parameter penilaian yang tersedia</div>
                        @endforelse
                    </div>

                    <div class="mt-4 border-y border-dashed border-slate-200 py-3 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">Skor Evaluasi</span>
                        <span class="text-lg font-bold text-primary" id="nilaiAkhir">{{ isset($existingReview) ? $existingReview->nilai_akhir : '0' }}/{{ $totalBobot }}</span>
                    </div>

                    <div class="mt-4">
                        <label for="catatan" class="text-xs font-semibold text-slate-600">Catatan Revisi</label>
                        <textarea id="catatan" name="catatan" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="4" placeholder="Masukkan detail perbaikan jika diperlukan...">{{ isset($existingReview) ? $existingReview->catatan : '' }}</textarea>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <button type="button" class="flex-1 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" id="btnKembalikan">Kembalikan</button>
                        <button type="button" class="flex-1 rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary/90 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed" id="btnSetujui">Setujui RPS</button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <p class="text-[11px] font-semibold text-primary uppercase tracking-wider mb-3">History Log</p>
                <div class="space-y-3">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.handleIframeLoad = function () {
                const pdfFrame = document.getElementById('pdfFrame');
                if (pdfFrame) {
                    pdfFrame.classList.add('ready');
                }
            };

            window.handleIframeError = function () {
                const pdfFrame = document.getElementById('pdfFrame');
                if (pdfFrame) {
                    pdfFrame.classList.add('ready');
                }
            };

            const pdfFrame = document.getElementById('pdfFrame');
            if (pdfFrame) {
                pdfFrame.classList.add('loading');
            }

            setTimeout(() => {
                if (pdfFrame && !pdfFrame.classList.contains('ready')) {
                    window.handleIframeLoad();
                }
            }, 3000);

            window.hitungSkor = function () {
                const form = document.getElementById('validasiForm');
                const nilaiAkhirEl = document.getElementById('nilaiAkhir');
                let totalNilai = 0;

                const inputs = form.querySelectorAll('input[type="radio"]:checked');
                inputs.forEach(input => {
                    if (input.value === '1') {
                        totalNilai += parseInt(input.getAttribute('data-bobot')) || 0;
                    }
                });

                nilaiAkhirEl.textContent = totalNilai + '/{{ $totalBobot }}';
                
                const MIN_SCORE = {{ $skorMinimum }};
                if (totalNilai >= MIN_SCORE) {
                    nilaiAkhirEl.classList.remove('text-slate-300', 'text-rose-600');
                    nilaiAkhirEl.classList.add('text-emerald-600');
                } else if (totalNilai > 0) {
                    nilaiAkhirEl.classList.remove('text-slate-300', 'text-emerald-600');
                    nilaiAkhirEl.classList.add('text-rose-600');
                } else {
                    nilaiAkhirEl.classList.remove('text-emerald-600', 'text-rose-600');
                    nilaiAkhirEl.classList.add('text-slate-300');
                }

                updateButtonState(totalNilai);
            };

            window.updateButtonState = function (score) {
                const MIN_SCORE = {{ $skorMinimum }};
                const btnSetujui = document.getElementById('btnSetujui');
                if (!btnSetujui) return;

                if (score < MIN_SCORE) {
                    btnSetujui.disabled = true;
                    btnSetujui.setAttribute('title', 'Nilai di bawah standar, ajukan revisi terlebih dahulu');
                } else {
                    btnSetujui.disabled = false;
                    btnSetujui.setAttribute('title', '');
                }
            };

            window.validateParametersNotEmpty = function () {
                const form = document.getElementById('validasiForm');
                const radioLabels = form.querySelectorAll('[name^="parameter_"]');
                const grouped = {};

                radioLabels.forEach((radio) => {
                    const name = radio.getAttribute('name');
                    grouped[name] = grouped[name] || [];
                    grouped[name].push(radio);
                });

                const emptyParameters = Object.keys(grouped).filter(name => {
                    return !grouped[name].some(radio => radio.checked);
                });

                if (emptyParameters.length > 0) {
                    showToast('Parameter belum lengkap. Harap lengkapi semua penilaian.', 'error');
                    return false;
                }
                return true;
            };

            const btnKembalikan = document.getElementById('btnKembalikan');
            const btnSetujui = document.getElementById('btnSetujui');
            const form = document.getElementById('validasiForm');
            const actionInput = document.getElementById('actionInput');
            const catatanTextarea = document.getElementById('catatan');

            updateButtonState(0);
            if (typeof hitungSkor === 'function') {
                hitungSkor();
            }

            if (btnKembalikan) {
                btnKembalikan.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (!validateParametersNotEmpty()) {
                        return;
                    }

                    const catatan = catatanTextarea.value.trim();
                    if (!catatan) {
                        showToast('Catatan revisi harus diisi sebelum mengembalikan RPS', 'error');
                        catatanTextarea.focus();
                        return;
                    }

                    actionInput.value = 'revisi';
                    submitForm();
                });
            }

            if (btnSetujui) {
                btnSetujui.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (!validateParametersNotEmpty()) {
                        return;
                    }

                    actionInput.value = 'setuju';
                    submitForm();
                });

                btnSetujui.addEventListener('mousedown', function (e) {
                    if (this.disabled) {
                        e.preventDefault();
                        showToast('Nilai di bawah standar, ajukan revisi terlebih dahulu', 'error');
                    }
                });
            }

            function submitForm() {
                const formData = new FormData(form);

                fetch('{{ route("banksoal.rps.gpm.validasi-rps.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const message = data.message + ' (Skor: ' + data.nilai_akhir + '/{{ $totalBobot }})';
                    showToast(message, 'success');
                    updateStatusBanner(data.status);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'Gagal menyimpan validasi', 'error');
                });
            }

            function updateStatusBanner(status) {
                const banner = document.getElementById('statusBanner');
                const statusIcon = document.getElementById('statusIcon');
                const statusText = document.getElementById('statusText');

                const baseClasses = ['border-amber-200', 'bg-amber-50', 'text-amber-700', 'border-rose-200', 'bg-rose-50', 'text-rose-700', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-700'];
                baseClasses.forEach(cls => banner.classList.remove(cls));

                const iconBase = ['bg-amber-100', 'text-amber-600', 'bg-rose-100', 'text-rose-600', 'bg-emerald-100', 'text-emerald-600'];
                iconBase.forEach(cls => statusIcon.classList.remove(cls));

                if (status === 'revisi') {
                    banner.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-700');
                    statusIcon.classList.add('bg-rose-100', 'text-rose-600');
                    statusIcon.textContent = '◄';
                    statusText.textContent = 'Revisi';
                } else if (status === 'disetujui') {
                    banner.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                    statusIcon.classList.add('bg-emerald-100', 'text-emerald-600');
                    statusIcon.textContent = '✓';
                    statusText.textContent = 'Disetujui';
                } else {
                    banner.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-700');
                    statusIcon.classList.add('bg-amber-100', 'text-amber-600');
                    statusIcon.textContent = '!';
                    statusText.textContent = 'Diajukan';
                }
            }

            function showToast(message, type) {
                if (typeof Snackbar !== 'undefined' && typeof Snackbar.show === 'function') {
                    Snackbar.show(message, type);
                } else {
                    alert(message);
                }
            }
        });
    </script>
</x-banksoal::layouts.gpm-master>