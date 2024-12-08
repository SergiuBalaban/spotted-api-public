<?php

namespace App\Events;

use App\Models\TrackedReportedPet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatTypingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected TrackedReportedPet $trackedReportedPet;
    protected array $chat;

    /**
     * ChatNewMessageEvent constructor.
     *
     * @param TrackedReportedPet $trackedReportedPet
     * @param array $chat
     */
    public function __construct(TrackedReportedPet $trackedReportedPet, array $chat)
    {
        $this->trackedReportedPet = $trackedReportedPet;
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat.reportedPet.'.$this->trackedReportedPet->reported_pet_id.'.missingPet.'.$this->trackedReportedPet->pet_id.'.typing');
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'chatTyping';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->chat;
    }
}
