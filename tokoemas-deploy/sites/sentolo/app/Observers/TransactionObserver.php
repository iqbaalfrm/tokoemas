<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Models\Transaction;
use App\Helpers\TransactionHelper;

class TransactionObserver
{
    public function creating(Transaction $transaction)
    {
        $transaction->transaction_number = TransactionHelper::generateUniqueTrxId();
    }
    
    public function created(Transaction $transaction)
    {
        CashFlow::create([
            'date'   => now(),
            'type'   => 'income',
            'source' => 'sales',
            'amount' => $transaction->total,
            'notes'  => 'Pemasukan dari transaksi #' . $transaction->transaction_number,
        ]);
    }

    public function updated(Transaction $transaction)
    {
        // Misalnya jika total diupdate maka perbarui juga di CashFlow
        if ($transaction->isDirty('total')) {
            CashFlow::where('notes', 'like', "%Pemasukan dari transaksi #{$transaction->transaction_number}%")
                ->update([
                    'amount' => $transaction->total,
                ]);
        }
    }

    public function deleted(Transaction $transaction)
    {
        // LOGIKA BARU: Bersihkan History (Hapus CashFlow Pemasukan Lama)
        // Agar laporan keuangan bersih dan tidak ada 'jejak' transaksi error
        CashFlow::where('notes', 'like', "%transaksi #{$transaction->transaction_number}%")
            ->where('type', 'income')
            ->delete();

        // Kembalikan stok produk
        foreach ($transaction->transactionItems as $item) {
            $product = $item->product;
            $product->stock += $item->quantity;
            $product->save();
        }

        // Kirim Notifikasi ke Super Admin (Audit Log tetap jalan untuk keamanan)
        $deleterName = auth()->user() ? auth()->user()->name : 'System';
        $superAdmins = \App\Models\User::role('super_admin')->get();
        
        foreach ($superAdmins as $admin) {
            $admin->notify(new \App\Notifications\TransactionDeletedNotification($transaction, $deleterName));
        }
    }

    public function restored(Transaction $transaction)
    {
        CashFlow::create([
            'date'   => now(),
            'type'   => 'income',
            'source' => 'restored_sales',
            'amount' => $transaction->total,
            'notes'  => 'Restore transaksi #' . $transaction->transaction_number,
        ]);

        // Kurangi lagi stok
        foreach ($transaction->transactionItems()->get() as $item) {
            $product = $item->product;
            $product->stock -= $item->quantity;
            $product->save();
        }
    }

    
    public function forceDeleting(Transaction $transaction)
    {
        if(!$transaction->trashed()){
            foreach ($transaction->transactionItems()->get() as $item) {
                $product = $item->product;
                $product->stock += $item->quantity;
                $product->save();
            }
        }

    }

    public function forceDeleted(Transaction $transaction)
    {
        // Misalnya hapus CashFlow terkait
        CashFlow::where('notes', 'like', "%transaksi #{$transaction->transaction_number}%")->delete();

    }
}
