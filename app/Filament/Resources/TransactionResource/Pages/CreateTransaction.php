<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User; // <-- TAMBAHKAN INI
use App\Notifications\TransaksiBaruDibuat; // <-- TAMBAHKAN INI
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Notification; // <-- TAMBAHKAN INI

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    // Properti untuk modal
    public bool $showPrintModal = false;
    public ?Transaction $newTransaction = null;

    // --- SATU FUNGSI afterCreate() YANG SUDAH DIGABUNG ---
    protected function afterCreate(): void
    {
        // 1. Ambil data transaksi yang baru saja dibuat
        $this->newTransaction = $this->record;

        // 2. Kirim notifikasi ke Superadmin
        $superAdmins = User::role('superadmin')->get();
        if ($superAdmins->isNotEmpty()) {
            Notification::send($superAdmins, new TransaksiBaruDibuat($this->record));
        }

        // 3. Beri sinyal untuk menampilkan modal
        $this->showPrintModal = true;

        // 4. Hentikan proses redirect otomatis dari Filament
        throw new Halt();
    }
}