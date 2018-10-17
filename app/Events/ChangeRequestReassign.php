<?php

namespace App\Events;

use App\BudgetChangeRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChangeRequestReassign
{
    use  SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public  $change_request;

    public function __construct(BudgetChangeRequest $changeRequest)
    {
        $this->change_request = $changeRequest;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
