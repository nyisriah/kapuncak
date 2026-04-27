<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\ResetPasswordMail;

class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Send the notification via email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        // Send using custom mailable
        \Illuminate\Support\Facades\Mail::send(
            new ResetPasswordMail($resetUrl, $notifiable->getEmailForPasswordReset())
        );

        // Return empty MailMessage since we're using custom mailable
        return (new MailMessage);
    }
}
