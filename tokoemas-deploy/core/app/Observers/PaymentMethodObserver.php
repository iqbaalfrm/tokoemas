<?php

namespace App\Observers;

use App\Models\PaymentMethod;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class PaymentMethodObserver
{
    /**
     * Handle the PaymentMethod "deleted" event.
     */
    public function deleted(PaymentMethod $paymentMethod): void
    {
        // Kirim notifikasi ke superadmin jika bukan superadmin yang menghapus
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $paymentMethod,
                        'delete',
                        'Metode Pembayaran',
                        route('filament.admin.resources.payment-methods.index')
                    )
                );
            }
        }
    }
}

