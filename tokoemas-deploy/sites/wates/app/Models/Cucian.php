<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cucian extends Model
{
    use HasFactory;

    protected $fillable = ['tanggal', 'status', 'berat_total', 'catatan'];

    protected $casts = ['tanggal' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(CucianItem::class);
    }
}