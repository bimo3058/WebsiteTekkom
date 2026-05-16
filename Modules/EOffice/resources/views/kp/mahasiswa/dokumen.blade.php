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
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dokumen Pelaksanaan KP</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dan unggah semua dokumen yang dibutuhkan selama masa Kerja Praktik.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Form Judul Fix & List Dokumen --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Card: Judul Fix & Tempat Fix --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-800">Judul & Tempat KP Fix</h2>
                            @if(!empty($kp->judul_fix) && !empty($kp->tempat_fix))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">Terisi</span>
                            @endif
                        </div>
                        <form action="{{ route('eoffice.kp.mahasiswa.dokumen.update_data') }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Instansi Fix</label>
                                    <input type="text" name="tempat_fix" value="{{ old('tempat_fix', $kp->tempat_fix) }}" required placeholder="Nama instansi tempat Anda diterima..."
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Laporan Fix</label>
                                    <input type="text" name="judul_fix" value="{{ old('judul_fix', $kp->judul_fix) }}" required placeholder="Judul laporan akhir KP Anda..."
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border">
                                </div>
                                <div class="flex justify-end pt-2">
                                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Card: Daftar Dokumen Wajib Upload --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h2 class="text-base font-bold text-slate-800">Daftar Dokumen Unggahan</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @php
                                $requiredDocs = [
                                    'Bukti Terima' => 'Bukti bahwa Anda telah diterima di instansi KP (Surat Balasan / Email).',
                                    'A2' => 'Borang A2: Logbook / Kehadiran harian (opsional diupload di sini).',
                                    'Laporan' => 'Draf Laporan KP untuk direview oleh Dosen Pembimbing.',
                                    'Makalah' => 'Draf Makalah (format IEEE) hasil dari Kerja Praktik.',
                                ];
                            @endphp

                            @foreach($requiredDocs as $jenis => $desc)
                            @php
                                $docGroup = $dokumenByJenis->get($jenis);
                                $latestDoc = $docGroup ? $docGroup->sortByDesc('created_at')->first() : null;
                                $status = $latestDoc ? strtolower($latestDoc->status_validasi) : 'belum';
                                
                                $statusColors = [
                                    'belum' => 'bg-slate-100 text-slate-500',
                                    'menunggu' => 'bg-amber-100 text-amber-700',
                                    'disetujui' => 'bg-emerald-100 text-emerald-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700', // alias
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    'rejected' => 'bg-red-100 text-red-700', // alias
                                ];
                                $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-500';
                            @endphp
                            
                            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-lg {{ $status === 'disetujui' || $status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-50 text-slate-400 border border-slate-200' }} flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-sm font-bold text-slate-900">{{ $jenis }}</h3>
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $color }}">
                                                {{ $status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500">{{ $desc }}</p>
                                        @if($latestDoc)
                                        <p class="text-[10px] text-slate-400 mt-2">Diunggah: {{ $latestDoc->tanggal_upload }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Upload Button Modal Trigger --}}
                                <div x-data="{ modalOpen: false }">
                                    <button @click="modalOpen = true" class="inline-flex items-center px-3 py-1.5 border border-slate-300 shadow-sm text-xs font-medium rounded text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        Unggah File
                                    </button>

                                    {{-- Modal Upload --}}
                                    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="modalOpen = false">
                                                <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                                <form action="{{ route('eoffice.kp.mahasiswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                                                    
                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                        <h3 class="text-lg leading-6 font-bold text-slate-900 mb-2">Unggah {{ $jenis }}</h3>
                                                        <p class="text-sm text-slate-500 mb-4">Pastikan file dalam format PDF/Word dan ukuran maksimal 10MB.</p>
                                                        
                                                        <div class="mt-2">
                                                            <input type="file" name="file" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                        </div>
                                                    </div>
                                                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-2">
                                                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:w-auto sm:text-sm">
                                                            Simpan Unggahan
                                                        </button>
                                                        <button type="button" @click="modalOpen = false" class="w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:w-auto sm:text-sm">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Modal --}}

                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Download Templates --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-blue-50/50">
                            <h2 class="text-base font-bold text-blue-900">Template Dokumen</h2>
                            <p class="text-xs text-blue-700 mt-1">Unduh template standar departemen untuk keperluan laporan dan borang.</p>
                        </div>
                        <div class="p-4 space-y-3">
                            <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'laporan') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all group">
                                <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 flex items-center justify-center mr-3 group-hover:bg-white group-hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-800">Template Laporan KP</p>
                                    <p class="text-[10px] text-slate-400">Format Microsoft Word (.docx)</p>
                                </div>
                            </a>

                            <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'makalah') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all group">
                                <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 flex items-center justify-center mr-3 group-hover:bg-white group-hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-800">Template Makalah IEEE</p>
                                    <p class="text-[10px] text-slate-400">Format Microsoft Word (.docx)</p>
                                </div>
                            </a>

                            <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'a2') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                                <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 flex items-center justify-center mr-3 group-hover:bg-white group-hover:text-emerald-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-800">Form A2 (Logbook & Nilai)</p>
                                    <p class="text-[10px] text-slate-400">Generate PDF Kosong</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>
</body>
</html>
