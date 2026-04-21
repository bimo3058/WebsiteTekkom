<x-banksoal::layouts.gpm-master>
    <div class="mb-6 flex flex-col gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $mataKuliah->nama }}</h1>
            <p class="text-sm text-slate-500">{{ $mataKuliah->kode }} • Program Studi Teknik Komputer</p>
        </div>
        <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="mb-6 flex justify-end">
        {{ $riwayatSoal->links() }}
    </div>

    <div class="space-y-6">
        @forelse($riwayatSoal as $index => $soal)
            @php
                $isSesuai = $soal->status_review == 'Sesuai';
                $isKurang = $soal->status_review == 'Kurang Sesuai';
                $colorClass = $isSesuai ? 'emerald' : ($isKurang ? 'amber' : 'rose');
                $badgeBg = $isSesuai ? 'bg-emerald-600 text-white' : ($isKurang ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white');
                $iconStatus = $isSesuai ? 'fa-check-circle' : ($isKurang ? 'fa-exclamation-triangle' : 'fa-times-circle');
            @endphp

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="h-1 {{ $colorClass === 'emerald' ? 'bg-emerald-500' : ($colorClass === 'amber' ? 'bg-amber-500' : 'bg-rose-500') }}"></div>
                <div class="p-6">
                    <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-[11px] font-semibold text-white">SOAL #{{ $riwayatSoal->firstItem() + $index }}</span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200">LEVEL: {{ strtoupper($soal->kesulitan) }}</span>
                            @if($soal->cpl)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200"><i class="fas fa-tag mr-1"></i> {{ $soal->cpl->kode }}</span>
                            @endif
                            <span class="text-xs text-slate-500">Dinilai pada: {{ \Carbon\Carbon::parse($soal->tanggal_review)->translatedFormat('d M Y') }}</span>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full {{ $badgeBg }} px-3 py-1 text-[11px] font-semibold">
                            <i class="fas {{ $iconStatus }}"></i> {{ strtoupper($soal->status_review) }}
                        </span>
                    </div>

                    <div class="prose prose-sm max-w-none text-slate-900 font-semibold leading-relaxed mt-4">
                        {!! $soal->soal !!}
                    </div>

                    @if($soal->jawaban && count($soal->jawaban) > 0)
                        <ul class="mt-4 space-y-2">
                            @foreach($soal->jawaban as $idx => $jawab)
                                @php $char = chr(65 + $idx); @endphp
                                <li class="flex items-center gap-3 rounded-lg border {{ $jawab->is_benar ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }} px-4 py-3">
                                    <span class="text-xs font-bold {{ $jawab->is_benar ? 'text-emerald-600' : 'text-slate-500' }}">{{ $jawab->opsi ?? $char }}.</span>
                                    <div class="prose prose-sm max-w-none text-slate-700">{!! $jawab->deskripsi !!}</div>
                                    @if($jawab->is_benar)
                                        <i class="fas fa-check-circle text-emerald-500 ml-auto"></i>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-5 rounded-xl border-l-4 {{ $colorClass === 'emerald' ? 'border-emerald-500 bg-emerald-50' : ($colorClass === 'amber' ? 'border-amber-500 bg-amber-50' : 'border-rose-500 bg-rose-50') }} p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/60">
                                    <i class="fas fa-comment-dots text-slate-500"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Catatan Evaluator</p>
                            </div>
                            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50" data-modal-open="editModal{{ $soal->id }}">
                                <i class="fas fa-edit"></i> Edit Review
                            </button>
                        </div>
                        <p class="mt-3 text-sm text-slate-700">{{ $soal->catatan ?: 'Tidak ada catatan.' }}</p>
                    </div>
                </div>
            </div>

            <div id="editModal{{ $soal->id }}" class="fixed inset-0 z-50 hidden" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="editModal{{ $soal->id }}"></div>
                <div class="relative mx-auto mt-16 w-full max-w-lg rounded-2xl bg-white shadow-xl">
                    <form action="{{ route('banksoal.soal.gpm.validasi-bank-soal.update', $soal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                            <h2 class="text-sm font-semibold text-slate-900">Edit Hasil Review</h2>
                            <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="editModal{{ $soal->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Status Review</label>
                                <select name="status_review" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
                                    <option value="Sesuai" {{ $soal->status_review == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                                    <option value="Kurang Sesuai" {{ $soal->status_review == 'Kurang Sesuai' ? 'selected' : '' }}>Kurang Sesuai</option>
                                    <option value="Revisi" {{ $soal->status_review == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Catatan Evaluator</label>
                                <textarea name="catatan" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="4" placeholder="Tuliskan catatan revisi jika ada...">{{ $soal->catatan }}</textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="editModal{{ $soal->id }}">Batal</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <i class="fas fa-folder-open mb-3 text-3xl text-slate-300"></i>
                <p class="text-lg font-semibold text-slate-900">Riwayat Validasi Kosong</p>
                <p class="text-sm text-slate-500">Belum ada hasil review soal yang bisa ditampilkan untuk mata kuliah ini.</p>
                <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="mt-4 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Kembali ke Daftar</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6 flex justify-center">
        {{ $riwayatSoal->links() }}
    </div>
</x-banksoal::layouts.gpm-master>