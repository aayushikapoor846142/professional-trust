<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatInvitation extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $table = 'chat_invitations';
    protected $fillable = ['unique_id','email','token','status','added_by'];
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

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    static function deleteRecord($id)
    {
        $getChatInvite=ChatInvitation::where("id", $id)->first();
        $user = User::where('email', $getChatInvite->email)->first();
        if($user){
         $chatRequest= ChatRequest::where(["sender_id"=> auth()->user()->id,"receiver_id"=>$user->id])->delete();
        }
        $getChatInvite->delete();

    }
}
