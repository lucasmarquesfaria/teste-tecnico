<?php

namespace App\Events;

use App\Models\ServiceOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceOrderCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The service order instance.
     * 
     * @var \App\Models\ServiceOrder
     */
    public $serviceOrder;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return void
     */
    public function __construct(ServiceOrder $serviceOrder)
    {
        $this->serviceOrder = $serviceOrder;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
