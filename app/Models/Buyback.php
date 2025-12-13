<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyback extends Model
{
    use HasFactory;

    protected $table = 'buybacks';

    protected $fillable = [
        'tanggal',
        'tipe',
        'berat_total',
        'total_amount_paid',
        'catatan',
        'customer_name',
        'customer_address',
        'customer_phone',
        'ktp_image',
        'processed_by_user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function buybackItems(): HasMany
    {
        return $this->hasMany(BuybackItem::class);
    }

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by_user_id');
    }

    public function approver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\TransactionLog::class, 'model');
    }

    protected static function booted()
    {
        static::created(function ($buyback) {
            \App\Models\TransactionLog::create([
                'model_type' => \App\Models\Buyback::class,
                'model_id' => $buyback->id,
                'user_id' => auth()->id(),
                'action' => 'Dibuat',
                'description' => 'Buyback dibuat untuk pelanggan ' . $buyback->customer_name
            ]);
        });

        static::updated(function ($buyback) {
            $changes = $buyback->getChanges();

            // Check for approval changes
            if (isset($changes['approved_by']) || isset($changes['approved_at'])) {
                $approver = $buyback->approver()->first();
                \App\Models\TransactionLog::create([
                    'model_type' => \App\Models\Buyback::class,
                    'model_id' => $buyback->id,
                    'user_id' => auth()->id(),
                    'action' => 'Disetujui',
                    'description' => 'Buyback disetujui oleh ' . ($approver ? $approver->name : 'admin')
                ]);
            }
        });
    }
}