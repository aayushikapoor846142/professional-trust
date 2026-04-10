<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedLikes extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'feed_id','added_by'];

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

    // Each like belongs to a specific user
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Each like is related to a specific feed
    public function feed()
    {
        return $this->belongsTo(Feeds::class, 'feed_id');
    }
}
