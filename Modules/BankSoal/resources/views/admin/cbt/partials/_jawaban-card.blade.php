@php
    $pertanyaan    = $kompreJawaban->pertanyaan;
    $terpilihId    = $kompreJawaban->jawaban_dipilih;
    $opsiTerpilih  = $kompreJawaban->opsiTerpilih;
    $isBenar       = $opsiTerpilih && $opsiTerpilih->is_benar;
    $isTidakDijawab = !$terpilihId;
@endphp

<div class="bg-white rounded-xl border border-gray-200 mb-5">
    {{-- Header / Question --}}
    <div class="px-6 pt-5 pb-5 border-b border-gray-100">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="text-[15px] font-bold text-gray-900">{{ $kompreJawaban->urutan_soal }}.</span>
                @if($pertanyaan?->cpl && (!isset($hideCplBadge) || !$hideCplBadge))
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-50 text-gray-500 border border-gray-200"
                          title="{{ $pertanyaan->cpl->deskripsi }}">
                        {{ $pertanyaan->cpl->kode }}
                    </span>
                @endif
            </div>
            
            {{-- Status Badge --}}
            @if ($isTidakDijawab)
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                    Kosong
                </span>
            @elseif ($isBenar)
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                    Benar
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-200/60">
                    Salah
                </span>
            @endif
        </div>

        <div class="prose max-w-none text-[14px] text-gray-800 leading-relaxed ml-7 prose-p:mt-0">
            {!! $pertanyaan->soal ?? '<span class="text-gray-400 italic">Teks soal tidak tersedia.</span>' !!}
        </div>
    </div>

    {{-- Options --}}
    @if($pertanyaan?->jawabans)
        <div class="px-6 py-5 bg-gray-50/30 rounded-b-xl">
            <div class="ml-7 space-y-2.5">
                @foreach($pertanyaan->jawabans as $opsiIdx => $opsi)
                    @php
                        $isSelected = $terpilihId == $opsi->id;
                        $isCorrect  = $opsi->is_benar;
                        $letter     = $opsi->opsi ?? chr(65 + $opsiIdx);

                        if ($isCorrect) {
                            $optBorder  = 'border border-emerald-300';
                            $optBg      = 'bg-emerald-50/50';
                            $letterCls  = 'border border-emerald-200 text-emerald-600 bg-white';
                            $textCls    = 'text-gray-900 font-medium';
                            $opacity    = '';
                        } elseif ($isSelected && !$isCorrect) {
                            $optBorder  = 'border border-rose-200';
                            $optBg      = 'bg-rose-50/50';
                            $letterCls  = 'border border-rose-200 text-rose-600 bg-white';
                            $textCls    = 'text-gray-900 font-medium';
                            $opacity    = '';
                        } else {
                            $optBorder  = 'border border-gray-200';
                            $optBg      = 'bg-white hover:bg-gray-50/80';
                            $letterCls  = 'border border-gray-200 text-gray-500 bg-gray-50/50';
                            $textCls    = 'text-gray-600';
                            $opacity    = 'opacity-80';
                        }
                    @endphp
                    <div class="flex items-start p-3 rounded-lg {{ $optBorder }} {{ $optBg }} {{ $opacity }} transition-colors">
                        <div class="w-7 h-7 rounded-md flex items-center justify-center font-bold text-[12px] flex-shrink-0 mr-3.5 {{ $letterCls }}">
                            {{ $letter }}
                        </div>
                        <div class="flex-1 flex items-start justify-between gap-3">
                            <div class="{{ $textCls }} text-[13.5px] leading-relaxed pt-0.5">
                                {!! $opsi->deskripsi !!}
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 pt-0.5">
                                @if($isCorrect && $isSelected)
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-100/60 border border-emerald-200/60 px-2 py-0.5 rounded whitespace-nowrap">Kunci & Pilihan Anda</span>
                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @elseif($isCorrect)
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-100/60 border border-emerald-200/60 px-2 py-0.5 rounded whitespace-nowrap">Kunci Jawaban</span>
                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @elseif($isSelected)
                                    <span class="text-[10px] font-semibold text-rose-600 bg-rose-100/60 border border-rose-200/60 px-2 py-0.5 rounded whitespace-nowrap">Pilihan Anda</span>
                                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
