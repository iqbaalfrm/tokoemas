<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TransactionItem;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'payment_method_id', 'transaction_number', 'name', 'email', 'phone',
        'address', 'notes', 'total', 'cash_received', 'change'
    ];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function products()
    {
        return $this->transactionItems()->with('product');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function logs()
    {
        return $this->morphMany(\App\Models\TransactionLog::class, 'model');
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            \App\Models\TransactionLog::create([
                'model_type' => \App\Models\Transaction::class,
                'model_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'Dibuat',
                'description' => 'Transaksi dibuat dengan nomor ' . $transaction->transaction_number
            ]);
        });

        static::updated(function ($transaction) {
            $changes = $transaction->getChanges();

            // Check for approval changes
            if (isset($changes['approved_by']) || isset($changes['approved_at'])) {
                $approver = $transaction->approver()->first();
                \App\Models\TransactionLog::create([
                    'model_type' => \App\Models\Transaction::class,
                    'model_id' => $transaction->id,
                    'user_id' => auth()->id(),
                    'action' => 'Disetujui',
                    'description' => 'Transaksi disetujui oleh ' . ($approver ? $approver->name : 'admin')
                ]);
            }
        });
    }
}