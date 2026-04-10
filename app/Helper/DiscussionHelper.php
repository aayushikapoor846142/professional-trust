<?php 

use App\Models\DiscussionCategory;
use App\Models\DiscussionBoard;

function discussionCategories(){
    $categories = DiscussionCategory::whereHas('discussionThreads')->get();
    return $categories;
}

function totalAllDiscussion($user_id = 0){
    return DiscussionBoard::where(function($query) use($user_id){
                    if($user_id != 0){
                        $query->where("added_by",$user_id);
                    }
                })
                ->count();
}

function totalDiscussionConnected($user_id)
{
     $query = DiscussionBoard::with(['user','category']);

            $query->whereHas('comments', function ($q) use($user_id){ // Search in comments by the current user
                $q->where('added_by', $user_id);
            });
            $query->where(function($query) use ($user_id) {
                $query->where('type', 'public')
                ->orWhere(function($query) use ($user_id) {
                    $query->where('type', 'private')
                    ->whereHas('member', function($query) use ($user_id) {
                        $query->where('member_id', $user_id);
                    });
                })
                ->orWhere('allow_join_request', 1);
            });
            $query->where(function($query) use ($user_id) {
            $query->where('type', 'public')
            ->orWhere(function($query) use ($user_id) {
                $query->where('type', 'private')
                ->whereHas('member', function($query) use ($user_id) {
                    $query->where('member_id', $user_id);
                });
            })
            ->orWhere('allow_join_request', 1);
        })->latest();

       return $discussionData=  $query->count();
}

function totalSavedDiscussion($user_id){
return DiscussionBoard::where('is_favourite',1)
                ->count();
}

function totalPendingRequest($user_id)
{
    $query = DiscussionBoard::where(function ($query) use ($user_id) {
                $query->where(function ($query) use ($user_id) {
                    $query->where('type', 'private')
                        ->whereHas('member', function ($query) use ($user_id) {
                            $query->where('member_id', $user_id);
                            $query->where('status', 'pending');
                            $query->orWhere('added_by', $user_id);
                        });
                });
            })->where(function ($query) use ($user_id) {
            $query->where('type', 'public')
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', 'private')
                        ->whereHas('member', function ($query) use ($user_id) {
                            $query->where('member_id', $user_id);
                        });
                })
                ->orWhere('allow_join_request', 1);
        })->latest();

         return $discussionData=  $query->count();
}