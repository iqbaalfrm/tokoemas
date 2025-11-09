<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

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
}