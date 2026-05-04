<x-banksoal::layouts.mahasiswa>

    {{-- Page Header --}}
    <div class="mb-10 border-b border-slate-200 pb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold tracking-widest text-slate-500 uppercase mb-3">Portal Akademik Mahasiswa</p>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">Riwayat Ujian</h1>
                <p class="text-sm text-slate-500 mt-2">Rekap seluruh ujian komprehensif yang pernah Anda ikuti.</p>
            </div>
        </div>
    </div>

    {{-- Statistik Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
        {{-- Total Ujian --}}
        <div class="border border-slate-200 bg-white p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Total Ujian</p>
            <p class="text-4xl font-black text-slate-900">{{ $totalUjian }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">sesi selesai</p>
        </div>

        {{-- Nilai Tertinggi --}}
        <div class="border border-slate-200 bg-white p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Tertinggi</p>
            <p class="text-4xl font-black {{ $nilaiTertinggi >= 60 ? 'text-slate-900' : 'text-red-600' }}">
                {{ $nilaiTertinggi > 0 ? $nilaiTertinggi : '—' }}
            </p>
            <p class="text-xs text-slate-500 mt-1 font-medium">dari 100 poin</p>
        </div>

        {{-- Rata-rata --}}
        <div class="border border-slate-200 bg-white p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Rata-rata</p>
            <p class="text-4xl font-black {{ $nilaiRataRata >= 60 ? 'text-slate-900' : 'text-red-600' }}">
                {{ $nilaiRataRata > 0 ? $nilaiRataRata : '—' }}
            </p>
            <p class="text-xs text-slate-500 mt-1 font-medium">poin per ujian</p>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="border border-slate-200 bg-white overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-black uppercase tracking-widest text-slate-700">Daftar Ujian</h2>
        </div>

        @if($sessions->isEmpty())
            {{-- Empty State --}}
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-16 h-16 border-2 border-slate-200 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-slate-900 font-bold text-base mb-2">Belum Ada Riwayat Ujian</p>
                <p class="text-slate-500 text-sm max-w-sm leading-relaxed">Anda belum pernah menyelesaikan ujian komprehensif. Riwayat akan muncul di sini setelah ujian selesai.</p>
                <a href="{{ route('komprehensif.mahasiswa.dashboard') }}"
                   class="mt-8 inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 text-white text-sm font-bold hover:bg-slate-700 transition-colors">
                    Ke Dashboard
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                            <th class="px-6 py-4">Periode Ujian</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Durasi</th>
                            <th class="px-6 py-4">Skor</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sessions as $session)
                            @php
                                $score       = $session->score ?? 0;
                                $dijawab     = $session->jawabans->whereNotNull('jawaban_dipilih')->count();
                                $totalSoal   = $session->jawabans->count();
                                $durasi      = null;
                                if ($session->started_at && $session->finished_at) {
                                    $diffInMinutes = $session->started_at->diffInMinutes($session->finished_at);
                                    $jam = floor($diffInMinutes / 60);
                                    $menit = $diffInMinutes % 60;
                                    
                                    if ($jam > 0 && $menit > 0) {
                                        $durasi = "{$jam} jam {$menit} mnt";
                                    } elseif ($jam > 0) {
                                        $durasi = "{$jam} jam";
                                    } else {
                                        $durasi = "{$menit} mnt";
                                    }
                                }

                                $lulus = $score >= 60;
                                $badgeBg    = $lulus ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200';
                                $badgeLabel = $lulus ? 'Lulus' : 'Tidak Lulus';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                {{-- Periode --}}
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 text-sm">
                                        {{ $session->jadwal->periode->nama_periode ?? 'Ujian Komprehensif' }}
                                    </p>
                                    @if($session->jadwal)
                                        <p class="text-xs text-slate-400 font-medium mt-0.5">
                                            Sesi: {{ $session->jadwal->nama_sesi ?? '-' }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-6 py-4">
                                    @if($session->finished_at)
                                        <p class="text-sm text-slate-700 font-medium">
                                            {{ \Carbon\Carbon::parse($session->finished_at)->translatedFormat('d M Y') }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ \Carbon\Carbon::parse($session->started_at)->format('H:i') }} –
                                            {{ \Carbon\Carbon::parse($session->finished_at)->format('H:i') }}
                                        </p>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Durasi --}}
                                <td class="px-6 py-4">
                                    @if($durasi !== null)
                                        <span class="text-sm font-medium text-slate-700">{{ $durasi }}</span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>


                                {{-- Skor --}}
                                <td class="px-6 py-4">
                                    <span class="text-2xl font-black {{ $lulus ? 'text-slate-900' : 'text-red-600' }}">
                                        {{ $score }}
                                    </span>
                                    <span class="text-slate-400 text-xs font-medium">/100</span>
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 border text-[11px] font-bold uppercase tracking-wider {{ $badgeBg }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $lulus ? 'bg-current' : 'bg-current' }}"></span>
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer tabel --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <p class="text-xs text-slate-400 font-medium">
                    Menampilkan <strong class="text-slate-600">{{ $sessions->count() }}</strong> catatan ujian.
                    Nilai minimum lulus: <strong class="text-slate-600">60</strong>.
                </p>
            </div>
        @endif
    </div>

</x-banksoal::layouts.mahasiswa>
