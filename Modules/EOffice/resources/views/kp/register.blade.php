<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Kerja Praktik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Pendaftaran Kerja Praktik</h1>
            <p class="text-lg text-slate-600">Sistem Akademik Terintegrasi - Form Pendaftaran KP</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-700">
                <h2 class="text-xl font-semibold text-white">Formulir Pengajuan</h2>
                <p class="text-blue-100 text-sm mt-1">Lengkapi data di bawah ini dengan benar sesuai rencana KP kamu.</p>
            </div>
            
            <div class="p-8">
                @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-emerald-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                <form action="{{ route('kp.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rencana Tempat -->
                        <div class="col-span-2">
                            <label for="rencana_tempat" class="block text-sm font-medium text-slate-700 mb-1">Nama Instansi / Perusahaan Tujuan</label>
                            <input type="text" name="rencana_tempat" id="rencana_tempat" placeholder="Contoh: PT. Telekomunikasi Indonesia (Telkom)" required
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150 ease-in-out py-3 px-4 border">
                        </div>

                        <!-- Rencana Judul -->
                        <div class="col-span-2">
                            <label for="rencana_judul" class="block text-sm font-medium text-slate-700 mb-1">Rencana Topik / Judul KP</label>
                            <input type="text" name="rencana_judul" id="rencana_judul" placeholder="Masukkan rencana topik yang akan dikerjakan" required
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150 ease-in-out py-3 px-4 border">
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150 ease-in-out py-3 px-4 border text-slate-600">
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" required
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150 ease-in-out py-3 px-4 border text-slate-600">
                        </div>
                    </div>

                    <!-- Alert Info -->
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mt-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-amber-700">
                                    Pastikan data yang diisi sudah benar. Pengajuan akan direview oleh Koordinator Kerja Praktik sebelum dapat diproses lebih lanjut.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" 
                            class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                            Kirim Pengajuan KP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
