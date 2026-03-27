{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5" id="password-form">
        @csrf
        @method('put')

        {{-- ── Password Saat Ini ── --}}
        <div>
            <label for="update_password_current_password" class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">
                {{ __('Password Saat Ini') }}
            </label>
            <div class="relative">
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="w-full rounded-xl border border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm py-2.5 px-3 pr-10 shadow-sm transition-all"
                    autocomplete="current-password" />
                <button type="button" onclick="togglePwd('update_password_current_password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        {{-- ── Divider ── --}}
        <div class="flex items-center gap-3 py-1">
            <div class="flex-1 h-px bg-slate-100"></div>
            <span class="text-[9px] text-slate-400 font-black uppercase tracking-[0.15em]">Sandi Baru</span>
            <div class="flex-1 h-px bg-slate-100"></div>
        </div>

        {{-- ── Password Baru ── --}}
        <div>
            <label for="update_password_password" class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">
                {{ __('Password Baru') }}
            </label>
            <div class="relative">
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    {{-- TRIGGER UTAMA: oninput akan memanggil fungsi JS setiap kali ada ketikan --}}
                    oninput="checkStrength(this.value)"
                    class="w-full rounded-xl border border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm py-2.5 px-3 pr-10 shadow-sm transition-all"
                    autocomplete="new-password" />
                <button type="button" onclick="togglePwd('update_password_password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                </button>
            </div>
            
            {{-- Visual Indikator Bar --}}
            <div class="mt-3">
                <div class="flex justify-between items-center mb-1.5">
                    <p class="text-[10px] font-black uppercase tracking-tight min-h-[14px]" id="strength-label"></p>
                    <p class="text-[9px] text-slate-400 font-bold uppercase" id="strength-percent"></p>
                </div>
                <div class="flex gap-1.5" id="strength-bars">
                    {{-- Bar 1 s/d 4 --}}
                    <div class="h-1.5 flex-1 rounded-full bg-slate-100 transition-all duration-500" id="bar1"></div>
                    <div class="h-1.5 flex-1 rounded-full bg-slate-100 transition-all duration-500" id="bar2"></div>
                    <div class="h-1.5 flex-1 rounded-full bg-slate-100 transition-all duration-500" id="bar3"></div>
                    <div class="h-1.5 flex-1 rounded-full bg-slate-100 transition-all duration-500" id="bar4"></div>
                </div>
                
                {{-- Checklist Syarat (Opsional tapi membantu user) --}}
                <ul class="mt-2.5 space-y-1">
                    <li id="req-length" class="text-[9px] font-bold text-slate-400 flex items-center gap-1.5 transition-colors">
                        <span class="material-symbols-outlined text-[12px]">circle</span> Minimal 8 karakter
                    </li>
                    <li id="req-caps-num" class="text-[9px] font-bold text-slate-400 flex items-center gap-1.5 transition-colors">
                        <span class="material-symbols-outlined text-[12px]">circle</span> Kombinasi Huruf Besar & Angka
                    </li>
                </ul>
            </div>
            
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        {{-- ── Konfirmasi Password ── --}}
        <div>
            <label for="update_password_password_confirmation" class="text-[11px] font-bold text-slate-700 block mb-1.5 tracking-tight">
                {{ __('Konfirmasi Password Baru') }}
            </label>
            <div class="relative">
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="w-full rounded-xl border border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm py-2.5 px-3 pr-10 shadow-sm transition-all"
                    autocomplete="new-password" />
                <button type="button" onclick="togglePwd('update_password_password_confirmation', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        {{-- Success Message --}}
        @if(session('status') === 'password-updated')
        <div class="flex items-center gap-2 text-emerald-700 text-[11px] font-bold bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ __('Password berhasil diperbarui.') }}
        </div>
        @endif

        <div class="pt-2">
            <button type="submit"
                class="bg-slate-800 hover:bg-slate-900 active:scale-[0.97] text-white font-bold text-[11px] uppercase tracking-widest px-8 py-3 rounded-xl transition-all shadow-md shadow-slate-200 w-full">
                Perbarui Password
            </button>
        </div>
    </form>

    {{-- ── JAVASCRIPT LOGIC ── --}}
    <script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('span');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    function checkStrength(val) {
        let score = 0;
        
        // 1. Logika Checklist
        const hasLength   = val.length >= 8;
        const hasUpper    = /[A-Z]/.test(val);
        const hasNumber   = /[0-9]/.test(val);
        const hasSpecial  = /[^A-Za-z0-9]/.test(val);

        // 2. Logika Skor yang lebih halus (6 poin total untuk 4 bar)
        if (val.length >= 4) score++;      // Start (Sangat Lemah)
        if (hasLength) score++;           // Panjang cukup
        if (hasUpper || hasNumber) score++; // Ada variasi (Sedang/Cukup)
        if (hasUpper && hasNumber) score++; // Variasi lengkap (Kuat)
        if (val.length >= 10 && hasSpecial) score++; // Bonus keamanan (Sangat Kuat)

        // Batasi skor maksimal 4 (sesuai jumlah bar)
        score = Math.min(score, 4);

        // 3. Update Visual Checklist
        const reqLen = document.getElementById('req-length');
        const reqCap = document.getElementById('req-caps-num');
        
        reqLen.className = `text-[9px] font-bold flex items-center gap-1.5 transition-colors ${hasLength ? 'text-emerald-500' : 'text-slate-400'}`;
        reqLen.querySelector('span').textContent = hasLength ? 'check_circle' : 'circle';
        
        const isCombined = hasUpper && hasNumber;
        reqCap.className = `text-[9px] font-bold flex items-center gap-1.5 transition-colors ${isCombined ? 'text-emerald-500' : 'text-slate-400'}`;
        reqCap.querySelector('span').textContent = isCombined ? 'check_circle' : 'circle';

        // 4. Update Bar & Label
        const colors   = ['', 'bg-red-400', 'bg-amber-400', 'bg-blue-400', 'bg-emerald-500'];
        const labels   = ['', 'Sangat Lemah', 'Cukup Aman', 'Kuat', 'Sangat Aman'];
        const txtClass = ['', 'text-red-500', 'text-amber-500', 'text-blue-500', 'text-emerald-600'];
        const percents = ['', '25%', '50%', '75%', '100%'];

        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar' + i);
            if (val.length === 0) {
                bar.className = 'h-1.5 flex-1 rounded-full bg-slate-100 transition-all duration-500';
            } else {
                bar.className = 'h-1.5 flex-1 rounded-full transition-all duration-500 ' +
                    (i <= score ? colors[score] : 'bg-slate-100');
            }
        }

        const lbl = document.getElementById('strength-label');
        const pct = document.getElementById('strength-percent');
        
        lbl.textContent = val.length ? labels[score] : '';
        lbl.className   = 'text-[10px] font-black uppercase tracking-tight ' + (val.length ? txtClass[score] : '');
        pct.textContent = val.length ? percents[score] : '';
    }
    </script>
</section>