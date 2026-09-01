# AGENTS.md

## Project Overview

CodeIgniter 3 + Bonfire HMVC application for garment order management ("order baju"). Uses PostgreSQL, AdminLTE theme, and vlucas/phpdotenv for configuration.

## Environment Setup

**Required before running:**

1. Copy `.env.example` to `.env`
2. Generate encryption key: `php -r "echo bin2hex(random_bytes(16));"`
3. Set `APP_ENCRYPTION_KEY` in `.env` (32 hex chars)
4. Copy `application/config/database.php.example` to `application/config/database.php` if `.env` doesn't exist
5. PostgreSQL credentials go in `.env` (DB_HOSTNAME, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME)
6. Run `composer install`

**Database setup:**

```bash
# Import schema
psql -U postgres -d nama_db -f database/schema.sql

# OR run Bonfire migrations
php public/index.php migrate
```

## Critical Paths

- **Web root:** `public/` (NOT project root)
- **Entry point:** `public/index.php`
- **App modules:** `application/modules/` (custom business logic)
- **Bonfire core:** `bonfire/` (framework modules, do not edit)
- **Config:** `application/config/`
- **Migrations:** `application/db/migrations/`

## Running the Application

- Set Apache DocumentRoot to `public/` folder
- Access via configured domain (e.g., `http://apktemplate.test`)
- Default login: `admin` / `password`

## Database

- Driver: `postgre` (CodeIgniter 3 PostgreSQL driver)
- Migrations use raw SQL (`$migration_type = 'sql'`)
- Run migrations: `php public/index.php migrate`

## Modules (HMVC)

Active custom modules in `application/modules/`:
- `backup` - Document & database backup (requires `pg_dump` in PATH)
- `order_baju` - Order management
- `transaksi` - Transactions
- `master_jenis_baju`, `master_ukuran`, `master_warna` - Master data
- `report_pdf`, `report_excel` - Reports
- `sk_tidak_mampu` - Certificate module

## Framework Notes

- Uses Bonfire's HMVC (wiredesignz/codeigniter-modular-extensions-hmvc)
- Base controllers in `application/core/`: `Base_Controller`, `Authenticated_Controller`, `Admin_Controller`, etc.
- CI3 system located at `bonfire/ci3/`
- Timezone: `Asia/Jakarta`
- Error reporting suppresses E_DEPRECATED and E_STRICT (PHP 8.2+ dynamic property warnings break DataTables JSON endpoints)

## No Testing Framework

No PHPUnit or test runner configured. Manual testing only.

## Backup Module Requirement

Database backup requires `pg_dump` utility available in system PATH or common PostgreSQL installation directories.
