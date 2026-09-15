<x-eoffice::layouts.dosen title="Detail Bimbingan">
    @section('breadcrumbs')
        <a href="{{ route('eoffice.kp.dosen.dashboard') }}" class="text-slate-400 hover:text-slate-700 transition-colors mr-2">
            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Detail Bimbingan</span>
    @endsection
        
        <!-- Flash Message -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-start shadow-sm">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-emerald-800">Berhasil!</h3>
                    <p class="text-sm text-emerald-700 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Header Section -->
            <div class="px-6 py-6 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-full bg-primary-100 flex items-center justify-center text-primary-500 font-bold text-xl border-2 border-white shadow-sm">
                        {{ substr($kp->nim ?? 'M', 0, 2) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Mahasiswa ({{ $kp->nim ?? 'Belum Diisi' }})</h2>
                        <div class="flex items-center gap-2 mt-1">
                            @if($kp->status_kp == 'pending' || !$kp->status_kp)
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">Fase: Pra KP</span>
                            @elseif($kp->status_kp == 'active')
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-primary-100 text-primary-500 uppercase tracking-wider">Fase: Saat KP</span>
                            @else
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Fase: Pasca KP</span>
                            @endif
                            <span class="text-xs text-slate-500">Diajukan pada {{ $kp->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Panel Berdasarkan Fase -->
                @if($kp->status_kp == 'pending' || !$kp->status_kp)
                    <form action="{{ route('eoffice.kp.dosen.bimbingan.approve_pra_kp', $kp->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-primary-500 hover:bg-primary-500 focus:outline-none transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui Topik & Tempat
                        </button>
                    </form>
                @elseif($kp->status_kp == 'active')
                    <a href="{{ route('eoffice.kp.dosen.bimbingan.penilaian', $kp->id) }}" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg shadow-sm text-white bg-primary-500 hover:bg-primary-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Beri Nilai KP
                    </a>
                @elseif($kp->status_kp == 'completed')
                    <a href="{{ route('eoffice.kp.dosen.bimbingan.penilaian', $kp->id) }}" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg shadow-sm text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                        <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Nilai KP
                    </a>
                @endif
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Rencana KP</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Rencana Judul / Topik</p>
                        <p class="mt-1 text-base font-semibold text-slate-900">{{ $kp->judul_kp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Instansi / Perusahaan Tujuan</p>
                        <p class="mt-1 text-base font-semibold text-slate-900">{{ $kp->instansi_kp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Tanggal Mulai</p>
                        <p class="mt-1 text-base font-medium text-slate-900">{{ $kp->tanggal_mulai ? \Carbon\Carbon::parse($kp->tanggal_mulai)->format('d F Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Tanggal Selesai</p>
                        <p class="mt-1 text-base font-medium text-slate-900">{{ $kp->tanggal_selesai ? \Carbon\Carbon::parse($kp->tanggal_selesai)->format('d F Y') : '-' }}</p>
                    </div>
                </div>

                <div class="mt-10">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Validasi Laporan & Makalah (Fase Saat / Pasca KP)</h3>
                    
                    @if($kp->status_kp == 'pending' || !$kp->status_kp)
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium text-slate-700">Tahap Pra KP</p>
                            <p class="text-xs text-slate-500 mt-1">Silakan tinjau judul dan instansi di atas terlebih dahulu. Jika sesuai, tekan tombol "Setujui" di atas agar mahasiswa bisa lanjut KP dan mengunggah laporan.</p>
                        </div>
                    @else
                        @if(session('error'))
                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                                <p class="text-red-700 font-medium text-sm">{{ session('error') }}</p>
                            </div>
                        @endif

                        <div class="space-y-4">
                            @php
                                // Ambil dokumen khusus Laporan dan Makalah
                                $dokumens = $kp->dokumen()->whereIn('jenis_dokumen', ['Laporan', 'Makalah'])->get();
                            @endphp

                            @if($dokumens->isEmpty())
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-center">
                                    <p class="text-sm font-medium text-slate-600">Mahasiswa belum mengunggah Laporan atau Makalah.</p>
                                </div>
                            @else
                                @foreach($dokumens as $dok)
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 border border-slate-200 rounded-xl bg-white hover:shadow-sm transition-shadow">
                                        <div class="flex items-center gap-4 mb-4 sm:mb-0">
                                            <div class="h-12 w-12 bg-primary-50 rounded-lg flex items-center justify-center text-primary-500 border border-primary-100">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-base font-semibold text-slate-900">{{ $dok->jenis_dokumen }} KP</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <a href="{{ $dok->file_url }}" class="text-xs text-primary-500 hover:text-primary-500 underline font-medium">Lihat File ({{ basename($dok->file_path) }})</a>
                                                    <span class="text-slate-300">•</span>
                                                    <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($dok->tanggal_upload)->format('d M Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="w-full sm:w-auto flex items-center gap-2">
                                            @if($dok->status_validasi == 'pending')
                                                <!-- Action Buttons for Dosen -->
                                                <form action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.reject', [$kp->id, $dok->id]) }}" method="POST" class="w-1/2 sm:w-auto">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200">Revisi</button>
                                                </form>
                                                <form action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.approve', [$kp->id, $dok->id]) }}" method="POST" class="w-1/2 sm:w-auto">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        ACC
                                                    </button>
                                                </form>
                                            @elseif($dok->status_validasi == 'approved')
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Disetujui
                                                </span>
                                            @elseif($dok->status_validasi == 'rejected')
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                                    <svg class="w-4 h-4 mr-1.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    Revisi (Ditolak)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
</x-eoffice::layouts.dosen>
