<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DiscussionBoard extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id',
        'category_id',
        'topic_title',
        'description',
        'added_by',
        'status',
        'is_favourite'
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

    public function category()
    {
        return $this->belongsTo(DiscussionCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function comments()
    {
        return $this->hasMany(DiscussionBoardComment::class, 'discussion_boards_id');
    }

    static function deleteRecord($id)
    {
        DiscussionBoardComment::where("discussion_boards_id", $id)->delete();
        MemberInDiscussion::where("discussion_boards_id", $id)->delete();
        DiscussionBoard::where("id", $id)->delete();
    }

    public function member()
    {
        return $this->hasMany(MemberInDiscussion::class, 'discussion_boards_id');
    }

    public function members()
    {
        return $this->hasMany(MemberInDiscussion::class, 'discussion_boards_id');
    }

    public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->added_by == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->added_by == $userId || in_array($this->added_by, $staffIds);
        }
    }
 public function flaggedComments()
{
    return $this->hasMany(DiscussionFlaggedComment::class, 'discussion_id');
}
}
