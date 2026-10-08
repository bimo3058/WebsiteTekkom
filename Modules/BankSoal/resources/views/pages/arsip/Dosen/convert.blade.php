<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @include('banksoal::pages.arsip.Dosen._styles')
    @section('breadcrumbs')
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Arsip Soal</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Konfirmasi Arsipkan</span>
    @endsection

<!-- Modal Konfirmasi Arsipkan -->
<div class="bs-archive-confirm fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="archiveConfirmTitle">
    <div class="bs-archive-confirm-card">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="bs-archive-confirm-icon">
                <i class="fas fa-archive" aria-hidden="true"></i>
            </div>
        </div>

        <!-- Title -->
        <h2 id="archiveConfirmTitle" class="text-center text-xl font-bold text-slate-900 mb-2">Arsipkan Penarikan?</h2>

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
            
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="bs-archive-secondary flex-1">
                Batal
            </a>
            <button type="submit" class="bs-archive-primary flex-1">
                Arsipkan
            </button>
        </form>
    </div>
</div>

</x-banksoal::layouts.dosen-admin>
