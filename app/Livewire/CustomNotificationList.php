<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomNotificationList extends Component
{
    public $isOpen = false;
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = [
        'notificationSent' => 'refreshNotifications',
    ];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
        
        if ($this->isOpen) {
            $this->refreshNotifications();
        }
    }

    public function refreshNotifications()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Get unread notifications with formatted data
            $notificationsCollection = $user->notifications()
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10) // Limit to recent notifications
                ->get();

            $this->notifications = $notificationsCollection
                ->map(function ($notification) {
                    // Parse data notification (bisa JSON string atau array)
                    $data = is_string($notification->data) 
                        ? json_decode($notification->data, true) 
                        : $notification->data;

                    return [
                        'id' => $notification->id,
                        'message' => $data['message'] ?? $data['body'] ?? 'Notification message',
                        'title' => $data['title'] ?? 'Notification',
                        'url' => $data['url'] ?? '#',
                        'created_at' => $notification->created_at->diffForHumans(),
                        'format' => $data['format'] ?? null,
                        'approval_id' => $data['approval_id'] ?? null,
                    ];
                })
                ->toArray();

            $this->unreadCount = $notificationsCollection->count();
        } else {
            $this->notifications = [];
            $this->unreadCount = 0;
        }
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $notificationId)->first();
            
            if ($notification) {
                $notification->update(['read_at' => now()]);
                $this->refreshNotifications();
                
                // Trigger event for other components
                $this->dispatch('notificationRead');
            }
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->refreshNotifications();
            
            // Trigger event for other components
            $this->dispatch('notificationRead');
        }
    }

    public function render()
    {
        return view('livewire.custom-notification-list');
    }
}