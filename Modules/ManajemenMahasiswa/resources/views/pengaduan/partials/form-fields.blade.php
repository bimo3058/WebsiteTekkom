{{--
    Isi form Buat Pengaduan dalam "satu kotak", meniru Edit User SITKOM
    (resources/views/superadmin/users/edit.blade.php): bilah atas Kembali | Lanjut,
    lalu tiap bagian = kolom judul + keterangan di kiri, field di kanan.

    Dipakai bersama jalur Reguler (pengaduan/create) dan Konfidensial (anon/create).
    Elemen <form>, @csrf, honeypot, dan input tersembunyi tetap di halaman pemanggil.

    Param:
      $jalur              'reguler' | 'konfidensial'
      $kategoriList       [value => ['label','example']]
      $dosenList          daftar nama dosen
      $frekuensiList      [value => label]
      $buktiPendingItems  bukti yang sudah terunggah (opsional)
      $backUrl            tujuan tombol kembali (pemilih jalur)
--}}
@php
    $isKonfidensial = $jalur === 'konfidensial';

    // Kelas field bermasalah + pesan per-field, seperti @error di Edit User SITKOM.
    $invalid = fn (string $key) => $errors->has($key) ? 'is-invalid' : '';
@endphp

@include('manajemenmahasiswa::pengaduan.partials.box-styles')

<div class="kf-box">
    {{-- ── Bilah atas ── --}}
    <div class="kf-toolbar">
        <div class="kf-toolbar-lead">
            <a href="{{ $backUrl }}" class="kf-back" title="Kembali" aria-label="Kembali ke pilih jalur">
                <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
            </a>
            <h1 class="kf-toolbar-title">Buat Pengaduan</h1>
        </div>
        <div class="kf-toolbar-actions">
            <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">Lanjut Konfirmasi</button>
        </div>
    </div>

    @if ($errors->any())
        <div class="kf-alerts">
            @include('manajemenmahasiswa::pengaduan.partials.alerts')
        </div>
    @endif

    {{-- ── Jalur ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Jalur Pelaporan</h3>
            <p>Menentukan apakah identitas Anda terlihat oleh pengelola.</p>
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
                        <a href="{{ $backUrl }}">Ganti jalur</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Kategori ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Kategori <span style="color: var(--c-error);">*</span></h3>
            <p>Pilih satu kategori yang paling sesuai dengan masalah Anda.</p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-kategori-grid" role="radiogroup" aria-label="Kategori pengaduan">
                @foreach ($kategoriList as $value => $meta)
                    <label class="pgd-kategori">
                        <input type="radio" name="kategori" value="{{ $value }}"
                               {{ old('kategori') === $value ? 'checked' : '' }}
                               {{ $loop->first ? 'required' : '' }}>
                        <span style="min-width: 0;">
                            <span class="pgd-kategori-title">{{ $meta['label'] }}</span>
                            <span class="pgd-kategori-desc">Contoh: {{ $meta['example'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('kategori') <div class="pgd-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ── Identitas ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Identitas</h3>
            <p>
                {{ $isKonfidensial
                    ? 'Nama tidak diminta. Angkatan boleh dikosongkan.'
                    : 'Nama diambil otomatis dari akun Anda.' }}
            </p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-fields">
                @unless ($isKonfidensial)
                    <div class="pgd-field">
                        <label class="pgd-label">Nama Mahasiswa</label>
                        <input type="text" class="pgd-input" value="{{ auth()->user()->name ?? '-' }}" disabled>
                        <div class="pgd-help">Tersimpan pada tiket. Pilih jalur Konfidensial bila tidak ingin identitas disimpan.</div>
                    </div>
                @endunless
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdAngkatan">Angkatan <span class="is-opt">(Opsional)</span></label>
                    <input type="text" class="pgd-input {{ $invalid('template.angkatan') }}" id="pgdAngkatan" name="template[angkatan]"
                           value="{{ old('template.angkatan') }}" placeholder="Contoh: 2022">
                    @error('template.angkatan') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Detail pengaduan ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Detail Pengaduan</h3>
            <p>Tuliskan subjek singkat dan ceritakan masalahnya dengan jelas.</p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-fields">
                <div class="pgd-field is-wide">
                    <label class="pgd-label" for="pgdJudul">Subjek <span class="is-req">*</span></label>
                    <input type="text" class="pgd-input {{ $invalid('template.judul') }}" id="pgdJudul" name="template[judul]"
                           value="{{ old('template.judul') }}" placeholder="Contoh: AC Ruang 3.12 tidak berfungsi" required>
                    @error('template.judul') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
                <div class="pgd-field is-wide">
                    <label class="pgd-label" for="pgdKronologi">Pesan <span class="is-req">*</span></label>
                    <textarea class="pgd-input {{ $invalid('template.kronologi') }}" id="pgdKronologi" name="template[kronologi]" rows="6"
                              placeholder="Ceritakan dengan jelas masalah yang Anda hadapi…" required>{{ old('template.kronologi') }}</textarea>
                    @error('template.kronologi')
                        <div class="pgd-error">{{ $message }}</div>
                    @else
                        <div class="pgd-help">Minimal 20 karakter untuk memberikan konteks yang jelas.</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Informasi tambahan ── --}}
    <div class="kf-split">
        <div class="kf-side">
            <h3>Informasi Tambahan</h3>
            <p>Semua opsional, tetapi membantu pengelola menindaklanjuti.</p>
        </div>
        <div class="kf-main is-fields">
            <div class="pgd-fields">
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdLokasi">Lokasi Kejadian</label>
                    <input type="text" class="pgd-input {{ $invalid('template.lokasi') }}" id="pgdLokasi" name="template[lokasi]"
                           value="{{ old('template.lokasi') }}" placeholder="Contoh: Lab Komputer">
                    @error('template.lokasi') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdWaktu">Waktu Kejadian</label>
                    <input type="datetime-local" class="pgd-input {{ $invalid('template.waktu_kejadian') }}" id="pgdWaktu" name="template[waktu_kejadian]"
                           value="{{ old('template.waktu_kejadian') ?? old('template.tanggal_kejadian') }}">
                    @error('template.waktu_kejadian') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="pgd-fields is-3">
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdMatkul">Mata Kuliah</label>
                    <input type="text" class="pgd-input {{ $invalid('template.mata_kuliah') }}" id="pgdMatkul" name="template[mata_kuliah]"
                           value="{{ old('template.mata_kuliah') }}" placeholder="Contoh: Basis Data">
                    @error('template.mata_kuliah') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdDosen">Dosen Terkait</label>
                    <x-manajemenmahasiswa::ui.select name="template[nama_dosen]" id="pgdDosen" size="md" :invalid="$errors->has('template.nama_dosen')">
                        <option value="" {{ old('template.nama_dosen') ? '' : 'selected' }}>Pilih dosen…</option>
                        @foreach (($dosenList ?? []) as $namaDosen)
                            <option value="{{ $namaDosen }}" {{ old('template.nama_dosen') === $namaDosen ? 'selected' : '' }}>
                                {{ $namaDosen }}
                            </option>
                        @endforeach
                    </x-manajemenmahasiswa::ui.select>
                    @error('template.nama_dosen') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdTendik">Tendik Terkait</label>
                    <input type="text" class="pgd-input {{ $invalid('template.nama_tendik') }}" id="pgdTendik" name="template[nama_tendik]"
                           value="{{ old('template.nama_tendik') }}" placeholder="Contoh: Bu Siti">
                    @error('template.nama_tendik') <div class="pgd-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="pgd-fields">
                <div class="pgd-field">
                    <label class="pgd-label" for="pgdFrekuensi">Seberapa Sering Terjadi</label>
                    <x-manajemenmahasiswa::ui.select name="template[frekuensi]" id="pgdFrekuensi" size="md" :invalid="$errors->has('template.frekuensi')">
                        <option value="" {{ old('template.frekuensi') ? '' : 'selected' }}>Pilih frekuensi…</option>
                        @foreach (($frekuensiList ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ old('template.frekuensi') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </x-manajemenmahasiswa::ui.select>
                    @error('template.frekuensi') <div class="pgd-error">{{ $message }}</div> @enderror
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
