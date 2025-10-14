<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear-pos-cart', function () {
    session()->forget('orderItems');
    return 'Keranjang POS sudah dibersihkan. Silakan kembali ke halaman POS.';
});