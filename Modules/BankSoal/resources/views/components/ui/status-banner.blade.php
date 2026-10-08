<!-- Status Banner Component - untuk menampilkan periode aktif -->
@if($activePeriode)
    <div class="mb-8 status-banner {{ $isUploadOpen ? 'status-banner-success' : 'status-banner-danger' }}">
        <div class="flex gap-2 items-start">
            @if($isUploadOpen)
                <i class="fas fa-calendar-check text-2xl text-green-600"></i>
            @else
                <i class="fas fa-calendar-times text-2xl text-red-600"></i>
            @endif
            <div>
                @if($isUploadOpen)
                    <h3 class="font-bold text-slate-800 mb-1 text-lg">{{ $activePeriode->judul }}</h3>
                    <p class="text-slate-600 text-sm">
                        Batas akhir pengunggahan RPS untuk Semester <strong>{{ $activePeriode->semester }}
                            {{ $activePeriode->tahun_ajaran }}</strong> adalah
                        <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->locale('id')->translatedFormat('d F Y, H:i') }}
                            WIB</strong>.
                @else
                        <h3 class="text-white-600 font-semibold block mt-2">Sesi unggah saat ini sedang ditutup.</h3>
                    @endif
                </p>
            </div>
        </div>
    </div>

    @if($tenggatH7 && count($unsubmittedMk) > 0)
        <div class="mb-0 alert alert-warning">
            <i class="fas fa-exclamation-triangle text-lg shrink-0"></i>
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
                <div x-data="{
                                            items: {{ json_encode($unsubmittedMk) }},
                                            limit: 5,
                                            get visibleItems() { return this.items.slice(0, this.limit); },
                                            get remaining() { return this.items.length - this.limit; }
                                        }">
                    <p class="text-sm">Anda belum mengunggah RPS untuk:
                        <strong>
                            <template x-for="(mk, index) in visibleItems" :key="index">
                                <span>
                                    <span x-text="mk"></span><span x-show="index < visibleItems.length - 1 || remaining > 0">,
                                    </span>
                                </span>
                            </template>
                            <button type="button" x-show="remaining > 0" @click="limit += 5"
                                class="text-amber-700 hover:text-amber-900 underline font-bold cursor-pointer"
                                x-text="'+' + remaining"></button>
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    @endif
@else
    <div
        class="mb-8 p-6 bg-white border-l-4 border-yellow-400 rounded-lg flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-xmark text-yellow-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Belum Ada Jadwal Pengajuan</h3>
                <p class="text-sm text-slate-600">Tidak ada sesi pengajuan RPS yang ditambahkan saat ini</p>
            </div>
        </div>
        <span
            class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full whitespace-nowrap flex-shrink-0">
            <i class="fas fa-exclamation-circle"></i> Belum Aktif
        </span>
    </div>
@endif