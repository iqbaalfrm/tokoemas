<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class MemberObserver
{
    /**
     * Handle the Member "deleted" event.
     */
    public function deleted(Member $member): void
    {
        // Kirim notifikasi ke superadmin jika bukan superadmin yang menghapus
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $member,
                        'delete',
                        'Member',
                        route('filament.admin.resources.members.index')
                    )
                );
            }
        }
    }
}

