# 0010 — Selection Vocabulary

Date: 2026-11-12
Status: Accepted

Context: `dropdown`, `select`, and `combobox` were used interchangeably for menus, filters, and form inputs.

Decision: Three canonical terms. `Selection Menu` = custom listbox without search (filters, per-page, short forms). `Combobox` = searchable Selection Menu (lecturers, rooms, titles, groups). `Dropdown` = action menu only (profile, settings, logout), never a form value.

Consequences: `GLOSSARY.md` updated; `field type=select|combobox` renders the matching component; menus stay in `dropdown.blade.php` with listbox keyboard support.
