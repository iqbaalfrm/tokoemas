<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\TransactionItem;

class Member extends Model
{
    use HasFactory;

    /**
     * Koneksi database untuk member (shared across all stores)
     */
    protected $connection = 'member';

    protected $fillable = ['nama', 'no_hp', 'alamat', 'email'];

    /**
     * Note: Relasi transactions() tidak bisa digunakan karena
     * transactions berada di database terpisah per-toko (wates/sentolo).
     * 
     * Untuk mengambil transaksi member, gunakan query manual:
     * Transaction::where('member_id', $member->id)->get();
     */
}