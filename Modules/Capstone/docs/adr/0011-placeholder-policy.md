# 0011 — Placeholder Policy

Date: 2026-11-12
Status: Accepted

Context: Many `field` call sites and raw dialog inputs shipped without placeholders, leaving empty inputs with no hint.

Decision: Every text-like input and select hint is Indonesian. Explicit `placeholder=` wins; otherwise `field` autofalls back (`Masukkan {label}`, `Pilih {label}`, `Cari...`, `cth., ...` per type). Checkbox, radio, and switch are excluded.

Consequences: No placeholder-less input ships; grep for `x-capstone::field` without `placeholder` may only return checkable types.
