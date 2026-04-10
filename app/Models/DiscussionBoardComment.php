<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DiscussionBoardComment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'discussion_board_comments';

    protected $fillable = [
        'discussion_boards_id',
        'comments',
        'files',
        'added_by',
        'reply_to',
        'edited_at',
        'mark_as_answer',
        'unique_id',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'mark_as_answer' => 'boolean',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function discussion()
    {
        return $this->belongsTo(DiscussionBoard::class, 'discussion_boards_id');
    }


    public function replyTo()
    {
        return $this->belongsTo(DiscussionBoardComment::class, 'reply_to');
    }


    static function deleteRecord($id)
    {
        DiscussionBoardComment::where("id", $id)->delete();
    }

    public function replyComments()
    {
        return $this->hasMany(DiscussionBoardComment::class, 'reply_to');
    }

    public function commentLikes()
    {
        return $this->hasMany(DiscussionCommentLike::class, 'comment_id');
    }

    public function potentialAnswer()
    {
        return $this->hasMany(PotentialDiscussionComment::class, 'comment_id');
    }

     public function flaggedByUsers()
    {
        return $this->hasMany(DiscussionFlaggedComment::class, 'comment_id');
    }

    public function isFlaggedByUser($userId)
    {
        return $this->flaggedByUsers()->where('user_id', $userId)->first();
    }
}