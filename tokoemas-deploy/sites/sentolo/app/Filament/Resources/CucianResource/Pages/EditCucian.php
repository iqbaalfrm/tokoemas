<?php

namespace App\Filament\Resources\CucianResource\Pages;

use App\Filament\Resources\CucianResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class EditCucian extends EditRecord
{
    protected static string $resource = CucianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
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
                        'update',
                        'Cucian',
                        CucianResource::getUrl('edit', ['record' => $this->record])
                    )
                );
            }
        }
    }
}
