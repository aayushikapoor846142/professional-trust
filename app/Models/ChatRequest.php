<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRequest extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="chat_request";

    protected $fillable = ['unique_id','sender_id','receiver_id','is_accepted'];

    // Each like belongs to a specific user
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
        // Each like belongs to a specific user
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function chat()
    {
        return $this->hasOne(Chat::class, 'chat_request_id');
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
