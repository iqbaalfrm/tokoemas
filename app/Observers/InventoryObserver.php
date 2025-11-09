<?php

namespace App\Observers;

use App\Models\Inventory;
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
        if (is_null($inventory->total_cost)) {
            $inventory->total_cost = 0;
        }
    }
}