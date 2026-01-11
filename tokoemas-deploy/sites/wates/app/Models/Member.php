<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\TransactionItem;

class Member extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'no_hp', 'alamat', 'email'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'member_id');
    }
    
    public function transactionItems(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(TransactionItem::class, Transaction::class);
    }
}