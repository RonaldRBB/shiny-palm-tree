<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmationMessage extends Notification
{
    use Queueable;
    private $sendSms = false;
    /**
     * Create a new notification instance.
     */
    public function __construct(bool $sendSms = false)
    {
        $this->sendSms = $sendSms;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($this->sendSms) {
            return ['mail', 'sms'];
        }
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Welcome to our application!')
            ->action('Confirm your email', url('/'))
            ->line('Thank you for using our application!');
    }
    /**
     * Get the sms representation of the notification.
     */
    public function toSms(object $notifiable): void
    {
        //future implementation
        // return 'Welcome to our application!';
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
