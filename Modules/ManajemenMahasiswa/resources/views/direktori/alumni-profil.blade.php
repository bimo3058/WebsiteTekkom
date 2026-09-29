<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.sitkom-ui')

@push('styles')
<style>
    /* Kotak putih pembungkus halaman sengaja tidak lagi dibuat transparan, supaya latar
       halaman ini sama dengan halaman Direktori Mahasiswa: konten di dalam kotak putih
       di atas latar abu, seperti dashboard Super Admin. */

    .card-section {
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--c-border);
        box-shadow: var(--shadow-card);
        padding: 28px;
    }

    .identity-card {
        text-align: center;
        padding: 32px 24px;
    }
    /* Avatar inisial netral, sama dengan komponen user-avatar global */
    .identity-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--c-grey-50);
        color: var(--c-fg-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: bold;
        margin: 0 auto 16px;
        overflow: hidden;
        border: 3px solid var(--c-border);
    }
    .identity-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .identity-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--c-fg);
        margin-bottom: 4px;
    }
    .identity-nim {
        font-family: monospace;
        color: var(--c-fg-muted);
        font-size: 14px;
        margin-bottom: 12px;
    }
    .identity-badges {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    /* Bentuk badge Role SITKOM (components/ui/role-badge), warna netral */
    .identity-chip {
        display: inline-block;
        padding: 3px 12px;
        border: 1px solid var(--c-border);
        border-radius: 9999px;
        background: var(--c-grey-50);
        color: var(--c-fg-sec);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .identity-note {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed var(--c-border);
        font-size: 12px;
        color: var(--c-fg-muted);
        line-height: 1.6;
    }

    .form-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--c-fg);
        margin-bottom: 20px;
        padding-bottom: 12px;
    }
    /* Label & kotak isian: partials/sitkom-ui (gaya Edit User SITKOM) */
    /* Pesan validasi Bootstrap memakai merah bawaannya; disamakan dengan merah palet global */
    .invalid-feedback { color: var(--c-error); }
</style>
@endpush

<x-manajemenmahasiswa::ui.page-header bordered
    title="Profil Karir Alumni"
    subtitle="Perbarui data pekerjaan dan karir Anda. Data ini digunakan untuk akreditasi dan jejaring alumni.">
    <x-slot:actions>
        <a href="{{ route('manajemenmahasiswa.direktori.alumni.profil.cv') }}" target="_blank"
           class="mk-btn mk-btn--secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download CV
        </a>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<!-- Flash Messages -->
<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />

<div class="row g-4">
    <!-- Identity Card -->
    <div class="col-lg-4">
        <div class="card-section identity-card">
            <div class="identity-avatar">
                @if($alumni->user && $alumni->user->avatar_url)
                    <img src="{{ $alumni->user->avatar_url }}" alt="Avatar">
                @else
                    {{ strtoupper(substr($alumni->user->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div class="identity-name">{{ $alumni->user->name ?? 'Tanpa Nama' }}</div>
            <div class="identity-nim">{{ $alumni->nim }}</div>

            <div class="identity-badges">
                <span class="identity-chip">Angkatan {{ $alumni->angkatan }}</span>
                <span class="identity-chip">Lulus {{ $alumni->tahun_lulus }}</span>
            </div>

            <div class="identity-note">
                Data karir ini digunakan untuk keperluan <strong>akreditasi</strong> program studi dan membangun jejaring alumni.
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="col-lg-8">
        <div class="card-section">
            <h5 class="form-title">Form Data Karir</h5>

            <form action="{{ route('manajemenmahasiswa.direktori.alumni.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Status Karir</label>
                        <x-manajemenmahasiswa::ui.select name="status_karir" size="md" :invalid="$errors->has('status_karir')">
                            <option value="">— Pilih Status —</option>
                            @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ old('status_karir', $alumni->status_karir) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-manajemenmahasiswa::ui.select>
                        @error('status_karir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Pembungkus yang disembunyikan JS saat status "Belum Terdata".
                         Ia sendiri harus col-12 berisi .row — kalau tidak, kolom di
                         dalamnya kehilangan jarak & lebar grid. --}}
                    <div id="karir-detail-fields" class="col-12">
                    <div class="row g-3">
                    <div class="col-md-6" id="field-bidang-industri">
                        <label class="form-label-custom">Bidang Industri</label>
                        <x-manajemenmahasiswa::ui.select name="bidang_industri" size="md" :invalid="$errors->has('bidang_industri')">
                            <option value="">— Pilih Bidang —</option>
                            @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::BIDANG_INDUSTRI_LIST as $key => $label)
                                <option value="{{ $key }}" {{ old('bidang_industri', $alumni->bidang_industri) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-manajemenmahasiswa::ui.select>
                        @error('bidang_industri') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label-custom" id="label-perusahaan">Perusahaan / Instansi / Nama Usaha</label>
                        <input type="text" name="perusahaan" value="{{ old('perusahaan', $alumni->perusahaan) }}"
                               class="form-control form-control-custom @error('perusahaan') is-invalid @enderror"
                               placeholder="Contoh: PT Teknologi Indonesia" id="input-perusahaan">
                        @error('perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom" id="label-jabatan">Jabatan / Posisi</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $alumni->jabatan) }}"
                               class="form-control form-control-custom @error('jabatan') is-invalid @enderror"
                               placeholder="Contoh: Software Engineer" id="input-jabatan">
                        @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6" id="field-tahun-mulai">
                        <label class="form-label-custom">Tahun Mulai Bekerja / Usaha</label>
                        <input type="number" name="tahun_mulai_bekerja" value="{{ old('tahun_mulai_bekerja', $alumni->tahun_mulai_bekerja) }}"
                               class="form-control form-control-custom @error('tahun_mulai_bekerja') is-invalid @enderror"
                               placeholder="Contoh: 2023" min="2000" max="{{ date('Y') }}">
                        @error('tahun_mulai_bekerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label-custom">Link LinkedIn (Opsional)</label>
                        <input type="url" name="linkedin" value="{{ old('linkedin', $alumni->linkedin) }}"
                               class="form-control form-control-custom @error('linkedin') is-invalid @enderror"
                               placeholder="https://linkedin.com/in/username">
                        @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-4 mt-3" style="border-top: 1px solid var(--c-border);">
                    <button type="submit" class="mk-btn mk-btn--primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Context-aware form: show/hide & relabel career fields berdasarkan status_karir --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect  = document.querySelector('[name="status_karir"]');
    const karirFields   = document.getElementById('karir-detail-fields');
    const fieldBidang   = document.getElementById('field-bidang-industri');
    const fieldTahun    = document.getElementById('field-tahun-mulai');
    const labelPerusahaan = document.getElementById('label-perusahaan');
    const labelJabatan    = document.getElementById('label-jabatan');
    const inputPerusahaan = document.getElementById('input-perusahaan');
    const inputJabatan    = document.getElementById('input-jabatan');

    function toggleKarirFields() {
        if (!statusSelect || !karirFields) return;
        const status = statusSelect.value;

        // Jika belum diisi atau belum_bekerja → sembunyikan semua field karir
        if (status === 'belum_bekerja' || status === '') {
            karirFields.style.display = 'none';
            return;
        }

        karirFields.style.display = '';

        if (status === 'studi_lanjut') {
            // Studi lanjut: sembunyikan bidang industri & tahun mulai bekerja, relabel
            if (fieldBidang)  fieldBidang.style.display  = 'none';
            if (fieldTahun)   fieldTahun.style.display   = 'none';
            if (labelPerusahaan) labelPerusahaan.textContent = 'Universitas / Institusi';
            if (labelJabatan)    labelJabatan.textContent    = 'Program Studi Lanjut';
            if (inputPerusahaan) inputPerusahaan.placeholder = 'Contoh: Universitas Indonesia';
            if (inputJabatan)    inputJabatan.placeholder    = 'Contoh: S2 Ilmu Komputer';
        } else if (status === 'wirausaha') {
            if (fieldBidang) fieldBidang.style.display = '';
            if (fieldTahun)  fieldTahun.style.display  = '';
            if (labelPerusahaan) labelPerusahaan.textContent = 'Nama Usaha';
            if (labelJabatan)    labelJabatan.textContent    = 'Posisi / Jabatan';
            if (inputPerusahaan) inputPerusahaan.placeholder = 'Contoh: CV Teknologi Nusantara';
            if (inputJabatan)    inputJabatan.placeholder    = 'Contoh: Founder & CEO';
        } else {
            // bekerja (default)
            if (fieldBidang) fieldBidang.style.display = '';
            if (fieldTahun)  fieldTahun.style.display  = '';
            if (labelPerusahaan) labelPerusahaan.textContent = 'Perusahaan / Instansi';
            if (labelJabatan)    labelJabatan.textContent    = 'Jabatan / Posisi';
            if (inputPerusahaan) inputPerusahaan.placeholder = 'Contoh: PT Teknologi Indonesia';
            if (inputJabatan)    inputJabatan.placeholder    = 'Contoh: Software Engineer';
        }
    }

    statusSelect.addEventListener('change', toggleKarirFields);
    toggleKarirFields(); // Run on page load to set initial state
});
</script>

</x-dynamic-component>

