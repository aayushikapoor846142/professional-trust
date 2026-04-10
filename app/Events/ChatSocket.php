<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSocket implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public $chat_id;
    public function __construct($chat_id,$data)
    {
        $this->data = $data;
        $this->chat_id = $chat_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('chat.' . $this->chat_id),
            new PrivateChannel('individual-chat.' . $this->chat_id),
            new PrivateChannel('chatBot.' . $this->chat_id),
        ];
    }

    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
}
