<x-banksoal::layouts.gpm-master>
    <x-banksoal::ui.page-header title="Dashboard GPM" subtitle="Ringkasan aktivitas penjaminan mutu akademik." />

    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Perhatian: Ada 3 RPS dan 5 Paket Soal yang mendekati batas waktu review.</p>
                    <p class="text-xs text-amber-800">Segera lakukan peninjauan sebelum batas waktu berakhir untuk menjaga kualitas akademik.</p>
                </div>
            </div>
            <button class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-amber-700">
                Lihat Detail
            </button>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-3">
        <x-banksoal::ui.stat-card label="RPS Menunggu Validasi" :value="$statRpsMenunggu" icon="fa-file-alt" tone="blue" />
        <x-banksoal::ui.stat-card label="Bank Soal Menunggu" :value="$statBankSoalMenunggu" icon="fa-question-circle" tone="amber" />
        <x-banksoal::ui.stat-card label="Selesai Direview Bulan Ini" :value="$tugasSelesai" icon="fa-check-circle" tone="green" />
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Tugas Prioritas</h2>
            <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat Semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Dokumen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tugasPrioritas as $tugas)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                @if($tugas->tipe_dokumen == 'Bank Soal')
                                    <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-[11px] font-semibold text-purple-700 border border-purple-200">Bank Soal</span>
                                @elseif(($tugas->sub_status ?? '') == 'revisi')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200">RPS - Revisi</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700 border border-blue-200">RPS - Diajukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $tugas->mk_nama }}</div>
                                <div class="text-xs text-slate-500">{{ $tugas->mk_kode }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">Menunggu Review</td>
                            <td class="px-6 py-4 text-right">
                                @if($tugas->tipe_dokumen == 'Bank Soal')
                                    <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Review Sekarang</a>
                                @else
                                    <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $tugas->rps_id) }}" class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Review Sekarang</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-check-circle text-3xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Semua tugas selesai.</p>
                                    <p class="text-xs text-slate-500">Tidak ada tugas prioritas yang perlu segera direview.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-banksoal::layouts.gpm-master>