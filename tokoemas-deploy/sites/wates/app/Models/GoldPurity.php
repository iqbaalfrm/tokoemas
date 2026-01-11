<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldPurity extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // Define the relationship to products
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'gold_purity_id');
    }
}
