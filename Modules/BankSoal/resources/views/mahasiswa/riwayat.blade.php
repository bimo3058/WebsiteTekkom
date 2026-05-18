<x-banksoal::layouts.mahasiswa>
    @section('breadcrumbs')
        <span class="text-slate-900 font-semibold">Riwayat Ujian</span>
    @endsection

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Riwayat Ujian</h1>
                <p class="text-base text-gray-500 mt-2">Rekap seluruh ujian komprehensif yang pernah Anda ikuti.</p>
            </div>
        </div>
    </div>

    {{-- Statistik Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-5 mb-8">
        {{-- Total Ujian --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Total Ujian</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-bold tracking-tight text-gray-900">{{ $totalUjian }}</p>
            </div>
        </div>

        {{-- Nilai Tertinggi --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Tertinggi</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-bold tracking-tight {{ $nilaiTertinggi >= 60 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $nilaiTertinggi > 0 ? (int)$nilaiTertinggi : '—' }}
                </p>
                @if($nilaiTertinggi > 0)
                <p class="text-sm text-gray-400 font-medium">/ 100</p>
                @endif
            </div>
        </div>

        {{-- Rata-rata --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-center col-span-2 md:col-span-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Rata-rata</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-bold tracking-tight {{ $nilaiRataRata >= 60 ? 'text-gray-900' : 'text-rose-600' }}">
                    {{ $nilaiRataRata > 0 ? $nilaiRataRata : '—' }}
                </p>
                @if($nilaiRataRata > 0)
                <p class="text-sm text-gray-400 font-medium">poin</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
            <h2 class="text-base font-semibold text-gray-900">Daftar Ujian</h2>
        </div>

        @if($sessions->isEmpty())
            {{-- Empty State --}}
            <div class="py-20 flex flex-col items-center justify-center text-center px-6 bg-[#F9FAFB]">
                <div class="w-16 h-16 bg-white rounded-full border border-gray-200 shadow-sm flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-gray-900 font-semibold text-base mb-1">Belum Ada Riwayat Ujian</p>
                <p class="text-gray-500 text-sm max-w-sm leading-relaxed mb-6">Anda belum pernah menyelesaikan ujian komprehensif. Riwayat akan muncul di sini setelah ujian selesai.</p>
                <a href="{{ route('komprehensif.mahasiswa.dashboard') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#2A3A7C] text-white text-sm font-semibold rounded-lg hover:bg-[#202c60] transition-colors shadow-sm">
                    Ke Dashboard
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-slate-50/80 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5 text-[13px] font-medium text-slate-500">Periode Ujian</th>
                            <th class="px-6 py-3.5 text-[13px] font-medium text-slate-500">Tanggal</th>
                            <th class="px-6 py-3.5 text-[13px] font-medium text-slate-500">Durasi</th>
                            <th class="px-6 py-3.5 text-[13px] font-medium text-slate-500">Skor</th>
                            <th class="px-6 py-3.5 text-[13px] font-medium text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sessions as $session)
                            @php
                                $score       = (int) ($session->score ?? 0);
                                $isNoShow    = $session->title === 'Tidak Mengerjakan';
                                $durasi      = null;

                                if ($isNoShow) {
                                    $durasi = '0 Menit';
                                } elseif ($session->started_at && $session->finished_at) {
                                    $diffInMinutes = $session->started_at->diffInMinutes($session->finished_at);
                                    $jam = floor($diffInMinutes / 60);
                                    $menit = $diffInMinutes % 60;
                                    
                                    if ($jam > 0 && $menit > 0) {
                                        $durasi = "{$jam} Jam {$menit} Menit";
                                    } elseif ($jam > 0) {
                                        $durasi = "{$jam} Jam";
                                    } else {
                                        $durasi = "{$menit} Menit";
                                    }
                                }

                                $lulus = $score >= 60;
                                $badgeClass = $lulus ? 'border-emerald-500 text-emerald-600' : 'border-slate-800 text-slate-800';
                                $badgeDot   = $lulus ? 'bg-emerald-500' : 'bg-slate-800';
                                $badgeLabel = $isNoShow ? 'Tidak Mengerjakan' : ($lulus ? 'Lulus' : 'Tidak Lulus');
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                {{-- Periode --}}
                                <td class="px-6 py-4">
                                    <p class="text-[13px] font-medium text-slate-700">
                                        {{ $session->jadwal->periode->nama_periode ?? 'Ujian Komprehensif' }}
                                        @if($session->jadwal)
                                            <span class="text-slate-400 font-normal ml-1">(Sesi {{ $session->jadwal->nama_sesi ?? '-' }})</span>
                                        @endif
                                    </p>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-6 py-4">
                                    @if($session->finished_at)
                                        <p class="text-[13px] text-slate-700">
                                            {{ \Carbon\Carbon::parse($session->finished_at)->translatedFormat('d F Y') }}
                                            <span class="text-slate-400 ml-1">{{ \Carbon\Carbon::parse($session->started_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->finished_at)->format('H:i') }}</span>
                                        </p>
                                    @else
                                        <span class="text-slate-400 text-[13px]">—</span>
                                    @endif
                                </td>

                                {{-- Durasi --}}
                                <td class="px-6 py-4 text-[13px] text-slate-700">
                                    {{ $durasi ?? '—' }}
                                </td>

                                {{-- Skor --}}
                                <td class="px-6 py-4">
                                    <span class="text-[13px] font-semibold {{ $lulus ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $score }}<span class="text-slate-400 font-normal">/100</span>
                                    </span>
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border {{ $badgeClass }} text-[11px] font-medium tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badgeDot }}"></span>
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer tabel --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-[#F9FAFB] flex items-center justify-between">
                <p class="text-[13px] text-gray-500 font-medium">
                    Menampilkan <span class="text-gray-900 font-semibold">{{ $sessions->count() }}</span> catatan ujian
                </p>
                <p class="text-[13px] text-gray-500 font-medium">
                    Nilai minimum lulus: <span class="text-gray-900 font-semibold">60</span>
                </p>
            </div>
        @endif
    </div>

</x-banksoal::layouts.mahasiswa>
