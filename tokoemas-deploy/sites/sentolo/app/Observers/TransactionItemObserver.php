<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\TransactionItem;

class TransactionItemObserver
{
    public function created(TransactionItem $transactionItem): void
    {
        $product = Product::withTrashed()->find($transactionItem->product_id);
        
        if ($product) {
            $product->decrement('stock', $transactionItem->quantity);
        }
    }

    public function updated(TransactionItem $transactionItem): void
    {
        $product = Product::withTrashed()->find($transactionItem->product_id);
        
        if ($product && $transactionItem->isDirty('quantity')) {
            $originalQuantity = $transactionItem->getOriginal('quantity');
            $newQuantity = $transactionItem->quantity;
            $quantityChange = $newQuantity - $originalQuantity; // Hitung selisihnya
            
            // Jika quantityChange positif (beli lebih banyak), kurangi stok
            // Jika quantityChange negatif (retur), tambah stok
            $product->decrement('stock', $quantityChange);
        }
    }

    public function deleted(TransactionItem $transactionItem): void
    {
        $product = Product::withTrashed()->find($transactionItem->product_id);
        
        if ($product) {
            $product->increment('stock', $transactionItem->quantity);
        }
    }
}