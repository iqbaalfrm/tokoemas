<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\GoldPurity;

use App\Models\SubCategory;
use App\Models\TransactionItem;
use App\Models\Concerns\BelongsToTenant;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'store_code',
        'sub_category_id',
        'name',
        'stock',
        'cost_price',
        'selling_price',
        'gold_type',
        'gold_karat',
        'weight_gram',
        'image',
        'barcode',
        'sku',
        'description',
        'is_active',
    ];

    protected $casts = [
        'weight_gram' => 'float',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function transactionItems() {
        return $this->hasMany(TransactionItem::class);
    }

    public function goldPurity()
    {
        return $this->belongsTo(GoldPurity::class);
    }
}