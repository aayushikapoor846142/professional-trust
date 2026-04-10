<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageCentreReaction extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="message_centre_reactions";
    protected $fillable = ['message_id','reaction','added_by'];

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
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }


}
