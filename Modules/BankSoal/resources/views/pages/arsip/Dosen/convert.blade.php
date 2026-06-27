<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Arsip Soal</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Konfirmasi Arsipkan</span>
    @endsection

<!-- Modal Konfirmasi Arsipkan -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-8">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-archive text-emerald-600 text-2xl"></i>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-center text-xl font-bold text-slate-900 mb-2">Arsipkan Penarikan?</h2>

        <!-- Message -->
        <p class="text-center text-slate-600 text-sm mb-6">
            Anda akan memindahkan <strong>{{ $penarikan->nama_ekstraksi }}</strong> ke arsip final. Tindakan ini tidak dapat dibatalkan.
        </p>

        <!-- Form & Actions -->
        <form action="{{ route('banksoal.arsip.dosen.penarikan.update', $penarikan->id) }}" method="POST" class="flex items-center gap-3" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
            @csrf
            @method('PUT')
            
            <!-- Hidden fields with default values -->
            <input type="hidden" name="nama_arsip" value="{{ $penarikan->nama_ekstraksi }}">
            <input type="hidden" name="deskripsi" value="{{ $penarikan->deskripsi ?? '' }}">
            <input type="hidden" name="catatan_internal" value="{{ $penarikan->catatan_internal ?? '' }}">
            <input type="hidden" name="catatan_konversi" value="">
            
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                Arsipkan
            </button>
        </form>
    </div>
</div>

</x-banksoal::layouts.dosen-admin>