<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Resources\CashFlowResource;
use App\Models\Approval;
use App\Models\User;
use App\Notifications\ApprovalDiminta;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Filament\Support\Exceptions\Halt;
use App\Models\CashFlow;

class CreateCashFlow extends CreateRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        $model = static::getModel();

        if ($user->hasRole('super_admin')) {
            return $model::create($data);
        }

        if ($user->hasRole('admin') || $user->hasRole('kasir')) {
            
            $approval = Approval::create([
                'user_id' => $user->id,
                'approvable_type' => $model,
                'approvable_id' => null,
                'action_type' => 'create',
                'changes' => $data, 
                'status' => 'pending',
            ]);

            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
            }
            
            FilamentNotification::make()
                ->title('Menunggu Approval')
                ->body('Data Alur Kas baru Anda telah dikirim untuk disetujui Superadmin.')
                ->success()
                ->send();

            throw new Halt();
        }

        return $model::create($data);
    }
}