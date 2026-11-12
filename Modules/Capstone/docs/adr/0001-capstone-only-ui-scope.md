# Capstone-only UI scope

Capstone UI stays inside `Modules/Capstone` with `x-capstone::*` and `x-df-*` (prefix `df`) aliases. Host `x-ui.*` and `/superadmin/*` visuals are untouched. We chose isolation over sharing because SITKOM parity and feature gating differ per module, and a global rename would leak across modules.
