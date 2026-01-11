# 🚀 PROMPT SIAP EKSEKUSI - POS Toko Emas

## 📌 CARA PAKAI:
1. Buat folder project baru (misal: C:\skripsi\tokoemas-v2)
2. Buka folder tersebut di VS Code
3. Buka chat AI (Gemini/Claude/ChatGPT)
4. Copy-paste prompt per fase di bawah
5. Tunggu selesai, lanjut ke fase berikutnya

---

## 📁 DAFTAR FILE YANG HARUS DIBUAT (SAMA PERSIS)

### Filament Resources (15 file):
```
app/Filament/Resources/
├── BuybackResource.php           ← Buyback emas (13.8KB)
├── CashFlowResource.php          ← Arus kas (11.7KB)
├── CategoryResource.php          ← Kategori produk (4KB)
├── CucianResource.php            ← Cucian/reparasi (6.5KB)
├── GoldPriceResource.php         ← Harga emas harian (3KB)
├── GoldPurityResource.php        ← Kadar emas (2.3KB)
├── InventoryResource.php         ← Log inventori (11KB)
├── MemberResource.php            ← Pelanggan (3.8KB)
├── PaymentMethodResource.php     ← Metode bayar (4.2KB)
├── ProductResource.php           ← Produk (20KB) ⭐ PALING PENTING
├── ReportResource.php            ← Laporan (5.5KB)
├── RiwayatNotifikasiResource.php ← Riwayat notif (3.9KB)
├── SettingResource.php           ← Pengaturan toko (5.2KB)
├── TransactionResource.php       ← Transaksi (20KB) ⭐ PALING PENTING
└── UserResource.php              ← User management (3.5KB)
```

### Filament Pages (4 file):
```
app/Filament/Pages/
├── Dashboard.php         ← Dashboard utama
├── DaftarApproval.php    ← Halaman approval (18.6KB) ⭐
├── LaporanProduk.php     ← Laporan produk
└── PosPage.php           ← Wrapper untuk POS Livewire
```

### Filament Widgets (6 file):
```
app/Filament/Widgets/
├── CashFlowRadarChart.php      ← Chart arus kas
├── PaymentMethodPieChart.php   ← Pie chart payment
├── ProductAlert.php            ← Stok menipis
├── ProductFavorite.php         ← Produk terlaris
├── StatsOverview.php           ← Stats hari ini (4.5KB) ⭐
└── TotalStatsOverview.php      ← Stats bulan ini
```

### Livewire Components:
```
app/Livewire/
└── Pos.php                     ← Logic POS kasir ⭐

resources/views/livewire/
└── pos.blade.php               ← UI POS kasir ⭐
```

### Observers:
```
app/Observers/
├── TransactionObserver.php     ← Auto stok, cashflow, inventory
├── BuybackObserver.php         ← Auto approve logic
└── ProductObserver.php         ← Alert stok menipis
```

### Notifications:
```
app/Notifications/
├── TransactionDeletedNotification.php
├── BuybackNeedApprovalNotification.php
├── BuybackApprovedNotification.php
├── BuybackRejectedNotification.php
└── StokMenipisNotification.php
```

### PDF Invoice:
```
resources/views/pdf/
└── invoice-a5.blade.php        ← Template invoice

app/Http/Controllers/
└── InvoiceController.php       ← Generate PDF

app/Helpers/
└── helpers.php                 ← terbilang() function
```

---

## ⚡ FASE 1: SETUP PROJECT

```
Buatkan project Laravel 11 baru di folder ini dengan nama 'tokoemas'.

Langkah yang harus dilakukan:
1. Jalankan: composer create-project laravel/laravel . "11.*"
2. Install packages:
   - composer require filament/filament:"^3.2"
   - composer require bezhansalleh/filament-shield:"^3.2"
   - composer require spatie/laravel-permission:"^6.0"
   - composer require maatwebsite/excel:"^3.1"
   - composer require barryvdh/laravel-dompdf:"^2.0"
   - composer require picqer/php-barcode-generator:"^2.0"
   
3. Setup Filament:
   - php artisan filament:install --panels
   - Gunakan ID panel: "admin"
   
4. Setup Tailwind dengan font Poppins:
   - Tambahkan Google Fonts Poppins di layout
   - Konfigurasi dark mode sebagai default

5. Buat file .env dengan konfigurasi database MySQL:
   - DB_DATABASE=tokoemas
   - DB_USERNAME=root
   - DB_PASSWORD=

Pastikan semua terinstall dengan benar dan `php artisan serve` berjalan tanpa error.
```

---

## ⚡ FASE 2: DATABASE MIGRATION

```
Buatkan semua migration untuk aplikasi POS Toko Emas dengan struktur berikut:

1. categories
   - id, name, soft_deletes, timestamps

2. sub_categories
   - id, category_id (FK), name, description, soft_deletes, timestamps

3. gold_purities (Kadar Emas)
   - id, name, purity_percentage (decimal 5,2), timestamps

4. products
   - id, category_id (FK nullable), sub_category_id (FK nullable), gold_purity_id (FK nullable)
   - name, sku (unique nullable), barcode (unique nullable)
   - weight_gram (decimal 10,4), cost_price (decimal 15,2), selling_price (decimal 15,2)
   - stock (integer default 0), image (nullable), is_active (boolean default true)
   - soft_deletes, timestamps

5. payment_methods
   - id, name, image (nullable), is_cash (boolean default false)
   - soft_deletes, timestamps

6. members (Pelanggan)
   - id, nama, no_hp (nullable), alamat (text nullable), email (nullable unique)
   - total_pembelian (decimal 15,2 default 0), total_gram (decimal 10,4 default 0)
   - timestamps

7. transactions
   - id, transaction_number (unique), member_id (FK nullable), user_id (FK)
   - name (nullable), email (nullable), phone (nullable), address (text nullable)
   - payment_method_id (FK nullable), total (decimal 15,2), cash_received (decimal 15,2)
   - change (decimal 15,2), notes (text nullable)
   - approved_by (FK nullable), approved_at (datetime nullable)
   - soft_deletes, timestamps

8. transaction_items
   - id, transaction_id (FK), product_id (FK)
   - quantity (integer), price (decimal 15,2), cost_price (decimal 15,2)
   - total_profit (decimal 15,2), weight_gram (decimal 10,4)
   - timestamps

9. buybacks (Beli Kembali Emas)
   - id, user_id (FK), tipe (enum: pelanggan, pembelian_stok)
   - tanggal (date), customer_name, customer_phone (nullable)
   - berat_total (decimal 10,4), total_bayar (decimal 15,2)
   - catatan (text nullable)
   - approved_by (FK nullable), approved_at (datetime nullable)
   - status (enum: pending, approved, rejected default pending)
   - timestamps

10. buyback_items
    - id, buyback_id (FK), nama_produk, kadar (nullable)
    - berat (decimal 10,4), harga_per_gram (decimal 15,2), subtotal (decimal 15,2)
    - timestamps

11. cash_flows (Arus Kas)
    - id, user_id (FK), type (enum: income, expense)
    - amount (decimal 15,2), description (text)
    - transaction_id (FK nullable), buyback_id (FK nullable)
    - timestamps

12. inventories (Log Inventori)
    - id, reference_number, type (enum: in, out, adjustment)
    - source (nullable), notes (text nullable)
    - timestamps

13. inventory_items
    - id, inventory_id (FK), product_id (FK)
    - quantity (integer), weight_gram (decimal 10,4)
    - timestamps

14. gold_prices (Harga Emas Harian)
    - id, tanggal (date unique), harga_beli (decimal 15,2), harga_jual (decimal 15,2)
    - catatan (text nullable), timestamps

15. settings (Pengaturan Toko)
    - id, logo (nullable), name, phone (nullable), address (text nullable)
    - print_via_bluetooth (boolean default false), name_printer_local (nullable)
    - timestamps

16. approvals (Log Approval)
    - id, approvable_type, approvable_id, user_id (FK)
    - action (enum: approve, reject), notes (text nullable)
    - timestamps

17. cucians (Cucian/Reparasi)
    - id, user_id (FK), customer_name, customer_phone (nullable)
    - tanggal_masuk (date), tanggal_selesai (date nullable)
    - status (enum: pending, selesai, diambil default pending)
    - catatan (text nullable), timestamps

18. cucian_items
    - id, cucian_id (FK), nama_barang, deskripsi (text nullable)
    - estimasi_harga (decimal 15,2 nullable)
    - timestamps

Jalankan juga migration bawaan Spatie Permission dan buat tabel notifications.
Jalankan: php artisan migrate

Semua label dan comment dalam Bahasa Indonesia.
```

---

## ⚡ FASE 3: ELOQUENT MODELS

```
Buatkan semua Model Eloquent dengan relationships lengkap:

1. Category - hasMany SubCategory, hasMany Product
2. SubCategory - belongsTo Category, hasMany Product
3. GoldPurity - hasMany Product
4. Product - belongsTo Category, SubCategory, GoldPurity; hasMany TransactionItem, InventoryItem
5. PaymentMethod - hasMany Transaction
6. Member - hasMany Transaction
7. Transaction - belongsTo User, PaymentMethod, Member; hasMany TransactionItem; morphMany Approval
8. TransactionItem - belongsTo Transaction, Product
9. Buyback - belongsTo User; hasMany BuybackItem; morphMany Approval
10. BuybackItem - belongsTo Buyback
11. CashFlow - belongsTo User, Transaction, Buyback
12. Inventory - hasMany InventoryItem
13. InventoryItem - belongsTo Inventory, Product
14. GoldPrice - standalone
15. Setting - standalone (singleton pattern)
16. Approval - morphTo approvable, belongsTo User
17. Cucian - belongsTo User, hasMany CucianItem
18. CucianItem - belongsTo Cucian
19. User - hasMany Transaction, Buyback, CashFlow; gunakan HasRoles trait dari Spatie

Tambahkan:
- Scope untuk filter aktif/non-aktif
- Accessor untuk format currency (Rp)
- Mutator untuk sanitize input
- SoftDeletes pada model yang diperlukan
- Label/attribute dalam Bahasa Indonesia

Pastikan User model sudah implement HasRoles dari Spatie Permission.
```

---

## ⚡ FASE 4: FILAMENT RESOURCES (Part 1 - Master Data)

```
Buatkan Filament Resource untuk Master Data dengan fitur lengkap:

1. CategoryResource
   - Form: name (required)
   - Table: name, products_count, created_at
   - Bulk delete, export Excel
   
2. SubCategoryResource  
   - Form: category_id (select), name, description
   - Table: name, category.name, created_at
   
3. GoldPurityResource (Kadar Emas)
   - Form: name, purity_percentage
   - Table: name, purity_percentage (format %), products_count
   
4. PaymentMethodResource
   - Form: name, image (upload), is_cash (toggle)
   - Table: image (circular), name, is_cash (badge), transactions_count
   
5. MemberResource (Pelanggan)
   - Form: nama, no_hp, alamat, email
   - Table: nama, no_hp, total_pembelian (currency), total_gram
   - Filter: has transactions
   - Search: nama, no_hp, email
   
6. GoldPriceResource (Harga Emas Harian)
   - Form: tanggal, harga_beli, harga_jual, catatan
   - Table: tanggal, harga_beli (currency), harga_jual (currency)
   - Filter: range tanggal
   - Hanya hari ini yang bisa diedit

7. SettingResource
   - Halaman tunggal (bukan list)
   - Form: logo, name, phone, address, print_via_bluetooth, name_printer_local
   - Tidak bisa create/delete, hanya edit

Semua resource harus:
- Label Bahasa Indonesia
- Mobile-friendly (visibleFrom('md') untuk kolom sekunder)
- Export Excel action
- Soft delete dengan restore
```

---

## ⚡ FASE 5: FILAMENT RESOURCES (Part 2 - Transaksi)

```
Buatkan Filament Resource untuk modul Transaksi:

1. ProductResource
   - Form sections: Informasi Produk, Detail, Harga, Stok
   - Fields: category_id, sub_category_id (dependent), gold_purity_id
   - Fields: name, sku, barcode, weight_gram
   - Fields: cost_price, selling_price, stock, image, is_active
   - Table: image (40x40), name+category, weight, selling_price, stock (badge warna)
   - Filter: category, is_active, low_stock
   - Action: Duplicate, Export
   - Bulk import dari Excel

2. TransactionResource
   - READ-ONLY untuk kasir (create via POS)
   - Table: transaction_number, name, total (currency), payment_method, user, created_at
   - View: Detail items, payment info
   - Action: Download PDF Invoice, Delete (dengan approval untuk kasir)
   - Filter: tanggal, payment_method, user
   - Sum total di footer

3. BuybackResource
   - Form: tipe (radio), tanggal, customer_name, customer_phone
   - Repeater: buyback_items (nama_produk, kadar, berat, harga_per_gram, subtotal)
   - Auto-calculate: berat_total, total_bayar
   - Table: tanggal, customer_name, berat_total, total_bayar, status (badge)
   - Action: Approve, Reject (untuk admin)
   - Kasir tidak bisa delete

4. CashFlowResource
   - Form: type (select), amount, description
   - Table: date, type (badge), amount (currency), description, user
   - Filter: type, tanggal
   - Sum income & expense di footer
   - Transaksi & Buyback otomatis tercatat (readonly)

5. InventoryResource
   - READ-ONLY (otomatis dari transaksi/buyback)
   - Table: reference_number, type (badge In/Out/Adj), source, created_at
   - View: detail items

6. CucianResource
   - Form: customer_name, customer_phone, tanggal_masuk, catatan
   - Repeater: cucian_items
   - Table: customer_name, tanggal_masuk, status (badge), items_count
   - Action: Mark as Selesai, Mark as Diambil

Semua harus mobile-friendly dan label Bahasa Indonesia.
```

---

## ⚡ FASE 6: FILAMENT SHIELD (RBAC)

```
Setup Filament Shield untuk Role-Based Access Control:

1. Jalankan:
   - php artisan shield:install
   - php artisan shield:generate --all --panel=admin
   
2. Buat 3 Role dengan permission:

   SUPER_ADMIN:
   - Semua permission
   - Bisa manage user
   - Bisa approve semua
   - Bisa delete transaksi langsung
   
   ADMIN:
   - Semua CRUD (Create, Update, Delete butuh approval Super Admin)
   - Bisa approve buyback kasir
   - Tidak bisa manage super_admin
   - Tidak bisa delete transaksi langsung
   
   KASIR:
   - POS (buat transaksi)
   - View transaksi sendiri saja
   - Input buyback (butuh approval)
   - View produk, member
   - Tidak bisa delete apapun

3. Buat 3 User default:
   - superadmin@gmail.com / password (role: super_admin)
   - admin@gmail.com / password (role: admin)
   - kasir@gmail.com / password (role: kasir)

4. Implement approval hierarchy:
   - Kasir → butuh approval Admin atau Super Admin
   - Admin → butuh approval Super Admin
   - Super Admin → langsung eksekusi

Tambahkan gate check di setiap action sensitif (delete, approve).
```

---

## ⚡ FASE 7: LIVEWIRE POS

```
Buatkan Livewire Component untuk halaman POS Kasir dengan spesifikasi:

LAYOUT DESKTOP (2 Kolom):
- Kiri (70%): Product Grid
- Kanan (30%): Keranjang

LAYOUT MOBILE:
- Full screen product grid
- Floating cart button dengan badge counter
- Slide-up modal untuk keranjang

FITUR:
1. Search produk (realtime, debounce 300ms)
2. Filter kategori (horizontal scrollable tabs)
3. Scan barcode (modal dengan input focus)
4. Product cards: image, name, price, stock, click to add
5. Cart: item list, quantity +/-, remove, subtotal per item
6. Checkout modal:
   - Nama pelanggan (autocomplete dari member)
   - No HP
   - Payment method (radio buttons with icons)
   - Total, Nominal bayar, Kembalian (auto-calculate)
   - Catatan
   - Tombol: Batal, Proses
7. Setelah sukses:
   - Modal pilih cetak: Printer Lokal, Bluetooth, PDF, Lewati
   - Reset cart

VALIDASI:
- Stok harus cukup
- Nominal bayar >= total (untuk tunai)
- Kembalian otomatis dihitung

LOGIC:
- Simpan transaksi ke database
- Kurangi stok produk
- Simpan/update member jika ada nomor HP
- Buat record cash_flow (income)
- Buat record inventory (out)

STYLING:
- Dark mode (bg-gray-900, bg-gray-800)
- Accent emerald/green
- Rounded corners, shadows
- Smooth transitions
- Touch-friendly buttons (min 44px)

Integrasikan sebagai Filament Custom Page dengan route /pos
```

---

## ⚡ FASE 8: DASHBOARD WIDGETS

```
Buatkan Filament Widgets untuk Dashboard:

1. StatsOverview (Stats Cards)
   - Transaksi Hari Ini (count)
   - Pendapatan Hari Ini (sum total)
   - Laba Kotor Hari Ini (sum profit)
   - Buyback Pending (count)
   - Icon dan warna berbeda per card
   - Trend indicator (naik/turun dari kemarin)

2. TotalStatsOverview
   - Statistik Bulan Ini
   - Total Transaksi, Total Pendapatan, Total Laba
   - Format currency Rp

3. ProductFavorite (Table Widget)
   - 5 Produk Terlaris bulan ini
   - Kolom: Nama, Qty Terjual, Total Penjualan
   - Ranking 1-5 dengan badge

4. ProductAlert (Table Widget)
   - Produk dengan stok < 3
   - Kolom: Nama, Stok (badge merah)
   - Link ke edit produk

5. PaymentMethodPieChart (Chart Widget)
   - Distribusi metode pembayaran bulan ini
   - Pie/Doughnut chart
   - Legend: nama + percentage

6. CashFlowRadarChart (Chart Widget)
   - Pemasukan vs Pengeluaran per minggu
   - Bar chart atau line chart
   - 4 minggu terakhir

Semua widget:
- Responsive
- Dark mode compatible
- Refresh otomatis
- Label Bahasa Indonesia
```

---

## ⚡ FASE 9: OBSERVERS & AUTOMATION

```
Buatkan Laravel Observers untuk automation:

1. TransactionObserver

   created():
   - Generate transaction_number (TRX + date + auto_increment)
   - Kurangi stok setiap produk di transaction_items
   - Buat CashFlow income dengan amount = total
   - Buat Inventory record (out)
   - Update member total_pembelian & total_gram
   - Kirim notifikasi ke Super Admin
   
   deleted() / forceDeleted():
   - Kembalikan stok produk
   - Hapus CashFlow terkait (bukan buat refund)
   - Buat Inventory adjustment (in)
   - Kirim notifikasi ke Super Admin

2. BuybackObserver

   created():
   - Auto-calculate berat_total dan total_bayar
   - Status default: pending
   - Kirim notifikasi approval ke Admin & Super Admin
   
   updated() - jika status berubah ke approved:
   - Buat CashFlow expense dengan amount = total_bayar
   - Buat Inventory record (in)
   - Catat approved_by dan approved_at
   - Kirim notifikasi ke pembuat buyback
   
   updated() - jika status berubah ke rejected:
   - Kirim notifikasi rejection ke pembuat

3. ProductObserver

   updated():
   - Jika stok < 3, kirim notifikasi "Stok Menipis" ke Admin
   - Log perubahan harga

4. TransactionItemObserver

   created():
   - Hitung total_profit = (price - cost_price) * quantity
   
Register semua observer di EventServiceProvider atau via attribute.
```

---

## ⚡ FASE 10: NOTIFICATION SYSTEM

```
Implementasi sistem notifikasi:

1. Buat Notification Classes:

   - TransaksiBaruNotification
     Channel: database
     Data: transaction_number, total, url ke detail
     
   - BuybackNeedApprovalNotification
     Channel: database
     Data: buyback info, requestor name, url ke approval
     
   - BuybackApprovedNotification
     Channel: database
     Data: buyback info, approver name
     
   - BuybackRejectedNotification
     Channel: database
     Data: buyback info, reason
     
   - TransactionDeletedNotification
     Channel: database, mail
     Data: transaction info, deleted by, reason
     
   - StokMenipisNotification
     Channel: database
     Data: product name, current stock, url ke produk

2. Setup Filament Notification:
   - Bell icon di header
   - Badge counter unread
   - Dropdown list notifications
   - Mark as read on click
   - Mark all as read button
   - Link "Lihat Semua" ke halaman notifications

3. Halaman Riwayat Notifikasi:
   - List semua notifikasi
   - Filter: read/unread, type
   - Bulk mark as read
   - Delete old notifications

4. Trigger notifikasi dari Observers yang sudah dibuat.
```

---

## ⚡ FASE 11: APPROVAL SYSTEM

```
Implementasi sistem Approval dengan hierarchy:

1. Buat Filament Page: DaftarApproval
   - List semua pending approval
   - Group by type (Buyback, Delete Request, dll)
   - Card untuk setiap item:
     - Icon & Type
     - Requestor info
     - Detail singkat
     - Tombol: View Detail, Approve, Reject
   - Modal konfirmasi dengan input alasan (optional)

2. Approval untuk Buyback:
   - Kasir buat buyback → status: pending
   - Admin/SuperAdmin lihat di DaftarApproval
   - Klik Approve:
     - Update status ke approved
     - Trigger BuybackObserver (buat cashflow, inventory)
     - Kirim notifikasi ke kasir
   - Klik Reject:
     - Update status ke rejected
     - Kirim notifikasi dengan alasan

3. Approval untuk Delete Transaksi:
   - Kasir/Admin klik delete → buat Approval record
   - SuperAdmin lihat di DaftarApproval
   - Klik Approve:
     - Soft delete transaksi
     - Trigger TransactionObserver (kembalikan stok, hapus cashflow)
   - Klik Reject:
     - Hapus approval record
     - Kirim notifikasi rejection

4. SuperAdmin bypass:
   - Jika user adalah super_admin, langsung eksekusi tanpa approval
   - Tampilkan confirmation modal saja

5. Tambahkan kolom di TransactionResource dan BuybackResource:
   - Status approval (badge)
   - Approver name
   - Approved at

Semua action harus memiliki Gate check sesuai role.
```

---

## ⚡ FASE 12: INVOICE PDF

```
Buatkan sistem cetak Invoice PDF:

1. Template: resources/views/pdf/invoice-a5.blade.php
   - Ukuran: A5 Landscape
   - Background: ornamen emas (public/images/bg.png)
   - Logo: public/images/logo.png

2. Layout Invoice:
   ┌─────────────────────────────────────────────────────────┐
   │  [Logo]                          Tanggal, Customer Info │
   │  Nama Toko                       No. Transaksi          │
   │  Alamat lengkap                  [Barcode]              │
   ├─────────────────────────────────────────────────────────┤
   │                  DETAIL TRANSAKSI                       │
   │  No. Trans | Pembeli | Nama Barang | Bayar | Harga      │
   ├─────────────────────────────────────────────────────────┤
   │  Terbilang: [angka dalam kata]          │   Kasir       │
   │  Perhatian:                             │               │
   │  1. Nota wajib disimpan...              │ (__________)  │
   │  2. Barang sudah diperiksa...           │               │
   │  3. ...                                 │               │
   └─────────────────────────────────────────────────────────┘

3. Controller: InvoiceController
   - Method: generatePdf($transaction_id)
   - Load transaction with items, payment, member
   - Generate barcode dari transaction_number
   - Return PDF stream

4. Helper: terbilang($angka)
   - Convert angka ke kata bahasa Indonesia
   - Contoh: 5500000 → "Lima Juta Lima Ratus Ribu"

5. Route: GET /invoice/{id}/pdf

6. Integrasi dengan POS:
   - Setelah transaksi sukses, tampilkan modal pilih cetak
   - Opsi: Download PDF, Cetak Langsung, Lewati
   - Download PDF: redirect ke route invoice PDF

7. Integrasi dengan TransactionResource:
   - Action button: Download Invoice
   - Open new tab dengan PDF

Styling menggunakan inline CSS karena DomPDF.
Warna: Gold (#b8860b), Red (#b00), transparan background.
```

---

## ⚡ FASE 13: MULTI-SITE DEPLOYMENT

```
Persiapkan deployment untuk 4 website:

1. Struktur folder:
   tokoemas-deploy/
   ├── core/               ← Laravel shared (app, vendor, dll)
   └── sites/
       ├── wates/          ← Main Site
       │   ├── public/
       │   ├── storage/
       │   └── .env
       ├── wates2/         ← Audit Site (read-only)
       ├── sentolo/        ← Main Site
       └── sentolo2/       ← Audit Site

2. Buat index.php multi-tenant:
   - Detect site dari folder
   - Load .env dari site folder
   - Use storage dari site folder
   - Bootstrap dari core/

3. Buat TransactionResource versi Audit (tanpa delete):
   - Hapus semua action edit/delete
   - Hapus create button
   - canCreate() return false
   - Table actions hanya View

4. Buat script deploy:
   - migrate-database.php
   - shield-setup.php
   - reset-password.php
   
5. Buat .htaccess security:
   - Block akses ke .env, .sql, .git
   - Disable directory listing

6. Implementasi sync data:
   - Observer di main site
   - INSERT ke audit database
   - TIDAK delete dari audit saat main delete

7. Konfigurasi .env per site:
   - APP_NAME berbeda
   - APP_URL berbeda
   - DB_DATABASE berbeda (main vs audit)
   - APP_KEY unik per site

Sertakan panduan upload ke cPanel shared hosting.
```

---

## ⚡ FASE 14: TESTING & FINALISASI

```
Lakukan testing menyeluruh:

1. Test Login per Role:
   - Super Admin: akses semua menu
   - Admin: tidak bisa manage user super_admin
   - Kasir: hanya POS dan view terbatas

2. Test POS:
   - Search produk
   - Filter kategori
   - Add to cart
   - Checkout tunai (hitung kembalian)
   - Checkout transfer
   - Cetak PDF

3. Test Buyback:
   - Kasir input buyback → pending
   - Admin approve → cashflow & inventory tercatat
   - Admin reject → notifikasi ke kasir

4. Test Approval:
   - Kasir delete transaksi → pending approval
   - Super Admin approve → transaksi terhapus, stok kembali
   - Super Admin reject → tetap ada

5. Test Notifikasi:
   - Bell icon update realtime
   - Click notification → redirect ke detail
   - Mark as read

6. Test Mobile:
   - POS responsive
   - Table columns hide di mobile
   - Floating cart button

7. Test Invoice PDF:
   - Layout benar
   - Barcode muncul
   - Terbilang benar

8. Test Multi-Site:
   - Main site: full CRUD
   - Audit site: read-only
   - Data sync dari main ke audit

Buat checklist dan pastikan semua test PASS.
```

---

## 📋 CHECKLIST FINAL

```
- [ ] Login 3 role berfungsi
- [ ] Permission sesuai role
- [ ] POS mobile-friendly
- [ ] Checkout & kembalian benar
- [ ] Stok berkurang setelah transaksi
- [ ] CashFlow otomatis tercatat
- [ ] Buyback dengan approval
- [ ] Notifikasi muncul
- [ ] Invoice PDF bisa download
- [ ] Dashboard widgets muncul
- [ ] Export Excel berfungsi
- [ ] Audit site read-only
- [ ] Data sync ke audit
- [ ] Security (.htaccess)
- [ ] Deployment scripts siap
```

---

## 🎯 SELESAI!

Jika semua fase sudah dieksekusi dan checklist terpenuhi,
Aplikasi POS Toko Emas siap digunakan!

**Estimasi Total: 30-40 jam kerja**
