<?php 

use App\Models\FeedComments;
use App\Models\Feeds;
use App\Models\FeedFavourite;
function totalPostedFeed($user_id = 0){
    return Feeds::where(function($query) use($user_id){
                    if($user_id != 0){
                        $query->where("added_by",$user_id);
                    }
                })
                ->where("status","post")
                ->count();
}
function draftFeeds($user_id){
    return Feeds::where("added_by",$user_id)->where("status","draft")->count();
}
function scheduledFeed($user_id){
    return Feeds::where("added_by",$user_id)->where("status","scheduled")->count();
}
function commentedFeed($user_id){
    return Feeds::whereHas("comments",function($query) use($user_id){
                $query->where("added_by",$user_id);
            })
            ->where("status","post")
            ->count();
}
function pinnedFeeds($user_id){
    return Feeds::where("added_by",$user_id)->where("is_pin","1")->count();
}
function likedFeeds($user_id){
    return Feeds::whereHas("likes",function($query) use($user_id){
                $query->where("added_by",$user_id);
            })
            ->where("status","post")
            ->count();
}

function favoriteFeeds($user_id) {
    return Feeds::whereHas("favorite", function($query) use($user_id) {
                $query->where("user_id", $user_id);
            })
            ->where("status", "post")
            ->count();
}

function findMainParentComment($comment_id){
    $comment = FeedComments::where("id",$comment_id)->first();
  
    if(!empty($comment) && $comment->reply_to == 0){
        return $comment;
    }else{
        if(!empty($comment)){
            $parent_comment = findMainParentComment($comment->reply_to);
            return $parent_comment;
        }else{
            return false;
        }
    }
}
function checkMoreComment($parent_id,$last_comment_id){
    return FeedComments::where("reply_to",$parent_id)->where("id","<",$last_comment_id)->count();
}


if(!function_exists('checkFeedSettings')){
    function checkFeedSettings($feed_id,$module)
    {
        $feed = Feeds::where('id',$feed_id)->first();
        if(!empty($feed)){
            if($module == "allow_to_repost"){
                if($feed->allow_to_repost == 1){
                    return true;
                }else{
                    if($feed->added_by == auth()->user()->id){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }else{
            return true;
        }
    }
}

if (!function_exists("checkFavFeed")) {
function checkFavFeed($id)
{
    return FeedFavourite::where('feed_id',$id)->where('user_id',auth()->user()->id)->first();
}
}
?>
