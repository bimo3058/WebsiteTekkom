<div align="center">

# 🎓 Web Akademik Terintegrasi Teknik Komputer

### *Laravel Modular Monolith Architecture*

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Supabase](https://img.shields.io/badge/Supabase-3FCF8E?style=for-the-badge&logo=supabase&logoColor=white)](https://supabase.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

*Sistem terintegrasi berbasis Laravel Modular Monolith yang terdiri dari empat aplikasi akademik dalam satu proyek terpusat.*

[📖 Documentation](#-documentation) • [🚀 Quick Start](#-quick-start) • [🏗️ Architecture](#-system-architecture) • [⚡ Performance](#-performance-configuration) • [🤝 Contributing](#-development-rules)

</div>

---

## 📋 Table of Contents

- [Contributor](#-contributor)
- [Overview](#-overview)
- [System Modules](#-system-modules)
- [System Architecture](#-system-architecture)
- [Project Structure](#-project-structure)
- [Quick Start](#-quick-start)
- [Database Convention](#-database-convention)
- [Migration Guide](#-migration-guide)
- [Database Seeding](#-database-seeding)
- [Essential Commands](#-essential-commands)
- [User Role System](#-user-role-system)
- [Development Rules](#-development-rules)
- [Performance Configuration](#-performance-configuration)
- [Redis Setup](#-redis-setup)
- [Laravel Telescope (Opsional)](#-laravel-telescope-opsional)
- [Troubleshooting](#-troubleshooting)
- [Future Roadmap](#-future-roadmap)

---

## 🤝 Contributor

### Development Team

- **Project Lead:** Bimo Kusumo Putro Wicaksono
- **Bank Soal:** Dzaki Eka Atmaja, Evan Adkara Christian P, Nabil Bintang Ardiansyah P.  
- **Capstone + TA:** Ananda Prida Yusuf S, Fayyadh Muhammad Habibie, Muhammad Riza Saputra
- **E-Office:** Andhinee Clarisaa Tanasale, Cetta Masinda Amany, Elvina Nasywa Ariyani
- **Manajemen Kemahasiswaan + KP:** Devarlo Rahadyan Razan, Muhammad Reswara Suryawan, Surya Hari Putra, Syahbana Hatab

---

## 🌟 Overview

**Web Akademik Terintegrasi Teknik Komputer** adalah platform akademik terpusat yang dibangun dengan **Laravel Modular Architecture** dan **Supabase** sebagai database backend, menggabungkan empat sistem utama dalam satu codebase untuk efisiensi dan konsistensi data.

### ✨ Key Features

- 🎯 **Modular Architecture** - Setiap modul independen namun terintegrasi
- 🟢 **Supabase Backend** - PostgreSQL hosting dengan realtime features
- 🗄️ **Single Database** - Satu database PostgreSQL terpusat
- 🔐 **Role-Based Access Control** - 4 level user roles
- ⚡ **Optimized Performance** - Redis caching + persistent DB connection
- 🔄 **Scalable Design** - Mudah dikembangkan ke microservices

---

## 📦 System Modules

<table>
<tr>
<td align="center" width="25%">

### 📘 Capstone + TA

Manajemen topik, bimbingan, workflow, dan evaluasi tugas akhir

</td>
<td align="center" width="25%">

### 📗 Bank Soal

Sistem manajemen bank soal dan ujian online

</td>
<td align="center" width="25%">

### 📙 Kemahasiswaan

Kegiatan, organisasi, dan administrasi mahasiswa

</td>
<td align="center" width="25%">

### 📕 E-Office

Surat menyurat dan manajemen dokumen internal

</td>
</tr>
</table>

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────┐
│         Client (Browser/Mobile)             │
└──────────────────┬──────────────────────────┘
                   │
┌──────────────────▼──────────────────────────┐
│      Laravel Application (Monolith)         │
│  ┌───────────────────────────────────────┐  │
│  │         Core (Global Layer)           │  │
│  │  • users  • students  • lecturers     │  │
│  └───────────────────────────────────────┘  │
│                                              │
│  ┌───────────────────────────────────────┐  │
│  │          Module Layer                 │  │
│  │  ┌──────────┐  ┌──────────┐          │  │
│  │  │ Capstone │  │ BankSoal │          │  │
│  │  └──────────┘  └──────────┘          │  │
│  │  ┌──────────┐  ┌──────────┐          │  │
│  │  │Kemahasis-│  │ EOffice  │          │  │
│  │  │  waan    │  │          │          │  │
│  │  └──────────┘  └──────────┘          │  │
│  └───────────────────────────────────────┘  │
└──────────────────┬──────────────────────────┘
                   │
       ┌───────────┴───────────┐
       ▼                       ▼
┌─────────────┐     ┌──────────────────────┐
│ 🔴 Redis    │     │ 🟢 Supabase          │
│ (Cache +    │     │ (PostgreSQL Database) │
│  Session)   │     │                      │
└─────────────┘     └──────────────────────┘
```

---

## 📂 Project Structure

```
WebsiteTekkom/
├── app/
│   ├── Http/
│   │   └── Middleware/
│   │       └── CheckRole.php     # Role-based access dengan Redis cache
│   ├── Models/
│   │   └── User.php              # getCachedRoles(), cacheUserData()
│   └── Providers/
│       └── AppServiceProvider.php # Cached Eloquent User Provider
├── config/
│   ├── auth.php                  # cached-eloquent driver
│   ├── database.php              # PDO persistent connection
│   └── cache.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php    # Global seeder entry point
├── Modules/
│   ├── Capstone/
│   │   └── Database/
│   │       └── Seeders/
│   ├── BankSoal/
│   │   └── Database/
│   │       └── Seeders/
│   ├── Kemahasiswaan/
│   │   └── Database/
│   │       └── Seeders/
│   └── EOffice/
│       └── Database/
│           └── Seeders/
├── routes/
├── .env.example
├── composer.json
└── README.md
```

---

## 🚀 Quick Start

### Prerequisites

- Laravel >= 12
- PHP >= 8.2.12
- Composer >= 2.9.5
- Supabase Account (or PostgreSQL >= 14)
- Node.js >= 18 (optional, for frontend assets)
- Redis (Memurai untuk Windows, atau Redis untuk Linux/Mac)

### 1️⃣ Clone Repository

```bash
git clone https://github.com/bimo3058/WebsiteTekkom.git
cd WebsiteTekkom
```

### 2️⃣ Install Dependencies

```bash
composer install
composer require predis/predis
```

### 3️⃣ Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ Configure Database & Redis

Edit `.env`:

```env
# Database - Supabase Singapore (ap-southeast-1)
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.your-project-ref
DB_PASSWORD=your-supabase-password

# Redis Cache
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 5️⃣ Run Migrations

```bash
php artisan migrate
```

### 6️⃣ Add Database Indexes

Jalankan di **Supabase SQL Editor** untuk optimasi query:

```sql
-- Index untuk login query
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_users_email_active
    ON users(email) WHERE deleted_at IS NULL;

CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_users_id_active
    ON users(id) WHERE deleted_at IS NULL;

-- Index untuk roles lookup
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_user_roles_user_id
    ON user_roles(user_id);

CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_roles_name
    ON roles(name);

-- Index untuk capstone
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_capstone_groups_period_status
    ON capstone_groups(period_id, status) WHERE deleted_at IS NULL;

CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_capstone_titles_status
    ON capstone_titles(status, approved_by_admin) WHERE deleted_at IS NULL;

-- Index untuk bank soal
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_pertanyaan_mk_status
    ON bs_pertanyaan(mk_id, status);
```

### 7️⃣ Update Auth Config

Edit `config/auth.php`:

```php
'providers' => [
    'users' => [
        'driver' => 'cached-eloquent', // pakai cached provider
        'model'  => App\Models\User::class,
    ],
],
```

### 8️⃣ Start Development Server

```bash
# Start Redis dulu (pastikan Memurai jalan di Windows)
# Lalu jalankan Laravel
composer run dev
```

Visit: **http://localhost:8000**

---

## ⚡ Performance Configuration

Sistem ini telah dioptimasi dari response time **~3200ms → <1000ms** melalui beberapa teknik berikut.

### Hasil Optimasi

| Request | Sebelum | Sesudah | Improvement |
|---|---|---|---|
| POST /login | ~3200ms | ~400ms | **87% faster** |
| GET /dashboard | ~2580ms | <1000ms | **~60% faster** |
| GET /superadmin/dashboard | ~2580ms | <1000ms | **~60% faster** |

---

### 1. Persistent Database Connection

**File:** `config/database.php`

Tambahkan `PDO::ATTR_PERSISTENT` di konfigurasi pgsql agar koneksi ke Supabase di-reuse antar request, menghilangkan overhead TCP handshake (~500ms) di setiap request.

```php
'pgsql' => [
    'driver'         => 'pgsql',
    'url'            => env('DB_URL'),
    'host'           => env('DB_HOST', '127.0.0.1'),
    'port'           => env('DB_PORT', '5432'),
    'database'       => env('DB_DATABASE', 'laravel'),
    'username'       => env('DB_USERNAME', 'root'),
    'password'       => env('DB_PASSWORD', ''),
    'charset'        => env('DB_CHARSET', 'utf8'),
    'prefix'         => '',
    'prefix_indexes' => true,
    'search_path'    => 'public',
    'sslmode'        => 'require',
    'options'        => [
        PDO::ATTR_PERSISTENT => true,  // reuse koneksi antar request
        PDO::ATTR_TIMEOUT    => 10,
    ],
],
```

---

### 2. Cached Eloquent User Provider

**File:** `app/Providers/AppServiceProvider.php`

Laravel memanggil `retrieveById()` di **setiap request** untuk re-authenticate user dari session. Override ini mengambil user dari Redis (~1ms) bukan DB (~1150ms).

```php
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

public function register(): void
{
    Auth::resolved(function ($auth) {
        $auth->provider('cached-eloquent', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                public function retrieveById($identifier): ?Authenticatable
                {
                    $cacheKey = "user:{$identifier}:data";
                    $cached   = Cache::get($cacheKey);

                    if ($cached) {
                        $model = $this->createModel();
                        return $model->newFromBuilder($cached);
                    }

                    $user = parent::retrieveById($identifier);
                    if ($user) {
                        Cache::put($cacheKey, $user->withoutRelations()->toArray(), now()->addHours(8));
                    }

                    return $user;
                }
            };
        });
    });

    // ... singleton registrations
}
```

Daftarkan di `config/auth.php`:

```php
'providers' => [
    'users' => [
        'driver' => 'cached-eloquent',
        'model'  => App\Models\User::class,
    ],
],
```

---

### 3. Cached Roles di User Model

**File:** `app/Models/User.php`

`hasRole()`, `hasAnyRole()`, `hasAllRoles()` mengambil roles dari Redis cache bukan query DB setiap kali dipanggil.

```php
use Illuminate\Support\Facades\Cache;

// Relasi dengan select kolom spesifik
public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles')
                ->select('roles.id', 'roles.name', 'roles.module');
}

// Semua role helper pakai getCachedRoles()
public function hasRole(string $roleName, ?string $module = null): bool
{
    return $this->getCachedRoles()
        ->when($module, fn($c) => $c->where('module', $module))
        ->contains('name', strtolower($roleName));
}

protected function getCachedRoles(): \Illuminate\Support\Collection
{
    if ($this->relationLoaded('roles')) {
        return collect($this->roles);
    }

    $cached = Cache::get("user:{$this->id}:roles");
    if ($cached) {
        return collect($cached);
    }

    $roles = $this->roles()->get();
    Cache::put("user:{$this->id}:roles", $roles->toArray(), now()->addHours(8));

    return $roles;
}

// Cache semua data user setelah login
public function cacheUserData(): void
{
    Cache::put(
        "user:{$this->id}:data",
        $this->makeVisible(['remember_token'])->withoutRelations()->toArray(),
        now()->addHours(8)
    );
}

// Hapus cache saat logout atau data user berubah
public function clearUserCache(): void
{
    Cache::forget("user:{$this->id}:data");
    Cache::forget("user:{$this->id}:roles");
}
```

---

### 4. Cache Saat Login & Clear Saat Logout

**File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

Simpan user data + roles ke Redis segera setelah login berhasil, sehingga request berikutnya (dashboard) tidak perlu query DB sama sekali.

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user      = auth()->user();
    $userRoles = $user->roles()->get();

    // Cache sekaligus setelah login
    $user->cacheUserData();
    Cache::put("user:{$user->id}:roles", $userRoles->toArray(), now()->addHours(8));

    $roleNames = $userRoles->pluck('name');

    if ($roleNames->intersect(['superadmin', 'admin'])->isNotEmpty()) {
        return redirect()->intended(route('superadmin.dashboard'));
    }

    if ($roleNames->contains('dosen')) {
        return redirect()->intended(route('dashboard'));
    }

    return redirect()->intended(route('dashboard'));
}

public function destroy(Request $request): RedirectResponse
{
    $user = auth()->user();
    Auth::guard('web')->logout();

    if ($user) {
        $user->clearUserCache(); // hapus cache saat logout
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

---

### 5. Cached Role Middleware

**File:** `app/Http/Middleware/CheckRole.php`

Middleware ini jalan di setiap request. Tanpa cache, tiap halaman akan query roles ke DB. Dengan cache, cukup 1ms dari Redis.

```php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (!auth()->check()) {
        return redirect('/login');
    }

    $userId = auth()->id(); // tidak trigger DB query
    $cached = Cache::get("user:{$userId}:roles");

    if ($cached) {
        $userRoles = collect($cached)->pluck('name');
    } else {
        $rolesCollection = auth()->user()->roles()->get();
        Cache::put("user:{$userId}:roles", $rolesCollection->toArray(), now()->addHours(8));
        $userRoles = $rolesCollection->pluck('name');
    }

    $roles   = collect(explode('|', $role))->map(fn($r) => strtolower($r));
    $hasRole = $roles->some(fn($r) => $userRoles->contains($r));

    if (!$hasRole) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}
```

> ⚠️ **Penting:** Setiap kali update roles user, wajib panggil `$user->clearUserCache()` supaya perubahan langsung efektif.

---

### 6. Cache Data per Modul

Untuk data yang jarang berubah tapi sering dibaca, gunakan pola `Cache::remember()`:

```php
// Contoh di service class
public function getActivePeriod(): ?CapstonePeriod
{
    return Cache::remember('capstone:period:active', now()->addHour(), fn() =>
        CapstonePeriod::where('is_active', true)->first()
    );
}

public function getMataKuliahList(): Collection
{
    return Cache::remember('banksoal:mk:all', now()->addDay(), fn() =>
        MataKuliah::with('cpls')->orderBy('kode')->get()
    );
}
```

**Cache key convention:**
```
{modul}:{entity}:{scope}:{id}

user:1:data          → data user
user:1:roles         → roles user
capstone:period:active
banksoal:mk:all
banksoal:statistik:mk:42
```

---

## 🔴 Redis Setup

Redis digunakan sebagai cache dan session driver untuk menghindari query DB berulang di setiap request.

### Windows (Memurai)

**1. Install Memurai**

Download di [memurai.com/get-memurai](https://www.memurai.com/get-memurai) → install. Memurai otomatis berjalan sebagai Windows Service.

Verifikasi:
```bash
memurai-cli ping
# PONG
```

**2. Install Predis**

```bash
composer require predis/predis
```

**3. Hapus php_redis.dll dari php.ini**

Buka `C:\xampp\php\php.ini`, comment out jika ada:
```ini
;extension=php_redis.dll
```

**4. Update `.env`**

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**5. Clear config**

```bash
php artisan config:clear
php artisan cache:clear
```

**6. Verifikasi**

```bash
php artisan tinker
Cache::put('test', 'redis working!', 60);
Cache::get('test'); // → "redis working!"
```

---

### Linux / Mac

```bash
# Ubuntu/Debian
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Mac (Homebrew)
brew install redis
brew services start redis

# Verifikasi
redis-cli ping  # PONG
```

Lanjutkan dari langkah **2. Install Predis** di atas.

---

### Laragon (Alternatif XAMPP)

Laragon sudah include Redis bawaan — lebih simpel dari setup manual:

1. Download di [laragon.org](https://laragon.org/download)
2. Install → klik kanan tray icon → centang **Redis**
3. Pindahkan project ke `C:\laragon\www\webtekkom`
4. Update `.env` seperti di atas

---

### Catatan Penting Redis

> ⚠️ **Redis harus jalan sebelum Laravel dijalankan.** Karena session disimpan di Redis, kalau Redis mati semua user tidak bisa login.

> ⚠️ **Jalankan `php artisan cache:clear` setelah mengubah data cache** untuk menghindari stale data.

---

## 🗄️ Database Convention

### Core Tables (Global)

Tabel global **tanpa prefix**:

| Table | Description |
|-------|-------------|
| `users` | User authentication |
| `students` | Student data |
| `lecturers` | Lecturer data |

### Module Tables (Dengan Prefix)

| Module | Prefix | Example Tables |
|--------|--------|----------------|
| 📘 Capstone | `capstone_` | `capstone_periods`, `capstone_topics` |
| 📗 Bank Soal | `bs_` | `bs_pertanyaan`, `bs_mata_kuliah` |
| 📙 Kemahasiswaan | `mk_` | `mk_kegiatan`, `mk_pengumuman` |
| 📕 E-Office | `eo_` | `eo_surat`, `eo_dokumen` |

> ⚠️ **IMPORTANT:** Semua tabel module **WAJIB** menggunakan prefix yang sesuai.

---

## 🔄 Migration Guide

### Run All Migrations

```bash
php artisan migrate
```

### Run Specific Module Migration

```bash
php artisan migrate --path=Modules/Capstone/Database/Migrations
php artisan migrate --path=Modules/BankSoal/Database/Migrations
php artisan migrate --path=Modules/Kemahasiswaan/Database/Migrations
php artisan migrate --path=Modules/EOffice/Database/Migrations
```

### Reset Database (⚠️ Danger Zone)

```bash
php artisan migrate:fresh
```

### Create New Migration

```bash
# Global migration
php artisan make:migration create_users_table

# Module-specific migration
php artisan make:migration create_capstone_periods_table --path=Modules/Capstone/Database/Migrations
```

---

## 🌱 Database Seeding

Seeding digunakan untuk mengisi database dengan data awal (roles, user dummy, data referensi, dll). Proyek ini menggunakan dua lapisan seeder: **global** di `database/seeders/` dan **per-modul** di `Modules/{Nama}/Database/Seeders/`.

---

### Struktur Seeder

```
database/
└── seeders/
    └── DatabaseSeeder.php          ← Entry point utama (global)

Modules/
├── Capstone/
│   └── Database/
│       └── Seeders/
│           ├── CapstoneSeeder.php  ← Seeder utama modul Capstone
│           └── ...
├── BankSoal/
│   └── Database/
│       └── Seeders/
│           ├── BankSoalSeeder.php
│           └── ...
├── Kemahasiswaan/
│   └── Database/
│       └── Seeders/
│           ├── KemahasiswaanSeeder.php
│           └── ...
└── EOffice/
    └── Database/
        └── Seeders/
            ├── EOfficeSeeder.php
            └── ...
```

---

### Global Seeder

**`database/seeders/DatabaseSeeder.php`** adalah entry point utama. Seeder ini berisi data inti yang dibutuhkan semua modul: roles, superadmin, dan data referensi global seperti data mahasiswa dan dosen.

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,       // seed tabel roles
            UserSeeder::class,       // seed superadmin & user dummy
            StudentSeeder::class,    // seed data mahasiswa
            LecturerSeeder::class,   // seed data dosen
        ]);
    }
}
```

**Contoh `RoleSeeder.php`:**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'superadmin', 'module' => null],
            ['name' => 'admin',      'module' => null],
            ['name' => 'dosen',      'module' => null],
            ['name' => 'mahasiswa',  'module' => null],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
```

**Contoh `UserSeeder.php`** (superadmin default):

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@tekkom.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign role superadmin
        $superadminRole = \App\Models\Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            $superadmin->roles()->syncWithoutDetaching([$superadminRole->id]);
        }
    }
}
```

---

### Module Seeder

Setiap modul punya seeder sendiri untuk data spesifik modulnya. Seeder modul **tidak dipanggil otomatis** dari `DatabaseSeeder` — harus didaftarkan secara eksplisit atau dijalankan manual.

**Contoh `CapstoneSeeder.php`:**

```php
<?php

namespace Modules\Capstone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CapstoneSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CapstonePeriodSeeder::class,   // seed periode capstone
            CapstoneTopicSeeder::class,    // seed topik/judul dummy
        ]);
    }
}
```

**Contoh `BankSoalSeeder.php`:**

```php
<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;

class BankSoalSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MataKuliahSeeder::class,   // seed daftar mata kuliah
            BankSoalDummySeeder::class, // seed soal dummy
        ]);
    }
}
```

---

### Cara Menjalankan Seeder

#### ▶ Jalankan semua seeder global

```bash
php artisan db:seed
```

> Menjalankan `DatabaseSeeder` beserta semua seeder yang didaftarkan di dalamnya.

---

#### ▶ Jalankan seeder global tertentu saja

```bash
# Hanya seed roles
php artisan db:seed --class=RoleSeeder

# Hanya seed user
php artisan db:seed --class=UserSeeder
```

---

#### ▶ Jalankan seeder modul tertentu

Karena seeder modul berada di namespace berbeda, gunakan flag `--class` dengan **fully qualified class name**:

```bash
# Capstone
php artisan db:seed --class="Modules\Capstone\Database\Seeders\CapstoneSeeder"

# Bank Soal
php artisan db:seed --class="Modules\BankSoal\Database\Seeders\BankSoalSeeder"

# Kemahasiswaan
php artisan db:seed --class="Modules\Kemahasiswaan\Database\Seeders\KemahasiswaanSeeder"

# E-Office
php artisan db:seed --class="Modules\EOffice\Database\Seeders\EOfficeSeeder"
```

---

#### ▶ Migrate fresh + seed sekaligus (⚠️ data lama terhapus)

```bash
# Hanya global seeder
php artisan migrate:fresh --seed

# Migrate fresh + seed semua modul sekaligus
php artisan migrate:fresh --seed && \
php artisan db:seed --class="Modules\Capstone\Database\Seeders\CapstoneSeeder" && \
php artisan db:seed --class="Modules\BankSoal\Database\Seeders\BankSoalSeeder" && \
php artisan db:seed --class="Modules\Kemahasiswaan\Database\Seeders\KemahasiswaanSeeder" && \
php artisan db:seed --class="Modules\EOffice\Database\Seeders\EOfficeSeeder"
```

---

#### ▶ Daftarkan modul seeder ke DatabaseSeeder (opsional)

Jika ingin semua modul ikut ter-seed saat `php artisan db:seed` atau `migrate:fresh --seed`, tambahkan ke `DatabaseSeeder`:

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        // Global
        RoleSeeder::class,
        UserSeeder::class,
        StudentSeeder::class,
        LecturerSeeder::class,

        // Modules — uncomment sesuai kebutuhan
        \Modules\Capstone\Database\Seeders\CapstoneSeeder::class,
        \Modules\BankSoal\Database\Seeders\BankSoalSeeder::class,
        \Modules\Kemahasiswaan\Database\Seeders\KemahasiswaanSeeder::class,
        \Modules\EOffice\Database\Seeders\EOfficeSeeder::class,
    ]);
}
```

> 💡 **Tips:** Di environment development, daftarkan semua modul seeder agar mudah reset & rebuild data. Di production, jalankan modul seeder secara manual sesuai kebutuhan untuk menghindari data dummy masuk ke production.

---

### Membuat Seeder Baru

```bash
# Global seeder
php artisan make:seeder NamaSeeder

# Module seeder (buat manual di folder yang sesuai)
# Contoh: Modules/Capstone/Database/Seeders/CapstonePeriodSeeder.php
```

Untuk module seeder yang dibuat manual, gunakan namespace yang sesuai:

```php
<?php

namespace Modules\Capstone\Database\Seeders;

use Illuminate\Database\Seeder;

class CapstonePeriodSeeder extends Seeder
{
    public function run(): void
    {
        // ...
    }
}
```

---

### Tips Seeding

> ⚠️ **Urutan penting!** Seed global (`roles`, `users`) **selalu duluan** sebelum modul, karena modul seeder biasanya butuh foreign key ke tabel global.

> ✅ Gunakan `updateOrInsert()` atau `firstOrCreate()` agar seeder **aman dijalankan berulang** tanpa duplikasi data.

> 🔄 Setelah seeding, clear cache Redis agar data lama tidak tersisa:
> ```bash
> php artisan cache:clear
> ```

---

## 🛠️ Essential Commands

### Cache Management

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Clear semua sekaligus
php artisan optimize:clear
```

### Production Optimization

```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload -o
```

### Development Tools

```bash
php artisan route:list
php artisan route:list | grep capstone
php artisan db:show
php artisan storage:link
```

---

## 👤 User Role System

### Available Roles

| Role | Code | Description |
|------|------|-------------|
| 🔴 **Superadmin** | `superadmin` | Full system access |
| 🟠 **Admin** | `admin` | Administrative access |
| 🟡 **Dosen** | `dosen` | Lecturer/faculty access |
| 🟢 **Mahasiswa** | `mahasiswa` | Student access (default) |

### Usage di Controller / Middleware

```php
// Cek single role
$user->hasRole('dosen');

// Cek salah satu dari beberapa role
$user->hasAnyRole(['superadmin', 'admin']);

// Cek dengan filter module
$user->hasRole('dosen', 'capstone');

// Di route middleware
Route::middleware(['auth', 'role:superadmin|admin'])->group(function () {
    // ...
});
```

### Invalidasi Cache Saat Update Role

```php
// Wajib dipanggil setiap kali roles user diubah
public function updateUserRoles(User $user, array $roleIds): void
{
    $user->roles()->sync($roleIds);
    $user->clearUserCache(); // hapus cache lama agar langsung efektif
}
```

---

## 🔐 Development Rules

### ⚠️ WAJIB DIBACA TIM

| Rule | Description |
|------|-------------|
| 🚫 **No Migration Edit** | Jangan edit migration yang sudah dijalankan di production |
| 📛 **Use Prefix** | Gunakan prefix sesuai module untuk semua tabel |
| 🔄 **Clear Cache** | Selalu clear cache setelah ubah route/config |
| 🔒 **No .env Commit** | Jangan commit file `.env` ke repository |
| ⚡ **Eager Loading** | Gunakan `with()` untuk menghindari N+1 query problem |
| 🗑️ **Clear User Cache** | Panggil `clearUserCache()` setiap kali update data/roles user |
| 📝 **Code Documentation** | Tambahkan docblock untuk function public |
| 🧪 **Test Before Commit** | Test fitur sebelum commit ke branch utama |
| 🔴 **Redis First** | Pastikan Redis/Memurai jalan sebelum start Laravel |
| 🌱 **Safe Seeder** | Gunakan `updateOrInsert` / `firstOrCreate` agar seeder idempotent |

### Git Workflow

```bash
git checkout -b feature/module-name-feature
git add .
git commit -m "feat(module): description"
git push origin feature/module-name-feature
```

### Commit Message Convention

```
feat(capstone): add topic submission feature
fix(bank-soal): resolve question duplication bug
docs(readme): update installation guide
refactor(kemahasiswaan): optimize event query
perf(auth): add redis caching for user roles
seed(capstone): add period and topic dummy data
```

---

## 🔭 Laravel Telescope (Opsional)

Laravel Telescope adalah **debug assistant** bawaan Laravel yang memungkinkan kamu memantau setiap request yang masuk: berapa lama prosesnya, query apa saja yang dijalankan, apakah ada query lambat, exception apa yang terjadi, dan masih banyak lagi — semuanya lewat UI web yang rapi.

> ⚠️ **Telescope hanya untuk environment `local` / `development`.** Jangan aktifkan di production karena menyimpan seluruh data request ke database dan berpotensi membocorkan informasi sensitif.

---

### Instalasi

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Perintah `telescope:install` akan:
- Mempublish config ke `config/telescope.php`
- Mempublish assets (CSS/JS) ke `public/vendor/telescope`
- Mendaftarkan `TelescopeServiceProvider` ke `bootstrap/providers.php`

---

### Konfigurasi Agar Hanya Aktif di Local

Pastikan Telescope **tidak pernah load di production**. Buka `app/Providers/TelescopeServiceProvider.php` dan verifikasi method `register()`:

```php
// app/Providers/TelescopeServiceProvider.php

public function register(): void
{
    // Telescope hanya aktif di environment local
    if ($this->app->isLocal()) {
        $this->app->register(\Laravel\Telescope\TelescopeApplicationServiceProvider::class);
    }
}
```

Atau bisa juga via `config/telescope.php`:

```php
// config/telescope.php
'enabled' => env('TELESCOPE_ENABLED', false),
```

Dan di `.env` development:

```env
TELESCOPE_ENABLED=true
```

> ✅ Dengan cara ini, Telescope tidak akan aktif kecuali kamu eksplisit menyalakannya di `.env` lokal.

---

### Akses Dashboard

Setelah server berjalan, buka:

```
http://localhost:8000/telescope
```

---

### Memantau Request & Response Time

Ini adalah fitur utama yang paling berguna untuk profiling performa.

#### Tab **Requests**

Buka **Telescope → Requests**. Kamu akan melihat tabel seperti ini:

| Method | Path | Status | Duration | Time |
|--------|------|--------|----------|------|
| POST | /login | 302 | **387ms** | 14:32:01 |
| GET | /dashboard | 200 | **210ms** | 14:32:02 |
| GET | /superadmin/dashboard | 200 | **950ms** | 14:32:05 |

Kolom **Duration** menunjukkan total waktu dari request masuk hingga response keluar.

#### Detail Request

Klik salah satu request untuk melihat breakdown lengkapnya:

```
Request Detail
├── 📋 Request Info
│   ├── Method, URL, Status Code
│   ├── Controller & Action yang dipanggil
│   └── Middleware yang dijalankan
│
├── 🗄️ Queries (paling penting untuk profiling!)
│   ├── Jumlah query yang dieksekusi
│   ├── Durasi tiap query (ms)
│   └── Raw SQL dengan binding-nya
│
├── ⚡ Cache
│   ├── Cache hit / miss
│   └── Key yang diakses
│
├── 📦 Session
│   └── Data session yang aktif
│
└── 🔢 Response
    └── Status & response body (jika JSON)
```

---

### Mengidentifikasi Request Lambat

Telescope bisa **highlight otomatis** request yang melebihi threshold tertentu. Konfigurasi di `config/telescope.php`:

```php
// config/telescope.php

'slow_queries' => [
    'enabled'   => true,
    'threshold' => 100, // query > 100ms dianggap lambat (dalam ms)
],

'slow_requests' => [
    'enabled'   => true,
    'threshold' => 1000, // request > 1000ms dianggap lambat (dalam ms)
],
```

Request dan query yang melewati threshold akan **ditandai merah** di dashboard Telescope.

---

### Tips Membaca Hasil Telescope

**Cek N+1 Query Problem:**

Kalau kamu buka satu halaman dan di tab Queries muncul puluhan query dengan pola yang mirip-mirip, itu tanda N+1. Solusinya pakai eager loading `with()`:

```php
// ❌ Tanpa eager loading → muncul N+1 di Telescope
$groups = CapstoneGroup::all();
foreach ($groups as $group) {
    echo $group->students->count(); // query baru tiap iterasi
}

// ✅ Dengan eager loading → hanya 2 query di Telescope
$groups = CapstoneGroup::with('students')->get();
```

**Cek Cache Hit/Miss:**

Di tab **Cache**, pastikan key seperti `user:{id}:roles` dan `user:{id}:data` berstatus **hit** (bukan miss) setelah login pertama. Kalau terus miss, berarti caching belum bekerja.

**Bandingkan Before/After Optimasi:**

Gunakan Telescope sebelum dan sesudah menerapkan perubahan (tambah index, eager loading, caching) untuk melihat penurunan duration secara konkret.

---

### Membersihkan Data Telescope

Data Telescope tersimpan di tabel `telescope_entries` dan `telescope_entries_tags`. Bersihkan secara berkala agar tidak memberatkan database lokal:

```bash
# Hapus semua data Telescope
php artisan telescope:clear

# Atau jalankan pruning otomatis (hapus data > 24 jam)
php artisan telescope:prune

# Jalankan pruning dengan custom hours
php artisan telescope:prune --hours=48
```

Bisa juga dijadwalkan di `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->daily();
```

---

### Uninstall Telescope (Jika Tidak Diperlukan)

Jika teammate tidak ingin install Telescope (misal di mesin yang resource-nya terbatas):

```bash
composer remove laravel/telescope
php artisan migrate:rollback  # rollback migration telescope
```

Hapus juga `TelescopeServiceProvider` dari `bootstrap/providers.php` jika masih terdaftar.

> 💡 Karena diinstall dengan flag `--dev`, Telescope **tidak akan ikut ter-install** di production saat `composer install --no-dev`.

---

## 🧪 Troubleshooting

<details>
<summary><b>❌ Route tidak berubah setelah edit</b></summary>

```bash
php artisan route:clear
php artisan config:clear
```
</details>

<details>
<summary><b>❌ Migration error "table already exists"</b></summary>

```bash
php artisan migrate:status

# Jika perlu reset (⚠️ data hilang)
php artisan migrate:fresh
```
</details>

<details>
<summary><b>❌ Laravel terasa lambat / response > 2 detik</b></summary>

Pastikan checklist berikut:

1. Redis/Memurai sudah jalan
2. `.env` sudah set `CACHE_STORE=redis` dan `SESSION_DRIVER=redis`
3. `config/auth.php` sudah pakai `cached-eloquent` driver
4. Index database sudah dibuat di Supabase SQL Editor
5. `config/database.php` sudah ada `PDO::ATTR_PERSISTENT => true`

```bash
php artisan config:clear
php artisan cache:clear
```
</details>

<details>
<summary><b>❌ Redis connection refused</b></summary>

**Windows:** Pastikan Memurai sudah jalan. Buka Start Menu → cari "Memurai" → Start. Atau cek di `services.msc`.

**Linux/Mac:**
```bash
sudo systemctl start redis-server  # Linux
brew services start redis          # Mac
```

Verifikasi:
```bash
memurai-cli ping  # Windows
redis-cli ping    # Linux/Mac
# Harus balik: PONG
```
</details>

<details>
<summary><b>❌ Unable to load php_redis.dll</b></summary>

Buka `C:\xampp\php\php.ini`, cari dan comment out:
```ini
;extension=php_redis.dll
```

Restart XAMPP/Laragon, lalu jalankan ulang Laravel.
</details>

<details>
<summary><b>❌ Call to a member function contains() on array</b></summary>

Ini terjadi karena data dari cache berupa array biasa, bukan Collection. Pastikan selalu wrap dengan `collect()` sebelum memanggil method Collection:

```php
// ❌ Salah
$cached = Cache::get("user:{$id}:roles");
$cached->contains('superadmin');

// ✅ Benar
$userRoles = collect(Cache::get("user:{$id}:roles"))->pluck('name');
$userRoles->contains('superadmin');
```

Jalankan `php artisan cache:clear` untuk hapus cache lama yang formatnya mungkin berbeda.
</details>

<details>
<summary><b>❌ The attribute [remember_token] does not exist</b></summary>

Pastikan `remember_token` tidak ada di `$hidden` di `User.php`, dan `cacheUserData()` menggunakan `makeVisible(['remember_token'])` sebelum serialize ke cache.

```php
// User.php — jangan masukkan remember_token ke $hidden
protected $hidden = [
    'password',
    // remember_token TIDAK di-hidden
];
```
</details>

<details>
<summary><b>❌ Authentication user provider [cached-eloquent] is not defined</b></summary>

Pastikan dua hal:

1. `AppServiceProvider.php` sudah ada `Auth::resolved(...)` di method `register()`
2. `config/auth.php` sudah diupdate:

```php
'providers' => [
    'users' => [
        'driver' => 'cached-eloquent',
        'model'  => App\Models\User::class,
    ],
],
```

Lalu jalankan:
```bash
php artisan config:clear
```
</details>

<details>
<summary><b>❌ Supabase connection timeout</b></summary>

1. Gunakan connection pooling port `6543` bukan `5432`
2. Pastikan region Supabase project di **Singapore** (`ap-southeast-1`) bukan Mumbai
3. Test koneksi:

```bash
php artisan db:show
```
</details>

<details>
<summary><b>❌ Seeder error: Class not found (module seeder)</b></summary>

Pastikan namespace di file seeder modul sudah benar, lalu regenerate autoload:

```bash
composer dump-autoload
```

Jalankan ulang seeder dengan fully qualified class name:

```bash
php artisan db:seed --class="Modules\Capstone\Database\Seeders\CapstoneSeeder"
```
</details>

<details>
<summary><b>❌ Seeder error: Duplicate entry / unique constraint violation</b></summary>

Seeder dijalankan lebih dari sekali tanpa guard idempotent. Ganti `insert()` dengan `updateOrInsert()` atau `firstOrCreate()`:

```php
// ❌ Akan error jika dijalankan dua kali
DB::table('roles')->insert(['name' => 'superadmin']);

// ✅ Aman dijalankan berulang
DB::table('roles')->updateOrInsert(
    ['name' => 'superadmin'],
    ['updated_at' => now()]
);
```

Atau truncate tabel dulu sebelum insert (hati-hati di production):

```php
DB::table('roles')->truncate();
DB::table('roles')->insert([...]);
```
</details>

---

## 🚀 Future Roadmap

- [ ] 🔄 **Microservices Migration** - Isolasi per module
- [ ] 🗄️ **Database Per Module** - Separate database untuk setiap modul
- [ ] 🔐 **Enhanced RBAC** - Permission-based access control
- [ ] 🏢 **Multi-Tenant** - Support multiple institutions
- [ ] 📱 **Mobile App** - Native mobile application
- [ ] 🤖 **API Gateway** - Centralized API management
- [ ] 📊 **Analytics Dashboard** - System-wide reporting
- [ ] 🔔 **Real-time Notifications** - WebSocket integration

---

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Supabase Documentation](https://supabase.com/docs)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Predis Documentation](https://github.com/predis/predis)
- [Memurai (Redis for Windows)](https://www.memurai.com)

---

<div align="center">

Made with ❤️ by Tim Capstone

</div>
