# PANDUAN UPLOAD KE HOSTING (Tanpa SSH)

## Persyaratan Hosting
- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- cPanel / DirectAdmin dengan File Manager
- Tidak perlu SSH/Terminal

---

## LANGKAH 1: Persiapan di Lokal

### 1.1 Jalankan Script Persiapan
```
Klik 2x file: prepare-deploy.bat
```
Tunggu sampai selesai. Ini akan membuat folder `tokoemas-deploy/` yang siap upload.

### 1.2 Konfigurasi .env
Buka folder `tokoemas-deploy/sites/` dan untuk setiap site:

1. **Rename** `.env.example` menjadi `.env`
2. **Edit** file `.env` dan ganti:
   - `xxxxxxxx_wates` → nama database yang dibuat di hosting
   - `xxxxxxxx_user` → username database
   - `GANTI_PASSWORD_DISINI` → password database

**Contoh untuk Wates:**
```env
DB_DATABASE=hartowi_wates
DB_USERNAME=hartowi_user
DB_PASSWORD=password123
```

### 1.3 Build Assets (jika belum)
```bash
npm install
npm run build
```

---

## LANGKAH 2: Buat Database di Hosting

Login ke cPanel, buka **MySQL Databases**:

### Buat 3 Database:
| Nama Database | Untuk |
|---------------|-------|
| `hartowi_wates` | Store Wates & Wates1 |
| `hartowi_sentolo` | Store Sentolo & Sentolo1 |
| `hartowi_member` | Member (shared) |

### Buat 1 User Database:
- Username: `hartowi_posuser`
- Password: (catat password ini)

### Assign User ke Semua Database:
- `hartowi_posuser` → `hartowi_wates` (ALL PRIVILEGES)
- `hartowi_posuser` → `hartowi_sentolo` (ALL PRIVILEGES)
- `hartowi_posuser` → `hartowi_member` (ALL PRIVILEGES)

---

## LANGKAH 3: Import Schema Database

### Via phpMyAdmin:
1. Buka **phpMyAdmin** di cPanel
2. Pilih database `hartowi_wates`
3. Klik tab **Import**
4. Upload file `database/schema-wates.sql`
5. Ulangi untuk `hartowi_sentolo` dan `hartowi_member`

**CATATAN:** File SQL akan di-generate saat prepare-deploy.

---

## LANGKAH 4: Upload ke Hosting

### 4.1 Upload via File Manager
1. Login cPanel → **File Manager**
2. Masuk ke folder `/public_html/`
3. Buat folder baru: `tokoemas`
4. Upload seluruh isi folder `tokoemas-deploy/` ke `/public_html/tokoemas/`

### 4.2 Struktur yang Benar:
```
/public_html/
└── tokoemas/
    ├── core/
    │   ├── app/
    │   ├── bootstrap/
    │   ├── config/
    │   ├── vendor/
    │   └── ...
    └── sites/
        ├── wates/
        │   ├── public/
        │   ├── storage/
        │   └── .env
        ├── wates1/
        ├── sentolo/
        └── sentolo1/
```

---

## LANGKAH 5: Set Permission

Di File Manager, set permission folder berikut:

| Folder | Permission |
|--------|------------|
| `tokoemas/core/bootstrap/cache` | 755 atau 775 |
| `tokoemas/sites/wates/storage` | 755 atau 775 (recursive) |
| `tokoemas/sites/wates1/storage` | 755 atau 775 (recursive) |
| `tokoemas/sites/sentolo/storage` | 755 atau 775 (recursive) |
| `tokoemas/sites/sentolo1/storage` | 755 atau 775 (recursive) |

**Cara set permission:**
1. Klik kanan folder → **Change Permissions**
2. Centang: Owner (Read, Write, Execute), Group (Read, Execute), World (Read, Execute)
3. Centang **Recurse into subdirectories**
4. Klik **Change Permissions**

---

## LANGKAH 6: Setup Subdomain

Di cPanel, buka **Subdomains**:

### Buat 4 Subdomain:

| Subdomain | Document Root |
|-----------|---------------|
| `wates.hartowiyono.my.id` | `/public_html/tokoemas/sites/wates/public` |
| `wates1.hartowiyono.my.id` | `/public_html/tokoemas/sites/wates1/public` |
| `sentolo.hartowiyono.my.id` | `/public_html/tokoemas/sites/sentolo/public` |
| `sentolo1.hartowiyono.my.id` | `/public_html/tokoemas/sites/sentolo1/public` |

**Cara buat subdomain:**
1. Klik **Subdomains** di cPanel
2. Isi subdomain: `wates`
3. Domain: pilih `hartowiyono.my.id`
4. Document Root: `/public_html/tokoemas/sites/wates/public`
5. Klik **Create**
6. Ulangi untuk subdomain lainnya

---

## LANGKAH 7: Setup SSL (HTTPS)

Di cPanel, buka **SSL/TLS Status** atau **AutoSSL**:

1. Centang semua subdomain baru
2. Klik **Run AutoSSL** atau **Issue**
3. Tunggu beberapa menit sampai SSL aktif

---

## LANGKAH 8: Test Akses

Buka browser dan akses:
- https://wates.hartowiyono.my.id
- https://wates1.hartowiyono.my.id
- https://sentolo.hartowiyono.my.id
- https://sentolo1.hartowiyono.my.id

### Jika Error 500:
1. Cek file `.env` sudah ada dan benar
2. Cek permission storage dan bootstrap/cache
3. Cek error di `sites/<store>/storage/logs/`

---

## TROUBLESHOOTING

### Error: "Core Laravel tidak ditemukan"
- Pastikan folder `core/` sudah ada di `/public_html/tokoemas/core/`
- Pastikan `vendor/` sudah ter-upload lengkap

### Error: "No application encryption key"
- Pastikan `APP_KEY` di .env sudah terisi
- APP_KEY harus sama di semua site

### Error: "SQLSTATE Connection refused"
- Cek credentials database di .env
- Pastikan user database punya akses ke database

### Error: "Permission denied"
- Set permission 755/775 untuk folder storage
- Set permission recursive

### Upload File Tidak Muncul
- Pastikan folder `public/storage/` ada di setiap site
- File upload tersimpan di `storage/app/public/<store_code>/`

---

## MAINTENANCE & UPDATE

### Update Code:
1. Lakukan perubahan di lokal
2. Jalankan `prepare-deploy.bat` lagi
3. Upload folder yang berubah via FTP

### Update Database (Migration):
Karena tidak ada akses artisan di server:
1. Export migration ke SQL di lokal:
   ```bash
   php artisan migrate --pretend > migration.sql
   ```
2. Jalankan SQL tersebut via phpMyAdmin

### Clear Cache:
Karena tidak bisa `php artisan cache:clear`:
1. Hapus isi folder `sites/<store>/storage/framework/cache/`
2. Hapus isi folder `sites/<store>/storage/framework/views/`

---

## DAFTAR FILE PENTING

| File | Fungsi |
|------|--------|
| `sites/<store>/.env` | Konfigurasi per-store |
| `sites/<store>/public/index.php` | Entry point per-store |
| `sites/<store>/storage/` | Storage terpisah per-store |
| `core/config/tenants.php` | Mapping domain & store |
| `core/config/database.php` | Konfigurasi multi-database |

---

## KONTAK SUPPORT

Jika ada kendala, hubungi developer dengan informasi:
1. Screenshot error
2. Isi file `.env` (sensor password)
3. Struktur folder di File Manager
