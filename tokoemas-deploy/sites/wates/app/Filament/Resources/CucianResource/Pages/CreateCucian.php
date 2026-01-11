<?php

namespace App\Filament\Resources\CucianResource\Pages;

use App\Filament\Resources\CucianResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreateCucian extends CreateRecord
{
    protected static string $resource = CucianResource::class;

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
                        'Cucian',
                        CucianResource::getUrl('edit', ['record' => $this->record])
                    )
                );
            }
        }
    }
}
