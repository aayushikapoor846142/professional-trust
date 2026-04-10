<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast;

class MessageController extends Controller
{    
public function sendMessage(Request $request)
{
    $message = "helloblue";
    Event::dispatch(new MessageSent('Sample Data'));
    Broadcast::event(new MessageSent('Broadcast Data'));
    broadcast(new MessageSent($message));
    event(new MessageSent($message));
       // return view('websocket_test');

    return response()->json(['status' => 'Event Triggered']);

}
public function receiveMessage(Request $request)
{
    return view('websocket_test');
}

}
