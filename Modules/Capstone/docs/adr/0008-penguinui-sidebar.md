# 0008 — PenguinUI Sidebar Skeleton

Date: 2026-11-11
Status: Accepted

Context: Custom `sb-*` CSS split across `sidebar.blade.php`, `sb-link.blade.php`, and `shell-styles.blade.php`. PenguinUI ships a sidebar pattern with the same structure.

Decision: Adopt PenguinUI sidebar skeleton (brand row, section labels, expandable groups with `x-collapse`, sublist) while freezing behavior: `navigation.json` data, `240/64` widths, pill + badge, `reason()` gating, `CapstoneUrl`, `x-trap` mobile-only, Esc, footer Settings + logout.

Consequences: `shell-styles` keeps only tokens + layout + scrollbar; `sb-item` selectors cover `i.ph` as well as `svg`. `GLOSSARY` no longer forbids Penguin sidebar.
