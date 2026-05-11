<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi Bank Soal" subtitle="Evaluasi kesesuaian butir soal dengan CPL">
        <x-slot name="actions">
            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </x-slot>
    </x-banksoal::ui.page-header>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-slate-700">
            <span class="font-semibold">Mata Kuliah:</span> {{ $soal->mk_nama }} ({{ $soal->mk_kode }})
            <span class="mx-2 text-slate-300">|</span>
            <span class="font-semibold">Dosen:</span> Budi Santoso
        </div>
        <div class="flex items-center gap-3 text-sm text-slate-700">
            <span class="font-semibold">Review Progress:</span>
            Soal {{ $currentIndex ?? $soal->id }} dari {{ $totalSoalMK ?? '?' }}
            <div class="h-2 w-40 rounded-full bg-slate-200 overflow-hidden">
                <div class="h-full bg-blue-600" style="width: {{ $progressPercentage ?? 0 }}%;"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-2 text-blue-700 font-semibold mb-4">
                <i class="far fa-dot-circle"></i>
                Target Capaian Pembelajaran (CPL)
            </div>

            <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Kode Capaian</div>
            <div class="text-lg font-bold text-slate-900 mb-4">{{ $soal->cpl_kode }}</div>

            <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Deskripsi Kompetensi (CPL)</div>
            <p class="text-sm text-slate-600 leading-relaxed mb-5">{{ $soal->cpl_deskripsi }}</p>

            @if($soal->cpmk_kode)
                <div class="h-px bg-slate-200 my-4"></div>
                <div class="flex items-center gap-2 text-blue-700 font-semibold mb-4">
                    <i class="far fa-dot-circle"></i>
                    Target Capaian Mata Kuliah (CPMK)
                </div>
                <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Kode Capaian</div>
                <div class="text-lg font-bold text-slate-900 mb-4">{{ $soal->cpmk_kode }}</div>
                <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Deskripsi Kompetensi (CPMK)</div>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $soal->cpmk_deskripsi }}</p>
            @endif

            @php
                $kognitifMap = [
                    'easy' => 'C1-C2 (Mengingat / Memahami)',
                    'intermediate' => 'C3-C4 (Mengaplikasikan / Menganalisis)',
                    'advanced' => 'C5-C6 (Mengevaluasi / Mencipta)',
                ];
                $kesulitanLabel = $soal->kesulitan ?? 'intermediate';
                $levelKognitif = $kognitifMap[$kesulitanLabel] ?? 'C3-C4 (Sedang)';
            @endphp
            <span class="mt-4 inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-[11px] font-semibold text-white">
                Level Kognitif: {{ $levelKognitif }} ({{ ucfirst($kesulitanLabel) }})
            </span>
        </div>

        <div class="lg:col-span-2">
            @if(isset($review) && !empty($review->catatan))
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3">
                    <i class="fas fa-history text-amber-600 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">Riwayat Catatan GPM Terakhir</p>
                        <p class="text-sm text-amber-700">{{ $review->catatan }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">Soal ID. {{ $soal->id }}</span>
                    <span class="text-xs text-slate-500">Tipe: Pilihan Ganda</span>
                </div>

                <div class="text-base font-semibold text-slate-900 leading-relaxed mb-6">
                    {!! $soal->soal !!}
                </div>

                <div class="space-y-3">
                    @foreach($opsi_jawaban as $opsi)
                        <div class="flex items-center gap-3 rounded-lg border {{ $opsi->is_benar ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white' }} px-4 py-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $opsi->is_benar ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700' }} text-xs font-semibold">
                                {{ $opsi->opsi }}
                            </div>
                            <div class="text-sm {{ $opsi->is_benar ? 'text-emerald-800 font-medium' : 'text-slate-600' }} flex-1">{{ $opsi->deskripsi }}</div>
                            @if($opsi->is_benar)
                                <i class="far fa-check-circle text-emerald-500"></i>
                            @endif
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('banksoal.soal.gpm.validasi-bank-soal.store', ['mk_id' => request('mk_id')]) }}" method="POST" class="mt-6" id="validasiForm">
                    @csrf
                    <input type="hidden" name="pertanyaan_id" value="{{ $soal->id }}">
                    <input type="hidden" name="status_review" id="statusReview" value="Sesuai">

                    <div class="border-t border-dashed border-slate-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Parameter Penilaian</p>
                            <span class="text-[11px] font-medium text-slate-400">Total 100%</span>
                        </div>

                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-200">
                            @php
                                $dummyParams = [
                                    ['id' => 1, 'parameter' => 'Kesesuaian materi soal dengan CPL/CPMK', 'bobot' => 40],
                                    ['id' => 2, 'parameter' => 'Tingkat kesulitan dan tingkatan kognitif', 'bobot' => 30],
                                    ['id' => 3, 'parameter' => 'Kejelasan penyusunan kalimat dan tata bahasa', 'bobot' => 30],
                                ];
                            @endphp
                            @foreach($dummyParams as $param)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                    <p class="text-sm font-semibold text-slate-700">{{ $param['parameter'] }} <span class="text-blue-600">({{ $param['bobot'] }}%)</span></p>
                                    <div class="mt-3 flex gap-6 text-sm text-slate-600">
                                        <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-blue-600 transition-colors">
                                            <input type="radio" name="parameter_{{ $param['id'] }}" value="1" data-bobot="{{ $param['bobot'] }}" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500" onchange="hitungSkor()" required> Sesuai
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-rose-600 transition-colors">
                                            <input type="radio" name="parameter_{{ $param['id'] }}" value="0" data-bobot="{{ $param['bobot'] }}" class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="hitungSkor()" required> Tidak Sesuai
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 border-y border-dashed border-slate-200 py-4 flex items-center justify-between bg-white">
                            <span class="text-sm font-bold text-slate-700">Skor Evaluasi (Otomatis)</span>
                            <span class="text-2xl font-black text-slate-300" id="nilaiAkhir">0/100</span>
                        </div>

                        <div class="mt-5">
                            <label for="catatan" class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">Catatan Revisi</label>
                            <textarea id="catatan" name="catatan" placeholder="Masukkan feedback untuk dosen..." class="w-full min-h-[120px] rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-50/50 focus:border-blue-300 transition-colors"></textarea>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="submit" class="flex-1 rounded-xl border border-rose-200 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors flex items-center justify-center gap-2" id="btnKembalikan" onclick="setKembalikan()">
                                Kembalikan Ke Dosen <i class="fas fa-undo"></i>
                            </button>
                            <button type="submit" class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2" id="btnSetujui" onclick="setSetuju()">
                                Valid dan Lanjut <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function hitungSkor() {
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
            
            // Ubah warna skor mengikuti nilai
            if (totalNilai >= 60) {
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
        }

        function updateButtonState(score) {
            const MIN_SCORE = 60;
            const btnSetujui = document.getElementById('btnSetujui');
            if (!btnSetujui) return;

            const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === 3; // Total parameter is 3
            
            if (!allAnswered || score < MIN_SCORE) {
                btnSetujui.disabled = true;
                btnSetujui.setAttribute('title', 'Seluruh parameter wajib diisi. Nilai < 60 silakan kembalikan soal.');
            } else {
                btnSetujui.disabled = false;
                btnSetujui.setAttribute('title', '');
            }
        }

        function setKembalikan() {
            const catatan = document.getElementById('catatan');
            catatan.required = true;
            document.getElementById('statusReview').value = 'Revisi Total';
        }

        function setSetuju() {
            const form = document.getElementById('validasiForm');
            const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === 3;
            if(!allAnswered) {
                // Biarkan validasi HTML5 handle prevent form submission
                return;
            }
            
            const btnSetujui = document.getElementById('btnSetujui');
            if(btnSetujui.disabled) {
                // Pencegahan ekstra
                event.preventDefault();
                return;
            }
            
            const catatan = document.getElementById('catatan');
            catatan.required = false;
            document.getElementById('statusReview').value = 'Sesuai';
        }

        document.getElementById('btnSetujui').addEventListener('mousedown', function (e) {
            const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === 3;
            if(!allAnswered) {
                return;
            }
            if (this.disabled) {
                e.preventDefault();
                alert('Nilai di bawah standar (< 60). Silakan kembalikan soal ini dengan menyertakan catatan revisi.');
            }
        });
        
        // Initial hitung
        hitungSkor();
    </script>
</x-banksoal::layouts.gpm-master>