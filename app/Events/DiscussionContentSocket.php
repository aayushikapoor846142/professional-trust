<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionContentSocket implements ShouldBroadcastNow
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
        \Log::info("\n\n");
        \Log::info("Test init dcs: ".$this->discussion_id);
        \Log::info("\n\n");
        return new PrivateChannel('discussion-content.'.$this->discussion_id);
    }

    public function broadcastWith(){
        \Log::info("\n\n response dcs: ");
        \Log::info(json_encode($this->data));
        \Log::info("\n\n");
        return ['data' => $this->data];
    }
}
