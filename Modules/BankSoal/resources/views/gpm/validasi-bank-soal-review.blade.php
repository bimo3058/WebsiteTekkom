<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi Bank Soal" subtitle="Evaluasi kesesuaian butir soal dengan CPL" />

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

            <span class="mt-4 inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-[11px] font-semibold text-white">
                Level Kognitif: C4 (Menganalisis)
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

                <form action="{{ route('banksoal.soal.gpm.validasi-bank-soal.store', ['mk_id' => request('mk_id')]) }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="pertanyaan_id" value="{{ $soal->id }}">

                    <div class="border-t border-dashed border-slate-200 pt-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Keputusan GPM</p>
                        <div class="flex flex-col gap-3 md:flex-row">
                            <label class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 flex items-center justify-center gap-2" data-decision onclick="selectDecision(this)">
                                <input type="radio" name="status_review" value="Sesuai" class="hidden" required>
                                <i class="fas fa-check"></i> Sesuai
                            </label>
                            <label class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 flex items-center justify-center gap-2" data-decision onclick="selectDecision(this)">
                                <input type="radio" name="status_review" value="Kurang Sesuai" class="hidden">
                                <i class="fas fa-exclamation-triangle"></i> Kurang Sesuai
                            </label>
                            <label class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 flex items-center justify-center gap-2" data-decision onclick="selectDecision(this)">
                                <input type="radio" name="status_review" value="Revisi Total" class="hidden">
                                <i class="fas fa-exclamation-circle"></i> Revisi Total
                            </label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Catatan Revisi</p>
                        <textarea name="catatan" placeholder="Masukkan feedback untuk dosen..." required class="w-full min-h-[120px] rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none"></textarea>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-slate-200 pt-6">
                        <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            Simpan & Lanjut Berikutnya <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectDecision(element) {
            document.querySelectorAll('[data-decision]').forEach(btn => {
                btn.classList.remove('bg-blue-50', 'border-blue-300', 'text-blue-600');
                btn.classList.add('border-slate-200', 'text-slate-600');
            });

            element.classList.remove('border-slate-200', 'text-slate-600');
            element.classList.add('bg-blue-50', 'border-blue-300', 'text-blue-600');

            const radioVal = element.querySelector('input[type="radio"]').value;
            const catatanElement = document.querySelector('textarea[name="catatan"]');
            if (!catatanElement) return;

            if (radioVal === 'Sesuai') {
                catatanElement.removeAttribute('required');
                catatanElement.placeholder = 'Opsional: Tambahkan catatan jika ada...';
            } else {
                catatanElement.setAttribute('required', 'required');
                catatanElement.placeholder = 'Masukkan feedback untuk dosen...';
            }
        }
    </script>
</x-banksoal::layouts.gpm-master>