# AGENTS.md

## Project Overview

CodeIgniter 3 + Bonfire HMVC app for garment order management. PostgreSQL, AdminLTE theme, `vlucas/phpdotenv` for config. Named `order_baju_template`, also called "SI-Reklame". Web root is `public/` (NOT project root). The README (`README.markdown`, Indonesian) is the authoritative setup guide — read it before changing setup/docs.

## Critical Paths

- **Web root / DocumentRoot:** `public/` — entry point `public/index.php`
- **Env loading:** `public/index.php` loads `.env` from project root via phpdotenv and sets `ENVIRONMENT` from `CI_ENV`. `application/config/database.php` reads DB values with `getenv()` — do NOT hardcode credentials there.
- **Git-ignored but load-bearing:** `.env`, `application/config/database.php`, and `uploads/` are gitignored (`.gitignore:93,100`) — edits there won't appear in `git status`, and a fresh clone rebuilds `uploads/` (report/upload files) from nothing.
- **Custom business logic:** `application/modules/`
- **Do not edit:** `bonfire/` (framework: core `bonfire/ci3/` + modules + `bonfire/migrations/`)
- **Logs:** `application/logs/`

## Environment Setup

1. `composer install` (installs `phpoffice/phpspreadsheet`, `vlucas/phpdotenv`)
2. `cp .env.example .env`
3. Set `APP_ENCRYPTION_KEY`: `php -r "echo bin2hex(random_bytes(16));"` (must be 32 hex chars)
4. PostgreSQL creds in `.env`. Variable names are read **verbatim** — gotchas:
   - Database name var is `DB_NAME` — **never** `DB_DATABASE`
   - `APP_BASE_URL` must end with `/` and match the exact access URL (incl. subfolder path, e.g. `http://localhost/order_baju_template/public/`)
   - Never commit `.env`.
5. `application/config/database.php.example` is a fallback only; `.env` is the primary config. Variants here: `database.php` reads `getenv('DB_*)` directly.

## Database Setup

- Driver `postgre`. Use **PostgreSQL** — SQL in migrations is Postgres syntax.
- **Import a real backup** (via admin menu Backup > Backup Database, or `psql -h localhost -U postgres -d nama_db -f backup.sql`). Do **NOT** use `database/schema.sql` — it is an empty structure only; roles/permissions (needed for admin CRUD buttons) only exist in a real backup's data.
- **Migrations (Bonfire, not CI):** applied automatically on page load (`application/config/application.php`: `migrate.auto_app = true`; core off). No CLI migrate command exists.
  - App migrations: `application/db/migrations/`
  - Per-module migrations: `application/modules/<module>/migrations/`
  - Core: `bonfire/migrations/`
  - Files are PHP classes extending `Migration` with `up()`/`down()`; `public $migration_type = 'sql';` is a per-class property allowing raw SQL strings. CI3's own `application/config/migration.php` (`migration_enabled = FALSE`) is irrelevant. Manual schema edits should be added as migration classes.

## Modules (HMVC)

Custom modules in `application/modules/` (11 dirs):
`backup`, `master`, `master_jenis_baju`, `master_ukuran`, `master_warna`, `order_baju`, `reports`, `report_excel`, `report_pdf`, `sk_tidak_mampu`, `transaksi`

Note: `order_baju` owns both `Content.php` and `Transaksi.php` controllers; `transaksi` is not where Transaksi lives. "Module" here = Bonfire context/permission namespace.

## Framework / Runtime Quirks

- HMVC via wiredesignz/codeigniter-modular-extensions-hmvc. Base controllers in `application/core/`: `Base_Controller`, `Authenticated_Controller`, `Admin_Controller`, etc.
- Timezone `Asia/Jakarta`.
- `public/index.php` suppresses `E_DEPRECATED`/`E_STRICT` **on purpose**: PHP 8.2+ "Creation of dynamic property" warnings flood output and break the DataTables JSON endpoints. Do not re-enable.
- DataTables endpoints return JSON — keep controllers' output free of stray warnings/dumps.
- Sidebar/menus and CRUD buttons are permission-driven from the DB (role_id=1 = admin). Missing buttons ⇒ check `role_permissions` in the DB, not code.

## Backup Module

Requires `pg_dump` reachable from PATH or common PG install dirs (`find_pg_dump()` in `application/modules/backup/controllers/Backup.php`). DB backups use `PGPASSWORD` env (never password on CLI), validate output, and run a test-restore unless `BACKUP_SKIP_TEST=1`.

## Tests

No PHPUnit/test runner configured — manual testing only.
- Login for manual validation: username `admin`, password `password` (role_id 1 = admin).
- Local URL follows `APP_BASE_URL` (e.g. `http://localhost/order_baju_template/public/`). After changing `.env`, hard-refresh (Ctrl+F5) — assets are cached in `public/assets/cache/`.