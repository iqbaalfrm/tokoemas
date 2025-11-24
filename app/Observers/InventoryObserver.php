<?php

namespace App\Observers;

use App\Filament\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Str;

class InventoryObserver
{
    public function creating(Inventory $inventory): void
    {
        $today = now()->format('Ymd');
        $prefix = 'INV-' . $today . '-';

        $latest = Inventory::where('reference_number', 'LIKE', $prefix . '%')
                           ->orderBy('reference_number', 'desc')
                           ->first();
        
        $nextCount = 1;
        if ($latest) {
            $lastCount = (int) Str::afterLast($latest->reference_number, '-');
            $nextCount = $lastCount + 1;
        }
        
        $inventory->reference_number = $prefix . str_pad($nextCount, 2, '0', STR_PAD_LEFT);

        if (is_null($inventory->total)) {
            $inventory->total = 0;
        }
        if (is_null($inventory->total_cost)) {
            $inventory->total_cost = 0;
        }
    }

    public function created(Inventory $inventory): void
    {
        // Kirim notifikasi ke superadmin jika bukan superadmin yang membuat
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $inventory,
                        'create',
                        'Inventori',
                        InventoryResource::getUrl('edit', ['record' => $inventory])
                    )
                );
            }
        }
    }

    public function deleted(Inventory $inventory): void
    {
        // Kirim notifikasi ke superadmin jika bukan superadmin yang menghapus
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $inventory,
                        'delete',
                        'Inventori',
                        InventoryResource::getUrl('index')
                    )
                );
            }
        }
    }
}