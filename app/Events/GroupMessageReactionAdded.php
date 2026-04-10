<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageReactionAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $reaction;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($messageId, $reaction, $userId,$reactionUniqueId)
    {
        $this->messageId = $messageId;
        $this->reaction = $reaction;
        $this->userId = $userId;
        $this->reactionUniqueId = $reactionUniqueId;

    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
    \Log::info('group channel broadcast result', ['reaction' =>  $this->reaction,'messageId' => $this->messageId,
                'reactionUniqueId'=>$this->reactionUniqueId]);
      return new PrivateChannel('group_message_reaction');
    }

    /**
     * Define the name of the event for frontend listeners.
     */
   
    public function broadcastWith()
    {
        return ['messageUniqueId' => $this->messageId,
                'reactionUniqueId'=>$this->reactionUniqueId
                ,'messageReaction'=>$this->reaction];
    }
}
