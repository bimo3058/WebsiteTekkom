<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asprak & Koordinator">

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Asprak &amp; Koordinator</h1>
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

<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#0D0D12;">Belum ada pendaftaran yang dibuka</div>
        <div style="font-size:12px;color:#666D80;margin-top:4px;">Admin belum membuka periode pendaftaran asprak atau koordinator saat ini.</div>
        <div style="font-size:12px;color:#808897;margin-top:8px;">Pantau terus halaman ini atau tunggu notifikasi dari sistem.</div>
    </div>
</div>

@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Periode Pendaftaran Aktif</span>
    <span class="sec-rule"></span>
</div>

{{-- Card per praktikum yang punya periode terbuka --}}
@foreach($praktikumDenganPeriode as $p)
@php
    $pAsprak = $periodeAktif[$p->id]['asprak'] ?? null;
    $pKoor   = $periodeAktif[$p->id]['koor']   ?? null;

    // Cek apakah user sudah terdaftar sebagai praktikan di praktikum ini
    $sudahTerdaftar = in_array($p->id, $praktikumDiikuti);

    // Cek pendaftaran yang sudah ada
    $existingAsprak = \Modules\EOffice\Models\PendaftaranAsprak::where('user_id', auth()->id())
        ->where('praktikum_id', $p->id)
        ->orderByDesc('created_at')
        ->first();
    $existingKoor = \Modules\EOffice\Models\PendaftaranKoordinator::where('user_id', auth()->id())
        ->where('praktikum_id', $p->id)
        ->orderByDesc('created_at')
        ->first();
@endphp

<div class="mp-card flex-shrink-0">

    {{-- Card Header --}}
    <div class="mp-card-header" style="background:#F9FAFB;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:13px;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->nama }}</div>
            <div style="font-size:11px;color:#666D80;margin-top:2px;">
                {{ $p->matkul?->kode ? '[' . $p->matkul->kode . '] · ' : '' }}{{ $p->semester }} {{ $p->tahun_ajaran }}
                @if($p->dosen) · {{ $p->dosen->name }} @endif
            </div>
        </div>
        <div class="right">
            @if($sudahTerdaftar)
            <span class="mp-badge success sm"><span class="dot"></span>Terdaftar sebagai praktikan</span>
            @else
            <span class="mp-badge warning sm"><span class="dot"></span>Belum ikut praktikum ini</span>
            @endif
        </div>
    </div>

    {{-- Dua kolom: Asprak | Koor --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;border-top:1px solid #DFE1E7;">

        {{-- ASPRAK --}}
        <div style="border-right:1px solid #DFE1E7;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 16px;border-bottom:1px solid #DFE1E7;background:{{ $pAsprak ? '#F0FDF9' : '#F9FAFB' }};">
                <span style="font-size:12px;font-weight:700;color:{{ $pAsprak ? '#0D9488' : '#666D80' }};">Asisten Praktikum</span>
                @if($pAsprak)
                <span class="mp-badge success sm"><span class="dot"></span>Terbuka</span>
                @else
                <span class="mp-badge neutral sm">Tutup</span>
                @endif
            </div>

            <div style="padding:16px;">
            @if(!$pAsprak)
                <div style="padding:16px 0;text-align:center;font-size:12px;color:#808897;">Pendaftaran belum dibuka.</div>

            @elseif($sudahJadiAsprak)
                <div style="padding:8px 0;font-size:12px;color:#0D9488;font-weight:600;">Anda sudah aktif sebagai Asprak</div>

            @elseif($existingAsprak && in_array($existingAsprak->status, ['pending','approved']))
                <div style="font-size:11px;color:#666D80;margin-bottom:8px;font-weight:600;">{{ $pAsprak->nama }}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($existingAsprak->status === 'pending')
                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu seleksi koordinator</span>
                    @else
                    <span class="mp-badge success sm"><span class="dot"></span>Diterima!</span>
                    @endif
                </div>

            @else
                @if($pAsprak->ditutup_pada)
                <div style="font-size:11px;color:#666D80;margin-bottom:12px;">
                    <span style="font-weight:600;">{{ $pAsprak->nama }}</span>
                    · Ditutup <span style="font-weight:600;color:#DF1C41;">{{ $pAsprak->ditutup_pada->format('d M Y H:i') }}</span>
                    <span style="color:#666D80;">({{ $pAsprak->ditutup_pada->diffForHumans() }})</span>
                </div>
                @endif

                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.store') }}"
                      enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:12px;">
                    @csrf
                    <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">IPK <span style="color:#DF1C41;">*</span></label>
                        <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50" class="mp-input" style="width:100%;font-size:12px;padding:6px 8px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">Motivasi <span style="color:#DF1C41;">*</span></label>
                        <textarea name="motivasi" rows="3" required minlength="20"
                                  placeholder="Ceritakan pengalaman & motivasi Anda..."
                                  class="mp-input" style="width:100%;font-size:12px;padding:6px 8px;resize:none;"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">CV <span style="font-weight:400;color:#666D80;">(PDF/DOCX)</span></label>
                            <input type="file" name="cv" accept=".pdf,.docx" class="mp-input" style="width:100%;font-size:11px;padding:4px 6px;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">Transkrip <span style="font-weight:400;color:#666D80;">(PDF)</span></label>
                            <input type="file" name="transkrip" accept=".pdf" class="mp-input" style="width:100%;font-size:11px;padding:4px 6px;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:6px;">Jadwal Ketersediaan</label>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                            <label style="display:flex;align-items:center;gap:4px;cursor:pointer;">
                                <input type="checkbox" name="jadwal[]" value="{{ $hari }}">
                                <span style="font-size:11px;color:#353849;">{{ $hari }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="mp-btn primary md" style="width:100%;">Kirim Pendaftaran Asprak</button>
                </form>
            @endif
            </div>
        </div>

        {{-- KOOR --}}
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 16px;border-bottom:1px solid #DFE1E7;background:{{ $pKoor ? '#EEF2FF' : '#F9FAFB' }};">
                <span style="font-size:12px;font-weight:700;color:{{ $pKoor ? '#0B266E' : '#666D80' }};">Koordinator</span>
                @if($pKoor)
                <span class="mp-badge navy sm"><span class="dot"></span>Terbuka</span>
                @else
                <span class="mp-badge neutral sm">Tutup</span>
                @endif
            </div>

            <div style="padding:16px;">
            @if(!$pKoor)
                <div style="padding:16px 0;text-align:center;font-size:12px;color:#808897;">Pendaftaran belum dibuka.</div>

            @elseif($sudahJadiKoor)
                <div style="padding:8px 0;font-size:12px;color:#0B266E;font-weight:600;">Anda sudah aktif sebagai Koordinator</div>

            @elseif($existingKoor && in_array($existingKoor->status, ['pending','approved']))
                <div style="font-size:11px;color:#666D80;margin-bottom:8px;font-weight:600;">{{ $pKoor->nama }}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($existingKoor->status === 'pending')
                    <span class="mp-badge warning sm"><span class="dot"></span>{{ $existingKoor->status_dosen === 'disetujui' ? 'Disetujui dosen, menunggu Admin' : 'Menunggu review dosen' }}</span>
                    @else
                    <span class="mp-badge navy sm"><span class="dot"></span>Diterima sebagai Koordinator!</span>
                    @endif
                </div>

            @else
                @if($pKoor->ditutup_pada)
                <div style="font-size:11px;color:#666D80;margin-bottom:12px;">
                    <span style="font-weight:600;">{{ $pKoor->nama }}</span>
                    · Ditutup <span style="font-weight:600;color:#DF1C41;">{{ $pKoor->ditutup_pada->format('d M Y H:i') }}</span>
                    <span style="color:#666D80;">({{ $pKoor->ditutup_pada->diffForHumans() }})</span>
                </div>
                @endif

                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-koor.store') }}"
                      enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:12px;">
                    @csrf
                    <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">IPK <span style="color:#DF1C41;">*</span></label>
                        <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50" class="mp-input" style="width:100%;font-size:12px;padding:6px 8px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">Motivasi <span style="color:#DF1C41;">*</span></label>
                        <textarea name="motivasi" rows="3" required minlength="20"
                                  placeholder="Ceritakan motivasi Anda menjadi koordinator..."
                                  class="mp-input" style="width:100%;font-size:12px;padding:6px 8px;resize:none;"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">Transkrip <span style="font-weight:400;color:#666D80;">(PDF, opsional)</span></label>
                        <input type="file" name="transkrip" accept=".pdf" class="mp-input" style="width:100%;font-size:11px;padding:4px 6px;">
                    </div>
                    <button type="submit" class="mp-btn primary md" style="width:100%;">Kirim Pendaftaran Koordinator</button>
                </form>
            @endif
            </div>
        </div>

    </div>{{-- end grid --}}
</div>
@endforeach

{{-- Daftar yang sudah lolos --}}
@if($asprakLolos->isNotEmpty() || $koorLolos->isNotEmpty())
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Asisten &amp; Koordinator Terpilih</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header" style="background:#F9FAFB;">
        <span class="mp-card-title">Asisten &amp; Koordinator Terpilih</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;border-top:1px solid #DFE1E7;">
        <div style="padding:16px;border-right:1px solid #DFE1E7;">
            <div style="font-size:11px;font-weight:700;color:#0D9488;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Asisten Praktikum</div>
            @forelse($asprakLolos as $a)
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #DFE1E7;">
                <div class="mp-av green" style="width:28px;height:28px;font-size:10px;flex-shrink:0;">
                    {{ strtoupper(substr($a->user?->name ?? '?', 0, 2)) }}
                </div>
                <div style="min-width:0;">
                    <div style="font-size:12px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $a->user?->name ?? '—' }}</div>
                    <div style="font-size:10px;color:#666D80;">{{ $a->praktikum?->nama }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:12px;color:#666D80;padding:12px 0;">Belum ada.</div>
            @endforelse
        </div>
        <div style="padding:16px;">
            <div style="font-size:11px;font-weight:700;color:#0B266E;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Koordinator</div>
            @forelse($koorLolos as $k)
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #DFE1E7;">
                <div class="mp-av violet" style="width:28px;height:28px;font-size:10px;flex-shrink:0;">
                    {{ strtoupper(substr($k->user?->name ?? '?', 0, 2)) }}
                </div>
                <div style="min-width:0;">
                    <div style="font-size:12px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $k->user?->name ?? '—' }}</div>
                    <div style="font-size:10px;color:#666D80;">{{ $k->praktikum?->nama }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:12px;color:#666D80;padding:12px 0;">Belum ada.</div>
            @endforelse
        </div>
    </div>
</div>
@endif

@endif {{-- end $praktikumDenganPeriode->isEmpty() --}}

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Syarat Umum</span>
    <span class="sec-rule"></span>
</div>

{{-- Syarat --}}
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach(['IPK minimal 3.00','Pernah mengikuti praktikum terkait','Tidak sedang mengambil praktikum yang sama','Bersedia hadir sesuai jadwal','Aktif sebagai mahasiswa','Menyetujui peraturan asisten praktikum'] as $s)
        <div style="display:flex;align-items:center;gap:8px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:12px;color:#353849;">{{ $s }}</span>
        </div>
        @endforeach
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
