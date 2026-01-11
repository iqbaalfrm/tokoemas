<?php

namespace App\Observers;

use App\Filament\Resources\CucianResource;
use App\Models\Cucian;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CucianObserver
{
    /**
     * Handle the Cucian "deleted" event.
     */
    public function deleted(Cucian $cucian): void
    {
        // Kirim notifikasi ke superadmin jika bukan superadmin yang menghapus
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $cucian,
                        'delete',
                        'Cucian',
                        CucianResource::getUrl('index')
                    )
                );
            }
        }
    }
}

