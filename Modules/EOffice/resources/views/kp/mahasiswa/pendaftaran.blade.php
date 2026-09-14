<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Pendaftaran KP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter Tight', sans-serif; }
        :root {
            --primary-50:#EBEDF6;--primary-100:#D0D6E9;--primary-500:#2A3A7C;
            --grey-0:#fff;--grey-50:#f9fafb;--grey-100:#f3f4f6;--grey-200:#e5e7eb;
            --grey-400:#9ca3af;--grey-500:#6b7280;--grey-600:#4b5563;
            --grey-700:#374151;--grey-800:#1f2937;--grey-900:#030712;
            --success-0:#f0fdf4;--success-50:#dcfce7;--success-100:#bbf7d0;--success-300:#16a34a;
            --warning-0:#fffbeb;--warning-50:#fef3c7;--warning-100:#fde68a;--warning-300:#d97706;
            --error-0:#fff1f2;--error-50:#ffe4e6;--error-200:#f87171;--error-300:#dc2626;
            --sky-500:#0ea5e9;
        }
        .sikape-card { background:#fff; border:1px solid #DFE1E7; border-radius:12px; }
    </style>
</head>
<body style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">

        {{-- Outer content container with border --}}
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden rounded-lg" style="margin:8px; border:1px solid #DFE1E7; background:#fff;">

            @include('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Pendaftaran KP'])

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pendaftaran Kerja Praktik</h1>
                        <p class="text-sm text-slate-500 mt-1">Lengkapi data di bawah ini untuk mengajukan Kerja Praktik.</p>
                    </div>
                </div>

                <div class="space-y-6">

                @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                {{-- Progress Stepper --}}
                @php
                    $step1_done = $existingKp ? true : false;
                    $step2_done = $existingKp ? $existingKp->is_acc_admin : false;
                    $step3_done = $existingKp ? !empty($existingKp->dosen_pembimbing_id) : false;
                    
                    $steps = [
                        ['label' => 'Mendaftar', 'desc' => $step1_done ? 'Selesai' : 'Belum', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'done' => $step1_done],
                        ['label' => 'Verifikasi Koor', 'desc' => $step2_done ? 'Selesai' : ($step1_done ? 'Menunggu' : 'Belum'), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'done' => $step2_done],
                        ['label' => 'Pengumuman Dosen', 'desc' => $step3_done ? 'Selesai' : ($step2_done ? 'Menunggu' : 'Belum'), 'icon' => 'M8 7v14m-4-4h8M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z', 'done' => $step3_done],
                    ];
                    $cur = 0;
                    if ($step3_done) {
                        $cur = 2;
                    } elseif ($step1_done) {
                        $cur = 1;
                    }
                @endphp
                <div class="sikape-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-widest mb-5" style="color:var(--grey-400);">Status Pendaftaran Kerja Praktik</p>
                    <div class="flex items-start gap-0 mb-8">
                        @foreach($steps as $i => $step)
                        <div class="flex-1 flex flex-col items-center relative">
                            {{-- Left half-line --}}
                            @if($i > 0)
                            <div class="absolute h-0.5 z-0"
                                    style="top:20px; left:0; right:50%; background:{{ $i <= $cur ? '#2A3A7C' : 'var(--grey-200)' }};"></div>
                            @endif

                            {{-- Right half-line --}}
                            @if($i < count($steps) - 1)
                            <div class="absolute h-0.5 z-0"
                                    style="top:20px; left:50%; right:0; background:{{ $i < $cur ? '#2A3A7C' : 'var(--grey-200)' }};"></div>
                            @endif

                            {{-- Circle --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all duration-300 flex-shrink-0"
                                    style="background:{{ $i <= $cur ? '#2A3A7C' : 'var(--grey-100)' }};
                                        {{ $i === $cur ? 'box-shadow:0 0 0 4px #D0D6E9;' : '' }}">
                                @if($i < $cur || $step['done'])
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @else
                                <svg class="w-5 h-5" style="color:{{ $i <= $cur ? 'white' : 'var(--grey-400)' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $step['icon'] }}"/></svg>
                                @endif
                            </div>

                            {{-- Label --}}
                            <p class="text-xs font-semibold mt-2 text-center" style="color:{{ $i <= $cur ? 'var(--grey-800)' : 'var(--grey-400)' }};">{{ $step['label'] }}</p>
                            <p class="text-[10px] text-center" style="color:{{ $i <= $cur ? 'var(--grey-500)' : 'var(--grey-300)' }};">{{ $step['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    @if($step3_done && $existingKp->dosenPembimbing)
                        <div class="p-4 bg-white rounded-lg border border-slate-200 shadow-sm">
                            <p class="text-slate-700 font-medium text-sm">
                                Anda sudah terdaftar sebagai mahasiswa kerja praktik, dan akan di bimbing oleh <strong class="text-slate-900">{{ $existingKp->dosenPembimbing->nama_lengkap ?? $existingKp->dosenPembimbing->name }}</strong>
                            </p>
                        </div>
                    @elseif($step1_done && !$step2_done)
                        <div class="p-4 bg-primary-50 rounded-lg border border-primary-100 shadow-sm">
                            <p class="text-primary-500 font-medium text-sm">Berhasil mendaftar! silahkan menunggu verifikasi oleh koordinator</p>
                        </div>
                    @elseif($step2_done && !$step3_done)
                        <div class="p-4 bg-white rounded-lg border border-slate-200 shadow-sm">
                            <p class="text-slate-700 font-medium text-sm">Persyaratan Anda telah di-ACC oleh Koordinator KP. Silakan menunggu pengumuman Dosen Pembimbing.</p>
                        </div>
                    @endif
                </div>

                @if($existingKp)
                    {{-- Jika sudah mendaftar, maka kita tidak menampilkan form --}}
                @elseif(!$registrationOpen)
                    {{-- Pendaftaran Ditutup --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center mt-10">
                        <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 mb-2">Pendaftaran Ditutup</h2>
                        <p class="text-slate-600 mb-4">
                            Saat ini periode pendaftaran Kerja Praktik sedang ditutup.
                            @if($startDate || $endDate)
                                <br><br>
                                <span class="inline-flex bg-slate-50 border border-slate-200 rounded px-3 py-2 text-sm font-medium">
                                    Jadwal Pendaftaran: 
                                    {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Kapan Saja' }}
                                    - 
                                    {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Kapan Saja' }}
                                </span>
                            @endif
                        </p>
                        <p class="text-sm text-slate-500 mb-6">Silakan cek kembali di lain waktu atau hubungi Koordinator KP untuk informasi lebih lanjut.</p>
                        <a href="{{ route('eoffice.kp.mahasiswa.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @else
                    {{-- Banner Pengingat Penutupan Pendaftaran --}}
                    @if($showReminder)
                        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                            <div class="p-2 bg-amber-100 rounded-lg text-amber-700 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h4 class="text-sm font-bold text-amber-900">Pengingat Batas Akhir</h4>
                                    @if($periodeAktif)
                                        <span class="px-2.5 py-0.5 bg-amber-200 text-amber-800 text-[10px] rounded-full uppercase tracking-wider font-semibold">
                                            Periode: {{ $periodeAktif->semester }} {{ $periodeAktif->tahun_ajaran }}
                                        </span>
                                    @endif
                                    @if($endDate)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] rounded-full font-bold border border-rose-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Deadline: {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Pastikan Anda menyelesaikan pengajuan pendaftaran sebelum batas waktu yang telah ditentukan.
                                </p>
                            </div>
                        </div>

                    @elseif($periodeAktif)
                        <div class="mb-6 bg-primary-50 border border-primary-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                            <div class="p-2 bg-primary-100 rounded-lg text-primary-500 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h4 class="text-sm font-bold text-primary-500">Pemberitahuan Pendaftaran</h4>
                                    <span class="px-2.5 py-0.5 bg-primary-200 text-primary-500 text-[10px] rounded-full uppercase tracking-wider font-semibold">
                                        Periode: {{ $periodeAktif->semester }} {{ $periodeAktif->tahun_ajaran }}
                                    </span>
                                    @if($endDate)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-primary-100 text-primary-500 text-[10px] rounded-full font-bold border border-primary-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Berakhir: {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-primary-500 leading-relaxed">
                                    Pendaftaran Kerja Praktik untuk periode ini sedang dibuka. Pastikan Anda melengkapi data yang dibutuhkan.
                                </p>
                            </div>
                        </div>

                    @endif

                    {{-- Form Pendaftaran --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                            <h2 class="text-base font-bold text-slate-800">Formulir Pengajuan</h2>
                            <p class="text-sm text-slate-500 mt-0.5">Pastikan data yang Anda isi sudah benar dan final.</p>
                        </div>
                        <form id="form-pendaftaran" action="{{ route('eoffice.kp.mahasiswa.pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Rencana Judul --}}
                                <div class="md:col-span-2">
                                    <label for="judul_kp" class="block text-sm font-medium text-slate-700 mb-1">Rencana Topik / Judul KP <span class="text-red-500">*</span></label>
                                    <input type="text" name="judul_kp" id="judul_kp" value="{{ old('judul_kp') }}" required placeholder="Contoh: Pembuatan Sistem Otomasi Jaringan..."
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border">
                                    @error('judul_kp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Rencana Tempat --}}
                                <div class="md:col-span-2">
                                    <label for="instansi_kp" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tempat Instansi <span class="text-red-500">*</span></label>
                                    <input type="text" name="instansi_kp" id="instansi_kp" value="{{ old('instansi_kp') }}" required placeholder="Contoh: PT Telekomunikasi Indonesia (Telkom)"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border">
                                    @error('instansi_kp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- IPK --}}
                                <div>
                                    <label for="ipk" class="block text-sm font-medium text-slate-700 mb-1">Indeks Prestasi Kumulatif (IPK) <span class="text-red-500">*</span></label>
                                    <input type="number" name="ipk" id="ipk" value="{{ old('ipk') }}" step="0.01" min="0" max="4.00" required placeholder="Contoh: 3.50"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border">
                                    @error('ipk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Kelas --}}
                                <div>
                                    <label for="kelas" class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                                    <select name="kelas" id="kelas" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border text-slate-700 bg-white">
                                        <option value="" disabled {{ old('kelas') == '' ? 'selected' : '' }}>Pilih Kelas</option>
                                        @foreach($listKelas as $kls)
                                            <option value="{{ $kls }}" {{ old('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                                        @endforeach
                                    </select>
                                    @error('kelas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- SKS yang Diambil --}}
                                <div class="md:col-span-2">
                                    <label for="sks_diambil" class="block text-sm font-medium text-slate-700 mb-1">Jumlah SKS yang Telah Diambil <span class="text-red-500">*</span></label>
                                    <input type="number" name="sks_diambil" id="sks_diambil" value="{{ old('sks_diambil') }}" min="0" required placeholder="Contoh: 110"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border">
                                    @error('sks_diambil') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tanggal Mulai --}}
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border text-slate-600">
                                    @error('tanggal_mulai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tanggal Selesai --}}
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Selesai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 border text-slate-600">
                                    @error('tanggal_selesai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                            </div>

                            {{-- Dokumen yang Diperlukan (Moved inside form) --}}
                            <div class="mt-8 border-t border-slate-200 pt-6">
                                <h3 class="text-sm font-bold text-slate-800">Dokumen yang Diperlukan</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Silakan download template (jika tersedia) dan upload file yang diminta.</p>
                                
                                <div class="mt-4 space-y-3">
                                    @forelse($templatesDokumen ?? collect() as $tmpl)
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-slate-200 bg-white hover:border-primary-300 hover:shadow-sm transition-all gap-4">
                                            
                                            {{-- Info Dokumen --}}
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-50 text-slate-500 shadow-sm border border-slate-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">{{ $tmpl->title }}</p>
                                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $tmpl->description ?? 'Dokumen pra-KP' }}</p>
                                                </div>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                                @if($tmpl->is_downloadable)
                                                    <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', $tmpl->id) }}"
                                                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-primary-500 bg-primary-50 border border-primary-200 rounded-lg hover:bg-primary-100 transition-colors">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Template
                                                    </a>
                                                @endif
                                                
                                                @if($tmpl->is_uploadable)
                                                    <div class="relative w-full sm:w-auto" x-data="{ fileName: '' }">
                                                        <input type="file" name="dokumen_{{ $tmpl->id }}" id="dokumen_{{ $tmpl->id }}" 
                                                               class="sr-only peer" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                                                               x-on:change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                                        <label for="dokumen_{{ $tmpl->id }}"
                                                               class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-slate-300">
                                                            <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            <span x-text="fileName ? fileName : 'Upload File'" class="truncate max-w-[120px]"></span>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center py-6 text-center bg-slate-50 rounded-xl border border-slate-200">
                                            <p class="text-xs font-medium text-slate-500">Tidak ada dokumen yang diperlukan saat ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            
                            <div class="mt-8 border-t border-slate-200 pt-6 flex justify-end bg-slate-50/50 -mx-6 -mb-6 px-6 pb-6 rounded-b-xl border">
                                <button type="submit" class="px-8 py-3 bg-primary-500 text-white text-sm font-bold rounded-xl hover:bg-primary-500 shadow-sm transition-colors focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 whitespace-nowrap active:scale-95">
                                    Kirim Pengajuan KP
                                </button>
                            </div>
                        </form>

                    </div>
                @endif
                </div>

            </div>

            </main>
        </div>
    </div>
</div>
</body>
</html>
