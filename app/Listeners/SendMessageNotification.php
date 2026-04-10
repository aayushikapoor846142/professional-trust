<?php

namespace App\Listeners;

use App\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendMessageNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        //$message = $event->message;

        Log::info('Message Sent', [
            'sender' => "kapoor123",
            'content' =>"dskdjskdjk",
            'time' => "dsjdshjdhj",
        ]);

    }
}
