# 🏆 BLUEPRINT LENGKAP: Aplikasi POS Toko Emas

## 📋 OVERVIEW

**Nama:** Aplikasi Kasir Toko Emas  
**Versi:** 1.0  
**Stack:** Laravel 11 + Filament v3 + Livewire 3 + Tailwind CSS  
**Target:** Multi-cabang toko emas dengan shared hosting

---

## � ARSITEKTUR MULTI-SITE (4 Website)

### Konsep: Main Site + Audit Site

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           ARSITEKTUR DEPLOYMENT                                 │
│                                                                                 │
│  ┌─────────────────────────┐              ┌─────────────────────────┐          │
│  │   WATES (Main Site)    │  ──SYNC──►  │   WATES2 (Audit Site)   │          │
│  │   wates.hartowiyono.my.id│              │   wates2.hartowiyono.my.id│          │
│  │                         │              │                         │          │
│  │   ✅ Full CRUD          │              │   🔒 READ-ONLY          │          │
│  │   ✅ Hapus Transaksi    │              │   ❌ Tidak Bisa Hapus   │          │
│  │   ✅ Operasional Harian │              │   📊 Backup Permanen    │          │
│  │                         │              │                         │          │
│  │   DB: db_wates_main     │              │   DB: db_wates_audit    │          │
│  └─────────────────────────┘              └─────────────────────────┘          │
│                                                                                 │
│  ┌─────────────────────────┐              ┌─────────────────────────┐          │
│  │  SENTOLO (Main Site)   │  ──SYNC──►  │  SENTOLO2 (Audit Site)  │          │
│  │   sentolo.hartowiyono.my.id│            │   sentolo2.hartowiyono.my.id│        │
│  │                         │              │                         │          │
│  │   ✅ Full CRUD          │              │   🔒 READ-ONLY          │          │
│  │   ✅ Hapus Transaksi    │              │   ❌ Tidak Bisa Hapus   │          │
│  │   ✅ Operasional Harian │              │   📊 Backup Permanen    │          │
│  │                         │              │                         │          │
│  │   DB: db_sentolo_main   │              │   DB: db_sentolo_audit  │          │
│  └─────────────────────────┘              └─────────────────────────┘          │
│                                                                                 │
│                        ┌─────────────────────────┐                             │
│                        │   SHARED MEMBER DB      │                             │
│                        │   db_members            │                             │
│                        │   (Data pelanggan       │                             │
│                        │    semua cabang)        │                             │
│                        └─────────────────────────┘                             │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### Perbedaan Main Site vs Audit Site

| Aspek | Main Site (wates, sentolo) | Audit Site (wates2, sentolo2) |
|-------|---------------------------|-------------------------------|
| **Tujuan** | Operasional harian kasir | Backup & audit pemilik |
| **CRUD** | Full (Create, Read, Update, Delete) | Read Only |
| **Hapus Transaksi** | ✅ Bisa (dengan approval) | ❌ Tidak ada tombol hapus |
| **Hapus Produk** | ✅ Bisa | ❌ Tidak bisa |
| **Input Buyback** | ✅ Bisa | ❌ Tidak bisa |
| **Data Source** | Database utama | Sinkron dari main site |
| **User** | Kasir, Admin | Super Admin only |
| **Keamanan** | Fitur hapus dilindungi approval | Fitur hapus dihilangkan total |

### Mekanisme Sinkronisasi Data

```
┌──────────────────────────────────────────────────────────────────┐
│                    FLOW SINKRONISASI                             │
│                                                                  │
│  MAIN SITE (wates.hartowiyono.my.id)                            │
│  ─────────────────────────────────────                          │
│                                                                  │
│  1. Kasir buat transaksi baru                                   │
│     └──► INSERT ke db_wates_main                                │
│     └──► TRIGGER: INSERT ke db_wates_audit (via Observer)      │
│                                                                  │
│  2. Admin hapus transaksi (approved)                            │
│     └──► DELETE dari db_wates_main                              │
│     └──► TIDAK delete dari db_wates_audit ❗                    │
│                                                                  │
│  3. Update produk                                               │
│     └──► UPDATE db_wates_main                                   │
│     └──► UPDATE db_wates_audit (optional)                       │
│                                                                  │
│  HASIL:                                                          │
│  • Main Site: Data bersih, transaksi yang dihapus hilang        │
│  • Audit Site: Data lengkap, semua transaksi tetap ada          │
│    (termasuk yang sudah dihapus di main)                        │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

### Implementasi Sinkronisasi

#### Opsi 1: Database Trigger (MySQL)
```sql
-- Di Main Database, buat trigger untuk insert ke Audit DB
DELIMITER //
CREATE TRIGGER sync_transaction_to_audit
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    INSERT INTO db_wates_audit.transactions 
    SELECT * FROM db_wates_main.transactions WHERE id = NEW.id;
END //
DELIMITER ;
```

#### Opsi 2: Laravel Observer (Lebih Fleksibel)
```php
// app/Observers/TransactionObserver.php

class TransactionObserver
{
    public function created(Transaction $transaction)
    {
        // Sync ke audit database
        DB::connection('audit')->table('transactions')->insert(
            $transaction->toArray()
        );
    }
    
    // TIDAK ada method deleted() - data audit tetap ada
}
```

#### Opsi 3: Scheduled Job (Batch Sync)
```php
// app/Console/Commands/SyncToAudit.php

class SyncToAudit extends Command
{
    protected $signature = 'sync:audit';
    
    public function handle()
    {
        // Ambil transaksi baru dari main yang belum ada di audit
        $newTransactions = Transaction::whereNotIn('id', function($q) {
            $q->select('id')->from('db_audit.transactions');
        })->get();
        
        // Insert ke audit database
        foreach ($newTransactions as $trx) {
            DB::connection('audit')->table('transactions')->insert(
                $trx->toArray()
            );
        }
    }
}

// Schedule: Jalankan setiap 5 menit
// app/Console/Kernel.php
$schedule->command('sync:audit')->everyFiveMinutes();
```

### Konfigurasi Database (.env)

#### Main Site (wates.hartowiyono.my.id)
```env
# Database Operasional
DB_CONNECTION=mysql
DB_DATABASE=harm7631_wates_main
DB_USERNAME=harm7631_wates
DB_PASSWORD=secretpassword

# Database Audit (untuk sync)
DB_AUDIT_CONNECTION=mysql
DB_AUDIT_DATABASE=harm7631_wates_audit
DB_AUDIT_USERNAME=harm7631_wates
DB_AUDIT_PASSWORD=secretpassword
```

#### Audit Site (wates2.hartowiyono.my.id)
```env
# Hanya koneksi ke Audit DB (READ-ONLY)
DB_CONNECTION=mysql
DB_DATABASE=harm7631_wates_audit
DB_USERNAME=harm7631_wates_readonly  # User dengan privilege SELECT only
DB_PASSWORD=secretpassword
```

### TransactionResource untuk Audit Site (Tanpa Delete)

```php
// app/Filament/Resources/TransactionResource.php (Versi Audit Site)

class TransactionResource extends Resource
{
    // ... (sama seperti main site)
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ... kolom sama
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // TIDAK ADA EditAction
                // TIDAK ADA DeleteAction
            ])
            ->bulkActions([
                // KOSONG - tidak ada bulk action
            ]);
    }
    
    // Hapus halaman create dan edit
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            // TIDAK ADA 'create' dan 'edit'
        ];
    }
    
    // Disable create
    public static function canCreate(): bool
    {
        return false;
    }
}
```

### Struktur Folder Deployment

```
tokoemas-deploy/
├── core/                           ← Laravel core (shared)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   └── vendor/
│
├── sites/
│   ├── wates/                      ← Main Site Wates
│   │   ├── public/
│   │   ├── storage/
│   │   ├── .env                    ← DB: wates_main
│   │   └── app/Filament/Resources/ ← Full CRUD
│   │
│   ├── wates2/                     ← Audit Site Wates
│   │   ├── public/
│   │   ├── storage/
│   │   ├── .env                    ← DB: wates_audit (READ-ONLY)
│   │   └── app/Filament/Resources/ ← Tanpa Delete/Edit
│   │
│   ├── sentolo/                    ← Main Site Sentolo
│   │   └── ...
│   │
│   └── sentolo2/                   ← Audit Site Sentolo
│       └── ...
```

## �🎨 DESIGN SYSTEM

### Warna Utama
```css
Primary: Emerald/Green (#10b981, #059669)
Secondary: Gold/Amber (#f59e0b)
Background Dark: #1f2937, #111827
Text: White/Gray
Accent: Red for danger, Blue for info
```

### Font
- **Primary:** Poppins (Google Fonts)
- **Fallback:** Inter, system-ui

### UI Components
- Cards dengan rounded-2xl, shadow-lg
- Gradient buttons (from-green-500 to-emerald-600)
- Dark mode sebagai default
- Glassmorphism effects (backdrop-blur)
- Micro-animations (hover:scale-105, transition-all)

---

## 📁 STRUKTUR FITUR DETAIL

### 1. 🔐 AUTHENTICATION & AUTHORIZATION

#### Login Page
```
- Logo toko di tengah atas
- Form: Email, Password, Ingat Saya
- Tombol "Masuk" gradient hijau
- Background gelap dengan subtle pattern
```

#### Role & Permission dengan Approval Hierarchy

```
┌─────────────────────────────────────────────────────────────────┐
│                    HIERARKI APPROVAL                            │
│                                                                 │
│                    ┌─────────────────┐                         │
│                    │  SUPER ADMIN    │ ◄── Approve semua       │
│                    │  (Pemilik)      │     Tidak butuh approval│
│                    └────────┬────────┘                         │
│                             │                                   │
│                    Approve ▼ Admin                              │
│                    ┌─────────────────┐                         │
│                    │     ADMIN       │ ◄── Semua aksi butuh    │
│                    │  (Manajer Toko) │     approval Super Admin│
│                    └────────┬────────┘                         │
│                             │                                   │
│                    Approve ▼ Kasir                              │
│                    ┌─────────────────┐                         │
│                    │     KASIR       │ ◄── Transaksi langsung  │
│                    │  (Operator POS) │     Buyback butuh Admin │
│                    └─────────────────┘                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

| Role | Aksi Langsung (Tanpa Approval) | Aksi Butuh Approval |
|------|-------------------------------|---------------------|
| **super_admin** | SEMUA fitur | Tidak ada - langsung eksekusi |
| **admin** | View semua data | Create/Update/Delete produk → Super Admin |
| | | Create/Update/Delete kategori → Super Admin |
| | | Approve buyback kasir → Super Admin |
| | | Update harga emas → Super Admin |
| | | Hapus transaksi → Super Admin |
| **kasir** | POS (transaksi langsung) | Buyback → Admin/Super Admin |
| | View transaksi sendiri | Cancel transaksi → Admin |
| | Input data member | |

#### Matrix Approval Detail

| Aksi | Kasir | Admin | Super Admin |
|------|-------|-------|-------------|
| **Transaksi POS** | ✅ Langsung | ✅ Langsung | ✅ Langsung |
| **Lihat Transaksi** | 🔒 Sendiri saja | ✅ Semua | ✅ Semua |
| **Hapus Transaksi** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |
| **Input Buyback** | 📝 Perlu Approval | 📝 Perlu Approval SA | ✅ Langsung |
| **Approve Buyback Kasir** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |
| **CRUD Produk** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |
| **CRUD Kategori** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |
| **Update Harga Emas** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |
| **CRUD User** | ❌ | ❌ | ✅ Langsung |
| **Lihat Laporan** | 🔒 Terbatas | ✅ Semua | ✅ Semua |
| **Export Data** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |
| **Pengaturan Toko** | ❌ | 📝 Perlu Approval SA | ✅ Langsung |

**Keterangan:**
- ✅ Langsung = Bisa eksekusi tanpa approval
- 📝 Perlu Approval = Masuk antrian approval
- 🔒 Terbatas = Akses terbatas
- ❌ = Tidak punya akses

---

### 2. 📊 DASHBOARD

#### Layout
```
┌──────────────────────────────────────────────────────┐
│  HEADER: Logo + Nama Toko + User Menu + Notif Bell  │
├──────────────────────────────────────────────────────┤
│ SIDEBAR │              CONTENT AREA                  │
│ ─────── │  ┌─────────────────────────────────────┐  │
│ Dashboard│  │  STATS CARDS (4 kolom)              │  │
│ POS      │  │  [Transaksi] [Pendapatan] [Laba]   │  │
│ Produk   │  └─────────────────────────────────────┘  │
│ Transaksi│  ┌──────────────┐ ┌──────────────────┐   │
│ Buyback  │  │ PIE CHART    │ │ PRODUK TERLARIS  │   │
│ Keuangan │  │ Payment      │ │ (Top 5 Table)    │   │
│ Inventori │  └──────────────┘ └──────────────────┘   │
│ Member   │  ┌──────────────┐ ┌──────────────────┐   │
│ Laporan  │  │ RADAR CHART  │ │ STOK MENIPIS     │   │
│ Pengaturan│ │ Cash Flow    │ │ (Alert Table)    │   │
│          │  └──────────────┘ └──────────────────┘   │
└──────────────────────────────────────────────────────┘
```

#### Widgets
1. **StatsOverview** - Transaksi hari ini, pendapatan, laba kotor
2. **TotalStatsOverview** - Statistik bulan berjalan
3. **ProductFavorite** - 5 produk terlaris (tabel)
4. **ProductAlert** - Produk dengan stok < 3 (tabel merah)
5. **PaymentMethodPieChart** - Distribusi metode bayar
6. **CashFlowRadarChart** - Pemasukan vs pengeluaran per minggu

---

### 3. 🛒 HALAMAN POS (Point of Sale)

#### Layout Desktop (XL screen)
```
┌────────────────────────────────────────────────────────────┐
│ HEADER: [Search Box] [Scan Barcode Button] [Scanner Modal] │
├────────────────────────────────────────────────────────────┤
│ KATEGORI TABS (Horizontal Scroll)                          │
│ [Semua] [Emas] [Perak] [Perhiasan] [Cincin] ...            │
├────────────────────────────────────────────┬───────────────┤
│                                            │               │
│  PRODUCT GRID (3-4 kolom)                 │   KERANJANG   │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐         │   ───────────  │
│  │ IMG │ │ IMG │ │ IMG │ │ IMG │         │   Item 1  x2  │
│  │Name │ │Name │ │Name │ │Name │         │   Item 2  x1  │
│  │Price│ │Price│ │Price│ │Price│         │   ───────────  │
│  │Stock│ │Stock│ │Stock│ │Stock│         │   Subtotal:   │
│  └─────┘ └─────┘ └─────┘ └─────┘         │   Rp 5.000.000│
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐         │               │
│  │ ... │ │ ... │ │ ... │ │ ... │         │  [CHECKOUT]   │
│  └─────┘ └─────┘ └─────┘ └─────┘         │               │
│                                            │               │
│  [Pagination]                              │  [RESET]      │
│                                            │               │
└────────────────────────────────────────────┴───────────────┘
```

#### Layout Mobile
```
┌─────────────────────────┐
│ [Search] [Scan]         │
├─────────────────────────┤
│ KATEGORI (scroll)       │
├─────────────────────────┤
│  ┌─────┐ ┌─────┐       │
│  │ IMG │ │ IMG │       │
│  │Name │ │Name │       │  <-- 2 kolom
│  │Rp   │ │Rp   │       │
│  └─────┘ └─────┘       │
│  ┌─────┐ ┌─────┐       │
│  │ ... │ │ ... │       │
│  └─────┘ └─────┘       │
│                         │
│  [Pagination]           │
│                         │
│        🛒               │  <-- Floating Cart Button
│       (3)               │      dengan badge counter
└─────────────────────────┘

Klik Floating Button:
┌─────────────────────────┐
│ ═══════════════         │  <-- Slide up modal
│    KERANJANG            │
│ ─────────────────────   │
│ Item 1          x2  [-] │
│ Rp 2.500.000      [+]   │
│ ─────────────────────   │
│ Item 2          x1  [-] │
│ Rp 1.500.000      [+]   │
│ ═══════════════════════ │
│ TOTAL: Rp 6.500.000     │
│ [CHECKOUT] [RESET]      │
└─────────────────────────┘
```

#### Checkout Modal
```
┌────────────────────────────────────────┐
│           CHECKOUT                     │
│ ────────────────────────────────────── │
│ Total Belanja: Rp 6.500.000            │
│                                        │
│ Nama Pelanggan: [__Umum__________]     │
│ No. HP:         [______________]       │
│ Alamat:         [______________]       │
│                                        │
│ Metode Bayar:                          │
│ (●) Tunai  ( ) Transfer  ( ) QRIS     │
│                                        │
│ Nominal Bayar: [Rp 7.000.000____]      │
│ Kembalian:     Rp 500.000              │
│                                        │
│ Catatan:       [______________]        │
│                                        │
│ ────────────────────────────────────── │
│    [BATAL]            [PROSES]         │
└────────────────────────────────────────┘
```

#### Modal Cetak Struk
```
┌────────────────────────────────────────┐
│      ✅ Transaksi Berhasil!            │
│ ────────────────────────────────────── │
│                                        │
│   Bagaimana Anda ingin mencetak?       │
│                                        │
│   [🖨️ Printer Lokal (Kabel)]           │
│                                        │
│   [📱 Printer Bluetooth]               │
│                                        │
│   [📄 Download PDF]                    │
│                                        │
│   [✕ Lewati]                           │
└────────────────────────────────────────┘
```

---

### 4. 📦 MANAJEMEN PRODUK

#### Tabel Produk (Desktop)
| Gambar | Nama Produk | Kategori | Kadar | Berat | Harga Jual | Stok | Aksi |
|--------|-------------|----------|-------|-------|------------|------|------|
| [img]  | Cincin Nikah 5g | Perhiasan | 22K | 5g | Rp 5.000.000 | 10 | Edit/Hapus |

#### Tabel Produk (Mobile)
| Gambar | Nama & Harga | Stok |
|--------|--------------|------|
| [img]  | Cincin Nikah 5g<br>Rp 5.000.000 | 10 |

*Kolom Kategori, Kadar, Berat: hidden di mobile (visibleFrom('md'))*

#### Form Produk
```
┌─────────────────────────────────────────────────────┐
│ INFORMASI PRODUK                                    │
│ ─────────────────────────────────────────────────── │
│ Nama:       [______________________________]        │
│ Kategori:   [Dropdown____▼]                        │
│ Sub Kategori: [Dropdown____▼]                      │
│ Kadar Emas: [Dropdown____▼]                        │
│ ─────────────────────────────────────────────────── │
│ DETAIL                                              │
│ SKU:        [______] Barcode: [__________]          │
│ Berat (g):  [______]                                │
│ ─────────────────────────────────────────────────── │
│ HARGA                                               │
│ Harga Modal: [Rp ________]                          │
│ Harga Jual:  [Rp ________]                          │
│ ─────────────────────────────────────────────────── │
│ STOK & STATUS                                       │
│ Stok:   [___]   Status: [✓] Aktif                  │
│ ─────────────────────────────────────────────────── │
│ GAMBAR                                              │
│ [Upload Image]                                      │
│ ─────────────────────────────────────────────────── │
│            [SIMPAN]    [BATAL]                      │
└─────────────────────────────────────────────────────┘
```

---

### 5. 💰 BUYBACK (Beli Kembali Emas)

#### Tipe Buyback
1. **Dari Pelanggan** - Pelanggan menjual emas ke toko
2. **Pembelian Stok** - Toko beli dari supplier/pengepul

#### Form Buyback
```
┌─────────────────────────────────────────────────────┐
│ INFORMASI BUYBACK                                   │
│ ─────────────────────────────────────────────────── │
│ Tipe:    (●) Dari Pelanggan  ( ) Pembelian Stok    │
│ Tanggal: [__/__/____]                               │
│ ─────────────────────────────────────────────────── │
│ DATA PENJUAL                                        │
│ Nama:    [______________________________]           │
│ No. HP:  [______________________________]           │
│ ─────────────────────────────────────────────────── │
│ ITEM YANG DIBELI                                    │
│ ┌───────────────────────────────────────────────┐   │
│ │ Nama Barang  │ Kadar │ Berat │ Harga/g │ Total│   │
│ │──────────────│───────│───────│─────────│──────│   │
│ │ Kalung emas  │ 22K   │ 10g   │ 900.000 │ 9jt  │   │
│ │ [+ Tambah Item]                               │   │
│ └───────────────────────────────────────────────┘   │
│ ─────────────────────────────────────────────────── │
│ TOTAL BAYAR: Rp 9.000.000                           │
│ Catatan:     [______________________________]       │
│ ─────────────────────────────────────────────────── │
│            [SIMPAN & MINTA APPROVAL]               │
└─────────────────────────────────────────────────────┘
```

---

### 6. 🔔 SISTEM NOTIFIKASI

#### Jenis Notifikasi
| Event | Dikirim Ke | Channel |
|-------|------------|---------|
| Transaksi baru dibuat | Super Admin | Database + Bell Icon |
| Buyback butuh approval | Admin, Super Admin | Database + Bell Icon |
| Buyback di-approve | Kasir (pembuat) | Database |
| Buyback di-reject | Kasir (pembuat) | Database |
| Transaksi dihapus | Super Admin | Database + Email |
| Stok produk menipis (<3) | Admin, Super Admin | Database |
| User baru dibuat | Super Admin | Database |

#### Implementasi Notifikasi
```php
// Notification Class: TransaksiBaruDibuat
public function via($notifiable) {
    return ['database'];
}

public function toDatabase($notifiable) {
    return [
        'title' => 'Transaksi Baru',
        'body' => "Transaksi #{$this->transaction->transaction_number} sebesar Rp " . 
                  number_format($this->transaction->total),
        'url' => route('filament.admin.resources.transactions.view', $this->transaction->id),
    ];
}
```

#### UI Notifikasi (Bell Icon)
```
┌─────────────────────────────────┐
│ 🔔 Notifikasi             (5)  │
├─────────────────────────────────┤
│ ● Transaksi Baru #TRX001       │
│   Rp 5.000.000 - 2 menit lalu  │
├─────────────────────────────────┤
│ ● Buyback Butuh Approval       │
│   Dari: Kasir - 10 menit lalu  │
├─────────────────────────────────┤
│ ○ Stok Cincin Emas Menipis     │
│   Tersisa 2 unit - 1 jam lalu  │
├─────────────────────────────────┤
│        [Lihat Semua]           │
└─────────────────────────────────┘
```

---

### 7. ✅ SISTEM APPROVAL

#### Flow Approval Buyback

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  KASIR                        ADMIN/SUPER ADMIN                 │
│  ──────                       ─────────────────                 │
│                                                                 │
│  ┌──────────────┐             ┌──────────────┐                 │
│  │ Input Buyback│ ──────────► │ Notifikasi   │                 │
│  │ (Draft)      │             │ Masuk        │                 │
│  └──────────────┘             └──────┬───────┘                 │
│                                      │                         │
│                                      ▼                         │
│                               ┌──────────────┐                 │
│                               │ Review Data  │                 │
│                               │ Buyback      │                 │
│                               └──────┬───────┘                 │
│                                      │                         │
│                            ┌─────────┴─────────┐               │
│                            ▼                   ▼               │
│                     ┌────────────┐      ┌────────────┐         │
│                     │  APPROVE   │      │   REJECT   │         │
│                     └─────┬──────┘      └─────┬──────┘         │
│                           │                   │                │
│                           ▼                   ▼                │
│                    ┌─────────────┐     ┌─────────────┐         │
│                    │ • Record    │     │ • Notif ke  │         │
│                    │   CashFlow  │     │   Kasir     │         │
│                    │ • Update    │     │ • Status    │         │
│                    │   Inventory │     │   Rejected  │         │
│                    │ • Notif ke  │     └─────────────┘         │
│                    │   Kasir     │                              │
│                    └─────────────┘                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Flow Approval Hapus Transaksi

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  KASIR/ADMIN                      SUPER ADMIN                   │
│  ───────────                      ───────────                   │
│                                                                 │
│  ┌──────────────┐                ┌──────────────┐              │
│  │ Klik "Hapus" │ ─────────────► │ Notifikasi   │              │
│  │ Transaksi    │                │ Request      │              │
│  └──────────────┘                └──────┬───────┘              │
│                                         │                      │
│  ┌──────────────┐                       ▼                      │
│  │ Status:      │                ┌──────────────┐              │
│  │ "Menunggu    │                │ • Review     │              │
│  │  Approval"   │                │ • Approve/   │              │
│  └──────────────┘                │   Reject     │              │
│         ▲                        └──────┬───────┘              │
│         │                               │                      │
│         │      ┌────────────────────────┘                      │
│         │      ▼                                               │
│  ┌──────┴───────────┐                                          │
│  │ Jika Approved:   │                                          │
│  │ • Soft Delete    │                                          │
│  │   Transaksi      │                                          │
│  │ • Hapus CashFlow │                                          │
│  │   terkait        │                                          │
│  │ • Kembalikan     │                                          │
│  │   Stok Produk    │                                          │
│  └──────────────────┘                                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Halaman Daftar Approval
```
┌─────────────────────────────────────────────────────────────────┐
│ 📋 DAFTAR APPROVAL                                    [Filter] │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ 🟡 PENDING │ Buyback #BYB001                               │ │
│ │ ──────────────────────────────────────────────────────────│ │
│ │ Dibuat oleh: Kasir                                         │ │
│ │ Tanggal: 08 Jan 2026                                       │ │
│ │ Total: Rp 9.000.000                                        │ │
│ │ Tipe: Pembelian dari Pelanggan                             │ │
│ │                                                            │ │
│ │ [📄 Lihat Detail]  [✅ Approve]  [❌ Reject]               │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ 🟡 PENDING │ Hapus Transaksi #TRX005                       │ │
│ │ ──────────────────────────────────────────────────────────│ │
│ │ Diminta oleh: Admin                                        │ │
│ │ Alasan: Salah input customer, customer minta batal         │ │
│ │                                                            │ │
│ │ [📄 Lihat Detail]  [✅ Approve]  [❌ Reject]               │ │
│ └─────────────────────────────────────────────────────────────┘ │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

### 8. 📈 LAPORAN & EXPORT

#### Jenis Laporan
1. **Laporan Transaksi** - Semua penjualan
2. **Laporan Buyback** - Semua pembelian emas
3. **Laporan Cash Flow** - Arus keuangan
4. **Laporan Inventori** - Stok masuk/keluar
5. **Laporan Produk** - Daftar produk dengan stok

#### Filter Laporan
- Rentang tanggal
- Kategori
- Status (untuk buyback)
- User (siapa yang melayani)

#### Export
- Excel (.xlsx)
- PDF

---

### 9. 🔄 OBSERVER & AUTOMATION

#### TransactionObserver
```php
// Saat transaksi dibuat:
- Kurangi stok produk sesuai quantity
- Buat record CashFlow (income)
- Buat record Inventory (out)
- Kirim notifikasi ke Super Admin

// Saat transaksi dihapus (soft delete):
- Kembalikan stok produk
- Hapus CashFlow terkait (bukan buat refund)
- Buat record Inventory (adjustment/in)
- Kirim notifikasi ke Super Admin
```

#### BuybackObserver
```php
// Saat buyback di-approve:
- Buat record CashFlow (expense)
- Buat record Inventory (in)
- Kirim notifikasi ke pembuat

// Saat buyback di-reject:
- Kirim notifikasi ke pembuat
```

#### ProductObserver
```php
// Saat stok berubah:
- Jika stok < 3, kirim notifikasi "Stok Menipis"
```

---

### 10. 🖨️ CETAK INVOICE

#### Format Invoice: A5 Landscape dengan Background Emas

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ [BACKGROUND: Ornamen Emas/Gold dengan pattern elegan]                       │
│                                                                             │
│  ┌──────────────────────────────────┐   ┌─────────────────────────────────┐ │
│  │ [LOGO]                           │   │ Wates, 08 Januari 2026          │ │
│  │                                  │   │ Nama: Budi Santoso              │ │
│  │ Toko Mas Harto Wiyono            │   │ Alamat: Jl. Mawar No. 5         │ │
│  │ Ps. Wates, Jl. Diponegoro 16A,  │   │ No. Telp: 081234567890          │ │
│  │ Wates, Kec. Wates,               │   │                                 │ │
│  │ Kab. Kulon Progo, DIY 55651      │   │ No. Trans: TRX20260108001       │ │
│  │ Jl. Wates No 18, Klebakan,       │   │ |||||||||||||||||||||||         │ │
│  │ Kec Sentolo. Pasar Sentolo Baru  │   │ [BARCODE]                       │ │
│  │ Kios Nomor B.14                  │   │                                 │ │
│  │ IG: @tokomashartonowiyono        │   └─────────────────────────────────┘ │
│  │ WA: 0812-3456-7890               │                                       │
│  └──────────────────────────────────┘                                       │
│                                                                             │
│                         DETAIL TRANSAKSI                                    │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │ No. Transaksi │ Nama Pembeli │ Nama Barang          │ Bayar │ Harga  │   │
│  ├───────────────┼──────────────┼──────────────────────┼───────┼────────┤   │
│  │ TRX20260108001│ Budi Santoso │ Cincin Emas 22K 5g,  │ Tunai │ Rp     │   │
│  │               │              │ Kalung Emas 24K 10g  │       │5.500.000│  │
│  └──────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  ┌────────────────────────────────────────────────┐ ┌─────────────────────┐ │
│  │ Terbilang:                                     │ │       Kasir         │ │
│  │ Lima Juta Lima Ratus Ribu Rupiah               │ │                     │ │
│  │                                                │ │                     │ │
│  │ Perhatian:                                     │ │                     │ │
│  │ 1. NOTA INI WAJIB DISIMPAN BAIK-BAIK.         │ │                     │ │
│  │    APABILA BARANG INGIN DIJUAL/DITUKAR,       │ │ ( ______________ )  │ │
│  │    NOTA INI WAJIB DIBAWA.                      │ │                     │ │
│  │ 2. Barang yang dibeli sudah diperiksa dengan   │ └─────────────────────┘ │
│  │    benar oleh Pembeli berupa berat dan kadar.  │                         │
│  │ 3. Apabila ada kekeliruan akibat kekhilafan    │                         │
│  │    kadar/berat dapat ditukar kepada kami.      │                         │
│  │ 4. Barang ini jika dijual akan dibeli menurut  │                         │
│  │    harga dibawah pasar & dipotong ongkos       │                         │
│  │    kecuali barang yang mengandung batu, patri, │                         │
│  │    dan rusak menurut harga yang berbeda.       │                         │
│  └────────────────────────────────────────────────┘                         │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### Spesifikasi Teknis Invoice

| Aspek | Spesifikasi |
|-------|-------------|
| **Ukuran Kertas** | A5 Landscape |
| **Background** | `bg.png` - Ornamen emas elegan |
| **Logo** | `logo.png` - Logo toko |
| **Font** | Arial, Helvetica (body), Times New Roman (nama toko) |
| **Warna Aksen** | Gold (#b8860b), Merah (#b00) |
| **Barcode** | Generated dari transaction_number |
| **PDF Library** | DomPDF (barryvdh/laravel-dompdf) |

#### File yang Dibutuhkan

```
resources/views/pdf/invoice-a5.blade.php   ← Template invoice
public/images/bg.png                        ← Background ornamen emas
public/images/logo.png                      ← Logo toko
app/Http/Controllers/InvoiceController.php  ← Generate PDF
app/Helpers/terbilang.php                   ← Helper angka ke terbilang
```

#### Controller Invoice

```php
// app/Http/Controllers/InvoiceController.php

class InvoiceController extends Controller
{
    public function generatePdf($id)
    {
        $transaction = Transaction::with([
            'transactionItems.product',
            'paymentMethod',
            'member'
        ])->findOrFail($id);
        
        // Generate barcode
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode(
            $transaction->transaction_number, 
            $generator::TYPE_CODE_128
        ));
        
        $pdf = PDF::loadView('pdf.invoice-a5', [
            'transaction' => $transaction,
            'barcode' => $barcode
        ]);
        
        $pdf->setPaper('a5', 'landscape');
        
        return $pdf->stream("invoice-{$transaction->transaction_number}.pdf");
    }
}
```

#### Helper Terbilang (Angka ke Kata)

```php
// app/Helpers/helpers.php

function terbilang($angka) {
    $angka = abs($angka);
    $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
    
    if ($angka < 12) return ' ' . $huruf[$angka];
    elseif ($angka < 20) return terbilang($angka - 10) . ' belas';
    elseif ($angka < 100) return terbilang($angka / 10) . ' puluh' . terbilang($angka % 10);
    elseif ($angka < 200) return ' seratus' . terbilang($angka - 100);
    elseif ($angka < 1000) return terbilang($angka / 100) . ' ratus' . terbilang($angka % 100);
    elseif ($angka < 2000) return ' seribu' . terbilang($angka - 1000);
    elseif ($angka < 1000000) return terbilang($angka / 1000) . ' ribu' . terbilang($angka % 1000);
    elseif ($angka < 1000000000) return terbilang($angka / 1000000) . ' juta' . terbilang($angka % 1000000);
    elseif ($angka < 1000000000000) return terbilang($angka / 1000000000) . ' miliar' . terbilang($angka % 1000000000);
    
    return '';
}
```

#### CSS Styling Invoice (Key Points)

```css
@page { 
    size: A5 landscape; 
    margin: 0; 
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    background-image: url("bg.png");
    background-size: 100% 100%;
}

.shop-name {
    font-family: 'Times New Roman', serif;
    font-size: 18pt;
    font-weight: bold;
    font-style: italic;
    color: #b8860b; /* Gold */
    text-shadow: 1px 1px 0 #fff;
}

table.items {
    border: 1px solid #b8860b;
    background-color: transparent;
}

table.items th {
    color: #b00; /* Red */
    border: 1px solid #b8860b;
}

.text-red { 
    color: #b00; 
    font-weight: bold; 
}
```

---

## 🗄️ DATABASE SCHEMA LENGKAP

### ERD Relationships
```
users ──────────────────────┬──────────────────────────────┐
  │                         │                              │
  │ 1:N                     │ 1:N                          │ 1:N
  ▼                         ▼                              ▼
transactions             buybacks                      cash_flows
  │                         │                              
  │ 1:N                     │ 1:N                          
  ▼                         ▼                              
transaction_items        buyback_items                    
  │                                                        
  │ N:1                                                    
  ▼                                                        
products ◄──────────────────────────────────────────────────
  │                                                        
  │ N:1                                                    
  ▼                                                        
categories ◄──── sub_categories                           
                                                          
products ◄──────────────────────────────────────────────────
  │                                                        
  │ N:1                                                    
  ▼                                                        
gold_purities                                             
                                                          
transactions ◄──────────────────────────────────────────────
  │                                                        
  │ N:1                                                    
  ▼                                                        
members                                                   
                                                          
transactions ◄──────────────────────────────────────────────
  │                                                        
  │ N:1                                                    
  ▼                                                        
payment_methods                                           
```

---

## 🚀 STEP-BY-STEP BUILD

### FASE 1: Foundation (2-3 jam)
```
1. laravel new tokoemas
2. composer require filament/filament
3. php artisan filament:install --panels
4. composer require bezhansalleh/filament-shield
5. composer require maatwebsite/excel
6. composer require barryvdh/laravel-dompdf
7. npm install
8. Setup Tailwind + Poppins font
```

### FASE 2: Database (2-3 jam)
```
1. Buat semua migration (17+ tabel)
2. Jalankan php artisan migrate
3. Buat semua Model dengan relationships
4. Buat Seeder data awal
```

### FASE 3: Filament Resources (4-6 jam)
```
1. php artisan make:filament-resource Product
2. php artisan make:filament-resource Category
3. ... (semua resource)
4. Setup form schema dengan validation
5. Setup table columns dengan filter
6. Tambahkan actions (Edit, Delete, Export)
```

### FASE 4: Filament Shield (1 jam)
```
1. php artisan shield:install
2. php artisan shield:generate --all
3. Configure permissions per role
4. Test akses setiap role
```

### FASE 5: Livewire POS (4-6 jam)
```
1. php artisan make:livewire Pos
2. Build UI dengan Tailwind (desktop + mobile)
3. Implement cart logic
4. Implement checkout logic
5. Integrate dengan Filament sebagai Page
```

### FASE 6: Observers & Automation (2-3 jam)
```
1. Buat TransactionObserver
2. Buat BuybackObserver
3. Buat ProductObserver
4. Register di EventServiceProvider
```

### FASE 7: Notifications (2-3 jam)
```
1. Setup database notification channel
2. Buat Notification classes
3. Implement bell icon di Filament
4. Test semua notification triggers
```

### FASE 8: Approval System (2-3 jam)
```
1. Buat halaman DaftarApproval
2. Implement approve/reject actions
3. Connect dengan notifications
4. Test full flow
```

### FASE 9: Cetak Struk (2-3 jam)
```
1. Buat PDF template
2. Implement thermal print via ESC/POS
3. Test di berbagai printer
```

### FASE 10: Mobile Optimization (2-3 jam)
```
1. Review semua halaman di mobile
2. Adjust columns visibleFrom('md')
3. Test floating cart di POS
4. Test semua modal/form di mobile
```

### FASE 11: Testing & Bug Fix (2-4 jam)
```
1. Test semua fitur end-to-end
2. Test dengan 3 role berbeda
3. Fix bugs
4. Performance check
```

### FASE 12: Deployment (2-3 jam)
```
1. Buat script migrate-database.php
2. Buat script shield-setup.php
3. Setup .htaccess security
4. Upload ke hosting
5. Test di production
```

---

## ⏱️ ESTIMASI TOTAL

| Fase | Estimasi |
|------|----------|
| Foundation | 2-3 jam |
| Database | 2-3 jam |
| Filament Resources | 4-6 jam |
| Shield | 1 jam |
| Livewire POS | 4-6 jam |
| Observers | 2-3 jam |
| Notifications | 2-3 jam |
| Approval | 2-3 jam |
| Cetak Struk | 2-3 jam |
| Mobile Opt | 2-3 jam |
| Testing | 2-4 jam |
| Deployment | 2-3 jam |
| **TOTAL** | **27-41 jam** |

---

## 📝 CHECKLIST FINAL

- [ ] Login/Logout berfungsi
- [ ] 3 Role dengan permission berbeda
- [ ] CRUD semua resource
- [ ] POS mobile-friendly
- [ ] Checkout & cetak struk
- [ ] Buyback dengan approval
- [ ] Notifikasi muncul
- [ ] Stok otomatis berkurang
- [ ] Cash flow otomatis tercatat
- [ ] Export Excel berfungsi
- [ ] Dashboard widgets muncul
- [ ] Dark mode
- [ ] Responsive di semua device

---

**Dibuat:** 8 Januari 2026  
**Untuk:** Dokumentasi Teknis & Skripsi
