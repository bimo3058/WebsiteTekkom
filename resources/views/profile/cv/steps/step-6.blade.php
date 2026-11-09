<div class="text-center py-8 animate-fade-in">
    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background: var(--c-success-subtle);">
        <span style="color: var(--c-success);"><x-icon name="check-circle" size="40" /></span>
    </div>
    
    <h3 class="text-2xl font-bold text-slate-800 mb-2">Data CV Berhasil Disimpan</h3>
    <p class="text-slate-500 max-w-md mx-auto mb-8">Data yang Anda masukkan beserta data tersinkronisasi dari sistem sudah disiapkan. Anda dapat mempratinjau atau mengunduh CV sekarang.</p>

    <div class="flex items-center justify-center gap-4">
        <a href="{{ route('profile.cv.preview') }}" target="_blank"
           class="btn-secondary text-sm">
            <x-icon name="eye" size="18" />
            Preview CV
        </a>

        <a href="{{ route('profile.cv.generate') }}" target="_blank"
           class="btn-primary text-sm shadow-sm">
            <x-icon name="download-01" size="18" />
            Download PDF / Print
        </a>
    </div>
</div>
