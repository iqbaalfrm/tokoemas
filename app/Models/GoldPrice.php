<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis_emas',      // Pastikan ini ada
        'harga_per_gram',  // Pastikan ini ada
        'tanggal',         // Pastikan ini ada
    ];
}