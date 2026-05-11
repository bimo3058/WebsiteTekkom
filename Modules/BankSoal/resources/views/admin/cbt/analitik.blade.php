<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Dasbor Analitik</span>
    @endsection

    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-7xl mx-auto space-y-8 font-inter">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Analitik Ujian Komprehensif</h1>
            </div>
            <!-- Optional Date Filter / Actions -->
        </div>

        <!-- 1. Top Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Peserta -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600">Total Selesai</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $totalPeserta }}</h3>
                    <span class="text-sm text-slate-500">Mahasiswa</span>
                </div>
            </div>

            <!-- Rata-rata -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600">Rata-rata Nilai</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $rataRata }}</h3>
                </div>
            </div>

            <!-- Tertinggi -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600">Nilai Tertinggi</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-emerald-600">{{ $tertinggi }}</h3>
                </div>
            </div>

            <!-- Terendah -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600">Nilai Terendah</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-rose-600">{{ $terendah }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- 2. Kelulusan (Kiri) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-800">Tingkat Kelulusan</h3>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-center">
                    @if($totalPeserta > 0)
                        @php
                            $persenLulus = round(($lulus / $totalPeserta) * 100);
                            $persenTidak = round(($tidakLulus / $totalPeserta) * 100);
                        @endphp
                        
                        <!-- Diagram Lingkaran (Pie Chart) -->
                        <div class="flex flex-col items-center justify-center mb-8 border-b border-slate-100 pb-8">
                            <!-- Legend -->
                            <div class="flex justify-center gap-6 mb-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-2.5 bg-emerald-500 rounded-sm"></div>
                                    <span class="text-xs font-bold text-slate-600">Lulus ({{ $persenLulus }}%)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-2.5 bg-rose-500 rounded-sm"></div>
                                    <span class="text-xs font-bold text-slate-600">Tidak Lulus ({{ $persenTidak }}%)</span>
                                </div>
                            </div>

                            <!-- Pie Chart -->
                            <div class="w-44 h-44 rounded-full shadow-sm" 
                                 style="background: conic-gradient(#10b981 0% {{ $persenLulus }}%, #f43f5e {{ $persenLulus }}% 100%);">
                            </div>
                        </div>

                        <!-- Keterangan Jumlah -->
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="p-3 bg-emerald-50 rounded-lg">
                                <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-1">Total Lulus</p>
                                <p class="text-2xl font-black text-emerald-600">{{ $lulus }} <span class="text-xs font-medium text-emerald-700/70">Mhs</span></p>
                            </div>
                            <div class="p-3 bg-rose-50 rounded-lg">
                                <p class="text-[10px] font-black text-rose-800 uppercase tracking-widest mb-1">Tidak Lulus</p>
                                <p class="text-2xl font-black text-rose-600">{{ $tidakLulus }} <span class="text-xs font-medium text-rose-700/70">Mhs</span></p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-400">
                            <svg class="mx-auto w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm">Belum ada data ujian.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Mapping CPL (Kanan, 2 Kolom) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-800">Capaian Pembelajaran Lulusan (CPL)</h3>
                    <p class="text-xs text-slate-500 mt-1">Persentase jawaban benar agregat berdasarkan pemetaan CPL pada soal ujian.</p>
                </div>
                <div class="p-6">
                    @if($cplStats && count($cplStats) > 0)
                        <div class="space-y-5">
                            @foreach($cplStats as $stat)
                                <div>
                                    <div class="flex justify-between items-start text-sm mb-2 gap-4">
                                        <span class="font-medium text-slate-700 leading-snug">
                                            {{ $stat['cpl_kode'] }} 
                                            <span class="text-slate-500 font-normal ml-1">{{ $stat['deskripsi'] }}</span>
                                        </span>
                                        <span class="font-bold text-slate-900 shrink-0">{{ $stat['persentase'] }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        @php
                                            // Warna dinamis berdasarkan persentase
                                            $color = 'bg-rose-500'; // Merah (< 60)
                                            if ($stat['persentase'] >= 80) $color = 'bg-emerald-500'; // Hijau (>= 80)
                                            elseif ($stat['persentase'] >= 60) $color = 'bg-amber-400'; // Kuning (60-79)
                                        @endphp
                                        <div class="{{ $color }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $stat['persentase'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-slate-400">
                            <p class="text-sm">Data analisis CPL belum tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Soal Tersulit -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-800">10 Soal Tersulit</h3>
                <p class="text-xs text-slate-500 mt-1">Pertanyaan yang paling banyak dijawab salah oleh mahasiswa.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 bg-slate-50/80 uppercase border-b border-slate-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium w-16 text-center">Rank</th>
                            <th scope="col" class="px-6 py-3 font-medium">Potongan Soal</th>
                            <th scope="col" class="px-6 py-3 font-medium">CPL Terkait</th>
                            <th scope="col" class="px-6 py-3 font-medium text-center">Jumlah Salah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if($kesalahanPerSoal && count($kesalahanPerSoal) > 0)
                            @foreach($kesalahanPerSoal as $idx => $item)
                                @php 
                                    $soal = $item['pertanyaan'];
                                    $salahCount = $item['salah_count'];
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $loop->iteration <= 3 ? 'bg-rose-100 text-rose-600 font-bold' : 'bg-slate-100 text-slate-500 font-medium' }}">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-slate-800 max-w-lg line-clamp-2 prose prose-sm prose-slate">
                                            {!! strip_tags($soal->soal ?? '-') !!}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700">
                                            {{ $soal->cpl ? $soal->cpl->kode : 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold text-rose-600">{{ $salahCount }}</span>
                                        <span class="text-xs text-slate-500 ml-1">peserta</span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada data evaluasi soal.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-banksoal::layouts.admin>
