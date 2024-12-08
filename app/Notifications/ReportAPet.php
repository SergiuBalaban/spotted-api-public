<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportAPet extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string|null
     */
    private $message;

    /**
     * BulkUpdateFinished constructor.
     * @param string $message
     */
    public function __construct(string $message = null)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title'       => null,
            'description' => $this->message ?: trans('message.reported_a_pet'),
            'link'        => null
        ];
    }
}
