<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class EditInventory extends EditRecord
{
    protected static string $resource = InventoryResource::class;

    protected function getFormQuery(): Builder
    {
        return parent::getFormQuery()->with('inventoryItems.product');
    }

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
                        'Inventori',
                        InventoryResource::getUrl('edit', ['record' => $this->record])
                    )
                );
            }
        }
    }
}