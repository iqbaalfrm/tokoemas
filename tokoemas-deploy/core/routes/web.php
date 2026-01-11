<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-pos-cart', function () {
    session()->forget('orderItems');
    return 'Keranjang POS sudah dibersihkan. Silakan kembali ke halaman POS.';
});

Route::get('/invoice/{id}/pdf', [TransactionController::class, 'printInvoice'])->name('invoice.pdf');

// Route untuk serve file storage tanpa symlink (untuk hosting tanpa SSH)
Route::get('/storage/{path}', [StorageController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve');

Route::get('/update-server', function() {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');
        
        // Buat direktori storage jika belum ada
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');
        $directories = ['products', 'receipts', 'ktp'];
        foreach ($directories as $dir) {
            $path = storage_path('app/public/' . $storeCode . '/' . $dir);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
        
        return "<h1>SUKSES!</h1><br>Database Updated.<br>Cache Cleared.<br>Storage Directories Created for {$storeCode}.";
    } catch (\Exception $e) {
        return "<h1>ERROR</h1>" . $e->getMessage();
    }
});