{{-- Capstone shell styles — ported from resources/views/components/sidebar.blade.php
     (<style> block) + resources/views/components/sidebar-link.blade.php (sb-item).
     Scoped under .sitkom-shell-capstone so host vars are never clobbered.
     Dark values reuse Modules/Capstone theme.css tokens via var(). --}}
<style>
.sitkom-shell-capstone {
    --c-primary: #0B266E;
    --c-primary-hover: #091958;
    --c-primary-subtle: rgba(11, 38, 110, 0.08);
    --c-primary-border: #5C78B8;
    --c-bg: #F6F8FA;
    --c-surface: #fff;
    --c-fg: #0D0D12;
    --c-fg-sec: #353849;
    --c-fg-muted: #666D80;
    --c-fg-placeholder: #808897;
    --c-border: #DFE1E7;
    --c-border-strong: #C1C7CF;
    --c-border-soft: #F0F1F4;
    --c-error: #DF1C41;
    --c-error-subtle: #FADAE1;
    font-family: 'Inter Tight', system-ui, sans-serif;
}
.dark .sitkom-shell-capstone {
    --c-bg: var(--background);
    --c-surface: var(--card);
    --c-fg: var(--card-foreground);
    --c-fg-sec: var(--sidebar-foreground);
    --c-fg-muted: var(--sidebar-foreground);
    --c-fg-placeholder: var(--sidebar-foreground);
    --c-border: var(--border);
    --c-border-strong: var(--sidebar-ring);
    --c-border-soft: var(--border);
    --c-primary: var(--sidebar-primary);
    --c-primary-hover: var(--sidebar-primary);
    --c-primary-subtle: var(--sidebar-accent);
}

/* ── Sidebar ── */
.sitkom-sidebar-capstone {
    background: var(--c-surface);
    border-right: 1px solid var(--c-border);
    transition: width .25s ease;
    overflow: hidden;
}
.sb-brand {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px;
    border-bottom: 1px solid var(--c-border);
    min-height: 60px;
    flex-shrink: 0;
}
.sb-brand-link { display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1; }
.sb-brand-logo { width: 32px; height: 32px; flex-shrink: 0; object-fit: contain; }
.sb-brand-text { display: grid; flex: 1; min-width: 0; text-align: left; line-height: 1.2; }
.sb-brand-name {
    font-weight: 700; font-size: 14px; color: var(--c-fg);
    letter-spacing: -.01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sb-brand-tag {
    font-size: 9px; color: var(--c-fg-placeholder); font-weight: 500; margin-top: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sb-collapse-btn {
    flex-shrink: 0; width: 28px; height: 28px; border-radius: 7px;
    border: 1px solid var(--c-border); background: var(--c-surface);
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    color: var(--c-fg-muted); transition: background .15s, border-color .15s, color .15s;
    padding: 0; margin-left: auto;
}
.sb-collapse-btn:hover { background: var(--c-bg); border-color: var(--c-border-strong); color: var(--c-fg); }
.sitkom-sidebar-capstone.is-collapsed .sb-brand { justify-content: center; }
.sitkom-sidebar-capstone.is-collapsed .sb-collapse-btn { margin-left: 0; }

.sb-nav {
    flex: 1; overflow-y: auto; overflow-x: hidden;
    padding: 6px 10px 10px; display: flex; flex-direction: column; gap: 1px; min-height: 0;
}
.sb-nav::-webkit-scrollbar { width: 3px; }
.sb-nav::-webkit-scrollbar-track { background: transparent; }
.sb-nav::-webkit-scrollbar-thumb { background: var(--c-border); border-radius: 9999px; }
.sb-section-label {
    font-size: 10px; font-weight: 600; color: var(--c-fg-placeholder);
    letter-spacing: .06em; text-transform: uppercase;
    padding: 12px 10px 5px; white-space: nowrap;
}
.sb-footer {
    padding: 8px 10px 12px; border-top: 1px solid var(--c-border);
    display: flex; flex-direction: column; gap: 1px; flex-shrink: 0;
}

/* ── sb-item (from sidebar-link) ── */
.sb-item {
    position: relative; display: flex; align-items: center; gap: 9px;
    padding: 7px 10px 7px 14px; border-radius: 8px;
    font-size: 13px; font-weight: 500; color: var(--c-fg-sec);
    cursor: pointer; text-decoration: none; background: none; border: none;
    font-family: inherit; transition: background .12s, color .12s;
    white-space: nowrap; overflow: hidden;
}
.sb-item svg { width: 16px; height: 16px; color: var(--c-fg-muted); flex-shrink: 0; }
.sb-item:hover { background: var(--c-bg); }
.sb-item.is-active { background: var(--c-primary-subtle); color: var(--c-primary); font-weight: 600; }
.sb-item.is-active svg { color: var(--c-primary); }
.sb-item-pill {
    position: absolute; left: 0; top: 50%; transform: translateY(-50%);
    width: 3px; height: 20px; background: var(--c-primary);
    border-radius: 0 3px 3px 0; flex-shrink: 0;
}
.sb-item.is-disabled { opacity: .45; cursor: default; pointer-events: none; }
.sb-item.is-collapsed { justify-content: center; padding-left: 0; padding-right: 0; }
.sb-item.is-collapsed .sb-item-pill { display: none; }
.sb-item-label { flex: 1; letter-spacing: .01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sb-item-badge {
    background: var(--c-primary-subtle); color: var(--c-primary);
    font-size: 11px; font-weight: 600; padding: 2px 7px; border-radius: 9999px; flex-shrink: 0;
}
.sb-sublist {
    margin-left: 14px; margin-right: 14px; display: flex; flex-direction: column; gap: 1px;
    border-left: 1px solid var(--c-border); padding: 2px 10px;
}
.sb-subitem {
    position: relative; display: flex; align-items: center; min-width: 0;
    height: 28px; padding: 0 8px; border-radius: 8px;
    font-size: 13px; font-weight: 500; color: var(--c-fg-sec);
    transition: background .12s, color .12s;
}
.sb-subitem:hover { background: var(--c-bg); }
.sb-subitem.is-active { background: var(--c-primary-subtle); color: var(--c-primary); font-weight: 600; }
.sb-subitem.is-disabled { opacity: .45; cursor: default; pointer-events: none; }
.sb-link-danger, .sb-link-danger svg { color: var(--c-error); }
.sb-link-danger:hover { background: var(--c-error-subtle); }

/* ── Topbar ── */
.sitkom-topbar-capstone {
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    height: 60px; padding: 0 28px; background: var(--c-surface);
    border-bottom: 1px solid var(--c-border);
    position: sticky; top: 0; z-index: 20; flex-shrink: 0;
}
.sitkom-crumb { font-size: 12px; color: var(--c-fg-muted); display: flex; align-items: center; gap: 7px; }
.sitkom-crumb b { color: var(--c-fg); font-weight: 600; }
.sitkom-crumb-sep { color: var(--c-border-strong); }
.sitkom-topbar-right { display: flex; align-items: center; gap: 8px; }
.sitkom-topbar-user { display: flex; align-items: center; gap: 10px; padding-left: 12px; border-left: 1px solid var(--c-border); margin-left: 4px; }
.sitkom-topbar-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #5C78B8, #0B266E); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0; overflow: hidden;
}
.sitkom-topbar-meta { line-height: 1.2; display: flex; flex-direction: column; align-items: flex-start; text-align: left; }
.sitkom-topbar-name { font-size: 13px; font-weight: 600; color: var(--c-fg); white-space: nowrap; }
.sitkom-topbar-role { font-size: 11px; color: var(--c-fg-muted); white-space: nowrap; }
.capstone-logout-item:hover { background: var(--c-error-subtle); }

@media (max-width: 767px) {
    .sitkom-topbar-capstone { padding: 0 14px; height: 52px; gap: 10px; }
    .sitkom-topbar-meta { display: none; }
    .sitkom-topbar-user { padding-left: 8px; gap: 6px; }
    .sitkom-topbar-avatar { width: 30px; height: 30px; font-size: 10px; }
}
</style>
