<?php

namespace App\Observers;

use App\Filament\Resources\BuybackResource;
use App\Models\Buyback;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class BuybackObserver
{
    /**
     * Handle the Buyback "deleted" event.
     */
    public function deleted(Buyback $buyback): void
    {
        // Kirim notifikasi ke superadmin jika bukan superadmin yang menghapus
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $buyback,
                        'delete',
                        'Buyback',
                        BuybackResource::getUrl('index')
                    )
                );
            }
        }
    }
}

