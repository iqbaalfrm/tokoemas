-- =============================================
-- DATA AWAL UNTUK APLIKASI KASIR TOKO EMAS
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Users (3 akun: Super Admin, Admin, Kasir)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@gmail.com', NOW(), '$2y$12$I1ksI1bKG8l7R/qkO2nc5ONG9y7/Jkj21PUrAnPCOkDvPHxyGtsW6', NULL, NOW(), NOW()),
(2, 'Admin Toko', 'admin@gmail.com', NOW(), '$2y$12$I1ksI1bKG8l7R/qkO2nc5ONG9y7/Jkj21PUrAnPCOkDvPHxyGtsW6', NULL, NOW(), NOW()),
(3, 'Kasir', 'kasir@gmail.com', NOW(), '$2y$12$I1ksI1bKG8l7R/qkO2nc5ONG9y7/Jkj21PUrAnPCOkDvPHxyGtsW6', NULL, NOW(), NOW());

-- Roles (3 role)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', NOW(), NOW()),
(2, 'admin', 'web', NOW(), NOW()),
(3, 'kasir', 'web', NOW(), NOW());

-- Assign roles ke users
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3);

-- Settings Toko
INSERT INTO `settings` (`id`, `logo`, `name`, `phone`, `address`, `print_via_bluetooth`, `name_printer_local`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Toko Emas Wates', '08123456789', 'Jl. Wates No. 123, Yogyakarta', 0, NULL, NOW(), NOW());

-- Kategori Produk
INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Emas', NOW(), NOW(), NULL),
(2, 'Perak', NOW(), NOW(), NULL),
(3, 'Perhiasan', NOW(), NOW(), NULL);

-- Sub Kategori
INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Emas 24 Karat', 'Emas murni 24 karat', NOW(), NOW(), NULL),
(2, 1, 'Emas 22 Karat', 'Emas 22 karat', NOW(), NOW(), NULL),
(3, 1, 'Emas 18 Karat', 'Emas 18 karat', NOW(), NOW(), NULL),
(4, 2, 'Perak 925', 'Perak sterling 925', NOW(), NOW(), NULL),
(5, 3, 'Cincin', 'Cincin emas/perak', NOW(), NOW(), NULL),
(6, 3, 'Kalung', 'Kalung emas/perak', NOW(), NOW(), NULL),
(7, 3, 'Gelang', 'Gelang emas/perak', NOW(), NOW(), NULL),
(8, 3, 'Anting', 'Anting emas/perak', NOW(), NOW(), NULL);

-- Payment Methods
INSERT INTO `payment_methods` (`id`, `name`, `image`, `is_cash`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tunai', NULL, 1, NOW(), NOW(), NULL),
(2, 'Transfer Bank', NULL, 0, NOW(), NOW(), NULL),
(3, 'QRIS', NULL, 0, NOW(), NOW(), NULL);

-- Gold Purities (Kadar Emas)
INSERT INTO `gold_purities` (`id`, `name`, `purity_percentage`, `created_at`, `updated_at`) VALUES
(1, '24 Karat', 99.99, NOW(), NOW()),
(2, '23 Karat', 95.83, NOW(), NOW()),
(3, '22 Karat', 91.67, NOW(), NOW()),
(4, '21 Karat', 87.50, NOW(), NOW()),
(5, '20 Karat', 83.33, NOW(), NOW()),
(6, '18 Karat', 75.00, NOW(), NOW()),
(7, '17 Karat', 70.83, NOW(), NOW()),
(8, '16 Karat', 66.67, NOW(), NOW());

-- Permissions (basic)
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view_any_product', 'web', NOW(), NOW()),
(2, 'create_product', 'web', NOW(), NOW()),
(3, 'update_product', 'web', NOW(), NOW()),
(4, 'delete_product', 'web', NOW(), NOW()),
(5, 'view_any_transaction', 'web', NOW(), NOW()),
(6, 'create_transaction', 'web', NOW(), NOW()),
(7, 'view_any_category', 'web', NOW(), NOW()),
(8, 'create_category', 'web', NOW(), NOW()),
(9, 'update_category', 'web', NOW(), NOW()),
(10, 'delete_category', 'web', NOW(), NOW()),
(11, 'view_any_setting', 'web', NOW(), NOW()),
(12, 'update_setting', 'web', NOW(), NOW()),
(13, 'view_any_report', 'web', NOW(), NOW()),
(14, 'view_any_user', 'web', NOW(), NOW()),
(15, 'create_user', 'web', NOW(), NOW()),
(16, 'update_user', 'web', NOW(), NOW()),
(17, 'delete_user', 'web', NOW(), NOW()),
(18, 'view_any_inventory', 'web', NOW(), NOW()),
(19, 'create_inventory', 'web', NOW(), NOW()),
(20, 'view_any_cash_flow', 'web', NOW(), NOW()),
(21, 'create_cash_flow', 'web', NOW(), NOW()),
(22, 'view_any_buyback', 'web', NOW(), NOW()),
(23, 'create_buyback', 'web', NOW(), NOW()),
(24, '_Dashboard', 'web', NOW(), NOW()),
(25, '_PosPage', 'web', NOW(), NOW()),
(26, 'view_any_approval', 'web', NOW(), NOW()),
(27, 'approve_transaction', 'web', NOW(), NOW()),
(28, 'approve_buyback', 'web', NOW(), NOW()),
(29, 'view_any_gold_price', 'web', NOW(), NOW()),
(30, 'update_gold_price', 'web', NOW(), NOW());

-- Assign permissions ke super_admin (semua permission)
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1), (8, 1), (9, 1), (10, 1),
(11, 1), (12, 1), (13, 1), (14, 1), (15, 1), (16, 1), (17, 1), (18, 1), (19, 1), (20, 1),
(21, 1), (22, 1), (23, 1), (24, 1), (25, 1), (26, 1), (27, 1), (28, 1), (29, 1), (30, 1);

-- Assign permissions ke admin (tanpa approval, user management terbatas)
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 2), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2), (7, 2), (8, 2), (9, 2), (10, 2),
(13, 2), (18, 2), (19, 2), (20, 2), (21, 2), (22, 2), (23, 2), (24, 2), (25, 2), (29, 2);

-- Assign permissions ke kasir (hanya POS, transaksi, buyback)
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 3), (5, 3), (6, 3), (22, 3), (23, 3), (24, 3), (25, 3), (29, 3);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- AKUN LOGIN:
-- Super Admin: superadmin@gmail.com / password
-- Admin: admin@gmail.com / password
-- Kasir: kasir@gmail.com / password
-- =============================================
