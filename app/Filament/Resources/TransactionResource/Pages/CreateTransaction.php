<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction; // <-- TAMBAHKAN INI
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt; // <-- TAMBAHKAN INI

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    // --- TAMBAHKAN SEMUA KODE DI BAWAH INI ---

    public bool $showPrintModal = false;
    public ?Transaction $newTransaction = null;

    protected function afterCreate(): void
    {
        // 1. Ambil data transaksi yang baru saja dibuat
        $this->newTransaction = $this->record;

        // 2. Beri sinyal untuk menampilkan modal
        $this->showPrintModal = true;

        // 3. Hentikan proses redirect otomatis dari Filament
        throw new Halt();
    }
}
