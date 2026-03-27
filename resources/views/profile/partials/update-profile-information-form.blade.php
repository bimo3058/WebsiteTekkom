{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
@php
    $user = auth()->user();
    $isSuperadmin = $user->hasRole('superadmin');
    $isAdmin = $user->hasAnyRole(['admin_banksoal','admin_capstone','admin_eoffice','admin_kemahasiswaan']);
    $canEditName = $isSuperadmin || $isAdmin; // Logika edit nama
@endphp

{{-- Tambahkan class 'flex flex-col h-full' pada form --}}
<form method="POST" action="{{ route('profile.update') }}" class="flex flex-col h-full space-y-5">
    @csrf
    @method('PATCH')

    {{-- Wrapper untuk Input agar bisa memakan ruang yang tersedia --}}
    <div class="flex-grow space-y-5">
        {{-- ── Nama Lengkap ── --}}
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
                class="w-full rounded-xl border text-sm py-2.5 px-3 transition-all
                    {{ !$canEditName
                        ? 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed opacity-80'
                        : 'border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 shadow-sm' }}"
            />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- ── Email Resmi (Readonly) ── --}}
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">Email Resmi (SSO)</label>
                <input
                    type="email"
                    value="{{ $user->email }}"
                    readonly
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm py-2.5 px-3 cursor-not-allowed opacity-80 shadow-sm"
                />
            </div>

            {{-- ── Identitas (NIM/NIP) ── --}}
            @php
                $idValue = null;
                $idLabel = 'Nomor Identitas';
                
                if($user->hasRole('mahasiswa') && $user->student) {
                    $idValue = $user->student->student_number;
                    $idLabel = 'NIM';
                } elseif($user->hasRole('dosen') && $user->lecturer) {
                    $idValue = $user->lecturer->employee_number;
                    $idLabel = 'NIP / No. Karyawan';
                }
            @endphp

            @if($idValue)
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">{{ $idLabel }}</label>
                <input
                    type="text"
                    value="{{ $idValue }}"
                    readonly
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm py-2.5 px-3 cursor-not-allowed opacity-80 shadow-sm"
                />
            </div>
            @endif
        </div>

        <hr class="border-slate-100 my-2">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- ── Nomor WhatsApp ── --}}
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">Nomor WhatsApp</label>
                <div class="flex gap-2">
                    <div class="flex items-center gap-1.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 flex-shrink-0 shadow-sm">
                        <span class="text-sm">🇮🇩</span>
                        <span>+62</span>
                    </div>
                    <input
                        type="tel"
                        name="whatsapp"
                        value="{{ old('whatsapp', ltrim($user->eoUserProfile?->no_wa ?? '', '+62')) }}"
                        placeholder="8xx-xxxx-xxxx"
                        class="flex-1 rounded-xl border border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm py-2.5 px-3 shadow-sm transition-all"
                    />
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('whatsapp')" />
            </div>

            {{-- ── Email Pribadi ── --}}
            <div>
                <label class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">Email Pribadi</label>
                <input
                    type="email"
                    name="personal_email"
                    value="{{ old('personal_email', $user->personal_email ?? '') }}"
                    placeholder="email@pribadi.com"
                    class="w-full rounded-xl border border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm py-2.5 px-3 shadow-sm transition-all"
                />
                <x-input-error class="mt-1" :messages="$errors->get('personal_email')" />
            </div>
        </div>
    </div>

    {{-- ── Action Area (Pindah ke Pojok Kanan Bawah) ── --}}
    <div class="mt-auto pt-6 flex flex-col items-end gap-3">
        {{-- Notifikasi Sukses --}}
        @if(session('status') === 'profile-updated')
        <div class="flex items-center gap-2 text-emerald-700 text-[10px] font-bold bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 animate-in fade-in duration-300">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            {{ __('Profil diperbarui') }}
        </div>
        @endif

        <button type="submit"
            class="bg-slate-800 hover:bg-slate-900 active:scale-[0.97] text-white font-bold text-[11px] uppercase tracking-widest px-8 py-3 rounded-xl transition-all shadow-md shadow-slate-200">
            Simpan Perubahan
        </button>
    </div>
</form>