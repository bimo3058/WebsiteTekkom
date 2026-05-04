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

    $savedWa       = $user->whatsapp ?? '';
    $savedCode     = '+62';
    $savedLocalNum = '';

    if ($savedWa !== '') {
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
        usort($knownCodes, fn($a, $b) => strlen($b) - strlen($a));
        foreach ($knownCodes as $code) {
            if (str_starts_with($savedWa, $code)) {
                $savedCode     = $code;
                $savedLocalNum = substr($savedWa, strlen($code));
                break;
            }
        }
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
                <label class="text-[12px] font-medium text-[#0D0D12]">Nama Lengkap</label>
                @if(!$canEditName)
                    <span class="text-[10px] text-[#956321] font-semibold bg-[#F9ECCB] border border-[#D39C3D]/30 px-2 py-0.5 rounded-full">
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
                        ? 'border-[#DFE1E7] bg-[#F6F8FA] text-[#A4ABB8] cursor-not-allowed'
                        : 'border-[#DFE1E7] bg-white text-[#0D0D12] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E]' }}"
            />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        {{-- Email + NIM/NIP --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">Email Resmi (SSO)</label>
                <input type="email" value="{{ $user->email }}" readonly
                    class="w-full rounded-xl border border-[#DFE1E7] bg-[#F6F8FA] text-[#A4ABB8] text-sm py-2.5 px-3 cursor-not-allowed" />
            </div>
            <div>
                <label class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">{{ $idLabel }}</label>
                <input type="text" value="{{ $idValue ?? '-' }}" readonly
                    class="w-full rounded-xl border border-[#DFE1E7] bg-[#F6F8FA] text-[#A4ABB8] text-sm py-2.5 px-3 cursor-not-allowed" />
            </div>
        </div>

        <div class="h-px bg-[#F0F1F4]"></div>

        {{-- WhatsApp + Email Pribadi --}}
        <div class="grid grid-cols-2 gap-4">

            {{-- WhatsApp dengan dropdown kode negara --}}
            <div x-data="phoneCode('{{ $savedCode }}')">
                <label class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">Nomor WhatsApp</label>

                <div class="flex border border-[#DFE1E7] rounded-xl bg-white focus-within:border-[#0B266E] focus-within:ring-1 focus-within:ring-[#0B266E] transition-all">
                    <button type="button"
                            @click.prevent="toggle($el)"
                            class="flex-shrink-0 flex items-center gap-1 px-3 py-2.5 bg-[#F6F8FA] border-r border-[#DFE1E7] hover:bg-[#E8EDF7] transition-colors text-[11px] font-semibold text-[#353849] rounded-l-xl min-w-[82px]">
                        <span x-text="selected.flag" class="leading-none"></span>
                        <span x-text="selected.dial"></span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2" stroke-linecap="round"
                             :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.15s">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <input type="tel" name="whatsapp"
                           value="{{ old('whatsapp', $savedLocalNum) }}"
                           placeholder="812xxxxxxx"
                           class="flex-1 text-sm py-2.5 px-3 outline-none bg-transparent text-[#0D0D12] rounded-r-xl min-w-0" />
                    <input type="hidden" name="phone_code" :value="selected.dial">
                </div>

                {{-- Dropdown --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     @click.outside="open = false"
                     :style="dropdownStyle"
                     class="fixed w-60 bg-white border border-[#DFE1E7] rounded-xl shadow-[0_12px_20px_-4px_rgba(22,22,43,0.08)] z-[9999] overflow-hidden"
                     style="display:none">
                    <div class="p-2 border-b border-[#F0F1F4] bg-[#F6F8FA]">
                        <input type="text"
                               x-model="search"
                               @click.stop
                               placeholder="Cari negara..."
                               class="w-full text-[12px] border border-[#DFE1E7] rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-[#0B266E]" />
                    </div>
                    <ul class="max-h-52 overflow-y-auto py-1">
                        <template x-for="c in filtered" :key="c.name">
                            <li>
                                <button type="button"
                                        @click="select(c)"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-left hover:bg-[#E8EDF7] transition-colors"
                                        :class="selected.name === c.name ? 'bg-[#E8EDF7]' : ''">
                                    <span x-text="c.flag" class="text-base leading-none flex-shrink-0"></span>
                                    <span x-text="c.name" class="text-[12px] text-[#353849] flex-1 truncate"></span>
                                    <span x-text="c.dial" class="text-[10px] font-semibold text-[#A4ABB8] flex-shrink-0"></span>
                                </button>
                            </li>
                        </template>
                        <li x-show="filtered.length === 0" class="px-3 py-3 text-[12px] text-[#A4ABB8] text-center">
                            Tidak ditemukan
                        </li>
                    </ul>
                </div>

                <x-input-error class="mt-1" :messages="$errors->get('whatsapp')" />
            </div>

            {{-- Email Pribadi --}}
            <div>
                <label class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">Email Pribadi</label>
                <input type="email" name="personal_email"
                       value="{{ old('personal_email', $user->personal_email ?? '') }}"
                       placeholder="email@pribadi.com"
                       class="w-full rounded-xl border border-[#DFE1E7] bg-white text-[#0D0D12] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] text-sm py-2.5 px-3 transition-all outline-none" />
                <x-input-error class="mt-1" :messages="$errors->get('personal_email')" />
            </div>

        </div>

    </div>

    {{-- Action --}}
    <div class="mt-auto pt-5 flex flex-col items-end gap-3">
        @if(session('status') === 'profile-updated')
        <div class="flex items-center gap-2 text-[#287F6E] text-[12px] font-medium bg-[#DDF2EE] border border-[#40C4AA]/30 rounded-lg px-3 py-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ __('Profil diperbarui') }}
        </div>
        @endif
        <button type="submit"
            class="bg-[#0B266E] hover:bg-[#091958] active:bg-[#071742] text-white font-semibold text-[13px] px-8 py-2.5 rounded-xl transition-all">
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

    const defaultCountry = countries.find(c => c.dial === defaultDial)
        ?? countries.find(c => c.dial === '+62');

    return {
        open: false, search: '', dropdownStyle: '',
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
            this.open   = !this.open;
            this.search = '';
        },
        select(c) { this.selected = c; this.open = false; this.search = ''; }
    };
}
</script>
