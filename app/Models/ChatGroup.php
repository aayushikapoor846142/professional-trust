<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatGroup extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "chat_groups";
    protected $fillable = ['unique_id', 'name', 'type', 'description', 'added_by', 'group_image', 'hash_uid', 'chat_type', 'reference_id'];

    static function deleteRecord($group_id)
    {
        $group = ChatGroup::where("unique_id",$group_id)->first();
        $id = $group->id;
        GroupMembers::where("group_id",$id)->delete();
        GroupMessages::where("group_id",$id)->delete();
        GroupMessagesRead::whereHas('message',function($query) use($id){
            $query->where("group_id",$id);
        })->delete();
        $group->delete();
    }   

    public function reads()
    {
        return $this->hasMany(GroupMessagesRead::class, 'group_message_id');
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMembers::class, 'group_id');
    }

    public function members()
	{
		return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id')
                    ->wherePivotNull('deleted_at');;

	}

	public function groupMessages() {
        return $this->hasMany(GroupMessages::class, 'group_id'); 
    }
	public function lastMessage()
	{
	    return $this->hasOne(GroupMessages::class, 'group_id', 'id')->latest('created_at')  
                    ->where(function($query){
                        $query->whereNull('clear_for');
                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [auth()->user()->id]);
                    });
                   
	}

	public function messages()
	{
	    return $this->hasMany(GroupMessages::class);
	}
	
	protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            $randomNumber= randomNumber();
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = $randomNumber;
            $object->hash_uid =hash('sha256',$randomNumber);

        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public function groupJoinRequest() {
        return $this->hasOne(GroupJoinRequest::class, 'group_id')
                ->where('requested_by', auth()->id())
                ->where('status', 0);
    }

    public function groupAdmin() {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function groupRequest() {
        return $this->hasMany(GroupJoinRequest::class, 'group_id')->where('status', 0);
    }
    
    public function unreadMessage($group_id,$user_id ){
        $chat_message_read = GroupMessagesRead::where("group_id",$group_id)
        ->where("user_id",$user_id)
        ->where("status","unread")
        ->count();
        return $chat_message_read;
    }
}
