<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Seminar KP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            font-family: 'Inter Tight', sans-serif;
        }

        :root {
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-500: #4f46e5;
            --grey-0: #fff;
            --grey-50: #f9fafb;
            --grey-100: #f3f4f6;
            --grey-200: #e5e7eb;
            --grey-400: #9ca3af;
            --grey-500: #6b7280;
            --grey-600: #4b5563;
            --grey-700: #374151;
            --grey-800: #1f2937;
            --grey-900: #030712;
            --success-0: #f0fdf4;
            --success-50: #dcfce7;
            --success-100: #bbf7d0;
            --success-300: #16a34a;
            --warning-0: #fffbeb;
            --warning-50: #fef3c7;
            --warning-100: #fde68a;
            --warning-300: #d97706;
            --error-0: #fff1f2;
            --error-50: #ffe4e6;
            --error-200: #f87171;
            --error-300: #dc2626;
            --sky-500: #0ea5e9;
        }

        .sikape-card {
            background: #fff;
            border: 1px solid #DFE1E7;
            border-radius: 12px;
        }
    </style>
</head>

<body style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen w-full overflow-hidden">

        @include('eoffice::kp.mahasiswa.partials.sidebar')

        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">

            {{-- Outer content container with border --}}
            <div class="flex-1 flex flex-col min-h-0 overflow-hidden rounded-lg"
                style="margin:8px; border:1px solid #DFE1E7; background:#fff;">

                @include('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Seminar KP'])

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition
                            class="mb-6 flex items-center justify-between bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-emerald-900 font-bold">Berhasil!</p>
                                    <p class="text-xs text-emerald-700">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-transition
                            class="mb-6 flex items-center justify-between bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-rose-900 font-bold">Gagal!</p>
                                    <p class="text-xs text-rose-700">{{ session('error') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-rose-400 hover:text-rose-600"><svg class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg></button>
                        </div>
                    @endif

                    {{-- Page Header --}}
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Seminar Kerja Praktik</h1>
                        <p class="text-sm text-slate-500 mt-1">Selesaikan setiap tahapan berikut secara berurutan untuk
                            menyelesaikan Seminar KP Anda.</p>
                    </div>

                    @if(!$isOpen)
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center mt-10 mb-8">
                            <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 mb-2">Fase Pasca KP Ditutup</h2>
                            <p class="text-slate-600 mb-4">
                                Saat ini periode Pasca KP (Seminar & Nilai) sedang ditutup atau belum dimulai.
                            </p>
                            <p class="text-sm text-slate-500 mb-6">Silakan cek kembali di lain waktu atau hubungi
                                Koordinator KP untuk informasi lebih lanjut.</p>
                        </div>
                    @else
                                @if($showReminder)
                                    <div
                                        class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                                        <div class="p-2 bg-amber-100 rounded-lg text-amber-700 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <h4 class="text-sm font-bold text-amber-900">⏰ Pengingat Batas Akhir</h4>
                                                @if($endDate)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] rounded-full font-bold border border-rose-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        Deadline: {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-amber-800 leading-relaxed">
                                                Pastikan Anda menyelesaikan proses seminar sebelum batas waktu yang telah ditentukan.
                                            </p>
                                        </div>
                                    </div>

                                @endif


                                {{-- COMPUTE STEP STATE --}}
                                @php
                                    $nlStatus = $nilaiLapanganDoc ? strtolower($nilaiLapanganDoc->status_validasi) : 'belum';
                                    $semStatus = $kp->seminar ? strtolower($kp->seminar->status_validasi_dosen) : 'belum';
                                    $penilaian = $kp->penilaian;

                                    // Step 1 dianggap selesai jika semua template dokumen syarat telah disetujui (dinamis)
                                    $step1Done = $syaratSeminar['semua_terpenuhi'];
                                    
                                    // Step 2 dianggap selesai HANYA JIKA Dosen sudah setuju
                                    $step2Done = $semStatus === 'approved';
                                    
                                    $step3Done = $penilaian && ($penilaian->nilai_akhir !== null);

                                    $currentStep = 1;
                                    if ($step1Done)
                                        $currentStep = 2;
                                    if ($step1Done && $step2Done)
                                        $currentStep = 3;
                                    if ($step1Done && $step2Done && $step3Done)
                                        $currentStep = 4;

                                    // Progress line width (3 steps = 2 gaps)
                                    $progressWidth = min(($currentStep - 1) * 50, 100);
                                @endphp

                                {{-- STEPPER BAR (3 steps) --}}
                                @php
                                    $steps = [
                                        ['label' => 'Persyaratan Dokumen', 'desc' => $currentStep > 1 ? 'Selesai' : ($currentStep === 1 ? 'Sedang Berjalan' : 'Belum'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                                        ['label' => 'Konfirmasi Seminar', 'desc' => $currentStep > 2 ? 'Selesai' : ($currentStep === 2 ? 'Sedang Berjalan' : 'Belum'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                        ['label' => 'Nilai Seminar', 'desc' => $currentStep > 3 ? 'Selesai' : ($currentStep === 3 ? 'Sedang Berjalan' : 'Belum'), 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                                    ];
                                    $cur = $currentStep - 1;
                                @endphp
                                <div class="sikape-card p-6 mb-8">
                                    <p class="text-xs font-semibold uppercase tracking-widest mb-5" style="color:var(--grey-400);">
                                        Progres Seminar KP</p>
                                    <div class="flex items-start gap-0">
                                        @foreach($steps as $i => $step)
                                            <div class="flex-1 flex flex-col items-center relative">
                                                @if($i > 0)
                                                    <div class="absolute h-0.5 z-0"
                                                        style="top:20px; left:0; right:50%; background:{{ $i <= $cur ? '#4f46e5' : 'var(--grey-200)' }};">
                                                    </div>
                                                @endif
                                                @if($i < count($steps) - 1)
                                                    <div class="absolute h-0.5 z-0"
                                                        style="top:20px; left:50%; right:0; background:{{ $i < $cur ? '#4f46e5' : 'var(--grey-200)' }};">
                                                    </div>
                                                @endif

                                                <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all duration-300 flex-shrink-0"
                                                    style="background:{{ $i <= $cur ? '#4f46e5' : 'var(--grey-100)' }};
                                                            {{ $i === $cur ? 'box-shadow:0 0 0 4px #e0e7ff;' : '' }}">
                                                    @if($i < $cur)
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" style="color:{{ $i <= $cur ? 'white' : 'var(--grey-400)' }};"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                                d="{{ $step['icon'] }}" />
                                                        </svg>
                                                    @endif
                                                </div>

                                                <p class="text-xs font-semibold mt-2 text-center"
                                                    style="color:{{ $i <= $cur ? 'var(--grey-800)' : 'var(--grey-400)' }};">
                                                    {{ $step['label'] }}</p>
                                                <p class="text-[10px] text-center"
                                                    style="color:{{ $i <= $cur ? 'var(--grey-500)' : 'var(--grey-300)' }};">
                                                    {{ $step['desc'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-8">
                                    <div class="space-y-6">

                                        {{-- ═══════════════════════════════════════════ --}}
                                        {{-- ═══════════════════════════════════════════ --}}
                                        {{-- CARD 1: DOKUMEN PERSYARATAN (DINAMIS) --}}
                                        {{-- ═══════════════════════════════════════════ --}}
                                        <div class="mt-8 pt-6">
                                            <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div>
                                                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-1">
                                                        Dokumen Pelaksanaan</h2>
                                                    <p class="text-[11px] text-slate-500 italic">Daftar dokumen sesuai ketentuan
                                                        Koordinator KP</p>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                @if($templatesDokumen && $templatesDokumen->isNotEmpty())
                                                    @foreach($templatesDokumen as $tmpl)
                                                        @php
                                                            $jenis = $tmpl->title;
                                                            $docGroup = $dokumenByJenis->get($jenis);
                                                            $latestDoc = $docGroup ? $docGroup->sortByDesc('created_at')->first() : null;
                                                            $status = $latestDoc ? strtolower($latestDoc->approval_status ?? $latestDoc->status_validasi) : 'belum';

                                                            $statusMap = [
                                                                'belum' => ['label' => 'Belum Ada', 'class' => 'bg-slate-100 text-slate-500'],
                                                                'menunggu' => ['label' => 'Menunggu Validasi', 'class' => 'bg-amber-100 text-amber-700'],
                                                                'pending' => ['label' => 'Menunggu Validasi', 'class' => 'bg-amber-100 text-amber-700'],
                                                                'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-700'],
                                                                'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-700'],
                                                                'ditolak' => ['label' => 'Revisi', 'class' => 'bg-rose-100 text-rose-700'],
                                                                'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
                                                                'revision' => ['label' => 'Revisi', 'class' => 'bg-rose-100 text-rose-700'],
                                                            ];
                                                            $st = $statusMap[$status] ?? $statusMap['belum'];
                                                        @endphp

                                                        <div
                                                            class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:shadow-sm transition-all gap-4">

                                                            {{-- Info Dokumen --}}
                                                            <div class="flex items-center gap-3">
                                                                <div
                                                                    class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-50 text-slate-500 shadow-sm border border-slate-100">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="1.8"
                                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                </div>
                                                                <div>
                                                                    <div class="flex items-center gap-2">
                                                                        <p class="text-sm font-bold text-slate-800">{{ $tmpl->title }}</p>
                                                                        @if($tmpl->is_uploadable && $latestDoc)
                                                                            <span
                                                                                class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $st['class'] }}">
                                                                                {{ $st['label'] }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                                                        {{ $tmpl->description ?? 'Dokumen Pasca KP' }}</p>

                                                                    @if($tmpl->is_uploadable && $latestDoc)
                                                                        <div class="mt-1 flex items-center gap-2">
                                                                            <p class="text-[10px] text-slate-400">
                                                                                v.{{ \Carbon\Carbon::parse($latestDoc->created_at)->format('d.m.Y') }}
                                                                            </p>
                                                                            <a href="{{ $latestDoc->file_url }}" target="_blank"
                                                                                class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700">LIHAT
                                                                                FILE</a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            {{-- Action Buttons --}}
                                                            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                                                @if($tmpl->is_downloadable && !empty($tmpl->file_path))
                                                                    <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', $tmpl->id) }}"
                                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4-4m4 4V4" />
                                                                        </svg>
                                                                        Template
                                                                    </a>
                                                                @endif

                                                                @if($tmpl->is_uploadable)
                                                                    <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}"
                                                                        method="POST" enctype="multipart/form-data"
                                                                        class="flex flex-wrap items-center gap-2 w-full sm:w-auto"
                                                                        x-data="{ fileName: '' }">
                                                                        @csrf
                                                                        <input type="hidden" name="jenis_dokumen" value="{{ $tmpl->title }}">
                                                                        <div class="relative w-full sm:w-auto">
                                                                            <input type="file" name="file" id="dokumen_{{ $tmpl->id }}"
                                                                                class="sr-only peer" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                                                                                required
                                                                                x-on:change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                                                            <label for="dokumen_{{ $tmpl->id }}"
                                                                                class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-slate-300">
                                                                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none"
                                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4-4m4 4V4" />
                                                                                </svg>
                                                                                <span
                                                                                    x-text="fileName ? fileName : '{{ $latestDoc ? 'Unggah Ulang' : 'Upload File' }}'"
                                                                                    class="truncate max-w-[120px]"></span>
                                                                            </label>
                                                                        </div>
                                                                        <button type="submit" x-show="fileName" style="display: none;"
                                                                            class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                                                                            Simpan
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div
                                                        class="p-8 text-center bg-slate-50 border border-slate-100 rounded-xl border-dashed">
                                                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <p class="text-xs font-semibold text-slate-500">Belum ada dokumen yang
                                                            disyaratkan.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ═══════════════════════════════════════════ --}}
                                        {{-- CARD 2: AJUKAN SEMINAR --}}
                                        {{-- ═══════════════════════════════════════════ --}}
                                        @php
                                            $semBadgeMap = [
                                                'belum' => ['txt' => 'Belum Diajukan', 'cls' => 'bg-slate-100 text-slate-500'],
                                                'pending' => ['txt' => 'Menunggu Validasi Dosen', 'cls' => 'bg-amber-100 text-amber-700'],
                                                'approved' => ['txt' => 'Disetujui', 'cls' => 'bg-emerald-100 text-emerald-700'],
                                                'rejected' => ['txt' => 'Ditolak', 'cls' => 'bg-rose-100 text-rose-700'],
                                            ];
                                            $semB = $semBadgeMap[$semStatus] ?? $semBadgeMap['belum'];
                                            $canAjukan = $step1Done && $syaratSeminar['semua_terpenuhi']
                                                && (!$kp->seminar || $semStatus === 'rejected');
                                        @endphp
                                        <div
                                            class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                                    {{ $currentStep === 2 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step2Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                                            <div
                                                class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep === 2 ? 'bg-indigo-50/40' : '' }}">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                                {{ $step2Done ? 'bg-indigo-600 text-white' : ($currentStep === 2 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400') }}">
                                                        @if($step2Done)<svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                    d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        @else<svg class="w-4 h-4 text-current" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h2 class="text-base font-bold text-slate-900">Konfirmasi Seminar</h2>
                                                        <p class="text-[11px] text-slate-500">Isi data pelaksanaan seminar yang
                                                            telah disepakati.</p>
                                                    </div>
                                                </div>
                                                <span
                                                    class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $semB['cls'] }}">{{ $semB['txt'] }}</span>
                                            </div>

                                            <div class="p-6">
                                                {{-- Seminar sudah diajukan: tampilkan info --}}
                                                @if($kp->seminar && $semStatus !== 'rejected')
                                                    <div class="grid grid-cols-3 gap-3 mb-5">
                                                        @foreach([
                                                                ['label' => 'Tanggal', 'val' => $kp->seminar->tanggal_seminar->translatedFormat('d M Y')],
                                                                ['label' => 'Waktu', 'val' => $kp->seminar->waktu_seminar . ' WIB'],
                                                                ['label' => 'Ruangan', 'val' => $kp->seminar->ruangan],
                                                            ] as $inf)
                                                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                                                <p
                                                                    class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                                                    {{ $inf['label'] }}</p>
                                                                <p class="text-sm font-bold text-slate-900">{{ $inf['val'] }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if($semStatus === 'approved')
                                                        <div
                                                            class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start justify-between gap-4">
                                                            <div>
                                                                <p class="text-sm font-bold text-blue-900">🎉 Jadwal Seminar Telah
                                                                    Disetujui!</p>
                                                                <p class="text-xs text-blue-700 mt-1">Jadwal seminar Anda telah disetujui
                                                                    oleh Dosen Pembimbing.</p>
                                                            </div>
                                                        </div>
                                                    @elseif($semStatus === 'pending')
                                                        <div
                                                            class="p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-center gap-3">
                                                            <div
                                                                class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-bold text-amber-800">Pengajuan sedang diproses...</p>
                                                                <p class="text-xs text-amber-600 mt-0.5">Dosen Pembimbing akan segera
                                                                    memvalidasi jadwal seminar Anda.</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                @else
                                                    {{-- Peringatan Syarat Belum Lengkap --}}
                                                    @if(!$step1Done)
                                                        <div
                                                            class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-5">
                                                            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                            <div>
                                                                <p class="text-sm font-semibold text-amber-800">Perhatian: Syarat Belum
                                                                    Lengkap</p>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- Peringatan Ditolak Dosen --}}
                                                        @if($semStatus === 'rejected')
                                                            <div class="flex items-start gap-4 p-4 mb-6 bg-red-50 border border-red-200 rounded-2xl w-full relative overflow-hidden">
                                                                <div class="absolute right-0 top-0 opacity-5">
                                                                    <svg class="w-32 h-32 text-red-900" fill="currentColor" viewBox="0 0 24 24">
                                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                                                                    </svg>
                                                                </div>
                                                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600 border border-red-200">
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1 relative z-10">
                                                                    <h3 class="text-sm font-bold text-red-900">Jadwal Seminar Ditolak Dosen</h3>
                                                                    <p class="text-xs text-red-700 mt-1 mb-2 leading-relaxed">
                                                                        Dosen Pembimbing Anda telah menolak usulan jadwal seminar sebelumnya. Silakan perhatikan catatan berikut dan ajukan ulang jadwal yang baru.
                                                                    </p>
                                                                    <div class="bg-white/60 p-3 rounded-lg border border-red-100/50">
                                                                        <p class="text-[10px] font-bold text-red-800 uppercase tracking-widest mb-1">Catatan Peringatan:</p>
                                                                        <p class="text-[13px] text-red-950 font-medium italic">
                                                                            "{{ $kp->seminar->catatan_dosen ?? 'Tidak ada catatan tambahan.' }}"
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Form Konfirmasi Seminar --}}

                                                        @if(!$kp->seminar || $semStatus === 'rejected')
                                                            <form action="{{ route('eoffice.kp.mahasiswa.seminar.store') }}" method="POST"
                                                                class="space-y-4">
                                                                @csrf
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label
                                                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama
                                                                            Mahasiswa</label>
                                                                        <input type="text" value="{{ $mahasiswa->nama_lengkap }}" readonly
                                                                            class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-100 text-slate-500 cursor-not-allowed">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIM</label>
                                                                        <input type="text" value="{{ $mahasiswa->nim }}" readonly
                                                                            class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-100 text-slate-500 cursor-not-allowed">
                                                                    </div>
                                                                    <div class="md:col-span-2">
                                                                        <label
                                                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal
                                                                            Seminar <span class="text-rose-500">*</span></label>
                                                                        <input type="date" name="tanggal_seminar" required
                                                                            min="{{ date('Y-m-d') }}"
                                                                            class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam
                                                                            Mulai <span class="text-rose-500">*</span></label>
                                                                        <input type="time" name="waktu_mulai" required
                                                                            class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam
                                                                            Selesai <span class="text-rose-500">*</span></label>
                                                                        <input type="time" name="waktu_selesai" required
                                                                            class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                                                    </div>
                                                                    <div class="md:col-span-2">
                                                                        <label
                                                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tempat
                                                                            / Ruangan <span class="text-rose-500">*</span></label>
                                                                        <input type="text" name="ruangan" required
                                                                            placeholder="Contoh: Ruang Rapat Lt.2 Gedung B"
                                                                            class="w-full rounded-xl border-slate-200 text-sm py-2.5 px-4 border bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all">
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="flex items-center justify-between pt-2 border-t border-slate-100 mt-4">
                                                                    <p class="text-[10px] text-slate-400">* Pastikan jadwal telah dikonfirmasi
                                                                        dengan dosen pembimbing.</p>
                                                                    <button type="submit"
                                                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                                        </svg>
                                                                        Submit
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ═══════════════════════════════════════════ --}}
                                        {{-- CARD 4: NILAI SEMINAR --}}
                                        {{-- ═══════════════════════════════════════════ --}}
                                        @php
                                            $nilaiAkhir = $penilaian?->nilai_akhir;
                                        @endphp
                                        <div
                                            class="bg-white rounded-2xl border overflow-hidden transition-all duration-300
                                    {{ $currentStep === 3 ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg shadow-indigo-100/40' : ($step3Done ? 'border-emerald-200 shadow-sm' : 'border-slate-200 shadow-sm') }}">

                                            <div
                                                class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep === 3 ? 'bg-indigo-50/40' : '' }}">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                                {{ $step3Done ? 'bg-indigo-600 text-white' : ($currentStep === 3 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400') }}">
                                                        @if($step3Done)<svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                    d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        @else<svg class="w-4 h-4 text-current" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h2 class="text-base font-bold text-slate-900">Nilai Akhir</h2>
                                                        <p class="text-[11px] text-slate-500">Nilai akhir dari pelaksanaan Kerja Praktik (KP).</p>
                                                    </div>
                                                </div>
                                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            {{ $step3Done ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                    {{ $step3Done ? 'Nilai Tersedia' : 'Menunggu Penilaian' }}
                                                </span>
                                            </div>

                                            <div class="p-6">
                                                @if($step3Done)
                                                    {{-- Nilai tersedia --}}
                                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-5">
                                                        {{-- Nilai Akhir --}}
                                                        @if($nilaiAkhir !== null)
                                                            <div
                                                                class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5">
                                                                <p
                                                                    class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-3">
                                                                    Nilai Akhir KP</p>
                                                                <div class="flex items-end gap-3">
                                                                    <span
                                                                        class="text-5xl font-black text-emerald-700 leading-none">{{ $nilaiAkhir }}</span>
                                                                </div>

                                                                <div
                                                                    class="absolute top-0 right-0 w-16 h-16 bg-emerald-100 rounded-full -translate-y-1/3 translate-x-1/3 opacity-50">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div
                                                                class="rounded-2xl border border-slate-100 bg-slate-50 p-5 flex items-center justify-center">
                                                                <div class="text-center">
                                                                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <p class="text-xs font-semibold text-slate-500">Nilai akhir belum
                                                                        direkap</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>


                                                @else
                                                    {{-- Belum ada nilai --}}
                                                    <div class="flex flex-col items-center justify-center py-10 text-center">
                                                        <div
                                                            class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-sm font-bold text-slate-600">Nilai belum tersedia</p>
                                                        <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                                            @if(!$step3Done)
                                                                Selesaikan pengajuan seminar terlebih dahulu.
                                                            @else
                                                                Nilai akan muncul setelah dosen pembimbing menginput penilaian pasca
                                                                seminar.
                                                            @endif
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>{{-- end lg:col-span-2 --}}

                                </div>{{-- end lg:col-span-2 --}}


                        </div>{{-- end grid --}}
                    @endif
            </main>
        </div>
    </div>
    </div>
</body>

</html>