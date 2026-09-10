<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Blind Review</span>
    @endsection

    <x-banksoal::ui.page-header title="Blind Review Soal" subtitle="Tinjau soal dari dosen lain, dan pantau status review soal Anda.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> Kembali ke Bank Soal
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    @if(session('info'))
        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Pending</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Approved</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $approvedCount }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Rejected</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $rejectedCount }}</p>
        </div>
    </div>

    {{-- Inbox: soal dari dosen lain yang harus saya review --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-semibold text-slate-900">
                Inbox Review
                <span class="text-sm font-normal text-slate-500">(soal yang perlu Anda review)</span>
            </h2>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($items as $item)
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Mata Kuliah</p>
                            <p class="font-semibold text-slate-900">{{ $item->round?->mataKuliah?->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-500 mt-1">Status:
                                @if($item->status === 'pending')
                                    <span class="font-semibold text-amber-600">Pending</span>
                                @elseif($item->status === 'approved')
                                    <span class="font-semibold text-emerald-600">Approved</span>
                                @else
                                    <span class="font-semibold text-rose-600">Rejected</span>
                                @endif
                            </p>
                            @if($item->round?->deadline_at)
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Deadline: {{ $item->round->deadline_at->locale('id')->translatedFormat('d M Y H:i') }}
                                    @if($item->round->deadline_at->isPast())
                                        <span class="text-rose-500 font-semibold">(Expired)</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            <p>Round: #{{ $item->round_id }}</p>
                            <p>Soal: #{{ $item->pertanyaan_id }}</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Konten Soal</p>
                        <div class="prose prose-sm max-w-none text-slate-700">{!! $item->pertanyaan?->soal !!}</div>
                    </div>

                    <form action="{{ route('banksoal.soal.dosen.blind-review.submit', $item->id) }}" method="POST"
                          class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        @csrf
                        <div class="md:col-span-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Keputusan</label>
                            <select name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    required {{ $item->status !== 'pending' ? 'disabled' : '' }}>
                                <option value="approved" {{ $item->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan / Masukan</label>
                            <textarea name="catatan" rows="2"
                                      class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                      placeholder="Tambahkan masukan untuk pembuat soal"
                                      {{ $item->status !== 'pending' ? 'disabled' : '' }}>{{ old('catatan', $item->catatan) }}</textarea>
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit"
                                    class="w-full rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90 disabled:cursor-not-allowed disabled:bg-slate-300"
                                    {{ $item->status !== 'pending' ? 'disabled' : '' }}>
                                Simpan Review
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="p-10 text-center text-slate-500">
                    <i class="fas fa-inbox text-3xl text-slate-300 mb-2"></i>
                    <p>Tidak ada tugas blind review untuk saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Status & Feedback untuk soal yang saya buat --}}
    @if($myRounds->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-semibold text-slate-900">
                Status Review Soal Saya
                <span class="text-sm font-normal text-slate-500">(feedback dari reviewer untuk soal yang Anda buat)</span>
            </h2>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($myRounds as $round)
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $round->mataKuliah?->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-500">
                                Round #{{ $round->id }} &bull; {{ $round->created_at->locale('id')->diffForHumans() }}
                            </p>
                            @if($round->deadline_at)
                                <p class="text-xs text-slate-400">
                                    Deadline: {{ $round->deadline_at->locale('id')->translatedFormat('d M Y H:i') }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if($round->status === 'completed')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <i class="fas fa-check-circle"></i> Selesai – Siap Cetak
                                </span>
                                <form method="POST" action="{{ route('banksoal.soal.dosen.cetak-ujian-approved') }}" target="_blank">
                                    @csrf
                                    <input type="hidden" name="round_id" value="{{ $round->id }}">
                                    @foreach($round->items->pluck('pertanyaan_id')->unique() as $soalId)
                                        <input type="hidden" name="soal_ids[]" value="{{ $soalId }}">
                                    @endforeach
                                    <input type="hidden" name="mk_id" value="{{ $round->mk_id }}">
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90">
                                        <i class="fas fa-print"></i> Cetak Soal Ujian
                                    </button>
                                </form>
                            @elseif($round->status === 'in_review')
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    <i class="fas fa-clock"></i> Sedang Direview
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($round->items->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($round->items->groupBy('pertanyaan_id') as $pertanyaanId => $itemGroup)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold text-slate-600 mb-2">Soal #{{ $pertanyaanId }}</p>
                                    @foreach($itemGroup as $reviewItem)
                                        <div class="flex items-start gap-3 mb-2 last:mb-0">
                                            <div class="mt-0.5 shrink-0">
                                                @if($reviewItem->status === 'approved')
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                                        <i class="fas fa-check"></i> Approved
                                                    </span>
                                                @elseif($reviewItem->status === 'rejected')
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">
                                                        <i class="fas fa-times"></i> Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-slate-500">
                                                    @if($round->is_blind)
                                                        <span class="font-medium italic text-slate-400">Reviewer: Anonim (blind)</span>
                                                    @else
                                                        Reviewer: <span class="font-medium">{{ $reviewItem->reviewer?->name ?? 'Anonim' }}</span>
                                                    @endif
                                                    &bull;
                                                    {{ $reviewItem->reviewed_at ? $reviewItem->reviewed_at->locale('id')->diffForHumans() : 'Belum direview' }}
                                                </p>
                                                @if($reviewItem->catatan)
                                                    <p class="mt-1 text-sm text-slate-700 bg-white rounded-lg border border-slate-200 px-3 py-2">
                                                        <i class="fas fa-comment-alt text-slate-400 mr-1"></i>
                                                        {{ $reviewItem->catatan }}
                                                    </p>
                                                @else
                                                    <p class="mt-1 text-xs text-slate-400 italic">Tidak ada catatan.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</x-banksoal::layouts.dosen-admin>
