<?php 
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
          \Log::info("Broadcasting to channel: my-channel");  // Log the channel name


        return new Channel('chatting');
       // dd('sajksjakj');
    }

    public function broadcastWith()
    {
        \Log::info('Broadcasting data: ', ['message' => $this->data]);
        return ['data' => $this->data];
    }

   
}
