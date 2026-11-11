@once
    <style>
        .mp-action-trigger { display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;padding:0;border:1px solid #DFE1E7;border-radius:8px;background:#fff;color:#666D80;cursor:pointer; }
        .mp-action-trigger:hover,.mp-action-trigger[aria-expanded="true"] { background:#F6F8FA;border-color:#A4ABB8;color:#0B266E; }
        .mp-action-trigger:focus-visible { outline:2px solid #0B266E;outline-offset:2px; }
        .mp-action-panel { position:fixed;z-index:150;width:224px;max-width:calc(100vw - 16px);max-height:calc(100vh - 16px);overflow-y:auto;padding:7px;background:#fff;border:1px solid #DFE1E7;border-radius:12px;box-shadow:0 10px 28px rgba(13,13,18,.14);text-align:left; }
        .mp-action-panel form { display:block !important;margin:0;width:100%; }
        .mp-action-panel div.flex { display:block !important; }
        .mp-action-panel a,.mp-action-panel button { display:flex !important;align-items:center !important;justify-content:flex-start !important;gap:10px !important;width:100% !important;height:auto !important;min-height:38px;padding:9px 12px !important;margin:0;border:0 !important;border-radius:7px !important;background:transparent !important;box-shadow:none !important;font-size:13px !important;font-weight:500 !important;line-height:1.4;text-decoration:none;white-space:normal;text-align:left;cursor:pointer; }
        .mp-action-panel .primary { color:#0B266E !important; }
        .mp-action-panel .destructive,.mp-action-panel .error { color:#DF1C41 !important; }
        .mp-action-panel a:hover,.mp-action-panel button:hover { background:#F6F8FA !important; }
        .mp-action-panel button:disabled { opacity:.5;cursor:not-allowed; }
        .mp-action-panel svg { flex-shrink:0;width:16px;height:16px;order:-1; }
        .mp-action-panel .mp-action-divider { border-top:1px solid #DFE1E7;margin:5px 6px; }
    </style>
@endonce

<div x-data="{
    actionMenuOpen: false, actionLeft: 0, actionTop: 0,
    toggleActions() {
        if (this.actionMenuOpen) { this.actionMenuOpen = false; return; }
        window.dispatchEvent(new CustomEvent('mp-close-action-menus'));
        this.actionMenuOpen = true;
        this.$nextTick(() => {
            const trigger = this.$refs.actionTrigger.getBoundingClientRect();
            const panel = this.$refs.actionPanel.getBoundingClientRect();
            this.actionLeft = Math.max(8, Math.min(trigger.right - panel.width, window.innerWidth - panel.width - 8));
            this.actionTop = trigger.bottom + 7 + panel.height <= window.innerHeight - 8
                ? trigger.bottom + 7 : Math.max(8, trigger.top - panel.height - 7);
        });
    }
}" style="display:inline-block;" @mp-close-action-menus.window="actionMenuOpen = false"
    @keydown.escape.window="if (actionMenuOpen) { actionMenuOpen = false; $refs.actionTrigger.focus(); }"
    @scroll.window.capture="if (actionMenuOpen && !$refs.actionPanel.contains($event.target)) actionMenuOpen = false"
    @resize.window="actionMenuOpen = false">
    <button x-ref="actionTrigger" type="button" class="mp-action-trigger" aria-label="Buka menu aksi"
        aria-haspopup="true" :aria-expanded="actionMenuOpen" @click.stop="toggleActions()"
        @keydown.arrow-down.prevent="if (!actionMenuOpen) toggleActions(); $nextTick(() => $refs.actionPanel.querySelector('a,button')?.focus())"
        @keydown.tab="if (actionMenuOpen && !$event.shiftKey) { $event.preventDefault(); $refs.actionPanel.querySelector('a,button')?.focus(); }">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="19" cy="12" r="1.7"/></svg>
    </button>
    <template x-teleport="body">
        <div x-ref="actionPanel" x-show="actionMenuOpen" x-cloak x-transition.opacity.duration.150ms
            class="mp-action-panel" :style="{ left: actionLeft + 'px', top: actionTop + 'px' }"
            @click.outside="actionMenuOpen = false"
            @click="if ($event.target.closest('a,button')) actionMenuOpen = false"
            aria-label="Aksi yang tersedia">
            {{ $slot }}
        </div>
    </template>
</div>
