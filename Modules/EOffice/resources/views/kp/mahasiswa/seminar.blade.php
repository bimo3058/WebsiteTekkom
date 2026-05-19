<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Seminar Kerja Praktik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Seminar KP'])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            {{-- Flash Messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 flex items-center justify-between bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center mr-3 flex-shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-emerald-900 font-bold">Berhasil!</p>
                        <p class="text-xs text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 flex items-center justify-between bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center mr-3 flex-shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-rose-900 font-bold">Gagal!</p>
                        <p class="text-xs text-rose-700">{{ session('error') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Seminar Kerja Praktik</h1>
                <p class="text-sm text-slate-500 mt-1">Selesaikan setiap tahapan berikut secara berurutan untuk menyelesaikan Seminar KP Anda.</p>
            </div>

            {{-- COMPUTE STEP STATE --}}
            @php
                $cvStatus  = $cvDoc          ? strtolower($cvDoc->status_validasi)          : 'belum';
                $ftStatus  = $fotoDoc        ? strtolower($fotoDoc->status_validasi)        : 'belum';
                $khStatus  = $kartuHijauDoc  ? strtolower($kartuHijauDoc->status_validasi)  : 'belum';
                $nlStatus  = $nilaiLapanganDoc ? strtolower($nilaiLapanganDoc->status_validasi) : 'belum';
                $semStatus = $kp->seminar      ? strtolower($kp->seminar->status_validasi_syarat) : 'belum';
                $penilaian = $kp->penilaian;

                // Untuk dokumen, kita anggap "selesai" (bisa lanjut ke step berikutnya) jika statusnya 'disetujui'
                $step1Done = ($cvStatus === 'disetujui');
                $step2Done = ($ftStatus === 'disetujui');
                $step3Done = ($khStatus === 'disetujui');
                $step4Done = ($nlStatus === 'disetujui');
                $step5Done = in_array($semStatus, ['proses', 'valid']);
                $step6Done = $penilaian && ($penilaian->nilai_seminar_pembimbing !== null);

                $currentStep = 1;
                if ($step1Done) $currentStep = 2;
                if ($step1Done && $step2Done) $currentStep = 3;
                if ($step1Done && $step2Done && $step3Done) $currentStep = 4;
                if ($step1Done && $step2Done && $step3Done && $step4Done) $currentStep = 5;
                if ($step1Done && $step2Done && $step3Done && $step4Done && $step5Done) $currentStep = 6;

                // Progress line width (6 steps = 5 gaps)
                $progressWidth = ($currentStep - 1) * 20;
            @endphp

            {{-- STEPPER BAR (4 steps) --}}
            <div class="mb-10">
                <div class="relative">
                    <div class="absolute top-5 left-0 w-full h-0.5 bg-slate-200"></div>
                    <div class="absolute top-5 left-0 h-0.5 bg-indigo-500 transition-all duration-700" style="width: {{ $progressWidth }}%"></div>
                    <div class="relative z-10 flex justify-between">
                        @foreach([
                            ['label' => 'CV',              'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label' => 'Foto (3x4)',      'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['label' => 'Kartu Hijau',     'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ['label' => 'Nilai Lapangan',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                            ['label' => 'Konfirmasi Seminar',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['label' => 'Nilai Seminar',   'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                        ] as $idx => $st)
                            @php $n = $idx+1; $done = $currentStep > $n; $active = $currentStep === $n; @endphp
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm
                                    {{ $done ? 'bg-indigo-600 text-white' : ($active ? 'bg-white border-2 border-indigo-600 text-indigo-600' : 'bg-white border-2 border-slate-200 text-slate-400') }}">
                                    @if($done)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $st['icon'] }}"/></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-[11px] font-bold uppercase tracking-wider {{ $active ? 'text-indigo-600' : ($done ? 'text-slate-700' : 'text-slate-400') }}">{{ $st['label'] }}</p>
                                    <p class="text-[9px] {{ $done ? 'text-indigo-500' : ($active ? 'text-slate-500' : 'text-slate-300') }}">{{ $done ? 'Selesai' : ($active ? 'Sedang Berjalan' : 'Belum') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                {{-- ═══════════════════════════════════════════ --}}
                {{-- CARD 1: CV                                 --}}
                {{-- ═══════════════════════════════════════════ --}}
                @php
                    $cvBadgeMap = [
                        'belum'     => ['txt' => 'Belum Diunggah',    'cls' => 'bg-slate-100 text-slate-500'],
                        'menunggu'  => ['txt' => 'Menunggu Validasi', 'cls' => 'bg-amber-100 text-amber-700'],
                        'disetujui' => ['txt' => 'Disetujui',         'cls' => 'bg-emerald-100 text-emerald-700'],
                        'ditolak'   => ['txt' => 'Perlu Revisi',      'cls' => 'bg-rose-100 text-rose-700'],
                    ];
                    $cvB = $cvBadgeMap[$cvStatus] ?? $cvBadgeMap['belum'];
                @endphp
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                    {{ $currentStep===1 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step1Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep===1 ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $step1Done ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                @if($step1Done)<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 1 @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Curriculum Vitae (CV)</h2>
                                <p class="text-[11px] text-slate-500">Unggah Curriculum Vitae terbaru dalam format PDF.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $cvB['cls'] }}">{{ $cvB['txt'] }}</span>
                    </div>

                    <div class="p-6" x-data="{ modalOpen: false }">
                        {{-- Riwayat Validasi --}}
                        <div class="mb-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Riwayat Validasi</p>
                            <div class="relative pl-5 space-y-3">
                                <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                                {{-- Fase Upload --}}
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow {{ $cvDoc ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $cvDoc ? 'text-slate-800' : 'text-slate-400' }}">
                                            {{ $cvDoc ? 'Dokumen berhasil diunggah' : 'Menunggu dokumen diunggah' }}
                                        </p>
                                        @if($cvDoc)
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($cvDoc->created_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                                {{-- Fase Validasi --}}
                                @if($cvDoc)
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow
                                        {{ $cvStatus==='disetujui' ? 'bg-emerald-500' : ($cvStatus==='ditolak' ? 'bg-rose-500' : 'bg-amber-400') }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $cvStatus==='disetujui' ? 'text-emerald-700' : ($cvStatus==='ditolak' ? 'text-rose-700' : 'text-amber-700') }}">
                                            @if($cvStatus==='disetujui') ✓ Disetujui oleh Koordinator KP
                                            @elseif($cvStatus==='ditolak') ✗ Ditolak — perlu revisi
                                            @else ⏳ Menunggu validasi Koordinator KP
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($cvDoc->updated_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- File preview --}}
                        @if($cvDoc)
                        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-[11px] text-slate-600 flex-1 font-medium">cv_foto.{{ pathinfo($cvDoc->file_path, PATHINFO_EXTENSION) }}</p>
                            <a href="{{ asset('storage/' . $cvDoc->file_path) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 flex-shrink-0 uppercase">Lihat File</a>
                        </div>
                        @endif

                        {{-- Action --}}
                        @if($cvStatus !== 'disetujui')
                        <button @click="modalOpen = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:border-indigo-400 hover:bg-indigo-50 hover:text-indigo-700 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            {{ $cvDoc ? 'Unggah Ulang CV & Foto' : 'Unggah CV & Foto' }}
                        </button>
                        @else
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-bold text-emerald-700">CV & Foto telah diverifikasi ✓</p>
                        </div>
                        @endif

                        {{-- Modal Upload --}}
                        <div x-show="modalOpen" class="fixed inset-0 z-50" style="display:none;">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalOpen = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
                                    <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="jenis_dokumen" value="CV dan Foto">
                                        <div class="p-6">
                                            <div class="flex items-center gap-4 mb-5">
                                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"/></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-900">Unggah CV & Foto</h3>
                                                    <p class="text-xs text-slate-400">Jadikan satu file PDF (CV & Foto 3x4) &mdash; maks. 10MB</p>
                                                </div>
                                            </div>
                                            <div x-data="{ fn: '' }" class="p-6 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:border-indigo-400 hover:bg-white transition-all text-center">
                                                <input type="file" name="file" required id="cv-file" class="hidden" @change="fn = $event.target.files[0].name" accept=".pdf">
                                                <label for="cv-file" class="cursor-pointer block">
                                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    <p class="text-sm font-semibold text-slate-600" x-text="fn || 'Klik untuk memilih file'"></p>
                                                    <p class="text-[10px] text-slate-400 mt-1">Format PDF</p>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95">Simpan Unggahan</button>
                                            <button type="button" @click="modalOpen = false" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-100 transition-all">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════ --}}
                {{-- CARD 2: FOTO                               --}}
                {{-- ═══════════════════════════════════════════ --}}
                @php
                    $ftBadgeMap = [
                        'belum'     => ['txt' => 'Belum Diunggah',    'cls' => 'bg-slate-100 text-slate-500'],
                        'menunggu'  => ['txt' => 'Menunggu Validasi', 'cls' => 'bg-amber-100 text-amber-700'],
                        'disetujui' => ['txt' => 'Disetujui',         'cls' => 'bg-emerald-100 text-emerald-700'],
                        'ditolak'   => ['txt' => 'Perlu Revisi',      'cls' => 'bg-rose-100 text-rose-700'],
                    ];
                    $ftB = $ftBadgeMap[$ftStatus] ?? $ftBadgeMap['belum'];
                @endphp
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                    {{ $currentStep===2 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step2Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep===2 ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $step2Done ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                @if($step2Done)<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 2 @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Foto (3x4)</h2>
                                <p class="text-[11px] text-slate-500">Unggah pas foto formal berukuran 3x4 dalam format PDF.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $ftB['cls'] }}">{{ $ftB['txt'] }}</span>
                    </div>

                    <div class="p-6" x-data="{ modalOpen: false }">
                        {{-- Riwayat Validasi --}}
                        <div class="mb-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Riwayat Validasi</p>
                            <div class="relative pl-5 space-y-3">
                                <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                                {{-- Fase Upload --}}
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow {{ $fotoDoc ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $fotoDoc ? 'text-slate-800' : 'text-slate-400' }}">
                                            {{ $fotoDoc ? 'Dokumen berhasil diunggah' : 'Menunggu dokumen diunggah' }}
                                        </p>
                                        @if($fotoDoc)
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($fotoDoc->created_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                                {{-- Fase Validasi --}}
                                @if($fotoDoc)
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow
                                        {{ $ftStatus==='disetujui' ? 'bg-emerald-500' : ($ftStatus==='ditolak' ? 'bg-rose-500' : 'bg-amber-400') }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $ftStatus==='disetujui' ? 'text-emerald-700' : ($ftStatus==='ditolak' ? 'text-rose-700' : 'text-amber-700') }}">
                                            @if($ftStatus==='disetujui') ✓ Disetujui oleh Koordinator KP
                                            @elseif($ftStatus==='ditolak') ✗ Ditolak — perlu revisi
                                            @else ⏳ Menunggu validasi Koordinator KP
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($fotoDoc->updated_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- File preview --}}
                        @if($fotoDoc)
                        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-[11px] text-slate-600 flex-1 font-medium">foto_3x4.{{ pathinfo($fotoDoc->file_path, PATHINFO_EXTENSION) }}</p>
                            <a href="{{ asset('storage/' . $fotoDoc->file_path) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 flex-shrink-0 uppercase">Lihat File</a>
                        </div>
                        @endif

                        {{-- Action --}}
                        @if($ftStatus !== 'disetujui')
                        <button @click="modalOpen = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:border-indigo-400 hover:bg-indigo-50 hover:text-indigo-700 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            {{ $fotoDoc ? 'Unggah Ulang Foto' : 'Unggah Foto' }}
                        </button>
                        @else
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-bold text-emerald-700">Foto telah diverifikasi ✓</p>
                        </div>
                        @endif

                        {{-- Modal Upload --}}
                        <div x-show="modalOpen" class="fixed inset-0 z-50" style="display:none;">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalOpen = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
                                    <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="jenis_dokumen" value="Foto">
                                        <div class="p-6">
                                            <div class="flex items-center gap-4 mb-5">
                                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-900">Unggah Foto</h3>
                                                    <p class="text-xs text-slate-400">Foto 3x4 formal &mdash; maks. 10MB</p>
                                                </div>
                                            </div>
                                            <div x-data="{ fn: '' }" class="p-6 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:border-indigo-400 hover:bg-white transition-all text-center">
                                                <input type="file" name="file" required id="ft-file" class="hidden" @change="fn = $event.target.files[0].name" accept=".pdf">
                                                <label for="ft-file" class="cursor-pointer block">
                                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    <p class="text-sm font-semibold text-slate-600" x-text="fn || 'Klik untuk memilih file'"></p>
                                                    <p class="text-[10px] text-slate-400 mt-1">Format PDF</p>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95">Simpan Unggahan</button>
                                            <button type="button" @click="modalOpen = false" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-100 transition-all">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════ --}}
                {{-- CARD 3: KARTU HIJAU                        --}}
                {{-- ═══════════════════════════════════════════ --}}
                @php
                    $khBadgeMap = [
                        'belum'     => ['txt' => 'Belum Diunggah',    'cls' => 'bg-slate-100 text-slate-500'],
                        'menunggu'  => ['txt' => 'Menunggu Validasi', 'cls' => 'bg-amber-100 text-amber-700'],
                        'disetujui' => ['txt' => 'Disetujui',         'cls' => 'bg-emerald-100 text-emerald-700'],
                        'ditolak'   => ['txt' => 'Perlu Revisi',      'cls' => 'bg-rose-100 text-rose-700'],
                    ];
                    $khB = $khBadgeMap[$khStatus] ?? $khBadgeMap['belum'];
                @endphp
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                    {{ $currentStep===3 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step3Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep===3 ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $step3Done ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                @if($step3Done)<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 3 @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Kartu Hijau</h2>
                                <p class="text-[11px] text-slate-500">Bukti persetujuan seminar dari program studi.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $khB['cls'] }}">{{ $khB['txt'] }}</span>
                    </div>

                    <div class="p-6" x-data="{ modalOpen: false }">
                        {{-- Riwayat Validasi --}}
                        <div class="mb-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Riwayat Validasi</p>
                            <div class="relative pl-5 space-y-3">
                                <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                                {{-- Fase Upload --}}
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow {{ $kartuHijauDoc ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $kartuHijauDoc ? 'text-slate-800' : 'text-slate-400' }}">
                                            {{ $kartuHijauDoc ? 'Dokumen berhasil diunggah' : 'Menunggu dokumen diunggah' }}
                                        </p>
                                        @if($kartuHijauDoc)
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($kartuHijauDoc->created_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                                {{-- Fase Validasi --}}
                                @if($kartuHijauDoc)
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow
                                        {{ $khStatus==='disetujui' ? 'bg-emerald-500' : ($khStatus==='ditolak' ? 'bg-rose-500' : 'bg-amber-400') }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $khStatus==='disetujui' ? 'text-emerald-700' : ($khStatus==='ditolak' ? 'text-rose-700' : 'text-amber-700') }}">
                                            @if($khStatus==='disetujui') ✓ Disetujui oleh Koordinator KP
                                            @elseif($khStatus==='ditolak') ✗ Ditolak — perlu revisi
                                            @else ⏳ Menunggu validasi Koordinator KP
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($kartuHijauDoc->updated_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- File preview --}}
                        @if($kartuHijauDoc)
                        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-[11px] text-slate-600 flex-1 font-medium">kartu_hijau.{{ pathinfo($kartuHijauDoc->file_path, PATHINFO_EXTENSION) }}</p>
                            <a href="{{ asset('storage/' . $kartuHijauDoc->file_path) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 flex-shrink-0 uppercase">Lihat File</a>
                        </div>
                        @endif

                        {{-- Action --}}
                        @if($khStatus !== 'disetujui')
                        <button @click="modalOpen = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:border-indigo-400 hover:bg-indigo-50 hover:text-indigo-700 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            {{ $kartuHijauDoc ? 'Unggah Ulang Kartu Hijau' : 'Unggah Kartu Hijau' }}
                        </button>
                        @else
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-bold text-emerald-700">Kartu Hijau telah diverifikasi ✓</p>
                        </div>
                        @endif

                        {{-- Modal Upload --}}
                        <div x-show="modalOpen" class="fixed inset-0 z-50" style="display:none;">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalOpen = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
                                    <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="jenis_dokumen" value="Kartu Hijau">
                                        <div class="p-6">
                                            <div class="flex items-center gap-4 mb-5">
                                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-900">Unggah Kartu Hijau</h3>
                                                    <p class="text-xs text-slate-400">PDF / JPG / PNG &mdash; maks. 10MB</p>
                                                </div>
                                            </div>
                                            <div x-data="{ fn: '' }" class="p-6 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:border-indigo-400 hover:bg-white transition-all text-center">
                                                <input type="file" name="file" required id="kh-file" class="hidden" @change="fn = $event.target.files[0].name">
                                                <label for="kh-file" class="cursor-pointer block">
                                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    <p class="text-sm font-semibold text-slate-600" x-text="fn || 'Klik untuk memilih file'"></p>
                                                    <p class="text-[10px] text-slate-400 mt-1">Format PDF/JPG/PNG</p>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95">Simpan Unggahan</button>
                                            <button type="button" @click="modalOpen = false" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-100 transition-all">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════ --}}
                {{-- CARD 4: NILAI LAPANGAN                     --}}
                {{-- ═══════════════════════════════════════════ --}}
                @php
                    $nlBadgeMap = [
                        'belum'     => ['txt' => 'Belum Diunggah',    'cls' => 'bg-slate-100 text-slate-500'],
                        'menunggu'  => ['txt' => 'Menunggu Validasi', 'cls' => 'bg-amber-100 text-amber-700'],
                        'disetujui' => ['txt' => 'Disetujui',         'cls' => 'bg-emerald-100 text-emerald-700'],
                        'ditolak'   => ['txt' => 'Perlu Revisi',      'cls' => 'bg-rose-100 text-rose-700'],
                    ];
                    $nlB = $nlBadgeMap[$nlStatus] ?? $nlBadgeMap['belum'];
                    $nilaiLap = $penilaian?->nilai_lapangan;
                @endphp
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                    {{ $currentStep===4 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step4Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep===4 ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $step4Done ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                @if($step4Done)<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 4 @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Presensi & Nilai Lapangan (A2)</h2>
                                <p class="text-[11px] text-slate-500">Unggah form A2 yang sudah ditandatangani pembimbing lapangan.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $nlB['cls'] }}">{{ $nlB['txt'] }}</span>
                    </div>

                    <div class="p-6" x-data="{ modalOpen: false }">

                        {{-- Riwayat Validasi --}}
                        <div class="mb-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Riwayat Validasi</p>
                            <div class="relative pl-5 space-y-3">
                                <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow {{ $nilaiLapanganDoc ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $nilaiLapanganDoc ? 'text-slate-800' : 'text-slate-400' }}">
                                            {{ $nilaiLapanganDoc ? 'Form A2 berhasil diunggah' : 'Menunggu form A2 diunggah' }}
                                        </p>
                                        @if($nilaiLapanganDoc)
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($nilaiLapanganDoc->created_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($nilaiLapanganDoc)
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 rounded-full mt-0.5 -ml-[18px] flex-shrink-0 border-2 border-white shadow
                                        {{ $nlStatus==='disetujui' ? 'bg-emerald-500' : ($nlStatus==='ditolak' ? 'bg-rose-500' : 'bg-amber-400') }}"></div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $nlStatus==='disetujui' ? 'text-emerald-700' : ($nlStatus==='ditolak' ? 'text-rose-700' : 'text-amber-700') }}">
                                            @if($nlStatus==='disetujui') ✓ Disetujui — nilai lapangan telah diinput
                                            @elseif($nlStatus==='ditolak') ✗ Ditolak — perlu revisi
                                            @else ⏳ Menunggu validasi & input nilai dari Koordinator
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($nilaiLapanganDoc->updated_at)->translatedFormat('d M Y \p\u\k\u\l H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Nilai Lapangan dari Dosen (tampil jika sudah diinput) --}}
                        @if($nilaiLap !== null)
                        <div class="mb-5 p-4 rounded-2xl border border-emerald-100 bg-emerald-50">
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-2">Nilai Lapangan dari Pembimbing</p>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-white border border-emerald-200 shadow-sm flex items-center justify-center">
                                    <span class="text-2xl font-black text-emerald-600">{{ $nilaiLap }}</span>
                                </div>
                                <div>
                                    @php
                                        $grade = $nilaiLap >= 85 ? 'A' : ($nilaiLap >= 75 ? 'B' : ($nilaiLap >= 60 ? 'C' : 'D'));
                                        $gradeColor = $nilaiLap >= 85 ? 'text-emerald-700' : ($nilaiLap >= 75 ? 'text-blue-700' : ($nilaiLap >= 60 ? 'text-amber-700' : 'text-rose-700'));
                                    @endphp
                                    <p class="text-sm font-bold text-slate-800">Grade: <span class="{{ $gradeColor }} text-lg">{{ $grade }}</span></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Nilai ini akan diperhitungkan dalam nilai akhir KP.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- File preview --}}
                        @if($nilaiLapanganDoc)
                        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-[11px] text-slate-600 flex-1 font-medium">form_a2.{{ pathinfo($nilaiLapanganDoc->file_path, PATHINFO_EXTENSION) }}</p>
                            <a href="{{ asset('storage/' . $nilaiLapanganDoc->file_path) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 flex-shrink-0 uppercase">Lihat File</a>
                        </div>
                        @endif

                        {{-- Action --}}
                        @if($nlStatus !== 'disetujui')
                        <button @click="modalOpen = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:border-indigo-400 hover:bg-indigo-50 hover:text-indigo-700 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            {{ $nilaiLapanganDoc ? 'Unggah Ulang Form A2' : 'Unggah Form A2' }}
                        </button>
                        @else
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-bold text-emerald-700">Form A2 telah diverifikasi ✓</p>
                        </div>
                        @endif

                        {{-- Modal Upload A2 --}}
                        <div x-show="modalOpen" class="fixed inset-0 z-50" style="display:none;">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalOpen = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
                                    <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="jenis_dokumen" value="Nilai Lapangan">
                                        <div class="p-6">
                                            <div class="flex items-center gap-4 mb-5">
                                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-900">Unggah Form A2</h3>
                                                    <p class="text-xs text-slate-400">Presensi & Nilai Lapangan &mdash; PDF/JPG/PNG, maks. 10MB</p>
                                                </div>
                                            </div>
                                            <div x-data="{ fn: '' }" class="p-6 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:border-amber-400 hover:bg-white transition-all text-center">
                                                <input type="file" name="file" required id="nl-file" class="hidden" @change="fn = $event.target.files[0].name">
                                                <label for="nl-file" class="cursor-pointer block">
                                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    <p class="text-sm font-semibold text-slate-600" x-text="fn || 'Klik untuk memilih file'"></p>
                                                    <p class="text-[10px] text-slate-400 mt-1">Format PDF/JPG/PNG</p>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95">Simpan Unggahan</button>
                                            <button type="button" @click="modalOpen = false" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-100 transition-all">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════ --}}
                {{-- CARD 5: AJUKAN SEMINAR                     --}}
                {{-- ═══════════════════════════════════════════ --}}
                @php
                    $semBadgeMap = [
                        'belum'   => ['txt' => 'Belum Diajukan',   'cls' => 'bg-slate-100 text-slate-500'],
                        'proses'  => ['txt' => 'Menunggu Validasi','cls' => 'bg-amber-100 text-amber-700'],
                        'valid'   => ['txt' => 'Disetujui',        'cls' => 'bg-emerald-100 text-emerald-700'],
                        'ditolak' => ['txt' => 'Ditolak',          'cls' => 'bg-rose-100 text-rose-700'],
                    ];
                    $semB = $semBadgeMap[$semStatus] ?? $semBadgeMap['belum'];
                    $canAjukan = $step1Done && $step2Done && $step3Done && $step4Done && $syaratSeminar['semua_terpenuhi']
                                 && (!$kp->seminar || $semStatus === 'ditolak');
                @endphp
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                    {{ $currentStep===5 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step5Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep===5 ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $step5Done ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                @if($step5Done)<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 5 @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Konfirmasi Seminar</h2>
                                <p class="text-[11px] text-slate-500">Isi data pelaksanaan seminar yang telah disepakati.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $semB['cls'] }}">{{ $semB['txt'] }}</span>
                    </div>

                    <div class="p-6">
                        {{-- Seminar sudah diajukan: tampilkan info --}}
                        @if($kp->seminar && $semStatus !== 'ditolak')
                        <div class="grid grid-cols-3 gap-3 mb-5">
                            @foreach([
                                ['label' => 'Tanggal', 'val' => $kp->seminar->tanggal_seminar->translatedFormat('d M Y')],
                                ['label' => 'Waktu',   'val' => $kp->seminar->waktu_seminar . ' WIB'],
                                ['label' => 'Ruangan', 'val' => $kp->seminar->ruangan],
                            ] as $inf)
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ $inf['label'] }}</p>
                                <p class="text-sm font-bold text-slate-900">{{ $inf['val'] }}</p>
                            </div>
                            @endforeach
                        </div>

                        @if($semStatus === 'valid')
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-blue-900">🎉 Seminar Anda Telah Disetujui!</p>
                                <p class="text-xs text-blue-700 mt-1">Unduh surat undangan dan form kehadiran peserta seminar.</p>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'b1') }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-blue-200 text-blue-700 text-xs font-bold rounded-xl hover:bg-blue-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Undangan
                                </a>
                                <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'b2') }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Form B2
                                </a>
                            </div>
                        </div>
                        @elseif($semStatus === 'proses')
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-amber-800">Pengajuan sedang diproses...</p>
                                <p class="text-xs text-amber-600 mt-0.5">Koordinator KP akan segera memvalidasi jadwal seminar Anda.</p>
                            </div>
                        </div>
                        @endif

                        @else
                        {{-- Form Konfirmasi Seminar (Selalu Tampil Jika Belum Valid/Proses) --}}
                        @if(!$step1Done || !$step2Done || !$step3Done || !$step4Done)
                        <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-5">
                            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Perhatian: Syarat Belum Lengkap</p>
                                <p class="text-[11px] text-amber-700 mt-0.5">
                                    Anda tetap dapat mensubmit form ini, namun validasi jadwal mungkin tertunda karena:
                                    @if(!$step1Done) <br>• CV belum diverifikasi.@endif
                                    @if(!$step2Done) <br>• Foto (3x4) belum diverifikasi.@endif
                                    @if(!$step3Done) <br>• Kartu Hijau belum diverifikasi.@endif
                                    @if(!$step4Done) <br>• Form A2 belum diverifikasi.@endif
                                </p>
                            </div>
                        </div>
                        @endif

                        @if(!$kp->seminar || $semStatus === 'ditolak')
                        <form action="{{ route('eoffice.kp.mahasiswa.seminar.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Mahasiswa</label>
                                    <input type="text" value="{{ $mahasiswa->nama_lengkap }}" readonly
                                        class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-100 text-slate-500 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIM</label>
                                    <input type="text" value="{{ $mahasiswa->nim }}" readonly
                                        class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-100 text-slate-500 cursor-not-allowed">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Seminar <span class="text-rose-500">*</span></label>
                                    <input type="date" name="tanggal_seminar" required min="{{ date('Y-m-d') }}"
                                        class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                                    <input type="time" name="waktu_mulai" required
                                        class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                                    <input type="time" name="waktu_selesai" required
                                        class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tempat / Ruangan <span class="text-rose-500">*</span></label>
                                    <input type="text" name="ruangan" required placeholder="Contoh: Ruang Rapat Lt.2 Gedung B"
                                        class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 mt-4">
                                <p class="text-[10px] text-slate-400">* Pastikan jadwal telah dikonfirmasi dengan dosen pembimbing.</p>
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Submit
                                </button>
                            </div>
                        </form>
                        @endif
                        @endif
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════ --}}
                {{-- CARD 6: NILAI SEMINAR                      --}}
                {{-- ═══════════════════════════════════════════ --}}
                @php
                    $nilaiSeminar = $penilaian?->nilai_seminar_pembimbing;
                    $nilaiAkhir   = $penilaian?->nilai_akhir;
                @endphp
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                    {{ $currentStep===6 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step6Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep===6 ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $step6Done ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                @if($step6Done)<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 6 @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Nilai Seminar</h2>
                                <p class="text-[11px] text-slate-500">Nilai akhir seminar KP yang diberikan oleh dosen pembimbing.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $step6Done ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $step6Done ? 'Nilai Tersedia' : 'Menunggu Penilaian' }}
                        </span>
                    </div>

                    <div class="p-6">
                        @if($step6Done)
                        {{-- Nilai tersedia --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            {{-- Nilai Seminar --}}
                            <div class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-5">
                                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-3">Nilai Seminar</p>
                                <div class="flex items-end gap-3">
                                    <span class="text-5xl font-black text-indigo-700 leading-none">{{ $nilaiSeminar }}</span>
                                    @php
                                        $gS = $nilaiSeminar >= 85 ? 'A' : ($nilaiSeminar >= 75 ? 'B' : ($nilaiSeminar >= 60 ? 'C' : 'D'));
                                    @endphp
                                    <span class="text-2xl font-bold text-indigo-400 mb-1">({{ $gS }})</span>
                                </div>
                                <p class="text-[10px] text-indigo-500 mt-2">Dari dosen pembimbing</p>
                                <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-100 rounded-full -translate-y-1/3 translate-x-1/3 opacity-50"></div>
                            </div>

                            {{-- Nilai Akhir --}}
                            @if($nilaiAkhir !== null)
                            <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5">
                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-3">Nilai Akhir KP</p>
                                <div class="flex items-end gap-3">
                                    <span class="text-5xl font-black text-emerald-700 leading-none">{{ $nilaiAkhir }}</span>
                                    @php
                                        $gA = $nilaiAkhir >= 85 ? 'A' : ($nilaiAkhir >= 75 ? 'B' : ($nilaiAkhir >= 60 ? 'C' : 'D'));
                                    @endphp
                                    <span class="text-2xl font-bold text-emerald-400 mb-1">({{ $gA }})</span>
                                </div>
                                <p class="text-[10px] text-emerald-500 mt-2">Rekap nilai lapangan + seminar</p>
                                <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-100 rounded-full -translate-y-1/3 translate-x-1/3 opacity-50"></div>
                            </div>
                            @else
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs font-semibold text-slate-500">Nilai akhir belum direkap</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Rekap komponen --}}
                        <div class="rounded-2xl border border-slate-100 overflow-hidden">
                            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Rekap Komponen Nilai</p>
                            </div>
                            <div class="divide-y divide-slate-50">
                                @foreach([
                                    ['label' => 'Nilai Lapangan (dari Pembimbing Lapangan)', 'val' => $nilaiLap],
                                    ['label' => 'Nilai Seminar (dari Dosen Pembimbing)',      'val' => $nilaiSeminar],
                                    ['label' => 'Nilai Akhir KP',                             'val' => $nilaiAkhir, 'bold' => true],
                                ] as $row)
                                <div class="flex items-center justify-between px-5 py-3 {{ isset($row['bold']) ? 'bg-slate-50/50' : '' }}">
                                    <p class="text-xs {{ isset($row['bold']) ? 'font-bold text-slate-800' : 'text-slate-600' }}">{{ $row['label'] }}</p>
                                    <span class="text-sm font-bold {{ isset($row['bold']) ? 'text-indigo-700' : 'text-slate-800' }}">
                                        {{ $row['val'] !== null ? $row['val'] : '—' }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        @else
                        {{-- Belum ada nilai --}}
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-600">Nilai belum tersedia</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                @if(!$step5Done)
                                    Selesaikan pengajuan seminar terlebih dahulu.
                                @else
                                    Nilai akan muncul setelah dosen pembimbing menginput penilaian pasca seminar.
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>{{-- end space-y-6 --}}

        </main>
    </div>
</div>
</body>
</html>
