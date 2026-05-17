<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asprak & Koordinator">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pendaftaran Asprak &amp; Koordinator</h1>
        <p class="mp-page-sub">Daftarkan diri sebagai calon asisten atau koordinator praktikum</p>
    </div>
</div>

{{-- Badge role aktif --}}
@if($sudahJadiAsprak || $sudahJadiKoor)
<div class="flex gap-2 flex-wrap flex-shrink-0">
    @if($sudahJadiAsprak)
    <div class="flex items-center gap-2 mp-badge success sm" style="padding:8px 14px;border-radius:10px;font-size:12px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        Anda aktif sebagai Asisten Praktikum
    </div>
    @endif
    @if($sudahJadiKoor)
    <div class="flex items-center gap-2 mp-badge primary sm" style="padding:8px 14px;border-radius:10px;font-size:12px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        Anda aktif sebagai Koordinator Praktikum
    </div>
    @endif
</div>
@endif

{{-- Tidak ada periode terbuka sama sekali --}}
@if($praktikumDenganPeriode->isEmpty())
<div class="mp-card flex-shrink-0" style="padding:56px;text-align:center;">
    <svg class="mx-auto mb-3 w-10 h-10" style="color:var(--c-border);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </svg>
    <div style="font-size:14px;font-weight:600;color:var(--c-fg);">Belum ada pendaftaran yang dibuka</div>
    <div style="font-size:12px;color:var(--c-fg-muted);margin-top:4px;">Admin belum membuka periode pendaftaran asprak atau koordinator saat ini.</div>
    <div style="font-size:12px;color:var(--c-fg-placeholder);margin-top:8px;">Pantau terus halaman ini atau tunggu notifikasi dari sistem.</div>
</div>

@else

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

    {{-- Header --}}
    <div class="flex items-center gap-3 px-5 py-[10px]" style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
        <div class="flex-1 min-w-0">
            <div style="font-weight:700;font-size:13px;color:var(--c-fg);" class="truncate">{{ $p->nama }}</div>
            <div style="font-size:11px;color:var(--c-fg-muted);">
                {{ $p->matkul?->kode ? '[' . $p->matkul->kode . '] · ' : '' }}{{ $p->semester }} {{ $p->tahun_ajaran }}
                @if($p->dosen) · {{ $p->dosen->name }} @endif
            </div>
        </div>
        @if($sudahTerdaftar)
        <span class="mp-badge success sm flex-shrink-0">Terdaftar sebagai praktikan</span>
        @else
        <span class="mp-badge warning sm flex-shrink-0">Belum ikut praktikum ini</span>
        @endif
    </div>

    {{-- Dua kolom: Asprak | Koor --}}
    <div class="grid grid-cols-2 divide-x" style="border-color:var(--c-border-light);">

        {{-- ASPRAK --}}
        <div>
            <div class="flex items-center justify-between px-4 py-2" style="border-bottom:1px solid var(--c-border-light);background:{{ $pAsprak ? '#F0FDF9' : '#FAFBFC' }};">
                <span style="font-size:12px;font-weight:700;color:{{ $pAsprak ? '#0D9488' : 'var(--c-fg-muted)' }};">Asisten Praktikum</span>
                @if($pAsprak)
                <span class="mp-badge success sm">TERBUKA</span>
                @else
                <span class="mp-badge neutral sm">TUTUP</span>
                @endif
            </div>

            <div style="padding:16px;">
            @if(!$pAsprak)
                <div style="padding:16px 0;text-align:center;font-size:12px;color:var(--c-fg-placeholder);">Pendaftaran belum dibuka.</div>

            @elseif($sudahJadiAsprak)
                <div style="padding:8px 0;font-size:12px;color:#40C4AA;font-weight:600;">Anda sudah aktif sebagai Asprak</div>

            @elseif($existingAsprak && in_array($existingAsprak->status, ['pending','approved']))
                <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:8px;font-weight:600;">{{ $pAsprak->nama }}</div>
                <div class="flex items-center gap-2">
                    @if($existingAsprak->status === 'pending')
                    <span class="mp-badge warning sm">Menunggu seleksi koordinator</span>
                    @else
                    <span class="mp-badge success sm">Diterima!</span>
                    @endif
                </div>

            @else
                @if($pAsprak->ditutup_pada)
                <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:12px;">
                    <span style="font-weight:600;">{{ $pAsprak->nama }}</span>
                    · Ditutup <span style="font-weight:600;color:#DF1C41;">{{ $pAsprak->ditutup_pada->format('d M Y H:i') }}</span>
                    <span style="color:var(--c-fg-muted);">({{ $pAsprak->ditutup_pada->diffForHumans() }})</span>
                </div>
                @endif

                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.store') }}"
                      enctype="multipart/form-data" class="flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">IPK <span class="text-red-500">*</span></label>
                        <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50" class="mp-input w-full" style="font-size:12px;padding:6px 8px;">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">Motivasi <span class="text-red-500">*</span></label>
                        <textarea name="motivasi" rows="3" required minlength="20"
                                  placeholder="Ceritakan pengalaman & motivasi Anda..."
                                  class="mp-input w-full" style="font-size:12px;padding:6px 8px;resize:none;"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">CV <span style="font-weight:400;color:var(--c-fg-muted);">(PDF/DOCX)</span></label>
                            <input type="file" name="cv" accept=".pdf,.docx" class="mp-input w-full" style="font-size:11px;padding:4px 6px;">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">Transkrip <span style="font-weight:400;color:var(--c-fg-muted);">(PDF)</span></label>
                            <input type="file" name="transkrip" accept=".pdf" class="mp-input w-full" style="font-size:11px;padding:4px 6px;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">Jadwal Ketersediaan</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" name="jadwal[]" value="{{ $hari }}" class="accent-[#40C4AA]">
                                <span style="font-size:11px;color:var(--c-fg-sub);">{{ $hari }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="mp-btn success md w-full">Kirim Pendaftaran Asprak</button>
                </form>
            @endif
            </div>
        </div>

        {{-- KOOR --}}
        <div>
            <div class="flex items-center justify-between px-4 py-2" style="border-bottom:1px solid var(--c-border-light);background:{{ $pKoor ? '#EEF2FF' : '#FAFBFC' }};">
                <span style="font-size:12px;font-weight:700;color:{{ $pKoor ? 'var(--c-primary)' : 'var(--c-fg-muted)' }};">Koordinator</span>
                @if($pKoor)
                <span class="mp-badge primary sm">TERBUKA</span>
                @else
                <span class="mp-badge neutral sm">TUTUP</span>
                @endif
            </div>

            <div style="padding:16px;">
            @if(!$pKoor)
                <div style="padding:16px 0;text-align:center;font-size:12px;color:var(--c-fg-placeholder);">Pendaftaran belum dibuka.</div>

            @elseif($sudahJadiKoor)
                <div style="padding:8px 0;font-size:12px;color:var(--c-primary);font-weight:600;">Anda sudah aktif sebagai Koordinator</div>

            @elseif($existingKoor && in_array($existingKoor->status, ['pending','approved']))
                <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:8px;font-weight:600;">{{ $pKoor->nama }}</div>
                <div class="flex items-center gap-2">
                    @if($existingKoor->status === 'pending')
                    <span class="mp-badge warning sm">{{ $existingKoor->status_dosen === 'disetujui' ? 'Disetujui dosen, menunggu Admin' : 'Menunggu review dosen' }}</span>
                    @else
                    <span class="mp-badge primary sm">Diterima sebagai Koordinator!</span>
                    @endif
                </div>

            @else
                @if($pKoor->ditutup_pada)
                <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:12px;">
                    <span style="font-weight:600;">{{ $pKoor->nama }}</span>
                    · Ditutup <span style="font-weight:600;color:#DF1C41;">{{ $pKoor->ditutup_pada->format('d M Y H:i') }}</span>
                    <span style="color:var(--c-fg-muted);">({{ $pKoor->ditutup_pada->diffForHumans() }})</span>
                </div>
                @endif

                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-koor.store') }}"
                      enctype="multipart/form-data" class="flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">IPK <span class="text-red-500">*</span></label>
                        <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50" class="mp-input w-full" style="font-size:12px;padding:6px 8px;">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">Motivasi <span class="text-red-500">*</span></label>
                        <textarea name="motivasi" rows="3" required minlength="20"
                                  placeholder="Ceritakan motivasi Anda menjadi koordinator..."
                                  class="mp-input w-full" style="font-size:12px;padding:6px 8px;resize:none;"></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">Transkrip <span style="font-weight:400;color:var(--c-fg-muted);">(PDF, opsional)</span></label>
                        <input type="file" name="transkrip" accept=".pdf" class="mp-input w-full" style="font-size:11px;padding:4px 6px;">
                    </div>
                    <button type="submit" class="mp-btn primary md w-full">Kirim Pendaftaran Koordinator</button>
                </form>
            @endif
            </div>
        </div>

    </div>{{-- end grid --}}
</div>
@endforeach

{{-- Daftar yang sudah lolos --}}
@if($asprakLolos->isNotEmpty() || $koorLolos->isNotEmpty())
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header" style="background:#FAFBFC;">
        <span class="mp-card-title">Asisten &amp; Koordinator Terpilih</span>
    </div>
    <div class="grid grid-cols-2 divide-x" style="border-color:var(--c-border-light);">
        <div style="padding:16px;">
            <div style="font-size:11px;font-weight:700;color:#40C4AA;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Asisten Praktikum</div>
            @forelse($asprakLolos as $a)
            <div class="flex items-center gap-2" style="padding:6px 0;border-bottom:1px solid var(--c-border-light);">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                     style="background:#DDF2EE;color:#174E43;">
                    {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:12px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $a->user?->name ?? '—' }}</div>
                    <div style="font-size:10px;color:var(--c-fg-muted);">{{ $a->praktikum?->nama }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:12px;color:var(--c-fg-muted);padding:12px 0;">Belum ada.</div>
            @endforelse
        </div>
        <div style="padding:16px;">
            <div style="font-size:11px;font-weight:700;color:var(--c-primary);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Koordinator</div>
            @forelse($koorLolos as $k)
            <div class="flex items-center gap-2" style="padding:6px 0;border-bottom:1px solid var(--c-border-light);">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                     style="background:var(--c-bg-sub);color:var(--c-primary);">
                    {{ strtoupper(substr($k->user?->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:12px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $k->user?->name ?? '—' }}</div>
                    <div style="font-size:10px;color:var(--c-fg-muted);">{{ $k->praktikum?->nama }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:12px;color:var(--c-fg-muted);padding:12px 0;">Belum ada.</div>
            @endforelse
        </div>
    </div>
</div>
@endif

@endif {{-- end $praktikumDenganPeriode->isEmpty() --}}

{{-- Syarat --}}
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="font-weight:700;font-size:13px;color:var(--c-fg);margin-bottom:12px;">Syarat Umum</div>
    <div class="grid grid-cols-2 gap-2">
        @foreach(['IPK minimal 3.00','Pernah mengikuti praktikum terkait','Tidak sedang mengambil praktikum yang sama','Bersedia hadir sesuai jadwal','Aktif sebagai mahasiswa','Menyetujui peraturan asisten praktikum'] as $s)
        <div class="flex items-center gap-2">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#40C4AA" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:12px;color:var(--c-fg-sub);">{{ $s }}</span>
        </div>
        @endforeach
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
