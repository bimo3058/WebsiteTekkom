<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.sitkom-ui')

@push('styles')
<style>
    /* Kotak putih pembungkus halaman sengaja tidak lagi dibuat transparan, supaya latar
       halaman ini sama dengan Edit Mahasiswa: konten di dalam kotak putih di atas
       latar abu, seperti dashboard Super Admin. */

    .edit-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--c-border);
        box-shadow: var(--shadow-card);
        padding: 32px;
    }
    .section-divider {
        font-size: 15px;
        font-weight: 700;
        color: var(--c-primary);
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--c-primary-subtle);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    /* Label & kotak isian: partials/sitkom-ui (gaya Edit User SITKOM) */
    /* Pesan validasi Bootstrap memakai merah bawaannya; disamakan dengan merah palet global */
    .invalid-feedback { color: var(--c-error); }
</style>
@endpush

<x-manajemenmahasiswa::ui.page-header bordered title="Edit Data Alumni">
    Admin — perbarui biodata dan karir alumni
    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.direktori.alumni.show', $alumni->id) }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" title="Kembali" aria-label="Kembali">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
    </x-slot:leading>
</x-manajemenmahasiswa::ui.page-header>

<div class="edit-card">
    <form action="{{ route('manajemenmahasiswa.direktori.alumni.update', $alumni->id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
            // Ambil kontak dari relasi user jika ada, atau kemahasiswaan
            $userContact = $alumni->user ? $alumni->user->whatsapp : null;
            if (!$userContact) {
                $mhs = \Modules\ManajemenMahasiswa\Models\Kemahasiswaan::where('user_id', $alumni->user_id)->first();
                $userContact = $mhs ? $mhs->kontak : '';
            }

            $savedWa = old('kontak', $userContact ?? '');
            $savedCode = '+62';
            $localNum = $savedWa;

            $knownCodes = ['+93','+27','+1','+966','+54','+61','+31','+55','+673','+971','+63','+91','+62','+44','+39','+81','+49','+855','+82','+856','+60','+95','+92','+33','+974','+64','+65','+66','+90','+84','+86'];
            usort($knownCodes, fn($a, $b) => strlen($b) - strlen($a));

            foreach ($knownCodes as $code) {
                if (str_starts_with($savedWa, $code)) {
                    $savedCode = $code;
                    $localNum = substr($savedWa, strlen($code));
                    break;
                }
            }
            
            if ($localNum === '' && $savedWa !== '') {
                $localNum = $savedWa;
                if (str_starts_with($localNum, '0')) {
                    $savedCode = '+62';
                    $localNum = ltrim($localNum, '0');
                }
            }
        @endphp

        <!-- Section: Akademik -->
        <div class="section-divider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            Informasi Akademik
        </div>
        {{-- NIM dan Angkatan dikunci dengan `disabled` (bukan sekadar `readonly`) agar
             benar-benar tidak ikut terkirim saat form disimpan. Sisi server juga sudah
             berhenti menerima kedua field ini. --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label-custom">NIM</label>
                <input type="text" class="form-control form-control-custom" value="{{ $alumni->nim }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Angkatan</label>
                <input type="number" class="form-control form-control-custom" value="{{ $alumni->angkatan }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Tahun Lulus</label>
                <input type="number" name="tahun_lulus" class="form-control form-control-custom @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $alumni->tahun_lulus) }}" required min="2000" max="2099">
                @error('tahun_lulus') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Kontak -->
            <div class="col-md-12" x-data="alumniPhoneCode('{{ $savedCode }}')">
                <label class="form-label-custom">Kontak / WhatsApp</label>
                <div class="d-flex position-relative form-control-custom p-0" style="overflow: visible; background: #fff;">
                    <button type="button" @click.prevent="toggle($el)"
                            class="btn border-0 d-flex align-items-center gap-2" style="background: var(--c-grey-0); color: var(--c-fg-sec); border-right: 1px solid var(--c-border-strong) !important; border-top-right-radius: 0; border-bottom-right-radius: 0; border-top-left-radius: 7px; border-bottom-left-radius: 7px;">
                        <span x-text="selected.dial" style="font-size: 13px; font-weight: 600; color: var(--c-fg-sec);"></span>
                        {{-- Warna panah diwarisi dari tombol: :style Alpine menimpa seluruh atribut style SVG ini. --}}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             :style="open ? 'transform:rotate(180deg)' : ''" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <input type="text" name="kontak" class="form-control border-0 shadow-none w-100"
                           value="{{ old('kontak', $localNum) }}" placeholder="8123456789"
                           @input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                           style="background: transparent; font-size: 13px; font-weight: 600; color: var(--c-fg-sec);">
                    <input type="hidden" name="phone_code" :value="selected.dial">

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false" :style="dropdownStyle"
                         class="position-fixed bg-white border rounded shadow-lg" style="display:none; width: 240px; z-index: 9999; border-radius: 12px !important; overflow: hidden;">
                        <div class="p-2 border-bottom" style="background: var(--c-grey-0);">
                            <input type="text" x-model="search" @click.stop placeholder="Cari negara..." class="form-control form-control-sm" style="font-size: 12px; border-radius: 8px;">
                        </div>
                        <ul class="list-unstyled mb-0" style="max-height: 200px; overflow-y: auto;">
                            <template x-for="c in filtered" :key="c.name">
                                <li>
                                    <button type="button" @click="select(c)"
                                            class="w-100 btn text-start d-flex align-items-center gap-2 py-2 px-3 border-0 rounded-0"
                                            :style="selected.name === c.name ? 'background: var(--c-bg);' : 'background: #fff;'">
                                        <span x-text="c.name" class="text-truncate flex-grow-1" style="font-size: 12px; color: var(--c-fg-sec); font-weight: 500;"></span>
                                        <span x-text="c.dial" style="font-size: 11px; font-weight: 700; color: var(--c-fg-muted);"></span>
                                    </button>
                                </li>
                            </template>
                            <li x-show="filtered.length === 0" class="text-center py-3" style="font-size: 12px; color: var(--c-fg-muted);">
                                Tidak ditemukan
                            </li>
                        </ul>
                    </div>
                </div>
                <small class="d-block mt-2" style="font-size: 11px; color: var(--c-fg-muted);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1" style="color: var(--c-warning);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Hanya masukkan <strong>angka</strong> tanpa spasi atau karakter khusus.
                </small>
            </div>

            <!-- Email Pribadi -->
            <div class="col-md-12">
                <label class="form-label-custom">Email Pribadi</label>
                <input type="email" name="personal_email" class="form-control form-control-custom @error('personal_email') is-invalid @enderror" value="{{ old('personal_email', $alumni->user->personal_email ?? '') }}" placeholder="nama@email.com">
                @error('personal_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="d-block mt-1" style="font-size: 11px; color: var(--c-fg-muted);">Email pribadi alumni (di luar email kampus).</small>
            </div>
        </div>

        <!-- Section: Karir -->
        <div class="section-divider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Informasi Karir
        </div>
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
            <div class="col-md-6">
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
                <label class="form-label-custom" id="label-perusahaan">Perusahaan / Instansi / Usaha</label>
                <input type="text" name="perusahaan" class="form-control form-control-custom @error('perusahaan') is-invalid @enderror" value="{{ old('perusahaan', $alumni->perusahaan) }}" id="input-perusahaan">
                @error('perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label-custom" id="label-jabatan">Jabatan / Posisi</label>
                <input type="text" name="jabatan" class="form-control form-control-custom @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $alumni->jabatan) }}" id="input-jabatan">
                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Tahun Mulai Bekerja</label>
                <input type="number" name="tahun_mulai_bekerja" class="form-control form-control-custom @error('tahun_mulai_bekerja') is-invalid @enderror" value="{{ old('tahun_mulai_bekerja', $alumni->tahun_mulai_bekerja) }}" min="2000" max="{{ date('Y') }}">
                @error('tahun_mulai_bekerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label-custom">LinkedIn URL</label>
                <input type="url" name="linkedin" class="form-control form-control-custom @error('linkedin') is-invalid @enderror" value="{{ old('linkedin', $alumni->linkedin) }}" placeholder="https://linkedin.com/in/username">
                @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end pt-4 mt-3" style="border-top: 1px solid var(--c-border);">
            <button type="submit" class="mk-btn mk-btn--primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('alumniPhoneCode', (defaultDial) => {
        const countries = [
            { flag: '🇦🇫', name: 'Afghanistan',       dial: '+93'  },
            { flag: '🇿🇦', name: 'Afrika Selatan',     dial: '+27'  },
            { flag: '🇺🇸', name: 'Amerika Serikat',    dial: '+1'   },
            { flag: '🇸🇦', name: 'Arab Saudi',         dial: '+966' },
            { flag: '🇦🇷', name: 'Argentina',          dial: '+54'  },
            { flag: '🇦🇺', name: 'Australia',          dial: '+61'  },
            { flag: '🇳🇱', name: 'Belanda',            dial: '+31'  },
            { flag: '🇧🇷', name: 'Brasil',             dial: '+55'  },
            { flag: '🇧🇳', name: 'Brunei',             dial: '+673' },
            { flag: '🇦🇪', name: 'Uni Emirat Arab',    dial: '+971' },
            { flag: '🇵🇭', name: 'Filipina',           dial: '+63'  },
            { flag: '🇮🇳', name: 'India',              dial: '+91'  },
            { flag: '🇮🇩', name: 'Indonesia',          dial: '+62'  },
            { flag: '🇬🇧', name: 'Inggris',            dial: '+44'  },
            { flag: '🇮🇹', name: 'Italia',             dial: '+39'  },
            { flag: '🇯🇵', name: 'Jepang',             dial: '+81'  },
            { flag: '🇩🇪', name: 'Jerman',             dial: '+49'  },
            { flag: '🇰🇭', name: 'Kamboja',            dial: '+855' },
            { flag: '🇨🇦', name: 'Kanada',             dial: '+1'   },
            { flag: '🇰🇷', name: 'Korea Selatan',      dial: '+82'  },
            { flag: '🇱🇦', name: 'Laos',               dial: '+856' },
            { flag: '🇲🇾', name: 'Malaysia',           dial: '+60'  },
            { flag: '🇲🇲', name: 'Myanmar',            dial: '+95'  },
            { flag: '🇵🇰', name: 'Pakistan',           dial: '+92'  },
            { flag: '🇫🇷', name: 'Prancis',            dial: '+33'  },
            { flag: '🇶🇦', name: 'Qatar',              dial: '+974' },
            { flag: '🇳🇿', name: 'Selandia Baru',      dial: '+64'  },
            { flag: '🇸🇬', name: 'Singapura',          dial: '+65'  },
            { flag: '🇹🇭', name: 'Thailand',           dial: '+66'  },
            { flag: '🇹🇷', name: 'Turki',              dial: '+90'  },
            { flag: '🇻🇳', name: 'Vietnam',            dial: '+84'  },
            { flag: '🇨🇳', name: 'China',              dial: '+86'  },
        ];

        const defaultCountry = countries.find(c => c.dial === defaultDial) ?? countries.find(c => c.dial === '+62');

        return {
            open: false,
            search: '',
            dropdownStyle: '',
            selected: defaultCountry,
            countries,
            get filtered() {
                if (!this.search) return this.countries;
                const q = this.search.toLowerCase();
                return this.countries.filter(c => c.name.toLowerCase().includes(q) || c.dial.includes(q));
            },
            toggle(triggerEl) {
                if (!this.open) {
                    const rect = triggerEl.getBoundingClientRect();
                    this.dropdownStyle = `top:${rect.bottom + 6}px;left:${rect.left}px;`;
                }
                this.open = !this.open;
                this.search = '';
            },
            select(c) {
                this.selected = c;
                this.open = false;
                this.search = '';
            }
        };
    });
});
</script>

{{-- Context-aware admin edit: relabel career fields berdasarkan status_karir (no hiding) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect    = document.querySelector('[name="status_karir"]');
    const labelPerusahaan = document.getElementById('label-perusahaan');
    const labelJabatan    = document.getElementById('label-jabatan');
    const inputPerusahaan = document.getElementById('input-perusahaan');
    const inputJabatan    = document.getElementById('input-jabatan');

    function relabelKarirFields() {
        if (!statusSelect) return;
        const status = statusSelect.value;

        if (status === 'studi_lanjut') {
            if (labelPerusahaan) labelPerusahaan.textContent = 'Universitas / Institusi';
            if (labelJabatan)    labelJabatan.textContent    = 'Program Studi Lanjut';
        } else if (status === 'wirausaha') {
            if (labelPerusahaan) labelPerusahaan.textContent = 'Nama Usaha';
            if (labelJabatan)    labelJabatan.textContent    = 'Posisi / Jabatan';
        } else {
            if (labelPerusahaan) labelPerusahaan.textContent = 'Perusahaan / Instansi / Usaha';
            if (labelJabatan)    labelJabatan.textContent    = 'Jabatan / Posisi';
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', relabelKarirFields);
        relabelKarirFields(); // Run on page load
    }
});
</script>
</x-dynamic-component>
