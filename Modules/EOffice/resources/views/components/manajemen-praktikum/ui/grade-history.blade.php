@props(['entries'=>[], 'label'=>'Riwayat Nilai', 'scoreKey'=>null, 'stageId'=>null, 'scoreLabel'=>'Nilai','clientEntries'=>null,'clientScoreLabel'=>null,'clientLabel'=>null])
@php
    $historyItems=collect($entries)->filter(fn($entry)=>!$stageId || empty($entry['tahap_id']) || $entry['tahap_id']===$stageId)->sortBy('waktu')->values()->map(function($entry) use($scoreKey){
        $format=fn($value)=>$value===null?'—':str_replace('.',',',(string)$value);
        return ['editor'=>$entry['oleh'] ?? '-','date'=>\Illuminate\Support\Carbon::parse($entry['waktu'])->translatedFormat('d F Y, H:i').' WIB','before'=>$format($entry['sebelum'][$scoreKey] ?? null),'after'=>$format($entry['sesudah'][$scoreKey] ?? null),'initial'=>$format($entry['awal'][$scoreKey] ?? null),'revision'=>$format($entry['revisi'][$scoreKey] ?? null),'type'=>$entry['jenis'] ?? 'penilaian'];
    });
@endphp
@once
<style>
.mp-grade-history{display:inline-flex;position:absolute;right:10px;top:50%;transform:translateY(-50%)}.mp-grade-history-trigger{opacity:0;transition:opacity .15s;border:0;padding:2px;background:none;color:#666D80;cursor:pointer;display:flex}.mp-grade-history-cell:hover .mp-grade-history-trigger,.mp-grade-history-cell:focus-within .mp-grade-history-trigger,.mp-grade-history-trigger:focus-visible{opacity:1}.mp-history-nav{display:flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #DFE1E7;border-radius:6px;background:white;color:#0B266E;cursor:pointer}.mp-history-nav:disabled{color:#A4ABB8;cursor:not-allowed;background:#F8F9FB}@media(hover:none){.mp-grade-history-trigger{opacity:1}}
</style>
@endonce
<span class="mp-grade-history" x-data="{historyOpen:false,get items(){return {{ $clientEntries ?? $historyItems->toJson() }}},historyIndex:{{ max(0,$historyItems->count()-1) }},get current(){return this.items[this.historyIndex]}}">
    <button class="mp-grade-history-trigger" type="button" @click="historyIndex=Math.max(0,items.length-1);historyOpen=true" aria-label="{{ $label }}" title="{{ $label }}" @if($clientLabel) :aria-label="{{ $clientLabel }}" :title="{{ $clientLabel }}" @endif><x-eoffice::manajemen-praktikum.ui.icon name="history" :size="14" /></button>
    <template x-teleport="body"><div x-show="historyOpen" x-cloak x-transition.opacity @keydown.escape.window="historyOpen=false" @click.self="historyOpen=false" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999">
        <section role="dialog" aria-modal="true" aria-label="{{ $label }}" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 40px);max-height:80vh;overflow:auto;background:white;border-radius:14px;text-align:left;font-weight:400">
            <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-size:16px;font-weight:700;color:#0D0D12" @if($clientLabel) x-text="{{ $clientLabel }}" @endif>{{ $label }}</div>
            <div class="mp-history-content" style="padding:20px 14px;display:grid;grid-template-columns:30px minmax(0,1fr) 30px;gap:12px;align-items:center;font-size:13px;color:#666D80;line-height:1.6">
                <button type="button" class="mp-history-nav" :disabled="historyIndex===0 || !items.length" @click="historyIndex--" aria-label="Riwayat lebih lawas"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-left" /></button>
                <div>
                <template x-if="current"><div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px"><span class="mp-av sky" style="width:32px;height:32px;font-size:12px;flex-shrink:0" x-text="current.editor.slice(0,2).toUpperCase()"></span><div><div style="font-weight:600;color:#353849" x-text="current.editor"></div><div style="font-size:12px" x-text="current.date"></div></div></div>
                    <div style="font-size:12px;margin-bottom:5px">Kolom Penilaian: <span style="color:#353849;font-weight:600" @if($clientScoreLabel) x-text="{{ $clientScoreLabel }}" @endif>{{ $scoreLabel }}</span></div>
                    <template x-if="current.type==='penilaian'"><div style="color:#353849;font-size:12px">Mengganti <strong x-text="current.before"></strong> dengan <strong x-text="current.after"></strong>.</div></template>
                    <p x-show="current.type!=='penilaian'" style="margin:0">Menunggu penilaian ulang setelah pengumpulan atau perbaikan.</p>
                </div></template>
                <p x-show="!items.length" style="margin:0">Belum ada riwayat perubahan nilai.</p>
                </div>
                <button type="button" class="mp-history-nav" :disabled="historyIndex>=items.length-1" @click="historyIndex++" aria-label="Riwayat lebih baru"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-right" /></button>
            </div>
            <div style="padding:16px 22px;border-top:1px solid #DFE1E7;display:flex;justify-content:space-between;align-items:center"><span style="font-size:12px;color:#666D80" x-text="items.length ? (historyIndex+1)+' / '+items.length : ''"></span><button type="button" class="mp-btn secondary md" @click="historyOpen=false">Tutup</button></div>
        </section>
    </div></template>
</span>
