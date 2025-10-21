<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Approval;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ApprovalDiminta;
use Filament\Actions;
use Filament\Notifications\Notification; // Ini adalah Notifikasi Filament (untuk UI)
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
// Kita akan panggil Notifikasi Laravel via full path untuk menghindari konflik

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function beforeSave(): void
    {
        $invalidProducts = collect($this->form->getState()['transactionItems'] ?? [])
            ->filter(function ($item) {
                // Pastikan product_id ada sebelum mencari
                if (empty($item['product_id'])) {
                    return false;
                }
                $product = Product::withTrashed()->find($item['product_id']);
                return $product?->trashed();
            });

        // --- PERBAIKAN LOGIKA: Harusnya isNotEmpty ---
        if ($invalidProducts->isNotEmpty()) {
            Notification::make()
                ->title('Produk tidak tersedia')
                ->body('Ada produk yang sudah dihapus. Edit tidak dapat dilanjutkan.')
                ->danger()
                ->send();

            $this->halt(); // menghentikan proses save
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // --- FUNGSI UNTUK SISTEM APPROVAL ---
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (auth()->user()->hasRole('superadmin')) {
            $record->update($data);
            return $record;
        }

        if (auth()->user()->hasRole('admin')) {
            $changes = collect($data)->diffAssoc($record->getOriginal());

            if ($changes->isEmpty()) {
                return $record;
            }

            $approval = Approval::create([
                'user_id' => auth()->id(),
                'approvable_type' => get_class($record),
                'approvable_id' => $record->id,
                'changes' => $changes->toArray(),
                'status' => 'pending',
            ]);

            $superAdmins = User::role('superadmin')->get();
            if ($superAdmins->isNotEmpty()) {
                // Panggil Notifikasi Laravel (database) dengan full path
                \Illuminate\Support\Facades\Notification::send($superAdmins, new ApprovalDiminta($approval));
            }
            
            // Panggil Notifikasi Filament (UI)
            Notification::make()
                ->title('Menunggu Approval')
                ->body('Perubahan Anda telah dikirim untuk disetujui Superadmin.')
                ->success()
                ->send();

            return $record; // Kembalikan data asli, jangan di-update
        }

        return $record;
    }
}