<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionThreadSocket implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public $discussion_id;
    public function __construct($discussion_id,$data)
    {
        $this->data = $data;
        $this->discussion_id = $discussion_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      
        return new PrivateChannel('discussion-threads.'.$this->discussion_id);
    }

    public function broadcastWith(){
        
        return ['data' => $this->data];
    }
}
