<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')

<style>
    /* Samakan shell card dengan form Edit Alumni. */
    .edit-card {
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 32px;
        width: 100%;
        box-shadow: var(--shadow-card);
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
    .form-label-custom {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-fg-sec);
        margin-bottom: 6px;
    }
    .form-control-custom {
        border: 1.5px solid var(--c-border);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: var(--c-fg);
        background: #ffffff;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-subtle);
    }
    .form-select-custom {
        border: 1.5px solid var(--c-border);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: var(--c-fg);
        background: #ffffff;
        transition: all 0.2s;
    }
    .form-select-custom:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-subtle);
    }
    .btn-primary-custom {
        background: var(--c-primary);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    /* Hover sama dengan tombol Simpan di Edit Alumni & tombol "Permissions" global */
    .btn-primary-custom:hover {
        background: var(--c-primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(11, 38, 110, 0.3);
    }
    /* Tombol sekunder: sama dengan tombol outline "Audit Logs"/"Users" di dashboard global
       dan tombol Batal di Edit Alumni. */
    .btn-outline-custom {
        background: #fff;
        color: var(--c-fg-sec);
        border: 1px solid var(--c-border);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-outline-custom:hover {
        background: var(--c-bg);
        border-color: var(--c-border-strong);
        color: var(--c-fg);
    }
</style>

<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="detail-back" title="Kembali" aria-label="Kembali">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        <span class="detail-back-label">Kembali</span>
    </a>
</div>

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: var(--c-error-subtle); color: var(--c-error-200); font-size: 14px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="edit-card">
    <div class="mb-4">
        <h5 class="fw-bold mb-1" style="font-size: 20px; color: var(--c-fg);">{{ ($isAdmin ?? false) ? 'Edit Biodata Mahasiswa' : 'Edit Data Saya' }}</h5>
        <p class="mb-0" style="font-size: 14px; color: var(--c-fg-muted);">
        @if($isAdmin ?? false)
            Perbarui informasi biodata mahasiswa: {{ $mhs->nama }}
        @else
            Perbarui data diri Anda pada direktori mahasiswa.
        @endif
        </p>
    </div>

    <form method="POST" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.update', $mhs->id) }}"
          id="formEditBiodata" data-status-awal="{{ $mhs->status }}">
        @csrf
        @method('PUT')

        <div class="section-divider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            Informasi Akademik
        </div>

        <div class="row g-3">
            @php
                // Prioritaskan users.whatsapp (sumber kanonik dari Settings global)
                // Fallback ke mk_kemahasiswaan.kontak jika belum tersinkron
                $savedWa = old('kontak', $mhs->user?->whatsapp ?? $mhs->kontak ?? '');
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
            {{-- Nama, NIM, dan Angkatan dikunci dengan `disabled` (bukan sekadar `readonly`)
                 agar benar-benar tidak ikut terkirim saat form disimpan. Sisi server juga
                 sudah berhenti menerima ketiga field ini. --}}
            <!-- Nama -->
            <div class="col-12">
                <label class="form-label-custom">Nama Lengkap</label>
                <input type="text" class="form-control form-control-custom"
                       value="{{ $mhs->nama }}" disabled style="background-color: var(--c-grey-50); cursor: not-allowed; opacity: 0.7;">
            </div>

            <!-- NIM -->
            <div class="col-md-6">
                <label class="form-label-custom">NIM</label>
                <input type="text" class="form-control form-control-custom"
                       value="{{ $mhs->nim }}" disabled style="background-color: var(--c-grey-50); cursor: not-allowed; opacity: 0.7;">
            </div>

            <!-- Angkatan -->
            <div class="col-md-6">
                <label class="form-label-custom">Angkatan</label>
                <input type="number" class="form-control form-control-custom"
                       value="{{ $mhs->angkatan }}" disabled style="background-color: var(--c-grey-50); cursor: not-allowed; opacity: 0.7;">
            </div>

            <div class="col-12">
                <small class="d-block" style="font-size: 11px; color: var(--c-fg-muted);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Nama, NIM, dan Angkatan mengikuti data <strong>SSO UNDIP</strong> dan tidak dapat diubah dari sini.
                </small>
            </div>

            {{-- Status hanya milik pengelola: aktif/cuti/DO/alumni adalah keputusan
                 akademik, bukan data yang ditetapkan sendiri oleh mahasiswa. Blok ini
                 benar-benar tidak dirender (bukan disembunyikan dengan CSS), dan sisi
                 server juga tidak menerima field `status` dari pemilik. --}}
            @if($isAdmin ?? false)
            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label-custom">Status <span style="color: var(--c-error);">*</span></label>
                <select name="status" id="statusSelect" class="form-select form-select-custom" required>
                    <option value="aktif" {{ old('status', $mhs->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="cuti" {{ old('status', $mhs->status) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    {{-- Opsi "Alumni" sebelumnya tidak ada. Akibatnya, saat admin membuka form
                         ini untuk mahasiswa yang sudah berstatus alumni, dropdown otomatis
                         jatuh ke opsi pertama (Aktif) dan sekali klik Simpan datanya terhapus
                         dari Direktori Alumni tanpa disadari. --}}
                    <option value="alumni" {{ old('status', $mhs->status) == 'alumni' ? 'selected' : '' }}>Alumni (Lulus)</option>
                    <option value="drop_out" {{ old('status', $mhs->status) == 'drop_out' ? 'selected' : '' }}>Drop Out</option>
                    <option value="pindah_studi" {{ old('status', $mhs->status) == 'pindah_studi' ? 'selected' : '' }}>Pindah Studi</option>
                    <option value="wafat" {{ old('status', $mhs->status) == 'wafat' ? 'selected' : '' }}>Wafat</option>
                    <option value="mangkir" {{ old('status', $mhs->status) == 'mangkir' ? 'selected' : '' }}>Mangkir</option>
                </select>
                <small class="d-block mt-1" style="font-size: 11px; color: var(--c-fg-muted);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1" style="color: var(--c-warning);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Memilih <strong>Alumni (Lulus)</strong> memindahkan mahasiswa ini ke <strong>Direktori Alumni</strong>.
                </small>
            </div>
            @endif

            <!-- IPK -->
            <div class="col-md-6">
                <label class="form-label-custom">IPK</label>
                <input type="number" name="ipk" class="form-control form-control-custom"
                       value="{{ old('ipk', $mhs->ipk) }}" min="0" max="4" step="0.01"
                       placeholder="Contoh: 3.75">
                <small class="d-block mt-1" style="font-size: 11px; color: var(--c-fg-muted);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1" style="color: var(--c-warning);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Masukkan nilai IPK antara <strong>0.00</strong> hingga <strong>4.00</strong>.
                </small>
            </div>

            <!-- Email Pribadi -->
            <div class="col-md-6">
                <label class="form-label-custom">Email Pribadi</label>
                <input type="email" name="email_pribadi" class="form-control form-control-custom"
                       value="{{ old('email_pribadi', $mhs->user?->personal_email) }}"
                       maxlength="100"
                       placeholder="contoh@gmail.com">
                <small class="d-block mt-1" style="font-size: 11px; color: var(--c-fg-muted);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1" style="color: var(--c-warning);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Akan tersinkron ke profil global mahasiswa.
                </small>
            </div>

            <!-- Kontak -->
            <div class="col-md-6" x-data="mhsPhoneCode('{{ $savedCode }}')">
                <label class="form-label-custom">Kontak</label>
                <div class="d-flex position-relative form-control-custom p-0" style="overflow: visible; background: #fff;">
                    <button type="button" @click.prevent="toggle($el)"
                            class="btn border-0 d-flex align-items-center gap-2" style="background: var(--c-grey-0); color: var(--c-fg-sec); border-right: 1.5px solid var(--c-border) !important; border-top-right-radius: 0; border-bottom-right-radius: 0; border-top-left-radius: 8.5px; border-bottom-left-radius: 8.5px;">
                        <span x-text="selected.dial" style="font-size: 13px; font-weight: 600; color: var(--c-fg-sec);"></span>
                        {{-- Warna panah diwarisi dari tombol: :style Alpine menimpa seluruh atribut style SVG ini. --}}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             :style="open ? 'transform:rotate(180deg)' : ''" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <input type="text" name="kontak" inputmode="numeric" maxlength="15" class="form-control border-0 shadow-none"
                           value="{{ old('kontak', $localNum) }}" placeholder="8123456789"
                           @input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 15)"
                           style="background: transparent; font-size: 14px; font-weight: 600; color: var(--c-fg);">
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
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-end gap-3 mt-4 pt-3" style="border-top: 1px solid var(--c-border);">
            <button type="submit" class="btn-primary-custom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="btn-outline-custom">
                Batal
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Pengaman terakhir: memindahkan mahasiswa KELUAR dari status Alumni akan menghapus
// datanya di Direktori Alumni, jadi minta konfirmasi eksplisit lebih dulu.
document.getElementById('formEditBiodata')?.addEventListener('submit', function (e) {
    const statusAwal = this.dataset.statusAwal;
    const select     = this.querySelector('#statusSelect');

    if (statusAwal !== 'alumni' || !select || select.value === 'alumni') {
        return;
    }

    const labelBaru = select.options[select.selectedIndex].text;
    const lanjut = window.confirm(
        'Mahasiswa ini berstatus ALUMNI.\n\n' +
        'Mengubah status menjadi "' + labelBaru + '" akan menghapus datanya dari Direktori Alumni.\n\n' +
        'Lanjutkan menyimpan?'
    );

    if (!lanjut) {
        e.preventDefault();
    }
});

document.addEventListener('alpine:init', () => {
    Alpine.data('mhsPhoneCode', (defaultDial) => {
        const countries = [
            { name: 'Afghanistan',       dial: '+93'  },
            { name: 'Afrika Selatan',     dial: '+27'  },
            { name: 'Amerika Serikat',    dial: '+1'   },
            { name: 'Arab Saudi',         dial: '+966' },
            { name: 'Argentina',          dial: '+54'  },
            { name: 'Australia',          dial: '+61'  },
            { name: 'Belanda',            dial: '+31'  },
            { name: 'Brasil',             dial: '+55'  },
            { name: 'Brunei',             dial: '+673' },
            { name: 'Uni Emirat Arab',    dial: '+971' },
            { name: 'Filipina',           dial: '+63'  },
            { name: 'India',              dial: '+91'  },
            { name: 'Indonesia',          dial: '+62'  },
            { name: 'Inggris',            dial: '+44'  },
            { name: 'Italia',             dial: '+39'  },
            { name: 'Jepang',             dial: '+81'  },
            { name: 'Jerman',             dial: '+49'  },
            { name: 'Kamboja',            dial: '+855' },
            { name: 'Kanada',             dial: '+1'   },
            { name: 'Korea Selatan',      dial: '+82'  },
            { name: 'Laos',               dial: '+856' },
            { name: 'Malaysia',           dial: '+60'  },
            { name: 'Myanmar',            dial: '+95'  },
            { name: 'Pakistan',           dial: '+92'  },
            { name: 'Prancis',            dial: '+33'  },
            { name: 'Qatar',              dial: '+974' },
            { name: 'Selandia Baru',      dial: '+64'  },
            { name: 'Singapura',          dial: '+65'  },
            { name: 'Thailand',           dial: '+66'  },
            { name: 'Turki',              dial: '+90'  },
            { name: 'Vietnam',            dial: '+84'  },
            { name: 'China',              dial: '+86'  },
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
</x-dynamic-component>
