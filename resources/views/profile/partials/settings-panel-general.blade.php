{{-- profile/partials/settings-panel-general.blade.php --}}
@php
    $user         = auth()->user();
    $name         = $user->name;
    $initials     = strtoupper(substr($name, 0, 1));
    $sp           = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));

    $isSuperadmin = $user->hasRole('superadmin');
    $isAdmin      = $user->roles->contains(fn($r) => str_starts_with($r->name, 'admin_'));

    $idValue = null; $idLabel = 'Nomor Identitas';
    if ($user->hasRole('mahasiswa') && $user->student) {
        $idValue = $user->student->student_number; $idLabel = 'NIM';
    } elseif ($user->hasRole('dosen') && $user->lecturer) {
        $idValue = $user->lecturer->employee_number; $idLabel = 'NIP / No. Karyawan';
    }

    $roleLabel = $user->roles->first()?->name ?? 'User';
    $roleColor = match(true) {
        $isSuperadmin               => 'bg-red-100 text-red-700',
        $user->hasRole('dosen')     => 'bg-purple-100 text-purple-700',
        $user->hasRole('mahasiswa') => 'bg-blue-100 text-blue-700',
        default                     => 'bg-amber-100 text-amber-700',
    };

    $savedWa = $user->whatsapp ?? '';
    $savedCode = '+62'; $savedLocalNum = '';
    if ($savedWa !== '') {
        $knownCodes = ['+1','+7','+20','+27','+30','+31','+32','+33','+34','+36','+39','+40','+41','+43','+44','+45','+46','+47','+48','+49','+51','+52','+53','+54','+55','+56','+57','+58','+60','+61','+62','+63','+64','+65','+66','+81','+82','+84','+86','+90','+91','+92','+93','+94','+95','+98','+212','+213','+216','+218','+220','+221','+222','+223','+224','+225','+226','+227','+228','+229','+230','+231','+232','+233','+234','+235','+236','+237','+238','+239','+240','+241','+242','+243','+244','+245','+246','+247','+248','+249','+250','+251','+252','+253','+254','+255','+256','+257','+258','+260','+261','+262','+263','+264','+265','+266','+267','+268','+269','+355','+356','+357','+358','+359','+370','+371','+372','+373','+374','+375','+376','+377','+378','+380','+381','+382','+383','+385','+386','+387','+389','+420','+421','+423','+592','+593','+594','+595','+596','+597','+598','+673','+674','+675','+676','+677','+678','+679','+680','+681','+682','+683','+685','+686','+687','+688','+689','+690','+691','+692','+850','+852','+853','+855','+856','+880','+886','+960','+961','+962','+963','+964','+965','+966','+967','+968','+970','+971','+972','+973','+974','+975','+976','+977','+992','+993','+994','+995','+996','+998'];
        usort($knownCodes, fn($a,$b) => strlen($b)-strlen($a));
        foreach ($knownCodes as $code) {
            if (str_starts_with($savedWa, $code)) { $savedCode = $code; $savedLocalNum = substr($savedWa, strlen($code)); break; }
        }
        if ($savedLocalNum === '') $savedLocalNum = ltrim($savedWa, '+0123456789');
    }
@endphp

<p class="panel-title">Profil Akun</p>
<p class="panel-sub">Kelola identitas dan keamanan akses Anda</p>

{{-- ── Avatar + Data SSO (read-only) ── --}}
<div style="display:flex;align-items:flex-start;gap:20px;padding-bottom:20px;border-bottom:1px solid #f1f5f9;margin-bottom:20px;">

    {{-- Avatar --}}
    <div style="position:relative;flex-shrink:0;">
        <div style="width:60px;height:60px;border-radius:50%;overflow:hidden;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;box-shadow:0 1px 3px rgba(0,0,0,.08);" class="{{ $roleColor }}">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" id="currentAvatar" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
            @else
                <span id="avatarInitials">{{ $initials }}</span>
            @endif
        </div>
        <button type="button" onclick="openManagePhotoModal()"
            style="position:absolute;bottom:-2px;right:-2px;width:22px;height:22px;background:#fff;border:1px solid #e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,.1);transition:border-color .15s;"
            onmouseover="this.style.borderColor='#5E53F4'" onmouseout="this.style.borderColor='#e2e8f0'">
            <span class="material-symbols-outlined" style="font-size:13px;">photo_camera</span>
        </button>
    </div>

    {{-- Data SSO read-only --}}
    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;">{{ $user->name }}</p>
            <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:2px 8px;border-radius:6px;" class="{{ $roleColor }}">{{ $roleLabel }}</span>
            <span style="font-size:10px;font-weight:600;color:#d97706;background:#fefce8;border:1px solid #fde68a;padding:2px 7px;border-radius:6px;display:inline-flex;align-items:center;gap:3px;">
                <span class="material-symbols-outlined" style="font-size:11px;">lock</span> Data SSO · Read Only
            </span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
                <label class="field-label" style="margin-bottom:4px;">Nama Lengkap</label>
                <input type="text" value="{{ $user->name }}" readonly class="field-input readonly">
            </div>
            <div>
                <label class="field-label" style="margin-bottom:4px;">Email Resmi (SSO)</label>
                <input type="email" value="{{ $user->email }}" readonly class="field-input readonly">
            </div>
            @if($idValue)
            <div>
                <label class="field-label" style="margin-bottom:4px;">{{ $idLabel }}</label>
                <input type="text" value="{{ $idValue }}" readonly class="field-input readonly">
            </div>
            @endif
            @if($user->hasRole('mahasiswa') && $user->student)
            <div>
                <label class="field-label" style="margin-bottom:4px;">Angkatan</label>
                <input type="text" value="{{ $user->student->cohort_year }}" readonly class="field-input readonly">
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Data yang dapat diubah ── --}}
<p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:14px;">Data yang dapat diubah</p>

<form method="POST" action="{{ route('profile.update') }}" id="form-general">
    @csrf @method('PATCH')
    <input type="hidden" name="name" value="{{ old('name', $user->name) }}">

    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Nomor Telepon (kiri) + Email Pribadi (kanan) --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            {{-- Nomor Telepon --}}
            <div x-data="phoneCode('{{ $savedCode }}')" style="position: relative;">
                <label class="field-label">Nomor Telepon</label>
                <div style="display:flex;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff;transition:border-color .15s,box-shadow .15s;"
                    onfocusin="this.style.borderColor='#5E53F4';this.style.boxShadow='0 0 0 3px rgba(94,83,244,.08)'"
                    onfocusout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    <button type="button" @click.prevent="toggle()"
                        style="flex-shrink:0;display:flex;align-items:center;gap:6px;padding:9px 12px;background:#f8fafc;border-right:1px solid #e2e8f0;font-size:13px;font-weight:500;color:#374151;cursor:pointer;border:none;min-width:88px;transition:background .15s;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <span x-text="selected.flag"></span>
                        <span x-text="selected.dial" style="font-size:12px;"></span>
                        <span class="material-symbols-outlined" style="font-size:14px;" :style="open?'transform:rotate(180deg)':''">expand_more</span>
                    </button>
                    <input type="tel" name="whatsapp" value="{{ old('whatsapp', $savedLocalNum) }}" placeholder="8123456789"
                        style="flex:1;font-size:13px;padding:9px 12px;outline:none;background:transparent;color:#0f172a;min-width:0;border:none;">
                    <input type="hidden" name="phone_code" :value="selected.dial">
                </div>
                
                {{-- Dropdown (Ubah ke absolute) --}}
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                    @click.outside="open=false"
                    style="display:none;position:absolute;top:100%;left:0;margin-top:6px;width:240px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.12);z-index:9999;overflow:hidden;">
                    <div style="padding:8px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
                        <input type="text" x-model="search" @click.stop placeholder="Cari negara..."
                            style="width:100%;font-size:12px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;outline:none;box-sizing:border-box;">
                    </div>
                    <ul style="max-height:200px;overflow-y:auto;padding:4px 0;margin:0;list-style:none;">
                        <template x-for="c in filtered" :key="c.name">
                            <li>
                                <button type="button" @click="select(c)"
                                    style="width:100%;display:flex;align-items:center;gap:10px;padding:8px 12px;text-align:left;background:transparent;border:none;cursor:pointer;font-size:12px;"
                                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <span x-text="c.flag" style="font-size:15px;flex-shrink:0;"></span>
                                    <span x-text="c.name" style="color:#374151;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                                    <span x-text="c.dial" style="color:#94a3b8;font-weight:700;font-size:11px;flex-shrink:0;"></span>
                                </button>
                            </li>
                        </template>
                        <li x-show="filtered.length===0" style="padding:12px;text-align:center;font-size:12px;color:#94a3b8;">Tidak ditemukan</li>
                    </ul>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('whatsapp')" />
            </div>

            {{-- Email Pribadi --}}
            <div>
                <label class="field-label">Email Pribadi</label>
                <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email ?? '') }}" placeholder="email@pribadi.com" class="field-input">
                <x-input-error class="mt-1" :messages="$errors->get('personal_email')" />
            </div>

        </div>

        @if(session('status') === 'profile-updated')
        <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:13px;color:#15803d;">
            <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
            Profil berhasil diperbarui.
        </div>
        @endif

    </div>
</form>

{{-- ── CV Builder — menyatu, rata kanan ── --}}
@if($user->hasAnyRole(['mahasiswa', 'alumni']))
<div style="margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;">
    <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:14px;">CV Builder</p>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
            <p style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:2px;display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:16px;color:#5E53F4;">description</span>
                CV Builder Cerdas
            </p>
            <p style="font-size:12px;color:#64748b;line-height:1.5;">Buat CV profesional semi-otomatis dari data profil & akademik Anda.</p>
        </div>
        <div style="display:flex;flex-direction:row;gap:8px;flex-shrink:0;align-items:center;">
            <a href="{{ $user->hasRole('mahasiswa') ? route('manajemenmahasiswa.direktori.mahasiswa.profil.cv') : route('manajemenmahasiswa.direktori.alumni.profil.cv') }}" target="_blank"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#f1f5f9;color:#374151;font-size:12px;font-weight:600;border-radius:8px;text-decoration:none;white-space:nowrap;border:1px solid #e2e8f0;transition:background .15s;"
                onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                <span class="material-symbols-outlined" style="font-size:14px;">download</span>
                Download PDF
            </a>
            <a href="{{ route('profile.cv.index') }}"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#5E53F4;color:#fff;font-size:12px;font-weight:600;border-radius:8px;text-decoration:none;white-space:nowrap;transition:background .15s;"
                onmouseover="this.style.background='#4e44e0'" onmouseout="this.style.background='#5E53F4'">
                <span class="material-symbols-outlined" style="font-size:14px;">edit</span>
                Mulai / Edit CV
            </a>
        </div>
    </div>
</div>
@endif

<script>
function phoneCode(defaultDial) {
    const countries = [
        {flag:'🇦🇫',name:'Afghanistan',dial:'+93'},{flag:'🇿🇦',name:'Afrika Selatan',dial:'+27'},
        {flag:'🇺🇸',name:'Amerika Serikat',dial:'+1'},{flag:'🇸🇦',name:'Arab Saudi',dial:'+966'},
        {flag:'🇦🇷',name:'Argentina',dial:'+54'},{flag:'🇦🇺',name:'Australia',dial:'+61'},
        {flag:'🇳🇱',name:'Belanda',dial:'+31'},{flag:'🇧🇷',name:'Brasil',dial:'+55'},
        {flag:'🇧🇳',name:'Brunei',dial:'+673'},{flag:'🇦🇪',name:'Uni Emirat Arab',dial:'+971'},
        {flag:'🇵🇭',name:'Filipina',dial:'+63'},{flag:'🇮🇳',name:'India',dial:'+91'},
        {flag:'🇮🇩',name:'Indonesia',dial:'+62'},{flag:'🇬🇧',name:'Inggris',dial:'+44'},
        {flag:'🇮🇹',name:'Italia',dial:'+39'},{flag:'🇯🇵',name:'Jepang',dial:'+81'},
        {flag:'🇩🇪',name:'Jerman',dial:'+49'},{flag:'🇰🇭',name:'Kamboja',dial:'+855'},
        {flag:'🇨🇦',name:'Kanada',dial:'+1'},{flag:'🇰🇷',name:'Korea Selatan',dial:'+82'},
        {flag:'🇱🇦',name:'Laos',dial:'+856'},{flag:'🇲🇾',name:'Malaysia',dial:'+60'},
        {flag:'🇲🇲',name:'Myanmar',dial:'+95'},{flag:'🇵🇰',name:'Pakistan',dial:'+92'},
        {flag:'🇫🇷',name:'Prancis',dial:'+33'},{flag:'🇶🇦',name:'Qatar',dial:'+974'},
        {flag:'🇳🇿',name:'Selandia Baru',dial:'+64'},{flag:'🇸🇬',name:'Singapura',dial:'+65'},
        {flag:'🇹🇭',name:'Thailand',dial:'+66'},{flag:'🇹🇷',name:'Turki',dial:'+90'},
        {flag:'🇻🇳',name:'Vietnam',dial:'+84'},{flag:'🇨🇳',name:'China',dial:'+86'},
    ];
    const def = countries.find(c=>c.dial===defaultDial) ?? countries.find(c=>c.dial==='+62');
    return {
        open:false, search:'', dropdownStyle:'', selected:def, countries,
        get filtered(){ if(!this.search) return this.countries; const q=this.search.toLowerCase(); return this.countries.filter(c=>c.name.toLowerCase().includes(q)||c.dial.includes(q)); },
        toggle(){ this.open = !this.open; this.search = ''; },
        select(c){ this.selected=c; this.open=false; this.search=''; }
    };
}
</script>