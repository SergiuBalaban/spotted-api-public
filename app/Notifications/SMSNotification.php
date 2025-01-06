<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Facades\Vonage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SMSNotification extends Notification
{
    use Queueable;

    public function __construct() {}

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['vonage', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Notification Subject')
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * @return array<string>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }

    public function toVonage(object $notifiable): void
    {
        (new VonageMessage)
            ->content('Using Laravel Notification to send a message.')
            ->from('16105552344');
        //        Vonage::message()->send([
        //            'to' => Auth::user()->phone,
        //            'from' => '16105552344',
        //            'text' => 'Using the facade to send a message.',
        //        ]);
    }

    //    public function toVonage(object $notifiable): VonageMessage
    //    {
    //        return (new VonageMessage)
    //            ->clientReference((string) $notifiable->id)
    //            ->content('Your SMS message content')
    //            ->unicode()
    //            ->from('15554443333');
    //    }
}
