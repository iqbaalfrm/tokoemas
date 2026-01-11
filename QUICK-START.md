# 🚀 QUICK START - 10 Langkah Deploy Toko Emas

> Ringkasan cepat untuk deployment. Untuk panduan lengkap, baca `PANDUAN-DEPLOYMENT-LENGKAP.md`

---

## DI LAPTOP (5 Langkah)

### 1️⃣ Install Dependencies
```powershell
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### 2️⃣ Generate APP_KEY
```powershell
php artisan key:generate --show
```
📝 Catat hasilnya: `base64:xxxxxxxxxxxxx`

### 3️⃣ Jalankan Script Deploy
```
Klik 2x: prepare-deploy.bat
```

### 4️⃣ Rename semua .env.example → .env
```
tokoemas-deploy/sites/wates/.env.example → .env
tokoemas-deploy/sites/wates1/.env.example → .env
tokoemas-deploy/sites/sentolo/.env.example → .env
tokoemas-deploy/sites/sentolo1/.env.example → .env
```

### 5️⃣ Edit setiap .env
- Isi `APP_KEY` dengan key dari langkah 2
- Isi credentials database (nanti setelah buat di hosting)

---

## DI HOSTING (5 Langkah)

### 6️⃣ Buat 3 Database di cPanel > MySQL Databases
- `hartowi_wates`
- `hartowi_sentolo`
- `hartowi_member`

### 7️⃣ Upload folder tokoemas-deploy ke public_html/tokoemas
- Gunakan File Manager atau FTP
- Bisa ZIP dulu untuk lebih cepat

### 8️⃣ Set Permission 755 untuk:
- `sites/wates/storage` (recursive)
- `sites/wates1/storage` (recursive)
- `sites/sentolo/storage` (recursive)
- `sites/sentolo1/storage` (recursive)
- `core/bootstrap/cache`

### 9️⃣ Buat 4 Subdomain dengan Document Root:
| Subdomain | Document Root |
|-----------|---------------|
| wates | public_html/tokoemas/sites/wates/public |
| wates1 | public_html/tokoemas/sites/wates1/public |
| sentolo | public_html/tokoemas/sites/sentolo/public |
| sentolo1 | public_html/tokoemas/sites/sentolo1/public |

### 🔟 Akses URL inisialisasi:
```
https://wates.hartowiyono.my.id/update-server
https://wates1.hartowiyono.my.id/update-server
https://sentolo.hartowiyono.my.id/update-server
https://sentolo1.hartowiyono.my.id/update-server
```

---

## ✅ DONE!

Akses aplikasi:
- https://wates.hartowiyono.my.id
- https://wates1.hartowiyono.my.id
- https://sentolo.hartowiyono.my.id
- https://sentolo1.hartowiyono.my.id

---

**Ada masalah?** Baca `PANDUAN-DEPLOYMENT-LENGKAP.md` bagian Troubleshooting.
