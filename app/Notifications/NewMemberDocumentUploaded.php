<?php

namespace App\Notifications;

use App\Models\Member;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class NewMemberDocumentUploaded extends BaseNotification
{
    use Queueable;

    public function __construct(public Member $member) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return Notification::make()
            ->title('New Medical Document Uploaded')
            ->body("Company user uploaded a new document for member: {$this.member->first_name} {$this.member->last_name} (ID: {$this.member->member_ID}). Please update invoice.")
            ->icon('heroicon-o-document-text')
            ->toDatabase();
    }
}