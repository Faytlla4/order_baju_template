# SI-Reklame (Sistem Informasi Reklame)

Aplikasi pengelolaan konveksi berbasis web dengan **CodeIgniter 3 + Bonfire HMVC**.

> **Catatan penting:** Project ini bernama `order_baju_template` dan dikenal juga sebagai "SI-Reklame". Web root & entry point adalah folder **`public/`**, bukan root project.

## Requirements

- PHP >= 7.4 (diuji dengan PHP 7.4.33; juga berjalan di PHP 8.2 dengan catatan error-reporting)
- PostgreSQL >= 12
- Composer
- Apache dengan `mod_rewrite`
- `pg_dump` (untuk fitur Backup Database)

## Kenapa aplikasi tampil blank / assets tidak terbaca

Penyebab paling umum:
1. **DocumentRoot salah** — Apache harus menunjuk ke folder `public/`, bukan root project.
2. **`.env` belum dibuat** — terutama `APP_ENCRYPTION_KEY` dan `DB_*`.
3. **`.env` memakai nama variabel salah** — aplikasi membaca `DB_NAME` (BUKAN `DB_DATABASE`).

---

## 1. Clone Repository

```bash
git clone https://github.com/Faytlla4/order_baju_template.git
cd order_baju_template
git checkout main
```

Pastikan ada **9 modul** di `application/modules/`:
`backup, master, master_jenis_baju, master_ukuran, master_warna, order_baju, reports, report_excel, report_pdf, sk_tidak_mampu, transaksi`

## 2. Install Dependencies

```bash
composer install
```

## 3. Buat Database

Buka pgAdmin atau terminal PostgreSQL:

```sql
CREATE DATABASE nama_db;
```

## 4. Import Database dari Backup

> **PENTING:** Gunakan backup database dari server asli (mis. hasil menu **Backup > Backup Database**), **JANGAN** pakai `database/schema.sql` karena isinya hanya struktur tabel kosong.

1. Masuk ke admin panel.
2. Buka menu **Backup > Backup Database**.
3. Klik **Run Backup Database** → unduh file ZIP.
4. Ekstrak ZIP → dapatkan file `.sql`.
5. Import:

```bash
psql -h localhost -U postgres -d nama_db -f backup_database.sql
```

> `Backup Database` membutuhkan `pg_dump` tersedia di PATH atau direktori instalasi PostgreSQL.

## 5. Buat File .env

Salin dari template:

```bash
cp .env.example .env
```

Lalu isi sesuai environment Anda. **Nama variabel wajib persis** (aplikasi membacanya apa adanya):

```env
# Environment (dibaca public/index.php)
CI_ENV=development

# Database — wajib gunakan DB_NAME (bukan DB_DATABASE)
DB_HOSTNAME=localhost
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=password_anda
DB_NAME=nama_db
DB_DRIVER=postgre
DB_DEBUG=true

# Application — wajib diakhiri slash
APP_BASE_URL=http://localhost/order_baju_template/public/
APP_ENCRYPTION_KEY=<32_karakter_hex>

# Backup — 1 = skip test restore (lebih cepat, ~5s vs ~50s)
BACKUP_SKIP_TEST=0
```

Generate `APP_ENCRYPTION_KEY` (wajib 32 karakter hex):

```bash
php -r "echo bin2hex(random_bytes(16));"
```

> JANGAN commit file `.env` ke git (sudah di-ignore di `.gitignore`).

## 6. Konfigurasi Database (otomatis via .env)

`application/config/database.php` sekarang **membaca nilai dari `.env`** (via `getenv()`). Anda tidak perlu mengubah file ini. Nilai default di dalamnya dipakai hanya jika variabel `.env` kosong.

## 7. Konfigurasi Web Server

### Opsi A — XAMPP (subfolder) — yang dipakai di env ini

Letakkan project di `C:\xampp74\htdocs\order_baju_template`. Akses:

```
http://localhost/order_baju_template/public/
```

`APP_BASE_URL` harus: `http://localhost/order_baju_template/public/`

### Opsi B — Virtual Host (mis. Laragon / apktemplate.test)

```apache
<VirtualHost *:80>
    DocumentRoot "D:\laragon\www\apktemplate\public"
    ServerName apktemplate.test
</VirtualHost>
```

`APP_BASE_URL` harus: `http://apktemplate.test/`

## 8. Akses Aplikasi

Buka browser sesuai konfigurasi:

- XAMPP subfolder: `http://localhost/order_baju_template/public/admin`
- VHost: `http://apktemplate.test/admin`

Login:
- **Username:** `admin`
- **Password:** `password`

> Melalui `.htaccess` + routing Bonfire, `.../public/admin` otomatis di-route ke halaman login.

---

## Fitur

| Menu | Fitur |
|------|-------|
| **Content** | Order Baju (CRUD + upload dokumen) |
| **Master** | Jenis Baju, Ukuran, Warna (CRUD) |
| **Transaksi** | Daftar Transaksi (CRUD + dokumen + edit dokumen) |
| **Laporan** | Cetak Laporan PDF, Cetak Laporan Excel |
| **Backup** | Backup Dokumen (ZIP), Backup Database (pg_dump ZIP) |

## Struktur Folder Penting

```
├── application/
│   ├── config/          # Konfigurasi (config.php, database.php, dsb)
│   ├── core/            # Controller dasar (Base, Authenticated, Admin, dll)
│   ├── db/migrations/   # Migrasi aplikasi (SQL)
│   └── modules/         # Modul HMVC kustom (bisnis logic)
├── bonfire/             # Framework Bonfire + CI3 (jangan diedit)
│   └── ci3/             # CodeIgniter 3 system
├── public/              # WEB ROOT (DocumentRoot)
│   ├── index.php        # Entry point
│   └── assets/          # CSS, JS, images
├── database/schema.sql  # Hanya struktur kosong (bukan data aktual)
├── uploads/             # File upload & report (tidak di git)
├── .env                 # Konfigurasi lokal (tidak di git)
└── .env.example         # Template .env
```

## Environment Variables

| Variable | Keterangan | Default |
|----------|-----------|---------|
| `CI_ENV` | Environment (`development`/`testing`/`production`) | `development` |
| `DB_HOSTNAME` | Host DB | `localhost` |
| `DB_PORT` | Port DB | `5432` |
| `DB_USERNAME` | User DB | `postgres` |
| `DB_PASSWORD` | Password DB | (kosong) |
| `DB_NAME` | Nama database (BUKAN `DB_DATABASE`) | `nama_db` |
| `DB_DRIVER` | Driver (`postgre`) | `postgre` |
| `DB_DEBUG` | Tampilkan error DB | `false` |
| `APP_BASE_URL` | URL aplikasi (berakhiran slash) | `http://localhost/order_baju_template/public/` |
| `APP_ENCRYPTION_KEY` | Kunci enkripsi 32 hex | (harus diisi) |
| `BACKUP_SKIP_TEST` | Skip test-restore backup (1=ya) | `0` |

## Troubleshooting

### Tombol Create/Edit tidak muncul
- Login sebagai **admin** (role_id = 1).
- Pastikan permissions sudah di-import (berasal dari backup database, bukan schema kosong).
- Cek: `SELECT * FROM role_permissions WHERE role_id = 1;`

### Halaman blank / error 500
- Pastikan PostgreSQL running & kredensial `.env` benar.
- Pastikan `APP_ENCRYPTION_KEY` terisi (32 hex).
- Cek `application/logs/` untuk error.

### Module tidak muncul di sidebar
- Pastikan `git checkout main` dan `application/modules/` berisi modul.
- Sidebar dibangun dari permission yang dimiliki user di database.

### Assets / CSS / JS tidak terbaca
- DocumentRoot harus menunjuk ke `public/`, dan `APP_BASE_URL` harus persis dengan URL akses Anda (termasuk subfolder `/order_baju_template/public/`).
- Setelah ganti `.env`, **hard refresh** browser (Ctrl+F5) karena asset di-cache di `public/assets/cache/`.

### Nama domain/URL berbeda
Ubah `APP_BASE_URL` di `.env` agar persis dengan URL akses aplikasi.

## License

MIT License
