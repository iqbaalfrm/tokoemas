<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use App\Models\Approval;
use App\Models\User;
use App\Models\Transaction;
use App\Notifications\ApprovalDiminta;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $data;
        }

        if ($user->hasRole('admin') || $user->hasRole('kasir')) {
            
            $approval = Approval::create([
                'user_id' => $user->id,
                'approvable_type' => Transaction::class,
                'approvable_id' => null,
                'action_type' => 'create',
                'changes' => $data,
                'status' => 'pending',
            ]);

            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
            }

            Notification::make()
                ->title('Menunggu Approval')
                ->body('Permintaan pembuatan transaksi baru telah dikirim ke Superadmin.')
                ->success()
                ->send();

            throw new Halt();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}