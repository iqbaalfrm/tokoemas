<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class CustomNotificationList extends Component
{
    public $isOpen = false;
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = [
        'notificationSent' => 'refreshNotifications',
        'notificationRead' => 'refreshNotifications',
    ];

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->notifications()
                ->whereNull('read_at')
                ->limit(50)
                ->count();
        }
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            $user = Auth::user();

            $notificationsCollection = $user->notifications()
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $this->notifications = $notificationsCollection
                ->map(function ($notification) {
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
                ->values()
                ->toArray();

            $this->unreadCount = count($this->notifications);
        }
    }

    public function refreshNotifications()
    {
        $this->loadUnreadCount();
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;

        if ($this->isOpen) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $notificationId)->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
                $this->loadUnreadCount(); // Update count after marking as read
                $this->dispatch('notificationRead');
            }
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->loadUnreadCount(); // Update count after marking all as read
            $this->dispatch('notificationRead');
        }
    }

    public function render()
    {
        return view('livewire.custom-notification-list');
    }
}