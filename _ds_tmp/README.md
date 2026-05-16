# SITKOM Design System

**SITKOM** (Sistem Informasi Teknik Komputer) is the academic web application for the Computer Engineering department (Teknik Komputer) at Universitas Diponegoro (UNDIP). It is a multi-module web platform used by students, lecturers, staff, and administrators for academic management tasks.

---

## Sources

- **Figma**: `Website Tekkom Design File.fig` (mounted as VFS) — 69 pages, 131 top-level frames. Key pages: `/Style-Guide`, `/Components`, `/Icons`, `/Template-Dashboard`, `/Desktop-Version`, `/Responsive-Version`
- **Codebase**: GitHub repo `bimo3058/WebsiteTekkom` (Laravel + Tailwind CSS + Blade templates, modular architecture)
- **Uploaded Assets**: SVG icon set in `uploads/`, UNDIP logo at `uploads/UNDIPOfficial.png`

---

## Product Context

SITKOM is structured as a **Laravel modular application** with 4 core modules:

| Module | Description |
|---|---|
| **BankSoal** | Question bank management — exam questions, scheduling, sessions |
| **Capstone** | Capstone/final project management |
| **EOffice** | E-Office administrative tools |
| **ManajemenMahasiswa** | Student management — data, records, enrollment |

The app serves multiple user roles (mahasiswa/students, dosen/lecturers, admin, GPM) and includes authentication (login, register, forgot password), dashboard overview, data tables, calendar views, and settings.

The design system used in Figma is named **"LuminHR"** (the template base) but has been adapted for **SITKOM** branding with the app name "SITKOM" and tagline "Sistem Informasi Teknik Komputer".

---

## CONTENT FUNDAMENTALS

- **Language**: Bahasa Indonesia is the primary language for labels, menu items, and navigation (e.g. "Utama", "Kendali", "Setting"). English is used for form labels, status text, and technical content.
- **Tone**: Formal and institutional. Clean, direct language. No marketing fluff. Academic context.
- **Casing**: Title Case for section headers and page titles; Sentence case for body text and hints.
- **I vs You**: Third-person / role-based. Labels use "Mahasiswa", "Dosen", "Admin" — not "You" or "I".
- **Emoji**: Not used. No emoji in UI.
- **Unicode**: Minimal. Only standard punctuation.
- **Numbers**: Indonesian academic year format (e.g. "2024/2025").
- **Copy examples**:
  - "Welcome Back" / "Glad to see you again. Log in to your account."
  - "Modul Setting", "System Monitor", "Bank Soal", "Manajemen Mahasiswa"
  - "This is a hint text to help user"
  - "© 2025 LuminHR. All right reserved."
  - "Don't have an account? Register"

---

## VISUAL FOUNDATIONS

### Colors
- **Primary**: Purple scale — `#897EFA` (300), `#6B39F4` (400/500), `#7A4DF5` (strong). Active states use `#6B39F4`.
- **Greyscale**: Ranges from `#F6F8FA` (25) → `#0D0D12` (900). Most body text is `#0D0D12` or `#666D80`.
- **Alert/Success**: Green tones `#DDF2EE` → `#174E43`.
- **Alert/Warning**: Amber `#F9ECCB` → `#5B3D1E`.
- **Alert/Error**: Red `#FADAE1` → `#710E21`. Error highlight: `rgb(223,28,65)`.
- **Additional/Sky**: Cyan `#D1F0F9` → `#0C4D6E`.
- **Background**: `#F6F8FA` (app bg), `#FFFFFF` (cards/panels), `#F8FAFB` (subtle surfaces).

### Typography
- **Primary Font**: **Inter Tight** — used for nearly all UI text (headings, body, labels, buttons). Loaded from Google Fonts.
- **Secondary Font**: **Geist** — used for the SITKOM brand name/logo only.
- **Display Font**: **Inter** (Regular/Bold) — used occasionally for large numbers and display text.
- **Type Scale**:
  - H1: 48px Bold
  - H2: 40px SemiBold
  - H3: 32px SemiBold
  - H4: 24px SemiBold
  - H5: 20px SemiBold
  - H6: 18px SemiBold
  - Body Large: 16px Regular
  - Body Medium: 14px Regular
  - Body Small: 12px Regular
  - Labels: 14px Medium (letter-spacing: 0.010em)
- **Letter spacing**: `0.010em`–`0.020em` on most UI text
- **Line heights**: 1.3–1.5×

### Spacing & Layout
- **Base unit**: 4px
- **Component padding**: 8–32px
- **Content max-width**: 1200px (on 1440px canvas)
- **Sidebar width**: 272px (collapsible)
- **Toolbar height**: 53px
- **Grid gaps**: 12px (color swatches), 16–20px (cards/widgets), 24–32px (sections)

### Cards & Containers
- **Border**: `1px solid rgb(223,225,231)` — very subtle grey
- **Border radius**: 14px (main content panels), 12px (form inputs), 10px (small inputs), 8px (dropdowns/buttons), 6px (chips/badges), `9999px` (pill badges)
- **Shadow (cards)**: `0px 1px 2px 0px rgba(228,229,231,0.24)` — extremely subtle
- **Shadow scale**: xs → xxlarge (from tailwind config)
- **Background**: White cards on `#F6F8FA` page background

### Shadows
- **XS**: `0 1px 2px rgba(22,22,43,0.04)`
- **SM**: `0 1px 3px rgba(22,22,43,0.06), 0 1px 2px rgba(22,22,43,0.04)`
- **MD**: `0 4px 8px -2px rgba(22,22,43,0.06), 0 2px 4px -2px rgba(22,22,43,0.04)`
- **LG**: `0 12px 20px -4px rgba(22,22,43,0.08), 0 4px 8px -4px rgba(22,22,43,0.04)`
- **XL**: `0 20px 25px -5px rgba(22,22,43,0.10), 0 8px 10px -6px rgba(22,22,43,0.04)`

### Backgrounds
- Page background: `#F6F8FA` flat — no gradients, no textures
- Login screen: subtle dot-grid pattern behind form, radial white mask overlay
- No full-bleed images, no decorative gradients in app UI
- Cards are always solid white

### Animations & Interactions
- Transitions: `transition-colors` (150–200ms ease-out) on hover states
- Hover (buttons): slightly darker background. No opacity changes.
- Focus (inputs): `border-color: #6b4ff4` + `box-shadow: 0 0 0 3px rgba(107,79,244,0.1)`
- Active (sidebar items): filled purple background `#6B39F4`, white text/icon
- Dropdown: `slideDown` 0.15s ease-out
- Snackbar: `slideUpIn` with slight spring `cubic-bezier(0.34,1.56,0.64,1)`
- No bounce, no scale press states visible

### Borders & Dividers
- Dividers: `1px solid rgb(226,232,240)` or `rgb(223,225,231)`
- Input borders: `1px solid #e2e8f0`, focus: `#6b4ff4`
- Badge borders: `1px solid rgb(13,13,18)` (neutral outlined)

### Imagery
- User photos: circular avatars (24px–48px), or initials placeholder
- No decorative illustrations
- UNDIP logo used in branding contexts

### Corner Radii
- Page panels: 14px
- Cards/widgets: 14px
- Form inputs (large): 12px
- Form inputs (small): 10px
- Buttons: 8px
- Badges: 16px (pill) or 6px (chip)
- Icons buttons: 8px
- Sidebar logo: 8px

---

## ICONOGRAPHY

- **Icon set**: Custom SVG icons provided in `uploads/`. Two variants per icon: standard stroke (e.g. `home-01.svg`) and filled/colored (e.g. `home-01-1.svg`).
- **Style**: Outline/stroke icons at 24×24px. Clean, rounded stroke ends. Stroke weight ~1.5px.
- **Coverage**: ~150+ icons covering: navigation (home, chevrons, arrows), actions (plus, minus, check, block, eye, filter, download, upload), alerts (check-circle, alert-circle, alert-triangle), communication (bell, mail, message), finance (bank, credit-card, coins, wallet, receipt, invoice), data (charts: bar, line, pie), files (file, folder), users (user, users, avatar), security (lock, shield, key), settings (gear), and more.
- **Usage**: Inline SVG via `<img>` tags or direct SVG embed. Icon size typically 16px, 20px, or 24px.
- **Sidebar icons**: Each nav item has a paired SVG icon (stroke for inactive, filled/colored for active).
- **Emoji**: Not used anywhere in the product.
- **Icon font**: Not used. All icons are SVG files.
- **Assets location**: `assets/icons/` — stroke variants

---

## File Index

```
README.md                    — This file
SKILL.md                     — Agent skill definition
colors_and_type.css          — CSS custom properties for all design tokens
fonts/
  InterTight-VariableFont_wght.ttf        — Inter Tight variable font (upright, wght 100–900)
  InterTight-Italic-VariableFont_wght.ttf — Inter Tight variable font (italic, wght 100–900)
  (+ static weight TTF files)
assets/
  icons/                     — 50+ stroke SVG icons (24×24, 1.5–1.8px stroke)
  UNDIPOfficial.png          — UNDIP university official logo
preview/
  colors-primary.html        — Primary purple scale (100–600)
  colors-greyscale.html      — Neutral grey scale (0–900)
  colors-semantic.html       — Success, Warning, Error, Sky palettes
  type-scale.html            — Typography specimens H1–H6, body, label
  type-fonts.html            — Font family weight strips + in-use examples
  spacing-tokens.html        — Spacing scale + border radius stops
  shadow-tokens.html         — Shadow scale xs → xl
  components-buttons.html    — Button variants: primary, secondary, tertiary, destructive
  components-forms.html      — Text inputs, select, toggle, checkbox, radio
  components-badges.html     — Badge: fill, subtle, outlined, dot — all semantic colors
  components-sidebar.html    — SITKOM sidebar with modules, active states, user footer
  brand-logo.html            — SITKOM logomark, lockup (light/dark) + UNDIP logo
  brand-icons.html           — Full icon set preview (40+ SVGs)
ui_kits/
  dashboard/
    index.html               — Full interactive dashboard prototype
                               • Login screen → Dashboard → Bank Soal
                               • Sidebar navigation, stat cards, bar chart,
                                 data table, activity feed, module cards
```
