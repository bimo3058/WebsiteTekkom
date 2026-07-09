<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <a href="{{ route('banksoal.soal.gpm.parameter.index') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen Parameter</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Tambah Parameter</span>
    @endsection

    <x-banksoal::notification.alerts />

    <div class="max-w-3xl mx-auto py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Tambah Parameter Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Definisikan aspek penilaian baru untuk validasi RPS atau Bank Soal.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <form action="{{ route('banksoal.soal.gpm.parameter.store') }}" method="POST" class="p-8 space-y-6" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="jenis" class="text-sm font-bold text-slate-700">Jenis Penilaian <span class="text-rose-500">*</span></label>
                        <select name="jenis" id="jenis" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none @error('jenis') border-rose-500 @enderror" required>
                            <option value="rps" {{ old('jenis') == 'rps' ? 'selected' : '' }}>Validasi RPS</option>
                            <option value="soal" {{ old('jenis') == 'soal' ? 'selected' : '' }}>Validasi Bank Soal</option>
                        </select>
                        @error('jenis') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="bobot" class="text-sm font-bold text-slate-700">Bobot (Poin) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="bobot" id="bobot" value="{{ old('bobot', 10) }}" min="1" max="100" class="w-full pl-4 pr-12 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none @error('bobot') border-rose-500 @enderror" required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Poin</span>
                        </div>
                        @error('bobot') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="aspek" class="text-sm font-bold text-slate-700">Aspek Parameter <span class="text-rose-500">*</span></label>
                    <textarea name="aspek" id="aspek" rows="3" placeholder="Contoh: Kesesuaian CPL dengan Materi Pembelajaran" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none @error('aspek') border-rose-500 @enderror" required>{{ old('aspek') }}</textarea>
                    @error('aspek') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('banksoal.soal.gpm.parameter.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-2.5 rounded-xl bg-[#0B266E] text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all">
                        Simpan Parameter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-banksoal::layouts.gpm-master>
