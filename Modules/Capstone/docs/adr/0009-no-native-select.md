# 0009 — No Native Select Popups

Date: 2026-11-12
Status: Accepted

Context: PenguinUI `select` is a styled native `<select>`; its dropdown popup is OS-rendered (dark in the reporter's screenshot) and unstyleable. Filter pills, per-page counts, and form selects must all look and behave identically.

Decision: Zero visible `<select>` popups in `/capstone`. All selection UI uses custom Alpine listboxes (`select-menu`, searchable `combobox`). Native `<select>` survives only as hidden carriers inside component internals plus `<noscript>` fallbacks.

Consequences: `select-menu` exposes `x-modelable="selectedValue"` so parent `x-model` two-way binds; side effects via `@select-menu-change.window`; dynamic `:disabled` mapped through `disabled-when` prop (Blade forbids Alpine bindings on component tags except `::` shorthand).
