<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Detail Soal" subtitle="Informasi detail pertanyaan dan opsi jawaban.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <x-banksoal::ui.panel :title="'Detail Soal (' . ('Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT)) . ')'" subtitle="Status dan metadata soal" padding="p-6">
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mata Kuliah</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $soal->mataKuliah->kode ?? '-' }} - {{ $soal->mataKuliah->nama ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">CPL / Topik</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $soal->cpl->kode ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kesulitan / Bobot</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ ucfirst($soal->kesulitan) }} ({{ $soal->bobot }} Poin)</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Pertanyaan</p>
                <span class="mt-1 inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ strtoupper($soal->status) }}</span>
            </div>
        </div>

        <div class="mb-6">
            <p class="mb-2 text-sm font-semibold text-slate-700">Pertanyaan</p>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-relaxed text-slate-700">
                {!! nl2br(e($soal->soal)) !!}
            </div>
        </div>

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
