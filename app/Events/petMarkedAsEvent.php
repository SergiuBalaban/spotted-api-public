<?php

namespace App\Events;

use App\Models\ReportedPet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class petMarkedAsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected ReportedPet $reportedPet;

    /**
     * newSpottedPetEvent constructor.
     *
     * @param ReportedPet $reportedPet
     */
    public function __construct(ReportedPet $reportedPet)
    {
        $this->reportedPet = $reportedPet;
    }

    public function broadcastOn()
    {
        return new Channel('pet');
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'markedAs';
    }

    /**
     * @return bool
     */
    public function broadcastWith()
    {
        return true;
    }
}
