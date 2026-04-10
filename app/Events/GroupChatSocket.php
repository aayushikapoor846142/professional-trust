<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupChatSocket implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public $group_id;
    public function __construct($group_id,$data)
    {
        $this->data = $data;
        $this->group_id = $group_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('group-chat.' . $this->group_id),
            new PrivateChannel('group-message.' . $this->group_id),
            new PrivateChannel('groupChatBot.' . $this->group_id),
        ];
    }

    public function broadcastWith(){
        
        return ['data' => $this->data];
    }
}
