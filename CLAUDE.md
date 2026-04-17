## Design Context: Addbrain Dashboard UI Kit (shadcn/ui Based)

Berdasarkan analisis visual menyeluruh dari pedoman antarmuka **Addbrain - Dashboard UI Kit** yang dibangun di atas fondasi **shadcn/ui**, berikut adalah pedoman desain mutlak (*design guidelines*) untuk proyek ini:

### 1. Arsitektur Komponen (shadcn/ui)
- Desain ini berakar kuat pada ekosistem **shadcn**. Oleh karenanya, komponen harus dibangun menggunakan konvensi Tailwind CSS yang ekstensif, pemisahan *structural classes* dan *aesthetic classes*, serta dukungan penuh terhadap *dark mode* bawaan dan aksesibilitas (Radix UI).

### 2. Tipografi Resmi (Official Typography)
- **Keluarga Font Utama:** **`Inter Tight`** (Tersedia via Google Fonts). Karakteristiknya lebih padat dan tegas dibanding font *Inter* reguler.
- **Ketebalan (Weights):** Hanya menggunakan 3 macam ketebalan: *Regular* (400), *Medium* (500), dan *Semibold* (600).
- **Skala Heading (Semua menggunakan Semibold):**
  - H1: 48px
  - H2: 40px
  - H3: 32px
  - H4: 24px
  - H5: 20px
  - H6: 18px
- **Skala Body (Tersedia dalam Semibold, Medium, Regular):** 
  - Body Large: 18px
  - Body Medium: 16px (Default text size)
  - Body Small: 14px
  - Body XSmall: 12px (Banyak digunakan untuk *Hint/Error text* di bawah form).

### 3. Palet Warna (Colors) 
Palet semantik ini sangat cocok direpresentasikan via *CSS Variables* bawaan shadcn (misal: `hsl(var(--primary))`):
- **Primary:** Ungu/Indigo cerah yang ikonik. Sebagai basis warna aktif (*toggle*, fokus form, tombol utama).
- **Greyscale:** *Cool slate* (seperti palet `slate` di Tailwind). 
- **Semantics:** Biru (Sky/Info), Hijau zamrud (Success), Kuning/Oranye (Warning), dan Merah Crimson (Error).

### 4. Sudut & Bayangan (Border Radius & Shadows)
- **Border Radius:** 
  - Sangat bulat (`rounded-full`) untuk Avatar dan label pil.
  - Sudut menenangkan (`rounded-lg`, `rounded-xl`, atau `rounded-2xl`) untuk Kartu, Modal, dan Form.
- **Sistem Bayangan (Shadows):** 
  - Dibagi dalam 6 tingkat: *XSmall, Small, Medium, Large, XLarge, XXLarge*.
  - Karakter bayangannya **sangat luas dan super-lembut (soft blur)**, bukan bayangan tebal/kasar. Berfungsi untuk "mengangkat" kartu putih sedikit demi sedikit di atas kanvas abu-abu (`bg-slate-50`).

### 5. Komponen Form & UI Khusus
- **Form (Stateful):** Mendukung gaya form interaktif dengan *hints*. *Active focus* menggunakan border utama ungu, *Error* menggunakan border merah dengan petunjuk `Body XSmall`.
- **Ikonografi:** *Light strokes* (garis murni tipis tanpa *fill* solid). Sangat serasi dengan shadcn/Lucide icons.
- **Resposivitas & Native Feel:** Mendukung elemen antarmuka yang sangat dekat dengan perangkat bergerak sejati (iOS-like), seperti elemen *Segmented Control*, susunan navigasi, dan interaksi form. Area klik terasa besar dan ramah-jari.

### 6. Estetika dan Vibe Keseluruhan (Brand Personality)
- **"The Ultimate Clean SaaS":** Ruang kosong (*white-space*) sangat digenjot pemakaiannya. Lebar tabel tidak dikekang garis tegas vertikal. Antarmuka terasa premium, efisien, tenang, sekaligus amat fungsional.
