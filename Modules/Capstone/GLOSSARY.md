# Capstone UI

Capstone-only reusable Blade UI for `admin`, `dosen`, `mahasiswa` roles. SITKOM is the visual reference; PenguinUI Modern is the markup reference; Phosphor Regular is the icon set; behavior follows Alpine stores.

## Language

**Primitive**:
Dumb presentational Blade in `resources/views/components/` with `variant/size/slot` props and no domain knowledge. PenguinUI Modern markup inside, same API.
_Avoid_: Widget, partial for these

**Domain Widget**:
Stateful composition in `components/` + `partials/` bound to Capstone data (`search/sort/page/bulk/grouped/loading/error`). PenguinUI supplies skin only; `capstoneTable.js` owns state.
_Avoid_: Generic component, bare table

**Layout Shell**:
`layouts/app/sidebar/topbar/shell-styles` + `sb-link`. Owns collapse, navigation, topbar, and bottom-nav. PenguinUI sidebar skeleton allowed (240/64, pill, badge, gating preserved).
_Avoid_: Generic header

**Gating Wrapper**:
`sb-link` and `feature-link`, the only place calling `BladeFeatureAccess::reason()`.
_Avoid_: Gated button, middleware check in Blade

**Form Field**:
`field` wrapper binding `x-model="form.{name}"` with `errors[{name}]`. PenguinUI input markup. Every text-like input renders an Indonesian placeholder (explicit or label-derived autofallback: `Masukkan {label}` / `Pilih {label}` / `Cari...`).
_Avoid_: Server old()/error field, placeholder-less input

**Selection Menu**:
Custom PenguinUI listbox (`button + role=listbox/option + hidden input`, `x-modelable`) for filters, per-page counts, and short forms. No OS popup.
_Avoid_: Native select, dropdown menu for values

**Combobox**:
Searchable Selection Menu for long or async lists (lecturers, rooms, titles, groups) with filter input and empty state.
_Avoid_: 50-option native select

**Dropdown**:
Action menu only (`profile/settings/logout`). Never holds a form value.
_Avoid_: Select, combobox

**Icon Key**:
PascalCase Phosphor key rendered as `<i class="ph ph-{kebab}">` Regular via `@phosphor-icons/web` per `DESIGN-GUIDE.md`.
_Avoid_: Ad-hoc SVG, heroicon, Lucide path

## Backend

**Partition**:
Status buckets owning every machine status exactly once.
_Avoid_: Tab filter, group-by

**Pooled Average**:
Group-level grade over submitted rows only; missing members are excluded, never zero-filled.
_Avoid_: Average, mean

**Write-Orchestration vs Reads**:
Service-owned mutations and guards vs support-owned queries; requests shape-check only.
_Avoid_: Controller logic, helper

**TA vs TA_DRAFT**:
UI display code vs engine approval-chain code (`TA` normalizes to `TA_DRAFT` where they meet).
_Avoid_: TA phase

**SIDANG**:
UI-only document bucket gated on EXPO approval; no engine state.
_Avoid_: Engine phase, defense stage

**Dissolved**:
Terminal partitioned state, still listed in the post tab; re-registration requires a new group.
_Avoid_: Deleted, archived

**Indicator Weight**:
Peer-review importance.
_Avoid_: Bare weight

**Component Weight**:
Template weight in grade math.
_Avoid_: Bare weight

**Phase Weight**:
Admin-configured weight per phase.
_Avoid_: Bare weight
