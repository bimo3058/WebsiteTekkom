<x-eoffice::manajemen-praktikum.layout pageTitle="Periode Pendaftaran">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Periode Pendaftaran</h1>
        <p class="mp-page-sub">Buka / tutup periode pendaftaran koor, asprak & praktikan per mata kuliah praktikum</p>
    </div>
</div>

{{-- ── FORM BUKA PERIODE ───────────────────────────────────────────────── --}}
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Buka Periode Pendaftaran Baru</span>
    </div>
    <div class="mp-card-body" style="padding:24px;">

        {{-- Step 1: Pilih Mata Kuliah --}}
        <form method="GET" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.index') }}" id="form-matkul">
            <label class="block text-[12px] font-semibold text-[#353849] mb-1">
                1. Pilih Mata Kuliah Praktikum <span class="text-red-500">*</span>
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
                ⚠ Belum ada data matkul. Jalankan:
                <code style="background:#F6F8FA;padding:0 4px;border-radius:4px;">php artisan db:seed --class=MatkulPraktikumSeeder</code>
            </p>
            @endif
        </form>

        @if($matkulDipilih)

        {{-- Info matkul terpilih --}}
        <div class="flex items-center gap-3 mt-4 px-4 py-3 rounded-[10px]" style="background:#EEF2FF;">
            <span style="font-family:monospace;font-size:12px;font-weight:700;color:#0B266E;background:white;padding:2px 8px;border-radius:4px;border:1px solid #C7D2FE;">
                {{ $matkulDipilih->kode }}
            </span>
            <div style="flex:1;font-size:13px;font-weight:600;color:var(--c-fg);">{{ $matkulDipilih->nama }}</div>
            <span style="font-size:11px;color:var(--c-fg-muted);">Sem {{ $matkulDipilih->semester }} · {{ $matkulDipilih->sks }} SKS</span>
        </div>

        @if($praktikumLinked->isEmpty())

        <div class="mt-4 px-4 py-4 rounded-[10px]" style="background:#FEF9EC;border:1px solid #D39C3D;">
            <div style="font-size:13px;font-weight:600;color:#7C5309;margin-bottom:4px;">
                ⚠ Belum ada praktikum yang terhubung ke mata kuliah ini
            </div>
            <div style="font-size:12px;color:#956321;margin-bottom:12px;">
                Hubungkan salah satu praktikum aktif ke mata kuliah ini, atau
                <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="underline font-semibold">buat praktikum baru</a>.
            </div>

            @if($praktikumSemua->isNotEmpty())
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.assign-matkul') }}"
                  class="flex gap-2 flex-wrap items-end">
                @csrf
                <input type="hidden" name="matkul_id" value="{{ $matkulDipilih->id }}">
                <div class="flex-1 min-w-[240px]">
                    <label style="display:block;font-size:11px;font-weight:600;color:#7C5309;margin-bottom:4px;">Pilih praktikum yang ingin dihubungkan:</label>
                    <select name="praktikum_id" required class="mp-input mp-select w-full" style="border-color:#D39C3D;">
                        <option value="">— Pilih Praktikum —</option>
                        @foreach($praktikumSemua as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->nama }}
                            @if($p->kode) [{{ $p->kode }}] @endif
                            · {{ $p->semester }} {{ $p->tahun_ajaran }}
                            @if($p->matkul) · ({{ $p->matkul->kode }}) @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="mp-btn primary md">Hubungkan & Lanjutkan</button>
            </form>
            @else
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn primary md" style="text-decoration:none;display:inline-flex;">
                Buat Praktikum Baru →
            </a>
            @endif
        </div>

        @else

        {{-- Ada praktikum yang terhubung → tampilkan form buka periode --}}
        <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.store') }}"
              class="mt-5 flex flex-col gap-4">
            @csrf
            <input type="hidden" name="matkul_id_filter" value="{{ $matkulDipilih->id }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">
                        2. Pilih Praktikum <span class="text-red-500">*</span>
                    </label>
                    <select name="praktikum_id" required class="mp-input mp-select w-full">
                        <option value="">— Pilih Praktikum —</option>
                        @foreach($praktikumLinked as $p)
                        <option value="{{ $p->id }}" {{ $praktikumId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                            @if($p->kode) [{{ $p->kode }}] @endif
                            · {{ $p->semester }} {{ $p->tahun_ajaran }}
                            @if($p->dosen) · {{ $p->dosen->name }} @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">
                        3. Jenis Pendaftaran <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis" required class="mp-input mp-select w-full">
                        <option value="koor">Koordinator Praktikum</option>
                        <option value="asprak">Asisten Praktikum</option>
                        <option value="praktikan">Praktikan (IRS)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">
                        Nama Periode <span style="color:var(--c-fg-placeholder);font-weight:400;">(opsional)</span>
                    </label>
                    <input type="text" name="nama" placeholder="cth: Koor Ganjil 2025/2026" class="mp-input w-full">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dibuka Pada</label>
                        <input type="datetime-local" name="dibuka_pada" class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Ditutup Pada</label>
                        <input type="datetime-local" name="ditutup_pada" class="mp-input w-full">
                    </div>
                </div>
            </div>

            <div class="mp-alert info">
                <strong>ℹ️ Info:</strong> Setelah dibuka, notifikasi dikirim ke <strong>seluruh pengguna</strong> aktif di sistem.
            </div>

            <div class="flex justify-end">
                <button type="submit" class="mp-btn primary md">Buka Periode & Kirim Notifikasi</button>
            </div>
        </form>

        @endif {{-- end $praktikumLinked->isEmpty() --}}

        @else
        {{-- Belum pilih matkul --}}
        <div class="mt-4 rounded-[10px] py-8 text-center" style="background:#F6F8FA;border:1px dashed var(--c-border);font-size:13px;color:var(--c-fg-placeholder);">
            Pilih mata kuliah praktikum di atas untuk melanjutkan.
        </div>
        @endif {{-- end $matkulDipilih --}}

    </div>
</div>

{{-- ── RIWAYAT PERIODE ─────────────────────────────────────────────────── --}}
<div class="flex items-center gap-3 flex-wrap flex-shrink-0">
    <div style="font-size:13px;font-weight:600;color:var(--c-fg-sub);">Riwayat Periode</div>
    <form method="GET" class="flex gap-2 items-center">
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
    <div class="mp-tr flex items-center gap-4" style="padding:16px 20px;border-bottom:1px solid var(--c-border-light);">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <div style="font-size:14px;font-weight:700;color:var(--c-fg);">{{ $periode->nama }}</div>
                @if($periode->is_aktif && $sedangBuka)
                <span class="mp-badge success sm">● AKTIF</span>
                @elseif($periode->is_aktif && !$sedangBuka)
                <span class="mp-badge warning sm">TERJADWAL</span>
                @else
                <span class="mp-badge neutral sm">DITUTUP</span>
                @endif
                @if($periode->jenis === 'koor')
                <span class="mp-badge primary sm">KOOR</span>
                @elseif($periode->jenis === 'asprak')
                <span class="mp-badge success sm">ASPRAK</span>
                @else
                <span class="mp-badge sky sm">PRAKTIKAN</span>
                @endif
            </div>
            <div style="font-size:12px;color:var(--c-fg-muted);">
                Praktikum: <span style="font-weight:500;color:var(--c-fg-sub);">{{ $periode->praktikum?->nama ?? '—' }}</span>
                @if($periode->praktikum?->matkul)
                · <span class="mp-badge primary sm" style="font-family:monospace;">{{ $periode->praktikum->matkul->kode }}</span>
                @endif
                @if($periode->dibuka_pada) · Buka: <span style="font-weight:500;">{{ $periode->dibuka_pada->format('d M Y H:i') }}</span> @endif
                @if($periode->ditutup_pada) · Tutup: <span style="font-weight:500;">{{ $periode->ditutup_pada->format('d M Y H:i') }}</span> @endif
            </div>
            <div style="font-size:11px;color:var(--c-fg-placeholder);margin-top:2px;">
                Dibuka oleh: {{ $periode->dibukaOleh?->name ?? '—' }} · {{ $periode->created_at?->format('d M Y') }}
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('eoffice.manprak.admin.periode-pendaftaran.edit', $periode->id) }}"
               class="mp-btn secondary sm" style="text-decoration:none;">Edit</a>
            @if($periode->is_aktif)
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.tutup', $periode->id) }}">
                @csrf
                <button type="submit" class="mp-btn warning sm">Tutup</button>
            </form>
            @endif
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.destroy', $periode->id) }}"
                  onsubmit="return confirm('Hapus periode ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="mp-btn destructive sm">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada periode pendaftaran.</div>
    @endforelse
</div>

{{-- ── RIWAYAT PENGUMUMAN SISTEM ──────────────────────────────────────── --}}
@if($riwayatPengumuman->isNotEmpty())
<div class="flex-shrink-0">
    <div style="font-size:13px;font-weight:600;color:var(--c-fg-sub);">Riwayat Pengumuman Sistem Otomatis</div>
    <div style="font-size:11px;color:var(--c-fg-placeholder);">Pengumuman yang dibuat otomatis saat periode dibuka/ditutup, termasuk yang sudah dihapus dari tampilan publik.</div>
</div>

<div class="mp-card flex-shrink-0">
    @foreach($riwayatPengumuman as $pg)
    @php $dihapus = !is_null($pg->deleted_at); @endphp
    <div class="flex items-start gap-4 mp-tr" style="padding:16px 20px;border-bottom:1px solid var(--c-border-light);{{ $dihapus ? 'opacity:0.6;' : '' }}">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span style="font-size:13px;font-weight:600;color:var(--c-fg);">{{ $pg->judul }}</span>
                @if($pg->tipe_sistem === 'buka')
                <span class="mp-badge success sm">BUKA</span>
                @else
                <span class="mp-badge error sm">TUTUP</span>
                @endif
                @if($dihapus)
                <span class="mp-badge neutral sm">Disembunyikan dari publik</span>
                @endif
            </div>
            <div style="font-size:12px;color:var(--c-fg-muted);">
                Praktikum: <span style="font-weight:500;color:var(--c-fg-sub);">{{ $pg->praktikum?->nama ?? '—' }}</span>
                · Dibuat: <span style="font-weight:500;">{{ $pg->created_at?->format('d M Y, H:i') }}</span>
                @if($dihapus)
                · Disembunyikan: <span style="font-weight:500;color:#DF1C41;">{{ $pg->deleted_at->format('d M Y, H:i') }}</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
