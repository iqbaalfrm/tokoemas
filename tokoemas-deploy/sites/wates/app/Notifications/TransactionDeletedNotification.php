<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class TransactionDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $transactionData;
    public $deleterName;
    public $deletedAt;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction, $deleterName)
    {
        // Simpan data penting karena modelnya sudah dihapus (soft delete atau force delete)
        $this->transactionData = [
            'number' => $transaction->transaction_number,
            'total' => $transaction->total,
            'customer_name' => $transaction->customer_name ?? 'Umum',
        ];
        $this->deleterName = $deleterName;
        $this->deletedAt = now();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ PERINGATAN: Transaksi Dihapus')
            ->greeting('Halo Admin,')
            ->line('Sebuah transaksi telah dihapus dari sistem. Berikut detailnya:')
            ->line('📝 No. Transaksi: ' . $this->transactionData['number'])
            ->line('💰 Total Nilai: Rp ' . number_format($this->transactionData['total'], 0, ',', '.'))
            ->line('👤 Pelanggan: ' . $this->transactionData['customer_name'])
            ->line('🗑️ Dihapus Oleh: ' . $this->deleterName)
            ->line('⏰ Waktu: ' . $this->deletedAt->format('d/m/Y H:i'))
            ->line('Mohon periksa sistem jika ini adalah aktivitas mencurigakan.')
            ->priority(1);
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('⚠️ Transaksi Dihapus')
            ->body("Transaksi #{$this->transactionData['number']} dihapus oleh {$this->deleterName}.")
            ->danger()
            ->icon('heroicon-o-trash')
            ->getDatabaseMessage();
    }
}
