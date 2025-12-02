<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class CustomNotificationCount extends Component
{
    public $unreadCount = 0;

    protected $listeners = [
        'notificationSent' => 'refreshCount',
        'notificationRead' => 'refreshCount',
    ];

    public function mount()
    {
        // Load count asynchronously to prevent blocking page load
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        if (Auth::check()) {
            // Use cache to avoid hitting DB on every page load
            $this->unreadCount = cache()->remember(
                'user_unread_count_' . Auth::id(),
                300, // Cache for 5 minutes
                function () {
                    return Auth::user()->notifications()
                        ->whereNull('read_at')
                        ->limit(50) // Prevent excessive counts
                        ->count();
                }
            );
        }
    }

    public function refreshCount()
    {
        if (Auth::check()) {
            // Clear cache and get fresh count
            cache()->forget('user_unread_count_' . Auth::id());
            $this->loadUnreadCount();
        }
    }

    public function render()
    {
        return view('livewire.custom-notification-count');
    }
}