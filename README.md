<div align="center">

# 🎓 Integrated Academic System

### *Laravel Modular Monolith Architecture*

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
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

**Integrated Academic System** adalah platform akademik terpusat yang dibangun dengan **Laravel Modular Architecture**, menggabungkan empat sistem utama dalam satu codebase untuk efisiensi dan konsistensi data.

### ✨ Key Features

- 🎯 **Modular Architecture** - Setiap modul independen namun terintegrasi
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
│    PostgreSQL Database (Supabase/Local)     │
└─────────────────────────────────────────────┘
```

---

## 📂 Project Structure

```
integrated-academic-system/
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

- PHP >= 8.2
- Composer >= 2.x
- PostgreSQL >= 14
- Node.js >= 18 (optional, for frontend assets)

### 1️⃣ Clone Repository

```bash
git clone <repository-url>
cd integrated-academic-system
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

Edit `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=your-database-host
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

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
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Module Architecture Guide](docs/ARCHITECTURE.md)
- [API Documentation](docs/API.md)

---

## 🤝 Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) first.

### Development Team

- **Project Lead:** [Your Name]
- **Backend Team:** [Team Members]
- **Frontend Team:** [Team Members]
- **Database Admin:** [Team Members]

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 📞 Support

Butuh bantuan? Hubungi kami:

- 📧 Email: support@yourdomain.com
- 💬 Slack: [workspace-name]
- 🐛 Issues: [GitHub Issues](link-to-issues)

---

<div align="center">

**⭐ Star this repository if you find it helpful!**

Made with ❤️ by [Your Team Name]

[🔝 Back to Top](#-integrated-academic-system)

</div>
