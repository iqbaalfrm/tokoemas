<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

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
                        'Kategori',
                        route('filament.admin.resources.categories.edit', $this->record)
                    )
                );
            }
        }
    }
}
