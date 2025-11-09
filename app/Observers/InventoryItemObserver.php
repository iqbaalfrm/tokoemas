<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;

class InventoryItemObserver
{
    public function created(InventoryItem $inventoryItem): void
    {
        $this->recalculateInventoryCost($inventoryItem->inventory);
    }

    public function updated(InventoryItem $inventoryItem): void
    {
        $this->recalculateInventoryCost($inventoryItem->inventory);
    }

    public function deleted(InventoryItem $inventoryItem): void
    {
        $this->recalculateInventoryCost($inventoryItem->inventory);
    }

    private function recalculateInventoryCost(?Inventory $inventory): void
    {
        if (!$inventory) {
            return;
        }
        
        $totalCost = $inventory->inventoryItems()->sum(DB::raw('cost_price * quantity'));

        $inventory->total = $totalCost;
        $inventory->total_cost = $totalCost;
        $inventory->saveQuietly();

        if ($inventory->type === 'in' && $inventory->source === 'purchase_stock') {
            
            if ($totalCost > 0) {
                CashFlow::updateOrCreate(
                    [
                        'inventory_id' => $inventory->id
                    ],
                    [
                        'date' => $inventory->updated_at ?? now(),
                        'type' => 'expense',
                        'source' => 'purchase_stock',
                        'amount' => $totalCost,
                        'notes' => 'Otomatis dari stok Inventory: ' . $inventory->reference_number,
                    ]
                );
            } else {
                CashFlow::where('inventory_id', $inventory->id)->delete();
            }
        }
    }
}