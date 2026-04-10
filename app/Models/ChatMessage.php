<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="chat_messages";
    protected $dates = ['deleted_at'];

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
 	public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
    public function chatMessageRead(){
        return $this->hasOne(ChatMessageRead::class, 'message_id');
    }
    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to');
    }
    public function checkReceiverRead($chat_id,$message_id,$receiver_id){
        $check_read = ChatMessageRead::where('message_id',$message_id)->where("receiver_id",$receiver_id)->first();
        if(!empty($check_read)){
            return $check_read->status;
        }else{
            $message = ChatMessage::where("id",$message_id)->first();
            if(!empty($message) &&  $message->sent_by != $receiver_id){
                unreadChatMessage($chat_id,$message_id,$receiver_id);
                return "unread";
            }else{
                return '';
            }
           
        }
    }
    
    public function deleteForUser($userId)
    {
        $deletedFor = !empty($this->clear_for) ? explode(',', $this->clear_for) : [];
        if (!in_array($userId, $deletedFor)) {
            $deletedFor[] = $userId;
        }
        $this->clear_for = implode(',', $deletedFor);
        $this->save();

        return 'Message Deleted Successfully';
    }
    
    public function messageReactions()
    {
        return $this->hasMany(MessageCentreReaction::class, 'message_id');
    }

    public function checkBeforeMessage($id,$chat_id,$date,$userId){
        $messages = ChatMessage::where("chat_id",$chat_id)
        ->where("id","<",$id)
        ->whereDate("created_at",$date)
        ->where(function($query) use($userId){
            $query->whereNull('clear_for');
            $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
        })  
        ->count();
        return $messages;
    }

}
