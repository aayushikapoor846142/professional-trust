<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            env('TWILIO_ACCOUNT_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    public function sendSms($to, $message)
    {
        return $this->twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message
        ]);
    }

    public function makeCall($to, $twimlUrl)
    {
        return $this->twilio->calls->create($to, env('TWILIO_PHONE_NUMBER'), [
            'url' => $twimlUrl
        ]);
    }
}
