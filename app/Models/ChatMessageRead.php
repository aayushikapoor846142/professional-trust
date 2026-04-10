<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessageRead extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="chat_message_read";

    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    
}
