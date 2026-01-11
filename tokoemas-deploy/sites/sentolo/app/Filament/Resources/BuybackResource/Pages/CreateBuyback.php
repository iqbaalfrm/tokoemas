<?php

namespace App\Filament\Resources\BuybackResource\Pages;

use App\Filament\Resources\BuybackResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreateBuyback extends CreateRecord
{
    protected static string $resource = BuybackResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['processed_by_user_id'] = auth()->id();
        return $data;
    }

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
                        'Buyback',
                        BuybackResource::getUrl('edit', ['record' => $this->record])
                    )
                );
            }
        }
    }
}
