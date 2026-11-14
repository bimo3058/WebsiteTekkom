@php
    $bannerTone = ! $activePeriode
        ? 'is-closed'
        : ($isUploadOpen ? 'is-open' : 'is-closed');
@endphp

<style>
    .bs-period-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid #FEDF89;
        border-radius: 12px;
        background: #FFFAEB;
        color: #93370D;
    }

    .bs-period-banner.is-open {
        border-color: #ABEFC6;
        background: #ECFDF3;
        color: #054F31;
    }

    .bs-period-banner.is-closed {
        border-color: #FECDCA;
        background: #FEF3F2;
        color: #912018;
    }

    .bs-period-banner.is-warning {
        border-color: #FEDF89;
        background: #FFFAEB;
        color: #93370D;
    }

    .bs-period-banner-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #FEF0C7;
        color: #B54708;
        font-size: 14px;
    }

    .bs-period-banner.is-open .bs-period-banner-icon {
        background: #D1FADF;
        color: #067647;
    }

    .bs-period-banner.is-closed .bs-period-banner-icon {
        background: #FEE4E2;
        color: #B42318;
    }

    .bs-period-banner-copy {
        flex: 1;
        min-width: 0;
    }

    .bs-period-banner-title {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
    }

    .bs-period-banner-copy p {
        margin-top: 3px;
        font-size: 12.5px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .bs-period-banner-copy button.bs-period-banner-more {
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 2px;
        background: none;
        border: 0;
        padding: 0;
        color: inherit;
        cursor: pointer;
    }

    .bs-period-banner-badge {
        display: inline-flex;
        align-items: center;
        flex-shrink: 0;
        padding: 4px 10px;
        border-radius: 999px;
        background: #FEF0C7;
        color: #B54708;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .bs-period-banner.is-open .bs-period-banner-badge {
        background: #D1FADF;
        color: #067647;
    }

    .bs-period-banner.is-closed .bs-period-banner-badge {
        background: #FEE4E2;
        color: #B42318;
    }

    @media (max-width: 767px) {
        .bs-period-banner {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .bs-period-banner-badge {
            margin-left: 44px;
        }
    }
</style>

@if($activePeriode)
    <div class="bs-period-banner {{ $bannerTone }}" role="status">
        <span class="bs-period-banner-icon">
            <i class="fas {{ $isUploadOpen ? 'fa-calendar-check' : 'fa-calendar-xmark' }}" aria-hidden="true"></i>
        </span>
        <div class="bs-period-banner-copy">
            @if($isUploadOpen)
                <span class="bs-period-banner-title">{{ $activePeriode->judul }}</span>
                <p>
                    Batas akhir pengunggahan RPS Semester
                    <strong>{{ $activePeriode->semester }} {{ $activePeriode->tahun_ajaran }}</strong>
                    adalah
                    <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</strong>.
                </p>
            @else
                <span class="bs-period-banner-title">Sesi Unggah RPS Ditutup</span>
                <p>
                    Pengunggahan RPS Semester
                    <strong>{{ $activePeriode->semester }} {{ $activePeriode->tahun_ajaran }}</strong>
                    sudah ditutup. Dokumen yang sudah diajukan tetap dapat dipantau di riwayat pengajuan.
                </p>
            @endif
        </div>
        @unless($isUploadOpen)
            <span class="bs-period-banner-badge">Ditutup</span>
        @endunless
    </div>

    @if($tenggatH7 && count($unsubmittedMk) > 0)
        <div class="bs-period-banner is-warning" role="alert">
            <span class="bs-period-banner-icon"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></span>
            <div class="bs-period-banner-copy">
                <span class="bs-period-banner-title">
                    Waktu tersisa {{ $daysLeft }}
                    @if(isset($isHourFormat) && $isHourFormat)
                        jam!
                    @else
                        hari!
                    @endif
                </span>
                <div x-data="{
                                    items: {{ json_encode($unsubmittedMk) }},
                                    limit: 5,
                                    get visibleItems() { return this.items.slice(0, this.limit); },
                                    get remaining() { return this.items.length - this.limit; }
                                }">
                    <p>Anda belum mengunggah RPS untuk:
                        <strong>
                            <template x-for="(mk, index) in visibleItems" :key="index">
                                <span>
                                    <span x-text="mk"></span><span x-show="index < visibleItems.length - 1 || remaining > 0">,
                                    </span>
                                </span>
                            </template>
                            <button type="button" x-show="remaining > 0" @click="limit += 5" class="bs-period-banner-more"
                                x-text="'+' + remaining"></button>
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="bs-period-banner is-closed" role="status">
        <span class="bs-period-banner-icon"><i class="fas fa-calendar-xmark" aria-hidden="true"></i></span>
        <div class="bs-period-banner-copy">
            <span class="bs-period-banner-title">Belum Ada Jadwal Pengajuan</span>
            <p>Tidak ada sesi pengajuan RPS yang ditambahkan saat ini.</p>
        </div>
        <span class="bs-period-banner-badge">Belum Aktif</span>
    </div>
@endif
