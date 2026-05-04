{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-4" id="password-form">
        @csrf
        @method('put')

        {{-- Password Saat Ini --}}
        <div>
            <label for="update_password_current_password"
                   class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">
                {{ __('Password Saat Ini') }}
            </label>
            <div class="relative">
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-[#DFE1E7] bg-white text-[#0D0D12] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] text-sm py-2.5 px-3 pr-10 transition-all outline-none"
                />
                <button type="button" onclick="togglePwd('update_password_current_password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#A4ABB8] hover:text-[#666D80] transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-3 py-1">
            <div class="flex-1 h-px bg-[#F0F1F4]"></div>
            <span class="text-[10px] text-[#A4ABB8] font-semibold uppercase tracking-widest">Sandi Baru</span>
            <div class="flex-1 h-px bg-[#F0F1F4]"></div>
        </div>

        {{-- Password Baru --}}
        <div>
            <label for="update_password_password"
                   class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">
                {{ __('Password Baru') }}
            </label>
            <div class="relative">
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    oninput="checkStrength(this.value)"
                    autocomplete="new-password"
                    class="w-full rounded-xl border border-[#DFE1E7] bg-white text-[#0D0D12] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] text-sm py-2.5 px-3 pr-10 transition-all outline-none"
                />
                <button type="button" onclick="togglePwd('update_password_password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#A4ABB8] hover:text-[#666D80] transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>

            {{-- Strength bar --}}
            <div class="mt-3">
                <div class="flex justify-between items-center mb-1.5">
                    <p class="text-[11px] font-semibold min-h-[14px]" id="strength-label"></p>
                    <p class="text-[10px] text-[#A4ABB8] font-medium" id="strength-percent"></p>
                </div>
                <div class="flex gap-1.5">
                    <div class="h-1 flex-1 rounded-full bg-[#F0F1F4] transition-all duration-500" id="bar1"></div>
                    <div class="h-1 flex-1 rounded-full bg-[#F0F1F4] transition-all duration-500" id="bar2"></div>
                    <div class="h-1 flex-1 rounded-full bg-[#F0F1F4] transition-all duration-500" id="bar3"></div>
                    <div class="h-1 flex-1 rounded-full bg-[#F0F1F4] transition-all duration-500" id="bar4"></div>
                </div>
                <ul class="mt-2 space-y-1">
                    <li id="req-length" class="text-[10px] font-medium text-[#A4ABB8] flex items-center gap-1.5 transition-colors">
                        <svg id="req-length-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/></svg>
                        Minimal 8 karakter
                    </li>
                    <li id="req-caps-num" class="text-[10px] font-medium text-[#A4ABB8] flex items-center gap-1.5 transition-colors">
                        <svg id="req-caps-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/></svg>
                        Kombinasi huruf besar &amp; angka
                    </li>
                </ul>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        {{-- Konfirmasi --}}
        <div>
            <label for="update_password_password_confirmation"
                   class="block text-[12px] font-medium text-[#0D0D12] mb-1.5">
                {{ __('Konfirmasi Password Baru') }}
            </label>
            <div class="relative">
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border border-[#DFE1E7] bg-white text-[#0D0D12] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] text-sm py-2.5 px-3 pr-10 transition-all outline-none"
                />
                <button type="button" onclick="togglePwd('update_password_password_confirmation', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#A4ABB8] hover:text-[#666D80] transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        {{-- Success --}}
        @if(session('status') === 'password-updated')
        <div class="flex items-center gap-2 text-[#287F6E] text-[12px] font-medium bg-[#DDF2EE] border border-[#40C4AA]/30 rounded-xl px-4 py-3">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('Password berhasil diperbarui.') }}
        </div>
        @endif

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-[#0B266E] hover:bg-[#091958] active:bg-[#071742] text-white font-semibold text-[13px] px-6 py-2.5 rounded-xl transition-all">
                Perbarui Password
            </button>
        </div>
    </form>

    <script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        // swap icon between eye and eye-off
        btn.querySelector('svg').innerHTML = isText
            ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
            : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
    }

    function checkStrength(val) {
        let score       = 0;
        const hasLength  = val.length >= 8;
        const hasUpper   = /[A-Z]/.test(val);
        const hasNumber  = /[0-9]/.test(val);
        const hasSpecial = /[^A-Za-z0-9]/.test(val);

        if (val.length >= 4)  score++;
        if (hasLength)         score++;
        if (hasUpper || hasNumber) score++;
        if (hasUpper && hasNumber) score++;
        if (val.length >= 10 && hasSpecial) score++;
        score = Math.min(score, 4);

        // Requirement indicators
        const setReq = (id, iconId, met) => {
            const li   = document.getElementById(id);
            const icon = document.getElementById(iconId);
            li.style.color = met ? '#287F6E' : '#A4ABB8';
            icon.innerHTML = met
                ? '<polyline points="20 6 9 17 4 12"/>'
                : '<circle cx="12" cy="12" r="10"/>';
        };
        setReq('req-length',   'req-length-icon',  hasLength);
        setReq('req-caps-num', 'req-caps-icon',    hasUpper && hasNumber);

        const colors   = ['', '#DF1C41', '#D39C3D', '#3C518B', '#287F6E'];
        const labels   = ['', 'Sangat Lemah', 'Cukup Aman', 'Kuat', 'Sangat Aman'];
        const percents = ['', '25%', '50%', '75%', '100%'];

        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar' + i);
            bar.style.backgroundColor = val.length === 0 ? '#F0F1F4' : (i <= score ? colors[score] : '#F0F1F4');
        }

        const lbl = document.getElementById('strength-label');
        const pct = document.getElementById('strength-percent');
        lbl.textContent  = val.length ? labels[score]   : '';
        lbl.style.color  = val.length ? colors[score]   : '';
        pct.textContent  = val.length ? percents[score] : '';
    }
    </script>
</section>
