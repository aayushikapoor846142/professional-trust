<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedComments extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['unique_id', 'feed_id', 'added_by', 'comment', 'media'];


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

    // Each comment belongs to a specific user
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Each comment is related to a specific feed
    public function feed()
    {
        return $this->belongsTo(Feeds::class, 'feed_id');
    }

    static function deleteRecord($id)
    {
        $comment = FeedComments::where("id",$id)->first();
        FeedComments::where("id", $id)->delete();
        FeedCommentLike::where('comment_id', $id)->delete();
        $replyComment = FeedComments::where("reply_to",$comment->id)->get();
        if(!empty($replyComment)){
            foreach($replyComment as $reply_comment){
                FeedComments::deleteRecord($reply_comment->id);
            }
        }
    }

    public function replyTo()
    {
        return $this->belongsTo(FeedComments::class, 'reply_to');
    }

    public function replyComments()
    {
        return $this->hasMany(FeedComments::class, 'reply_to')->orderBy('id','desc');
    }

    public function flaggedByUsers()
    {
        return $this->hasMany(FeedFlaggedComment::class, 'comment_id');
    }

    public function isFlaggedByUser($userId)
    {
        return $this->flaggedByUsers()->where('user_id', $userId)->first();
    }

    public function commentLiked()
    {
        return $this->hasMany(FeedCommentLike::class, 'comment_id');
    }

}
