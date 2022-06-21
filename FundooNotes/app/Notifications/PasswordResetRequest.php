<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetRequest extends Notification
{
    use Queueable;

    public $token;
    public $email;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $email, string $token)
    {
        $this->token = $token;
        $this->user = $email;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // $url = "/api/auth/resetpassword".$this->token;
        return (new MailMessage)
                    ->line('You have receive this email for reset password token =' . $this->token)
                    ->action('Password Reset Request', url(''))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
