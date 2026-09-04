# AGENTS.md

## Project Overview

CodeIgniter 3 + Bonfire HMVC garment-order system ("SI-Reklame", folder name `order_baju_template`). Stack: PostgreSQL, AdminLTE 3, `vlucas/phpdotenv`. Web root is `public/`. README (`README.markdown`, Indonesian) is the setup source of truth.

## Critical Paths

- **DocumentRoot:** `public/index.php` — loads `.env` via phpdotenv, sets `ENVIRONMENT` from `CI_ENV`, and **forces timezone to `Asia/Jakarta`**. Do not remove the timezone override (timestamps depend on it).
- **DB config:** `application/config/database.php` reads `getenv('DB_*')` — never hardcode creds there.
- **Dashboard entry:**
  - Controller: `application/controllers/admin/Home.php` (extends `Admin_Controller`) supplies `$total_order, $total_customer, $total_transaksi, $status_diproses/diambil/selesai, $total_pendapatan, $recent_orders, $customers, $recent_activity`.
  - View: `public/themes/adminlte/home/index.php` (active). Bonfire's `Template::find_view()` searches theme paths only — it does **not** fall back to `application/modules/<m>/views/`. Any `application/modules/admin/views/...` you write is invisible. The custom dashboard CSS is `public/assets/css/fashioner-dashboard.css`, loaded from the theme `index.php`.
  - Theme wrapper: `public/themes/adminlte/index.php` provides shell + sidebar (white) + transition overlay.
- **Business modules:** `application/modules/` (`order_baju` has both `Content.php` + `Transaksi.php`; `transaksi` module is the permission context, not where Transaksi logic lives).
- **Do not edit:** `bonfire/` (framework core + migrations + modules).
- **Logs:** `application/logs/`.

## Upload/Asset Trees (don't conflate)

- `public/assets/dokumen/` — report PDFs/Excel + `dokumen_transaksi/[id]/` uploads. **NOT gitignored** (untracked, empty on fresh clone).
- `application/uploads/dokumen_staging/` — staging for create-transaksi uploads (outside `public/`, not in backup). Moves to `dokumen_transaksi/[id]/` on submit.
- `application/uploads/backup/*.{zip,sql}` — DB dumps; gitignored.
- `.env`, `application/config/database.php` — gitignored (edits won't show in `git status`).
- After `.env` change: hard-refresh (Ctrl+F5); assets cached in `public/assets/cache/`.

## Environment Setup

1. `composer install` (pulls `phpoffice/phpspreadsheet`, `vlucas/phpdotenv`)
2. `cp .env.example .env`
3. `php -r "echo bin2hex(random_bytes(16));"` → set `APP_ENCRYPTION_KEY` (32 hex chars)
4. `.env` gotchas: var name is **`DB_NAME`** (not `DB_DATABASE`); `APP_BASE_URL` must end with `/` and include subfolder (`http://localhost/order_baju_template/public/`); never commit `.env`.

## Database

- Driver `postgre` — SQL is Postgres syntax.
- Import a real backup (admin Backup menu or `psql -h localhost -U postgres -d nama_db -f backup.sql`). Do **not** use `database/schema.sql` (empty schema only; roles/permissions for admin CRUD buttons only exist in real backup data).
- Migrations: **Bonfire, not CI3**, run automatically on page load (`migrate.auto_app = true` in `application/config/application.php`). No CLI command.
  - Locations: `application/db/migrations/`, `application/modules/<module>/migrations/`, `bonfire/migrations/`.
  - Files extend `Migration` with `up()`/`down()`. SQL-string migrations set `public $migration_type = 'sql';` per-file.
  - CI3's `application/config/migration.php` (`migration_enabled = FALSE`) is irrelevant.
  - Manual schema changes → add a migration class.

## Runtime Quirks

- `public/index.php` suppresses `E_DEPRECATED`/`E_STRICT` on purpose: PHP 8.2+ dynamic-property warnings break DataTables JSON endpoints. Do not re-enable.
- DataTables endpoints return JSON — keep controllers' output free of stray warnings/dumps.
- Sidebar/menus and CRUD buttons are permission-driven from DB (role_id=1 = admin). Missing buttons ⇒ check `role_permissions`, not code.
- Base controllers: `application/core/` (`Base_Controller`, `Authenticated_Controller`, `Admin_Controller`).

## Backup Module (`application/modules/backup/`)

Two kinds on one controller/page:
- **Dokumen backup** (`Backup::document`, POST): zips selected report files + transaction uploads; validates each `id:nama_file` is registered. Output ZIP → `public/assets/dokumen/`. Served via `backup/download/doc/(:num)`.
- **Database backup** (`Backup::database`): uses `pg_dump` (resolved by `find_pg_dump()`). PGPASSWORD env (never on CLI). Validates SQL; test-restore to temp DB unless `BACKUP_SKIP_TEST=1`. Artifacts → `application/uploads/backup/` (gitignored).

View JS convention (`backup/views/index.php`): every jQuery plugin init null-guarded (`if ($('#tbl-backup-history').length)`, `if ($.fn.datetimepicker)`). Inline JS assembled in one `$inline_js` string via `Assets::add_js($inline_js, 'inline')` with `'" . json_encode($server_data) . "'` interpolation inside. Preserve both.

## Frontend Conventions

- AdminLTE 3 + custom palette (krem/cokelat): `#F8F5EF` bg, `#FFFDF9` card, `#2A2520` ink, `#8A6A47` accent, `#C8A96B` warm-accent, `#E4D6C2` line. Display face `Cormorant Garamond`, body `Inter` — both loaded from Google Fonts in `public/themes/adminlte/index.php` and `public/themes/default/index.php`.
- Custom CSS files live at `public/assets/css/` (e.g. `fashioner-admin.css`, `fashioner-home.css`). Load via `Assets::add_css('css/<file>.css?v=N')` in the relevant theme `index.php` — bump `?v=` to bust cache.

## Tests

No PHPUnit/test runner — manual only.
- Admin login: `admin` / `password` (role_id 1).
- Local URL = `APP_BASE_URL`. After `.env` edit, Ctrl+F5.

## AGENTS.md Scope

This file covers the full project. Module-specific or task-specific guidance may live in `kilo.json` `instructions` or additional AGENTS.md files in subdirectories.