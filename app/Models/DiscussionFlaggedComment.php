<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DiscussionFlaggedComment extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'comment_id',
        'discussion_id',
        'user_id',
        'comment_flag_id',
        'description',
    ];

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

     static function deleteRecord($id)
    {
        DiscussionFlaggedComment::where("id", $id)->delete();
    }
}
