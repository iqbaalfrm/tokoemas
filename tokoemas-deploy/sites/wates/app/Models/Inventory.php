<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToTenant;

class Inventory extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['store_code', 'type', 'source', 'total', 'total_cost', 'notes'];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
}

