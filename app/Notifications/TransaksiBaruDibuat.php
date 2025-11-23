<?php

namespace App\Notifications;

use App\Models\Transaction;
use Filament\Notifications\Notification as FilamentNotification;
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
        $userName = auth()->user()->name ?? 'Kasir';
        $title = 'Transaksi Baru';
        $message = "Transaksi baru telah dibuat oleh {$userName} - No. Transaksi: {$this->transaction->transaction_number}";
        
        // Menggunakan Filament Notification Builder untuk mendapatkan format yang benar
        $notification = FilamentNotification::make()
            ->title($title)
            ->body($message)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->url(route('filament.admin.resources.transactions.edit', $this->transaction))
                    ->markAsRead(),
            ]);

        $databaseMessage = $notification->getDatabaseMessage();

        // Add custom data fields that our custom notification component expects
        $databaseMessage['message'] = $message;
        $databaseMessage['title'] = $title;
        $databaseMessage['transaction_id'] = $this->transaction->id;
        $databaseMessage['url'] = route('filament.admin.resources.transactions.edit', $this->transaction);

        return $databaseMessage;
    }
}