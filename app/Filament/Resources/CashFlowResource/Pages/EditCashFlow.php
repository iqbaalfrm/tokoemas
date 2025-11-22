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
use App\Models\CashFlow;

class EditCashFlow extends EditRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $record->update($data);
            return $record;
        } 

        if ($user->hasRole('admin') || $user->hasRole('kasir')) {
            $changes = collect($data)->diffAssoc($record->getOriginal())->toArray();

            if (empty($changes)) {
                FilamentNotification::make()->title('Tidak ada data yang berubah.')->info()->send();
                return $record; 
            }

            $approval = Approval::create([
                'user_id' => $user->id,
                'approvable_type' => CashFlow::class,
                'approvable_id' => $record->id,
                'action_type' => 'update',
                'changes' => $changes,
                'status' => 'pending',
            ]);

            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
            }
            
            FilamentNotification::make()
                ->title('Menunggu Approval')
                ->body('Perubahan Alur Kas Anda telah dikirim untuk disetujui Superadmin.')
                ->success()
                ->send();

            throw new Halt();
        }
        
        FilamentNotification::make()->title('Akses ditolak.')->danger()->send();
        throw new Halt();
    }
}