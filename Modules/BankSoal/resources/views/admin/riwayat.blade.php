<x-banksoal::layouts.admin>
    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Riwayat Ujian Komprehensif</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar rekapitulasi nilai akhir dari mahasiswa yang telah menyelesaikan ujian.</p>
            </div>
            <div class="flex gap-2">
                <!-- Nantinya fitur export ke CSV bisa dihidupkan -->
                <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 border border-transparent text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Rekap (CSV)
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Peserta</th>
                            <th class="px-6 py-4 font-semibold">Sesi</th>
                            <th class="px-6 py-4 font-semibold">Waktu Mulai</th>
                            <th class="px-6 py-4 font-semibold">Waktu Selesai</th>
                            <th class="px-6 py-4 font-semibold">Skor Akhir</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($session->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $session->user->name }}</p>
                                            <p class="text-slate-500 text-xs">{{ $session->user->nim ?? 'NIM tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $session->title }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($session->started_at)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($session->score !== null)
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold {{ $session->score >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $session->score }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-xs">Belum dikalkulasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('banksoal.admin.cbt.detail', $session->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        </div>
                                        <p class="text-slate-500 text-sm font-medium">Belum ada riwayat ujian yang selesai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-banksoal::layouts.admin>
