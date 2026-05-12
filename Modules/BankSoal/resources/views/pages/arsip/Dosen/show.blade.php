<x-banksoal::layouts.dosen-admin>

<x-banksoal::ui.page-header
    title="{{ $mode === 'arsip' ? 'Detail Arsip Soal' : 'Detail Riwayat Penarikan' }}"
    subtitle="{{ $record->nama_arsip ?? $record->nama_ekstraksi }}">
    <x-slot:actions>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors">
            <i class="fas fa-arrow-left w-4"></i> Kembali
        </a>
    </x-slot:actions>
</x-banksoal::ui.page-header>

<x-banksoal::ui.panel title="Informasi Umum" padding="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700">
        <div><span class="font-semibold">Mata Kuliah:</span> {{ $record->mataKuliah->nama ?? '-' }}</div>
        <div><span class="font-semibold">Dosen:</span> {{ auth()->user()->name ?? '-' }}</div>
        <div><span class="font-semibold">Tahun Akademik:</span> {{ $record->tahun_akademik ?? '-' }}</div>
        <div><span class="font-semibold">Semester:</span> {{ $record->semester ?? '-' }}</div>
        <div><span class="font-semibold">Tipe Ujian:</span> {{ strtoupper($record->tipe_ujian ?? '-') }}</div>
        <div>
            <span class="font-semibold">Metode Ujian:</span> 
            @if(($record->metode_ujian ?? '') === 'offline')
                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Cetak Kertas (Offline)</span>
            @else
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Online</span>
            @endif
        </div>
        <div><span class="font-semibold">Status:</span> {{ strtoupper($record->status ?? '-') }}</div>
        @if(($record->metode_ujian ?? '') === 'offline' && $record->status_cetak)
        <div>
            <span class="font-semibold">Status Cetak:</span> 
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">{{ ucfirst($record->status_cetak) }}</span>
        </div>
        @endif
    </div>
</x-banksoal::ui.panel>

<div class="mt-6">
    <x-banksoal::ui.panel title="Daftar Soal" padding="p-0">
        <div class="divide-y divide-slate-100">
            @forelse($soalList as $soal)
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 font-bold flex items-center justify-center">{{ $soal['nomor'] ?? $loop->iteration }}</div>
                        <div class="flex-1">
                            <div class="text-sm text-slate-800 leading-relaxed">
                                {!! $soal['soal'] ?? '-' !!}
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                @if(!empty($soal['cpl']))<span class="px-2 py-1 rounded-full bg-slate-100">CPL: {{ $soal['cpl'] }}</span>@endif
                                @if(!empty($soal['cpmk']))<span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">CPMK: {{ $soal['cpmk'] }}</span>@endif
                                @if(!empty($soal['bobot']))<span class="px-2 py-1 rounded-full bg-amber-50 text-amber-700">Bobot: {{ $soal['bobot'] }}</span>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500">Tidak ada soal yang tersimpan.</div>
            @endforelse
        </div>
    </x-banksoal::ui.panel>
</div>

</x-banksoal::layouts.dosen-admin>