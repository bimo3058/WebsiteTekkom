<div class="text-center py-8">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-green-500">
        <span class="material-symbols-outlined text-[40px]">task_alt</span>
    </div>
    
    <h3 class="text-2xl font-bold text-slate-800 mb-2">Data CV Berhasil Disimpan</h3>
    <p class="text-slate-500 max-w-md mx-auto mb-8">Data yang Anda masukkan beserta data tersinkronisasi dari sistem sudah disiapkan. Anda dapat mempratinjau atau mengunduh CV sekarang.</p>

    <div class="flex items-center justify-center gap-4">
        <a href="{{ route('profile.cv.preview') }}" target="_blank" 
           class="px-6 py-3 rounded-xl border border-[#5E53F4] text-[#5E53F4] font-bold text-sm hover:bg-indigo-50 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">visibility</span>
            Preview CV
        </a>
        
        <a href="{{ route('profile.cv.generate') }}" target="_blank"
           class="px-8 py-3 rounded-xl bg-[#5E53F4] text-white font-bold text-sm hover:bg-[#4e44e0] transition-all shadow-sm shadow-[#5E53F4]/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Download PDF / Print
        </a>
    </div>
</div>
