<x-dynamic-component :component="$layout">

@push('styles')
<style>
    .main-wrapper {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .back-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .btn-back {
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #374151;
        transition: all 0.2s;
    }
    .btn-back:hover { background: #f9fafb; color: #111827; }

    .edit-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 32px;
    }
    .section-divider {
        font-size: 15px;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eef2ff;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: #ffffff;
    }
    .btn-save {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        border: none;
        padding: 11px 28px;
        font-weight: 600;
        font-size: 14px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-save:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
</style>
@endpush

<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('manajemenmahasiswa.direktori.alumni.show', $alumni->id) }}" class="btn-back" style="font-size: 12px; padding: 6px 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Batal
    </a>
</div>

<div class="edit-card">
    <div class="mb-4">
        <h5 class="fw-bold mb-1" style="font-size: 20px; color: #1f2937;">Edit Data Alumni</h5>
        <p class="text-muted mb-0" style="font-size: 14px;">Admin — perbarui biodata dan karir alumni</p>
    </div>

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
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            Informasi Akademik
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">NIM</label>
                <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim', $alumni->nim) }}" required>
                @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Program Studi</label>
                <input type="text" name="program_studi" class="form-control @error('program_studi') is-invalid @enderror" value="{{ old('program_studi', $alumni->program_studi) }}">
                @error('program_studi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror" value="{{ old('angkatan', $alumni->angkatan) }}" required min="2000" max="2099">
                @error('angkatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tahun Lulus</label>
                <input type="number" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $alumni->tahun_lulus) }}" required min="2000" max="2099">
                @error('tahun_lulus') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Kontak -->
            <div class="col-md-12" x-data="alumniPhoneCode('{{ $savedCode }}')">
                <label class="form-label">Kontak / WhatsApp</label>
                <div class="d-flex position-relative p-0" style="overflow: visible; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px;">
                    <button type="button" @click.prevent="toggle($el)"
                            class="btn border-0 d-flex align-items-center gap-2" style="background: #f8fafc; border-right: 1.5px solid #e2e8f0 !important; border-top-right-radius: 0; border-bottom-right-radius: 0; border-top-left-radius: 8.5px; border-bottom-left-radius: 8.5px;">
                        <span x-text="selected.flag" style="font-size: 15px;"></span>
                        <span x-text="selected.dial" style="font-size: 13px; font-weight: 600; color: #475569;"></span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             :style="open ? 'transform:rotate(180deg)' : ''" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <input type="text" name="kontak" class="form-control border-0 shadow-none w-100"
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

        <!-- Section: Karir -->
        <div class="section-divider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Informasi Karir
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Status Karir</label>
                <select name="status_karir" class="form-select @error('status_karir') is-invalid @enderror">
                    <option value="">— Pilih Status —</option>
                    @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::STATUS_LABELS as $key => $label)
                        <option value="{{ $key }}" {{ old('status_karir', $alumni->status_karir) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status_karir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Bidang Industri</label>
                <select name="bidang_industri" class="form-select @error('bidang_industri') is-invalid @enderror">
                    <option value="">— Pilih Bidang —</option>
                    @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::BIDANG_INDUSTRI_LIST as $key => $label)
                        <option value="{{ $key }}" {{ old('bidang_industri', $alumni->bidang_industri) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('bidang_industri') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Perusahaan / Instansi / Usaha</label>
                <input type="text" name="perusahaan" class="form-control @error('perusahaan') is-invalid @enderror" value="{{ old('perusahaan', $alumni->perusahaan) }}">
                @error('perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Jabatan / Posisi</label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $alumni->jabatan) }}">
                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tahun Mulai Bekerja</label>
                <input type="number" name="tahun_mulai_bekerja" class="form-control @error('tahun_mulai_bekerja') is-invalid @enderror" value="{{ old('tahun_mulai_bekerja', $alumni->tahun_mulai_bekerja) }}" min="2000" max="{{ date('Y') + 1 }}">
                @error('tahun_mulai_bekerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="linkedin" class="form-control @error('linkedin') is-invalid @enderror" value="{{ old('linkedin', $alumni->linkedin) }}" placeholder="https://linkedin.com/in/username">
                @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end pt-4 mt-3" style="border-top: 1px solid #f1f5f9;">
            <button type="submit" class="btn-save">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</x-dynamic-component>
