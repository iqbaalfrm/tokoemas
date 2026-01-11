-- Disable FK checks
SET FOREIGN_KEY_CHECKS = 0;

-- Reset Tables
TRUNCATE TABLE buyback_items;
TRUNCATE TABLE buybacks;
TRUNCATE TABLE transaction_items;
TRUNCATE TABLE transactions;
TRUNCATE TABLE cash_flows;
TRUNCATE TABLE inventories;
TRUNCATE TABLE products;
TRUNCATE TABLE sub_categories;
TRUNCATE TABLE categories;
TRUNCATE TABLE gold_purities;
TRUNCATE TABLE gold_prices;
TRUNCATE TABLE members;
TRUNCATE TABLE payment_methods;

-- 1. Payment Methods
INSERT INTO payment_methods (id, name, is_active, created_at, updated_at) VALUES
(1, 'Tunai', 1, NOW(), NOW()),
(2, 'Transfer BCA', 1, NOW(), NOW()),
(3, 'Transfer BRI', 1, NOW(), NOW()),
(4, 'QRIS', 1, NOW(), NOW());

-- 2. Categories
INSERT INTO categories (id, name, created_at, updated_at) VALUES 
(1, 'Perhiasan Wanita', NOW(), NOW()),
(2, 'Perhiasan Pria', NOW(), NOW()),
(3, 'Logam Mulia', NOW(), NOW());

-- 3. Sub Categories
INSERT INTO sub_categories (id, category_id, name, code, created_at, updated_at) VALUES
(1, 1, 'Cincin', 'CIN', NOW(), NOW()),
(2, 1, 'Kalung', 'KAL', NOW(), NOW()),
(3, 1, 'Gelang Rantai', 'GEL', NOW(), NOW()),
(4, 1, 'Anting', 'ANT', NOW(), NOW()),
(5, 3, 'Logam Mulia Antam', 'LMA', NOW(), NOW()),
(6, 3, 'Logam Mulia UBS', 'LMU', NOW(), NOW());

-- 4. Gold Purities (Kadar)
INSERT INTO gold_purities (id, name, description, created_at, updated_at) VALUES
(1, '24 Karat (99.9%)', 'Logam Mulia Murni', NOW(), NOW()),
(2, '23 Karat (95%)', 'Emas Tua Kualitas Tinggi', NOW(), NOW()),
(3, '22 Karat (90%)', 'Emas Tua Standar', NOW(), NOW()),
(4, '17 Karat (700)', 'Emas Tua 70%', NOW(), NOW()),
(5, '16 Karat (375)', 'Emas Muda', NOW(), NOW()),
(6, '8 Karat (300)', 'Emas Muda Ekonomis', NOW(), NOW());

-- 5. Members
INSERT INTO members (id, name, phone, address, created_at, updated_at) VALUES
(1, 'Budi Santoso', '081234567890', 'Jl. Wates Km 5, Yogyakarta', NOW(), NOW()),
(2, 'Siti Aminah', '089876543210', 'Jl. Kaliurang Km 10, Sleman', NOW(), NOW()),
(3, 'Pelanggan Umum', '-', '-', NOW(), NOW());

-- 6. Gold Prices (Harga Harian)
INSERT INTO gold_prices (id, jenis_emas, harga_per_gram, tanggal, created_at, updated_at) VALUES
(1, 'Emas Tua', 950000, CURDATE(), NOW(), NOW()),
(2, 'Emas Muda', 450000, CURDATE(), NOW(), NOW());

-- 7. Products
-- store_code = 'wates'
-- selling_price = manual
INSERT INTO products (
    id, store_code, sub_category_id, gold_purity_id, name, stock, cost_price, selling_price,
    gold_type, weight_gram, barcode, sku, is_active, created_at, updated_at
) VALUES
(1, 'wates', 1, 4, 'Cincin Kawin Polos 2g', 10, 1400000, 1750000, 'Emas Tua', 2.0, '899001', 'CIN-001', 1, NOW(), NOW()),
(2, 'wates', 1, 4, 'Cincin Permata Merah 3g', 5, 2100000, 2600000, 'Emas Tua', 3.0, '899002', 'CIN-002', 1, NOW(), NOW()),
(3, 'wates', 2, 4, 'Kalung Nori 5g', 8, 3500000, 4250000, 'Emas Tua', 5.0, '899003', 'KAL-001', 1, NOW(), NOW()),
(4, 'wates', 3, 5, 'Gelang Rantai Sisik Naga 4g', 15, 1400000, 1900000, 'Emas Muda', 4.0, '899004', 'GEL-001', 1, NOW(), NOW()),
(5, 'wates', 4, 5, 'Anting Toge 1g', 20, 350000, 500000, 'Emas Muda', 1.0, '899005', 'ANT-001', 1, NOW(), NOW()),
(6, 'wates', 5, 1, 'LM Antam 1 Gram', 50, 1300000, 1350000, 'Emas Tua', 1.0, '899006', 'LMA-001', 1, NOW(), NOW()),
(7, 'wates', 5, 1, 'LM Antam 5 Gram', 20, 6400000, 6600000, 'Emas Tua', 5.0, '899007', 'LMA-002', 1, NOW(), NOW());

-- 8. Transactions (Contoh Transaksi Penjualan)
-- Transaksi 1: Budi beli Cincin Kawin
INSERT INTO transactions (
    id, store_code, payment_method_id, transaction_number, name, phone, address, 
    notes, total, cash_received, `change`, member_id, created_at, updated_at
) VALUES
(1, 'wates', 1, 'TRX-20260108-001', 'Budi Santoso', '081234567890', 'Jl. Wates Km 5', 
 'Pembelian Cincin Nikah', 1750000, 1800000, 50000, 1, NOW(), NOW());

-- Transaction Items for TRX 1
INSERT INTO transaction_items (
    id, transaction_id, product_id, product_name, quantity, price, 
    subtotal, created_at, updated_at
) VALUES
(1, 1, 1, 'Cincin Kawin Polos 2g', 1, 1750000, 1750000, NOW(), NOW());

-- Enable FK checks
SET FOREIGN_KEY_CHECKS = 1;
