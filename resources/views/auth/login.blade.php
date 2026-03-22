{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    {{-- Wrapper utama untuk Card + Footer --}}
    <div class="flex flex-col items-center w-full max-w-5xl px-4">
        
        {{-- Box Login dengan Background Solid --}}
        {{-- Kita gunakan bg-slate-100 untuk light mode dan bg-slate-800 untuk dark mode agar terlihat solid --}}
        <div class="w-full bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl border-none"> 
            <div class="flex flex-col md:flex-row">
                
                {{-- Kita beri warna background yang sedikit berbeda (lebih gelap/terang) untuk membedakan panel --}}
                <div class="w-full md:w-5/12 p-8 md:p-12 bg-slate-50 dark:bg-slate-900/50 flex flex-col justify-center">
                    <div class="mb-8">
                        <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
                            Aplikasi <span class="text-blue-600">Tekkom</span>
                        </h1>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            Kelola aset teknik komunikasi dengan sistem terintegrasi.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">Akses Cepat</p>
                        <a href="{{ route('microsoft.redirect') }}" 
                           class="group w-full flex items-center justify-center gap-3 px-4 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.98]">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 2h8v8H2V2zm10 0h6v6h-6V2zm-10 10h6v6H2v-6zm8 0h6v6h-6v-6z"/>
                            </svg>
                            <span>Login via SSO UNDIP</span>
                        </a>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-8 md:p-12">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded text-red-700 dark:text-red-400 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (app()->isLocal())
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Development Sign In</h2>
                            <p class="text-sm text-slate-500 mt-1">Gunakan akun lokal untuk testing</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf
                            <div>
                                <x-input-label for="email" :value="__('Email Address')" class="text-slate-700 dark:text-slate-300 font-bold mb-2" />
                                <x-text-input id="email" class="block w-full bg-slate-50 dark:bg-slate-900 border-none focus:ring-2 focus:ring-blue-600 transition-all py-3 px-4 rounded-xl" type="email" name="email" placeholder="nama@undip.ac.id" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" class="text-slate-700 dark:text-slate-300 font-bold mb-2" />
                                <x-text-input id="password" class="block w-full bg-slate-50 dark:bg-slate-900 border-none focus:ring-2 focus:ring-blue-600 transition-all py-3 px-4 rounded-xl" type="password" name="password" placeholder="••••••••" required />
                            </div>

                            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl transition-all shadow-lg active:translate-y-0.5 uppercase tracking-widest text-sm">
                                {{ __('Masuk ke Dashboard') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer terpusat di bawah --}}
        <div class="mt-8 text-center text-xs text-slate-500 dark:text-slate-400 font-medium">
            <p>© {{ date('Y') }} Departemen Teknik Komunikasi • Universitas Diponegoro</p>
        </div>

    </div>
</x-guest-layout>