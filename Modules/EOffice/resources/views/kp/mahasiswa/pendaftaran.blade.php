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
            @elseif(!$registrationOpen)
                {{-- Pendaftaran Ditutup --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center max-w-2xl mx-auto mt-10">
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
                @if($endDate)
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3 shadow-sm max-w-3xl">
                        <div class="p-2 bg-blue-100 rounded-lg text-blue-700 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-blue-900">Pemberitahuan Pendaftaran</h4>
                            <p class="text-xs text-blue-700 mt-1">Batas akhir pendaftaran Kerja Praktik adalah tanggal <strong class="text-blue-950">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>. Pastikan Anda menyelesaikan pengisian data sebelum tanggal tersebut.</p>
                        </div>
                    </div>
                @endif

                {{-- Form Pendaftaran --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden max-w-3xl">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                        <h2 class="text-base font-bold text-slate-800">Formulir Pengajuan</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Pastikan data yang Anda isi sudah benar dan final.</p>
                    </div>
                    <form action="{{ route('eoffice.kp.mahasiswa.pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        
                        <div class="space-y-6">


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

                                {{-- IPK --}}
                                <div>
                                    <label for="ipk" class="block text-sm font-medium text-slate-700 mb-1">Indeks Prestasi Kumulatif (IPK) <span class="text-red-500">*</span></label>
                                    <input type="number" name="ipk" id="ipk" value="{{ old('ipk') }}" step="0.01" min="0" max="4.00" required placeholder="Contoh: 3.50"
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    @error('ipk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Kelas --}}
                                <div>
                                    <label for="kelas" class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                                    <select name="kelas" id="kelas" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border text-slate-700 bg-white">
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
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    @error('sks_diambil') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

                                {{-- Bukti IRS / Transkrip Terbaik --}}
                                <div class="md:col-span-2">
                                    <label for="transkrip_terbaik" class="block text-sm font-medium text-slate-700 mb-1">Bukti IRS / Transkrip Terbaik (PDF/JPG/PNG) <span class="text-red-500">*</span></label>
                                    <input type="file" name="transkrip_terbaik" id="transkrip_terbaik" required accept=".pdf,image/*"
                                           class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border rounded-lg p-2">
                                    <p class="text-xs text-slate-400 mt-1">Maksimal ukuran file: 10MB</p>
                                    @error('transkrip_terbaik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
