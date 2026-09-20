<div class="gpm-notifications" x-data="{
    open: false,
    loading: false,
    error: false,
    items: [],
    async loadNotifications() {
        if (this.loading) return;
        this.loading = true;
        this.error = false;
        try {
            const response = await fetch('{{ route('banksoal.gpm.notifications') }}', {
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) throw new Error('load');
            const data = await response.json();
            this.items = data.notifications || [];
        } catch (_) {
            this.error = true;
        } finally {
            this.loading = false;
        }
    },
    async toggle() {
        this.open = !this.open;
        if (this.open && this.error) await this.loadNotifications();
    }
}" x-init="loadNotifications()" @click.outside="open = false" @keydown.escape.stop="open = false; $refs.bell.focus()">
    <button type="button" x-ref="bell" class="gpm-notification-button" title="Notifikasi" aria-label="Notifikasi GPM"
            :aria-expanded="open" aria-controls="gpm-notification-panel" @click="toggle()">
        <i class="fas fa-bell"></i>
        <span class="gpm-notification-dot" x-show="items.length > 0" x-cloak></span>
    </button>

    <section id="gpm-notification-panel" class="gpm-notification-panel" x-show="open" x-cloak aria-label="Notifikasi terbaru">
        <header>
            <strong>Notifikasi</strong>
            <span>Antrean review terbaru</span>
        </header>
        <div class="gpm-notification-list" :aria-busy="loading">
            <p class="gpm-notification-state" role="status" x-show="loading">Memuat notifikasi...</p>
            <p class="gpm-notification-state" role="alert" x-show="error">Notifikasi gagal dimuat. Coba buka kembali.</p>
            <p class="gpm-notification-state" x-show="!loading && !error && !items.length">Belum ada antrean review.</p>
            <template x-for="item in items" :key="item.id">
                <a class="gpm-notification-item" :href="item.url">
                    <strong x-text="item.title"></strong>
                    <span x-text="item.description"></span>
                    <small x-text="item.time"></small>
                </a>
            </template>
        </div>
    </section>
</div>

@once
    <style>
        .gpm-notifications { position: relative; }
        .gpm-notification-button { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; color: #94a3b8; border-radius: 9999px; transition: color .2s, background-color .2s; }
        .gpm-notification-button:hover, .gpm-notification-button:focus-visible { color: #475569; background: #f8fafc; outline: none; }
        .gpm-notification-dot { position: absolute; top: .3rem; right: .3rem; width: .45rem; height: .45rem; border: 2px solid #fff; border-radius: 9999px; background: #ef4444; }
        .gpm-notification-panel { position: absolute; top: calc(100% + 10px); right: 0; width: 22.5rem; max-width: calc(100vw - 24px); overflow: hidden; z-index: 80; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 12px 32px #0f172a24; }
        .gpm-notification-panel header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 16px 12px; border-bottom: 1px solid #e2e8f0; }
        .gpm-notification-panel header strong { font-size: 13px; color: #0f172a; }
        .gpm-notification-panel header span { font-size: 11px; color: #64748b; }
        .gpm-notification-list { max-height: min(420px, 65vh); overflow-y: auto; overscroll-behavior: contain; }
        .gpm-notification-item { display: flex; flex-direction: column; gap: 4px; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; overflow-wrap: anywhere; }
        .gpm-notification-item:hover, .gpm-notification-item:focus-visible { background: #f8fafc; outline: none; }
        .gpm-notification-item strong { font-size: 12px; color: #1d4ed8; }
        .gpm-notification-item span { font-size: 12px; line-height: 1.6; color: #475569; }
        .gpm-notification-item small, .gpm-notification-state { font-size: 11px; color: #94a3b8; }
        .gpm-notification-state { padding: 20px 16px; line-height: 1.6; }
        @media (max-width: 767px) { .gpm-notification-panel { position: fixed; top: 58px; right: 12px; } }
    </style>
@endonce
