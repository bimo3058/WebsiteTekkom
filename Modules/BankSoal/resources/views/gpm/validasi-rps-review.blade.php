<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi RPS" subtitle="Periksa kelengkapan dokumen RPS">
        <x-slot:actions>
            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

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
                    <i class="fas fa-clipboard-check text-blue-600"></i> Form Penilaian GPM
                </div>

                <form id="validasiForm" method="POST" class="flex-1 flex flex-col">
                    @csrf
                    <input type="hidden" name="rps_id" value="{{ $rps->rps_id }}">
                    <input type="hidden" name="action" id="actionInput" value="">
                    <div class="flex-1 overflow-y-auto pr-4 max-h-[320px]">
                        @forelse($parameters as $index => $param)
                            <div class="mb-4">
                                <div class="flex items-center justify-between gap-3 text-sm text-slate-700 font-medium">
                                    <span>{{ $index + 1 }}. {{ $param->aspek }}</span>
                                    <span class="inline-flex shrink-0 whitespace-nowrap items-center rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700 border border-blue-200">{{ $param->bobot }} poin</span>
                                </div>
                                <div class="mt-2 flex gap-4 text-xs text-slate-600">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="parameter_{{ $param->id }}" value="1" data-bobot="{{ $param->bobot }}" onchange="hitungSkor()"> Sesuai
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="parameter_{{ $param->id }}" value="0" data-bobot="{{ $param->bobot }}" onchange="hitungSkor()"> Tidak Sesuai
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">Tidak ada parameter penilaian yang tersedia</div>
                        @endforelse
                    </div>

                    <div class="mt-4 border-y border-dashed border-slate-200 py-3 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">Skor Evaluasi</span>
                        <span class="text-lg font-bold text-blue-600" id="nilaiAkhir">{{ isset($existingReview) ? $existingReview->nilai_akhir : '0' }}/100</span>
                    </div>

                    <div class="mt-4">
                        <label for="catatan" class="text-xs font-semibold text-slate-600">Catatan Revisi</label>
                        <textarea id="catatan" name="catatan" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="4" placeholder="Masukkan detail perbaikan jika diperlukan...">{{ isset($existingReview) ? $existingReview->catatan : '' }}</textarea>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <button type="button" class="flex-1 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" id="btnKembalikan">Kembalikan</button>
                        <button type="button" class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed" id="btnSetujui">Setujui RPS</button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <p class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider mb-3">History Log</p>
                <div class="space-y-3">
                    @forelse($history as $item)
                        <div class="relative pl-4">
                            <span class="absolute left-0 top-1.5 h-2 w-2 rounded-full {{ $loop->first ? 'bg-blue-500' : 'bg-amber-400' }}"></span>
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

                nilaiAkhirEl.textContent = totalNilai + '/100';
                updateButtonState(totalNilai);
            };

            window.updateButtonState = function (score) {
                const MIN_SCORE = 60;
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
                    const message = data.message + ' (Skor: ' + data.nilai_akhir + '/100)';
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