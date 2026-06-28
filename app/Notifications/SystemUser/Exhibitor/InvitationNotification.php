<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Models\Invitation;
use App\Models\SystemUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Invitation $invitation
    ){}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable instanceof SystemUser ? ['database', 'mail'] : ['mail'] ;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $actionUrl = "{$frontendUrl}/invitations/{$this->invitation->token}";

        $entityType = $this->invitation->inviteable_type === 'App\Models\Company' ? 'Company' : 'Pavilion';

        $senderName = $this->invitation->sender->name;
        $entityName = $this->invitation->inviteable->name ?? 'Team';

        return (new MailMessage)
            ->subject('Invitation to Join')
            ->greeting('Hello,')
            ->line("{$senderName} has invited you to join the {$entityType} \"{$entityName}\".")
            ->line('Please click the button below to accept or decline the invitation.')
            ->action('View Invitation Details', $actionUrl)
            ->line('If you were not expecting this invitation, you can safely ignore it.');
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            //
        ];
    }
}
