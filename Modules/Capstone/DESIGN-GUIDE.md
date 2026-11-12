# Capstone (SICATA) — Sidebar / Header / Layout / Icons Design Guide

> Scope: `Modules/Capstone` Blade shell, aligned to SITKOM superadmin.
> Audience: all roles (`admin`, `dosen`, `mahasiswa`) — clarity first.
> Sources: `resources/views/layouts/app.blade.php`, `layouts/sidebar.blade.php`,
> `layouts/topbar.blade.php`, `components/icon.blade.php`,
> `resources/reference/navigation.json`, `resources/reference/icons.json`,
> `resources/assets/css/theme.css`.
> Reference shell: `resources/views/components/sidebar.blade.php` (SITKOM),
> `resources/views/components/sidebar-link.blade.php` (`sb-item` contract),
> header primitives `resources/views/components/ui/dropdown.blade.php`,
> `ui/dropdown-item.blade.php`, `ui/separator.blade.php`, `ui/avatar.blade.php`,
> `ui/button.blade.php`, `resources/views/layouts/app.blade.php`.

## Design Principles

1. **Match SITKOM, don't fork it.** Same widths, heights, section labels, radii, and interaction rhythm. Users switch between `/superadmin/*` and `/capstone/*` constantly.
2. **Scannable over dense.** Section labels + `h-8` rows + `8px` gaps. One scroll container only.
3. **Calm confidence for long workflows.** Navy primary, muted greys, semantic colors reserved for meaning (success/warning/error/sky).
4. **Forgiving admin density.** Large-enough hit targets, visible hover/active, disabled states explain *why* via `title`.

## Design Tokens (Light + Dark)

Capstone uses Tailwind v4 `@theme inline` in `resources/assets/css/theme.css`.
Target = SITKOM values. Migrate toward the right column.

| Token | Capstone current | SITKOM target (use this) |
|---|---|---|
| Primary | `--primary: #293C79` | `--c-primary: #0B266E`, hover `#091958`, subtle `rgba(11,38,110,.08)` |
| Page bg | `--background: #F8F9FB` | `--c-bg: #F6F8FA` |
| Surface / sidebar | `--sidebar: #ffffff`, `--card: #ffffff` | `#fff`, border `#DFE1E7` |
| Text | `--foreground: #353849`, muted `#808897` | `#0D0D12` / `#353849` / muted `#666D80` / placeholder `#808897` |
| Border / input / ring | `#DFE1E6` / `#6F7DA4` | `#DFE1E7` / strong `#C1C7CF` |
| Semantic | success `#40C4AA`, warning `#FFBD4C`, error `#DF1C41`, sky `#33CFFF` | success `#287F6E`/`#DDF2EE`, warning `#956321`/`#F9ECCB`, error `#DF1C41`/`#FADAE1`, sky `#0C4D6E`/`#D1F0F9` |
| Font | `Segoe UI, Arial` (`--font-geist-sans`) | `Inter Tight, system-ui` + brand `Geist` (see `resources/views/layouts/app.blade.php:16`) |
| Radius | `--radius: 0.625rem` (`sm/md/lg/xl…4xl` derived) | Keep. Links use `8px`, cards `12px` (`rounded-xl`) |
| Shadow | `--shadow-small`, `--shadow-medium` | `--shadow-card: 0 1px 2px rgba(228,229,231,.5)` for cards |
| Sidebar dark | `--sidebar: #1A1B25`, fg `#F8F9FB`, accent `#415086` | Keep `theme.css` `.dark` block as-is; only swap light primary/bg/font |

Dark mode is already defined in `theme.css` (`.dark` overrides for background, card, sidebar, charts). Do not invent new dark values — reuse them.

## Layout Shell

```
+----------------------------------------------------------+
| sidebar (240 open / 64 collapsed) | main                 |
|  brand 60px                       |  topbar 60px sticky    |
|  nav (scroll)                     |  content 24/28/48      |
|  footer                           |   card rounded-xl      |
+----------------------------------------------------------+
Mobile <768px: sidebar hidden -> <x-mobile-navigation/> bottom nav.
Topbar 52px, content 16/14 + 80px bottom clearance.
```

Blade skeleton (`layouts/app.blade.php`) — SITKOM structure, topbar above content:

```blade
<div class="flex min-h-svh w-full bg-[#F6F8FA]" style="--sidebar-width:240px">
  @include('capstone::layouts.sidebar')
  <main class="flex h-screen w-full min-w-0 flex-col overflow-hidden">
    @include('capstone::layouts.topbar') {{-- 60px, sticky, full-width --}}
    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-7 sm:pb-12">
      <div class="capstone-mobile-content w-full pb-20 md:pb-0">@yield('content')</div>
    </div>
  </main>
</div>
```

Rules:

- One vertical scroll: the `flex-1 overflow-y-auto` wrapper. Never nest another page-level scroller.
- No card-in-page wrapper: topbar sits full-width above content (`sticky top-0 z-30`); pages bring their own cards.
- Global overlays: progress bar (`capstone-progress`, `pendingRequests > 0`) at top; toasts (`capstone-notices fixed bottom-5 right-5 w-80`) bottom-right; both defined in `assets/css/app.css`.
- Target SITKOM metrics: sidebar `240/64px` (not `16rem/4rem` ≈ `256/64px`), topbar `60px` with `0 28px` padding, content `24px 28px 48px`. See `resources/views/components/sidebar.blade.php:445-547`.

## Sidebar

File: `resources/views/layouts/sidebar.blade.php` (+ `components/sb-link.blade.php`,
`layouts/shell-styles.blade.php`). Data: `navigation.json` + `$actor`, `$activeRole`, `$pagePath`, `$featureAccess`.

`sb-link` is a Capstone-owned mirror of the host `x-sidebar-link` (`sb-item` contract:
`is-active` + left pill, `is-disabled`, `is-collapsed`, `sb-item-badge`) driven by the
Capstone Alpine state (`collapsed`) with Lucide `x-capstone::icon` instead of inline
stroke paths. Shell CSS (`sb-brand`, `sb-nav`, `sb-item`, `sitkom-topbar-capstone`)
is ported from the reference `<style>` block into `shell-styles.blade.php`, scoped under
`.sitkom-shell-capstone` (dark values reuse `theme.css` tokens via `var()`).

Anatomy (top → bottom):

1. **Brand row** — logo `32px` (`/capstone/assets/logo.png`) + `SICATA` (semibold 14px) + `Sistem Informasi Capstone & TA` (9–12px muted). Collapse button `28px bordered rounded-md`, `ChevronLeft`, rotates `180°` when collapsed (SITKOM pattern: `sb-collapse-btn`, `sidebar.blade.php:67-76`).
2. **Nav** — `flex-1 overflow-y-auto p-2 space-y-1`, scrollbar `3px`. Section label: `10px uppercase semibold tracking .06em, #808897`, e.g. `Menu`, `System`, plus per-role `Admin`/`Dosen` when combined. SITKOM labels: `Main Menu / Kendali / Akademik / Setting`.
3. **Footer** — `border-t p-2`: `Settings` (admin only via `<x-capstone::feature-link href="/admin/settings">`), `Logout` (destructive, `POST capstone.logout`).

Link spec:

- Row: `flex h-8 items-center gap-2 rounded-md p-2 text-sm`, icon `16px`, hover `bg-sidebar-accent`, active `bg-sidebar-accent font-medium` when `$pagePath === $item['url']`.
- Expandable group: `button w-full` + trailing `ChevronRight size-4`, rotates `90°` on expand (`:class="expanded && 'rotate-90'"`). Sub-list: `mx-3.5 border-l px-2.5 py-0.5`, sub-links `h-7 text-sm`.
- Collapsed (`w-16` → target `64px`): hide labels (`x-show="!collapsed"`), keep icons + `title` tooltips. Clicking a group when collapsed first expands the sidebar.
- Disabled (mahasiswa gating): `BladeFeatureAccess::reason($url, $featureAccess)` → `aria-disabled="true" tabindex="-1" pointer-events-none opacity-50`, `title` = reason. Never hide — explain.
- Badge: `Notifications` shows `unread` count (`rounded-full bg-primary text-white px-1.5 text-xs`, `x-show="unread > 0"`).

Adding a nav item (`resources/reference/navigation.json`):

```json
{ "title": "My Schedule", "url": "/mahasiswa/schedule", "icon": "Calendar" }
```

- Parent `icon` must exist in `icons.json` (normalize `CalendarIcon` → `Calendar` in sidebar).
- Group form: `{ "title": "...", "icon": "Users", "items": [...] }`.
- Keep `admin` groups to the four existing buckets: `Master Data`, `Period Setup`, `Management`, `Analytics`.

Do / Don't:

- Do keep labels short (1–2 words), Title Case, same as breadcrumb target.
- Do use `title="reason"` on disabled links.
- Don't add a second sidebar-level CTA — page actions belong in `partials/schedule-actions` / page headers.
- Don't change icon size per-row; `16px` everywhere (SITKOM `sb-link svg 16px`).

## Header (Topbar)

File: `resources/views/layouts/topbar.blade.php` — composed from host ui primitives:
`x-ui.button` (search/bell, forced to `34px/8px` via inline style), `x-ui.dropdown` +
`x-ui.dropdown-item` + `x-ui.separator` (user menu; Settings keeps
`x-capstone::feature-link` gating), `x-ui.avatar` (photo path; initials keep the
SITKOM gradient). Crumb, divider, and metrics follow `sitkom-topbar`.

Anatomy (left → right):

1. **Mobile menu button** (`md:hidden`, `Menu` icon, toggles `mobileSidebar`).
2. **Breadcrumb**: `SICATA / {Page}` — `12–14px`, root muted `#808897`, sep `#C1C7CF`, current `#353849 semibold`. Derived from `$pagePath` (numeric tail segments resolve to parent).
3. **Actions**: search `h-9 w-9 rounded-full border` (`Search`), notifications bell with dot (`bg-error-100`, `x-show="unread > 0"`), user chip (`initials circle 32px bg-grey-100` + name 13–14px semibold + role 11–12px muted, `hidden sm:flex`).
4. **User dropdown**: `absolute right-0 mt-2 w-56 rounded-md border bg-popover p-1 shadow-md z-50` — header (name + email), `Profile`, `Settings` (admin only), divider, `Logout` (`text-error-100`).

Spec:

- Container: `sticky top-0 z-30 flex items-center justify-between gap-4 px-3 sm:px-6 py-4 border-b border-grey-100 bg-white`. Target SITKOM: fixed `60px` height, `0 28px` padding (`sitkom-topbar`).
- Icon buttons: `34px rounded 8px bordered` on SITKOM vs `36px rounded-full` on Capstone — **use SITKOM `34px/8px` for new work**.
- Mobile: hide name/role text, keep avatar `30px`; topbar `52px`, `0 14px` padding.

## Icons

Component: `resources/views/components/icon.blade.php` (Phosphor Regular via `@phosphor-icons/web`, `<i class="ph ph-{kebab}">`).

Usage:

```blade
<x-capstone::icon name="SquaresFour" />
<x-capstone::icon name="CalendarBlank" size="16" />
<x-capstone::icon name="CaretRight" size="16" class="ml-auto transition-transform" />
```

Rules:

- `name` is PascalCase Phosphor key; default render Regular weight, `size` prop sets `font-size` (16 rows, 20 topbar actions). Never inline `width/height`.
- Canonical map (use these, don't invent synonyms):

| Meaning | Icon name |
|---|---|
| Dashboard | `SquaresFour` |
| Users / Groups / Mentoring | `Users` |
| Schedule / Periods | `CalendarBlank` |
| Grades / Evaluation setup | `GraduationCap` |
| Documents / Bimbingan | `FileText` |
| TA Submission | `FileCheck` |
| Titles / Marketplace | `BookOpen` |
| Bids | `Gavel` |
| Expo | `PresentationChart` |
| Peer review | `Star` |
| Approval / Schedule check | `ClipboardText` |
| Setup / Master / Management | `Gear`, `ListChecks` |
| Charts / Reports | `ChartBar`, `TrendUp` |
| Audit | `Scroll` |
| Notifications | `Bell` |
| Search / Menu / Close | `MagnifyingGlass` / `List` / `X` |
| Collapse / Expand | `CaretLeft` / `CaretRight` |
| Account / Logout | `User` / `SignOut` |
| Location | `MapPin` |
| Add user | `UserPlus` |
| Finalize | `ShieldCheck` |
| Library | `Library` |

- Unknown names log `capstone.icon.unknown` — if you see a circle, the key is misspelled (see `icon.blade.php` allowlist). Old Lucide aliases (`LayoutDashboard`, `Calendar`, `Chevron*`, `LogOut`, etc.) still resolve via the map but new code must use Phosphor names.
- Add new icons by extending the allowlist in `icon.blade.php` with the Phosphor kebab. No per-page SVGs.
- A11y: `aria-hidden="true"` is built-in; the parent link/button must carry `title` / `aria-label` (sidebar does this; keep it).

## Responsive + Accessibility

- Breakpoint `768px`: desktop sidebar `display:none`, bottom nav takes over (`<x-mobile-navigation/>` included in both `app.blade.php` shells). Content gets `80px` bottom padding so the nav never covers actions.
- Focus: `focus-visible:outline-2 outline-ring` on toast close and interactive rows (see `layouts/app.blade.php:42`).
- Live regions: toasts use `aria-live="polite"`; progress bar uses `role="status"`.
- Motion: `assets/css/app.css` disables loader/progress/notice animations under `prefers-reduced-motion`. Keep it — no new animation without a reduced-motion fallback.

## Migration Checklist (Capstone → SITKOM parity)

- [ ] Font: load `Inter Tight` (+ `Geist` brand) like `resources/views/layouts/app.blade.php:16`; replace `Segoe UI` default in `theme.css:100`.
- [ ] Primary/bg: `--primary #293C79 → #0B266E`, `--background #F8F9FB → #F6F8FA` (light only; keep `.dark`).
- [ ] Sidebar width: `--sidebar-width:16rem → 240px`, collapsed `w-16 (64px)` stays; move collapse button to bordered `28px` box style.
- [ ] Section labels: sentence `Menu/System` → SITKOM-style uppercase groups; keep per-role split for `admin+dosen`.
- [ ] Topbar: fixed `60px`, `0 28px` padding; icon buttons `34px rounded-lg` (not `36px full`).
- [ ] Content: `p-4 card` → `24px 28px 48px` rhythm (keep `rounded-xl border shadow-small` card).
- [ ] Icons: audit all `name=` against the canonical map; fix `CalendarIcon → Calendar`; enforce `16px` rows / `20px` topbar actions.
- [ ] Verify: `php artisan test --compact`, compare `/capstone/admin/dashboard` vs `/superadmin/dashboard` side-by-side (light + `.dark`), `767px` viewport for bottom-nav clearance.
