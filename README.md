<div align="center">

# 🎓 Web Akademik Terintegrasi Teknik Komputer

### *Laravel Modular Monolith Architecture*

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Supabase](https://img.shields.io/badge/Supabase-3FCF8E?style=for-the-badge&logo=supabase&logoColor=white)](https://supabase.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

*Sistem terintegrasi berbasis Laravel Modular Monolith yang terdiri dari empat aplikasi akademik dalam satu proyek terpusat.*

[📖 Documentation](#-documentation) • [🚀 Quick Start](#-quick-start) • [🏗️ Architecture](#-system-architecture) • [🤝 Contributing](#-development-rules)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [System Modules](#-system-modules)
- [System Architecture](#-system-architecture)
- [Project Structure](#-project-structure)
- [Quick Start](#-quick-start)
- [Database Convention](#-database-convention)
- [Migration Guide](#-migration-guide)
- [Essential Commands](#-essential-commands)
- [User Role System](#-user-role-system)
- [Development Rules](#-development-rules)
- [Performance Configuration](#-performance-configuration)
- [Troubleshooting](#-troubleshooting)
- [Future Roadmap](#-future-roadmap)

---

## 🌟 Overview

**Web Akademik Terintegrasi Teknik Komputer** adalah platform akademik terpusat yang dibangun dengan **Laravel Modular Architecture** dan **Supabase** sebagai database backend, menggabungkan empat sistem utama dalam satu codebase untuk efisiensi dan konsistensi data.

### ✨ Key Features

- 🎯 **Modular Architecture** - Setiap modul independen namun terintegrasi
- 🟢 **Supabase Backend** - PostgreSQL hosting dengan realtime features
- 🗄️ **Single Database** - Satu database PostgreSQL terpusat
- 🔐 **Role-Based Access Control** - 4 level user roles
- ⚡ **Optimized Performance** - Siap untuk production environment
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
┌──────────────────▼──────────────────────────┐
│      🟢 Supabase (PostgreSQL Database)      │
└─────────────────────────────────────────────┘
```

---

## 📂 Project Structure

```
WebsiteTekkom/
├── app/
│   ├── Http/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── migrations/          # ← Global migrations (core tables)
│   └── seeders/
├── Modules/
│   ├── Capstone/
│   │   ├── Database/
│   │   │   └── Migrations/  # ← Capstone-specific migrations
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   ├── Models/
│   │   └── Routes/
│   ├── BankSoal/
│   ├── Kemahasiswaan/
│   └── EOffice/
├── routes/
│   ├── web.php
│   └── api.php
├── .env.example
├── composer.json
└── README.md
```

---

## 🚀 Quick Start

### Prerequisites

- PHP >= 8.2.12
- Composer >= 2.9.5
- Supabase Account (or PostgreSQL >= 14)
- Node.js >= 18 (optional, for frontend assets)

### 1️⃣ Clone Repository

```bash
git clone https://github.com/bimo3058/WebsiteTekkom.git
cd WebsiteTekkom
```

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4️⃣ Configure Database

#### Option A: Using Supabase (Recommended)

1. Create a new project at [Supabase Dashboard](https://app.supabase.com)
2. Go to **Project Settings** → **Database**
3. Copy connection details

Edit `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=db.your-project-ref.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
```

#### Option B: Using Local PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

> 💡 **Tip:** Supabase menyediakan hosting database gratis dengan 500MB storage dan realtime features.

### 5️⃣ Run Migrations

```bash
php artisan migrate
```

### 6️⃣ Start Development Server

```bash
php artisan serve
```

Visit: **http://127.0.0.1:8000**

---

## 🟢 Supabase Configuration

### Why Supabase?

- ✅ **Free Tier:** 500MB database, unlimited API requests
- ✅ **Auto Backup:** Automatic daily backups
- ✅ **Realtime:** Built-in realtime subscriptions
- ✅ **Global CDN:** Fast worldwide access
- ✅ **SSL Connection:** Secure by default

### Setup Guide

#### 1. Create Supabase Project

1. Visit [Supabase Dashboard](https://app.supabase.com)
2. Click **New Project**
3. Fill in project details:
   - **Name:** `laravel`
   - **Database Password:** (save this securely)
   - **Region:** Choose closest to your users

#### 2. Get Connection String

Go to **Project Settings** → **Database** → **Connection String**

**URI Format:**
```
postgresql://postgres:[YOUR-PASSWORD]@db.your-ref.supabase.co:5432/postgres
```

**Connection pooling (recommended for production):**
```
postgresql://postgres:[YOUR-PASSWORD]@db.your-ref.supabase.co:6543/postgres?pgbouncer=true
```

#### 3. Configure Laravel

Update `.env`:

```env
# Supabase Database
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-secure-password

# For connection pooling (production)
# DB_PORT=6543
```

#### 4. Test Connection

```bash
php artisan db:show
```

Expected output:
```
PostgreSQL ................................................ 15.x
Database .................................................. postgres
Host ...................................................... db.xxxxx.supabase.co
Port ...................................................... 5432
Username .................................................. postgres
```

### Supabase Features Integration

#### Row Level Security (RLS)

Supabase mendukung RLS untuk keamanan ekstra. Aktifkan di Supabase Dashboard:

```sql
-- Example: Enable RLS for capstone_topics
ALTER TABLE capstone_topics ENABLE ROW LEVEL SECURITY;

-- Create policy
CREATE POLICY "Students can view their own topics"
ON capstone_topics FOR SELECT
USING (auth.uid() = student_id);
```

#### Realtime Subscriptions

Enable realtime untuk tabel tertentu di **Database** → **Replication**:

```javascript
// Frontend example
const supabase = createClient(SUPABASE_URL, SUPABASE_KEY)

supabase
  .channel('capstone-changes')
  .on('postgres_changes', 
    { event: '*', schema: 'public', table: 'capstone_topics' },
    (payload) => console.log('Change detected:', payload)
  )
  .subscribe()
```

#### Storage for Files

Gunakan Supabase Storage untuk upload file:

```php
// Laravel integration with Supabase Storage
// Install: composer require supabase/supabase-php

use Supabase\SupabaseClient;

$supabase = new SupabaseClient(
    env('SUPABASE_URL'),
    env('SUPABASE_KEY')
);

// Upload file
$file = $request->file('document');
$supabase->storage
    ->from('capstone-documents')
    ->upload("documents/{$filename}", $file);
```

### Supabase CLI (Optional)

Install Supabase CLI untuk local development:

```bash
# Install
npm install -g supabase

# Login
supabase login

# Link project
supabase link --project-ref your-project-ref

# Pull remote schema
supabase db pull
```

### Monitoring & Analytics

Akses **Database** → **Reports** untuk:
- Query performance
- Connection pooling stats
- Database size
- API usage

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
| 📗 Bank Soal | `bank_soal_` | `bank_soal_questions`, `bank_soal_exams` |
| 📙 Kemahasiswaan | `kemahasiswaan_` | `kemahasiswaan_events`, `kemahasiswaan_organizations` |
| 📕 E-Office | `eoffice_` | `eoffice_letters`, `eoffice_documents` |

> ⚠️ **IMPORTANT:** Semua tabel module **WAJIB** menggunakan prefix yang sesuai.

---

## 🔄 Migration Guide

### Run All Migrations

```bash
php artisan migrate
```

### Run Specific Module Migration

```bash
# Capstone module
php artisan migrate --path=Modules/Capstone/Database/Migrations

# Bank Soal module
php artisan migrate --path=Modules/BankSoal/Database/Migrations

# Kemahasiswaan module
php artisan migrate --path=Modules/Kemahasiswaan/Database/Migrations

# E-Office module
php artisan migrate --path=Modules/EOffice/Database/Migrations
```

### Reset Database (⚠️ Danger Zone)

```bash
# This will drop all tables and re-run migrations
php artisan migrate:fresh
```

### Create New Migration

**Global migration:**
```bash
php artisan make:migration create_users_table
```

**Module-specific migration:**
```bash
php artisan make:migration create_capstone_periods_table --path=Modules/Capstone/Database/Migrations
```

---

## 🛠️ Essential Commands

### Cache Management

**Clear all caches (run after route/config changes):**

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Or clear all at once:**

```bash
php artisan optimize:clear
```

### Production Optimization

```bash
php artisan optimize
composer dump-autoload -o
```

### Development Tools

```bash
# List all routes
php artisan route:list

# List routes for specific module
php artisan route:list | grep capstone

# Check database connection
php artisan db:show

# Create symbolic link for storage
php artisan storage:link
```

---

## 👤 User Role System

### Available Roles

| Role | Code | Description |
|------|------|-------------|
| 🔴 **Superadmin** | `SUPERADMIN` | Full system access |
| 🟠 **Admin** | `ADMIN` | Administrative access |
| 🟡 **Lecturer** | `LECTURER` | Lecturer/faculty access |
| 🟢 **Student** | `STUDENT` | Student access (default) |

> ⚠️ **Important:** Role values are **case-sensitive** and must match database enum constraints.

### Default Configuration

- **Default role:** `STUDENT`
- **Role field:** `users.role` (enum type)
- **Case-sensitive:** Yes (use UPPERCASE)

---

## 🔐 Development Rules

### ⚠️ WAJIB DIBACA TIM

| Rule | Description |
|------|-------------|
| 🚫 **No Migration Edit** | Jangan edit migration yang sudah dijalankan di production |
| 📛 **Use Prefix** | Gunakan prefix sesuai module untuk semua tabel |
| 🔄 **Clear Cache** | Selalu clear cache setelah ubah route/config |
| 🔒 **No .env Commit** | Jangan commit file `.env` ke repository |
| 🔤 **Uppercase Enum** | Gunakan UPPERCASE untuk semua enum role |
| ⚡ **Eager Loading** | Gunakan `with()` untuk menghindari N+1 query problem |
| 📝 **Code Documentation** | Tambahkan docblock untuk function public |
| 🧪 **Test Before Commit** | Test fitur sebelum commit ke branch utama |

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/module-name-feature

# Commit changes
git add .
git commit -m "feat(module): description"

# Push to remote
git push origin feature/module-name-feature
```

### Commit Message Convention

```
feat(capstone): add topic submission feature
fix(bank-soal): resolve question duplication bug
docs(readme): update installation guide
refactor(kemahasiswaan): optimize event query
```

---

## ⚡ Performance Configuration

### Development Environment

```env
APP_ENV=local
APP_DEBUG=true
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Production Environment

```env
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=database
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### After Deployment

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 🧪 Troubleshooting

### Common Issues

<details>
<summary><b>❌ Route tidak berubah setelah edit</b></summary>

**Solution:**
```bash
php artisan route:clear
php artisan config:clear
```
</details>

<details>
<summary><b>❌ Migration error "table already exists"</b></summary>

**Solution:**
```bash
# Check migration status
php artisan migrate:status

# If needed, reset (⚠️ data will be lost)
php artisan migrate:fresh
```
</details>

<details>
<summary><b>❌ Enum role error / constraint violation</b></summary>

**Solution:**
- Pastikan value role menggunakan UPPERCASE
- Check database enum constraint: `SUPERADMIN`, `ADMIN`, `LECTURER`, `STUDENT`
</details>

<details>
<summary><b>❌ Laravel terasa lambat di development</b></summary>

**Solution:**
```env
# Set in .env
SESSION_DRIVER=file
CACHE_DRIVER=file
```

Then run:
```bash
php artisan optimize:clear
```
</details>

<details>
<summary><b>❌ Class not found error</b></summary>

**Solution:**
```bash
composer dump-autoload
php artisan clear-compiled
```
</details>

<details>
<summary><b>❌ Supabase connection timeout</b></summary>

**Solution:**
1. Check if your IP is allowed in Supabase Dashboard
   - Go to **Project Settings** → **Database** → **Connection Pooling**
   - Disable "Restrict database access to dedicated IPs" for development
   
2. Try connection pooling port:
```env
DB_PORT=6543  # Instead of 5432
```

3. Test connection:
```bash
php artisan db:show
php artisan tinker
>>> DB::connection()->getPdo();
```
</details>

<details>
<summary><b>❌ SSL connection error with Supabase</b></summary>

**Solution:**
Add SSL mode to database config in `config/database.php`:

```php
'pgsql' => [
    // ... other config
    'sslmode' => env('DB_SSLMODE', 'prefer'),
],
```

Then in `.env`:
```env
DB_SSLMODE=require
```
</details>

---

## 🚀 Future Roadmap

Struktur modular ini mendukung pengembangan ke arah:

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
- [Module Architecture Guide](docs/ARCHITECTURE.md)
- [API Documentation](docs/API.md)

### Supabase Resources

- 📖 [Supabase with Laravel Guide](https://supabase.com/docs/guides/getting-started/tutorials/with-laravel)
- 🔐 [Row Level Security](https://supabase.com/docs/guides/auth/row-level-security)
- 💾 [Storage Management](https://supabase.com/docs/guides/storage)
- ⚡ [Realtime Subscriptions](https://supabase.com/docs/guides/realtime)

---

## 🤝 Contributing

### Development Team

- **Project Lead:** Bimo Kusumo Putro Wicaksono
- **Bank Soal:** Dzaki Eka Atmaja, Evan Adkara Christian P, Nabil Bintang Ardiansyah P.  
- **Capstone + TA:** Ananda Prida Yusuf S, Fayyadh Muhammad Habibie, Muhammad Riza Saputra
- **E-Office:** Andhinee Clarisaa Tanasale, Cetta Masinda Amany, Elvina Nasywa Ariyani
- **Manajemen Kemahasiswaan + KP:** Devarlo Rahadyan Razan, Muhammad Reswara Suryawan, Surya Hari Putra, Syahbana Hatab

---

<div align="center">

**⭐ Star this repository if you find it helpful!**

Made with ❤️ by Tim Capstone

</div>
