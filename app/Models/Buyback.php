<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyback extends Model
{
    use HasFactory;

    protected $table = 'buybacks';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function buybackItems(): HasMany
    {
        return $this->hasMany(BuybackItem::class);
    }
}