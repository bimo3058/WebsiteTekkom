<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asisten Praktikum & Koordinator">

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Asisten Praktikum & Koordinator</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Daftarkan diri sebagai calon asisten atau koordinator praktikum · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- Badge role aktif --}}
@if($sudahJadiAsprak || $sudahJadiKoor)
<div style="display:flex;gap:8px;flex-wrap:wrap;" class="flex-shrink-0">
    @if($sudahJadiAsprak)
    <div class="mp-badge success sm" style="padding:8px 14px;border-radius:10px;font-size:12px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        Anda aktif sebagai Asisten Praktikum
    </div>
    @endif
    @if($sudahJadiKoor)
    <div class="mp-badge navy sm" style="padding:8px 14px;border-radius:10px;font-size:12px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        Anda aktif sebagai Koordinator Praktikum
    </div>
    @endif
</div>
@endif

{{-- Tidak ada periode terbuka sama sekali --}}
@if($praktikumDenganPeriode->isEmpty())

<div class="mp-card flex-1 flex items-center justify-center" style="min-height:300px;">
    <div style="padding:48px;text-align:center;">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 14px;display:block;">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum ada pendaftaran yang dibuka</div>
        <div style="font-size:13px;color:#666D80;">Admin belum membuka periode pendaftaran asisten praktikum atau koordinator saat ini.</div>
    </div>
</div>

@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum untuk Mendaftar</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        <form method="GET" class="flex gap-2 flex-wrap">
            @php
                $praktikumOptions = [];
                if(isset($praktikumList)) {
                    foreach($praktikumList as $p) {
                        $label = $p->nama;
                        $label .= " · {$p->semester} {$p->tahun_ajaran}";
                        $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $label];
                    }
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : '')))"
                placeholder="Pilih Praktikum..."
                onChange="$event.target.form.submit()"
                minWidth="240px"
            />
        </form>
    </div>
</div>

@php $selectedPraktikumId = request('praktikum_id', $praktikumDenganPeriode->first()?->id); @endphp
@php $selectedPraktikum = $praktikumDenganPeriode->firstWhere('id', $selectedPraktikumId); @endphp

@if($selectedPraktikum)

@php
    $pAsprak = $periodeAktif[$selectedPraktikum->id]['asprak'] ?? null;
    $pKoor   = $periodeAktif[$selectedPraktikum->id]['koor']   ?? null;
    
    $sudahTerdaftar = in_array($selectedPraktikum->id, $praktikumDiikuti);
    
    $existingAsprak = \Modules\EOffice\Models\PendaftaranAsprak::where('user_id', auth()->id())
        ->where('praktikum_id', $selectedPraktikum->id)
        ->orderByDesc('created_at')
        ->first();
    $existingKoor = \Modules\EOffice\Models\PendaftaranKoordinator::where('user_id', auth()->id())
        ->where('praktikum_id', $selectedPraktikum->id)
        ->orderByDesc('created_at')
        ->first();
        
    $isAsprakDiPraktikumIni = \Modules\EOffice\Models\AsprakPraktikum::where('user_id', auth()->id())
        ->where('praktikum_id', $selectedPraktikum->id)
        ->where('role', 'asprak')
        ->exists();

    $isKoorDiPraktikumIni = \Modules\EOffice\Models\AsprakPraktikum::where('user_id', auth()->id())
        ->where('praktikum_id', $selectedPraktikum->id)
        ->where('role', 'koor')
        ->exists();
@endphp




{{-- Dua kolom: Asisten Praktikum | Koordinator --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- ====== ASISTEN PRAKTIKUM ====== --}}
    <div>
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Asisten Praktikum</span>
            <span class="sec-rule"></span>
            @if($pAsprak)
            <span class="mp-badge success sm"><span class="dot"></span>Terbuka</span>
            @else
            <span class="mp-badge neutral sm">Tutup</span>
            @endif
        </div>

        @if(!$pAsprak)
        <div class="mp-card flex-shrink-0">
            <div style="padding:32px;text-align:center;">
                <div style="font-size:13px;color:#666D80;">Pendaftaran asisten praktikum belum dibuka untuk praktikum ini.</div>
            </div>
        </div>

        @elseif($isAsprakDiPraktikumIni)
        <div class="mp-card flex-shrink-0">
            <div style="padding:20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span style="font-weight:600;color:#10B981;">Anda sudah aktif sebagai Asisten Praktikum</span>
                </div>
                <div style="font-size:12px;color:#666D80;margin-top:4px;">Anda terdaftar dan aktif sebagai asisten di praktikum ini.</div>
            </div>
        </div>

        @elseif($existingAsprak && in_array($existingAsprak->status, ['pending','approved']))
        <div class="mp-card flex-shrink-0">
            <div style="padding:20px;">
                <div style="font-size:12px;color:#666D80;margin-bottom:8px;font-weight:600;">{{ $pAsprak->nama }}</div>
                @if($existingAsprak->status === 'pending')
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu seleksi koordinator</span>
                <div style="font-size:11px;color:#666D80;margin-top:8px;">Pendaftaran Anda telah dikirim. Tunggu review dari Koordinator Praktikum.</div>
                @else
                <span class="mp-badge success sm"><span class="dot"></span>Diterima!</span>
                <div style="font-size:11px;color:#666D80;margin-top:8px;">Selamat! Anda telah diterima sebagai asisten praktikum.</div>
                @endif
            </div>
        </div>

        @else
        {{-- Form daftar asisten praktikum --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Form Pendaftaran Asisten Praktikum</span>
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.store') }}"
                  enctype="multipart/form-data" style="padding:20px;display:flex;flex-direction:column;gap:14px;"
                  onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = 'Mengirim...';">
                @csrf
                <input type="hidden" name="praktikum_id" value="{{ $selectedPraktikum->id }}">

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">IPK <span style="color:#DF1C41;">*</span></label>
                    <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50"
                           class="mp-input" style="width:100%;">
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Motivasi</label>
                    <textarea name="motivasi" rows="4" placeholder="Ceritakan pengalaman & motivasi Anda menjadi asisten..."
                              class="mp-input" style="width:100%;resize:none;"></textarea>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Transkrip <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;">(PDF)</span></label>
                    <input type="file" name="transkrip" accept=".pdf" required class="mp-input" style="width:100%;">
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Keanggotaan CERC <span style="font-weight:400;color:#666D80;">(Opsional, .pdf, .jpg, .png, .csv, .xlsx)</span></label>
                    <input type="file" name="berkas_cerc" accept=".pdf,.jpg,.jpeg,.png,.csv,.xlsx" class="mp-input" style="width:100%;">
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:8px;">Jadwal Ketersediaan</label>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="checkbox" name="jadwal[]" value="{{ $hari }}" class="accent-[#0B266E]">
                            <span style="font-size:12px;color:#353849;">{{ $hari }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="mp-btn primary md" style="margin-top:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Kirim Pendaftaran
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- ====== KOORDINATOR ====== --}}
    <div>
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Koordinator Praktikum</span>
            <span class="sec-rule"></span>
            @if($pKoor)
            <span class="mp-badge navy sm"><span class="dot"></span>Terbuka</span>
            @else
            <span class="mp-badge neutral sm">Tutup</span>
            @endif
        </div>

        @if(!$pKoor)
        <div class="mp-card flex-shrink-0">
            <div style="padding:32px;text-align:center;">
                <div style="font-size:13px;color:#666D80;">Pendaftaran koordinator belum dibuka untuk praktikum ini.</div>
            </div>
        </div>

        @elseif($isKoorDiPraktikumIni)
        <div class="mp-card flex-shrink-0">
            <div style="padding:20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span style="font-weight:600;color:#0B266E;">Anda sudah aktif sebagai Koordinator</span>
                </div>
                <div style="font-size:12px;color:#666D80;margin-top:4px;">Anda terdaftar dan aktif sebagai koordinator di praktikum ini.</div>
            </div>
        </div>

        @elseif($existingKoor && in_array($existingKoor->status, ['pending','approved']))
        <div class="mp-card flex-shrink-0">
            <div style="padding:20px;">
                <div style="font-size:12px;color:#666D80;margin-bottom:8px;font-weight:600;">{{ $pKoor->nama }}</div>
                @if($existingKoor->status === 'pending')
                @php
                    $reviewStatus = $existingKoor->status_dosen === 'disetujui' 
                        ? 'Disetujui dosen, menunggu approval admin' 
                        : 'Menunggu review dosen';
                @endphp
                <span class="mp-badge warning sm"><span class="dot"></span>{{ $reviewStatus }}</span>
                <div style="font-size:11px;color:#666D80;margin-top:8px;">Pendaftaran Anda sedang dalam proses review.</div>
                @else
                <span class="mp-badge navy sm"><span class="dot"></span>Diterima!</span>
                <div style="font-size:11px;color:#666D80;margin-top:8px;">Selamat! Anda telah diterima sebagai koordinator praktikum.</div>
                @endif
            </div>
        </div>

        @else
        {{-- Form daftar koordinator --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Form Pendaftaran Koordinator</span>
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-koor.store') }}"
                  enctype="multipart/form-data" style="padding:20px;display:flex;flex-direction:column;gap:14px;"
                  onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = 'Mengirim...';">
                @csrf
                <input type="hidden" name="praktikum_id" value="{{ $selectedPraktikum->id }}">

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">IPK <span style="color:#DF1C41;">*</span></label>
                    <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.60"
                           class="mp-input" style="width:100%;">
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Motivasi & Visi</label>
                    <textarea name="motivasi" rows="4" placeholder="Jelaskan visi Anda sebagai koordinator praktikum..."
                              class="mp-input" style="width:100%;resize:none;"></textarea>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Transkrip <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;">(PDF)</span></label>
                    <input type="file" name="transkrip" accept=".pdf" required class="mp-input" style="width:100%;">
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Keanggotaan CERC <span style="font-weight:400;color:#666D80;">(Opsional, .pdf, .jpg, .png, .csv, .xlsx)</span></label>
                    <input type="file" name="berkas_cerc" accept=".pdf,.jpg,.jpeg,.png,.csv,.xlsx" class="mp-input" style="width:100%;">
                </div>

                <button type="submit" class="mp-btn primary md" style="margin-top:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Kirim Pendaftaran
                </button>
            </form>
        </div>
        @endif
    </div>

</div>

@endif {{-- end if selectedPraktikum --}}

@endif {{-- end if praktikumDenganPeriode not empty --}}

</x-eoffice::manajemen-praktikum.layout>