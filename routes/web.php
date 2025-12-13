<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-pos-cart', function () {
    session()->forget('orderItems');
    return 'Keranjang POS sudah dibersihkan. Silakan kembali ke halaman POS.';
});

Route::get('/invoice/{id}/pdf', [TransactionController::class, 'printInvoice'])->name('invoice.pdf');

Route::get('/update-server', function() {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('storage:link');
        Artisan::call('optimize:clear');
        return "<h1>SUKSES!</h1><br>Database Updated.<br>Storage Linked.<br>Cache Cleared.";
    } catch (\Exception $e) {
        return "<h1>ERROR</h1>" . $e->getMessage();
    }
});