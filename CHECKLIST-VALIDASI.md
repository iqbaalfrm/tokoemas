# CHECKLIST VALIDASI SEBELUM UPLOAD

## A. Persiapan Lokal

### Dependencies
- [ ] `composer install` sudah dijalankan (folder vendor/ ada)
- [ ] `npm run build` sudah dijalankan (folder public/build/ ada)

### Environment Files
- [ ] `sites/wates/.env.example` sudah ada
- [ ] `sites/wates1/.env.example` sudah ada
- [ ] `sites/sentolo/.env.example` sudah ada
- [ ] `sites/sentolo1/.env.example` sudah ada

### APP_KEY
- [ ] APP_KEY sudah di-generate: `base64:eSLVTDdozsC5b/N4TNJJdggTPb1Pe1SezC7QCTK86LQ8=`
- [ ] APP_KEY sama untuk semua file .env.example

### Entry Point
- [ ] `sites/wates/public/index.php` sudah ada
- [ ] `sites/wates1/public/index.php` sudah ada
- [ ] `sites/sentolo/public/index.php` sudah ada
- [ ] `sites/sentolo1/public/index.php` sudah ada

### Storage Structure
- [ ] `sites/wates/storage/` sudah ada
- [ ] `sites/wates/storage/app/public/` sudah ada
- [ ] `sites/wates/storage/framework/cache/` sudah ada
- [ ] `sites/wates/storage/framework/sessions/` sudah ada
- [ ] `sites/wates/storage/framework/views/` sudah ada
- [ ] `sites/wates/storage/logs/` sudah ada
- [ ] (Ulangi untuk wates1, sentolo, sentolo1)

---

## B. Sebelum Upload

### Rename .env.example → .env
- [ ] `sites/wates/.env.example` → `sites/wates/.env`
- [ ] `sites/wates1/.env.example` → `sites/wates1/.env`
- [ ] `sites/sentolo/.env.example` → `sites/sentolo/.env`
- [ ] `sites/sentolo1/.env.example` → `sites/sentolo1/.env`

### Edit Credentials di .env
- [ ] Database name sudah disesuaikan dengan hosting
- [ ] Database username sudah disesuaikan
- [ ] Database password sudah diisi
- [ ] APP_URL sudah benar untuk setiap domain

---

## C. Di Hosting

### Database
- [ ] Database `hartowi_wates` sudah dibuat
- [ ] Database `hartowi_sentolo` sudah dibuat
- [ ] Database `hartowi_member` sudah dibuat
- [ ] User database sudah dibuat
- [ ] User sudah di-assign ke semua database dengan ALL PRIVILEGES

### Import Database
- [ ] Schema sudah diimport ke `hartowi_wates`
- [ ] Schema sudah diimport ke `hartowi_sentolo`
- [ ] Schema sudah diimport ke `hartowi_member`

### Upload Files
- [ ] Folder `tokoemas-deploy/` sudah diupload ke `/public_html/tokoemas/`
- [ ] Struktur folder benar (core/ dan sites/ terlihat)
- [ ] vendor/ ter-upload lengkap

### Permissions
- [ ] `core/bootstrap/cache/` → 755
- [ ] `sites/wates/storage/` → 755 (recursive)
- [ ] `sites/wates1/storage/` → 755 (recursive)
- [ ] `sites/sentolo/storage/` → 755 (recursive)
- [ ] `sites/sentolo1/storage/` → 755 (recursive)

### Subdomain
- [ ] `wates.hartowiyono.my.id` → `/public_html/tokoemas/sites/wates/public`
- [ ] `wates1.hartowiyono.my.id` → `/public_html/tokoemas/sites/wates1/public`
- [ ] `sentolo.hartowiyono.my.id` → `/public_html/tokoemas/sites/sentolo/public`
- [ ] `sentolo1.hartowiyono.my.id` → `/public_html/tokoemas/sites/sentolo1/public`

### SSL
- [ ] SSL aktif untuk wates.hartowiyono.my.id
- [ ] SSL aktif untuk wates1.hartowiyono.my.id
- [ ] SSL aktif untuk sentolo.hartowiyono.my.id
- [ ] SSL aktif untuk sentolo1.hartowiyono.my.id

---

## D. Testing

### Basic Access
- [ ] https://wates.hartowiyono.my.id menampilkan halaman login
- [ ] https://wates1.hartowiyono.my.id menampilkan halaman login
- [ ] https://sentolo.hartowiyono.my.id menampilkan halaman login
- [ ] https://sentolo1.hartowiyono.my.id menampilkan halaman login

### Initial Setup
- [ ] Akses https://wates.hartowiyono.my.id/update-server → SUKSES
- [ ] Akses https://wates1.hartowiyono.my.id/update-server → SUKSES
- [ ] Akses https://sentolo.hartowiyono.my.id/update-server → SUKSES
- [ ] Akses https://sentolo1.hartowiyono.my.id/update-server → SUKSES

### Login Test
- [ ] Bisa login di wates.hartowiyono.my.id
- [ ] Bisa login di sentolo.hartowiyono.my.id

### Data Isolation Test
- [ ] Buat transaksi di wates → tidak muncul di sentolo
- [ ] Upload file di wates → tidak muncul di sentolo

### Member Shared Test
- [ ] Buat member di wates
- [ ] Member terlihat di wates1 (karena shared DB)
- [ ] Member tidak terlihat di sentolo (DB berbeda)

---

## E. Post-Deployment

### Buat User Admin
Akses phpMyAdmin dan insert user ke tabel `users`:
```sql
INSERT INTO users (name, email, password, created_at, updated_at)
VALUES ('Admin', 'admin@hartowiyono.my.id', '$2y$10$...', NOW(), NOW());
```
*Note: Generate password hash dengan online tool atau dari lokal*

### Assign Role
```sql
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (1, 'App\\Models\\User', 1);
```

---

## Catatan
- Tanggal deployment: _______________
- Dilakukan oleh: _______________
- Catatan khusus: _______________
