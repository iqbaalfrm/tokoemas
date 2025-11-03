<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\CashFlow;

class InventoryObserver
{
    public function creating(Inventory $inventory): void
    {
        $today = now()->format('Ymd');
        $countToday = Inventory::whereDate('created_at', today())
            ->count() + 1;

        $inventory->reference_number = 'INV-' . $today . '-' . str_pad($countToday, 2, '0', STR_PAD_LEFT);
        
        if (is_null($inventory->total)) {
            $inventory->total = 0;
        }
    }

    public function created(Inventory $inventory): void
    {
        if ($inventory->type === 'in' && $inventory->source === 'purchase_stock' && $inventory->total_cost > 0) {
            CashFlow::create([
                'date' => $inventory->created_at ?? now(),
                'type' => 'expense',
                'source' => 'purchase_stock',
                'amount' => $inventory->total_cost, 
                'notes' => 'Otomatis dari penambahan stok Inventory: ' . $inventory->reference_number,
            ]);
        } 

    }

    
    public function updated(Inventory $inventory): void
    {
        if ($inventory->isDirty('total_cost') && $inventory->source === 'purchase_stock') {
            CashFlow::where('notes', 'like', "%{$inventory->reference_number}%")
                ->update([
                    'amount' => $inventory->total_cost,
                ]);
        }
    }


    public function deleted(Inventory $inventory): void
    {
        CashFlow::where('notes', 'like', "%{$inventory->reference_number}%")->delete();
    }

}