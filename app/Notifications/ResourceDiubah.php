<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class ResourceDiubah extends Notification
{
    public $model;
    public $action;
    public $modelName;
    public $url;

    public function __construct($model, string $action, string $modelName, string $url)
    {
        $this->model = $model;
        $this->action = $action; // 'create', 'update', 'delete'
        $this->modelName = $modelName;
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $userName = auth()->user()->name ?? 'Admin';
        
        $actionLabels = [
            'create' => 'membuat',
            'update' => 'mengubah',
            'delete' => 'menghapus',
        ];
        
        $actionLabel = $actionLabels[$this->action] ?? $this->action;
        $title = ucfirst($this->modelName) . ' ' . ucfirst($actionLabel);
        $message = "{$userName} telah {$actionLabel} data {$this->modelName}";
        
        // Jika ada model, tambahkan informasi tambahan
        if ($this->model && method_exists($this->model, 'getKey')) {
            $identifier = $this->getModelIdentifier();
            if ($identifier) {
                $message .= " - {$identifier}";
            }
        }
        
        // Menggunakan Filament Notification Builder untuk mendapatkan format yang benar
        $notification = FilamentNotification::make()
            ->title($title)
            ->body($message)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->url($this->url)
                    ->markAsRead(),
            ]);

        $databaseMessage = $notification->getDatabaseMessage();

        // Add custom data fields that our custom notification component expects
        $databaseMessage['message'] = $message;
        $databaseMessage['title'] = $title;
        $databaseMessage['url'] = $this->url;
        $databaseMessage['action'] = $this->action;
        $databaseMessage['model_name'] = $this->modelName;

        return $databaseMessage;
    }

    private function getModelIdentifier(): ?string
    {
        if (!$this->model) {
            return null;
        }

        // Coba berbagai field yang umum digunakan sebagai identifier
        $fields = ['name', 'nama', 'title', 'transaction_number', 'no_hp', 'email'];
        
        foreach ($fields as $field) {
            if (isset($this->model->$field)) {
                return $this->model->$field;
            }
        }

        // Jika tidak ada, return ID
        if (method_exists($this->model, 'getKey')) {
            return 'ID: ' . $this->model->getKey();
        }

        return null;
    }
}

