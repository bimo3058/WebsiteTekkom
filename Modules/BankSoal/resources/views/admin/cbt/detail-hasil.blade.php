<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.cbt.riwayat') }}" class="text-slate-500 hover:text-primary transition-colors">Riwayat Ujian</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Detail Hasil</span>
    @endsection

    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('banksoal.admin.cbt.riwayat') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-primary mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke Riwayat
                </a>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Detail Hasil Ujian</h1>
                <p class="text-sm text-slate-500 mt-1">Review lengkap jawaban mahasiswa.</p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Peserta -->
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Peserta</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                            {{ substr($session->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $session->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ optional($session->user->student)->student_number ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sesi -->
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Sesi Ujian</p>
                    <p class="font-semibold text-slate-900">{{ $session->title }}</p>
                    @if($session->jadwal)
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($session->jadwal->tanggal_ujian)->translatedFormat('d F Y') }}</p>
                    @endif
                </div>

                <!-- Waktu -->
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Durasi Pengerjaan</p>
                    <p class="font-semibold text-slate-900">
                        {{ \Carbon\Carbon::parse($session->started_at)->format('H:i') }}
                        —
                        {{ $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->format('H:i') : '-' }}
                    </p>
                    @if($session->started_at && $session->finished_at)
                        <p class="text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($session->started_at)->diffInMinutes(\Carbon\Carbon::parse($session->finished_at)) }} menit
                        </p>
                    @endif
                </div>

                <!-- Skor -->
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Skor Akhir</p>
                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold {{ $session->score >= 60 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $session->score ?? 0 }}
                        </span>
                        <span class="text-sm text-slate-400">/ 100</span>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            @php
                $totalSoal = $session->jawabans->count();
                $benar = $session->jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
                $salah = $session->jawabans->filter(fn($j) => $j->jawaban_dipilih && (!$j->opsiTerpilih || !$j->opsiTerpilih->is_benar))->count();
                $kosong = $session->jawabans->filter(fn($j) => !$j->jawaban_dipilih)->count();
            @endphp
            <div class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex flex-wrap gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-slate-600">Benar: <strong class="text-slate-800">{{ $benar }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-slate-600">Salah: <strong class="text-slate-800">{{ $salah }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                    <span class="text-slate-600">Tidak Dijawab: <strong class="text-slate-800">{{ $kosong }}</strong></span>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <span class="text-slate-600">Total: <strong class="text-slate-800">{{ $totalSoal }} soal</strong></span>
                </div>
            </div>
        </div>

        <!-- Daftar Soal & Jawaban -->
        <div class="space-y-4">
            @foreach($session->jawabans->sortBy('urutan_soal') as $idx => $kompreJawaban)
                @php
                    $pertanyaan = $kompreJawaban->pertanyaan;
                    $terpilihId = $kompreJawaban->jawaban_dipilih;
                    $opsiTerpilih = $kompreJawaban->opsiTerpilih;
                    $isBenar = $opsiTerpilih && $opsiTerpilih->is_benar;
                    $isTidakDijawab = !$terpilihId;
                @endphp
                <div class="bg-white rounded-lg border {{ $isTidakDijawab ? 'border-slate-200' : ($isBenar ? 'border-green-200' : 'border-red-200') }} shadow-sm overflow-hidden">
                    
                    <!-- Soal Header -->
                    <div class="px-6 py-4 flex items-start gap-4 {{ $isTidakDijawab ? 'bg-slate-50' : ($isBenar ? 'bg-green-50' : 'bg-red-50') }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm shrink-0 {{ $isTidakDijawab ? 'bg-slate-200 text-slate-600' : ($isBenar ? 'bg-green-500 text-white' : 'bg-red-500 text-white') }}">
                            {{ $kompreJawaban->urutan_soal }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="prose max-w-none text-slate-800 text-sm">{!! $pertanyaan->soal ?? '-' !!}</div>
                        </div>
                        <div class="shrink-0">
                            @if($isTidakDijawab)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600">Tidak Dijawab</span>
                            @elseif($isBenar)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700">Benar</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-700">Salah</span>
                            @endif
                        </div>
                    </div>

                    <!-- Opsi Jawaban -->
                    @if($pertanyaan && $pertanyaan->jawabans)
                        <div class="px-6 py-4 space-y-2">
                            @foreach($pertanyaan->jawabans as $opsiIdx => $opsi)
                                @php
                                    $isSelected = $terpilihId == $opsi->id;
                                    $isCorrect = $opsi->is_benar;
                                @endphp
                                <div class="flex items-start gap-3 p-3 rounded-lg text-sm
                                    {{ $isCorrect ? 'bg-green-50 border border-green-200' : ($isSelected ? 'bg-red-50 border border-red-200' : 'bg-white border border-slate-100') }}">
                                    
                                    <div class="w-7 h-7 rounded-md flex items-center justify-center font-bold text-xs shrink-0
                                        {{ $isCorrect ? 'bg-green-500 text-white' : ($isSelected ? 'bg-red-500 text-white' : 'bg-slate-100 text-slate-500') }}">
                                        {{ $opsi->opsi ?? chr(65 + $opsiIdx) }}
                                    </div>

                                    <div class="flex-1 text-slate-700">{!! $opsi->deskripsi !!}</div>
                                    
                                    <div class="shrink-0 flex items-center gap-1">
                                        @if($isCorrect)
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="text-xs font-medium text-green-600">Kunci</span>
                                        @endif
                                        @if($isSelected && !$isCorrect)
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            <span class="text-xs font-medium text-red-600">Dipilih</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>
</x-banksoal::layouts.admin>
