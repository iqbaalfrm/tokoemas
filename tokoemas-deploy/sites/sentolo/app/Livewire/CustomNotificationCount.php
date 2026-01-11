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
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = cache()->remember(
                'user_unread_count_' . Auth::id(),
                300,
                function () {
                    return Auth::user()->notifications()
                        ->whereNull('read_at')
                        ->limit(50)
                        ->count();
                }
            );
        }
    }

    public function refreshCount()
    {
        if (Auth::check()) {
            cache()->forget('user_unread_count_' . Auth::id());
            $this->loadUnreadCount();
        }
    }

    public function render()
    {
        return view('livewire.custom-notification-count');
    }
}