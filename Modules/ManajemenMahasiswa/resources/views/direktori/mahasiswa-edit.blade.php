<x-dynamic-component :component="$layout">

<style>
    .form-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 16px;
        padding: 32px;
        max-width: 720px;
    }
    .form-title {
        font-size: 20px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .form-subtitle {
        font-size: 14px;
        color: #9ca3af;
        margin-bottom: 28px;
    }
    .form-label-custom {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-control-custom {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-select-custom {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-select-custom:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .btn-primary-custom {
        background: #4f46e5;
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
    .btn-primary-custom:hover {
        background: #4338ca;
    }
    .btn-outline-custom {
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #e5e7eb;
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
        background: #f8fafc;
        border-color: #d1d5db;
        color: #374151;
    }
</style>

<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="btn-outline-custom" style="font-size: 12px; padding: 6px 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali
    </a>
</div>

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: #fef2f2; color: #991b1b; font-size: 14px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">
    <div class="form-title">Edit Biodata Mahasiswa</div>
    <div class="form-subtitle">Perbarui informasi biodata mahasiswa: {{ $mhs->nama }}</div>

    <form method="POST" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.update', $mhs->id) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            @php
                $savedWa = old('kontak', $mhs->kontak ?? '');
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
            <!-- Nama -->
            <div class="col-12">
                <label class="form-label-custom">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama" class="form-control form-control-custom"
                       value="{{ old('nama', $mhs->nama) }}" required>
            </div>

            <!-- NIM -->
            <div class="col-md-6">
                <label class="form-label-custom">NIM <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nim" class="form-control form-control-custom"
                       value="{{ old('nim', $mhs->nim) }}" required>
            </div>

            <!-- Angkatan -->
            <div class="col-md-6">
                <label class="form-label-custom">Angkatan <span style="color: #ef4444;">*</span></label>
                <input type="number" name="angkatan" class="form-control form-control-custom"
                       value="{{ old('angkatan', $mhs->angkatan) }}" min="2000" max="2099" required>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label-custom">Status <span style="color: #ef4444;">*</span></label>
                <select name="status" class="form-select form-select-custom" required>
                    <option value="aktif" {{ old('status', $mhs->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="alumni" {{ old('status', $mhs->status) == 'alumni' ? 'selected' : '' }}>Lulus (Alumni)</option>
                    <option value="cuti" {{ old('status', $mhs->status) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="drop_out" {{ old('status', $mhs->status) == 'drop_out' ? 'selected' : '' }}>Drop Out</option>
                </select>
            </div>

            <!-- Tahun Lulus -->
            <div class="col-md-6">
                <label class="form-label-custom">Tahun Lulus</label>
                <input type="number" name="tahun_lulus" class="form-control form-control-custom"
                       value="{{ old('tahun_lulus', $mhs->tahun_lulus) }}" min="2000" max="2099"
                       placeholder="Kosongkan jika belum lulus">
            </div>

            <!-- Profesi -->
            <div class="col-md-6">
                <label class="form-label-custom">Profesi</label>
                <input type="text" name="profesi" class="form-control form-control-custom"
                       value="{{ old('profesi', $mhs->profesi) }}" placeholder="Opsional">
            </div>

            <!-- Kontak -->
            <div class="col-md-6" x-data="mhsPhoneCode('{{ $savedCode }}')">
                <label class="form-label-custom">Kontak</label>
                <div class="d-flex position-relative form-control-custom p-0" style="overflow: visible; background: #fff;">
                    <button type="button" @click.prevent="toggle($el)"
                            class="btn border-0 d-flex align-items-center gap-2" style="background: #f8fafc; border-right: 1.5px solid #e5e7eb !important; border-top-right-radius: 0; border-bottom-right-radius: 0; border-top-left-radius: 8.5px; border-bottom-left-radius: 8.5px;">
                        <span x-text="selected.flag" style="font-size: 15px;"></span>
                        <span x-text="selected.dial" style="font-size: 13px; font-weight: 600; color: #475569;"></span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             :style="open ? 'transform:rotate(180deg)' : ''" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <input type="text" name="kontak" class="form-control border-0 shadow-none"
                           value="{{ old('kontak', $localNum) }}" placeholder="8123456789" 
                           @input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                           style="background: transparent; font-size: 14px; font-weight: 600; color: #374151;">
                    <input type="hidden" name="phone_code" :value="selected.dial">

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false" :style="dropdownStyle"
                         class="position-fixed bg-white border rounded shadow-lg" style="display:none; width: 240px; z-index: 9999; border-radius: 12px !important; overflow: hidden;">
                        <div class="p-2 border-bottom" style="background: #f8fafc;">
                            <input type="text" x-model="search" @click.stop placeholder="Cari negara..." class="form-control form-control-sm" style="font-size: 12px; border-radius: 8px;">
                        </div>
                        <ul class="list-unstyled mb-0" style="max-height: 200px; overflow-y: auto;">
                            <template x-for="c in filtered" :key="c.name">
                                <li>
                                    <button type="button" @click="select(c)"
                                            class="w-100 btn text-start d-flex align-items-center gap-2 py-2 px-3 border-0 rounded-0"
                                            :style="selected.name === c.name ? 'background: #f1f5f9;' : 'background: #fff;'">
                                        <span x-text="c.flag" style="font-size: 15px;"></span>
                                        <span x-text="c.name" class="text-truncate flex-grow-1" style="font-size: 12px; color: #374151; font-weight: 500;"></span>
                                        <span x-text="c.dial" style="font-size: 11px; font-weight: 700; color: #9ca3af;"></span>
                                    </button>
                                </li>
                            </template>
                            <li x-show="filtered.length === 0" class="text-center py-3 text-muted" style="font-size: 12px;">
                                Tidak ditemukan
                            </li>
                        </ul>
                    </div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 11px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 text-warning"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Hanya masukkan <strong>angka</strong> tanpa spasi atau karakter khusus.
                </small>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex gap-3 mt-4 pt-3" style="border-top: 1px solid #f3f4f6;">
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
document.addEventListener('alpine:init', () => {
    Alpine.data('mhsPhoneCode', (defaultDial) => {
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
</x-dynamic-component>
