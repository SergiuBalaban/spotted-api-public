<?php

namespace App\Events;

use App\Models\TrackedReportedPet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class petMissingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected TrackedReportedPet $trackedReportedPet;

    /**
     * petMissingEvent constructor.
     *
     * @param TrackedReportedPet $trackedReportedPet
     */
    public function __construct(TrackedReportedPet $trackedReportedPet)
    {
        $this->trackedReportedPet = $trackedReportedPet;
    }

    public function broadcastOn()
    {
        return new Channel('pet.'.$this->trackedReportedPet->pet_id.'.missing');
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'missing_pet';
    }

    /**
     * @return bool
     */
    public function broadcastWith()
    {
        return true;
    }
}
