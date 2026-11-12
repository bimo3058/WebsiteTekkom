# Alpine-event dialog as canonical

`x-capstone::dialog` (native `<dialog>` + `open-{id}/close-{id}` window events, backdrop `@click`, `Esc`) is canonical over a `div`-based modal. Native `showModal()` already focus-traps, existing `document.getElementById(...).showModal()` calls keep working, and new code dispatches events. This required `@alpinejs/focus` in the Capstone build only.
