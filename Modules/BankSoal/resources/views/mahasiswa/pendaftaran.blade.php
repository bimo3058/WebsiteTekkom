<x-banksoal::layouts.mahasiswa>
    <!-- Simple Header -->
    <div class="mb-6 border-b border-slate-200 pb-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">Pengajuan
            Pendaftaran</h1>
        <p class="text-[12px] text-slate-500 mt-1.5 font-bold uppercase tracking-widest">Portal Pendaftaran Ujian
            Komprehensif S1</p>
    </div>

    @if($activePeriode)

        <!-- Status Banner Logic -->
        @if(isset($pendaftar) && $pendaftar->status_pendaftaran === 'pending')
            <div
                class="mb-6 bg-white border-2 border-amber-500 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h4 class="text-[10px] font-bold text-amber-600 mb-1 uppercase tracking-widest">STATUS PENGAJUAN</h4>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-0.5">Dalam Proses Verifikasi</h3>
                    <p class="text-xs text-slate-600 font-medium">Berkas pendaftaran Anda sedang ditinjau oleh staf akademik
                        program studi.</p>
                </div>
                <div
                    class="shrink-0 px-4 py-2 bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-widest border border-amber-200">
                    Menunggu Validasi
                </div>
            </div>
        @elseif(isset($pendaftar) && ($pendaftar->status_pendaftaran === 'rejected' || $pendaftar->trashed()))
            <div
                class="mb-6 bg-white border-2 border-red-600 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h4 class="text-[10px] font-bold text-red-600 mb-1 uppercase tracking-widest">STATUS PENGAJUAN</h4>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-0.5">Pendaftaran Ditolak</h3>
                    <p class="text-xs text-slate-600 font-medium">Mohon maaf, pengajuan Anda ditolak. Silakan periksa kembali
                        kelengkapan syarat Anda.</p>
                </div>
                <div
                    class="shrink-0 px-4 py-2 bg-red-100 text-red-800 text-xs font-bold uppercase tracking-widest border border-red-200">
                    Ditolak
                </div>
            </div>
        @elseif(isset($pendaftar) && $pendaftar->status_pendaftaran === 'approved')
            <div
                class="mb-6 bg-white border-2 border-green-600 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h4 class="text-[10px] font-bold text-green-700 mb-1 uppercase tracking-widest">STATUS PENGAJUAN</h4>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-0.5">Pendaftaran Disetujui</h3>
                    <p class="text-xs text-slate-600 font-medium">Berkas Anda telah diverifikasi dan disetujui. Silakan cek
                        jadwal di dasbor utama.</p>
                </div>
                <div
                    class="shrink-0 px-4 py-2 bg-green-100 text-green-800 text-xs font-bold uppercase tracking-widest border border-green-200">
                    Disetujui
                </div>
            </div>
        @endif

        <!-- Registration Card (Compact Stacked Layout) -->
        <div class="bg-white border border-slate-300 flex flex-col mx-auto w-full max-w-5xl">

            <!-- Content Section -->
            <div class="w-full p-6 md:p-8 flex flex-col justify-center">
                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
                    <div>
                        <div
                            class="inline-flex items-center px-3 py-1 text-[10px] font-bold uppercase tracking-widest border mb-3 w-max @if(isset($pendaftar) && ($pendaftar->status_pendaftaran === 'rejected' || $pendaftar->trashed())) bg-red-50 text-red-700 border-red-200 @else bg-slate-900 text-white border-slate-900 @endif">
                            @if(isset($pendaftar) && ($pendaftar->status_pendaftaran === 'rejected' || $pendaftar->trashed())) Registrasi Ditolak @else
                            Registrasi Dibuka @endif
                        </div>
                        <h2
                            class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-none tracking-tight uppercase">
                            {{ $activePeriode->nama_periode }}
                        </h2>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 p-3 text-right">
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">Tenggat Registrasi</p>
                        <p class="text-sm font-bold text-slate-900 font-mono">
                            {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}, 23:59
                            WIB
                        </p>
                    </div>
                </div>

                <div class="text-sm text-slate-600 leading-relaxed mb-6 space-y-3">
                    <p>
                        Formulir ini digunakan untuk pendaftaran Ujian Komprehensif Program Studi S1 Teknik Komputer bulan <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('F Y') }}</strong>. Pendaftaran hanya dibuka untuk mahasiswa minimal semester 7 dan diprioritaskan bagi mahasiswa yang telah siap mengikuti Sidang Tugas Akhir.
                    </p>
                    <p>
                        Dengan mengisi formulir ini, Anda menyatakan bersedia mematuhi seluruh aturan ujian yang berlaku.
                    </p>
                </div>

                <div class="w-full border-t border-slate-200 mb-6"></div>

                <div class="flex justify-end mt-auto">
                    @if(isset($pendaftar))
                        <!-- Already registered / disabled button -->
                        <button disabled
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-slate-100 text-slate-400 border border-slate-200 px-8 py-3 text-xs font-bold uppercase tracking-widest cursor-not-allowed">
                            Berkas Telah Diajukan
                        </button>
                    @else
                        <!-- Active register button -->
                        <a href="{{ route('komprehensif.mahasiswa.pendaftaran.form') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 text-xs font-bold uppercase tracking-widest transition-colors">
                            Borang Pendaftaran &rarr;
                        </a>
                    @endif
                </div>
            </div>

        </div>
    @else
        <!-- No Active Registration State -->
        <div class="flex flex-col border border-slate-300 bg-white min-h-[40vh] justify-center p-8 md:p-12">
            <div class="max-w-3xl flex flex-col items-start w-full">

                <div class="mb-8 inline-flex">
                    <span
                        class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-bold uppercase tracking-widest">
                        Status Terkunci
                    </span>
                </div>

                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                    Pendaftaran Belum Dibuka
                </h2>

                <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl font-medium">
                    Saat ini tidak ada periode pendaftaran ujian komprehensif yang aktif. Jadwal baru akan diumumkan oleh
                    administrasi akademik BAAK.
                </p>

                <div class="border-t border-slate-200 w-full pt-6">
                    <a href="{{ route('komprehensif.mahasiswa.dashboard') }}"
                        class="inline-flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 text-xs font-bold uppercase tracking-widest transition-colors">
                        &larr; Dasbor
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-banksoal::layouts.mahasiswa>