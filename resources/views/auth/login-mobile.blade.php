{{-- resources/views/auth/login-mobile.blade.php --}}
<x-guest-layout>
    <div class="flex flex-col items-center w-full px-4 py-6 min-h-screen justify-between">

        {{-- Top Section --}}
        <div class="w-full">
            {{-- Logo & Title --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-2xl shadow-lg shadow-blue-500/30 mb-3">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 2h8v8H2V2zm10 0h6v6h-6V2zm-10 10h6v6H2v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Aplikasi <span class="text-blue-600">Tekkom</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Sistem Informasi Akademik Terpadu
                </p>
            </div>

            {{-- Error --}}
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium">{{ $errors->first() }}</p>
                </div>
            @endif

            {{-- SSO Button --}}
            <div class="mb-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 text-center">
                    Login dengan Akun Kampus
                </p>
                <a href="{{ route('microsoft.redirect') }}"
                   class="group w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-semibold rounded-xl transition-all shadow-md shadow-blue-500/25 text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 2h8v8H2V2zm10 0h6v6h-6V2zm-10 10h6v6H2v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                    <span>Login via SSO UNDIP</span>
                </a>
            </div>

            {{-- Dev Login --}}
            @if (app()->isLocal())
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white dark:bg-slate-900 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            Dev Only
                        </span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-3">
                        Development Sign In
                    </p>

                    <form method="POST" action="{{ route('login') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email</label>
                            <input
                                type="email"
                                name="email"
                                placeholder="nama@undip.ac.id"
                                required
                                autofocus
                                class="block w-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm rounded-xl px-3 py-2.5 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Password</label>
                            <input
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                class="block w-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm rounded-xl px-3 py-2.5 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            >
                        </div>
                        <button
                            type="submit"
                            class="w-full py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 active:scale-[0.98] text-white font-bold rounded-xl transition-all text-xs uppercase tracking-widest"
                        >
                            Masuk
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="mt-6 text-center">
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                © {{ date('Y') }} Teknik Komunikasi • Universitas Diponegoro
            </p>
        </div>
    </div>

    <script>
        localStorage.removeItem('um_per_page');
        localStorage.removeItem('cat_per_page');
    </script>
</x-guest-layout>