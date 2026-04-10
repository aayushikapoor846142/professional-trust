<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory,SoftDeletes;

    protected $table="chats";
    protected $fillable = ['is_typing'];

	public function addedBy()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }
        // Each like belongs to a specific user
    public function chatWith()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }
    public function lastMessage()
	{
	    return $this->hasOne(ChatMessage::class, 'chat_id')->latest('created_at')
                ->where(function($query){
                    $query->whereNull('clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [auth()->user()->id]);
                });
                    

	}

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id');
    }

    public function deleteChatForAll($userId)
    {
        // Retrieve related user IDs
        $userId1 = $this->user1_id;
        $userId2 = $this->user2_id;

        // Process the `delete_for` column
        $deletedFor = !empty($this->delete_for) ? explode(',', $this->delete_for) : [];
        if (!in_array($userId1, $deletedFor)) {
            $deletedFor[] = $userId1;
        }
        if (!in_array($userId2, $deletedFor)) {
            $deletedFor[] = $userId2;
        }

        // Update the `delete_for` column
        $this->delete_for = implode(',', $deletedFor);
        $this->save();

        // If `delete_for` is updated, delete associated records
        if (!empty($deletedFor)) {
            // Get all related message IDs for this chat
            $messageIds = ChatMessage::where('chat_id', $this->id)->pluck('id');

            // Delete reactions related to these messages
            MessageCentreReaction::whereIn('message_id', $messageIds)->delete();

            // Delete chat messages
            ChatMessage::where('chat_id', $this->id)->delete();

            // Delete chat message read records
            ChatMessageRead::where('chat_id', $this->id)->delete();

            // Delete the chat request if it exists
            if ($this->chat_request_id) {
                $getChatRequest= ChatRequest::where('id', $this->chat_request_id)->first();
                $getReceiver=User::where('id',$getChatRequest->receiver_id)->first();
                ChatInvitation::where('added_by',$getChatRequest->sender_id)
                        ->where('email',$getReceiver->email)
                        ->delete();

            }

            // Delete chat messages
            ChatRequest::where(function($query) use($userId1,$userId2){
                $query->where('sender_id', $userId1)->where('receiver_id', $userId2);
            })
            ->orWhere(function($query) use($userId1,$userId2){
                $query->where('receiver_id', $userId1)->where('sender_id', $userId2);
            })
            ->delete();

            // Delete the chat itself
            $this->delete();
        }

        return 'Chat Deleted Successfully';
    }

    public function reactions()
    {
        return $this->hasManyThrough(
            MessageCentreReaction::class,         // Final related model
            ChatMessage::class,      // Intermediate model
            'chat_id',               // Foreign key on ChatMessage (intermediate) table
            'message_id',            // Foreign key on Reaction (final) table
            'id',                    // Local key on Chat (starting) table
            'id'                     // Local key on ChatMessage (intermediate) table
        );
    }
    public function unreadMessage($chat_id,$user_id ){
        $chat_message_read = ChatMessageRead::where("chat_id",$chat_id)
        ->where("receiver_id",$user_id)->where("status","unread")->count();
        return $chat_message_read;
    }

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

}