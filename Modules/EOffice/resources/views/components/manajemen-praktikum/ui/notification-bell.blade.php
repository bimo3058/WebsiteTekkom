@props(['count'=>0, 'role'=>'mahasiswa'])
<div style="position:relative;" data-read-url="{{ route('eoffice.manprak.notifications.read-all',['role'=>$role]) }}" x-data="{open:false,loading:false,error:false,items:[],async toggle(){this.open=!this.open;if(!this.open)return;this.loading=true;this.error=false;try{const r=await fetch(@js(route('eoffice.manprak.notifications.index',['role'=>$role])),{headers:{Accept:'application/json'}});if(!r.ok)throw new Error();this.items=(await r.json()).notifications;}catch(e){this.error=true;}finally{this.loading=false;}}}" @click.outside="open=false" @keydown.escape.stop="open=false;$refs.bell.focus()">
    <button type="button" x-ref="bell" class="mp-icon-button" @click="toggle()" :aria-expanded="open" aria-controls="mp-notification-panel" aria-label="Notifikasi" style="position:relative;">
        <x-eoffice::manajemen-praktikum.ui.icon name="bell" />
        @if($count > 0)<span class="mp-notification-badge" style="position:absolute;top:-5px;right:-5px;background:#F43F5E;color:#fff;font-size:10px;font-weight:700;border:2px solid #fff;border-radius:99px;padding:2px 4px;">{{ $count > 99 ? '99+' : $count }}</span>@endif
    </button>
    <section id="mp-notification-panel" class="mp-notification-panel" x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-1 scale-95" style="display:none;">
        <header style="padding:12px 16px;border-bottom:1px solid #DFE1E7;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div><strong style="font-size:13px;color:#353849;">Notifikasi</strong><p style="font-size:11px;color:#666D80;margin-top:2px;">Aktivitas terkini</p></div>
            <button type="button" x-show="items.some(i=>i.unread)" :disabled="loading" style="border:0;background:none;color:#0B266E;font-size:12px;font-weight:600;cursor:pointer;" @click="loading=true;error=false;fetch($el.closest('[x-data]').dataset.readUrl,{method:'POST',headers:{'X-CSRF-TOKEN':@js(csrf_token()),Accept:'application/json'}}).then(r=>{if(!r.ok)throw new Error();items.forEach(i=>i.unread=false);$el.closest('[x-data]').querySelector('.mp-notification-badge')?.remove();}).catch(()=>error=true).finally(()=>loading=false)">Tandai semua dibaca</button>
        </header>
        <div style="max-height:min(420px,65vh);overflow:auto;overscroll-behavior:contain;">
            <p x-show="loading" class="mp-notification-state">Memuat notifikasi...</p>
            <p x-show="!loading && error" class="mp-notification-state">Notifikasi belum dapat dimuat. Tutup dan coba lagi.</p>
            <p x-show="!loading && !error && !items.length" class="mp-notification-state">Belum ada notifikasi terbaru.</p>
            <template x-for="item in items" :key="item.id"><a :href="item.url" class="mp-notification-entry" :class="{'is-unread':item.unread}"
                @click.prevent="if($event.ctrlKey||$event.metaKey){window.open(item.url,'_blank','noopener');return}fetch(item.read_url,{method:'POST',headers:{'X-CSRF-TOKEN':@js(csrf_token()),Accept:'application/json'}}).catch(()=>{}).finally(()=>window.location.assign(item.url))">
                <strong class="mp-notification-title" x-text="item.title"></strong><p class="mp-notification-message" x-text="item.message"></p><small class="mp-notification-time" x-text="item.time"></small>
            </a></template>
        </div>
    </section>
</div>
<style>
.mp-notification-panel .mp-notification-entry{display:block;cursor:pointer;text-decoration:none;box-sizing:border-box;padding:16px 20px;border-bottom:1px solid #DFE1E7;border-left:3px solid transparent;background:#fff;overflow-wrap:anywhere;text-align:left;transition:background-color .15s ease}
.mp-notification-panel .mp-notification-entry.is-unread{background:#EFF6FF;border-left-color:#0B266E}
.mp-notification-panel .mp-notification-entry:hover{background:#E4EBF8;outline:none}
.mp-notification-panel .mp-notification-entry:focus{outline:none}
.mp-notification-panel .mp-notification-entry:focus-visible{background:#E4EBF8;outline:none;box-shadow:inset 0 0 0 2px #0B266E}
.mp-notification-panel .mp-notification-title{display:block;font-size:12px;font-weight:600;line-height:18px;color:#353849}
.mp-notification-panel .mp-notification-message{font-size:12px;line-height:1.6;color:#666D80;margin:6px 0 10px}
.mp-notification-panel .mp-notification-time{display:block;font-size:10px;line-height:16px;color:#666D80}
</style>
<style>.mp-notification-panel{border-radius:16px!important}.mp-notification-panel .mp-notification-state{text-align:center;padding:24px 16px}</style>
<style>.mp-notification-panel{position:absolute;top:calc(100% + 10px);right:0;width:360px;max-width:calc(100vw - 24px);background:#fff;border:1px solid #DFE1E7;border-radius:12px;box-shadow:0 12px 32px #0f172a24;z-index:100;overflow:hidden}.mp-notification-state{padding:20px 16px;font-size:12px;color:#666D80}@media(max-width:767px){.mp-notification-panel{position:fixed;top:58px;right:12px}}</style>
