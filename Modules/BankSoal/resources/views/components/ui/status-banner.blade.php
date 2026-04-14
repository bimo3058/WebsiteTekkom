<!-- Status Banner Component - untuk menampilkan periode aktif -->
@if($activePeriode)
    <div class="mb-8 status-banner {{ $isUploadOpen ? 'status-banner-success' : 'status-banner-warning' }}">
        <div class="flex gap-4 items-start">
            @if($isUploadOpen)
                <i class="fas fa-calendar-check text-2xl text-green-600 mt-1"></i>
            @else
                <i class="fas fa-calendar-times text-2xl text-yellow-600 mt-1"></i>
            @endif
            <div>
                <h3 class="font-bold text-slate-800 mb-1 text-lg">{{ $activePeriode->judul }}</h3>
                <p class="text-slate-600 text-sm">
                    Batas akhir pengunggahan RPS untuk Semester <strong>{{ $activePeriode->semester }} {{ $activePeriode->tahun_ajaran }}</strong> adalah <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }}</strong>.
                    @if(!$isUploadOpen)
                        <span class="text-red-600 font-semibold block mt-2">⚠️ Sesi unggah saat ini sedang ditutup.</span>
                    @endif
                </p>
            </div>
        </div>
        <a href="#" class="btn-secondary whitespace-nowrap">
            <i class="fas fa-circle-question text-sm"></i> Panduan
        </a>
    </div>

    @if($tenggatH7 && count($unsubmittedMk) > 0)
        <div class="mb-8 alert alert-warning">
            <i class="fas fa-exclamation-triangle text-lg"></i>
            <div>
                <p class="text-sm">
                    <strong>
                        Waktu tersisa {{ $daysLeft }} 
                        @if(isset($isHourFormat) && $isHourFormat)
                            jam!
                        @else
                            hari!
                        @endif
                    </strong>
                </p>
                <p class="text-sm">Anda belum mengunggah RPS untuk: <strong>{{ implode(', ', $unsubmittedMk) }}</strong></p>
            </div>
        </div>
    @endif
@else
    <div class="mb-8 p-6 bg-white border-l-4 border-yellow-400 rounded-lg flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-xmark text-yellow-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Belum Ada Jadwal Pengajuan</h3>
                <p class="text-sm text-slate-600">Tidak ada sesi pengajuan RPS yang ditambahkan saat ini</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full whitespace-nowrap flex-shrink-0">
            <i class="fas fa-exclamation-circle"></i> Belum Aktif
        </span>
    </div>
@endif
