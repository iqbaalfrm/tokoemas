<?php

namespace App\Notifications;

use App\Models\Transaction;
//use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransaksiBaruDibuat extends Notification
{
    //use Queueable;

    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'Transaksi baru telah dibuat oleh ' . auth()->user()->name,
            'transaction_id' => $this->transaction->id,
            'url' => route('filament.admin.resources.transactions.edit', $this->transaction),
        ];
    }
}