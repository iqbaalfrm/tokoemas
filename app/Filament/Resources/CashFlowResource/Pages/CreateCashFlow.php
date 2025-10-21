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

class CreateCashFlow extends CreateRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        $source = $data['source'] ?? null;

        // Kondisi bypass approval: Super Admin ATAU (Kasir DAN sumbernya sales)
        $bypassApproval = $user->hasRole('super_admin') || ($user->hasRole('kasir') && $source === 'sales');

        if ($bypassApproval) {
            // Langsung buat record
            return static::getModel()::create($data);
        } else {
            // User Admin atau Kasir (non-sales) -> Kirim ke Approval
            $approval = Approval::create([
                'user_id' => $user->id,
                'approvable_type' => static::getModel(),
                'approvable_id' => null, // Tandanya ini request CREATE
                'changes' => $data, // Simpan semua data form
                'status' => 'pending',
            ]);

            // Kirim notifikasi ke Superadmin
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
            }
            
            FilamentNotification::make()
                ->title('Menunggu Approval')
                ->body('Data Alur Kas baru Anda telah dikirim untuk disetujui Superadmin.')
                ->success()
                ->send();

            // Hentikan proses pembuatan record asli
            throw new Halt();
        }
    }
}