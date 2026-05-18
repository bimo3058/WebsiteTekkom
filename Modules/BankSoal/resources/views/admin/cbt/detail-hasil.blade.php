<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-gray-500 hover:text-[#2A3A7C] transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-gray-300">/</span>
    <a href="{{ route('banksoal.admin.cbt.riwayat') }}" class="text-gray-500 hover:text-[#2A3A7C] transition-colors">Riwayat Ujian</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-800 font-medium">Detail Hasil</span>
    @endsection

    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-5xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('banksoal.admin.cbt.riwayat') }}" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-gray-500 hover:text-[#2A3A7C] mb-3 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Riwayat
                </a>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Detail Hasil Ujian</h1>
                <p class="text-[13px] text-gray-500 mt-1">Evaluasi lengkap jawaban dan pencapaian mahasiswa.</p>
            </div>
        </div>

        @php
            $isNoShow    = $session->title === 'Tidak Mengerjakan';
            $totalSoal   = $session->jawabans->count();
            $benar       = $session->jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
            $salah       = $session->jawabans->filter(fn($j) => $j->jawaban_dipilih && (!$j->opsiTerpilih || !$j->opsiTerpilih->is_benar))->count();
            $tidakDijawab = $totalSoal - ($benar + $salah);

            $durasiMenit = 0;
            if (!$isNoShow && $session->started_at && $session->finished_at) {
                $durasiMenit = (int) \Carbon\Carbon::parse($session->started_at)->diffInMinutes(\Carbon\Carbon::parse($session->finished_at));
            }
        @endphp

        <!-- Profil & Ringkasan Skor (Split Panel) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row">
            
            <!-- Bagian Kiri: Profil Mahasiswa -->
            <div class="flex-1 p-5 md:p-6 flex flex-col justify-start">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 rounded-full bg-[#2A3A7C]/10 text-[#2A3A7C] flex items-center justify-center font-bold text-lg shrink-0 border border-[#2A3A7C]/20">
                        {{ substr($session->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ $session->user->name }}</h2>
                        <p class="text-sm text-gray-500 font-mono mt-0.5">{{ optional($session->user->student)->student_number ?? '—' }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-x-10 gap-y-4">
                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-0.5">Periode</p>
                        <p class="text-[13px] text-gray-900 font-medium">{{ $session->jadwal->periode->nama_periode ?? 'Ujian Komprehensif' }}</p>
                        <p class="text-[12px] text-gray-500 mt-0.5">
                            Sesi {{ $session->jadwal->nama_sesi ?? '—' }} 
                            @if($session->jadwal?->tanggal_ujian)
                                &bull; {{ \Carbon\Carbon::parse($session->jadwal->tanggal_ujian)->locale('id')->isoFormat('D MMMM YYYY') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-0.5">Waktu Pengerjaan</p>
                        @if(!$isNoShow && $session->started_at && $session->finished_at)
                            <p class="text-[13px] text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($session->started_at)->format('H:i') }} — {{ \Carbon\Carbon::parse($session->finished_at)->format('H:i') }}
                            </p>
                            <p class="text-[12px] text-gray-500 mt-0.5">{{ $durasiMenit }} Menit</p>
                        @else
                            <p class="text-[13px] text-gray-400">—</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan: Skor Akhir -->
            <div class="w-full md:w-64 bg-[#F9FAFB] border-t md:border-t-0 md:border-l border-gray-200 p-5 md:p-6 flex flex-col justify-center">
                <div class="flex items-end justify-between mb-3">
                    <p class="text-[12px] font-medium text-gray-500">Skor Akhir</p>
                    <div class="flex items-baseline gap-0.5">
                        <span class="text-4xl font-bold tracking-tight leading-none {{ $session->score >= 60 ? 'text-emerald-600' : 'text-rose-600' }}">{{ (int) ($session->score ?? 0) }}</span>
                        <span class="text-sm font-medium text-gray-400">/100</span>
                    </div>
                </div>
                
                <div class="w-full space-y-2 pt-3 border-t border-gray-200/60">
                    <div class="flex items-center justify-between text-[12px]">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-gray-600">Benar</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $benar }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[12px]">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                            <span class="text-gray-600">Salah</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $salah }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[12px]">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full border-2 border-gray-300"></div>
                            <span class="text-gray-600">Kosong</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $tidakDijawab }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Soal & Jawaban -->
        <div class="space-y-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900">Review Jawaban</h3>
            
            <div class="space-y-4">
                @forelse($session->jawabans->sortBy('urutan_soal') as $idx => $kompreJawaban)
                    @php
                        $pertanyaan = $kompreJawaban->pertanyaan;
                        $terpilihId = $kompreJawaban->jawaban_dipilih;
                        $opsiTerpilih = $kompreJawaban->opsiTerpilih;
                        $isBenar = $opsiTerpilih && $opsiTerpilih->is_benar;
                        $isTidakDijawab = !$terpilihId;

                        // Tentukan style state
                        if ($isTidakDijawab) {
                            $borderColor = 'border-gray-200';
                            $leftAccent = 'bg-gray-300';
                            $badgeStyle = 'bg-gray-100 text-gray-600 border border-gray-200';
                            $badgeText = 'Tidak Dijawab';
                        } elseif ($isBenar) {
                            $borderColor = 'border-emerald-200';
                            $leftAccent = 'bg-emerald-500';
                            $badgeStyle = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                            $badgeText = 'Benar';
                        } else {
                            $borderColor = 'border-rose-200';
                            $leftAccent = 'bg-rose-500';
                            $badgeStyle = 'bg-rose-50 text-rose-700 border border-rose-200';
                            $badgeText = 'Salah';
                        }
                    @endphp

                    <div class="relative bg-white rounded-xl border {{ $borderColor }} shadow-sm overflow-hidden flex flex-col">
                        <!-- Left Accent Line -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $leftAccent }}"></div>
                        
                        <!-- Soal Content -->
                        <div class="px-6 py-5 pl-8 border-b border-gray-100 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-2 flex-1">
                                <div class="shrink-0">
                                    <span class="text-[14px] font-semibold text-gray-900">{{ $kompreJawaban->urutan_soal }}.</span>
                                </div>
                                <div class="flex-1">
                                    <div class="prose max-w-none text-[14px] text-gray-800 leading-relaxed mb-2 prose-p:mt-0 prose-headings:mt-0">
                                        {!! $pertanyaan->soal ?? '<span class="text-gray-400 italic">Teks soal tidak tersedia.</span>' !!}
                                    </div>
                                    @if($pertanyaan && $pertanyaan->cpl)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-[#2A3A7C]/5 text-[#2A3A7C] border border-[#2A3A7C]/20 uppercase tracking-wide" title="{{ $pertanyaan->cpl->deskripsi }}">
                                            {{ $pertanyaan->cpl->kode }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 ml-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold uppercase tracking-wide {{ $badgeStyle }}">
                                    {{ $badgeText }}
                                </span>
                            </div>
                        </div>

                        <!-- Opsi Jawaban -->
                        @if($pertanyaan && $pertanyaan->jawabans)
                            <div class="p-5 pl-8 pr-6 space-y-2.5 bg-gray-50/50">
                                @foreach($pertanyaan->jawabans as $opsiIdx => $opsi)
                                    @php
                                        $isSelected = $terpilihId == $opsi->id;
                                        $isCorrect = $opsi->is_benar;

                                        $opsiBg = 'bg-white hover:bg-gray-50';
                                        $opsiBorder = 'border-gray-200';
                                        $opsiText = 'text-gray-600';
                                        $letterBg = 'bg-gray-100 text-gray-500 border border-gray-200';

                                        if ($isCorrect) {
                                            $opsiBg = 'bg-emerald-50/40';
                                            $opsiBorder = 'border-emerald-300 ring-1 ring-emerald-300';
                                            $opsiText = 'text-emerald-900 font-medium';
                                            $letterBg = 'bg-emerald-500 text-white border-emerald-600 shadow-sm';
                                        } elseif ($isSelected && !$isCorrect) {
                                            $opsiBg = 'bg-rose-50/40';
                                            $opsiBorder = 'border-rose-300';
                                            $opsiText = 'text-rose-900';
                                            $letterBg = 'bg-rose-500 text-white border-rose-600 shadow-sm';
                                        }
                                    @endphp
                                    <div class="flex items-start gap-3 p-3 rounded-lg border text-[13px] transition-colors {{ $opsiBg }} {{ $opsiBorder }}">
                                        
                                        <div class="w-6 h-6 rounded flex items-center justify-center font-bold text-[11px] shrink-0 mt-0.5 {{ $letterBg }}">
                                            {{ $opsi->opsi ?? chr(65 + $opsiIdx) }}
                                        </div>

                                        <div class="flex-1 {{ $opsiText }} leading-relaxed pt-0.5">
                                            {!! $opsi->deskripsi !!}
                                        </div>
                                        
                                        <div class="shrink-0 flex items-center gap-1.5 pt-0.5">
                                            @if($isCorrect)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    Kunci Jawaban
                                                </span>
                                            @endif
                                            @if($isSelected && !$isCorrect)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-700 bg-rose-100 px-2 py-0.5 rounded">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    Jawaban Peserta
                                                </span>
                                            @endif
                                            @if($isSelected && $isCorrect)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded ml-1">
                                                    Dipilih
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">Belum ada data jawaban tersimpan.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-banksoal::layouts.admin>
