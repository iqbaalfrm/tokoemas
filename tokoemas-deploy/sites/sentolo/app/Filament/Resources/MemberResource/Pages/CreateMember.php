<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected function afterCreate(): void
    {
        $user = auth()->user();
        
        // Hanya kirim notifikasi jika bukan superadmin
        if (!$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $this->record,
                        'create',
                        'Member',
                        route('filament.admin.resources.members.view', $this->record)
                    )
                );
            }
        }
    }
}
