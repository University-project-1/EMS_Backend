<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetApiPassword extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    protected function resetUrl($notifiable)
    {
        // Redirect to React Frontend: https://exhibition-react.com/reset-password?token=...&email=...
        $frontendUrl = config('app.frontend_url') . '/reset-password';

        return $frontendUrl . '?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset());
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Reset Password Notification - EMS')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $this->resetUrl($notifiable))
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
