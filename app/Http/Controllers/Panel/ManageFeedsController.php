<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CommentFlag;
use App\Models\FeedCommentLike;
use App\Models\FeedComments;
use App\Models\FeedFlaggedComment;
use App\Models\FeedLikes;
use App\Models\Feeds;
use App\Models\FeedsConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\FeedService;
use Illuminate\Support\Facades\DB;
use App\Services\FeatureCheckService;
use View;
use App\Models\FeedFavourite;
class ManageFeedsController extends Controller
{
    protected $feedService;
    protected $featureCheckService;
    
    public function __construct(FeedService $feedService,FeatureCheckService $featureCheckService)
    {
        $this->feedService = $feedService;
         $this->featureCheckService = $featureCheckService;
    }

    public function index($status= 'all'){

         $user = auth()->user();
        $feedFeatureStatus = $this->featureCheckService->canAddFeed($user->id);
        $viewData['status'] = $status;
        $viewData['feedFeatureStatus'] = $feedFeatureStatus;
        $viewData['canAddFeed'] = $feedFeatureStatus['allowed'];
        return view("admin-panel.02-feeds.pages.feeds",$viewData);
    }
 public function addNewFeed()
    {

        $pageTitle = "Add New Feed";
        $viewData['pageTitle'] = $pageTitle;

        $view = View::make('admin-panel.02-feeds.forms.create-feed', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post' => 'required_without:media',
            'media' => 'required_without:post',
            'schedule_date' => 'nullable|date|required_if:status,scheduled',
        ], [
            'post.required_without' => 'The post content is required if no media file is provided.',
            'media.required_without' => 'The media file is required if the post content is not provided.',
            'media.file' => 'The media must be a valid file.',
            'media.mimes' => 'The media must be a file of type: jpg, jpeg, png, gif, or pdf.',
            'media.max' => 'The media file must not be greater than 2MB.',
            'schedule_date.required_if' => 'The scheduled date is required'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }

        $feed = $this->feedService->createFeed($request->all(), $request->file('media'));
        $response['feedId'] = $feed->unique_id;
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('feed/manage');
        $response['message'] = "Feed Posted!";
        return response()->json($response);
    }

    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input("status");
        $like = $request->input('like');
        $trending_comments = $request->input('trending_comments');
        $feed_post_by = $request->input('feed_post_by');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $seller_filters = $request->input('seller_filters');
        $records = Feeds::with([
                'user',
                'likes',
                'comments'
            ])
            ->when($search != '', function ($query) use ($search) {
                $query->where("post", "LIKE", "%" . $search . "%");
            })
            ->when(isset($feed_post_by) && $feed_post_by != '', function ($query) use ($feed_post_by) {
                if ($feed_post_by === 'professional') {
                    $query->whereHas('user', function ($q) {
                        $q->where('role', 'professional');
                    });
                } elseif ($feed_post_by === 'associate') {
                    $query->whereHas('user', function ($q) {
                        $q->where('role', 'associate');
                    });
                }
            })
            ->when(isset($start_date) && $start_date != '', function ($query) use ($start_date) {
                $query->whereDate('created_at', '>=', $start_date);
            })
            ->when(isset($end_date) && $end_date != '', function ($query) use ($end_date) {
                $query->whereDate('created_at', '<=', $end_date);
            })
            ->when(isset($seller_filters) && is_array($seller_filters), function ($query) use ($seller_filters) {
                foreach ($seller_filters as $filter) {
                    switch ($filter) {
                        case 'today':
                            $query->whereDate('created_at', today());
                            break;
                        case 'this_week':
                            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                            break;
                    }
                }
            })
            ->where(function($query) use($status){
                if($status != 'all' && $status != ''){
                    if($status == 'pinned'){
                        $query->where("added_by",auth()->user()->id);
                        $query->where("is_pin",1);
                    }elseif($status == 'liked'){
                        $query->whereHas("likes",function($q){
                            $q->where("added_by",auth()->user()->id);
                        })
                        ->where("status","post");
                    }elseif($status == 'favorites') {
                                $query->whereHas("favorite", function($q) {
                                    $q->where("user_id", auth()->user()->id);
                                })
                                ->where("status", "post");
                        }else if($status == "commented"){
                        $commentedFeedIds = FeedComments::where('added_by', auth()->user()->id)
                            ->pluck('feed_id')
                            ->toArray();

                        $query->whereIn('id', $commentedFeedIds)->where("status","post");
                    }else{
                        $status = $status == 'my-feeds'?'post':$status;
                        $query->where("added_by",auth()->user()->id);
                        $query->where("status",$status);
                    }
                    
                }else{
                    $query->where("status","post");
                }
            })
            ->whereHas("user");

            if(isset($like) && $like == 1){
                // Order by most liked feeds - count of likes relationship
                $records = $records->withCount('likes')
                    ->orderBy('likes_count', 'desc')
                    ->orderBy('id', 'desc');
            } elseif(isset($trending_comments) && $trending_comments == 1) {
                // Order by most commented/trending feeds - count of comments relationship
                $records = $records->withCount('comments')
                    ->orderBy('comments_count', 'desc')
                    ->orderBy('id', 'desc');
            } else {
                // Default ordering by ID desc
                $records = $records->orderBy('id', 'desc');
            }

            $records = $records->paginate(2);

        $viewData['records'] = $records;
        $viewData['currentUser'] = \Auth::user();
        $viewData['current_page'] = $records->currentPage() ?? 0;
        $viewData['last_page'] = $records->lastPage() ?? 0;
        $viewData['next_page'] = ($records->lastPage() ?? 0) != 0 ? ($records->currentPage() + 1) : 0;
        $view = \View::make('admin-panel.02-feeds.components.feed-ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    public function feedDetail($feed_id){
        $record = Feeds::where("unique_id",$feed_id)->first();
        $viewData['record'] = $record;
        return view("admin-panel.02-feeds.pages.feed-detail",$viewData);
    }
    public function saveComments($feed_unique_id, Request $request)
    {
        try {
            DB::beginTransaction();
            if(!$request->comment && !$request->file("attachment")){
                $response['status'] = false;
                $response['message'] = "Comment or attachment is required";
                return response()->json($response);
            }

            $comment = $this->feedService->addComment($feed_unique_id, $request->all(), $request->file('attachment'));
            
            $getFeed = Feeds::with('comments')->where("unique_id", $feed_unique_id)->first();
            $feedId = $getFeed->id;
            $parent_comment_id = 0;
            if ($request->input("comment_type") == 'reply') {
                $parent_feed = FeedComments::where("unique_id", $request->input("parent_comment_id"))->first();
                $parent_comment_id = $parent_feed->unique_id;
            }

            $comment_counts = FeedComments::where("feed_id",$feedId)->count();
            $socket_data = [
                "action" => "new_feed_comment",
                "comment" => $comment->comment,
                "parent_comment_id" => $parent_comment_id,
                "feed_id" => $feedId,
                "feed_unique_id" => $getFeed->unique_id,
                "last_comment_id" => $comment->id,
                "last_comment_unique_id" => $comment->unique_id,
                "comment_counts" => $comment_counts,
                "sender_id" => auth()->user()->id,
            ];
            initFeedContentSocket($feedId, $socket_data);
            if ($getFeed->added_by !=  auth()->user()->id) {
                $feeds = Feeds::where('id', $feedId)->where('added_by', $getFeed->added_by)->first();

                if (!empty($feeds) && $feeds->allow_to_mute == 0) {
                    $arr1 = [
                        'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has commented  ' . $comment->comment . 'on your feed ',
                        'type' => 'feed_comment',
                        'redirect_link' => null,
                        'is_read' => 0,
                        'user_id' => $getFeed->added_by ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];

                    chatNotification(arr: $arr1);
                }
            }

            $get_feed_comment = FeedComments::where('id', $comment->id)
                ->with('replyTo')->first();
            if ($request->reply_to != NULL && $get_feed_comment->replyTo && $get_feed_comment->replyTo->added_by != auth()->user()->id) {
                $arr_reply = [
                    'comment' => '*' . auth()->user()->first_name . " " .  auth()->user()->last_name . '* has replied to your comment ' . $get_feed_comment->replyTo->comment,
                    'type' => 'feed_comment',
                    'redirect_link' => null,
                    'is_read' => 0,
                    'user_id' => $comment->replyTo->added_by ?? '',
                    'send_by' => auth()->user()->id ?? '',
                ];
                chatNotification(arr: $arr_reply);
            }
            if ($get_feed_comment->comment != NULL || $get_feed_comment->media != NULL) {
                $viewData['feed_comm'] = $get_feed_comment;
            } else {
                $viewData['feed_comm'] = NULL;
            }

            $view = \View::make('admin-panel.04-profile.feeds.conversation.partials.comments-sender-message', $viewData);
            $contents = $view->render();

            $commented_user_ids = FeedComments::where("feed_id", $comment->feed_id)->get()->pluck('added_by')->toArray();
            $commented_users = User::whereIn('id', $commented_user_ids)->get();
            foreach ($commented_users as $usr) {

                if ($getFeed->added_by != $usr->id && $usr->id != auth()->user()->id) {
                    $arr2 = [
                        'comment' =>  '*' . auth()->user()->first_name . " " .  auth()->user()->last_name . '* has commented on the feed you commented on',
                        'type' => 'feed_comment',
                        'redirect_link' => null,
                        'is_read' => 0,
                        'user_id' => $usr->id ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];
                    chatNotification(arr: $arr2);
                }
            }

            DB::commit();
            $response['status'] = true;
            $response['contents'] = $contents;
            $response['id'] = $comment->id;
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            $response['status'] = false;
            $response['message'] = $e->getMessage()." File: ".$e->getFile()." Line No:".$e->getLine();
            return response()->json($response);
        }
    }

    public function fetchComments(Request $request){
        $last_comment_id = $request->last_comment_id;
        $first_comment_id = $request->first_comment_id;
        $comment_order = $request->comment_order;
        $feed_id = $request->feed_id;
        $feed = Feeds::where("unique_id",$feed_id)->first();
        $feedComments = FeedComments::where("feed_id",$feed->id)
                        ->where(function($query) use($comment_order,$last_comment_id,$first_comment_id){
                            if($comment_order  == 'latest'){
                                $query->where("id",">",$last_comment_id);
                            }else{
                                $query->where("id","<",$last_comment_id);
                            }
                        })
                        ->where("reply_to",0)
                        ->limit(2)
                        ->latest()
                        ->get();
        $last_comment = $feedComments->last();
        $first_comment = $feedComments->first();
        $hasMore = false;
        if($comment_order  == 'latest'){
            if(!empty($last_comment)){
                $has_more_comments = FeedComments::where("feed_id",$feed->id)
                            ->where("id",">",$last_comment->id)
                            ->where("reply_to",0)
                            ->count();
                if($has_more_comments > 0){
                    $hasMore = true;
                }
            }
        }else{
            if(!empty($first_comment)){
                $has_more_comments = FeedComments::where("feed_id",$feed->id)
                            ->where("id","<",$first_comment->id)
                            ->where("reply_to",0)
                            ->count();
                if($has_more_comments > 0){
                    $hasMore = true;
                }
            }
        }
        $viewData['hasMore'] = $hasMore;
        $viewData['comments'] = $feedComments;
        $view = view("admin-panel.02-feeds.comments.feed-comments",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        $response['has_more_comments'] = $hasMore;
        $response['first_comment_id'] = $first_comment->id??'';
        $response['last_comment_id'] = $last_comment->id??0;
        $response['comment_counts'] = FeedComments::where("feed_id",$feed->id)->count();
        return response()->json($response);
    }
    public function likeFeed(Request $request, $feed_id)
    {
        $liked = $this->feedService->likeFeed($feed_id);
        $likeCount = FeedLikes::where('feed_id', $feed_id)->count();
        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount,
        ]);
    }

    public function replyCommentForm($parent_comment_id,Request $request){
        $comment = FeedComments::where("unique_id",$parent_comment_id)->first();
        $viewData['parent_comment'] = $comment;
        $view = view("admin-panel.02-feeds.comments.reply-comment-form",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function fetchReplyComments(Request $request){
        $parent_comment_id = $request->parent_comment_id;
        $parent_comment = FeedComments::where("unique_id",$parent_comment_id)->first();
        $feedComments = FeedComments::where("reply_to",$parent_comment->id)->orderBy('id','desc')->get();
        // $main_parent_comment = findMainParentComment($parent_comment->id);
        $viewData['replyComments'] = $feedComments;
        $viewData['parent_id'] = $parent_comment->id;
        $view = view("admin-panel.02-feeds.comments.feed-comment-reply",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        $feedCommentCounts = FeedComments::where("reply_to",$parent_comment->id)->orderBy('id','desc')->count();
        $response['parent_comment_id'] = $parent_comment_id;
        $response['reply_counts'] = $feedCommentCounts;

        return response()->json($response);
    }

    public function loadMoreReply(Request $request){
        $parent_comment_id = $request->parent_comment_id;
        $last_comment_id = $request->last_comment_id;
        $parent_comment = FeedComments::where("id",$parent_comment_id)->first();
        $feedComments = FeedComments::where("reply_to",$parent_comment->id)
        ->where("id","<",$last_comment_id)
        ->orderBy("id","desc")
        ->take(2)
        ->get();
        $viewData['replyComments'] = $feedComments;
        $viewData['parent_id'] = $parent_comment->id;
        $view = view("admin-panel.02-feeds.comments.feed-comment-reply",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        $response['parent_comment_id'] = $parent_comment->unique_id??0;

        return response()->json($response);
    }

    public function edit($id)
    {
        $viewData['record'] = Feeds::where('unique_id', $id)->first();
        $viewData['pageTitle'] = "Edit Feed";
        $view = \View::make('admin-panel.02-feeds.forms.edit-feed', $viewData);

        // $view = view("admin-panel.04-profile.feeds.modal.edit",$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    /**
     * Update the specified country in the database.
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        
        $object = Feeds::where('unique_id', $id)->first();
        $post = NULL;
        if ($request->input('post')) {
            if($request->input('post') == "<br>"){
                $post = NULL;
            }else{
                $post = $request->input('post');
            }
        } 

        $request->merge(['post' => $post]);
        
        $validator = Validator::make($request->all(), [
            'post' => 'required_without:media',
            'media' => 'required_without:post|nullable',
        ], [
            'post.required_without' => 'The post content is required if no media file is provided.',
            'media.required_without' => 'The media file is required if the post content is not provided.',
            'media.file' => 'The media must be a valid file.',
            'media.mimes' => 'The media must be a file of type: jpg, jpeg, png, gif, or pdf.',
            'media.max' => 'The media file must not be greater than 2MB.',
        ]);
        if ($request->input('current_media') == null) {
            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();

                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                return response()->json($response);
            }
        }

       
        $object->post = $post;
        $status = 'post';

        if($request->scheduled_at != 'undefined'){
            $schedule_date = $request->scheduled_at;
        }else{
            $schedule_date = NULL;
        }
       
        $posted_at = date('Y-m-d');
        if ($request->status == 'draft') {
            $status = 'draft';
            $posted_at = NULL;
        } else if ($request->status == 'scheduled') {
            $status = 'scheduled';
            $posted_at = NULL;
        } else {
            $status = 'post';
        }
        if ($request->hasFile('media') && $request->file('media')) {
            $files = $request->file('media');
            $media_files = array();
            foreach($files as $media){
                $file = $media; // Store file in a variable
                $fileName = $file->getClientOriginalName(); // Get original name
                $newName = mt_rand(1, 99999) . "-" . $fileName; // Generate new name

                $uploadPath = feedDir(); // Destination path
                $sourcePath = $file->getPathname(); // Source path
                $media_files[] = $newName;
                $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName); // Upload via API
            }
            if($request->prev_files){
                $prev_files = implode(",",$request->prev_files);
                $object->media = $prev_files.",".implode(",",$media_files);
            }else{
                $object->media = implode(",",$media_files);
            }
            
        } else {
            if($request->prev_files){
                $prev_files = implode(",",$request->prev_files);
                $object->media = $prev_files;
            }else{
                $object->media = NULL;
            }
        }

        $object->status = $status;
        $object->schedule_date = $schedule_date;
        $object->posted_at = $posted_at;
        $object->edited_at = date('Y-m-d');
        $object->save();

        $response['status'] = true;
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    public function likeComment($comment_id){
        $like_count = $this->feedService->likeComment($comment_id);
        $comment = FeedComments::where("unique_id",$comment_id)->first();
        if($comment->reply_to == 0){
            $parent_comment_id = $comment->unique_id;
        }else{
            $parent_comment = findMainParentComment($comment->id);
            $parent_comment_id = $parent_comment->unique_id;
        }
        $socket_data = [
            "action" => "feed_liked",
            "parent_comment_id" => $parent_comment_id,
        ];
        initFeedContentSocket($comment->feed_id, $socket_data);

        $response['status'] = true;
        $response['like_count'] = $like_count;
        $response['message'] = "Comment liked";
        return response()->json($response);

    }

    public function unlikeComment($comment_id){
        $like_count = $this->feedService->unlikeComment($comment_id);
        $comment = FeedComments::where("unique_id",$comment_id)->first();
        if($comment->reply_to == 0){
            $parent_comment_id = $comment->unique_id;
        }else{
            $parent_comment = findMainParentComment($comment->reply_to);
            $parent_comment_id = $parent_comment->unique_id;
        }
        $socket_data = [
            "action" => "feed_unliked",
            "parent_comment_id" => $parent_comment_id,
        ];
        initFeedContentSocket($comment->feed_id, $socket_data);
        $response['status'] = true;
        $response['like_count'] = $like_count;
        $response['message'] = "Comment unlike";
        return response()->json($response);

    }

    public function deleteSingle($id)
    {
        $this->feedService->deleteFeed($id);
        return redirect(baseUrl('my-feeds/status/my-feeds'))->with("success", "Feed deleted successfully.");
    }

    public function flagComment(Request $request, $comment_id)
    {
        $viewData['commentFlags'] = CommentFlag::all();
        $viewData['pageTitle'] = "Flag Comment";
        $feedcomments= FeedComments::where("unique_id", $comment_id)->first();
        $existingComment= $feedcomments->flaggedByUsers()
        ->where('user_id', auth()->id())
        ->first();

        $viewData['existingComment'] = $existingComment;
        $viewData['feedcomments'] = $feedcomments;
        $view = \View::make('admin-panel.02-feeds.comments.flag-comment', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function saveFlagComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_flag_id' => 'required|exists:comment_flags,id',
            'description' => 'required|string|max:300',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $errors = $validator->errors()->toArray();
            $errMsg = [];
            foreach ($errors as $key => $error) {
                $errMsg[$key] = $error[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }

        $this->feedService->flagComment($request->comment_id, $request->comment_flag_id, $request->description);
        $feedComment = FeedComments::where('unique_id', $request->comment_id)->first();

        return response()->json([
            'feed_id'=>$feedComment->feed_id,
            'status' => true,
            'message' => 'Comment flagged successfully.',
        ]);
    }

    public function removeFlagComment($uniqueId)
    {
        $feedFlagged = FeedFlaggedComment::where('unique_id', $uniqueId)->first();
        if (!$feedFlagged) {
            return redirect()->back()->with("error", "Comment not found");
        }
        FeedFlaggedComment::deleteRecord($feedFlagged->id);
        return redirect()->back()->with("success", "Flag Removed Successfully");
    }

    public function addSetting($feed_id)
    {
        $feeds = Feeds::where("unique_id", $feed_id)->first();
        $viewData['feeds'] = $feeds;

        $view = view("admin-panel.02-feeds.pages.feed-setting", $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function saveSetting(Request $request, $feed_id)
    {
        $this->feedService->updateFeedSettings($feed_id, $request->all());
        $response['status'] = true;
        $response['message'] = 'Settings added successfully';
        return response()->json($response);
    }

    public function pinPost($id)
    {
        $this->feedService->pinPost($id);
        $message = "Feed pin successfully";
        $response['status'] = true;
        $response['message'] = $message;
        \Session::flash("success",$message);
        return response()->json($response);
    }

    public function unpinPost($id)
    {
        $this->feedService->unpinPost($id);
        $message = "Feed unpin successfully";
        $response['status'] = true;
        $response['message'] = $message;
        \Session::flash("success",$message);
        return response()->json($response);
    }

    public function follow($user_id)
    {
        $this->feedService->follow($user_id);
        $response['status'] = true;
        $response['message'] = "You are now following";
        if (request()->ajax()) {
            return response()->json($response);
        } else {
            return redirect()->back()->with('success', 'You are now following ');
        }
    }

    public function unfollow(Request $request, $user_id)
    {
        $this->feedService->unfollow($user_id, $request->remove_connection);
        $response['status'] = true;
        $response['message'] = "Unfollowed Successfully";
        if (request()->ajax()) {
            return response()->json($response);
        } else {
            return redirect()->back()->with('success', 'Unfollowed successfully');
        }
    }

    public function mutualFollows()
    {
        // Retrieve mutual followers (people who follow each other)
        $user = auth()->user();
        $mutualFollows = $user->following()->whereIn('id', $user->followers()->pluck('id'))->get();

        return view('users.mutual_follows', compact('mutualFollows'));
    }

    public function copyFeed($id, Request $request)
    {
        $this->feedService->copyFeed($id);
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('my-feeds');
        $response['message'] = "Feed Copied successfully";
        return response()->json($response);
    }
    public function repostFeed($id)
    {
        $this->feedService->repostFeed($id);
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('my-feeds');
        $response['message'] = "Feed repost successfully";
        return response()->json($response);
    }
    public function editComment($id)
    {
        $record = FeedComments::where('unique_id', $id)->where('added_by',auth()->user()->id)->first();
        if(empty($record)){
            $response['message'] = 'Cannot edit the comment';
            $response['status'] = false;
            return response()->json($response);
        }
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Comment";
        $view = \View::make('admin-panel.02-feeds.comments.edit-comment', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function updateComment($comment_id, Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'comment' => ['sometimes', 'input_sanitize'],
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();
                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                return response()->json($response);
            }

            $comment = $this->feedService->updateComment($comment_id, $request->all());
            $getFeed = Feeds::with('comments')->where("id", $comment->feed_id)->first();
            $feedId = $getFeed->id;

            $socket_data = [
                "action" => "edit_feed_comment",
                "feed_id" => $comment->feed_id,
                "comment" => $comment->comment,
                "feed_unique_id" => $getFeed->unique_id,
                "last_comment_id" => $comment->id,
                "last_comment_unique_id" => $comment->unique_id,
            ];
            initFeedContentSocket($feedId, $socket_data);
            if ($getFeed->added_by !=  auth()->user()->id) {
                $feeds = Feeds::where('id', $feedId)->where('added_by', $getFeed->added_by)->first();

                if (!empty($feeds) && $feeds->allow_to_mute == 0) {
                    $arr1 = [
                        'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has commented  ' . $comment->comment . 'on your feed ',
                        'type' => 'feed_comment',
                        'redirect_link' => null,
                        'is_read' => 0,
                        'user_id' => $getFeed->added_by ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];
                    chatNotification(arr: $arr1);
                }
            }

            DB::commit();
            $response['status'] = true;
            $response['id'] = $comment->id;
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            $response['status'] = false;
            $response['message'] = $e->getMessage()." File: ".$e->getFile()." Line No:".$e->getLine();
            return response()->json($response);
        }
    }

    public function deleteComment($comment_id)
    {
        $record = FeedComments::where('unique_id', $comment_id)->first();
        $this->feedService->deleteComment($comment_id);
        $socket_data = [
            "action" => "comment_deleted",
            "commentUniqueId" => $comment_id,
        ];
        initFeedContentSocket($record->feed_id, $socket_data);
        if(request()->ajax()) {
            $response['status'] = true;
            $response['message'] = "Comment deleted successfully";
            return response()->json($response);
        }
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function fetchUpdatedComments(Request $request){
        $last_comment_id = $request->last_comment_id;
        $comment_unique_id = $request->comment_unique_id;
        $feed_id = $request->feed_id;
        $feed = Feeds::where("unique_id",$feed_id)->first();
        $feedComments = FeedComments::where("feed_id",$feed->id)
                        ->where(function($query) use($last_comment_id){
                            $query->where("id",">=",$last_comment_id);
                        })
                        ->where("reply_to",0)
                        ->latest()
                        ->get();
        $viewData['comments'] = $feedComments;
        $view = view("admin-panel.02-feeds.comments.feed-comments",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        $response['comment_unique_id'] = $comment_unique_id;
        return response()->json($response);
    }

    // public function viewMedia($feed_id,$media)
    // {
    //     try {
    //         $feed = Feeds::where("unique_id", $feed_id)->first();
            
    //         if (!$feed || empty($feed->media)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Feed not found or no media available'
    //             ]);
    //         }

    //         $mediaFiles = explode(',', $feed->media);
    //         $files_arr = [];
    //         $current_file_index = 0;

    //         $previewableTypes = [
    //             'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
    //             'documents' => ['pdf'],
    //             'videos' => ['mp4', 'webm', 'ogg'],
    //             'audio' => ['mp3', 'wav', 'ogg'],
    //             'text' => ['txt', 'csv', 'log', 'json', 'xml', 'html', 'css', 'js'],
    //             'office' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
    //         ];

    //         foreach ($mediaFiles as $index => $fileKey) {
    //             $fileKey = urldecode(trim($fileKey));
    //             $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
    //             $filename = basename($fileKey);

    //             // Determine file type and previewability
    //             $isPreviewable = false;
    //             $fileType = '';
                
    //             foreach ($previewableTypes as $type => $extensions) {
    //                 if (in_array($extension, $extensions)) {
    //                     $isPreviewable = true;
    //                     $fileType = $type;
    //                     break;
    //                 }
    //             }

    //             // Build file path - adjust this according to your storage structure
    //             // $filePath = config('awsfilepath.feeds') . "/" . $feed_id . "/" . $fileKey;
    //             // $expiration = '+2 hours';

    //             // Generate URLs
    //             $previewUrl = $isPreviewable 
    //                 ?  feedDirUrl(trim($fileKey), 't')
    //                 : null;

    //             $downloadUrl = feedDirUrl(trim($fileKey), 't');

    //             // Check if image
    //             $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'];
    //             $isImage = in_array($extension, $imageExtensions);

    //             $fileData = [
    //                 'id' => $index,
    //                 'unique_id' => $feed_id,
    //                 'name' => $filename,
    //                 'type' => $isImage ? 'image' : $extension,
    //                 'size' => 'N/A', // You can implement actual size if needed
    //                 'url' => $previewUrl,
    //                 'download_url' => $downloadUrl,
    //                 'thumbnail' => $isImage ? $previewUrl : null,
    //                 'extension' => $extension,
    //                 'isPreviewable' => $isPreviewable
    //             ];

    //             // Handle PDF thumbnails
    //             if ($extension == 'pdf') {
    //                 try {
    //                     $file_data = feedDirUrl(trim($fileKey), 't');
    //                     $pdf_thumb = mediaUploadBaseCode("pdf-thumbnail", $file_data['data'], 'pdf-images', $filename);
    //                     $fileData['thumbnail'] = $pdf_thumb['thumbnail_base64'] ?? null;
    //                 } catch (\Exception $e) {
    //                     $fileData['thumbnail'] = null;
    //                 }
    //             }

    //             $files_arr[] = $fileData;
    //         }

    //         // $response = [
    //         //     'status' => true,
    //         //     'files_arr' => $files_arr,
    //         //     'current_file_index' => $current_file_index,
    //         //     'feed_id' => $feed_id
    //         // ];

    //         $pageTitle = "Preview";
    //         $viewData['pageTitle'] = $pageTitle;
    //         $viewData['fileKey'] = $fileKey;
    //         $viewData['filename'] = $filename;
    //         $viewData['extension'] = $extension;
    //         $viewData['previewUrl'] = $previewUrl;
    //         $viewData['fileType'] = $fileType;
    //         $viewData['files_arr'] = json_encode($files_arr);
    //         $viewData['current_file_index'] = $current_file_index; // Pass the current file index
            
    //         $view = view('admin-panel.02-feeds.feed-media-preview', $viewData);
    //         $contents = $view->render();
            
    //         $response['status'] = true;
    //         $response['contents'] = $contents;
    //         $response['files_arr'] = $files_arr;
    //         $response['current_file_index'] = $current_file_index;

          
    //         return response()->json($response);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    public function viewMedia($feed_id, $media)
    {
        try {
            $feed = Feeds::where("unique_id", $feed_id)->first();
            
            if (!$feed || empty($feed->media)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Feed not found or no media available'
                ]);
            }

            $mediaFiles = explode(',', $feed->media);
            $files_arr = [];
            $current_file_index = 0; // Default to first file
            $selectedFileData = null; // Will store data for the requested media

            $previewableTypes = [
                'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
                'documents' => ['pdf'],
                'videos' => ['mp4', 'webm', 'ogg'],
                'audio' => ['mp3', 'wav', 'ogg'],
                'text' => ['txt', 'csv', 'log', 'json', 'xml', 'html', 'css', 'js'],
                'office' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
            ];

            foreach ($mediaFiles as $index => $fileKey) {
                $fileKey = urldecode(trim($fileKey));
                $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
                $filename = basename($fileKey);

                // Check if this is the requested media file
                if ($fileKey === $media) {
                    $current_file_index = $index;
                }

                // Determine file type and previewability
                $isPreviewable = false;
                $fileType = '';
                
                foreach ($previewableTypes as $type => $extensions) {
                    if (in_array($extension, $extensions)) {
                        $isPreviewable = true;
                        $fileType = $type;
                        break;
                    }
                }

                $previewUrl = $isPreviewable ? feedDirUrl(trim($fileKey), 't') : null;
                // $downloadUrl = feedDirUrl(trim($fileKey), 't');
                $downloadUrl = url('download-media-file?dir='.feedDir().'&file_name='.$fileKey);
                // Check if image
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'];
                $isImage = in_array($extension, $imageExtensions);

                $fileData = [
                    'id' => $index,
                    'unique_id' => $feed_id,
                    'name' => $filename,
                    'type' => $isImage ? 'image' : $extension,
                    'size' => 'N/A',
                    'url' => $previewUrl,
                    'download_url' => $downloadUrl,
                    'thumbnail' => $isImage ? $previewUrl : null,
                    'extension' => $extension,
                    'isPreviewable' => $isPreviewable,
                    'fileKey' => $fileKey,
                    'filename' => $filename,
                    'previewUrl' => $previewUrl,
                    'fileType' => $fileType
                ];

                // Handle PDF thumbnails
                if ($extension == 'pdf') {
                    try {
                        $file_data = feedDirUrl(trim($fileKey), 't');
                        $pdf_thumb = mediaUploadBaseCode("pdf-thumbnail", $file_data['data'], 'pdf-images', $filename);
                        $fileData['thumbnail'] = $pdf_thumb['thumbnail_base64'] ?? null;
                    } catch (\Exception $e) {
                        $fileData['thumbnail'] = null;
                    }
                }

                $files_arr[] = $fileData;
            }

            // Get the selected file's data
            if (isset($files_arr[$current_file_index])) {
                $selectedFile = $files_arr[$current_file_index];
                $selectedFileData = [
                    'fileKey' => $selectedFile['fileKey'],
                    'filename' => $selectedFile['filename'],
                    'extension' => $selectedFile['extension'],
                    'previewUrl' => $selectedFile['previewUrl'],
                    'fileType' => $selectedFile['fileType']
                ];
            }

            $pageTitle = "Preview";
            $viewData['pageTitle'] = $pageTitle;
            $viewData['fileKey'] = $selectedFileData['fileKey'] ?? '';
            $viewData['filename'] = $selectedFileData['filename'] ?? '';
            $viewData['extension'] = $selectedFileData['extension'] ?? '';
            $viewData['previewUrl'] = $selectedFileData['previewUrl'] ?? '';
            $viewData['fileType'] = $selectedFileData['fileType'] ?? '';
            $viewData['files_arr'] = json_encode($files_arr);
            $viewData['current_file_index'] = $current_file_index;
            
            $view = view('admin-panel.02-feeds.components.feed-media-preview', $viewData);
            $contents = $view->render();
            
            $response['status'] = true;
            $response['contents'] = $contents;
            $response['files_arr'] = $files_arr;
            $response['current_file_index'] = $current_file_index;

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function viewCommentMedia($comment_id,$media)
    {
        try {
            $comment = FeedComments::where("unique_id", $comment_id)->first();
            
            if (!$comment || empty($comment->media)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Feed not found or no media available'
                ]);
            }

            $mediaFiles = explode(',', $comment->media);
            $files_arr = [];
            $current_file_index = 0; // Default to first file
            $selectedFileData = null; // Will store data for the requested media

            $previewableTypes = [
                'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
                'documents' => ['pdf'],
                'videos' => ['mp4', 'webm', 'ogg'],
                'audio' => ['mp3', 'wav', 'ogg'],
                'text' => ['txt', 'csv', 'log', 'json', 'xml', 'html', 'css', 'js'],
                'office' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
            ];

            foreach ($mediaFiles as $index => $fileKey) {
                $fileKey = urldecode(trim($fileKey));
                $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
                $filename = basename($fileKey);

                // Check if this is the requested media file
                if ($fileKey === $media) {
                    $current_file_index = $index;
                }

                // Determine file type and previewability
                $isPreviewable = false;
                $fileType = '';
                
                foreach ($previewableTypes as $type => $extensions) {
                    if (in_array($extension, $extensions)) {
                        $isPreviewable = true;
                        $fileType = $type;
                        break;
                    }
                }

                $previewUrl = $isPreviewable ? commentDirUrl(trim($fileKey), 't') : null;
                // $downloadUrl = feedDirUrl(trim($fileKey), 't');
                $downloadUrl = url('download-media-file?dir='.commentDir().'&file_name='.$fileKey);
                // Check if image
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'];
                $isImage = in_array($extension, $imageExtensions);

                $fileData = [
                    'id' => $index,
                    'unique_id' => $comment_id,
                    'name' => $filename,
                    'type' => $isImage ? 'image' : $extension,
                    'size' => 'N/A',
                    'url' => $previewUrl,
                    'download_url' => $downloadUrl,
                    'thumbnail' => $isImage ? $previewUrl : null,
                    'extension' => $extension,
                    'isPreviewable' => $isPreviewable,
                    'fileKey' => $fileKey,
                    'filename' => $filename,
                    'previewUrl' => $previewUrl,
                    'fileType' => $fileType
                ];

                // Handle PDF thumbnails
                if ($extension == 'pdf') {
                    try {
                        $file_data = commentDirUrl(trim($fileKey), 't');
                        $pdf_thumb = mediaUploadBaseCode("pdf-thumbnail", $file_data['data'], 'pdf-images', $filename);
                        $fileData['thumbnail'] = $pdf_thumb['thumbnail_base64'] ?? null;
                    } catch (\Exception $e) {
                        $fileData['thumbnail'] = null;
                    }
                }

                $files_arr[] = $fileData;
            }

            // Get the selected file's data
            if (isset($files_arr[$current_file_index])) {
                $selectedFile = $files_arr[$current_file_index];
                $selectedFileData = [
                    'fileKey' => $selectedFile['fileKey'],
                    'filename' => $selectedFile['filename'],
                    'extension' => $selectedFile['extension'],
                    'previewUrl' => $selectedFile['previewUrl'],
                    'fileType' => $selectedFile['fileType']
                ];
            }

            $pageTitle = "Preview";
            $viewData['pageTitle'] = $pageTitle;
            $viewData['fileKey'] = $selectedFileData['fileKey'] ?? '';
            $viewData['filename'] = $selectedFileData['filename'] ?? '';
            $viewData['extension'] = $selectedFileData['extension'] ?? '';
            $viewData['previewUrl'] = $selectedFileData['previewUrl'] ?? '';
            $viewData['fileType'] = $selectedFileData['fileType'] ?? '';
            $viewData['files_arr'] = json_encode($files_arr);
            $viewData['current_file_index'] = $current_file_index;
            
            $view = view('admin-panel.02-feeds.components.feed-media-preview', $viewData);
            $contents = $view->render();
            
            $response['status'] = true;
            $response['contents'] = $contents;
            $response['files_arr'] = $files_arr;
            $response['current_file_index'] = $current_file_index;

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function FavouritesFeed($id, $type)
    {
        if ($type == 'add') {
        $feed_fav = new FeedFavourite;
        $feed_fav->user_id = auth()->user()->id;
        $feed_fav->feed_id = $id;
        $feed_fav->save();
        $message = "Feed added to favorites successfully";
    } else {
        FeedFavourite::where('user_id', auth()->user()->id)
                   ->where('feed_id', $id)
                   ->delete();
        $message = "Feed removed from favorites successfully";
    }
    
    return response()->json([
        'status' => true,
        'message' => $message
    ]);
    }
}
