<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomNotificationCount extends Component
{
    public $unreadCount = 0;

    protected $listeners = [
        'notificationSent' => 'refreshCount',
        'notificationRead' => 'refreshCount',
    ];

    public function mount()
    {
        $this->refreshCount();
    }

    public function refreshCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->notifications()
                ->whereNull('read_at')
                ->count();
        }
    }

    public function render()
    {
        return view('livewire.custom-notification-count');
    }
}