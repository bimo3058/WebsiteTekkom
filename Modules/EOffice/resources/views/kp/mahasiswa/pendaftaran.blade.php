<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Pendaftaran Kerja Praktik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Pendaftaran KP'])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pendaftaran Kerja Praktik</h1>
                <p class="text-sm text-slate-500 mt-1">Lengkapi data di bawah ini untuk mengajukan Kerja Praktik.</p>
            </div>

            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @if($existingKp)
                {{-- Jika sudah mendaftar --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center max-w-2xl mx-auto mt-10">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Anda Sudah Terdaftar</h2>
                    <p class="text-slate-600 mb-6">Anda sudah memiliki pendaftaran Kerja Praktik yang sedang aktif dengan status <strong>{{ $existingKp->status_kp }}</strong>.</p>
                    <a href="{{ route('eoffice.kp.mahasiswa.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @else
                {{-- Form Pendaftaran --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden max-w-3xl">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                        <h2 class="text-base font-bold text-slate-800">Formulir Pengajuan</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Pastikan data yang Anda isi sudah benar dan final.</p>
                    </div>

                    <form action="{{ route('eoffice.kp.mahasiswa.pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        
                        <div class="space-y-6">
                            {{-- Transkrip --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Upload Transkrip Nilai Terakhir <span class="text-red-500">*</span></label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-blue-500 transition-colors bg-slate-50" x-data="{ fileName: '' }">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <label for="transkrip" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none px-1">
                                                <span>Upload a file</span>
                                                <input id="transkrip" name="transkrip" type="file" class="sr-only" required accept=".pdf,.jpg,.jpeg,.png" @change="fileName = $event.target.files[0].name">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-slate-500">PDF, PNG, JPG up to 5MB</p>
                                        <p class="text-sm font-semibold text-blue-600 mt-2" x-show="fileName" x-text="fileName"></p>
                                    </div>
                                </div>
                                @error('transkrip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Rencana Judul --}}
                                <div class="md:col-span-2">
                                    <label for="rencana_judul" class="block text-sm font-medium text-slate-700 mb-1">Rencana Topik / Judul KP <span class="text-red-500">*</span></label>
                                    <input type="text" name="rencana_judul" id="rencana_judul" value="{{ old('rencana_judul') }}" required placeholder="Contoh: Pembuatan Sistem Otomasi Jaringan..."
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    @error('rencana_judul') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Rencana Tempat --}}
                                <div class="md:col-span-2">
                                    <label for="rencana_tempat" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tempat Instansi <span class="text-red-500">*</span></label>
                                    <input type="text" name="rencana_tempat" id="rencana_tempat" value="{{ old('rencana_tempat') }}" required placeholder="Contoh: PT Telekomunikasi Indonesia (Telkom)"
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    @error('rencana_tempat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tanggal Mulai --}}
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border text-slate-600">
                                    @error('tanggal_mulai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tanggal Selesai --}}
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Selesai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border text-slate-600">
                                    @error('tanggal_selesai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                            <button type="reset" class="px-5 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                                Reset Form
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Kirim Pengajuan KP
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </main>
    </div>
</div>
</body>
</html>
