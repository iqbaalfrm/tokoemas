<?php

namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function afterCreate(): void
    {
        $user = auth()->user();
        
        // Hanya kirim notifikasi jika bukan superadmin
        if (!$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $this->record,
                        'create',
                        'Metode Pembayaran',
                        route('filament.admin.resources.payment-methods.edit', $this->record)
                    )
                );
            }
        }
    }
}
