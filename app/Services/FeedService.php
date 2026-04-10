<?php

namespace App\Services;

use App\Models\Feeds;
use App\Models\FeedComments;
use App\Models\FeedLikes;
use App\Models\FeedFlaggedComment;
use App\Models\FeedCommentLike;
use App\Models\FeedsConnection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\FeatureCheckService;

class FeedService
{
    protected $featureCheckService;
    public function __construct(FeatureCheckService $featureCheckService)
    {
        $this->featureCheckService = $featureCheckService;
    }

    public function createFeed(array $data, $mediaFiles = null)
    {
       
        $status = $data['status'] ?? 'post';
      if (($data['status'] ?? null) === "published") {
            $status = "post";
        }
        
        $schedule_date = $status === 'scheduled' ? $data['schedule_date'] ?? null : null;
        $posted_at = $status === 'draft' || $status === 'scheduled' ? null : date('Y-m-d');
    
        $feed = new Feeds();
        $feed->post = htmlentities($data['post'] ?? '');
        $feed->media = $this->handleMediaUpload($data['media'] ?? null, []);
        $feed->added_by = Auth::id();
        $feed->unique_id = randomNumber();
        $feed->status = $status;
        $feed->schedule_date = $schedule_date;
        $feed->posted_at = $posted_at;
        $feed->save();

        $this->featureCheckService->savePlanFeature(
            'feeds', 
            \Auth::user()->id, 
            1, // action type: add
            1, // count: 1 article
            [
                'feed_id' => $feed->id,
            ]
        );

        return $feed;
    }

    public function updateFeed(Feeds $feed, array $data, $mediaFiles = null)
    {
        $post = $data['post'] ?? null;
        if ($post === "<br>") {
            $post = null;
        }
        $feed->post = $post;

        $status = $data['status'] ?? 'post';
        $schedule_date = $status === 'scheduled' ? $data['scheduled_at'] ?? null : null;
        $posted_at = $status === 'draft' || $status === 'scheduled' ? null : date('Y-m-d');

        $feed->media = $this->handleMediaUpload($mediaFiles, $data['prev_files'] ?? null);
        $feed->status = $status;
        $feed->schedule_date = $schedule_date;
        $feed->posted_at = $posted_at;
        $feed->edited_at = date('Y-m-d');
        $feed->save();

        return $feed;
    }

    
    public function handleMediaUpload($mediaFiles, $existingMedia = null)
    {
        $media_files = [];

        if ($mediaFiles) {
            // Ensure it's always an array
            $mediaFiles = is_array($mediaFiles) ? $mediaFiles : [$mediaFiles];

            foreach ($mediaFiles as $media) {
                if ($media->isValid()) {
                    $originalName = $media->getClientOriginalName();
                    $newName = mt_rand(10000, 99999) . '-' . $originalName;
                    
                    $uploadPath = feedDir(); // Your custom function
                    $sourcePath = $media->getPathname(); // Temporary path

                    // Upload file via custom API — we ignore temp path in result
                    mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);

                    // Only storing new filename
                    $media_files[] = $newName;
                    // \Log::info($newName);
                }
            }
        }
        // \Log::info($existingMedia);
        // Add existing media if provided
        if ($existingMedia) {
            if (is_array($existingMedia)) {
                $media_files = array_merge($existingMedia, $media_files);
            } else {
                $media_files[] = $existingMedia;
            }
        }

        // Return only file names as comma-separated string
        return !empty($media_files) ? implode(",", $media_files) : null;
    }


    public function addComment($feedUniqueId, array $data, $attachments = null)
    {
        \Log::info($data);
        $feed = Feeds::where("unique_id", $feedUniqueId)->firstOrFail();
        $comment = new FeedComments();
        $comment->feed_id = $feed->id;
        $comment->comment = $data['comment'] ?? '';
        $comment->added_by = Auth::id();

        if ($attachments) {
            $attachedFiles = [];
            foreach ($attachments as $file) {
                $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'pdf'];
                $fileResponse = validateFileType($file, $allowedTypes);
                if (!$fileResponse['status']) {
                    throw new \Exception($fileResponse['message']);
                }
                $fileName = $file->getClientOriginalName();
                $newName = mt_rand(1, 99999) . "-" . $fileName;
                $uploadPath = commentDir();
                $sourcePath = $file->getPathName();
                $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
                $attachedFiles[] = $newName;
            }
            $comment->media = implode(',', $attachedFiles);
        }
       
        if (!empty($data['reply_to'])) {
            $parent = FeedComments::where("unique_id", $data['reply_to'])->first();
            $comment->reply_to = $parent ? $parent->id : 0;
        }
      
        $parent_comment_id = 0;
        if (isset($data["comment_type"]) && $data["comment_type"] == 'reply') {
            $parent_feed = FeedComments::where("unique_id", $data["parent_comment_id"])->first();
            $comment->reply_to = $parent_feed->id;
            $parent_comment_id = $parent_feed->unique_id;
        }

        $comment->save();
        return $comment;
    }

    public function likeFeed($feedId)
    {
        $like = FeedLikes::withTrashed()
            ->where('feed_id', $feedId)
            ->where('added_by', Auth::id())
            ->first();

        if ($like) {
            if ($like->trashed()) {
                $like->restore();
                return true;
            } else {
                $like->delete();
                return false;
            }
        } else {
            FeedLikes::create([
                'feed_id' => $feedId,
                'added_by' => Auth::id(),
            ]);
            return true;
        }
    }

    public function likeComment($commentId)
    {
        $comment = FeedComments::where("unique_id", $commentId)->firstOrFail();
        FeedCommentLike::updateOrCreate([
            'user_id' => Auth::id(),
            'comment_id' => $comment->id
        ]);
        return FeedCommentLike::where("comment_id", $comment->id)->count();
    }

    public function unlikeComment($commentId)
    {
        $comment = FeedComments::where("unique_id", $commentId)->firstOrFail();
        FeedCommentLike::where([
            'user_id' => Auth::id(),
            'comment_id' => $comment->id
        ])->delete();
        return FeedCommentLike::where("comment_id", $comment->id)->count();
    }

    public function flagComment($commentUniqueId, $flagId, $description)
    {
        $comment = FeedComments::where('unique_id', $commentUniqueId)->firstOrFail();
        FeedFlaggedComment::updateOrCreate(
            [
                'comment_id' => $comment->id,
                'user_id' => Auth::id(),
            ],
            [
                'feed_id' => $comment->feed_id,
                'comment_flag_id' => $flagId,
                'description' => $description,
            ]
        );
        return true;
    }

    public function updateComment($commentId, array $data)
    {
        $comment = FeedComments::where("unique_id", $commentId)->where('added_by', Auth::id())->firstOrFail();
        $comment->comment = $data['comment'] ?? '';
        $comment->edited_at = date("Y-m-d H:i:s");
        $comment->save();
        return $comment;
    }

    public function deleteComment($commentId)
    {
        $comment = FeedComments::where('unique_id', $commentId)->firstOrFail();
        FeedComments::deleteRecord($comment->id);
        return true;
    }

    public function pinPost($feedId)
    {
        $feed = Feeds::where('unique_id', $feedId)->firstOrFail();
        $feed->update(['is_pin' => 1]);
        return true;
    }

    public function unpinPost($feedId)
    {
        $feed = Feeds::where('unique_id', $feedId)->firstOrFail();
        $feed->update(['is_pin' => 0]);
        return true;
    }

    public function follow($userId)
    {
        Auth::user()->following()->syncWithoutDetaching([
            $userId => ['unique_id' => randomNumber()]
        ]);
        FeedsConnection::create([
            'unique_id' => randomNumber(),
            'connection_with' => $userId,
            'user_id' => Auth::id(),
            'connection_type' => 'follow',
            'status' => 'active'
        ]);
        return true;
    }

    public function unfollow($userId, $removeConnection = false)
    {
        Auth::user()->following()->detach($userId);
        FeedsConnection::where('user_id', Auth::id())->where('connection_with', $userId)->delete();
        if ($removeConnection == "yes") {
            removeUserConnection($userId, Auth::id());
        }
        return true;
    }

    public function copyFeed($feedId)
    {
        $original = Feeds::where('unique_id', $feedId)->firstOrFail();
        $copy = $original->replicate();
        $copy->unique_id = randomNumber();
        $copy->post_id = $original->id;

        if ($original->added_by != Auth::id()) {
            $copy->added_by = Auth::id();
        }
        $copy->save();
        return $copy;
    }

    public function repostFeed($feedId)
    {
        $original = Feeds::where('unique_id', $feedId)->firstOrFail();
        $copy = $original->replicate();
        $copy->is_repost = 1;
        $copy->unique_id = randomNumber();
        $copy->post_id = $original->id;

        if ($original->added_by != Auth::id()) {
            $copy->added_by = Auth::id();
        }
        $copy->save();
        return $copy;
    }

    public function updateFeedSettings($feedId, array $settings)
    {
        $feed = Feeds::where("unique_id", $feedId)->firstOrFail();
        $feed->update([
            'allow_to_view' => $settings['settings'],
            'allow_to_mute' => $settings['allow_to_mute'],
            'allow_to_repost' => $settings['allow_to_repost']
        ]);
        return true;
    }

    public function deleteFeed($feedId)
    {
        $feed = Feeds::where('unique_id', $feedId)->where('added_by', Auth::id())->firstOrFail();
        Feeds::deleteRecord($feed->id);
        return true;
    }
} 