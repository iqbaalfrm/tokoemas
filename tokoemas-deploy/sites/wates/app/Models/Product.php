<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\GoldPurity;
use App\Models\GoldPrice;
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
        'gold_type',
        'gold_karat',
        'weight_gram',
        'image',
        'barcode',
        'sku',
        'description',
        'is_active',
        'selling_price',
        'profit',
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

    public function getCurrentPricePerGramAttribute(): ?int
    {
        if (!$this->gold_type) {
            return null;
        }

        $gp = GoldPrice::where('jenis_emas', $this->gold_type)
                            ->orderBy('tanggal', 'desc')
                            ->first();

        return $gp?->harga_per_gram ? (int) $gp->harga_per_gram : null;
    }

    public function getComputedPriceAttribute(): ?int
    {
        if (!$this->gold_type || !$this->weight_gram) {
            return null;
        }

        $pricePerGram = $this->current_price_per_gram;

        return $pricePerGram ? (int) round($this->weight_gram * $pricePerGram) : null;
    }

    public function goldPurity()
    {
        return $this->belongsTo(GoldPurity::class);
    }
}