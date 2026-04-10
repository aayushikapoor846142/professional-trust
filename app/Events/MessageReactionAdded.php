<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $reaction;
    public $userId;
    public $chatId;
    public $action;
    public $reactionUniqueId;

    /**
     * Create a new event instance.
     */
    public function __construct($messageId, $reaction, $userId,$chatId,$reactionUniqueId,$action)
    {
        $this->messageId = $messageId;
        $this->reaction = $reaction;
        $this->userId = $userId;
        $this->chatId = $chatId;
        $this->reactionUniqueId = $reactionUniqueId;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return [    
            new PrivateChannel('chatMessageReaction.'.$this->chatId),
            new PrivateChannel('individual-chat-reaction.'.$this->chatId),
        ];
        
    }

    /**
     * Define the name of the event for frontend listeners.
     */
   
    public function broadcastWith()
    {
        return [
            'messageUniqueId' => $this->messageId, 
            'reactionUniqueId' => $this->reactionUniqueId, 
            'messageReaction' => $this->reaction,
            'chat_id' => $this->chatId,
            'sender_id' => $this->userId,
            'action' => $this->action
        ];
    }
}