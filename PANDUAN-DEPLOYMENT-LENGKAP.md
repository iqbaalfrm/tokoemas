# 📘 PANDUAN LENGKAP DEPLOYMENT TOKO EMAS
## Dari Laptop Sampai Live di Shared Hosting (Tanpa SSH)

---

# DAFTAR ISI

1. [Persiapan di Laptop](#bagian-a-persiapan-di-laptop)
2. [Upload ke Hosting](#bagian-b-upload-ke-hosting)
3. [Setup Database](#bagian-c-setup-database-di-cpanel)
4. [Setup Subdomain](#bagian-d-setup-subdomain)
5. [Aktivasi & Testing](#bagian-e-aktivasi--testing)
6. [Troubleshooting](#bagian-f-troubleshooting)

---

# INFORMASI TARGET

| Item | Nilai |
|------|-------|
| **Domain Utama** | hartowiyono.my.id |
| **Subdomain 1** | wates.hartowiyono.my.id |
| **Subdomain 2** | wates1.hartowiyono.my.id |
| **Subdomain 3** | sentolo.hartowiyono.my.id |
| **Subdomain 4** | sentolo1.hartowiyono.my.id |
| **Database 1** | tokoemas_wates (untuk wates + wates1) |
| **Database 2** | tokoemas_sentolo (untuk sentolo + sentolo1) |
| **Database 3** | tokoemas_member (shared semua store) |

---

# BAGIAN A: PERSIAPAN DI LAPTOP

> ⚠️ **PENTING**: Semua langkah di bagian ini dilakukan di laptop Anda, BUKAN di server hosting.

## A.1 Pastikan Software Terinstall

Pastikan laptop Anda sudah terinstall:
- ✅ PHP 8.2 atau lebih baru
- ✅ Composer
- ✅ Node.js (untuk build assets)
- ✅ Git (opsional)

**Cara Cek:**
```
Buka Command Prompt atau PowerShell, ketik:
> php -v
> composer -v
> node -v
```

---

## A.2 Buka Folder Project

1. Buka **File Explorer**
2. Masuk ke folder project: `C:\skripsi\tokoemas`
3. Klik kanan di area kosong → **Open in Terminal** atau **Open PowerShell window here**

---

## A.3 Install Dependencies (vendor/)

Di terminal, jalankan:
```powershell
composer install --optimize-autoloader --no-dev
```

**Tunggu sampai selesai.** Ini akan membuat folder `vendor/` dengan semua library Laravel.

**Verifikasi:**
- Buka folder `C:\skripsi\tokoemas\vendor`
- Pastikan ada banyak folder di dalamnya (autoload.php, laravel, filament, dll)

---

## A.4 Build Assets (CSS & JavaScript)

Di terminal yang sama, jalankan:
```powershell
npm install
npm run build
```

**Verifikasi:**
- Buka folder `C:\skripsi\tokoemas\public\build`
- Pastikan ada file-file dengan nama acak (manifest.json, assets/, dll)

---

## A.5 Generate APP_KEY

Di terminal, jalankan:
```powershell
php artisan key:generate --show
```

**CATAT hasilnya!** Contoh output:
```
base64:eSLVTDdozsC5b/N4TNJJdggTPb1Pe1SezC7QCTK86LQ8=
```

> 📝 Simpan key ini, akan dipakai di semua file .env

---

## A.6 Jalankan Script Persiapan

1. Buka **File Explorer**
2. Masuk ke folder: `C:\skripsi\tokoemas`
3. **Klik 2x** file `prepare-deploy.bat`
4. Tunggu sampai muncul tulisan "DEPLOYMENT PACKAGE SIAP!"
5. Tekan **Enter** untuk menutup

**Hasilnya:** Akan terbuat folder baru `tokoemas-deploy` dengan struktur:
```
tokoemas-deploy/
├── core/                    ← Laravel core (shared)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── vendor/              ← Pastikan ada!
│   └── ...
├── sites/
│   ├── wates/
│   │   ├── public/
│   │   ├── storage/
│   │   └── .env.example
│   ├── wates1/
│   ├── sentolo/
│   └── sentolo1/
├── DEPLOYMENT.md
└── PANDUAN-UPLOAD-HOSTING.md
```

---

## A.7 Konfigurasi File .env

### Langkah 7a: Rename File

Di setiap folder site, rename file `.env.example` menjadi `.env`:

| Dari | Menjadi |
|------|---------|
| `sites/wates/.env.example` | `sites/wates/.env` |
| `sites/wates1/.env.example` | `sites/wates1/.env` |
| `sites/sentolo/.env.example` | `sites/sentolo/.env` |
| `sites/sentolo1/.env.example` | `sites/sentolo1/.env` |

**Cara Rename:**
1. Buka folder `tokoemas-deploy\sites\wates`
2. Klik kanan file `.env.example`
3. Pilih **Rename**
4. Hapus `.example` sehingga menjadi `.env`
5. Ulangi untuk folder lainnya

### Langkah 7b: Edit File .env

Buka setiap file `.env` dengan **Notepad** dan edit bagian berikut:

#### Untuk `sites/wates/.env`:
```env
APP_KEY=base64:eSLVTDdozsC5b/N4TNJJdggTPb1Pe1SezC7QCTK86LQ8=

# Ganti dengan nama database yang akan dibuat di hosting
DB_DATABASE=hartowi_wates
DB_USERNAME=hartowi_posuser
DB_PASSWORD=PASSWORD_ANDA_DISINI

DB_WATES_DATABASE=hartowi_wates
DB_WATES_USERNAME=hartowi_posuser
DB_WATES_PASSWORD=PASSWORD_ANDA_DISINI

DB_MEMBER_DATABASE=hartowi_member
DB_MEMBER_USERNAME=hartowi_posuser
DB_MEMBER_PASSWORD=PASSWORD_ANDA_DISINI
```

#### Untuk `sites/wates1/.env`:
```env
APP_KEY=base64:eSLVTDdozsC5b/N4TNJJdggTPb1Pe1SezC7QCTK86LQ8=

# SAMA dengan wates karena 1 group database
DB_DATABASE=hartowi_wates
DB_USERNAME=hartowi_posuser
DB_PASSWORD=PASSWORD_ANDA_DISINI

DB_WATES_DATABASE=hartowi_wates
DB_WATES_USERNAME=hartowi_posuser
DB_WATES_PASSWORD=PASSWORD_ANDA_DISINI

DB_MEMBER_DATABASE=hartowi_member
DB_MEMBER_USERNAME=hartowi_posuser
DB_MEMBER_PASSWORD=PASSWORD_ANDA_DISINI
```

#### Untuk `sites/sentolo/.env`:
```env
APP_KEY=base64:eSLVTDdozsC5b/N4TNJJdggTPb1Pe1SezC7QCTK86LQ8=

# Database sentolo group
DB_DATABASE=hartowi_sentolo
DB_USERNAME=hartowi_posuser
DB_PASSWORD=PASSWORD_ANDA_DISINI

DB_SENTOLO_DATABASE=hartowi_sentolo
DB_SENTOLO_USERNAME=hartowi_posuser
DB_SENTOLO_PASSWORD=PASSWORD_ANDA_DISINI

DB_MEMBER_DATABASE=hartowi_member
DB_MEMBER_USERNAME=hartowi_posuser
DB_MEMBER_PASSWORD=PASSWORD_ANDA_DISINI
```

#### Untuk `sites/sentolo1/.env`:
```env
APP_KEY=base64:eSLVTDdozsC5b/N4TNJJdggTPb1Pe1SezC7QCTK86LQ8=

# SAMA dengan sentolo karena 1 group database
DB_DATABASE=hartowi_sentolo
DB_USERNAME=hartowi_posuser
DB_PASSWORD=PASSWORD_ANDA_DISINI

DB_SENTOLO_DATABASE=hartowi_sentolo
DB_SENTOLO_USERNAME=hartowi_posuser
DB_SENTOLO_PASSWORD=PASSWORD_ANDA_DISINI

DB_MEMBER_DATABASE=hartowi_member
DB_MEMBER_USERNAME=hartowi_posuser
DB_MEMBER_PASSWORD=PASSWORD_ANDA_DISINI
```

> 📝 **Catatan**: 
> - `hartowi_` adalah prefix dari hosting. Sesuaikan dengan username cPanel Anda.
> - PASSWORD_ANDA_DISINI akan diisi setelah membuat database di hosting.

---

## A.8 Checklist Sebelum Upload

Sebelum upload, pastikan:

- [ ] Folder `tokoemas-deploy/core/vendor/` ada dan berisi banyak folder
- [ ] Folder `tokoemas-deploy/core/public/build/` ada
- [ ] File `tokoemas-deploy/sites/wates/.env` sudah ada (bukan .env.example)
- [ ] File `tokoemas-deploy/sites/wates1/.env` sudah ada
- [ ] File `tokoemas-deploy/sites/sentolo/.env` sudah ada
- [ ] File `tokoemas-deploy/sites/sentolo1/.env` sudah ada
- [ ] Semua APP_KEY di .env sudah terisi
- [ ] Folder storage ada di setiap site

---

# BAGIAN B: UPLOAD KE HOSTING

## B.1 Login ke cPanel

1. Buka browser (Chrome/Firefox)
2. Akses: `https://hartowiyono.my.id/cpanel` atau `https://hartowiyono.my.id:2083`
3. Masukkan **Username** dan **Password** cPanel
4. Klik **Log in**

---

## B.2 Buka File Manager

1. Di halaman cPanel, cari **File Manager**
2. Klik untuk membuka
3. Anda akan melihat struktur folder hosting

---

## B.3 Masuk ke public_html

1. Di panel kiri, klik **public_html**
2. Ini adalah folder utama website Anda

---

## B.4 Buat Folder tokoemas

1. Klik tombol **+ Folder** di toolbar atas
2. Isi nama folder: `tokoemas`
3. Klik **Create New Folder**

---

## B.5 Upload File (Cara 1: File Manager - untuk file kecil)

> ⚠️ Untuk folder besar seperti vendor/, disarankan gunakan cara ZIP

### Upload Folder core:

1. Klik 2x folder `tokoemas` untuk masuk
2. Klik 2x lagi untuk membuat folder `core`, lalu masuk
3. Klik **Upload** di toolbar
4. Drag & drop semua isi folder `tokoemas-deploy/core/` ke sini
5. Tunggu sampai selesai

### Upload Folder sites:

1. Kembali ke folder `tokoemas`
2. Buat folder `sites`, lalu masuk
3. Ulangi proses upload untuk setiap folder site

---

## B.5 Upload File (Cara 2: ZIP - DIREKOMENDASIKAN)

### Di Laptop:

1. Buka folder `tokoemas-deploy`
2. Pilih folder `core` → Klik kanan → **Send to** → **Compressed (zipped) folder**
3. Akan terbuat file `core.zip`
4. Ulangi untuk folder `sites` → hasil `sites.zip`

### Di File Manager Hosting:

1. Masuk ke folder `public_html/tokoemas`
2. Klik **Upload**
3. Upload file `core.zip`
4. Setelah selesai, klik kanan file `core.zip` → **Extract**
5. Pilih extract ke folder saat ini
6. Hapus file `core.zip` setelah extract selesai
7. Ulangi untuk `sites.zip`

---

## B.6 Verifikasi Struktur

Setelah upload, struktur harus seperti ini:
```
public_html/
└── tokoemas/
    ├── core/
    │   ├── app/
    │   ├── bootstrap/
    │   ├── config/
    │   ├── vendor/        ← WAJIB ADA
    │   └── ...
    └── sites/
        ├── wates/
        │   ├── public/
        │   ├── storage/
        │   └── .env       ← WAJIB ADA
        ├── wates1/
        ├── sentolo/
        └── sentolo1/
```

---

## B.7 Set Permission Folder

### Langkah-langkah:

1. Masuk ke folder `public_html/tokoemas/sites/wates`
2. Klik kanan folder `storage`
3. Pilih **Change Permissions**
4. Set permission: **755**
5. ✅ Centang **Recurse into subdirectories**
6. Klik **Change Permissions**

**Ulangi untuk:**
- `sites/wates/storage`
- `sites/wates1/storage`
- `sites/sentolo/storage`
- `sites/sentolo1/storage`
- `core/bootstrap/cache`

---

# BAGIAN C: SETUP DATABASE DI CPANEL

## C.1 Buka MySQL Databases

1. Di halaman utama cPanel, cari **MySQL Databases**
2. Klik untuk membuka

---

## C.2 Buat Database

### Database 1: Wates Group
1. Di bagian **Create New Database**
2. Ketik nama: `wates` (akan menjadi `hartowi_wates`)
3. Klik **Create Database**
4. Klik **Go Back**

### Database 2: Sentolo Group
1. Ketik nama: `sentolo`
2. Klik **Create Database**
3. Klik **Go Back**

### Database 3: Member (Shared)
1. Ketik nama: `member`
2. Klik **Create Database**
3. Klik **Go Back**

---

## C.3 Buat User Database

1. Scroll ke bagian **MySQL Users**
2. Di **Add New User**:
   - Username: `posuser`
   - Password: **Buat password kuat** (catat password ini!)
   - Konfirmasi password
3. Klik **Create User**

---

## C.4 Assign User ke Database

1. Scroll ke bagian **Add User To Database**
2. Pilih User: `hartowi_posuser`
3. Pilih Database: `hartowi_wates`
4. Klik **Add**
5. Di halaman privileges, centang **ALL PRIVILEGES**
6. Klik **Make Changes**

**Ulangi untuk:**
- `hartowi_posuser` → `hartowi_sentolo` (ALL PRIVILEGES)
- `hartowi_posuser` → `hartowi_member` (ALL PRIVILEGES)

---

## C.5 Update Password di File .env

Sekarang update password database di file .env:

1. Di File Manager, buka `public_html/tokoemas/sites/wates/.env`
2. Klik kanan → **Edit**
3. Ganti `PASSWORD_ANDA_DISINI` dengan password yang dibuat tadi
4. Klik **Save Changes**

**Ulangi untuk semua file .env di sites lainnya.**

---

## C.6 Import Database

### Export dari Lokal:

1. Buka **phpMyAdmin** di XAMPP lokal
2. Pilih database yang ingin diexport
3. Klik **Export** → **Go**
4. Simpan file .sql

### Import ke Hosting:

1. Di cPanel, klik **phpMyAdmin**
2. Di panel kiri, pilih database `hartowi_wates`
3. Klik tab **Import**
4. Klik **Choose File** → pilih file .sql dari lokal
5. Klik **Go**

**Ulangi untuk database lainnya.**

---

# BAGIAN D: SETUP SUBDOMAIN

## D.1 Buka Subdomains

1. Di halaman cPanel, cari **Subdomains**
2. Klik untuk membuka

---

## D.2 Buat Subdomain Wates

1. **Subdomain**: ketik `wates`
2. **Domain**: pilih `hartowiyono.my.id`
3. **Document Root**: HAPUS isi default, ganti dengan:
   ```
   public_html/tokoemas/sites/wates/public
   ```
4. Klik **Create**

---

## D.3 Buat Subdomain Lainnya

Ulangi langkah D.2 untuk:

| Subdomain | Document Root |
|-----------|---------------|
| `wates1` | `public_html/tokoemas/sites/wates1/public` |
| `sentolo` | `public_html/tokoemas/sites/sentolo/public` |
| `sentolo1` | `public_html/tokoemas/sites/sentolo1/public` |

---

## D.4 Verifikasi Document Root

1. Setelah membuat semua subdomain, lihat daftar subdomain
2. Pastikan **Document Root** sudah benar:

| Subdomain | Document Root yang Benar |
|-----------|--------------------------|
| wates.hartowiyono.my.id | /public_html/tokoemas/sites/wates/public |
| wates1.hartowiyono.my.id | /public_html/tokoemas/sites/wates1/public |
| sentolo.hartowiyono.my.id | /public_html/tokoemas/sites/sentolo/public |
| sentolo1.hartowiyono.my.id | /public_html/tokoemas/sites/sentolo1/public |

---

## D.5 Setup SSL (HTTPS)

1. Di cPanel, cari **SSL/TLS Status** atau **AutoSSL**
2. Klik **Run AutoSSL** atau **Manage AutoSSL**
3. Pastikan semua subdomain baru tercentang
4. Klik **Run AutoSSL**
5. Tunggu beberapa menit (bisa sampai 1 jam)

---

# BAGIAN E: AKTIVASI & TESTING

## E.1 Inisialisasi Database

Buka browser dan akses URL berikut satu per satu:

```
https://wates.hartowiyono.my.id/update-server
https://wates1.hartowiyono.my.id/update-server
https://sentolo.hartowiyono.my.id/update-server
https://sentolo1.hartowiyono.my.id/update-server
```

**Hasil yang diharapkan:**
```
SUKSES!
Database Updated.
Cache Cleared.
Storage Directories Created for wates.
```

> ⚠️ Jika error, lanjut ke Bagian F: Troubleshooting

---

## E.2 Test Halaman Utama

Buka browser dan akses:
1. https://wates.hartowiyono.my.id → Harus tampil halaman login
2. https://wates1.hartowiyono.my.id → Harus tampil halaman login
3. https://sentolo.hartowiyono.my.id → Harus tampil halaman login
4. https://sentolo1.hartowiyono.my.id → Harus tampil halaman login

---

## E.3 Test Halaman Error 404

Buka URL yang tidak ada:
```
https://wates.hartowiyono.my.id/halaman-tidak-ada
```

**Hasil yang diharapkan:**
- Halaman error dengan design bagus
- Ada tulisan "Halaman Tidak Ditemukan"
- Ada tombol "Kembali ke Beranda"
- Di footer ada nama store dan STORE_CODE

---

## E.4 Cek Log Error

1. Buka **File Manager** di cPanel
2. Masuk ke: `public_html/tokoemas/sites/wates/storage/logs/`
3. Cari file dengan nama seperti: `wates-laravel-2026-01-06.log`
4. Klik kanan → **View** atau **Edit** untuk melihat isi

---

## E.5 Test Isolasi Storage

1. Login ke **wates.hartowiyono.my.id**
2. Upload foto produk
3. Cek di File Manager: `sites/wates/storage/app/public/wates/products/`
4. Pastikan foto TIDAK muncul di `sites/sentolo/storage/`

---

## E.6 Test Isolasi Database

1. Buat transaksi di **wates.hartowiyono.my.id**
2. Login ke **sentolo.hartowiyono.my.id**
3. Pastikan transaksi dari wates TIDAK muncul di sentolo

---

# BAGIAN F: TROUBLESHOOTING

## F.1 Layar Putih / Error 500

**Kemungkinan Penyebab:**

| Masalah | Solusi |
|---------|--------|
| File .env tidak ada | Cek folder site, pastikan ada file `.env` (bukan `.env.example`) |
| APP_KEY kosong | Edit .env, isi APP_KEY |
| Permission salah | Set permission folder storage ke 755 (recursive) |
| Vendor tidak ada | Upload ulang folder core/vendor |
| Path salah di index.php | Cek sites/wates/public/index.php |

**Cara Cek Error:**
1. Edit file `.env` di site
2. Ubah `APP_DEBUG=false` menjadi `APP_DEBUG=true`
3. Refresh halaman untuk melihat error detail
4. **PENTING**: Kembalikan ke `APP_DEBUG=false` setelah selesai debugging

---

## F.2 Tidak Bisa Connect Database

**Kemungkinan Penyebab:**

| Masalah | Solusi |
|---------|--------|
| Nama database salah | Cek nama database di cPanel, cocokkan dengan .env |
| Username salah | Cek username di MySQL Users |
| Password salah | Update password di .env |
| User belum di-assign | Assign user ke database di cPanel |
| Localhost salah | Coba ganti `DB_HOST=localhost` dengan `DB_HOST=127.0.0.1` |

---

## F.3 Upload File Gagal

**Kemungkinan Penyebab:**

| Masalah | Solusi |
|---------|--------|
| Folder storage tidak writable | Set permission 755 untuk folder storage |
| Disk penuh | Cek Disk Usage di cPanel |
| File terlalu besar | Cek limit upload di hosting |

---

## F.4 Domain Menampilkan Konten Salah

**Kemungkinan Penyebab:**

| Masalah | Solusi |
|---------|--------|
| Document root salah | Edit subdomain, perbaiki document root |
| Cache browser | Clear cache browser (Ctrl+Shift+R) |
| .env STORE_CODE salah | Edit .env, pastikan STORE_CODE sesuai |

**Cara Cek Document Root:**
1. Buka **Subdomains** di cPanel
2. Lihat kolom **Document Root**
3. Pastikan path mengarah ke folder `public` yang benar

---

## F.5 SSL/HTTPS Tidak Aktif

**Solusi:**
1. Tunggu 1-2 jam setelah membuat subdomain
2. Jalankan **AutoSSL** ulang di cPanel
3. Jika masih error, hubungi support hosting

---

## F.6 Halaman Error Tidak Tampil Custom

**Kemungkinan Penyebab:**

| Masalah | Solusi |
|---------|--------|
| View error tidak ada | Pastikan folder `core/resources/views/errors/` sudah ada |
| Cache view | Hapus isi folder `sites/wates/storage/framework/views/` |

---

# CHECKLIST FINAL ✅

Setelah semua selesai, pastikan:

- [ ] https://wates.hartowiyono.my.id bisa diakses
- [ ] https://wates1.hartowiyono.my.id bisa diakses
- [ ] https://sentolo.hartowiyono.my.id bisa diakses
- [ ] https://sentolo1.hartowiyono.my.id bisa diakses
- [ ] Bisa login di setiap subdomain
- [ ] Halaman error 404 tampil custom
- [ ] Upload file di wates tidak muncul di sentolo
- [ ] Transaksi wates tidak muncul di sentolo
- [ ] Log error terpisah per store

---

# CATATAN MAINTENANCE

## Update Code

Jika ada perubahan code:
1. Lakukan perubahan di laptop
2. Jalankan `prepare-deploy.bat` lagi
3. Upload folder yang berubah via File Manager (replace)

## Update Database

Jika ada perubahan struktur database:
1. Export SQL dari lokal
2. Jalankan SQL via phpMyAdmin di hosting

## Backup

Lakukan backup rutin:
1. Database via **Backup Wizard** di cPanel
2. Files via File Manager (download folder penting)

---

**Selamat! Sistem Toko Emas Anda sudah live! 🎉**

---
*Dokumen ini dibuat untuk memandu deployment tanpa SSH/terminal.*
*Terakhir diupdate: 6 Januari 2026*
