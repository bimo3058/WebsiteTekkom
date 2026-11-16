<div x-data="{open:false,message:'',resolve:null,origin:null,get action(){const text=this.message.trim().toLowerCase();if(/^(hapus|yakin.*hapus)/.test(text))return 'Hapus';if(/^(cabut|penting:.*mencabut)/.test(text))return 'Cabut';if(/^(batalkan|batal)/.test(text))return 'Batalkan';if(/^(nonaktifkan|tutup|reset|kosongkan|tarik)/.test(text))return 'Peringatan';return 'Lanjutkan'},get tone(){return ['Hapus','Cabut','Batalkan'].includes(this.action)?'danger':this.action==='Peringatan'?'warning':'primary'},get title(){return this.action==='Lanjutkan'?'Konfirmasi Tindakan':this.action==='Peringatan'?'Lanjutkan Tindakan?':this.action+' Data?'},ask(detail){this.message=detail.message;this.resolve=detail.resolve;this.origin=document.activeElement;this.open=true;this.$nextTick(()=>$refs.cancel.focus())},finish(value){this.open=false;this.resolve?.(value);this.resolve=null;this.origin?.focus()}}"
    @mp-confirm.window="ask($event.detail)" @keydown.escape.window="if(open)finish(false)">
    <template x-teleport="body">
        <div class="mp-confirm-overlay" x-show="open" x-cloak x-transition.opacity.duration.200ms @click.self="finish(false)" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="mp-confirm-title" aria-describedby="mp-confirm-message">
            <section class="mp-confirm-card" :class="'mp-confirm-'+tone" x-show="open"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-3 scale-95">
                <div class="mp-confirm-symbol"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.3 3.9 2 18a2 2 0 0 0 1.7 3h16.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/></svg></div>
                <h2 id="mp-confirm-title" x-text="title"></h2>
                <p id="mp-confirm-message" x-text="message"></p>
                    <div class="mp-confirm-actions">
                        <button type="button" x-ref="cancel" class="mp-btn secondary md" @keydown.shift.tab.prevent="$refs.approve.focus()" @click="finish(false)">Kembali</button>
                        <button type="button" x-ref="approve" class="mp-btn primary md mp-confirm-approve" @keydown.tab.prevent="$refs.cancel.focus()" @click="finish(true)" x-text="action === 'Lanjutkan' || action === 'Peringatan' ? 'Ya, Lanjutkan' : 'Ya, '+action">Ya, Lanjutkan</button>
                    </div>
            </section>
        </div>
    </template>
</div>
<script src="{{ asset('js/practicum-feedback.js') }}?v={{ filemtime(public_path('js/practicum-feedback.js')) }}" defer></script>
<style>
.mp-confirm-special{position:fixed!important;inset:0!important;z-index:10060!important;display:flex;align-items:center!important;justify-content:center!important;padding:20px!important;background:rgba(13,13,18,.5)!important;box-sizing:border-box;overflow-y:auto}
.mp-confirm-special>[role=dialog]{position:relative!important;top:auto!important;left:auto!important;transform:none!important;box-sizing:border-box;width:100%!important;max-width:520px!important;max-height:calc(100dvh - 40px)!important;overflow-y:auto!important;border:1px solid #DFE1E7!important;border-radius:20px!important;background:#fff!important;padding:28px 24px 24px!important;text-align:center!important;box-shadow:0 20px 40px #0D0D1226}
.mp-confirm-special>[role=dialog]>h2,.mp-confirm-special>[role=dialog]>div:first-child:not(.mp-confirm-symbol){padding:0!important;margin:0 0 12px!important;font-size:18px!important;line-height:26px!important;font-weight:600!important;color:#353849!important;border:0!important;background:none!important}
.mp-confirm-special>[role=dialog]>p,.mp-confirm-special>[role=dialog]>div:nth-last-child(2):not(.mp-confirm-symbol){padding:0!important;margin:0!important;font-size:13px!important;line-height:22px!important;color:#666D80!important}
.mp-confirm-special>[role=dialog]>div:last-child{display:flex!important;gap:12px!important;justify-content:center!important;padding:0!important;margin:28px 0 0!important;border:0!important;background:none!important}
.mp-confirm-special>[role=dialog]>div:last-child button{flex:1;min-width:0;min-height:42px;justify-content:center;font-size:13px!important;font-weight:600!important}
.mp-confirm-special .mp-confirm-symbol{background:#EEF1FA;color:#0B266E}.mp-confirm-special .mp-status-warning{background:#FFFAEB;color:#B54708}
.mp-confirm-special .mp-special-danger .mp-confirm-symbol{background:#FFF1F3;color:#DF1C41}.mp-confirm-special .mp-special-danger button.primary{background:#DF1C41;border-color:#DF1C41}
.mp-confirm-special .mp-special-warning .mp-confirm-symbol{background:#FFFAEB;color:#B54708}.mp-confirm-special .mp-special-warning button.primary{background:#B54708;border-color:#B54708}
.mp-confirm-special>[role=dialog]>.mp-confirm-symbol+div{padding:0!important;margin:0 0 12px!important;border:0!important;background:none!important;font-size:18px!important;line-height:26px!important;font-weight:600!important;color:#353849!important}
.mp-confirm-special>[role=dialog]:not(:has(>.mp-confirm-symbol))::before{content:'!';display:flex;align-items:center;justify-content:center;box-sizing:border-box;width:56px;height:56px;margin:0 auto 20px;border-radius:50%;background:#EEF1FA;color:#0B266E;font-size:26px;font-weight:600}
.mp-confirm-special>.mp-special-danger:not(:has(>.mp-confirm-symbol))::before{background:#FFF1F3;color:#DF1C41}
.mp-confirm-special>.mp-special-warning:not(:has(>.mp-confirm-symbol))::before{background:#FFFAEB;color:#B54708}
.mp-confirm-overlay{position:fixed;inset:0;z-index:10060;display:flex;align-items:center;justify-content:center;background:rgba(13,13,18,.5);padding:20px;box-sizing:border-box;overflow-y:auto}
.mp-confirm-card{--confirm-fg:#0B266E;--confirm-bg:#EEF1FA;width:100%;max-width:520px;min-width:0;max-height:calc(100dvh - 40px);overflow-y:auto;box-sizing:border-box;padding:28px 24px 24px;background:#fff;border:1px solid #DFE1E7;border-radius:20px;box-shadow:0 20px 40px #0D0D1226;text-align:center}
.mp-confirm-card.mp-confirm-danger{--confirm-fg:#DF1C41;--confirm-bg:#FFF1F3}.mp-confirm-card.mp-confirm-warning{--confirm-fg:#B54708;--confirm-bg:#FFFAEB}
.mp-confirm-symbol{display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:var(--confirm-bg);color:var(--confirm-fg);margin:0 auto 20px}
.mp-confirm-card h2{font-size:18px;line-height:26px;font-weight:600;color:#353849;margin:0 0 12px}
.mp-confirm-card p{font-size:13px;line-height:22px;color:#666D80;white-space:pre-line;overflow-wrap:anywhere;margin:0}
.mp-confirm-actions{display:flex;gap:12px;margin-top:28px}.mp-confirm-actions button{flex:1;min-width:0;min-height:42px;justify-content:center;font-size:13px;font-weight:600}
.mp-confirm-actions .mp-confirm-approve{background:var(--confirm-fg);border-color:var(--confirm-fg);color:white}
.mp-confirm-actions,.mp-confirm-special>[role=dialog]>div:last-child{width:100%;box-sizing:border-box;align-items:stretch!important;flex-wrap:nowrap!important}
.mp-confirm-actions button,.mp-confirm-special>[role=dialog]>div:last-child button{box-sizing:border-box!important;display:inline-flex!important;flex:1 1 0%!important;width:0!important;min-width:0!important;height:44px!important;min-height:44px!important;padding:0 12px!important;border-radius:8px!important;align-items:center!important;justify-content:center!important;line-height:18px!important;text-align:center;font-size:13px!important;font-weight:600!important;white-space:normal!important}
@media(prefers-reduced-motion:reduce){.mp-confirm-overlay,.mp-confirm-card{transition-duration:.01ms!important;animation-duration:.01ms!important}}
.mp-box-body>.mp-flash{display:flex;align-items:center;gap:10px;background:#fff;border-radius:10px!important;padding:10px 14px;font-size:12px;color:#353849;border:1px solid #DFE1E7;border-left-width:3px}
.mp-box-body>.mp-flash-success{border-color:#9DE0D3;border-left-color:#287F6E;color:#287F6E}
.mp-box-body>.mp-flash-error{border-color:#FADAE1;border-left-color:#DF1C41;color:#DF1C41}
.mp-feedback-dismiss{margin-left:auto;flex-shrink:0;border:0;background:none;color:#666D80;cursor:pointer;padding:4px}
.mp-feedback-stack{position:fixed;top:80px;left:50%;transform:translateX(-50%);width:calc(100vw - 24px);max-width:520px;z-index:11000;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.mp-feedback-stack .mp-floating-feedback{box-sizing:border-box;width:100%;display:flex;align-items:center;gap:10px;pointer-events:auto;padding:9.5px 16px;margin:0!important;border:1px solid;border-radius:8px!important;font-size:13px;font-weight:600;box-shadow:0 10px 25px #0000001A;animation:mp-feedback-down .3s ease-out both}
.mp-feedback-stack .mp-flash-success{background:#DDF2EE;color:#174E43;border-color:#40C4AA}
.mp-feedback-stack .mp-flash-error{background:#FADAE1;color:#7C1028;border-color:#DF1C41}
.mp-feedback-stack .mp-floating-feedback{flex-shrink:0;max-width:100%!important;min-height:0!important;white-space:normal;overflow-wrap:anywhere;text-align:left;line-height:20px!important}
.mp-feedback-stack .mp-floating-feedback>span{flex:1;min-width:0}
.mp-feedback-stack .is-leaving{animation:mp-feedback-up .3s ease-in both}
.mp-floating-feedback svg{flex-shrink:0}.mp-floating-feedback .mp-feedback-dismiss{color:inherit}
@keyframes mp-feedback-down{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:translateY(0)}}
@keyframes mp-feedback-up{from{opacity:1;transform:translateY(0)}to{opacity:0;transform:translateY(-16px)}}
@media(prefers-reduced-motion:reduce){.mp-feedback-stack .mp-floating-feedback{animation-duration:.01ms}}
</style>
