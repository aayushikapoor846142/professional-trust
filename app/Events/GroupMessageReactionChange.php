<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageReactionChange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $status;
    public $groupId;

    /**
     * Create a new event instance.
     */
    public function __construct($groupId,$messageId, $status)
    {
        $this->groupId = $groupId;
        $this->messageId = $messageId;
        $this->status = $status;

    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
      return new PrivateChannel('groupMessageReaction.'.$this->groupId);
    }

    /**
     * Define the name of the event for frontend listeners.
     */
   
    public function broadcastWith()
    {
        return ['messageUniqueId' => $this->messageId,
                'status'=>$this->status
                ];
    }
}
