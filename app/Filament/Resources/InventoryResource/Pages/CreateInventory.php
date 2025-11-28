<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use Filament\Actions;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InventoryResource;

class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;

    protected function afterCreate(): void
    {
        $inventory = $this->record; 


        foreach ($inventory->inventoryItems as $item) {
            $product = Product::find($item->product_id);

            if ($product) {

                if ($inventory->type === 'out') {
                    $product->decrement('stock', abs($item->quantity));
                } else {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
    }
}