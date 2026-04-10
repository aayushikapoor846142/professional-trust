<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\PotentialDiscussionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use Auth;
use App\Models\DiscussionBoard;
use App\Models\User;
use App\Models\DiscussionCategory;
use App\Models\DiscussionBoardComment;
use Illuminate\Support\Facades\DB;
use App\Models\MemberInDiscussion;
use App\Models\ChatRequest;
use App\Models\DiscussionCommentLike;
use App\Models\CommentFlag;
use App\Models\DiscussionFlaggedComment;
use App\Services\FeatureCheckService;

class ManageDiscussionThreadsController extends Controller
{
    
    protected $featureCheckService;

    public function __construct(FeatureCheckService $featureCheckService)
    {
        $this->featureCheckService = $featureCheckService;
    }


    public function index($list_type= 'all-discussion'){
       
        $user = auth()->user();
        $threadFeatureStatus = $this->featureCheckService->canAddThread($user->id);
        
        $viewData['threadFeatureStatus'] = $threadFeatureStatus;
        $viewData['canAddThread'] = $threadFeatureStatus['allowed'];

        $viewData['pageTitle'] = "Discussion Thread";
        $viewData['subPageTitle'] = ucwords(str_replace('-', ' ', $list_type));
        $viewData['categories'] = DiscussionCategory::get();
        $viewData['list_type'] = $list_type;
        $viewData['category_id'] = null;
        // $viewData['list_type'] = "all";
        return view("admin-panel.05-discussion-boards.manage-discussion-thread.lists",$viewData);
    }

    public function getAjaxList(Request $request)
    {
        $categoryId = $request->category_id;
        
        $trending_comments = $request->input('trending_comments');
        if ($request->list_type) {
            $type = $request->list_type;
        } else {
            $type = "all";
        }

        if ($request->search) {
            $search = $request->search;
        } else {
            $search = "";
        }
        $userId = auth()->user()->id;
        $query = DiscussionBoard::with(['user', 'category']);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('topic_title', 'LIKE', "%{$search}%") // Search in feed post
                    ->orWhereHas('user', function ($q) use ($search) { // Search in user name
                        $q->where('first_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('comments', function ($q) use ($search) { // Search in comments by the current user
                        $q->where('added_by', auth()->user()->id)
                            ->where('comments', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($type == 'my-discussion') {
            $query->where('added_by', $userId);
        } else if ($type == "discussion-connected") {
            $query->whereHas('comments', function ($q) { // Search in comments by the current user
                $q->where('added_by', auth()->user()->id);
            });
            $query->where(function ($query) use ($userId) {
                $query->where('type', 'public')
                    ->orWhere(function ($query) use ($userId) {
                        $query->where('type', 'private')
                            ->whereHas('member', function ($query) use ($userId) {
                                $query->where('member_id', $userId);
                            });
                    })
                    ->orWhere('allow_join_request', 1);
            });
        } else if ($type == "pending-requests") {
            $query->where(function ($query) use ($userId) {
                $query->where(function ($query) use ($userId) {
                    $query->where('type', 'private')
                        ->whereHas('member', function ($query) use ($userId) {
                            $query->where('member_id', $userId);
                            $query->where('status', 'pending');
                            $query->orWhere('added_by', $userId);
                        });
                });
            });
        } else if ($type == "saved-discussion") {
            $query->where('is_favourite', 1);
        }

        $query->where(function ($query) use ($userId) {
            $query->where('type', 'public')
                ->orWhere(function ($query) use ($userId) {
                    $query->where('type', 'private')
                        ->whereHas('member', function ($query) use ($userId) {
                            $query->where('member_id', $userId);
                        });
                })
                ->orWhere('allow_join_request', 1);
        });
        if ($categoryId !== null && $categoryId !== 'null') {

            $query->where('category_id', $categoryId);
        }

        $discussion_categories = $request->discussion_categories;

        if(!empty($discussion_categories)){
            $query->whereIn('category_id',$discussion_categories);
        }

        $discussion_type = $request->discussion_type;

        if($discussion_type != ''){
            $query->where('type',$discussion_type);
        }


        if(isset($trending_comments) && $trending_comments == 1) {
            $query = $query->withCount('comments')
                ->orderBy('comments_count', 'desc')
                ->orderBy('id', 'desc');
        } else {
            // Default ordering by ID desc
            $query = $query->orderBy('id', 'desc');
        }

        $query->latest();

        $discussionData = $query->paginate();


        $viewData['discussionData'] = $discussionData;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.ajax-list', $viewData);
        $contents = $view->render();

        $response['contents'] = $contents;
        $response['last_page'] = $discussionData->lastPage();
        $response['current_page'] = $discussionData->currentPage();
        $response['total_records'] = $discussionData->total();
        return response()->json($response);

    }

    public function discussionDetail($discussionUId , Request $request)
    {

        try {
            $user_id = auth()->user()->id;
            $discussionBoard = DiscussionBoard::with(['user', 'comments'])
                ->orderBy('id', 'desc')
                ->where('unique_id', $discussionUId)
                ->first();
            $user_id = auth()->user()->id;
            $commenterIds = DiscussionBoardComment::where('discussion_boards_id', $discussionBoard->id)
                ->where('added_by', '!=', $user_id) // Exclude the current user
                ->pluck('added_by')
                ->toArray();

            $memberIds = MemberInDiscussion::where('discussion_boards_id', $discussionBoard->id)
                ->where('status','active')
                ->pluck('member_id')
                ->toArray();

            $allUserIds = array_unique(array_merge($commenterIds, $memberIds));
            $members = User::whereIn('id', $allUserIds)->get();

            $pendingMembersIds = MemberInDiscussion::where('discussion_boards_id', $discussionBoard->id)
            ->where('status','pending')
            ->pluck('member_id')
            ->toArray();
        
            $pendingMembers = User::whereIn('id', $pendingMembersIds)->get();

            $viewData['members'] = $members;
            $viewData['pendingMembers'] = $pendingMembers;
            $viewData['discussion'] = $discussionBoard;
            $viewData['discussion_id'] = $discussionBoard->id;
            $viewData['discussionUId'] = $discussionBoard->unique_id;
            $viewData['pageTitle'] = 'Discussion Threads';
            $viewData['list_type'] = "all-discussion";
            $viewData['categories'] = DiscussionCategory::get();

            return view('admin-panel.05-discussion-boards.manage-discussion-thread.discussion-manage', $viewData);
        } catch (\Exception $e) {
            // Handle any errors that occur during the process.
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
    }

    public function saveComments($discussionId, Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'comment' => 'nullable|string|max:5000',
                'attachment' => 'nullable|file|max:10240', // 10MB max
                'reply_to' => 'nullable|exists:discussion_board_comments,unique_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            // Check if at least comment or attachment is provided
            if (empty($request->comment) && !$request->hasFile('attachment')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment or attachment is required'
                ]);
            }

            // Verify discussion exists
            $discussion = DiscussionBoard::findOrFail($discussionId);
            
            // Create new comment
            $discussionComment = new DiscussionBoardComment();
            $discussionComment->discussion_boards_id = $discussionId;
            $discussionComment->comments = $request->comment ?? '';
            $discussionComment->added_by = auth()->user()->id;
            
            // Handle reply to comment
            if ($request->filled('reply_to')) {
                $parentComment = DiscussionBoardComment::where('unique_id', $request->reply_to)->first();
                $discussionComment->reply_to = $parentComment->id;
            }

            // Handle file upload
            $uploadedFiles = [];
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                
                // Validate file type
                $allowedTypes = [
                    'jpeg', 'jpg', 'png', 'gif', 'bmp', 'webp', 'svg', // Images
                    'xls', 'xlsx', 'csv', // Excel
                    'pdf', // PDF
                    'txt', // Plain text
                    'mp3', // Audio
                    'mp4', 'mpeg' // Video
                ];

                $fileValidation = validateFileType($file, $allowedTypes);
                if (!$fileValidation['status']) {
                    return response()->json($fileValidation);
                }

                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $newFileName = time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
                
                // Upload file
                $uploadPath = discussionCommentDir();
                $sourcePath = $file->getPathName();
                
                $uploadResponse = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newFileName);
                
                if (($uploadResponse['status'] ?? '') === 'success') {
                    $uploadedFiles[] = $newFileName;
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to upload file. Please try again.'
                    ]);
                }
            }

            // Set files field if files were uploaded
            if (!empty($uploadedFiles)) {
                $discussionComment->files = implode(',', $uploadedFiles);
            }

            // Save the comment
            $discussionComment->save();

            // Prepare socket data for real-time updates
            $socketData = [
                "action" => "new_comment",
                "parent_comment_id" => $discussionComment->reply_to ?? 0,
                "comment" => $discussionComment->comments ?: 'No comment',
                "discussion_board_id" => $discussionId,
                "last_comment_id" => $discussionComment->id,
                "last_comment_unique_id" => $discussionComment->id,
                "sender_id" => auth()->user()->id,
            ];

            // Send socket notification
            initDiscussionThreadSocket($discussionId, $socketData);
            $newComment = 'New comment posted on discussion thread *'.$discussion->topic_title.'*';
            // Send notifications to other users who commented on this discussion
            $this->sendCommentNotifications($discussion, $newComment);

            // Prepare response data
            $viewData = [
                'discussion_comm' => $discussionComment,
                'discussion_comments' => DiscussionBoardComment::where("discussion_boards_id", $discussionId)->get()
            ];

            // Render comment view
            $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
            $contents = $view->render();

            return response()->json([
                'status' => true,
                'message' => 'Comment posted successfully',
                'contents' => $contents,
                'id' => $discussionComment->id
            ]);

        } catch (\Exception $e) {
            // \Log::error('Error saving discussion comment: ' . $e->getMessage(), [
            //     'discussion_id' => $discussionId,
            //     'user_id' => auth()->id(),
            //     'request_data' => $request->all()
            // ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while posting the comment. Please try again. '.$e->getMessage()." File: ".$e->getFile()." LINE: ".$e->getLine()
            ]);
        }
    }

    /**
     * Send notifications to users who commented on the discussion
     */
    private function sendCommentNotifications($discussion, $newComment)
    {
        try {
            // Get all users who commented on this discussion (excluding the current user and discussion owner)
            $commentedUserIds = DiscussionBoardComment::where("discussion_boards_id", $discussion->id)
                ->where('added_by', '!=', auth()->user()->id)
                ->where('added_by', '!=', $discussion->added_by)
                ->distinct()
                ->pluck('added_by')
                ->toArray();

            if (empty($commentedUserIds)) {
                return;
            }

            $commentedUsers = User::whereIn('id', $commentedUserIds)->get();
            
            foreach ($commentedUsers as $user) {
                $notificationData = [
                    'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has commented on the discussion that you commented on',
                    'type' => 'new_comment',
                    'redirect_link' => null,
                    'is_read' => 0,
                    'user_id' => $user->id,
                    'send_by' => $discussion->added_by,
                ];
                
                chatNotification($notificationData);
            }
        } catch (\Exception $e) {
            \Log::error('Error sending comment notifications: ' . $e->getMessage());
        }
    }

    public function updateComment(Request $request, $commentId)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|max:5000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            // Find the comment
            $comment = DiscussionBoardComment::where("unique_id", $commentId)->first();
            
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found'
                ]);
            }

            // Check if user has permission to edit this comment
            if ($comment->added_by !== auth()->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You can only edit your own comments'
                ]);
            }

            // Update the comment
            $comment->comments = $request->comment;
            $comment->edited_at = now();
            $comment->save();

            // Send socket notification for real-time updates
            $socketData = [
                "action" => "comment_edited",
                "commentUniqueId" => $commentId,
                "editedComment" => $comment->comments,
            ];

            initDiscussionThreadSocket($comment->discussion_boards_id, $socketData);

            return response()->json([
                'status' => true, 
                'message' => 'Comment updated successfully.', 
                'updated_comment' => $request->comment
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating discussion comment: ' . $e->getMessage(), [
                'comment_id' => $commentId,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the comment. Please try again.'
            ]);
        }
    }

    
    // public function fetchComment(Request $request)
    // {

    //     $object = DiscussionBoardComment::with('user', 'discussion')
    //         ->where('discussion_boards_id', $request->id)
    //         // ->where("reply_to",0)
    //         ->orderBy('created_at', 'asc') // Optional ordering
    //         ->get();


    //     $viewData['record'] = $object;
    //     $view = View::make('admin-panel.05-discussion-boards.discussion-threads.comments', $viewData);
    //     $contents = $view->render();
    //     $response['status'] = true;
    //     $response['contents'] = $contents;
    //     return response()->json($response);
    // }
    public function getDiscussionContent($discussion_boards_id, Request $request)
    {
        try {
            $order_type  = $request->order_type;
            $call_from  = $request->call_from;
            $last_comment_id  = $request->last_comment_id;
            $first_comment_id  = $request->first_comment_id;
            $viewData['last_comment_id'] = $last_comment_id;
            $userId = auth()->user()->id;
            $discussion_comments = DiscussionBoardComment::where('discussion_boards_id', $discussion_boards_id)
                ->where(function ($query) use ($last_comment_id,$first_comment_id,$call_from) {
                    if($call_from == 'load_more'){
                        $query->where("id", "<", $first_comment_id);
                    }else{
                        $query->where("id", ">", $last_comment_id);
                    }
                })
                // ->where("reply_to",0)
                ->latest()
                ->limit(10)
                ->get();

            $first_comment_id = $discussion_comments[count($discussion_comments) - 1]->id ?? 0;
            $discussion_comments = $discussion_comments->sortBy("id");
            $last_comment = DiscussionBoardComment::where('discussion_boards_id', $discussion_boards_id)
                                                ->latest()
                                                ->first();
            $last_comment = $discussion_comments->last();
            $discussion_comments = $discussion_comments->reverse();

            // $last_comment_id = $last_comment->id ?? 0;
            $has_prev_comment = DiscussionBoardComment::where('discussion_boards_id', $discussion_boards_id)
                                                    ->where(function ($query) use ($first_comment_id) {
                                                        $query->where("id", "<", $first_comment_id);
                                                    })
                                                    ->count();  ;
            $has_new_comment = DiscussionBoardComment::where('discussion_boards_id', $discussion_boards_id)
                                                    ->where(function ($query) use ($last_comment_id) {
                                                        $query->where("id", ">", $last_comment_id);
                                                    })
                                                    ->count();         
                                      
            $response['first_comment_id'] = $first_comment_id;
            $response['has_new_comment'] = $has_new_comment;
            $response['has_prev_comment'] = $has_prev_comment;
            $response['last_comment_unique_id'] = $last_comment ? $last_comment->unique_id : 0;
            $response['last_comment_id'] = $last_comment->id ?? 0;
            $response['comment_id'] = $last_comment->unique_id ?? 0;
            if($call_from == 'load_more'){
                $viewData['discussion_comments'] = $discussion_comments;
                $viewData['has_new_comment'] = $has_new_comment;
                $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
                $contents = $view->render();
                $response['discussion_comments'] = $discussion_comments->count();
                $response['contents'] = $contents;
                $response['new_comment'] = true;
            }else{
                if ($last_comment_id != ($last_comment->id ?? 0)) {
                    $viewData['discussion_comments'] = $discussion_comments;
                    $viewData['has_new_comment'] = $has_new_comment;
                    $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
                    $contents = $view->render();
                    $response['discussion_comments'] = $discussion_comments->count();
                    $response['contents'] = $contents;
                    $response['new_comment'] = true;
                } else {
                    $response['new_comment'] = false;
                }
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage() ." File: ".$e->getFile()." LINE: " . $e->getLine();
            return response()->json($response);
        }
    }

    public function categoryList($category_id)
    {
        $category = DiscussionCategory::where('unique_id',$category_id)->first();

        $list_type = "all-discussion";
        
        
        $viewData['category_id'] = $category->id;
        $viewData['pageTitle'] = "Discussion Thread";
        $viewData['subPageTitle'] = ucwords(str_replace('-', ' ', $list_type));
        $viewData['categories'] = DiscussionCategory::get();
        $viewData['list_type'] = $list_type;
        $threadFeatureStatus = $this->featureCheckService->canAddThread(auth()->user()->id);
        
        $viewData['threadFeatureStatus'] = $threadFeatureStatus;
        $viewData['canAddThread'] = $threadFeatureStatus['allowed'];
        return view("admin-panel.05-discussion-boards.manage-discussion-thread.lists",$viewData);
        // $viewData['discussionData'] = $discussionData;
        // $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.ajax-list', $viewData);
        // $contents = $view->render();

        // $response['contents'] = $contents;
        // $response['last_page'] = $discussionData->lastPage();
        // $response['current_page'] = $discussionData->currentPage();
        // $response['total_records'] = $discussionData->total();
        // return response()->json($response);

    }


    public function commentLike($comment_id,Request $request){
        $comment_icon = $request->emoji;
        DiscussionCommentLike::updateOrCreate([
            'comment_id'=>$comment_id,
            'discussion_board_id'=>$request->discussion_board_id,
            'user_id' => auth()->user()->id
        ],
        [
            'comment_icon'=>$comment_icon,
        ]
        );
        $response['status'] = true;
        $discussion_comments = DiscussionBoardComment::where('id', $comment_id)
                ->latest()
                ->get();
        $first_comment_id = $discussion_comments->last()->unique_id;
        $viewData['discussion_comments'] = $discussion_comments;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
        $contents = $view->render();
        $response['comment_id'] = $first_comment_id;
        $response['contents'] = $contents;
        return response()->json($response);
    }
    public function commentUnlike($id,Request $request){
        DiscussionCommentLike::where([
            'comment_id'=>$request->comment_id,
            'discussion_board_id'=>$request->discussion_board_id,
            'id'=>$id,
            'user_id' => auth()->user()->id
        ])->delete();
        $response['status'] = true;
        $discussion_comments = DiscussionBoardComment::where('id', $request->comment_id)
                                                    ->latest()
                                                    ->get();
        $first_comment_id = $discussion_comments->last()->unique_id;
        $viewData['discussion_comments'] = $discussion_comments;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
        $contents = $view->render();
        $response['comment_id'] = $first_comment_id;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    
    public function deleteComment($comment_id)
    {

        $record = DiscussionBoardComment::where('unique_id', $comment_id)->first();

        DiscussionBoardComment::deleteRecord($record->id);
        $socket_data = [
            "action" => "comment_deleted",
            "commentUniqueId" => $comment_id,

        ];
        initDiscussionThreadSocket($record->discussion_boards_id, $socket_data);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function markCommentAsAnswer($id,Request $request){
        $comment = DiscussionBoardComment::where("unique_id",$id)->first();
        DiscussionBoardComment::where("discussion_boards_id",$comment->discussion_boards_id)->update(['mark_as_answer'=>0]);
        DiscussionBoardComment::where("unique_id",$id)->update(['mark_as_answer'=>1]);
        $response['status'] = true;
        $discussion_comments = DiscussionBoardComment::where('id', $comment->id)
                ->latest()
                ->get();
        $first_comment_id = $discussion_comments->last()->unique_id;
        $viewData['discussion_comments'] = $discussion_comments;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
        $contents = $view->render();
        $response['comment_id'] = $first_comment_id;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function removeCommentAsAnswer($id,Request $request){
        $comment = DiscussionBoardComment::where("unique_id",$id)->first();
        DiscussionBoardComment::where("unique_id",$id)->update(['mark_as_answer'=>0]);
        $response['status'] = true;
        $discussion_comments = DiscussionBoardComment::where('id', $comment->id)
                ->latest()
                ->get();
        $first_comment_id = $discussion_comments->last()->unique_id;
        $viewData['discussion_comments'] = $discussion_comments;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
        $contents = $view->render();
        $response['comment_id'] = $first_comment_id;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function markAsPotentialAnswer($comment_id,Request $request){
        $comment = DiscussionBoardComment::where("unique_id",$comment_id)->first();
        PotentialDiscussionComment::updateOrCreate([
            'comment_id'=>$comment->id,
            'user_id' => auth()->user()->id
        ]
        );
        $response['status'] = true;
        $discussion_comments = DiscussionBoardComment::where('id', $comment->id)
                ->latest()
                ->get();
        $first_comment_id = $discussion_comments->last()->unique_id;
        $viewData['discussion_comments'] = $discussion_comments;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
        $contents = $view->render();
        $response['comment_id'] = $first_comment_id;
        $response['contents'] = $contents;
        return response()->json($response);
    }
    public function removeAsPotentialAnswer($id,Request $request){
        $comment = DiscussionBoardComment::where("unique_id",$id)->first();
        DiscussionCommentLike::where([
            'comment_id'=>$comment->id,
            'user_id' => auth()->user()->id
        ])->delete();
        $response['status'] = true;
        $discussion_comments = DiscussionBoardComment::where('id', $comment->id)
                                                    ->latest()
                                                    ->get();
        $first_comment_id = $discussion_comments->last()->unique_id;
        $viewData['discussion_comments'] = $discussion_comments;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.comment', $viewData);
        $contents = $view->render();
        $response['comment_id'] = $first_comment_id;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function flagComment(Request $request, $comment_id)
    {
        $viewData['commentFlags'] = CommentFlag::all();
        $viewData['pageTitle'] = "Flag Comment";
        $discussionComment = DiscussionBoardComment::where("unique_id", $comment_id)->first();
        $existingComment= $discussionComment->flaggedByUsers()
        ->where('user_id', auth()->id())
        ->first();
      
        $viewData['existingComment'] = $existingComment;
        $viewData['discussioncomment'] = $discussionComment;
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.flag-discussion-comment', $viewData);
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

        $discussionComment = DiscussionBoardComment::where('unique_id', $request->comment_id)->first();

        if (!$discussionComment) {
            return response()->json([
                'status' => false,
                'message' => 'Comment not found.',
            ]);
        }

  DiscussionFlaggedComment::updateOrCreate(
        [
            'comment_id' => $discussionComment->id,
            'user_id' => auth()->id(),
        ],
        [
            'discussion_id' => $discussionComment->discussion_boards_id,
            'comment_flag_id' =>  $request->comment_flag_id ?? '',
            'description' =>  $request->description ?? '',
        ]
    );

        
        return response()->json([
            'discussion_id'=>$discussionComment->discussion_boards_id,
            'status' => true,
            'message' => 'Comment flagged successfully.',
        ]);
    }

    public function removeFlagComment($uniqueId)
    {
         $discussionFlagged = DiscussionFlaggedComment::where('unique_id', $uniqueId)->first();
             if (!$discussionFlagged) {
                return redirect()->back()->with("error", "Comment not found");
        }
        DiscussionFlaggedComment::deleteRecord($discussionFlagged->id);
        return redirect()->back()->with("success", "Flag Removed Successfully");


    }

    public function editDiscussionThread($id)
    {
        $viewData['members'] = ChatRequest::where(function ($query) {
                    $query->where('receiver_id', auth()->user()->id)
                        ->orWhere('sender_id', auth()->user()->id);
                })
                ->where('is_accepted', 1)
                ->orderBy('id', 'desc')
                ->with(['sender', 'receiver'])
                ->get()
                ->map(function ($chat) {
                    $user = $chat->sender_id == auth()->id() ? $chat->receiver : $chat->sender;
                                                            if (!$user) {
        return null;
    }
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'role' => $user->role
                    ];
                })
                ->filter(function ($user) {
                   return $user !== null 
           && is_array($user) 
           && isset($user['role']) 
           && $user['role'] === 'professional';
                })
                ->unique('id')
                ->values();
        $viewData['record'] = DiscussionBoard::where('unique_id', $id)->first();

        $viewData['pageTitle'] = "Edit Discussion";
        $viewData['categories'] = DiscussionCategory::get();
        return view('admin-panel.05-discussion-boards.manage-discussion-thread.edit', $viewData);
    }

    public function editDiscussionThreadModal($id)
    {
        $viewData['members'] = ChatRequest::where(function ($query) {
                    $query->where('receiver_id', auth()->user()->id)
                        ->orWhere('sender_id', auth()->user()->id);
                })
                ->where('is_accepted', 1)
                ->orderBy('id', 'desc')
                ->with(['sender', 'receiver'])
                ->get()
                ->map(function ($chat) {
                    $user = $chat->sender_id == auth()->id() ? $chat->receiver : $chat->sender;
                    if (!$user) {
                        return null;
                    }
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'role' => $user->role
                    ];
                })
                ->filter(function ($user) {
                   return $user !== null 
           && is_array($user) 
           && isset($user['role']) 
           && $user['role'] === 'professional';
                })
                ->unique('id')
                ->values();
        
        $viewData['record'] = DiscussionBoard::with('members')->where('unique_id', $id)->first();
        
        if (!$viewData['record']) {
            return response()->json([
                'status' => false,
                'message' => 'Discussion thread not found'
            ]);
        }
        


        $viewData['pageTitle'] = "Edit Discussion";
        $viewData['categories'] = DiscussionCategory::get();
        
        $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.edit-discussion-modal', $viewData);
        $contents = $view->render();
        
        return response()->json([
            'status' => true,
            'contents' => $contents
        ]);
    }


    public function updateDiscussionThread($id, Request $request)
    {

        $object = DiscussionBoard::where('unique_id', $id)->first();

        $validator = Validator::make($request->all(), [
            'topic_title' => 'required',
            'description' => 'required',
            'discussion_category' => 'required',
            'type' => 'required',
        ], );

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

        $object->category_id = $request->discussion_category;
        $object->topic_title = $request->topic_title;
        $object->short_description = $request->input("short_description");
        $object->description = htmlentities($request->description);
        $object->added_by = \Auth::user()->id;
        $object->type = $request->type;
        // Debug: Log the files being saved
        \Log::info('Update Discussion - Files data:', [
            'record_id' => $object->id,
            'unique_id' => $object->unique_id,
            'old_files' => $object->files,
            'new_files' => $request->updated_files,
            'updated_files_param' => $request->input('updated_files'),
            'file_param' => $request->input('file')
        ]);
        
        $object->files = $request->updated_files;
        $object->status = 0;
        $object->save();
        
        // Debug: Log after save
        \Log::info('Update Discussion - After save:', [
            'saved_files' => $object->files
        ]);
        
       $selectedMembers = $request->input('selected_members', []);
    
        // Fetch existing members for the discussion
        $existingMemberIds = MemberInDiscussion::where('discussion_boards_id', $object->id)
            ->pluck('member_id')
            ->toArray();

        foreach ($selectedMembers as $memberId) {
            
            // Check if the member is already in the discussion
            if (!in_array($memberId, $existingMemberIds)) {
                // Add new member
                $members = new MemberInDiscussion();
                $members->discussion_boards_id = $object->id;
                $members->member_id = $memberId;
                $members->joined_via = 'invite';
                $members->added_by = \Auth::user()->id;
                $members->save();
                
                // Fetch user details
                $user = User::find($memberId);
                if ($user) {
                    $mailData = [
                        'discussionLink' => baseUrl("discussion-threads/manage/" . $object->unique_id),
                        'professional_name' => $user->first_name . " " . $user->last_name,
                        'sender_name' => auth()->user()->first_name . " " . auth()->user()->last_name,
                    ];
        
                    $message = view('emails.discussion-invitations', $mailData)->render();
        
                    $parameter = [
                        'to' => $user->email,
                        'to_name' => $user->first_name . ' ' . $user->last_name,
                        'message' => $message,
                        'subject' => siteSetting("company_name") . ": Received a Discussion Board Request",
                        'view' => 'emails.discussion-invitations',
                        'data' => $mailData,
                    ];
        
                    sendMail($parameter);
                }
            }
        }
        $response['discussionId'] = $object->unque_id;
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('manage-discussion-threads');

        $response['message'] = "Discussion Updated!";
        return response()->json($response);
    }


    public function addeDiscussionThread(Request $request)
    {
        try {
            $user_id=auth()->user()->id;
            $viewData['discussion'] = $discussion = DiscussionBoard::with(['user','comments'])->whereHas('user', function ($query)use ($user_id) {
                    $query->where('added_by', $user_id);

                })
                ->orderBy('id', 'desc')
                ->first();
            $viewData['type'] = 'discussion';
            $discussionData =discussionListData();

        $viewData['members'] = ChatRequest::where(function ($query) {
                    $query->where('receiver_id', auth()->user()->id)
                        ->orWhere('sender_id', auth()->user()->id);
                })
                ->where('is_accepted', 1)
                ->orderBy('id', 'desc')
                ->with(['sender', 'receiver'])
                ->get()
                ->map(function ($chat) {
                    $user = $chat->sender_id == auth()->id() ? $chat->receiver : $chat->sender;
                                             if (!$user) {
        return null;
    }
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'role' => $user->role
                    ];
                })
                ->filter(function ($user) {
                       return $user !== null 
           && is_array($user) 
           && isset($user['role']) 
           && $user['role'] === 'professional';
                })
                ->unique('id')
                ->values();

        
            

                $viewData['categories'] = DiscussionCategory::get();
                $viewData['discussionData'] = $discussionData;
                $viewData['template'] = 'discussion-manage';
                $viewData['discussion_comments'] =NULL;
                $viewData['feed_connections']=NULL;
                $viewData['discussion_id'] =$discussion_id=NULL;
                $viewData['currentFeedUser'] =NULL;
                $viewData['user'] = User::where("id",auth()->user()->id)->first();
                $viewData['discussionUId'] = '';
                $viewData['pageTitle'] = 'Discussion Boards';
                $viewData['showSidebar'] = true;
            if($discussion){
               
                $viewData['discussion_id'] =$discussion_id=$discussion->id;
            }
            
           userActiveStatus();  
           return view('admin-panel.05-discussion-boards.manage-discussion-thread.add-discussion-thread',$viewData);
            
        } catch (\Exception $e) {
            // Handle any errors that occur during the process.
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
    }

    public function addDiscussionThreadModal(Request $request)
    {
        try {
            $user_id=auth()->user()->id;
            $viewData['discussion'] = $discussion = DiscussionBoard::with(['user','comments'])->whereHas('user', function ($query)use ($user_id) {
                    $query->where('added_by', $user_id);

                })
                ->orderBy('id', 'desc')
                ->first();
            $viewData['type'] = 'discussion';
            $discussionData =discussionListData();

        $viewData['members'] = ChatRequest::where(function ($query) {
                    $query->where('receiver_id', auth()->user()->id)
                        ->orWhere('sender_id', auth()->user()->id);
                })
                ->where('is_accepted', 1)
                ->orderBy('id', 'desc')
                ->with(['sender', 'receiver'])
                ->get()
                ->map(function ($chat) {
                    $user = $chat->sender_id == auth()->id() ? $chat->receiver : $chat->sender;
                                             if (!$user) {
        return null;
    }
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'role' => $user->role
                    ];
                })
                ->filter(function ($user) {
                       return $user !== null 
           && is_array($user) 
           && isset($user['role']) 
           && $user['role'] === 'professional';
                })
                ->unique('id')
                ->values();

        
            

                $viewData['categories'] = DiscussionCategory::get();
                $viewData['discussionData'] = $discussionData;
                $viewData['template'] = 'discussion-manage';
                $viewData['discussion_comments'] =NULL;
                $viewData['feed_connections']=NULL;
                $viewData['discussion_id'] =$discussion_id=NULL;
                $viewData['currentFeedUser'] =NULL;
                $viewData['user'] = User::where("id",auth()->user()->id)->first();
                $viewData['discussionUId'] = '';
                $viewData['pageTitle'] = 'Add Discussion Thread';
                $viewData['showSidebar'] = true;
            if($discussion){
               
                $viewData['discussion_id'] =$discussion_id=$discussion->id;
            }
            
           userActiveStatus();  
           $view = View::make('admin-panel.05-discussion-boards.manage-discussion-thread.add-discussion-thread-modal', $viewData);
           $contents = $view->render();
           $response['contents'] = $contents;
           $response['status'] = true;
           return response()->json($response);
            
        } catch (\Exception $e) {
            // Handle any errors that occur during the process.
            return response()->json([
                'status' => false,
                'message' => "An error occurred: " . $e->getMessage()
            ]);
        }
    }

    public function saveDiscussionThread(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'topic_title' => 'required|string|input_sanitize|max:255',
             'description' => 'required|string',
             'short_description' => 'required|string|input_sanitize',
            'discussion_category'=> 'required',
            'type' => 'required',
        ] );
        
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
      
        $object = new DiscussionBoard();
        $object->category_id = $request->input("discussion_category");
        $object->topic_title = $request->input("topic_title");
        $object->description = htmlentities($request->input("description"));
        $object->short_description = $request->input("short_description");
        $object->files = $request->file;
        $object->type =$request->input("type"); 
        $object->allow_join_request = $request->input('allow_join_request') ?? 0;
        $object->added_by = \Auth::user()->id;
        $object->status = 0;
        $object->save();

        $result = $this->featureCheckService->savePlanFeature(
            'threads', 
            \Auth::user()->id, 
            1, 
            1, 
            [
                'threads_id' => $object->id,
                'threads_title' => $object->topic_title
            ]
        );

        if($request->input("type") == 'private'){
            $selectedMembers = $request->input('selected_members');
            foreach ($selectedMembers as $memberId) {

                $members = new MemberInDiscussion();
                $members->discussion_boards_id = $object->id;
                $members->member_id = $memberId;
                $members->joined_via = 'invite';
                $members->added_by = \Auth::user()->id;
                $members->save();
        
                $user = User::find($memberId);
                if ($user) {

                    $mailData = array();
                    $mailData['discussionLink'] = baseUrl("discussion-boards/manage/" . $object->unique_id);
                    $mailData['professional_name'] = $user->first_name . " " . $user->last_name;
                    $mailData['sender_name'] = auth()->user()->first_name . " " . auth()->user()->last_name;
                    $mail_message = \View::make('emails.discussion-invitations', $mailData);
                    $view = \View::make('emails.discussion-invitations', $mailData);
                    $message = $view->render();

                    $parameter = [
                        'to' => $user->email,
                        'to_name' => $user->first_name . ' ' . $user->last_name,
                        'message' => $message,
                        'subject' => siteSetting("company_name") . ": Received a Discussion Board Request ",
                        'view' => 'emails.discussion-invitations',
                        'data' => $mailData,
                    ];
                    sendMail($parameter);
                
                }
            }
        }

        $response['discussionId'] = $object->unque_id;
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('manage-discussion-threads');
        $response['message'] = "Discussion Posted!";
     //   return redirect()->back()->with( "success", "Feed Posted!");

          return response()->json($response);
    }

    public function deleteDiscussion($id)
    {
        $action = DiscussionBoard::where('unique_id', $id)->first();
        $discussionCommentExist = DiscussionBoardComment::where('discussion_boards_id', $action->id)->exists();
        if ($discussionCommentExist) {
            DiscussionBoardComment::where('discussion_boards_id', $action->id)->delete();
        }
        DiscussionBoard::deleteRecord($action->id);

        return redirect(baseUrl('manage-discussion-threads'))->with("success", "Record deleted successfully.");

    }

    public function replyCommentForm($parent_comment_id,Request $request){
        $comment = DiscussionBoardComment::where("unique_id",$parent_comment_id)->first();
        $viewData['parent_comment'] = $comment;
        $view = view("admin-panel.05-discussion-boards.manage-discussion-thread.reply-comment-form",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function deleteMemberFromDiscussion($id,$discussion_id)
    {
   
        $user = User::where('unique_id', $id)->first();
        $discussion = DiscussionBoard::where('unique_id',$discussion_id)->first();
        $discussionCommentExist = DiscussionBoardComment::where('discussion_boards_id',$discussion->id)->where('added_by', $user->id)->get();
        if ($discussionCommentExist->isNotEmpty()) {
            DiscussionBoardComment::where('discussion_boards_id', $discussion->id)->delete();
        }
        MemberInDiscussion::where('member_id',$user->id)->where('discussion_boards_id',$discussion->id)->delete();

        return redirect(baseUrl('manage-discussion-threads/' . $discussion->unique_id.'/detail'))->with("success", "Record deleted successfully.");

    }

    public function acceptMemberForDiscussion($id,$discussion_id)
    {
        // Find the user by unique ID
        $action = User::where('unique_id', $id)->first();
        $discussion = DiscussionBoard::where('unique_id',$discussion_id)->first();
        // Find the pending member record
        $member = MemberInDiscussion::where('member_id', $action->id)
                                    ->where('discussion_boards_id',$discussion->id)
                                        ->where('status', 'pending')
                                        ->first();
        if ($member) {
            // Update the member's status to 'active'
            $member->status = 'active';
            $member->save();
        }
    
        
        return redirect(baseUrl('manage-discussion-threads/' . $member->discussion->unique_id.'/detail'))->with("success", "Member accepted successfully.");

    }

    public function uploadDiscussionFiles(Request $request)
    {
        $newName = "";
        if ($file = $request->file('file')) {

            $fileName = $file->getClientOriginalName();
            $newName = mt_rand(1, 99999) . "-" . $fileName;

            $uploadPath = discussionDir();
            $sourcePath = $file->getPathName();
            $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);

            if (($api_response['status'] ?? '') === 'success') {
                $response['status'] = true;
                $response['filename'] = $newName;
                $response['message'] = "Record added successfully";
            } else {
                $response['status'] = false;
                $response['message'] = "Error while upload";
            }

        }

        return response()->json($response);
    }

    public function viewMedia($feed_id, $media)
    {
        try {
            $feed = DiscussionBoard::where("unique_id", $feed_id)->first();
            
            if (!$feed || empty($feed->files)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Discussion not found or no media available'
                ]);
            }

            $mediaFiles = explode(',', $feed->files);
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

                $previewUrl = $isPreviewable ? discussionDirUrl(trim($fileKey), 't') : null;
                // $downloadUrl = feedDirUrl(trim($fileKey), 't');
                $downloadUrl = url('download-media-file?dir='.discussionDir().'&file_name='.$fileKey);
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
                        $file_data = discussionDirUrl(trim($fileKey), 't');
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
            
            

            $view = view('admin-panel.05-discussion-boards.manage-discussion-thread.discussion-media-preview', $viewData);
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

    public function viewCommentMedia($comment_id, $media)
    {
        try {
            $comment = DiscussionBoardComment::where("unique_id", $comment_id)->first();
            
            if (!$comment || empty($comment->files)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found or no media available'
                ]);
            }

            $mediaFiles = explode(',', $comment->files);
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

                $previewUrl = $isPreviewable ? discussionCommentDirUrl(trim($fileKey), 't') : null;
                $downloadUrl = url('download-media-file?dir='.discussionCommentDir().'&file_name='.$fileKey);
                
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
                        $file_data = discussionCommentDirUrl(trim($fileKey), 't');
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

            $pageTitle = "Comment Media Preview";
            $viewData['pageTitle'] = $pageTitle;
            $viewData['fileKey'] = $selectedFileData['fileKey'] ?? '';
            $viewData['filename'] = $selectedFileData['filename'] ?? '';
            $viewData['extension'] = $selectedFileData['extension'] ?? '';
            $viewData['previewUrl'] = $selectedFileData['previewUrl'] ?? '';
            $viewData['fileType'] = $selectedFileData['fileType'] ?? '';
            $viewData['files_arr'] = json_encode($files_arr);
            $viewData['current_file_index'] = $current_file_index;
            
            $view = view('admin-panel.05-discussion-boards.manage-discussion-thread.discussion-media-preview', $viewData);
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

     public function addFavourite($id)
    {
        $discussionBoard = DiscussionBoard::where('unique_id', $id)->first();
        $message = "";
        if ($discussionBoard->is_favourite == 0) {
            $discussionBoard->is_favourite = 1;
            $message = "Post marked as a favourite";
        } else {
            $discussionBoard->is_favourite = 0;
            $message = "Post unmarked as a favourite";
        }
        $discussionBoard->save();

        return redirect()->back()->with("success", $message);
    }
}
