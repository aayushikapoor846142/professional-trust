<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feeds extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'media','post','added_by','allow_to_repost','allow_to_mute','allow_to_view','posted_at','edited_at','is_pin'];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    
    static function deleteRecord($id)
    {
        Feeds::where("id", $id)->delete();
        FeedLikes::where('feed_id', $id)->delete();
        $comments = FeedComments::where('feed_id', $id)->get();
        foreach($comments as $comment){
            FeedComments::deleteRecord($comment->id);
        }
    }
    public function likes()
    {
        return $this->hasMany(FeedLikes::class, 'feed_id');
    }
    
    public function comments()
    {
        return $this->hasMany(FeedComments::class, 'feed_id');
    }

    public function originalPost()
    {
        return $this->belongsTo(Feeds::class, 'id');
    }

    public function repost()
    {
        return $this->belongsTo(Feeds::class, 'post_id');
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

     public function favorite()
    {
        return $this->hasMany(FeedFavourite::class, 'feed_id');
    }
}

