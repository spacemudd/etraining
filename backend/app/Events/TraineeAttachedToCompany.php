<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TraineeAttachedToCompany
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trainee_id;

    public $company_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($trainee_id, $company_id)
    {
        $this->trainee_id = $trainee_id;
        $this->company_id = $company_id;
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
