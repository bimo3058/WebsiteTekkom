{{--
    Isi form Buat Pengaduan dalam "satu kotak", meniru Edit User SITKOM
    (resources/views/superadmin/users/edit.blade.php): bilah atas Kembali + judul,
    tiap bagian = kolom judul + keterangan di kiri, field di kanan, tombol Lanjut di kaki.

    Urutan bagian: Pelapor → Kategori → Detail Kejadian → Isi Pengaduan → Bukti Dukung.

    - Pelapor: satu form untuk semua; pelapor yang ingin anonim mencentang
      "Sembunyikan identitas saya" (is_anonim). Saat dicentang, kolom Nama & Angkatan
      disembunyikan seluruhnya. Teksnya menyebut konsekuensinya apa adanya.
    - Detail Kejadian: empat pertanyaan yang menyesuaikan kategori terpilih
      (sumber: Support\PengaduanPertanyaan). Tiap kategori punya <fieldset> sendiri;
      yang tidak aktif disembunyikan DAN di-disable sehingga isiannya tidak ikut terkirim.
    - Bukti Dukung: opsional di semua kategori. Punya bukti → unggah PDF; tidak punya →
      dikosongkan.

    Elemen <form>, @csrf, dan honeypot ada di halaman pemanggil (pengaduan/create).

    Param:
      $kategoriList       [value => ['label','example']]
      $dosenList          daftar nama dosen
      $buktiPendingItems  bukti yang sudah terunggah (opsional)
      $backUrl            tujuan tombol kembali (daftar pengaduan)
--}}
@php
    use Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan;

    $pertanyaan     = PengaduanPertanyaan::semua();
    $kategoriAktif  = old('kategori');
    $setAktif       = $pertanyaan[$kategoriAktif] ?? null;

    $namaAkun       = auth()->user()->name ?? '-';
    $angkatanAkun   = optional(auth()->user()->student)->cohort_year;
    $emailAkun      = auth()->user()->email ?? '';
    $anonAwal       = (bool) old('is_anonim');
    // Email akun diisikan otomatis hanya untuk pengaduan biasa; di mode anonim
    // kotaknya mulai kosong supaya email kampus tidak terkirim tanpa disadari.
    $emailAwal      = old('template.email_kontak', $anonAwal ? '' : $emailAkun);

    // Kelas field bermasalah + pesan per-field, seperti @error di Edit User SITKOM.
    $invalid = fn (string $key) => $errors->has($key) ? 'is-invalid' : '';
@endphp

@include('manajemenmahasiswa::pengaduan.partials.box-styles')

<div class="kf-box" id="pgdForm">
    {{-- ── Bilah atas ── --}}
    <div class="kf-toolbar">
        <div class="kf-toolbar-lead">
            <a href="{{ $backUrl }}" class="kf-back" title="Kembali" aria-label="Kembali ke daftar pengaduan">
                <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
            </a>
            <h1 class="kf-toolbar-title">Buat Pengaduan</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="kf-alerts">
            @include('manajemenmahasiswa::pengaduan.partials.alerts')
        </div>
    @endif

    {{-- ── Pelapor ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Pelapor</h3>
            <p data-pgd-biasa-only @if ($anonAwal) hidden @endif>Nama dan angkatan diambil dari akun Anda.</p>
            <p data-pgd-anon-only @unless ($anonAwal) hidden @endunless>Identitas Anda disembunyikan.</p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-fields">
                {{-- Mode anonim: Nama & Angkatan disembunyikan seluruhnya. Angkatan akun tetap
                     disimpan server untuk statistik; isian manual di-disable agar tak terkirim. --}}
                <div class="pgd-field" data-pgd-biasa-only @if ($anonAwal) hidden @endif>
                    <label class="pgd-label" for="pgdNama">Nama Mahasiswa <span class="is-req">*</span></label>
                    <input type="text" class="pgd-input" id="pgdNama" value="{{ $namaAkun }}" disabled>
                </div>
                <div class="pgd-field" data-pgd-biasa-only @if ($anonAwal) hidden @endif>
                    <label class="pgd-label" for="pgdAngkatan">Angkatan <span class="is-req">*</span></label>
                    @if (filled($angkatanAkun))
                        <input type="text" class="pgd-input" id="pgdAngkatan" value="{{ $angkatanAkun }}" disabled>
                    @else
                        {{-- Akun tanpa data mahasiswa: angkatan diketik sendiri. --}}
                        <input type="text" class="pgd-input {{ $invalid('template.angkatan') }}" id="pgdAngkatan" name="template[angkatan]"
                               value="{{ old('template.angkatan') }}" placeholder="Contoh: 2022" data-pgd-angkatan-manual
                               inputmode="numeric" pattern="\d{4}" maxlength="4" autocomplete="off"
                               title="Tahun 4 digit, misalnya 2022" @if ($anonAwal) disabled @else required @endif
                               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                    @endif
                    @error('template.angkatan') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
                <div class="pgd-field is-wide">
                    <label class="pgd-label" for="pgdEmail">
                        Email Kontak
                        <span class="is-req" data-pgd-biasa-only @if ($anonAwal) hidden @endif>*</span>
                        <span class="is-opt" data-pgd-anon-only @unless ($anonAwal) hidden @endunless>(opsional)</span>
                    </label>
                    <input type="email" class="pgd-input {{ $invalid('template.email_kontak') }}" id="pgdEmail" name="template[email_kontak]"
                           value="{{ $emailAwal }}" data-email-akun="{{ $emailAkun }}" maxlength="255" autocomplete="email"
                           placeholder="nama@contoh.com" @unless ($anonAwal) required @endunless>
                    @error('template.email_kontak')
                        <div class="pgd-error">{{ $message }}</div>
                    @else
                        <div class="pgd-help" data-pgd-biasa-only @if ($anonAwal) hidden @endif>
                            Untuk dihubungi bila perlu klarifikasi. Nama, angkatan, dan email ini terlihat oleh pengelola.
                        </div>
                        <div class="pgd-help" data-pgd-anon-only @unless ($anonAwal) hidden @endunless>
                            Isi bila bersedia dihubungi. Gunakan email tanpa nama atau NIM Anda.
                        </div>
                    @enderror
                </div>
            </div>

            {{-- Nilai 0 dikirim bila kotak tidak dicentang. --}}
            <input type="hidden" name="is_anonim" value="0">
            <label class="pgd-anon">
                <input type="checkbox" name="is_anonim" value="1" id="pgdAnonim" @checked($anonAwal)>
                <span style="min-width: 0;">
                    <span class="pgd-anon-title">Sembunyikan identitas saya dari pengelola</span>
                    <span class="pgd-anon-desc">
                        Pengaduan tidak tercatat atas nama Anda dan tidak muncul di daftar Anda.
                        Anda mendapat tautan rahasia untuk melihatnya kembali.
                    </span>
                </span>
            </label>
        </div>
    </div>

    {{-- ── Kategori ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Kategori</h3>
            <p>Pilih satu yang paling sesuai.</p>
        </div>
        <div class="kf-main is-fields">
            <span class="pgd-label" id="pgdKategoriLabel">Kategori Pengaduan <span class="is-req">*</span></span>
            <div class="pgd-kategori-grid" role="radiogroup" aria-labelledby="pgdKategoriLabel" aria-required="true">
                @foreach ($kategoriList as $value => $meta)
                    <label class="pgd-kategori">
                        <input type="radio" name="kategori" value="{{ $value }}"
                               data-label="{{ $meta['label'] }}"
                               data-subjek="{{ $pertanyaan[$value]['subjek'] ?? '' }}"
                               data-pesan="{{ $pertanyaan[$value]['pesan'] ?? '' }}"
                               {{ $kategoriAktif === $value ? 'checked' : '' }}
                               {{ $loop->first ? 'required' : '' }}>
                        <span style="min-width: 0;">
                            <span class="pgd-kategori-title">{{ $meta['label'] }}</span>
                            <span class="pgd-kategori-desc">{{ $meta['example'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('kategori') <div class="pgd-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ── Detail kejadian: pertanyaan per kategori ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Detail Kejadian</h3>
            <p data-pgd-caption>
                @if ($setAktif)
                    Pertanyaan untuk kategori <strong>{{ $kategoriList[$kategoriAktif]['label'] ?? '' }}</strong>.
                @else
                    Menyesuaikan kategori terpilih.
                @endif
            </p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-set-empty" data-pgd-empty @if ($setAktif) hidden @endif>
                <x-manajemenmahasiswa::ui.icon name="arrow-narrow-up" size="16" />
                Pilih kategori dulu.
            </div>

            @foreach ($pertanyaan as $kategori => $set)
                @php $aktif = $kategori === $kategoriAktif; @endphp
                <fieldset class="pgd-set" data-pgd-set="{{ $kategori }}" @unless ($aktif) hidden disabled @endunless>
                    <legend class="pgd-sr-only">Detail kejadian {{ $kategoriList[$kategori]['label'] ?? $kategori }}</legend>
                    <div class="pgd-fields">
                        @foreach ($set['fields'] as $key => $field)
                            @php
                                $fieldId = 'pgd-' . $kategori . '-' . $key;
                                $name    = 'template[' . $key . ']';
                                // old() hanya milik set aktif; set lain mulai kosong.
                                $nilai   = $aktif ? old('template.' . $key, $key === 'waktu_kejadian' ? old('template.tanggal_kejadian') : null) : null;
                            @endphp
                            <div class="pgd-field">
                                <label class="pgd-label" for="{{ $fieldId }}">{{ $field['label'] }} <span class="is-req">*</span></label>

                                {{-- required tidak menghalangi set lain: kontrol di fieldset disabled dilewati validasi browser. --}}
                                @if ($field['type'] === 'datetime')
                                    <input type="datetime-local" class="pgd-input {{ $invalid('template.' . $key) }}"
                                           id="{{ $fieldId }}" name="{{ $name }}" value="{{ $nilai }}"
                                           max="{{ now()->format('Y-m-d\TH:i') }}" required>
                                @elseif ($field['type'] === 'dosen')
                                    <x-manajemenmahasiswa::ui.select :name="$name" :id="$fieldId" size="md" :invalid="$errors->has('template.' . $key)" required>
                                        <option value="" {{ $nilai ? '' : 'selected' }}>Pilih dosen…</option>
                                        @foreach (($dosenList ?? []) as $namaDosen)
                                            <option value="{{ $namaDosen }}" {{ $nilai === $namaDosen ? 'selected' : '' }}>{{ $namaDosen }}</option>
                                        @endforeach
                                    </x-manajemenmahasiswa::ui.select>
                                @elseif ($field['type'] === 'select')
                                    <x-manajemenmahasiswa::ui.select :name="$name" :id="$fieldId" size="md" :invalid="$errors->has('template.' . $key)" required>
                                        <option value="" {{ $nilai ? '' : 'selected' }}>Pilih…</option>
                                        @foreach ($field['options'] as $opsi)
                                            <option value="{{ $opsi }}" {{ $nilai === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                        @endforeach
                                    </x-manajemenmahasiswa::ui.select>
                                @else
                                    <input type="text" class="pgd-input {{ $invalid('template.' . $key) }}"
                                           id="{{ $fieldId }}" name="{{ $name }}" value="{{ $nilai }}"
                                           placeholder="{{ $field['placeholder'] ?? '' }}" maxlength="255" required>
                                @endif

                                @if ($aktif)
                                    @error('template.' . $key) <div class="pgd-error">{{ $message }}</div> @enderror
                                @endif
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>
    </div>

    {{-- ── Isi pengaduan ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Isi Pengaduan</h3>
            <p>Subjek singkat dan cerita masalahnya.</p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-fields">
                <div class="pgd-field is-wide">
                    <label class="pgd-label" for="pgdJudul">Subjek <span class="is-req">*</span></label>
                    <input type="text" class="pgd-input {{ $invalid('template.judul') }}" id="pgdJudul" name="template[judul]"
                           value="{{ old('template.judul') }}" maxlength="255" required
                           placeholder="{{ $setAktif['subjek'] ?? 'Contoh: AC Ruang A.3.12 tidak berfungsi' }}">
                    @error('template.judul') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
                <div class="pgd-field is-wide">
                    <label class="pgd-label" for="pgdKronologi">Pesan <span class="is-req">*</span></label>
                    <textarea class="pgd-input {{ $invalid('template.kronologi') }}" id="pgdKronologi" name="template[kronologi]" rows="6"
                              maxlength="5000" required
                              placeholder="{{ $setAktif['pesan'] ?? 'Ceritakan dengan jelas masalah yang Anda hadapi…' }}">{{ old('template.kronologi') }}</textarea>
                    @error('template.kronologi')
                        <div class="pgd-error">{{ $message }}</div>
                    @else
                        <div class="pgd-help pgd-help-row">
                            <span>Minimal 20 karakter.</span>
                            <span data-pgd-count>{{ mb_strlen((string) old('template.kronologi')) }}/5000</span>
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bukti dukung ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Bukti Dukung</h3>
            <p>Opsional, tanpa bukti tetap dicatat.</p>
        </div>
        <div class="kf-main is-fields">
            @include('manajemenmahasiswa::pengaduan.partials.bukti-input', [
                'buktiPendingItems' => $buktiPendingItems ?? [],
            ])
        </div>
    </div>

    <div class="kf-footer">
        <button type="submit" class="mk-btn mk-btn--primary">Lanjut Konfirmasi</button>
    </div>
</div>

<script>
    (function () {
        var root = document.getElementById('pgdForm');
        if (!root) return;

        var sets = root.querySelectorAll('[data-pgd-set]');
        var empty = root.querySelector('[data-pgd-empty]');
        var caption = root.querySelector('[data-pgd-caption]');
        var judul = document.getElementById('pgdJudul');
        var pesan = document.getElementById('pgdKronologi');
        var count = root.querySelector('[data-pgd-count]');

        var anon = document.getElementById('pgdAnonim');
        var angkatanManual = root.querySelector('[data-pgd-angkatan-manual]');
        var email = document.getElementById('pgdEmail');

        // Tampilkan hanya set pertanyaan kategori terpilih. Set lain di-disable
        // supaya isiannya tidak ikut terkirim (server juga membuangnya).
        function pilihKategori(radio) {
            sets.forEach(function (set) {
                var aktif = set.dataset.pgdSet === radio.value;
                set.hidden = !aktif;
                set.disabled = !aktif;
            });
            if (empty) empty.hidden = true;
            if (caption) {
                caption.textContent = '';
                caption.append('Pertanyaan untuk kategori ');
                var strong = document.createElement('strong');
                strong.textContent = radio.dataset.label;
                caption.append(strong, '.');
            }
            if (judul && radio.dataset.subjek) judul.placeholder = radio.dataset.subjek;
            if (pesan && radio.dataset.pesan) pesan.placeholder = radio.dataset.pesan;
        }

        // Centang anonim: kolom Nama & Angkatan disembunyikan, email kontak jadi opsional,
        // teks bantuan & peringatan PDF ikut berganti.
        function perbaruiIdentitas(dariCentang) {
            var on = !!(anon && anon.checked);
            if (angkatanManual) {
                angkatanManual.disabled = on;
                angkatanManual.required = !on;
            }
            if (email) {
                email.required = !on;
                // Hanya saat pelapor mengubah centang: email akun dikosongkan ketika
                // beralih ke anonim, dan diisikan lagi ketika kembali ke biasa.
                if (dariCentang === true) {
                    if (on && email.value === email.dataset.emailAkun) email.value = '';
                    if (!on && email.value === '') email.value = email.dataset.emailAkun;
                }
            }
            root.querySelectorAll('[data-pgd-anon-only]').forEach(function (el) { el.hidden = !on; });
            root.querySelectorAll('[data-pgd-biasa-only]').forEach(function (el) { el.hidden = on; });
        }

        root.querySelectorAll('input[name="kategori"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (radio.checked) pilihKategori(radio);
            });
        });
        if (anon) anon.addEventListener('change', function () { perbaruiIdentitas(true); });

        if (pesan && count) {
            pesan.addEventListener('input', function () { count.textContent = pesan.value.length + '/5000'; });
        }
    })();
</script>
