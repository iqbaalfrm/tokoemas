<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\User;
use App\Notifications\ResourceDiubah;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CategoryObserver
{

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $category->products()->delete();
        
        // Kirim notifikasi ke superadmin jika bukan superadmin yang menghapus
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin')) {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isNotEmpty()) {
                LaravelNotification::send(
                    $superAdmins,
                    new ResourceDiubah(
                        $category,
                        'delete',
                        'Kategori',
                        route('filament.admin.resources.categories.index')
                    )
                );
            }
        }
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        $category->products()->restore();
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        $category->products()->forceDelete();
    }
}
