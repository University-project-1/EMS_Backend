<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyApiEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable; // Enforce Queueing (NFR 4.1)
    public function __construct(){}

    protected function verificationUrl($notifiable)
    {
        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        $apiUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $id, 'hash' => $hash]
        );

        // Extract expires and signature
        $query = parse_url($apiUrl, PHP_URL_QUERY);

        // Inject ID and Hash directly into the React URL parameters
        return config('app.frontend_url') . '/verify-email?id=' . $id . '&hash=' . $hash . '&' . $query;
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address - EMS')
            ->greeting('Welcome to the Exhibition Management System!')
            ->line('Please click the button below to verify your email address and start booking your booths.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');
    }

}
