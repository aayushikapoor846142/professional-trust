<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMessages extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "group_messages";
    protected $fillable = ['unique_id', 'group_id', 'user_id', 'reply_to', 'message', 'attachment'];

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
    public function group()
	{
	    return $this->belongsTo(ChatGroup::class);
	}

	public function sentBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(GroupMessages::class, 'reply_to');
    }

    public function checkMemberStatus($group_id,$user_id){
        $check_member = GroupMembers::withTrashed()->where('group_id',$group_id)->where("user_id",$user_id)->first();
        if($check_member->trashed()){
            return 'notexists';
        }else{
            return 'exists';
        }
    }

    public function messageReactions()
    {
        return $this->hasMany(GroupMessageReaction::class, 'message_id');
    }
    public function markAsDeletedForUser(int $userId)
    {
        // Get the existing users for whom the message is marked as deleted
        $deletedFor = !empty($this->clear_for) ? explode(',', $this->clear_for) : [];

        // Add the user ID to the deleted list if not already present
        if (!in_array($userId, $deletedFor)) {
            $deletedFor[] = $userId;
        }

        // Update the `clear_for` field
        $this->clear_for = implode(',', $deletedFor);
        $this->save();

        // Clear any read status associated with this message
        GroupMessagesRead::where('group_message_id', $this->id)->delete();

        return 'Message Deleted Successfully';
    }

    public function checkBeforeMessage($id,$group_id,$date,$userId){
        $messages = GroupMessages::where("group_id",$group_id)
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
