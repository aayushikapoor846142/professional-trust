<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatBlocked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $blockedBy;
    public $blockedUserId;
    public $chatId;
    /**
     * Create a new event instance.
     */
    public function __construct($blockedBy, $blockedUserId = null, $chatId = null,$status=null)
    {
        $this->blockedBy = $blockedBy;
        $this->blockedUserId = $blockedUserId;
        $this->chatId = $chatId;
        $this->status = $status;

    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {    

        return new PrivateChannel('chat_blocked.'.$this->chatId);
    }

    /**
     * Define the name of the event for frontend listeners.
     */
   
    public function broadcastWith()
    {
        return ['blockedBy' => $this->blockedBy,
                'chatId'=>$this->chatId,
                'blockedUserId'=>$this->blockedUserId,
                'status'=>$this->status
            ];
    }
}
