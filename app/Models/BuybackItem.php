<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuybackItem extends Model
{
    use HasFactory;

    protected $table = 'buyback_items';

    protected $guarded = [];

    protected $casts = [
        'foto' => 'string',
    ];

    public function buyback(): BelongsTo
    {
        return $this->belongsTo(Buyback::class);
    }
}
