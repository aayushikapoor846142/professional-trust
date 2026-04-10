<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketSystemSocket implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $user_id;

    public function __construct($user_id, $data)
    {
        $this->data = $data;
        $this->user_id = $user_id;
    }
    public function broadcastAs()
    {
        return 'TicketSystemSocket';
    }


    public function broadcastOn()
    {
        return new PrivateChannel('ticket-user.' . $this->user_id);
    }

    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
} 