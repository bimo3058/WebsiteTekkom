<section class="space-y-6">
    <div class="p-4 bg-red-50 rounded-xl border border-red-100">
        <p class="text-[11px] text-red-600 leading-relaxed font-medium">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Pastikan Anda telah mengunduh data penting yang ingin Anda simpan.') }}
        </p>
    </div>

    <button 
        x-data="" 
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-white hover:bg-red-50 text-red-600 border border-red-200 font-black text-[10px] uppercase tracking-widest px-6 py-2.5 rounded-xl transition-all shadow-sm"
    >
        {{ __('Hapus Akun Permanen') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-base font-bold text-slate-800 uppercase tracking-tight">
                {{ __('Konfirmasi Penghapusan Akun') }}
            </h2>

            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                {{ __('Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun ini secara permanen.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="block w-full border-slate-200 focus:border-red-500 focus:ring-red-500 rounded-xl text-xs py-2.5" placeholder="{{ __('Masukkan Password Konfirmasi') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 rounded-xl transition-all">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all shadow-md">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>