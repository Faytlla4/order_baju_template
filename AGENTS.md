# AGENTS.md

## Project Overview

CodeIgniter 3 + Bonfire HMVC garment-order system ("SI-Reklame", folder name `order_baju_template`). Stack: PostgreSQL, AdminLTE 3, `vlucas/phpdotenv`. Web root is `public/`. README (`README.markdown`, Indonesian) is the setup source of truth.

## Critical Paths

- **Web root / DocumentRoot:** `public/` — entry point `public/index.php`
- **Env loading:** `public/index.php` loads `.env` from project root via phpdotenv and sets `ENVIRONMENT` from `CI_ENV`. `application/config/database.php` reads DB values with `getenv()` — do NOT hardcode credentials there.
- **Generated / uploaded files are split across two trees — do not confuse them:**
  - `public/assets/dokumen/` (report PDF/Excel + `dokumen_transaksi/[id]/` uploads) — **NOT gitignored**; files here show up as untracked in `git status` and a fresh clone starts them empty. This is separate from the admin `uploads/` tree. Upload staging saat CREATE transaksi disimpan di `application/uploads/dokumen_staging/` (DI LUAR `public/assets/dokumen/` sehingga tidak ikut backup) lalu dipindah ke `dokumen_transaksi/[id]/`.
  - `application/uploads/backup/*.{zip,sql}` — gitignored (`application/uploads/backup/`, `.gitignore:104-106`); DB backup artifacts.
  - `.env`, `application/config/database.php` are gitignored (`.gitignore:93,100`) — edits won't appear in `git status`.
- **Custom business logic:** `application/modules/`
- **Do not edit:** `bonfire/` (framework: core `bonfire/ci3/` + modules + `bonfire/migrations/`)
- **Gitignore trap:** `application/modules/*` is gitignored; only the 11 known modules are whitelisted via `.gitignore:48-72`. If you create a new module, add a `!application/modules/<name>/` + `!application/modules/<name>/**` pair to `.gitignore` or it won't be tracked.
- **Logs:** `application/logs/`

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
