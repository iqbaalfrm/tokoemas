<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use App\Models\Approval;
use App\Models\User;
use App\Models\Product;
use App\Notifications\ApprovalDiminta;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->hasRole('super_admin')) {
            return $data;
        }

        if (auth()->user()->hasRole('admin')) {
            
            $approval = Approval::create([
                'user_id' => auth()->id(),
                'approvable_type' => Product::class,
                'approvable_id' => $this->getRecord()->id,
                'action_type' => 'update',
                'changes' => $data,
                'status' => 'pending',
            ]);

            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
            }

            Notification::make()
                ->title('Menunggu Approval')
                ->body('Permintaan perubahan produk telah dikirim ke Superadmin.')
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