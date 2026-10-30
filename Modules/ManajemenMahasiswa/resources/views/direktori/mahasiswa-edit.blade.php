<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.sitkom-ui')

{{--
    Satu halaman, dua wajah (aturannya ada di KemahasiswaanPolicy):
      $sebagaiPengelola = true  → pengelola membuka baris ORANG LAIN: Kontak (email pribadi,
                                  WhatsApp), tercatat di audit log.
      $sebagaiPengelola = false → pemilik baris (termasuk pengelola yang membuka barisnya
                                  sendiri): hanya data pribadinya, yaitu email pribadi dan WhatsApp.
    Nama, NIM, dan Angkatan milik SSO dan hanya tampil sebagai teks di subjudul.
    Status kemahasiswaan tidak lagi diubah di sini (S-53, 29 Okt 2026): tidak bisa disinkronkan
    dengan SIAP Undip. Menjadikan alumni hanya lewat Permission.
--}}
@php
    $sebagaiPengelola = $sebagaiPengelola ?? false;

    // Prioritaskan users.whatsapp (sumber kanonik dari Settings global)
    // Fallback ke mk_kemahasiswaan.kontak jika belum tersinkron
    $savedWa = $mhs->user?->whatsapp ?? $mhs->kontak ?? '';
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

<style>
    /* Bagian form bergaya Edit User SITKOM: bingkai tipis sudut 10px, kepala bagian
       berjudul 14px/600 + satu kalimat penjelas, isian dalam grid dua kolom. */
    .mm-sec {
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 10px;
        margin-bottom: 16px;
    }
    .mm-sec-head {
        padding: 12px 16px;
        border-bottom: 1px solid var(--c-border);
    }
    .mm-sec-head h2 {
        font-size: 14px;
        font-weight: 600;
        color: var(--c-fg);
        margin: 0 0 4px;
    }
    .mm-sec-head p {
        font-size: 12px;
        color: var(--c-fg-muted);
        line-height: 1.5;
        margin: 0;
    }
    .mm-sec-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 20px;
        padding: 16px;
    }
    .mm-hint {
        margin: 6px 0 0;
        font-size: 12px;
        line-height: 1.5;
        color: var(--c-fg-muted);
    }
    @media (max-width: 767px) {
        .mm-sec-fields { grid-template-columns: 1fr; padding: 12px; gap: 12px; }
    }
</style>

<x-manajemenmahasiswa::ui.page-header bordered :title="$sebagaiPengelola ? 'Edit Data Mahasiswa' : 'Edit Data Saya'">
    {{ $mhs->nama }} · {{ $mhs->nim }} · Angkatan {{ $mhs->angkatan }}
    @unless($sebagaiPengelola)
        — data dari SSO, tidak dapat diubah.
    @endunless
    <x-slot:actions>
        <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="mk-btn mk-btn--secondary mk-btn--sm">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
            Kembali
        </a>
        <button type="submit" form="formEditBiodata" id="btnSimpan" class="mk-btn mk-btn--primary mk-btn--sm">Simpan Perubahan</button>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<x-manajemenmahasiswa::ui.flash type="error" :messages="$errors->all()" class="mb-3" />

<form method="POST" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.update', $mhs->id) }}"
      id="formEditBiodata"
      data-email-awal="{{ $mhs->user?->personal_email }}" data-kontak-awal="{{ $localNum }}" data-kode-awal="{{ $savedCode }}">
    @csrf
    @method('PUT')

    <div class="mm-sec">
        <div class="mm-sec-head">
            @if($sebagaiPengelola)
                <h2>Kontak</h2>
                <p>Isi atau perbaiki bila mahasiswa melapor perubahan atau datanya belum terisi. Perubahan ikut tersinkron ke profil global dan CV mahasiswa.</p>
            @else
                <h2>Data Pribadi</h2>
                <p>Email pribadi dan WhatsApp diubah oleh pemiliknya sendiri, lalu tersinkron ke profil global dan CV.</p>
            @endif
        </div>

        <div class="mm-sec-fields">
            <div>
                <label class="form-label-custom" for="emailPribadi">Email Pribadi</label>
                <input type="email" id="emailPribadi" name="email_pribadi"
                       class="form-control form-control-custom @error('email_pribadi') is-invalid @enderror"
                       value="{{ old('email_pribadi', $mhs->user?->personal_email) }}"
                       maxlength="254" placeholder="contoh@gmail.com">
            </div>

            <div x-data="mhsPhoneCode('{{ old('phone_code', $savedCode) }}')">
                <label class="form-label-custom" for="kontakInput">WhatsApp/Telepon</label>
                <div class="d-flex position-relative form-control-custom p-0" style="overflow: visible; background: #fff;">
                    <button type="button" @click.prevent="toggle($el)"
                            class="btn border-0 d-flex align-items-center gap-2" style="background: var(--c-grey-0); color: var(--c-fg-sec); border-right: 1px solid var(--c-border-strong) !important; border-top-right-radius: 0; border-bottom-right-radius: 0; border-top-left-radius: 7px; border-bottom-left-radius: 7px;">
                        <span x-text="selected.dial" style="font-size: 13px; font-weight: 500; color: var(--c-fg-sec);"></span>
                        {{-- Warna panah diwarisi dari tombol: :style Alpine menimpa seluruh atribut style SVG ini. --}}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             :style="open ? 'transform:rotate(180deg)' : ''" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <input type="text" id="kontakInput" name="kontak" inputmode="numeric" maxlength="15" class="form-control border-0 shadow-none"
                           value="{{ old('kontak', $localNum) }}" placeholder="8123456789"
                           @input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 15)"
                           style="background: transparent; font-size: 13px; font-weight: 400; color: var(--c-fg);">
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
                <p class="mm-hint">Hanya masukkan <strong>angka</strong> tanpa spasi atau karakter khusus.</p>
            </div>
        </div>
    </div>

</form>

<script>
(function () {
    const form       = document.getElementById('formEditBiodata');
    const btnSimpan  = document.getElementById('btnSimpan');
    const awal       = form.dataset;

    // Simpan baru aktif setelah kontak berbeda dari yang tersimpan.
    function sinkron() {
        const nomor = form.elements.kontak.value;
        const kode  = form.elements.phone_code.value || awal.kodeAwal;
        const kontakBerubah = form.elements.email_pribadi.value.trim() !== awal.emailAwal
            || nomor !== awal.kontakAwal
            || (nomor !== '' && kode !== awal.kodeAwal);

        btnSimpan.disabled = !kontakBerubah;
    }
    form.addEventListener('input', sinkron);
    form.addEventListener('change', sinkron);
    sinkron();
})();

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
                // Kode negara tersimpan di input tersembunyi yang tidak memicu event sendiri;
                // beri tahu form agar tombol Simpan ikut menilai ulang perubahan.
                this.$nextTick(() => document.getElementById('formEditBiodata').dispatchEvent(new Event('change')));
            }
        };
    });
});
</script>
</x-dynamic-component>
