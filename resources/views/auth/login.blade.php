{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 px-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
                    Aplikasi Tekkom
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Kelola aset teknik komunikasi dengan mudah
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <!-- Status Messages -->
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
                            <p class="font-semibold mb-1">Login gagal</p>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- SSO Button -->
                    <a href="{{ route('microsoft.redirect') }}" 
                       class="group w-full flex items-center justify-center gap-3 px-4 py-3 mb-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 2h8v8H2V2zm10 0h6v6h-6V2zm-10 10h6v6H2v-6zm8 0h6v6h-6v-6z"/>
                        </svg>
                        <span>Login dengan SSO UNDIP</span>
                    </a>

                    <!-- Divider -->
                    @if (app()->isLocal())
                        <div class="relative mb-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-3 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                    Development Only
                                </span>
                            </div>
                        </div>

                        <!-- Traditional Login Form -->
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <!-- Email Input -->
                            <div>
                                <x-input-label for="email" :value="__('Email Address')" class="text-slate-700 dark:text-slate-300 text-sm font-semibold" />
                                <x-text-input 
                                    id="email" 
                                    class="block mt-2 w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                                    type="email" 
                                    name="email" 
                                    :value="old('email')" 
                                    placeholder="nama@undip.ac.id"
                                    required 
                                    autofocus
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 dark:text-red-400 text-sm" />
                            </div>

                            <!-- Password Input -->
                            <div>
                                <x-input-label for="password" :value="__('Password')" class="text-slate-700 dark:text-slate-300 text-sm font-semibold" />
                                <x-text-input 
                                    id="password" 
                                    class="block mt-2 w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                                    type="password" 
                                    name="password" 
                                    placeholder="••••••••"
                                    required 
                                />
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 dark:text-red-400 text-sm" />
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full mt-6 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-semibold rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                                {{ __('Sign In') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
                <p>© {{ date('Y') }} Departemen Teknik Komunikasi, UNDIP</p>
            </div>
        </div>
    </div>
</x-guest-layout>