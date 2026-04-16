<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Detail Soal" subtitle="Informasi detail pertanyaan dan opsi jawaban.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <x-banksoal::ui.panel :title="'Detail Soal (' . ('Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT)) . ')'" subtitle="Status dan metadata soal" padding="p-6">
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mata Kuliah</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $soal->mataKuliah->kode ?? '-' }} - {{ $soal->mataKuliah->nama ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">CPL / Topik</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $soal->cpl->kode ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">CPMK</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $soal->cpmk->kode ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kesulitan / Bobot</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ ucfirst($soal->kesulitan) }} ({{ $soal->bobot }} Poin)</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tipe Soal</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ ucwords(str_replace('_', ' ', $soal->tipe_soal ?? 'Pilihan Ganda')) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Pertanyaan</p>
                <span class="mt-1 inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ strtoupper($soal->status) }}</span>
            </div>
        </div>
        
        @if(isset($review) && !empty($review->catatan))
        <div class="mb-6">
            @if(in_array(strtolower($soal->status), ['revisi', 'ditolak']))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1 text-red-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Catatan Revisi dari GPM</h4>
                        <p class="mt-1 text-sm text-red-700 leading-relaxed">{{ $review->catatan }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-history mt-1 text-amber-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800">Riwayat Catatan GPM Sebelumnya</h4>
                        <p class="mt-1 text-sm text-amber-700 leading-relaxed">{{ $review->catatan }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="mb-6">
            <p class="mb-2 text-sm font-semibold text-slate-700">Pertanyaan</p>
            <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-relaxed text-slate-900 prose quill-content max-w-none">
                {!! $soal->soal !!}
            </div>
        </div>

        @push('styles')
        <style>
            .quill-content p { margin-bottom: 1em; }
            .quill-content ul { list-style-type: disc; margin-left: 1.5em; margin-bottom: 1em; }
            .quill-content ol { list-style-type: decimal; margin-left: 1.5em; margin-bottom: 1em; }
            .quill-content blockquote { border-left: 4px solid #cbd5e1; padding-left: 1em; color: #64748b; font-style: italic; }
            .quill-content pre { background-color: #1e293b; color: #f8fafc; padding: 1em; border-radius: 0.5rem; overflow-x: auto; }
            .quill-content img { max-width: 100%; border-radius: 0.5rem; margin-top: 1em; margin-bottom: 1em; }
            .quill-content table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
            .quill-content th, .quill-content td { border: 1px solid #cbd5e1; padding: 0.5rem; }
        </style>
        @endpush

        <div>
            <p class="mb-2 text-sm font-semibold text-slate-700">Pilihan Jawaban</p>
            <div class="space-y-2">
                @foreach($soal->jawaban as $jawab)
                    <div class="flex items-start gap-3 rounded-xl border p-4 {{ $jawab->is_benar ? 'border-green-400 bg-green-50' : 'border-slate-200 bg-white' }}">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold {{ $jawab->is_benar ? 'bg-green-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $jawab->opsi }}</span>
                        <p class="flex-1 text-sm text-slate-700">{{ $jawab->deskripsi }}</p>
                        @if($jawab->is_benar)
                            <i class="fas fa-check-circle text-green-600" title="Jawaban Benar"></i>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @can('banksoal.edit')
            <div class="mt-6 flex justify-end">
                <a href="{{ route('banksoal.soal.dosen.edit', $soal->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    <i class="fas fa-pen"></i> Edit Soal
                </a>
            </div>
        @endcan
    </x-banksoal::ui.panel>
</x-banksoal::layouts.dosen-admin>
