<x-guest-layout>
    <div class="flex flex-col items-center w-full max-w-md px-4">

        {{-- Card --}}
        <div class="w-full bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl">
            
            {{-- Header --}}
            <div class="px-8 pt-8 pb-6 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="relative shrink-0">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}"
                                 class="w-14 h-14 rounded-full object-cover ring-2 ring-blue-500/40">
                        @else
                            <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center ring-2 ring-blue-500/40">
                                <span class="text-white font-bold text-xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        {{-- Microsoft badge --}}
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none">
                                <rect x="1" y="1" width="8" height="8" fill="#F35325"/>
                                <rect x="11" y="1" width="8" height="8" fill="#81BC06"/>
                                <rect x="1" y="11" width="8" height="8" fill="#05A6F0"/>
                                <rect x="11" y="11" width="8" height="8" fill="#FFBA08"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-500 mb-0.5">
                            Terverifikasi via SSO
                        </p>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $user->name }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[220px]">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-8 py-7">
                <div class="mb-6">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">
                        Verifikasi Password Aplikasi
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Masukkan password aplikasi Anda untuk melanjutkan.
                    </p>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-3 p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl">
                        <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</p>
                    </div>
                @endif

                {{-- Sisa percobaan --}}
                @if ($remaining < 5)
                    <div class="mb-5 flex items-center gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs font-semibold text-amber-700 dark:text-amber-400">
                            Sisa percobaan: <span class="font-bold">{{ $remaining }}x</span> sebelum akun dikunci.
                        </p>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('sso.verify') }}" class="space-y-5">
                    @csrf

                    {{-- Hidden email — trigger browser password manager --}}
                    <input type="email"
                           name="email"
                           value="{{ $user->email }}"
                           autocomplete="username"
                           style="display:none;">

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                autofocus
                                placeholder="Masukkan password Anda"
                                class="block w-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 rounded-xl px-4 py-3 pr-11 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                            >
                            {{-- Toggle password visibility --}}
                            <button 
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                            >
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @if ($isDefault)
                            <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">
                                Password default: 
                                <span class="font-mono font-semibold text-slate-500 dark:text-slate-400">NamaDepan + NIM</span> 
                                <span class="text-slate-300 dark:text-slate-600">— contoh: John20000000000003</span>
                            </p>
                        @endif
                    </div>

                    <button 
                        type="submit"
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25 text-sm uppercase tracking-widest"
                    >
                        Masuk ke Dashboard
                    </button>
                </form>

                {{-- Bukan kamu? --}}
                <div class="mt-5 pt-5 border-t border-slate-200 dark:border-slate-700 text-center">
                    <a href="{{ route('login') }}" 
                       class="text-sm text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        ← Bukan akun Anda? Login dengan akun lain
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400 font-medium">
            <p>© {{ date('Y') }} Departemen Teknik Komunikasi • Universitas Diponegoro</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input    = document.getElementById('password');
            const eyeOn    = document.getElementById('eye-icon');
            const eyeOff   = document.getElementById('eye-off-icon');
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            eyeOn.classList.toggle('hidden', isHidden);
            eyeOff.classList.toggle('hidden', !isHidden);
        }
    </script>
</x-guest-layout>