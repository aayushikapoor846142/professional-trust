<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMessagesRead extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "group_message_read";
    protected $fillable = ['unique_id', 'group_id', 'message_id', 'user_id', 'read_at', 'status'];

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
	    return $this->belongsTo(GroupMessages::class,'group_message_id');
	}

}
