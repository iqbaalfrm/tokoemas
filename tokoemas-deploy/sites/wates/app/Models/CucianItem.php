<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CucianItem extends Model
{
    use HasFactory;

    protected $fillable = ['cucian_id', 'nama_produk', 'berat'];
}