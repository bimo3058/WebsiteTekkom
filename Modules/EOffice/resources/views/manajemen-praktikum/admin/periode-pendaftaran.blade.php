<x-eoffice::manajemen-praktikum.layout pageTitle="Periode Pendaftaran">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Buka / tutup periode pendaftaran koor, asprak &amp; praktikan per mata kuliah · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- ── FORM BUKA PERIODE ──────────────────────────────────────────────────── --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Periode Baru</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Buka Periode Pendaftaran Baru</span>
    </div>
    <div style="padding:24px;">

        {{-- Step 1: Pilih Mata Kuliah --}}
        <form method="GET" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.index') }}" id="form-matkul">
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                1. Pilih Mata Kuliah Praktikum <span style="color:#DF1C41;">*</span>
            </label>
            <select name="matkul_id" onchange="document.getElementById('form-matkul').submit()"
                    class="mp-input mp-select w-full">
                <option value="">— Pilih Mata Kuliah Praktikum —</option>
                @foreach($matkulList->groupBy('semester') as $sem => $items)
                <optgroup label="Semester {{ $sem }}">
                    @foreach($items as $mk)
                    <option value="{{ $mk->id }}" {{ $matkulId == $mk->id ? 'selected' : '' }}>
                        [{{ $mk->kode }}] {{ $mk->nama }} ({{ $mk->sks }} SKS)
                    </option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>
            @if($matkulList->isEmpty())
            <p style="margin-top:4px;font-size:11px;color:#DF1C41;">
                Belum ada data matkul. Jalankan:
                <code style="background:#F6F8FA;padding:0 4px;border-radius:4px;">php artisan db:seed --class=MatkulPraktikumSeeder</code>
            </p>
            @endif
        </form>

        @if($matkulDipilih)

        {{-- Info matkul terpilih --}}
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#EEF2FF;">
            <span style="font-family:monospace;font-size:12px;font-weight:700;color:#0B266E;background:white;padding:2px 8px;border-radius:4px;border:1px solid #C7D2FE;">
                {{ $matkulDipilih->kode }}
            </span>
            <div style="flex:1;font-size:13px;font-weight:600;color:#0D0D12;">{{ $matkulDipilih->nama }}</div>
            <span style="font-size:11px;color:#666D80;">Sem {{ $matkulDipilih->semester }} &middot; {{ $matkulDipilih->sks }} SKS</span>
        </div>

        @if($praktikumLinked->isEmpty())

        <div class="mp-alert warning flex-shrink-0" style="margin-top:16px;">
            <div style="font-size:13px;font-weight:600;color:#7C5309;margin-bottom:4px;">
                Belum ada kelas praktikum yang aktif untuk mata kuliah ini
            </div>
            <div style="font-size:12px;color:#956321;margin-bottom:12px;">
                Anda tidak dapat membuka periode pendaftaran karena belum ada kelas praktikum yang terbentuk untuk mata kuliah ini di semester berjalan. Silakan buat kelas praktikum baru terlebih dahulu.
            </div>
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn primary md" style="text-decoration:none;display:inline-flex;">
                Buat Praktikum Baru &rarr;
            </a>
        </div>

        @else

        {{-- Ada praktikum yang terhubung → tampilkan form buka periode --}}
        <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.store') }}"
              style="margin-top:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf
            <input type="hidden" name="matkul_id_filter" value="{{ $matkulDipilih->id }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        2. Pilih Praktikum <span style="color:#DF1C41;">*</span>
                    </label>
                    <select name="praktikum_id" required class="mp-input mp-select w-full">
                        <option value="">— Pilih Praktikum —</option>
                        @foreach($praktikumLinked as $p)
                        <option value="{{ $p->id }}" {{ $praktikumId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                            @if($p->kode) [{{ $p->kode }}] @endif
                            &middot; {{ $p->semester }} {{ $p->tahun_ajaran }}
                            @if($p->dosen) &middot; {{ $p->dosen->name }} @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        3. Jenis Pendaftaran <span style="color:#DF1C41;">*</span>
                    </label>
                    <select name="jenis" required class="mp-input mp-select w-full">
                        <option value="koor">Koordinator Praktikum</option>
                        <option value="asprak">Asisten Praktikum</option>

                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Nama Periode <span style="color:#808897;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="text" name="nama" placeholder="cth: Koor Ganjil 2025/2026" class="mp-input w-full">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Dibuka Pada <span style="color:#DF1C41;">*</span></label>
                        <input type="datetime-local" name="dibuka_pada" required class="mp-input w-full">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Ditutup Pada <span style="color:#DF1C41;">*</span></label>
                        <input type="datetime-local" name="ditutup_pada" required class="mp-input w-full">
                    </div>
                </div>
            </div>

            <div class="mp-alert warning flex-shrink-0">
                <strong>Info:</strong> Setelah dibuka, notifikasi dikirim ke <strong>seluruh pengguna</strong> aktif di sistem.
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="mp-btn primary md">Buka Periode &amp; Kirim Notifikasi</button>
            </div>
        </form>

        @endif {{-- end $praktikumLinked->isEmpty() --}}

        @else
        {{-- Belum pilih matkul --}}
        <div style="margin-top:16px;border-radius:10px;padding:32px;text-align:center;background:#F6F8FA;border:1px dashed #DFE1E7;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <div style="font-size:13px;color:#808897;">Pilih mata kuliah praktikum di atas untuk melanjutkan.</div>
        </div>
        @endif {{-- end $matkulDipilih --}}

    </div>
</div>

{{-- ── RIWAYAT PERIODE ──────────────────────────────────────────────────── --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode</span>
    <span class="sec-rule"></span>
    <form method="GET" style="display:flex;gap:8px;align-items:center;">
        @if($matkulId)<input type="hidden" name="matkul_id" value="{{ $matkulId }}">@endif
        <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select">
            <option value="">Semua Praktikum</option>
            @foreach($praktikumLinked as $p)
            <option value="{{ $p->id }}" {{ $praktikumId == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Riwayat Periode Pendaftaran</span>
    </div>
    @forelse($periodeList as $periode)
    @php $sedangBuka = $periode->isSedangBuka(); @endphp
    <div class="mp-tr" style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid #DFE1E7;"
         onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
         onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap;">
                <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $periode->nama }}</div>
                @if($periode->is_aktif && $sedangBuka)
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                @elseif($periode->is_aktif && !$sedangBuka)
                <span class="mp-badge warning sm"><span class="dot"></span>Terjadwal</span>
                @else
                <span class="mp-badge neutral sm">Ditutup</span>
                @endif
                @if($periode->jenis === 'koor')
                <span class="mp-badge navy sm">Koor</span>
                @elseif($periode->jenis === 'asprak')
                <span class="mp-badge success sm">Asprak</span>
                @else
                <span class="mp-badge sky sm">Praktikan</span>
                @endif
            </div>
            <div style="font-size:12px;color:#666D80;">
                Praktikum: <span style="font-weight:500;color:#353849;">{{ $periode->praktikum?->nama ?? '—' }}</span>
                @if($periode->praktikum?->matkul)
                &middot; <span style="font-family:monospace;font-weight:700;color:#0B266E;">{{ $periode->praktikum->matkul->kode }}</span>
                @endif
                @if($periode->dibuka_pada) &middot; Buka: <span style="font-weight:500;color:#0D0D12;">{{ $periode->dibuka_pada->format('d M Y H:i') }}</span> @endif
                @if($periode->ditutup_pada) &middot; Tutup: <span style="font-weight:500;color:#0D0D12;">{{ $periode->ditutup_pada->format('d M Y H:i') }}</span> @endif
            </div>
            <div style="font-size:11px;color:#808897;margin-top:2px;">
                Dibuka oleh: {{ $periode->dibukaOleh?->name ?? '—' }} &middot; {{ $periode->created_at?->format('d M Y') }}
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <a href="{{ route('eoffice.manprak.admin.periode-pendaftaran.edit', $periode->id) }}"
               class="mp-btn secondary sm" style="text-decoration:none;">Edit</a>
            @if($periode->is_aktif)
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.tutup', $periode->id) }}">
                @csrf
                <button type="submit" class="mp-btn ghost sm">Tutup</button>
            </form>
            @endif
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.destroy', $periode->id) }}"
                  onsubmit="return confirm('Hapus periode ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada periode pendaftaran.</div>
    </div>
    @endforelse
</div>

{{-- ── RIWAYAT PENGUMUMAN SISTEM ─────────────────────────────────────────── --}}
@if($riwayatPengumuman->isNotEmpty())
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Pengumuman Sistem</span>
    <span class="sec-rule"></span>
    <span style="font-size:12px;color:#808897;">Dibuat otomatis saat periode dibuka/ditutup</span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Riwayat Pengumuman Otomatis</span>
    </div>
    @foreach($riwayatPengumuman as $pg)
    @php $dihapus = !is_null($pg->deleted_at); @endphp
    <div class="mp-tr" style="display:flex;align-items:flex-start;gap:16px;padding:16px 20px;border-bottom:1px solid #DFE1E7;{{ $dihapus ? 'opacity:0.55;' : '' }}">
        <div class="mp-stat-icon {{ $pg->tipe_sistem === 'buka' ? 'green' : 'red' }}" style="flex-shrink:0;">
            @if($pg->tipe_sistem === 'buka')
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            @else
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            @endif
        </div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $pg->judul }}</span>
                @if($pg->tipe_sistem === 'buka')
                <span class="mp-badge success sm"><span class="dot"></span>Dibuka</span>
                @else
                <span class="mp-badge error sm"><span class="dot"></span>Ditutup</span>
                @endif
                @if($dihapus)
                <span class="mp-badge neutral sm">Disembunyikan dari publik</span>
                @endif
            </div>
            <div style="font-size:12px;color:#666D80;">
                Praktikum: <span style="font-weight:500;color:#353849;">{{ $pg->praktikum?->nama ?? '—' }}</span>
                &middot; Dibuat: <span style="font-weight:500;">{{ $pg->created_at?->format('d M Y, H:i') }}</span>
                @if($dihapus)
                &middot; Disembunyikan: <span style="font-weight:500;color:#DF1C41;">{{ $pg->deleted_at->format('d M Y, H:i') }}</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
