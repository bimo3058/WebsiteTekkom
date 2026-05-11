<x-banksoal::layouts.dosen-admin>

<x-banksoal::ui.page-header title="Konversi Penarikan ke Arsip" subtitle="Lengkapi detail arsip sebelum memindahkan penarikan menjadi arsip final.">
    <x-slot:actions>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors">
            <i class="fas fa-arrow-left w-4"></i> Kembali
        </a>
    </x-slot:actions>
</x-banksoal::ui.page-header>

<x-banksoal::ui.panel title="Konversi" padding="p-6">
    <form action="{{ route('banksoal.arsip.dosen.penarikan.update', $penarikan->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Arsip</label>
            <input type="text" name="nama_arsip" value="{{ $penarikan->nama_ekstraksi }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ $penarikan->deskripsi }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Internal</label>
            <textarea name="catatan_internal" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ $penarikan->catatan_internal }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Konversi</label>
            <textarea name="catatan_konversi" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></textarea>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                <i class="fas fa-archive"></i> Konversi Sekarang
            </button>
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</x-banksoal::ui.panel>

</x-banksoal::layouts.dosen-admin>