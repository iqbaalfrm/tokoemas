<?php

namespace App\Notifications;

use App\Models\Approval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Str;

class ApprovalDiminta extends BaseNotification
{
    use Queueable;

    public Approval $approval;

    public function __construct(Approval $approval)
    {
        $this->approval = $approval;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Build the database notification message using Filament's builder pattern.
     * This ensures the notification is recognized by the Filament UI component.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $modelName = class_basename($this->approval->approvable_type);
        $userName = $this->approval->user->name;
        $actionType = $this->approval->changes['action'] ?? 'mengubah';

        $title = "TIKET BARU: {$modelName}";
        $body = "{$userName} meminta persetujuan untuk {$actionType} data.";

        // Menggunakan Filament Notification Builder untuk mendapatkan format yang benar
        $notification = FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->url(route('filament.admin.pages.daftar-approval'))
                    ->markAsRead(),
            ]);

        $databaseMessage = $notification->getDatabaseMessage();

        // Add custom data fields that our custom notification component expects
        $databaseMessage['message'] = $body;
        $databaseMessage['title'] = $title;
        $databaseMessage['approval_id'] = $this->approval->id;
        $databaseMessage['url'] = route('filament.admin.pages.daftar-approval');

        return $databaseMessage;
    }
}