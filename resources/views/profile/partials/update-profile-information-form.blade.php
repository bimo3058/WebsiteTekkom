{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
@php
    $user         = auth()->user();
    $isSuperadmin = $user->hasRole('superadmin');
    $isAdmin      = $user->roles->contains(fn($r) => str_starts_with($r->name, 'admin_'));
    $canEditName  = $isSuperadmin || $isAdmin;

    $idValue = null;
    $idLabel = 'Nomor Identitas';
    if ($user->hasRole('mahasiswa') && $user->student) {
        $idValue = $user->student->student_number;
        $idLabel = 'NIM';
    } elseif ($user->hasRole('dosen') && $user->lecturer) {
        $idValue = $user->lecturer->employee_number;
        $idLabel = 'NIP / No. Karyawan';
    }

    // ── Parse kode negara + nomor lokal dari DB ──────────────────────────
    // Format tersimpan: +62812xxxxxxx atau +1234xxxxxxx
    $savedWa       = $user->whatsapp ?? '';
    $savedCode     = '+62';   // default
    $savedLocalNum = '';

    if ($savedWa !== '') {
        // Daftar kode yang mungkin, urutkan dari terpanjang agar tidak salah match
        $knownCodes = [
            '+1', '+7', '+20', '+27', '+30', '+31', '+32', '+33', '+34', '+36',
            '+39', '+40', '+41', '+43', '+44', '+45', '+46', '+47', '+48', '+49',
            '+51', '+52', '+53', '+54', '+55', '+56', '+57', '+58', '+60', '+61',
            '+62', '+63', '+64', '+65', '+66', '+81', '+82', '+84', '+86', '+90',
            '+91', '+92', '+93', '+94', '+95', '+98',
            '+212', '+213', '+216', '+218', '+220', '+221', '+222', '+223', '+224',
            '+225', '+226', '+227', '+228', '+229', '+230', '+231', '+232', '+233',
            '+234', '+235', '+236', '+237', '+238', '+239', '+240', '+241', '+242',
            '+243', '+244', '+245', '+246', '+247', '+248', '+249', '+250', '+251',
            '+252', '+253', '+254', '+255', '+256', '+257', '+258', '+260', '+261',
            '+262', '+263', '+264', '+265', '+266', '+267', '+268', '+269',
            '+355', '+356', '+357', '+358', '+359', '+370', '+371', '+372', '+373',
            '+374', '+375', '+376', '+377', '+378', '+380', '+381', '+382', '+383',
            '+385', '+386', '+387', '+389', '+420', '+421', '+423',
            '+592', '+593', '+594', '+595', '+596', '+597', '+598',
            '+673', '+674', '+675', '+676', '+677', '+678', '+679', '+680', '+681',
            '+682', '+683', '+685', '+686', '+687', '+688', '+689', '+690', '+691',
            '+692',
            '+850', '+852', '+853', '+855', '+856', '+880', '+886',
            '+960', '+961', '+962', '+963', '+964', '+965', '+966', '+967', '+968',
            '+970', '+971', '+972', '+973', '+974', '+975', '+976', '+977',
            '+992', '+993', '+994', '+995', '+996', '+998',
        ];

        // Urutkan dari yang terpanjang agar +855 tidak salah match ke +85
        usort($knownCodes, fn($a, $b) => strlen($b) - strlen($a));

        foreach ($knownCodes as $code) {
            if (str_starts_with($savedWa, $code)) {
                $savedCode     = $code;
                $savedLocalNum = substr($savedWa, strlen($code));
                break;
            }
        }

        // Fallback jika tidak ada yang cocok
        if ($savedLocalNum === '') {
            $savedLocalNum = ltrim($savedWa, '+0123456789');
        }
    }
@endphp

<form method="POST" action="{{ route('profile.update') }}" class="flex flex-col h-full space-y-5">
    @csrf
    @method('PATCH')

    <div class="flex-grow space-y-5">

        {{-- Nama Lengkap --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-[11px] font-bold text-slate-700 tracking-tight">Nama Lengkap</label>
                @if(!$canEditName)
                    <span class="text-[9px] text-amber-600 font-black uppercase tracking-tighter bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                        Data SSO · Read Only
                    </span>
                @endif
            </div>
            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                @if(!$canEditName) readonly @endif
                class="w-full rounded-xl border text-sm py-2.5 px-3 transition-all outline-none
                    {{ !$canEditName
                        ? 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed opacity-80'
                        : 'border-slate-200 bg-white text-slate-900 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] shadow-sm' }}"
            />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        {{-- Email + NIM/NIP --}}
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">Email Resmi (SSO)</label>
                <input type="email" value="{{ $user->email }}" readonly
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm py-2.5 px-3 cursor-not-allowed opacity-80" />
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">{{ $idLabel }}</label>
                <input type="text" value="{{ $idValue ?? '-' }}" readonly
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm py-2.5 px-3 cursor-not-allowed opacity-80" />
            </div>
        </div>

        <div class="h-px bg-slate-100"></div>

        {{-- WhatsApp + Email Pribadi --}}
        <div class="grid grid-cols-2 gap-5">

            {{-- WhatsApp dengan dropdown kode negara --}}
            <div x-data="phoneCode('{{ $savedCode }}')">
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">Nomor WhatsApp</label>

                <div class="flex border border-slate-200 rounded-xl bg-white shadow-sm focus-within:border-[#5E53F4] focus-within:ring-1 focus-within:ring-[#5E53F4] transition-all">
                    {{-- Trigger --}}
                    <button type="button"
                            @click.prevent="toggle($el)"
                            class="flex-shrink-0 flex items-center gap-1 px-3 py-2.5 bg-slate-50 border-r border-slate-200 hover:bg-slate-100 transition-colors text-[11px] font-bold text-slate-600 rounded-l-xl min-w-[82px]">
                        <span x-text="selected.flag" class="leading-none"></span>
                        <span x-text="selected.dial"></span>
                        <span class="material-symbols-outlined text-[15px] leading-none transition-transform duration-150"
                              :style="open ? 'transform:rotate(180deg)' : ''">expand_more</span>
                    </button>

                    {{-- Input nomor lokal --}}
                    <input type="tel" name="whatsapp"
                           value="{{ old('whatsapp', $savedLocalNum) }}"
                           placeholder="812xxxxxxx"
                           class="flex-1 text-sm py-2.5 px-3 outline-none bg-transparent text-slate-900 rounded-r-xl min-w-0" />

                    <input type="hidden" name="phone_code" :value="selected.dial">
                </div>

                {{-- Dropdown FIXED --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     @click.outside="open = false"
                     :style="dropdownStyle"
                     class="fixed w-60 bg-white border border-slate-200 rounded-xl shadow-2xl z-[9999] overflow-hidden"
                     style="display:none">
                    <div class="p-2 border-b border-slate-100 bg-slate-50">
                        <input type="text"
                               x-model="search"
                               @click.stop
                               placeholder="Cari negara..."
                               class="w-full text-[11px] border border-slate-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-[#5E53F4]" />
                    </div>
                    <ul class="max-h-52 overflow-y-auto py-1">
                        <template x-for="c in filtered" :key="c.name">
                            <li>
                                <button type="button"
                                        @click="select(c)"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-left hover:bg-slate-50 transition-colors"
                                        :class="selected.name === c.name ? 'bg-[#5E53F4]/5' : ''">
                                    <span x-text="c.flag" class="text-base leading-none flex-shrink-0"></span>
                                    <span x-text="c.name" class="text-[11px] text-slate-700 flex-1 truncate"></span>
                                    <span x-text="c.dial" class="text-[10px] font-bold text-slate-400 flex-shrink-0"></span>
                                </button>
                            </li>
                        </template>
                        <li x-show="filtered.length === 0" class="px-3 py-3 text-[11px] text-slate-400 text-center">
                            Tidak ditemukan
                        </li>
                    </ul>
                </div>

                <x-input-error class="mt-1" :messages="$errors->get('whatsapp')" />
            </div>

            {{-- Email Pribadi --}}
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">Email Pribadi</label>
                <input type="email" name="personal_email"
                       value="{{ old('personal_email', $user->personal_email ?? '') }}"
                       placeholder="email@pribadi.com"
                       class="w-full rounded-xl border border-slate-200 bg-white text-slate-900 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] text-sm py-2.5 px-3 shadow-sm transition-all outline-none" />
                <x-input-error class="mt-1" :messages="$errors->get('personal_email')" />
            </div>

        </div>

    </div>

    {{-- Action --}}
    <div class="mt-auto pt-5 flex flex-col items-end gap-3">
        @if(session('status') === 'profile-updated')
        <div class="flex items-center gap-2 text-emerald-700 text-[10px] font-bold bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            {{ __('Profil diperbarui') }}
        </div>
        @endif
        <button type="submit"
            class="bg-[#5E53F4] hover:bg-[#4e44e0] active:scale-[0.98] text-white font-bold text-[11px] uppercase tracking-widest px-8 py-2.5 rounded-xl transition-all shadow-sm shadow-[#5E53F4]/30">
            Simpan Perubahan
        </button>
    </div>

</form>

<script>
function phoneCode(defaultDial) {
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

    // Cari negara yang cocok dengan kode dari DB, fallback ke Indonesia
    const defaultCountry = countries.find(c => c.dial === defaultDial)
        ?? countries.find(c => c.dial === '+62');

    return {
        open:     false,
        search:   '',
        dropdownStyle: '',
        selected: defaultCountry,
        countries,
        get filtered() {
            if (!this.search) return this.countries;
            const q = this.search.toLowerCase();
            return this.countries.filter(c =>
                c.name.toLowerCase().includes(q) || c.dial.includes(q)
            );
        },
        toggle(triggerEl) {
            if (!this.open) {
                const rect = triggerEl.getBoundingClientRect();
                this.dropdownStyle = `top:${rect.bottom + 6}px;left:${rect.left}px;`;
            }
            this.open  = !this.open;
            this.search = '';
        },
        select(c) {
            this.selected = c;
            this.open     = false;
            this.search   = '';
        }
    };
}
</script>