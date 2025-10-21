<?php

namespace App\Notifications;

use App\Models\Approval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalDiminta extends Notification
{
    use Queueable;

    public Approval $approval;

    public function __construct(Approval $approval)
    {
        $this->approval = $approval;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $modelName = class_basename($this->approval->approvable_type);
        
        return [
            'message' => auth()->user()->name . " meminta approval untuk mengubah data {$modelName}.",
            'approval_id' => $this->approval->id,
            'url' => route('filament.admin.pages.daftar-approval'),
        ];
    }
}