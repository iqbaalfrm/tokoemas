# Panduan Deployment Multi-Domain Toko Emas

## Struktur Folder

```
tokoemas/                       # Folder utama di hosting
├── app/                        # Core Laravel (shared)
├── bootstrap/
├── config/
├── database/
├── lang/
├── public/                     # Public assets (bisa di-symlink)
├── resources/
├── routes/
├── storage/                    # Storage default (untuk artisan commands)
├── vendor/
├── sites/
│   ├── wates/
│   │   ├── public/             # Document root untuk wates.hartowiyono.my.id
│   │   │   └── index.php
│   │   ├── storage/            # Storage khusus wates
│   │   │   ├── app/public/
│   │   │   ├── framework/
│   │   │   └── logs/
│   │   └── .env                # Environment khusus wates
│   ├── wates1/
│   ├── sentolo/
│   └── sentolo1/
└── ...
```

## Setup di Hosting

### 1. Upload Files

Upload seluruh folder `tokoemas` ke hosting. Pastikan struktur tetap utuh.

### 2. Setup Database

Buat 3 database:
- `tokoemas_wates` - untuk store Wates & Wates1
- `tokoemas_sentolo` - untuk store Sentolo & Sentolo1  
- `tokoemas_member` - untuk data member (shared)

### 3. Konfigurasi .env per Site

Copy `.env.example` ke `.env` di setiap folder site dan sesuaikan:

```bash
# Di folder sites/wates/
cp .env.example .env
# Edit .env dan isi APP_KEY, DB credentials, dll
```

Generate APP_KEY (bisa sama untuk semua site karena 1 codebase):
```bash
php artisan key:generate --show
```

Copy key tersebut ke semua file .env di sites/

### 4. Setup Document Root Subdomain

Di cPanel atau control panel hosting, set document root:

| Subdomain | Document Root |
|-----------|---------------|
| `wates.hartowiyono.my.id` | `/public_html/tokoemas/sites/wates/public` |
| `wates1.hartowiyono.my.id` | `/public_html/tokoemas/sites/wates1/public` |
| `sentolo.hartowiyono.my.id` | `/public_html/tokoemas/sites/sentolo/public` |
| `sentolo1.hartowiyono.my.id` | `/public_html/tokoemas/sites/sentolo1/public` |

### 5. Setup Storage Symlink

Untuk setiap site, buat symlink dari public/storage ke storage/app/public:

```bash
# SSH ke server, lalu:
cd /path/to/tokoemas/sites/wates/public
ln -s ../storage/app/public storage

cd /path/to/tokoemas/sites/wates1/public
ln -s ../storage/app/public storage

# Ulangi untuk sentolo dan sentolo1
```

### 6. Copy Assets ke Setiap Site

Salin folder assets dari public/ core ke public/ setiap site:

```bash
cp -r /path/to/tokoemas/public/css sites/wates/public/
cp -r /path/to/tokoemas/public/js sites/wates/public/
cp -r /path/to/tokoemas/public/images sites/wates/public/
# atau buat symlink
```

Alternatif: gunakan symbolic links untuk assets:
```bash
cd sites/wates/public
ln -s ../../../public/css css
ln -s ../../../public/js js
ln -s ../../../public/images images
ln -s ../../../public/build build
```

### 7. Run Migrations

```bash
# Migrasi database Wates
php artisan migrate --database=wates

# Migrasi database Sentolo  
php artisan migrate --database=sentolo

# Migrasi database Member
php artisan migrate --database=member
```

### 8. Set Permissions

```bash
chmod -R 775 sites/wates/storage
chmod -R 775 sites/wates1/storage
chmod -R 775 sites/sentolo/storage
chmod -R 775 sites/sentolo1/storage
```

## Setup SSL (Let's Encrypt)

Di cPanel, pergi ke SSL/TLS dan generate certificate untuk setiap subdomain:
- wates.hartowiyono.my.id
- wates1.hartowiyono.my.id
- sentolo.hartowiyono.my.id
- sentolo1.hartowiyono.my.id

## Menambah Store Baru

Untuk menambah store baru (misalnya `kota2`):

### 1. Buat Folder Site

```bash
mkdir -p sites/kota2/public
mkdir -p sites/kota2/storage/app/public
mkdir -p sites/kota2/storage/framework/cache
mkdir -p sites/kota2/storage/framework/sessions
mkdir -p sites/kota2/storage/framework/views
mkdir -p sites/kota2/storage/logs
```

### 2. Copy index.php

```bash
cp sites/wates/public/index.php sites/kota2/public/
```

### 3. Buat .env

```bash
cp sites/wates/.env.example sites/kota2/.env
# Edit dan sesuaikan STORE_CODE, APP_URL, dll
```

### 4. Update config/tenants.php

Tambahkan mapping domain dan store definition:

```php
'domains' => [
    // ...existing...
    'kota2.hartowiyono.my.id' => 'kota2',
],

'stores' => [
    // ...existing...
    'kota2' => [
        'name' => 'Toko Emas Kota 2',
        'group' => 'kota_group',  // atau group existing
        'db_connection' => 'kota', // atau connection existing
    ],
],
```

### 5. Tambah DB Connection (jika perlu group baru)

Di `config/database.php`, tambahkan connection baru jika ini group baru.

### 6. Setup Subdomain di Hosting

Set document root subdomain ke folder public site yang baru.

## Troubleshooting

### Error "Class not found"

Pastikan autoload sudah di-generate:
```bash
composer dump-autoload
```

### Session/Cache Conflict

Pastikan STORE_CODE di .env setiap site berbeda.

### Storage Link Error

Pastikan symlink storage sudah dibuat dengan benar di folder public setiap site.

### Database Connection Error

Cek credentials di .env site yang bersangkutan.

## Testing Lokal

Untuk testing lokal, tambahkan entries di file hosts:

```
127.0.0.1 wates.localhost
127.0.0.1 wates1.localhost
127.0.0.1 sentolo.localhost
127.0.0.1 sentolo1.localhost
```

Lalu jalankan server dengan:
```bash
php artisan serve --host=wates.localhost --port=8000
```
