{{--
    Isi form Buat Pengaduan dalam "satu kotak", meniru Edit User SITKOM
    (resources/views/superadmin/users/edit.blade.php): bilah atas Kembali + judul,
    tiap bagian = kolom judul + keterangan di kiri, field di kanan, tombol Lanjut di kaki.

    Urutan bagian: Pelapor → Kategori → Detail Kejadian → Isi Pengaduan → Bukti Dukung.
    "Detail Kejadian" berisi empat pertanyaan yang menyesuaikan kategori terpilih
    (sumber: Support\PengaduanPertanyaan). Tiap kategori punya <fieldset> sendiri;
    yang tidak aktif disembunyikan DAN di-disable sehingga isiannya tidak ikut terkirim.

    Dipakai bersama jalur Reguler (pengaduan/create) dan Konfidensial (anon/create).
    Elemen <form>, @csrf, honeypot, dan input tersembunyi tetap di halaman pemanggil.

    Param:
      $jalur              'reguler' | 'konfidensial'
      $kategoriList       [value => ['label','example']]
      $dosenList          daftar nama dosen
      $buktiPendingItems  bukti yang sudah terunggah (opsional)
      $backUrl            tujuan tombol kembali (pemilih jalur);
                          hanya tampil di jalur Reguler
--}}
@php
    $isKonfidensial = $jalur === 'konfidensial';
    $pertanyaan     = \Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan::semua();
    $kategoriAktif  = old('kategori');
    $setAktif       = $pertanyaan[$kategoriAktif] ?? null;

    // Kelas field bermasalah + pesan per-field, seperti @error di Edit User SITKOM.
    $invalid = fn (string $key) => $errors->has($key) ? 'is-invalid' : '';
@endphp

@include('manajemenmahasiswa::pengaduan.partials.box-styles')

<div class="kf-box" id="pgdForm">
    {{-- ── Bilah atas ── --}}
    <div class="kf-toolbar">
        <div class="kf-toolbar-lead">
            {{-- Jalur Konfidensial dibuka lewat magic link, jadi tanpa tombol kembali. --}}
            @unless ($isKonfidensial)
                <a href="{{ $backUrl }}" class="kf-back" title="Kembali" aria-label="Kembali ke pilih jalur">
                    <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
                </a>
            @endunless
            <h1 class="kf-toolbar-title">Buat Pengaduan</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="kf-alerts">
            @include('manajemenmahasiswa::pengaduan.partials.alerts')
        </div>
    @endif

    {{-- ── Pelapor: jalur + identitas dalam satu bagian ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Pelapor</h3>
            <p>
                {{ $isKonfidensial
                    ? 'Nama tidak diminta. Angkatan boleh dikosongkan.'
                    : 'Nama dan angkatan diambil otomatis dari akun Anda.' }}
            </p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-jalur">
                <span class="pgd-jalur-icon {{ $isKonfidensial ? 'is-konfidensial' : '' }}">
                    <x-manajemenmahasiswa::ui.icon :name="$isKonfidensial ? 'shield-02' : 'user-circle'" size="20" />
                </span>
                <div>
                    <div class="pgd-jalur-title">{{ $isKonfidensial ? 'Jalur Konfidensial' : 'Jalur Reguler' }}</div>
                    <div class="pgd-jalur-desc">
                        {{ $isKonfidensial
                            ? 'Identitas Anda tidak ditampilkan kepada publik maupun admin.'
                            : 'Identitas Anda terlihat oleh admin untuk memudahkan tindak lanjut.' }}
                    </div>
                </div>
            </div>

            @php
                // Reguler: angkatan dari akun (read-only). Konfidensial, atau akun tanpa
                // data mahasiswa: diketik sendiri — wajib di Reguler, opsional di Konfidensial.
                $angkatanAkun = $isKonfidensial ? null : optional(auth()->user()->student)->cohort_year;
            @endphp
            <div class="pgd-fields" style="margin-top: 16px;">
                @unless ($isKonfidensial)
                    <div class="pgd-field">
                        <label class="pgd-label" for="pgdNama">Nama Mahasiswa <span class="is-req">*</span></label>
                        <input type="text" class="pgd-input" id="pgdNama" value="{{ auth()->user()->name ?? '-' }}" disabled>
                    </div>
                @endunless
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdAngkatan">
                        Angkatan
                        @if ($isKonfidensial) <span class="is-opt">(opsional)</span> @else <span class="is-req">*</span> @endif
                    </label>
                    @if (filled($angkatanAkun))
                        <input type="text" class="pgd-input" id="pgdAngkatan" value="{{ $angkatanAkun }}" disabled>
                    @else
                        <input type="text" class="pgd-input {{ $invalid('template.angkatan') }}" id="pgdAngkatan" name="template[angkatan]"
                               value="{{ old('template.angkatan') }}" placeholder="Contoh: 2022"
                               inputmode="numeric" pattern="\d{4}" maxlength="4" autocomplete="off"
                               title="Tahun 4 digit, misalnya 2022"
                               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)"
                               @unless ($isKonfidensial) required @endunless>
                    @endif
                    @error('template.angkatan') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
            </div>
            @unless ($isKonfidensial)
                <div class="pgd-help">Nama dan angkatan tersimpan pada tiket. Pilih jalur Konfidensial bila tidak ingin identitas disimpan.</div>
            @endunless
        </div>
    </div>

    {{-- ── Kategori ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Kategori</h3>
            <p>Pilih satu kategori yang paling sesuai. Pertanyaan berikutnya menyesuaikan pilihan Anda.</p>
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
                    Pertanyaan menyesuaikan kategori yang Anda pilih.
                @endif
            </p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-set-empty" data-pgd-empty @if ($setAktif) hidden @endif>
                <x-manajemenmahasiswa::ui.icon name="arrow-narrow-up" size="16" />
                Pilih kategori terlebih dahulu untuk menampilkan pertanyaan yang sesuai.
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
            <p>Tuliskan subjek singkat dan ceritakan masalahnya dengan jelas.</p>
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
                            <span>Minimal 20 karakter agar dapat ditindaklanjuti.</span>
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
            <p>Lampirkan berkas pendukung bila ada.</p>
        </div>
        <div class="kf-main is-fields">
            @include('manajemenmahasiswa::pengaduan.partials.bukti-input', [
                'buktiPendingItems' => $buktiPendingItems ?? [],
                'confidential'      => $isKonfidensial,
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

        // Tampilkan hanya set pertanyaan kategori terpilih. Set lain di-disable
        // supaya isiannya tidak ikut terkirim (server juga membuangnya).
        function pilih(radio) {
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

        root.querySelectorAll('input[name="kategori"]').forEach(function (radio) {
            radio.addEventListener('change', function () { if (radio.checked) pilih(radio); });
        });

        if (pesan && count) {
            pesan.addEventListener('input', function () { count.textContent = pesan.value.length + '/5000'; });
        }
    })();
</script>
