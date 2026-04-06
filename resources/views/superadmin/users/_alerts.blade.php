{{-- Progress Bar (Design Minimalis & Persistent) --}}
@php 
    $displayId = $activeImportId ?? session('import_id');
    
    // Jika session import_id ada tapi activeImportId tidak, cek ke database
    if ($displayId && !$activeImportId) {
        $importExists = \App\Models\ImportStatus::where('id', $displayId)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
        
        if (!$importExists) {
            $displayId = null;
            session()->forget('import_id');
        }
    }
@endphp

@if($displayId && $activeImportId)
<div id="importProgressContainer" data-import-id="{{ $displayId }}" 
     class="mb-6 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm animate-in fade-in slide-in-from-top-2 duration-500">
    <div class="px-4 py-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm">
                <span id="importIcon" class="material-symbols-outlined text-blue-600 animate-spin" style="font-size: 16px">sync</span>
            </div>
            <div>
                <h3 id="importStatusText" class="text-[10px] font-semibold text-slate-700 uppercase tracking-widest">Sinkronisasi Data...</h3>
                <p class="text-[9px] text-slate-400 font-medium italic">Status: Menghubungkan ke database...</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right flex flex-col items-end">
                <span id="importPercentText" class="text-xs font-semibold text-blue-600 tracking-tighter">0%</span>
                <span class="text-[8px] font-semibold text-slate-300 uppercase tracking-tighter">Progress</span>
            </div>
            
            {{-- Tombol Cancel --}}
            <button type="button" id="btnCancelImport" onclick="cancelImport('{{ $displayId }}')" 
                class="flex items-center gap-1.5 px-2 py-1 bg-white hover:bg-red-50 text-red-500 border border-slate-200 hover:border-red-100 rounded-lg transition-all group shadow-sm">
                <span class="material-symbols-outlined group-hover:rotate-90 transition-transform" style="font-size: 14px">close</span>
                <span class="text-[9px] font-semibold uppercase tracking-wider">Batal</span>
            </button>
        </div>
    </div>
    
    <div class="p-4 bg-white">
        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-50">
            <div id="importProgressBar" class="bg-blue-600 h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(37,99,235,0.3)]" style="width: 0%"></div>
        </div>
    </div>
</div>
@endif

{{-- Alert Gagal/Error (Compact Design) --}}
@if($errors->any())
<div class="bg-white border border-red-200 rounded-xl mb-6 shadow-sm overflow-hidden animate-in shake-in duration-300">
    <div class="px-4 py-2 bg-red-50/50 border-b border-red-100 flex items-center gap-2">
        <span class="material-symbols-outlined text-red-500" style="font-size:16px">error</span>
        <h3 class="text-red-800 font-semibold text-[9px] uppercase tracking-widest">Validation Error</h3>
    </div>
    <div class="p-3 bg-white">
        <ul class="text-red-600 text-[10px] font-medium space-y-1 pl-4 list-disc">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- Alert Sukses Umum --}}
@if(session('success') && !$displayId)
<div class="bg-white border border-emerald-200 rounded-xl p-3 mb-6 shadow-sm flex items-center justify-between animate-in fade-in slide-in-from-right-4 duration-300">
    <div class="flex items-center gap-3">
        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm">
            <span class="material-symbols-outlined text-emerald-500" style="font-size:16px">check_circle</span>
        </div>
        <span class="text-emerald-700 text-[10px] font-semibold uppercase tracking-widest">{{ session('success') }}</span>
    </div>
    <button type="button" onclick="this.parentElement.remove()" class="text-slate-300 hover:text-slate-500 transition-colors p-1">
        <span class="material-symbols-outlined" style="font-size:16px">close</span>
    </button>
</div>
@endif