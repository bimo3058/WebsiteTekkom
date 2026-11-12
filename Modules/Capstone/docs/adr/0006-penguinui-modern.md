# 0006 — PenguinUI Modern over Classic

Date: 2026-11-11
Status: Accepted

Context: PenguinUI ships Classic (hardcoded Tailwind colors) and Modern (CSS-variable semantic theming). Capstone has semantic `theme.css` tokens plus a `.dark` block.

Decision: Use Modern. Map PenguinUI `--color-surface/on-surface/primary/outline/...` to existing `--background/foreground/primary/border/...` so SITKOM `#0B266E` and dark mode keep working with zero value changes.

Consequences: One `app.css` bundle (Tailwind + Phosphor). No hardcoded color forks. `dark:` variants resolve via existing `.dark` vars.
