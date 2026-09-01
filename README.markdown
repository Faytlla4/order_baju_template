# SI-Reklame (Sistem Informasi Reklame)

Aplikasi pengelolaan konveksi berbasis web dengan CodeIgniter 3 + Bonfire HMVC.

## Requirements

- PHP >= 7.4 (tested with PHP 7.4.33)
- PostgreSQL >= 12
- Composer
- Apache with mod_rewrite (or Laragon/XAMPP)
- `pg_dump` utility (for database backup feature)

## Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd apktemplate
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
DB_HOSTNAME=localhost
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_NAME=nama_db
DB_DRIVER=postgre
DB_DEBUG=false

APP_BASE_URL=http://localhost/
APP_ENCRYPTION_KEY=your-random-32-char-key
```

Generate an encryption key:

```bash
php -r "echo bin2hex(random_bytes(16));"
```

### 4. Setup Database

Create the PostgreSQL database:

```sql
CREATE DATABASE nama_db;
```

### 5. Import Database Schema

```bash
psql -U postgres -d nama_db -f database/schema.sql
```

Or use Bonfire's built-in migration:

```bash
php public/index.php migrate
```

### 6. Configure Web Server

**Apache Virtual Host:**

```apache
<VirtualHost *:80>
    DocumentRoot "/path/to/apktemplate/public"
    ServerName apktemplate.test
</VirtualHost>
```

**Laragon:**

Create a site with:
- Domain: `apktemplate.test`
- Document Root: `public`

### 7. Run Application

Open in browser: `http://apktemplate.test`

Default login:
- Username: `admin`
- Password: `password`

## Modules

| Module | Description |
|--------|-------------|
| `order_baju` | Manajemen pesanan baju |
| `transaksi` | Transaksi penjualan |
| `master_jenis_baju` | Master data jenis baju |
| `master_ukuran` | Master data ukuran |
| `master_warna` | Master data warna |
| `report_pdf` | Laporan dalam format PDF |
| `report_excel` | Laporan dalam format Excel |
| `backup` | Backup dokumen & database |
| `sk_tidak_mampu` | Surat keterangan tidak mampu |

## Backup Module

### Backup Dokumen

Creates a ZIP archive containing PDF and Excel reports based on filter criteria.

### Backup Database

Creates a ZIP archive containing a SQL dump of the PostgreSQL database.

**Requirements:**
- `pg_dump` must be available in system PATH or in a common installation directory
- The module auto-detects `pg_dump` from:
  - System PATH (`where pg_dump` on Windows, `which pg_dump` on Linux)
  - Common PostgreSQL installation paths

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOSTNAME` | Database host | `localhost` |
| `DB_PORT` | Database port | `5432` |
| `DB_USERNAME` | Database username | `postgres` |
| `DB_PASSWORD` | Database password | (empty) |
| `DB_NAME` | Database name | `nama_db` |
| `DB_DRIVER` | Database driver | `postgre` |
| `DB_DEBUG` | Show DB errors | `false` |
| `APP_BASE_URL` | Application base URL | `http://localhost/` |
| `APP_ENCRYPTION_KEY` | Encryption key (32 chars) | (must be set) |

## Project Structure

```
apktemplate/
├── application/           # CI3 application directory
│   ├── config/           # Configuration files
│   ├── core/             # Core controllers (Base, Authenticated)
│   ├── modules/          # HMVC modules
│   │   ├── backup/       # Backup module (tracked in git)
│   │   ├── order_baju/   # Order management
│   │   ├── transaksi/    # Transactions
│   │   └── ...
│   └── uploads/          # Uploaded files (not in git)
├── bonfire/              # Bonfire framework (CI3 HMVC)
├── public/               # Web root (DocumentRoot)
│   ├── assets/           # CSS, JS, images
│   ├── themes/           # AdminLTE theme
│   └── index.php         # Entry point
├── vendor/               # Composer dependencies (not in git)
├── .env.example          # Environment template
├── .env                  # Your local environment (not in git)
└── composer.json
```

## Troubleshooting

### pg_dump not found

Ensure PostgreSQL is installed and `pg_dump` is in your system PATH.

**Windows:**
```bash
# Check if pg_dump is in PATH
where pg_dump
```

**Linux:**
```bash
# Check if pg_dump is installed
which pg_dump
sudo apt install postgresql-client
```

### Database connection failed

1. Ensure PostgreSQL is running
2. Check `.env` credentials match your PostgreSQL setup
3. Verify the database `nama_db` exists

### Session errors

Ensure the temp directory is writable:
```bash
chmod -R 777 application/cache
```

## License

MIT License
