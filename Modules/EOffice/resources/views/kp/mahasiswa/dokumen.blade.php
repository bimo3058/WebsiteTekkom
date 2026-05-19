<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Dokumen KP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Dokumen KP'])

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
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 flex items-center justify-between bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center mr-3 flex-shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-rose-900 font-bold">Gagal disubmit!</p>
                        <p class="text-xs text-rose-700">{{ session('error') ?? 'Pastikan format file sesuai dan ukuran tidak melebihi batas.' }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dokumen Pelaksanaan KP</h1>
                <p class="text-sm text-slate-500 mt-1">Lengkapi seluruh tahapan dokumen berikut untuk menyelesaikan rangkaian Kerja Praktik Anda.</p>
            </div>

            {{-- STEPPER SEQUENCE --}}
            @php
                $step1 = !empty($kp->judul_fix) && !empty($kp->tempat_fix);
                $step2 = $dokumenByJenis->has('Bukti Terima');
                $step3 = $dokumenByJenis->has('Laporan') && $dokumenByJenis->has('Makalah');
                
                $currentStep = 1;
                if ($step1) $currentStep = 2;
                if ($step1 && $step2) $currentStep = 3;
                if ($step1 && $step2 && $step3) $currentStep = 4;
            @endphp

            <div class="mb-10">
                <div class="relative">
                    {{-- Progress Line --}}
                    <div class="absolute top-5 left-0 w-full h-0.5 bg-slate-200 -z-0"></div>
                    <div class="absolute top-5 left-0 h-0.5 bg-indigo-500 transition-all duration-500 -z-0" style="width: {{ ($currentStep - 1) * 33.33 }}%"></div>
                    
                    {{-- Steps --}}
                    <div class="relative z-10 flex justify-between">
                        @foreach([
                            ['label' => 'Data Instansi', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                            ['label' => 'Bukti Terima', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label' => 'Laporan Akhir', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253']
                        ] as $index => $step)
                            @php 
                                $num = $index + 1;
                                $isCompleted = $currentStep > $num;
                                $isCurrent = $currentStep === $num;
                            @endphp
                            <div class="flex flex-col items-center group">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm
                                    {{ $isCompleted ? 'bg-indigo-600 text-white' : ($isCurrent ? 'bg-white border-2 border-indigo-600 text-indigo-600' : 'bg-white border-2 border-slate-200 text-slate-400') }}">
                                    @if($isCompleted)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-[11px] font-bold uppercase tracking-wider {{ $isCurrent ? 'text-indigo-600' : 'text-slate-500' }}">{{ $step['label'] }}</p>
                                    <p class="text-[9px] text-slate-400">{{ $isCompleted ? 'Selesai' : ($isCurrent ? 'Sedang Berjalan' : 'Belum') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Kolom Kiri: Form Judul Fix & List Dokumen --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Card Step 1: Judul Fix & Tempat Fix --}}
                    <div id="step-1" class="bg-white rounded-2xl border transition-all duration-300 {{ $currentStep === 1 ? 'border-indigo-500 ring-4 ring-indigo-200 shadow-lg shadow-indigo-100/50' : 'border-slate-200 shadow-sm' }} overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between {{ $currentStep === 1 ? 'bg-indigo-50/30' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-900 text-white text-[10px] flex items-center justify-center font-bold">1</span>
                                <h2 class="text-base font-bold text-slate-800">Data Instansi & Laporan Fix</h2>
                            </div>
                            @if($step1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Terverifikasi
                            </span>
                            @endif
                        </div>
                        <form action="{{ route('eoffice.kp.mahasiswa.dokumen.update_data') }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tempat Instansi Fix</label>
                                    <input type="text" name="tempat_fix" value="{{ old('tempat_fix', $kp->tempat_fix) }}" required placeholder="Nama instansi tempat Anda diterima..."
                                           class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 border bg-slate-50/50 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul Laporan Fix</label>
                                    <input type="text" name="judul_fix" value="{{ old('judul_fix', $kp->judul_fix) }}" required placeholder="Judul laporan akhir KP Anda..."
                                           class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 border bg-slate-50/50 focus:bg-white transition-all">
                                </div>
                            </div>
                            <div class="flex justify-end border-t border-slate-50 pt-5">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-indigo-600 transition-all shadow-sm active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Section Dokumen Uploads --}}
                    <div class="space-y-6">
                        <div class="flex items-center justify-between px-2">
                            <h2 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Dokumen Pelaksanaan</h2>
                            <span class="text-[10px] text-slate-400 italic">Klik unggah untuk memperbarui draf</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @php
                                $uploadItems = [
                                    ['id' => 'step-2', 'key' => 'Bukti Terima', 'display_name' => 'Bukti Terima', 'number' => '2', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'desc' => 'Surat balasan atau email penerimaan dari instansi.', 'color' => 'blue'],
                                    ['id' => 'step-3-1', 'key' => 'Laporan', 'display_name' => 'Laporan Akhir', 'number' => '3', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'desc' => 'Draf Laporan KP versi terakhir (PDF).', 'color' => 'indigo'],
                                    ['id' => 'step-3-2', 'key' => 'Makalah', 'display_name' => 'Makalah IEEE', 'number' => '3', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'desc' => 'Makalah format IEEE (PDF).', 'color' => 'rose'],
                                ];
                            @endphp

                            @foreach($uploadItems as $item)
                            @php
                                $jenis = $item['key'];
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
                                
                                if ($jenis === 'Nilai Lapangan' && $latestDoc) {
                                    $st = ['label' => 'Tersedia', 'class' => 'bg-blue-100 text-blue-700'];
                                }
                                
                                $isActiveCard = false;
                                if ($jenis === 'Bukti Terima' && $currentStep === 2) $isActiveCard = true;
                                if (($jenis === 'Laporan' || $jenis === 'Makalah') && $currentStep === 3) $isActiveCard = true;
                            @endphp

                            <div id="{{ $item['id'] }}" class="bg-white rounded-2xl border p-5 flex flex-col h-full transition-all duration-300 relative {{ $isActiveCard ? 'border-indigo-500 ring-4 ring-indigo-200 shadow-lg shadow-indigo-100/50 z-10' : 'border-slate-100 shadow-sm hover:shadow-md' }}">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 relative">
                                            <span class="absolute -top-2 -right-2 w-5 h-5 bg-slate-800 text-white text-[10px] font-bold flex items-center justify-center rounded-full shadow-sm">{{ $item['number'] }}</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900">{{ $item['display_name'] }}</h3>
                                    </div>
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $st['class'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[11px] text-slate-500 leading-relaxed mb-4">{{ $item['desc'] }}</p>
                                </div>

                                @if($latestDoc)
                                <div class="mb-4 flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <p class="text-[10px] text-slate-500 truncate flex-1">v.{{ \Carbon\Carbon::parse($latestDoc->created_at)->format('d.m.Y') }}</p>
                                    <a href="{{ asset('storage/' . $latestDoc->file_path) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700">LIHAT</a>
                                </div>
                                @endif

                                <div x-data="{ modalOpen: false }">
                                    <button @click="modalOpen = true" class="w-full flex items-center justify-center gap-2 px-4 py-2 border-2 border-slate-100 rounded-xl text-xs font-bold text-slate-700 hover:border-indigo-500 hover:bg-indigo-50 transition-all active:scale-95">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        {{ $latestDoc ? 'Unggah Ulang' : 'Unggah Dokumen' }}
                                    </button>


                                    {{-- Modal Upload --}}
                                    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" @click="modalOpen = false">
                                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                                <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                                                    
                                                    <div class="bg-white p-6">
                                                        <div class="flex items-center gap-4 mb-6">
                                                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-lg leading-6 font-bold text-slate-900">Unggah {{ $jenis }}</h3>
                                                                <p class="text-xs text-slate-400 mt-0.5">Versi akan diperbarui setelah berhasil disimpan.</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-4 p-8 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:bg-white hover:border-indigo-400 transition-all group text-center">
                                                            <input type="file" name="file" required class="hidden" id="file-{{ $jenis }}" @change="fileName = $event.target.files[0].name" x-data="{ fileName: '' }">
                                                            <label for="file-{{ $jenis }}" class="cursor-pointer block">
                                                                <svg class="w-10 h-10 mx-auto text-slate-300 group-hover:text-indigo-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                                <p class="text-sm font-bold text-slate-700" x-text="fileName || 'Klik untuk memilih file'"></p>
                                                                <p class="text-[10px] text-slate-400 mt-1">Format PDF/Word, Maks. 10MB</p>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                                        <button type="submit" class="inline-flex justify-center rounded-xl px-6 py-2.5 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                                                            Simpan Unggahan
                                                        </button>
                                                        <button type="button" @click="modalOpen = false" class="inline-flex justify-center rounded-xl px-6 py-2.5 bg-white border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-100 transition-all">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Download Templates --}}
                <div class="space-y-8">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-50 bg-indigo-50/20">
                            <h2 class="text-base font-bold text-indigo-900">Template Dokumen</h2>
                            <p class="text-[11px] text-indigo-700/70 mt-0.5">Wajib menggunakan format standar departemen.</p>
                        </div>
                        <div class="p-5 space-y-4">
                            @foreach([
                                ['type' => 'a2', 'title' => 'Presensi KP (A2)', 'desc' => 'Borang Nilai Lapangan (.pdf)', 'color' => 'amber'],
                                ['type' => 'laporan', 'title' => 'Laporan Akhir KP', 'desc' => 'Draf Laporan (.docx)', 'color' => 'blue'],
                                ['type' => 'makalah', 'title' => 'Makalah IEEE', 'desc' => 'Format Konferensi (.docx)', 'color' => 'indigo'],
                            ] as $tm)
                                <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', $tm['type']) }}" class="flex items-center p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50 transition-all group">
                                    <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center mr-4 group-hover:bg-white group-hover:text-indigo-600 shadow-sm transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-slate-800">{{ $tm['title'] }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $tm['desc'] }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-100">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-amber-900">Informasi Penting</h3>
                        </div>
                        <p class="text-[11px] text-amber-800 leading-relaxed italic">
                            Pastikan "Data Instansi & Laporan Fix" telah diisi dengan benar sebelum mengunggah draf laporan. Data ini akan muncul secara otomatis di lembar pengesahan dan undangan seminar.
                        </p>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>
</body>
</html>
