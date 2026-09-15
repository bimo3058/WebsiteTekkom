<div class="sa-notifications" x-data="{
    open: false, loading: false, error: false, items: [],
    async toggle() {
        this.open = !this.open;
        if (!this.open) return;
        this.loading = true;
        this.error = false;
        this.items = [];
        try {
            const response = await fetch('<?php echo e(route('superadmin.notifications.index')); ?>', { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('load');
            const data = await response.json();
            this.items = data.notifications;
        } catch (_) { this.error = true; }
        finally { this.loading = false; }
    }
}" @click.outside="open = false" @keydown.escape.stop="open = false; $refs.bell.focus()">
    <button type="button" x-ref="bell" class="sitkom-icon-btn" title="Notifikasi" aria-label="Notifikasi superadmin"
            :aria-expanded="open" aria-controls="superadmin-notification-panel" @click="toggle()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M6 8a6 6 0 1112 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10 21a2 2 0 004 0"/>
        </svg>
    </button>
    <section id="superadmin-notification-panel" class="sa-notification-panel" x-show="open" x-cloak aria-label="Notifikasi terbaru">
        <header><strong>Notifikasi</strong><a href="<?php echo e(route('profile.edit', ['tab' => 'notifikasi'])); ?>">Pengaturan</a></header>
        <p class="sa-notification-hint">Aktivitas terbaru · 7 hari terakhir</p>
        <div class="sa-notification-list" :aria-busy="loading">
            <p class="sa-notification-state" role="status" x-show="loading">Memuat notifikasi…</p>
            <p class="sa-notification-state" role="alert" x-show="error">Notifikasi gagal dimuat. Tutup lalu buka kembali untuk mencoba lagi.</p>
            <p class="sa-notification-state" x-show="!loading && !error && !items.length">Belum ada aktivitas sesuai pilihan notifikasi Anda.</p>
            <template x-for="item in items" :key="item.id">
                <a class="sa-notification-item" :href="item.url">
                    <strong x-text="item.title"></strong>
                    <span x-text="item.description"></span>
                    <small x-text="item.time"></small>
                </a>
            </template>
        </div>
    </section>
</div>
<style>
    .sa-notifications { position:relative; }
    .sa-notification-panel { position:absolute; top:calc(100% + 10px); right:0; width:360px; max-width:calc(100vw - 24px); background:#fff; border:1px solid var(--c-border); border-radius:12px; box-shadow:0 12px 32px #0f172a24; z-index:80; overflow:hidden; }
    .sa-notification-panel header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 16px 6px; font-size:13px; }
    .sa-notification-panel header a { color:var(--c-primary); font-size:12px; }
    .sa-notification-hint { padding:0 16px 12px; margin:0; color:var(--c-fg-muted); font-size:11px; }
    .sa-notification-list { max-height:min(420px,65vh); overflow-y:auto; overscroll-behavior:contain; }
    .sa-notification-item { display:flex; flex-direction:column; gap:4px; padding:12px 16px; border-top:1px solid var(--c-border); color:var(--c-fg); text-decoration:none; overflow-wrap:anywhere; }
    .sa-notification-item:hover, .sa-notification-item:focus-visible { background:#f8fafc; }
    .sa-notification-item strong { font-size:12px; }
    .sa-notification-item span { font-size:12px; line-height:1.6; color:var(--c-fg-sec); }
    .sa-notification-item small { font-size:10px; color:var(--c-fg-muted); }
    .sa-notification-state { padding:20px 16px; font-size:12px; line-height:1.6; color:var(--c-fg-muted); }
    @media(max-width:767px) { .sa-notification-panel { position:fixed; top:58px; right:12px; } }
</style>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views/superadmin/partials/notification-bell.blade.php ENDPATH**/ ?>