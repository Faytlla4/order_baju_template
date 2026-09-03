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
├── application/uploads/backup/   # ZIP/SQL backup DB (tidak di git)
├── public/assets/dokumen/        # Laporan (PDF/Excel) + upload dokumen transaksi (tidak di git)
├── .env                 # Konfigurasi lokal (tidak di git)
└── .env.example         # Template .env
```

## Menampilkan Kembali Backup Dokumen Report

Setelah membuat **Backup Dokumen** (menu **Backup** > form *Backup Dokumen Terpilih*), aplikasi menyimpan riwayatnya di tabel `backup_document_history` dan menyimpan file ZIP di `application/uploads/backup/`. Untuk menampilkan kembali / mengunduh laporan (PDF/Excel) yang sudah di-backup:

1. Buka menu **Backup**.
2. Pada bagian **Riwayat Backup Dokumen**, cari baris backup yang diinginkan (kolom *Tanggal*, *Nama File*, *Jumlah Dokumen*, *Periode*).
3. Klik tombol **Download** (ikon ⬇) pada baris tersebut → browser mengunduh file ZIP.
4. Ekstrak ZIP. Di dalamnya terdapat folder `dokumen/dokumen_transaksi/[id]/` berisi file laporan/dokumen transaksi yang bisa dibuka atau dicetak ulang dengan aplikasi PDF/Office.

> Nama file ZIP berformat `backup_dokumen_YYYY-MM-DD_HHMMSS.zip` (berdasarkan waktu WIB).

### Alur & Kode Terkait

Unduhan ini disajikan oleh **`Backup::download('doc', $id)`** di `application/modules/backup/controllers/Backup.php`, di-route via `application/config/routes.php`:

```php
// application/config/routes.php
Route::any('backup/download/doc/(:num)', 'backup/backup/download/doc/$1');
```

Metode `download()` mengambil baris riwayat berdasarkan ID, mengecek file `file_path` di server, lalu mengirimkan ZIP sebagai unduhan:

```php
// application/modules/backup/controllers/Backup.php
public function download($type = '', $id = 0)
{
    $this->load->model('backup/backup_model');

    if ($type === 'doc') {
        $row = $this->backup_model->get_document_history_by_id($id);
    } elseif ($type === 'db') {
        $row = $this->backup_model->get_database_history_by_id($id);
    } else {
        show_404();
        return;
    }

    if (empty($row) || empty($row->file_path)) {
        Template::set_message('File backup tidak ditemukan.', 'error');
        redirect(SITE_AREA . '/backup');
        return;
    }

    $path = $row->file_path;
    if (!is_file($path) || filesize($path) <= 0) {
        Template::set_message('File backup sudah tidak tersedia di server.', 'error');
        redirect(SITE_AREA . '/backup');
        return;
    }

    $zipName = $row->file_name;
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipName) . '"');
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: max-age=0');
    header('Pragma: public');
    header('Expires: 0');
    readfile($path);
    exit;
}
```

Tombol **Download** pada tabel riwayat dibuat di `application/modules/backup/views/index.php`:

```php
<a href="<?php echo site_url(SITE_AREA . '/backup/download/doc/' . $h->id); ?>" class="btn btn-sm btn-success" title="Download">
    <i class="fas fa-download"></i>
</a>
```

> Riwayat diambil oleh `Backup_model::get_document_history()` dari tabel `backup_document_history`, dan disimpan saat backup dibuat lewat `save_document_history()` (kolom: `file_name`, `file_path`, `file_size`, `jumlah_dokumen`, `filter_used`, `created_on`).

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
