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
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Seminar Kerja Praktik</h1>
                <p class="text-sm text-slate-500 mt-1">Daftarkan rencana seminar Anda dan pantau status persetujuannya.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Form Pendaftaran Seminar --}}
                <div class="lg:col-span-2 space-y-6">

                    @if($kp && $kp->seminar)
                    {{-- Status Seminar --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-800">Status Permohonan Seminar</h2>
                            @php
                                $statusSem = strtolower($kp->seminar->status_validasi_syarat);
                                $badgeColors = [
                                    'belum' => 'bg-slate-100 text-slate-700',
                                    'proses' => 'bg-amber-100 text-amber-700',
                                    'valid' => 'bg-emerald-100 text-emerald-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $badgeColors[$statusSem] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $statusSem }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $kp->seminar->tanggal_seminar->translatedFormat('d F Y') }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Waktu</p>
                                    <p class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($kp->seminar->waktu_seminar)->format('H:i') }} WIB</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Ruangan</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $kp->seminar->ruangan }}</p>
                                </div>
                            </div>

                            @if($statusSem === 'valid')
                            <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-bold text-blue-900">Surat Undangan & Form B2 Siap</p>
                                    <p class="text-xs text-blue-700 mt-0.5">Unduh surat undangan dan form kehadiran seminar untuk dicetak.</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'b1') }}" class="inline-flex items-center px-3 py-2 bg-white border border-blue-300 text-blue-700 text-xs font-medium rounded hover:bg-blue-100 transition-colors">
                                        Surat Undangan
                                    </a>
                                    <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'b2') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                        Form B2
                                    </a>
                                </div>
                            </div>
                            @elseif($statusSem === 'proses')
                            <p class="text-sm text-slate-500 text-center">Menunggu validasi Koordinator KP untuk penerbitan surat undangan seminar.</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(!$kp || !$kp->seminar || $kp->seminar->status_validasi_syarat === 'ditolak')
                    {{-- Form Pendaftaran Seminar --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                            <h2 class="text-base font-bold text-slate-800">Daftar Seminar KP</h2>
                            <p class="text-sm text-slate-500 mt-0.5">Tentukan jadwal seminar yang telah disepakati dengan dosen pembimbing.</p>
                        </div>
                        
                        <form action="{{ route('eoffice.kp.mahasiswa.seminar.store') }}" method="POST" class="p-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Seminar <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_seminar" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    <p class="text-[10px] text-slate-500 mt-1">Minimal H+1 dari hari ini</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Waktu Seminar <span class="text-red-500">*</span></label>
                                    <input type="time" name="waktu_seminar" required
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Ruangan <span class="text-red-500">*</span></label>
                                    <input type="text" name="ruangan" required placeholder="Contoh: Ruang Rapat Lt.2 Gedung B"
                                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-200 pt-6">
                                <p class="text-xs text-slate-500">* Pastikan semua syarat seminar telah terpenuhi di panel kanan.</p>
                                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ !$syaratSeminar['semua_terpenuhi'] ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$syaratSeminar['semua_terpenuhi'] ? 'disabled' : '' }}>
                                    Ajukan Seminar
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                </div>

                {{-- Kolom Kanan: Checklist Syarat Seminar --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h2 class="text-base font-bold text-slate-800">Syarat Pendaftaran Seminar</h2>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-4">
                                @php
                                    $checklist = [
                                        ['key' => 'judul_fix', 'label' => 'Judul & Tempat Fix terisi', 'is_met' => $syaratSeminar['judul_fix']],
                                        ['key' => 'bukti_terima', 'label' => 'Bukti Terima diunggah', 'is_met' => $syaratSeminar['bukti_terima']],
                                        ['key' => 'laporan_acc', 'label' => 'Laporan KP disetujui Pembimbing', 'is_met' => $syaratSeminar['laporan_acc']],
                                        ['key' => 'makalah_acc', 'label' => 'Makalah IEEE disetujui Pembimbing', 'is_met' => $syaratSeminar['makalah_acc']],
                                        ['key' => 'kartu_hijau', 'label' => 'Kartu Hijau diunggah', 'is_met' => $syaratSeminar['kartu_hijau']],
                                        ['key' => 'nilai_lapangan', 'label' => 'Nilai Lapangan (Instansi) diunggah', 'is_met' => $syaratSeminar['nilai_lapangan']],
                                    ];
                                @endphp

                                @foreach($checklist as $item)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 mt-0.5">
                                        @if($item['is_met'])
                                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-sm {{ $item['is_met'] ? 'text-slate-700' : 'text-slate-400 line-through' }}">{{ $item['label'] }}</span>
                                </li>
                                @endforeach
                            </ul>

                            @if($syaratSeminar['semua_terpenuhi'])
                            <div class="mt-6 p-3 bg-emerald-50 rounded text-center border border-emerald-200">
                                <p class="text-xs font-bold text-emerald-700">Semua syarat telah terpenuhi!</p>
                            </div>
                            @else
                            <div class="mt-6 p-3 bg-amber-50 rounded text-center border border-amber-200">
                                <p class="text-xs font-bold text-amber-700">Penuhi semua syarat untuk mendaftar seminar.</p>
                            </div>
                            @endif

                            {{-- Tombol Unggah Tambahan (Kartu Hijau & Nilai Lapangan) --}}
                            <div class="mt-6 border-t border-slate-100 pt-6 space-y-3" x-data="{ uploadType: '', modalOpen: false }">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Unggah Berkas Tambahan</p>
                                
                                <button @click="uploadType = 'Kartu Hijau'; modalOpen = true" class="w-full flex items-center justify-between px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                    <span class="text-sm font-medium text-slate-700">Unggah Kartu Hijau</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </button>
                                
                                <button @click="uploadType = 'Nilai Lapangan'; modalOpen = true" class="w-full flex items-center justify-between px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                    <span class="text-sm font-medium text-slate-700">Unggah Nilai Lapangan</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
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
                                                <input type="hidden" name="jenis_dokumen" :value="uploadType">
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <h3 class="text-lg leading-6 font-bold text-slate-900 mb-2">Unggah <span x-text="uploadType"></span></h3>
                                                    <p class="text-sm text-slate-500 mb-4">Pastikan file dalam format PDF/JPG/PNG dan ukuran maksimal 10MB.</p>
                                                    <div class="mt-2">
                                                        <input type="file" name="file" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                    </div>
                                                    
                                                    <template x-if="uploadType === 'Nilai Lapangan'">
                                                        <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                                                            <label class="block text-sm font-bold text-slate-700 mb-1">Nilai Angka <span class="text-red-500">*</span></label>
                                                            <input type="number" step="0.01" min="0" max="100" name="nilai_input_mahasiswa" required class="block w-full text-sm text-slate-900 py-2.5 px-4 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="Contoh: 85.50">
                                                            <p class="text-xs text-slate-500 mt-1">Masukkan nilai 0-100 sesuai dengan berkas pdf penilaian dari instansi.</p>
                                                        </div>
                                                    </template>
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
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>
</body>
</html>
