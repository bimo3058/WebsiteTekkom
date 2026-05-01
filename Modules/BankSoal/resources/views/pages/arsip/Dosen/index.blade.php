<x-banksoal::layouts.dosen-admin>

<x-banksoal::ui.page-header title="Arsip Soal" subtitle="Kelola dan tinjau riwayat soal yang telah diarsipkan per semester.">
    <x-slot:actions>
        <a href="#" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors">
            <i class="fas fa-download w-4"></i> Export Arsip
        </a>
    </x-slot:actions>
</x-banksoal::ui.page-header>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-banksoal::ui.stat-card label="Total Soal Diarsipkan" :value="$stats['total'] ?? 248" icon="fa-archive" tone="blue" />
    <x-banksoal::ui.stat-card label="Mata Kuliah" :value="$stats['mata_kuliah'] ?? 12" icon="fa-book-open" tone="green" />
    <x-banksoal::ui.stat-card label="Semester Tercatat" :value="$stats['semester'] ?? 6" icon="fa-calendar-alt" tone="amber" />
    <x-banksoal::ui.stat-card label="Tahun Ajaran" :value="$stats['tahun'] ?? 3" icon="fa-clock-rotate-left" tone="slate" />
</div>

<x-banksoal::ui.panel title="Daftar Arsip Soal" padding="p-0">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="searchArsip" placeholder="Cari soal, mata kuliah, atau topik..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none">
        </div>
        <select class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none">
            <option value="">Semua Tahun</option>
            @foreach($tahunAjarans ?? ['2023/2024','2022/2023','2021/2022'] as $ta)
                <option value="{{ $ta }}">{{ $ta }}</option>
            @endforeach
        </select>
        <select class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none">
            <option value="">Semua Semester</option>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
        </select>
        <select class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none">
            <option value="">Semua Tingkat</option>
            <option value="Easy">Easy</option>
            <option value="Medium">Medium</option>
            <option value="Hard">Hard</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID Soal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Topik</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Semester</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tingkat</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse(($arsips ?? collect()) as $arsip)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $arsip->kode }}</td>
                    <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 mr-2">{{ $arsip->mataKuliah->kode }}</span><span class="text-slate-600">{{ $arsip->mataKuliah->nama }}</span></td>
                    <td class="px-6 py-4 text-slate-600">{{ $arsip->topik }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $arsip->semester }} {{ $arsip->tahun_ajaran }}</td>
                    <td class="px-6 py-4">@php $d = strtolower($arsip->tingkat_kesulitan ?? ''); @endphp
                        @if($d === 'easy')<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ ucfirst($arsip->tingkat_kesulitan) }}</span>
                        @elseif($d === 'hard')<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ ucfirst($arsip->tingkat_kesulitan) }}</span>
                        @else<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ ucfirst($arsip->tingkat_kesulitan) }}</span>@endif
                    </td>
                    <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Diarsipkan</span></td>
                    <td class="px-6 py-4"><div class="flex items-center gap-2"><button class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Lihat"><i class="fas fa-eye text-sm"></i></button><button class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Pulihkan"><i class="fas fa-rotate-left text-sm"></i></button><button class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus"><i class="fas fa-trash text-sm"></i></button></div></td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-slate-600"><div class="flex flex-col items-center justify-center"><i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i><p class="font-medium">Belum ada data arsip.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
        <span class="text-sm text-slate-600">Showing 1–6 of {{ $arsips?->total() ?? 248 }} entries</span>
    </div>
</x-banksoal::ui.panel>

<script src="{{ asset('modules/banksoal/js/Banksoal/shared/SearchHandler.js') }}"></script>

</x-banksoal::layouts.dosen-admin>
