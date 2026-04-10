<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MemberInDiscussion extends Model
{

    use HasFactory,SoftDeletes;

    protected $table = 'member_in_discussion';

    protected $fillable = [
        'discussion_id',
        'member_id',
        'status',
        'joined_via',
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
    /**
     * Get the discussion that the member belongs to.
     */
    public function discussion()
    {
        return $this->belongsTo(DiscussionBoard::class, 'discussion_boards_id');
    }

    /**
     * Get the member associated with this discussion.
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function user()
{
    return $this->belongsTo(User::class, 'member_id');
}

static function deleteRecord($id)
{
    MemberInDiscussion::where("member_id", $id)->delete();
 
}
}
