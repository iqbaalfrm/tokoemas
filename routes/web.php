<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;

Route::get('/clear-pos-cart', function () {
    session()->forget('orderItems');
    return 'Keranjang POS sudah dibersihkan. Silakan kembali ke halaman POS.';
});

Route::get('/invoice/{id}/pdf', [TransactionController::class, 'printInvoice'])->name('invoice.pdf');