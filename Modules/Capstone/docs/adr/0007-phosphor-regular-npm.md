# 0007 — Phosphor Regular via NPM

Date: 2026-11-11
Status: Accepted

Context: Capstone used Lucide paths in `resources/reference/icons.json` rendered as inline SVG. Decision: all `/capstone` icons become Phosphor Regular.

Decision: Install `@phosphor-icons/web`, import `regular` weight once in `assets/js/app.js`, keep `<x-capstone::icon name=>` API as a thin wrapper emitting `<i class="ph ph-{kebab}">`, remap all call sites at once, keep `Log::warning('capstone.icon.unknown')`.

Consequences: `icons.json` retired (kept on disk, unused). Only Regular weight bundled (~fonts in `public/build/fonts`). 16px rows / 20px topbar via `size` prop (font-size).
