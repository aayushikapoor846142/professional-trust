<?php

namespace App\Http\Controllers;

use App\Models\EmailEventLog;
use Illuminate\Http\Request;

class SendGridWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $events = $request->all();

        foreach ($events as $event) {
            EmailEventLog::create([
                'email' => $event['email'],
                'event' => $event['event'],
                'response' => json_encode($event),
                'timestamp' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
