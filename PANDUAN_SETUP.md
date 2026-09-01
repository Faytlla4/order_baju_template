# Panduan Setup SI-Reklame

## 1. Clone Repository

```bash
git clone https://github.com/Faytlla4/order_baju_template.git
cd order_baju_template
git checkout main
```

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

1. Login ke admin panel **http://apktemplate.test/admin**
2. Masuk ke menu **Backup > Backup Database**
3. Klik **Run Backup Database**
4. Download file ZIP yang dihasilkan
5. Ekstrak ZIP, dapatkan file `.sql`
6. Import ke database teman kamu:

```bash
psql -h localhost -U postgres -d nama_db -f backup_database.sql
```

> **PENTING:** Gunakan backup database dari server asli, JANGAN pakai `database/schema.sql` karena isinya hanya struktur tabel kosong.

## 5. Buat Virtual Host (Laragon)

1. Buka Laragon
2. Klik **Menu > www > folder project**
3. Rename folder sesuai project name
4. Laragon otomatis buat vhost: `apktemplate.test`

Atau buat manual di `C:\laragon\etc\apache2\hosts\*.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "D:\laragon\www\apktemplate\public"
    ServerName apktemplate.test
</VirtualHost>
```

## 6. Buat File .env

Buat file `.env` di root project:

```env
APP_ENV=development
APP_DEBUG=true

DB_HOSTNAME=localhost
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=postgres
DB_DATABASE=nama_db

BACKUP_SKIP_TEST=1
```

> Sesuaikan `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE` dengan database teman kamu.

## 7. Akses Aplikasi

Buka browser: **http://apktemplate.test/admin**

Login:
- **Username:** `admin`
- **Password:** `password`

## Fitur yang Tersedia

| Menu | Fitur |
|------|-------|
| **Content** | Order Baju (CRUD + upload dokumen) |
| **Master** | Jenis Baju, Ukuran, Warna (CRUD) |
| **Transaksi** | Daftar Transaksi (CRUD + dokumen) |
| **Laporan** | Cetak Laporan PDF, Cetak Laporan Excel |
| **Backup** | Backup Dokumen (ZIP), Backup Database (pg_dump ZIP) |

## Troubleshooting

### Tombol Create/Edit tidak muncul
- Login sebagai **admin** (role_id = 1)
- Pastikan permissions sudah di-import dari backup database
- Cek di database: `SELECT * FROM role_permissions WHERE role_id = 1;`

### Halaman blank / error 500
- Pastikan PostgreSQL running
- Cek file `.env` sudah benar
- Cek `application/logs/` untuk lihat error

### Module tidak muncul di sidebar
- Pastikan `git checkout main` (bukan branch lain)
- Pastikan folder `application/modules/` ada isinya (9 module)

### Nama domain berbeda
Kalau pakai domain lain selain `apktemplate.test`, ubah di:
- File `.env`
- `application/config/config.php` → `$config['base_url']`
