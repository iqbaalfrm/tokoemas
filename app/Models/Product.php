<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\GoldPrice;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'stock',
        'cost_price',
        'gold_type',
        'weight_gram',
        'image',
        'barcode',
        'sku',
        'description',
        'is_active',
        'selling_price',
        'profit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight_gram' => 'float',
    ];

    // Relasi ke model Category
    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke model TransactionItem
    public function transactionItems() {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Accessor untuk mendapatkan harga per gram terbaru sesuai jenis emas produk.
     * Berguna untuk menampilkan harga pasar saat ini di tabel.
     */
    public function getCurrentPricePerGramAttribute(): ?int
    {
        if (!$this->gold_type) {
            return null;
        }

        // Asumsi Anda punya scope 'latestFor' di model GoldPrice
        $gp = GoldPrice::where('jenis_emas', $this->gold_type)
                       ->orderBy('tanggal', 'desc')
                       ->first();

        return $gp?->harga_per_gram ? (int) $gp->harga_per_gram : null;
    }

    /**
     * Accessor untuk menghitung total harga produk berdasarkan harga emas TERBARU.
     * Berguna untuk kolom 'computed_price' di tabel Anda.
     */
    public function getComputedPriceAttribute(): ?int
    {
        if (!$this->gold_type || !$this->weight_gram) {
            return null;
        }
        
        $pricePerGram = $this->current_price_per_gram;
        
        return $pricePerGram ? (int) round($this->weight_gram * $pricePerGram) : null;
    }
}