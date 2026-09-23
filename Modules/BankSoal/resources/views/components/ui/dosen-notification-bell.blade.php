<div class="dosen-notifications" x-data="{
    open: false,
    loading: false,
    error: false,
    items: [],
    async loadNotifications() {
        if (this.loading) return;
        this.loading = true;
        this.error = false;
        try {
            const response = await fetch('{{ route('banksoal.dosen.notifications') }}', {
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
    <button type="button" x-ref="bell" class="dosen-notification-button" title="Notifikasi" aria-label="Notifikasi Dosen"
            :aria-expanded="open" aria-controls="dosen-notification-panel" @click="toggle()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M6 8a6 6 0 1112 0c0 7 3 9 3 9H3s3-2 3-9" />
            <path d="M10 21a2 2 0 004 0" />
        </svg>
        <span class="dosen-notification-dot" x-show="items.length > 0" x-cloak></span>
    </button>

    <section id="dosen-notification-panel" class="dosen-notification-panel" x-show="open" x-cloak aria-label="Notifikasi terbaru">
        <header>
            <strong>Notifikasi</strong>
            <span>Status pengajuan Anda</span>
        </header>
        <div class="dosen-notification-list" :aria-busy="loading">
            <p class="dosen-notification-state" role="status" x-show="loading">Memuat notifikasi...</p>
            <p class="dosen-notification-state" role="alert" x-show="error">Notifikasi gagal dimuat. Coba buka kembali.</p>
            <p class="dosen-notification-state" x-show="!loading && !error && !items.length">Belum ada pembaruan pengajuan.</p>
            <template x-for="item in items" :key="item.id">
                <a class="dosen-notification-item" :href="item.url">
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
        .dosen-notifications { position: relative; }
        .dosen-notification-button { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; color: #94a3b8; border-radius: 9999px; transition: color .2s, background-color .2s; }
        .dosen-notification-button:hover, .dosen-notification-button:focus-visible { color: #475569; background: #f8fafc; outline: none; }
        .dosen-notification-dot { position: absolute; top: .3rem; right: .3rem; width: .45rem; height: .45rem; border: 2px solid #fff; border-radius: 9999px; background: #ef4444; }
        .dosen-notification-panel { position: absolute; top: calc(100% + 10px); right: 0; width: 22.5rem; max-width: calc(100vw - 24px); overflow: hidden; z-index: 80; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 12px 32px #0f172a24; }
        .dosen-notification-panel header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 16px 12px; border-bottom: 1px solid #e2e8f0; }
        .dosen-notification-panel header strong { font-size: 13px; color: #0f172a; }
        .dosen-notification-panel header span { font-size: 11px; color: #64748b; }
        .dosen-notification-list { max-height: min(420px, 65vh); overflow-y: auto; overscroll-behavior: contain; }
        .dosen-notification-item { display: flex; flex-direction: column; gap: 4px; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; overflow-wrap: anywhere; }
        .dosen-notification-item:hover, .dosen-notification-item:focus-visible { background: #f8fafc; outline: none; }
        .dosen-notification-item strong { font-size: 12px; color: #1d4ed8; }
        .dosen-notification-item span { font-size: 12px; line-height: 1.6; color: #475569; }
        .dosen-notification-item small, .dosen-notification-state { font-size: 11px; color: #94a3b8; }
        .dosen-notification-state { padding: 20px 16px; line-height: 1.6; }
        @media (max-width: 767px) { .dosen-notification-panel { position: fixed; top: 58px; right: 12px; } }
    </style>
@endonce
