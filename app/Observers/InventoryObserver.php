<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\CashFlow;
use Illuminate\Support\Str;

class InventoryObserver
{
    public function creating(Inventory $inventory): void
    {
        $today = now()->format('Ymd');
        $prefix = 'INV-' . $today . '-';

        $latest = Inventory::where('reference_number', 'LIKE', $prefix . '%')
                           ->orderBy('reference_number', 'desc')
                           ->first();
        
        $nextCount = 1;
        if ($latest) {
            $lastCount = (int) Str::afterLast($latest->reference_number, '-');
            $nextCount = $lastCount + 1;
        }
        
        $inventory->reference_number = $prefix . str_pad($nextCount, 2, '0', STR_PAD_LEFT);
        
        if (is_null($inventory->total)) {
            $inventory->total = 0;
        }
    }

    public function created(Inventory $inventory): void
    {
        if ($inventory->type === 'in' && $inventory->source === 'purchase_stock') {
            
            $totalCost = 0;
            foreach ($inventory->inventoryItems as $item) {
                $totalCost += $item->cost_price * $item->quantity;
            }

            if ($totalCost > 0) {
                CashFlow::create([
                    'date' => $inventory->created_at ?? now(),
                    'type' => 'expense',
                    'source' => 'purchase_stock',
                    'amount' => $totalCost, 
                    'notes' => 'Otomatis dari penambahan stok Inventory: ' . $inventory->reference_number,
                    'inventory_id' => $inventory->id, 
                ]);
            }
        }
    }

    
    public function updated(Inventory $inventory): void
    {
        if ($inventory->source === 'purchase_stock') {
            
            $inventory->refresh(); // Ambil data item terbaru
            
            $totalCost = 0;
            foreach ($inventory->inventoryItems as $item) {
                $totalCost += $item->cost_price * $item->quantity;
            }

            CashFlow::updateOrCreate(
                ['inventory_id' => $inventory->id], // Cari berdasarkan ini
                [
                    'date' => $inventory->updated_at ?? now(),
                    'type' => 'expense',
                    'source' => 'purchase_stock',
                    'amount' => $totalCost, // Update amount-nya
                    'notes' => 'Update stok Inventory: ' . $inventory->reference_number,
                ]
            );
        }
    }

    public function deleted(Inventory $inventory): void
    {
        CashFlow::where('inventory_id', $inventory->id)->delete();
    }
}