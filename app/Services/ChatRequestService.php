<?php

namespace App\Services;

use App\Models\ChatRequest;
use App\Models\ChatInvitation;
use App\Models\Chat;
use App\Models\User;
use App\Models\FeedsConnection;
use Illuminate\Support\Facades\DB;

class ChatRequestService
{
    public function sendChatRequest($sender, $receiver)
    {
        $existing = ChatRequest::where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->first();
        if ($existing) {
            return $existing;
        }
        $chat_req = new ChatRequest();
        $chat_req->unique_id = randomNumber();
        $chat_req->sender_id = $sender->id;
        $chat_req->receiver_id = $receiver->id;
        $chat_req->is_accepted = 0;
        $chat_req->save();
        $this->sendRequestEmail($sender, $receiver);
        return $chat_req;
    }

    public function sendRequestEmail($sender, $receiver)
    {
        $mailData = [
            'professional_name' => $receiver->first_name . ' ' . $receiver->last_name,
            'sender_name' => $sender->first_name . ' ' . $sender->last_name,
        ];
        $mail_message = \View::make('emails.chat_request_professional', $mailData);
        $mailData['mail_message'] = $mail_message;
        $parameter = [
            'to' => $receiver->email,
            'to_name' => $receiver->first_name . ' ' . $receiver->last_name,
            'message' => $mail_message,
            'subject' => 'Received a Chat Request',
            'view' => 'emails.chat_request_professional',
            'data' => $mailData,
        ];
        sendMail($parameter);
    }

    public function acceptChatRequest($chat_req, $receiver)
    {
        $chat_req->is_accepted = 1;
        $chat_req->save();
        $getReceiver = $receiver;
        ChatInvitation::where('added_by', $chat_req->sender_id)
            ->where('email', $getReceiver->email)
            ->update(['status' => '1']);
        checkUserConnection($chat_req->sender_id, $receiver->id, 'connect');
        $feeds_connection = FeedsConnection::create([
            'unique_id' => randomNumber(),
            'connection_with' => $chat_req->receiver_id,
            'user_id' => $chat_req->sender_id,
            'connection_type' => 'connect',
            'status' => 'active',
        ]);
        $user_id = $chat_req->sender_id;
        $receiver->following()->syncWithoutDetaching([
            $user_id => ['unique_id' => randomNumber()],
        ]);
        $chat_check = Chat::where(function ($query) use ($receiver, $user_id) {
            $query->where('user1_id', $user_id)->where('user2_id', $receiver->id);
        })->orWhere(function ($query) use ($receiver, $user_id) {
            $query->where('user1_id', $receiver->id)->where('user2_id', $user_id);
        })->first();
        if (!$chat_check) {
            $chat_ins = new Chat();
            $chat_ins->unique_id = randomNumber();
            $chat_ins->user1_id = $chat_req->sender_id;
            $chat_ins->chat_type = 'individual';
            $chat_ins->user2_id = $receiver->id;
            $chat_ins->chat_request_id = $chat_req->id;
            $chat_ins->save();
            $this->sendAcceptEmail($chat_req, $receiver);
            return $chat_ins;
        }
        $this->sendAcceptEmail($chat_req, $receiver);
        return $chat_check;
    }

    public function sendAcceptEmail($chat_req, $receiver)
    {
        $mailData = [
            'sender_name' => $chat_req->sender->first_name . ' ' . $chat_req->sender->last_name,
            'professional_name' => $receiver->first_name . ' ' . $receiver->last_name,
        ];
        $mail_message = \View::make('emails.chat_request_accepted', $mailData);
        $mailData['mail_message'] = $mail_message;
        $parameter = [
            'to' => $chat_req->sender->email,
            'to_name' => $chat_req->sender->first_name . ' ' . $chat_req->sender->last_name,
            'message' => $mail_message,
            'subject' => ' Chat Request Accepted',
            'view' => 'emails.chat_request_accepted',
            'data' => $mailData,
        ];
        sendMail($parameter);
    }

    public function declineChatRequest($chat_req, $receiver)
    {
        $chat_req->is_accepted = 2;
        $chat_req->save();
        $mailData = [
            'sender_name' => $chat_req->sender->first_name . ' ' . $chat_req->sender->last_name,
            'professional_name' => $receiver->first_name . ' ' . $receiver->last_name,
        ];
        $mail_message = \View::make('emails.chat_request_declined', $mailData);
        $mailData['mail_message'] = $mail_message;
        $parameter = [
            'to' => $chat_req->sender->email,
            'to_name' => $chat_req->sender->first_name . ' ' . $chat_req->sender->last_name,
            'message' => $mail_message,
            'subject' => ' Chat Request Declined',
            'view' => 'emails.chat_request_declined',
            'data' => $mailData,
        ];
        sendMail($parameter);
        ChatInvitation::where('added_by', $chat_req->sender_id)
            ->where('email', $receiver->email)
            ->delete();
        $chat_req->delete();
    }
} 