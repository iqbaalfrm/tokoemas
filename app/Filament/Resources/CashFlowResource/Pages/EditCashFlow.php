<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Resources\CashFlowResource;
use App\Models\Approval;
use App\Models\User;
use App\Notifications\ApprovalDiminta;
use Filament\Actions;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Filament\Support\Exceptions\Halt;

class EditCashFlow extends EditRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(), // Hapus jika tidak ada fitur delete
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            // Super Admin langsung update
            $record->update($data);
            return $record;
        } else {
            // User Admin atau Kasir -> Kirim ke Approval
            $changes = collect($data)->diffAssoc($record->getOriginal())->toArray();

            if (empty($changes)) {
                FilamentNotification::make()->title('Tidak ada data yang berubah.')->info()->send();
                return $record; // Kembalikan data asli jika tidak ada perubahan
            }

            $approval = Approval::create([
                'user_id' => $user->id,
                'approvable_type' => get_class($record),
                'approvable_id' => $record->id,
                'changes' => $changes,
                'status' => 'pending',
            ]);

            // Kirim notifikasi ke Superadmin
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
            }
            
            FilamentNotification::make()
                ->title('Menunggu Approval')
                ->body('Perubahan Alur Kas Anda telah dikirim untuk disetujui Superadmin.')
                ->success()
                ->send();

            // Hentikan proses update record asli
            throw new Halt();
        }
    }
}