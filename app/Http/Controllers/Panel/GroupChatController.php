<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\GroupChatService;
use App\Models\DraftChatMessage;
use App\Models\GroupMessageReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\GroupSettings;

use App\Models\User;
use App\Models\ChatGroup;
use App\Models\GroupMembers;
use App\Models\GroupMessages;
use App\Models\ChatRequest;
use App\Models\GroupMessagesRead;
use App\Models\Chat;
use Carbon\Carbon;
use App\Models\GroupMessagePermission;

use App\Models\ChatNotification;
use Auth;
use App\Models\GroupJoinRequest;
use DB;

class GroupChatController extends Controller
{
    protected $groupChatService;

    public function __construct(GroupChatService $groupChatService)
    {
        $this->groupChatService = $groupChatService;
    }

    /**
     * Display the list of settings.
     *
     * @return \Illuminate\View\View
     */

    public function addNewGroup()
    {
        $user_id = auth()->user()->id;
        $pageTitle = "Add New Group";
        $viewData['pageTitle'] = $pageTitle;

        $result = $this->groupChatService->getAvailableMembers($user_id);
        
        if ($result['success']) {
            $viewData['members'] = $result['members'];
        } else {
            $viewData['members'] = collect();
        }

        $view = View::make('admin-panel.01-message-system.group-chat.add-new-group', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function addNewMembers($group_id)
    {
        $user_id = auth()->user()->id;
        $pageTitle = "Add New Member";
        $viewData['pageTitle'] = $pageTitle;
        $viewData['group_id'] = $group_id;

        $result = $this->groupChatService->getGroupMembersForAdding($group_id, $user_id);
        
        if ($result['success']) {
            $viewData['members'] = $result['members'];
            $viewData['group_members'] = $result['group_members'];
        } else {
            $viewData['members'] = collect();
            $viewData['group_members'] = [];
        }

        $view = View::make('admin-panel.01-message-system.group-chat.add-new-members', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }
    public function viewGroupMembers($group_id)
    {
        $user_id = auth()->user()->id;
        $pageTitle = "Group Members";
        $viewData['pageTitle'] = $pageTitle;
        $viewData['group_id'] = $group_id;

        $result = $this->groupChatService->getGroupMembersForViewing($group_id, $user_id);
        
        if ($result['success']) {
            $viewData['group_members'] = $result['group_members'];
            $viewData['member'] = $result['member'];
            $viewData['currentGroupMember'] = $result['currentGroupMember'];
        } else {
            $viewData['group_members'] = collect();
            $viewData['member'] = null;
            $viewData['currentGroupMember'] = null;
        }

        $view = View::make('admin-panel.01-message-system.group-chat.view-group-members', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function searchGroupMembers(Request $request)
    {
        $groupId = $request->input('group_id');
        $search = $request->input('search') ? str_replace("@", "", $request->input('search')) : '';
        $user_id = auth()->user()->id;

        $result = $this->groupChatService->searchGroupMembers($groupId, $search, $user_id);
        
        if ($result['success']) {
        $response['status'] = true;
            $response['members'] = $result['members'];
        } else {
            $response['status'] = false;
            $response['members'] = [];
        }

        return response()->json($response);
    }


    public function myGroupsList()
    {
        $user_id = auth()->user()->id;
        
        $result = $this->groupChatService->getUserGroupsList($user_id);
        
        if ($result['success']) {
            $viewData['groupdata'] = $result['groupdata'];
        } else {
            $viewData['groupdata'] = collect();
        }

        $view = View::make('admin-panel.01-message-system.group-chat.group_sidebar_ajax', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function addGroup(Request $request)
    {
        $pageTitle="Create Group";
        $members=ChatRequest::with('receiver')->get()->pluck('receiver');
        return view('admin-panel.01-message-system.group-chat.add_group')->with(['members' => $members,'pageTitle'=>$pageTitle]);
    }



    public function makeGroupAdmin($member_id,Request $request)
    {

           $fetch_member=GroupMembers::where(['unique_id'=>$member_id])->first();
           $fetch_member->is_admin=1;
           $fetch_member->save();

        return redirect()->back()->with("success", "Marked as Group Admin Successfully");
        // return response()->json($response);


    }

    public function removeGroupAdmin($member_id,Request $request)
    {

           $fetch_member=GroupMembers::where(['unique_id'=>$member_id])->first();
           $fetch_member->is_admin=0;
           $fetch_member->save();

        return redirect()->back()->with("success", "Remove from group admin successfully");


    }

    public function saveMemberToGroup($group_id,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['error_type'] = 'validation';
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        $newMemberId=0;
        $group_chat = ChatGroup::where("unique_id",$group_id)->first();
        foreach ($request->input('member_id') as $member) {
            $fetch_members=GroupMembers::withTrashed()->where(['group_id'=>$group_chat->id,'user_id'=>$member])->first();
            if( $fetch_members!=NULL && $fetch_members->deleted_at!=NULL){
                $restoremember =  GroupMembers::withTrashed()->find($fetch_members->id);
                $restoremember->restore();
                $restoremember->update([
                        'is_admin' => '0',
                ]);
                $newMemberId=$restoremember->user_id;
            }
            elseif($fetch_members==NULL){
                $groupuser = new GroupMembers();
                $groupuser->added_by=auth()->user()->id;
                $groupuser->group_id = $group_chat->id;
                $groupuser->user_id = $member;
                $groupuser->save();
                $newMemberId=$member;
            }
        
            if($newMemberId>0){
                $newMemberName=User::where('id',$newMemberId)->first()->first_name;
            }else{
                $newMemberName="New Member";
            }
                $group_message = new GroupMessages;
                $group_message->group_id=$group_chat->id;
                $group_message->message='*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has added *'.$newMemberName.'* to this Group.';
                $group_message->user_id=0;
                $group_message->save();
                $fetchAllMembers=GroupMembers::where(['group_id'=>$group_chat->id])->get();
                foreach ($fetchAllMembers as $member) {
                    if($member->user_id!=auth()->user()->id){
                    $arr = [
                        'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has added *' . $newMemberName . '* to ' . $group_chat->name . '  Group.',
                        'type' => 'group_chat',
                        'redirect_link' => 'group/chat/' . $group_id,
                        'is_read' => 0,
                        'user_id' => $member->user_id ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];
                    chatNotification($arr);
                }
            }
       
            }
     
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('group/chat/');
        $response['message'] = "Record Added Successfully";

        return response()->json($response);


    }


    public function joinGroupWithLink($group_encoded_id, Request $request)
    {

        $userId = auth()->user()->id;
        $group_chat = ChatGroup::where("hash_uid", $group_encoded_id)->first();
        if ($group_chat) {
            $userId2 = $group_chat->added_by;
            $canJoinGroup = true;
            $getChatBetweenUsers = chatExists($userId, $userId2);
            if ($getChatBetweenUsers) {
                $isChatBlocked = $getChatBetweenUsers->blocked_chat;
                $isBlockedBy = $getChatBetweenUsers->blocked_by;
                if ($isBlockedBy == $userId2) {
                    $canJoinGroup = false;
                } else {
                    $canJoinGroup = true;
                }
            }
  
            if ($canJoinGroup) {
                $check_group_member = GroupMembers::where('group_id', $group_chat->id)->where('user_id', $userId)->count();
                if ($check_group_member == 0) {
                    $groupuser = new GroupMembers();
                    $groupuser->group_id = $group_chat->id;
                    $groupuser->user_id = $userId;
                    $groupuser->save();

                    $group_message = new GroupMessages;
                    $group_message->group_id = $group_chat->id;
                    $group_message->message = '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has joined via invite link.';
                    $group_message->user_id = 0;
                    $group_message->save();

                    $socket_data = [
                        "action" => "new_member_joined",
                        'last_message_id' => $group_message->id,
                        "group_id" => $group_chat->id,
                    ];
                    initGroupChatSocket($group_chat->id, $socket_data);
                    $redirect_back = 'group/chat/' . $group_chat->unqiue_id;
                    $arr = [
                        'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '*  has joined via invite link  ' . $group_chat->name . '  Group.',
                        'type' => 'group_chat',
                        'redirect_link' => $redirect_back,
                        'is_read' => 0,
                        'user_id' => $userId2 ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];
                    chatNotification($arr);

                    $msg_type = "success";
                    $message = "Joined Group Successfully";

                } else {
                    $msg_type = "success";
                    $message = "Already Joined Group";
                }
            } else {
                $msg_type = "error";
                $message = "You Can't access this group";
            }


        } else {
            $msg_type = "error";
            $message = "Group Not Found";
            $redirect_back = baseUrl('group/chat/');
        }

        return redirect($redirect_back)->with($msg_type, $message);

    }

    public function createGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'group_type' => 'required',
            'description' => 'required|max:300',
            'banner_image' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=500',
        ], [
            'banner_image.image' => 'The file must be an image.',
            'banner_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'banner_image.dimensions' => 'The image must have a minimum width of 500 pixels.',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['error_type'] = 'validation';
            $response['message'] = $errMsg;
            return response()->json($response);
        }
       
        // Handle file uploads
        $groupData = $request->all();
        $groupData['added_by'] = auth()->user()->id;

        if ($file = $request->file('group_image')) {
            $fileName = $file->getClientOriginalName();
                $newName = mt_rand(1, 99999) . "-" . $fileName;
                $uploadPath = groupChatDir();
                $sourcePath = $file->getPathName();
            $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
            
            if (($api_response['status'] ?? '') === 'success') {
                $groupData['group_image'] = $newName;
            }
        }
        
        if ($file = $request->file('banner_image')) {
            $fileName = $file->getClientOriginalName();
                $newName = mt_rand(1, 99999) . "-" . $fileName;
                $uploadPath = groupChatDir();
                $sourcePath = $file->getPathName();
            $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
            
            if (($api_response['status'] ?? '') === 'success') {
                $groupData['banner_image'] = $newName;
            }
        }

        $result = $this->groupChatService->createGroup($groupData, auth()->user()->id);

        if ($result['success']) {
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('group/chat/');
            $response['message'] = $result['message'];
        } else {
            $response['status'] = false;
            $response['message'] = $result['message'];
        }

        return response()->json($response);
    }
    
 
    public function index(Request $request)
    {
        try {
            $user_id=auth()->user()->id;
            $viewData['group'] = $group = ChatGroup::with('groupMembers')->whereHas('groupMembers', function ($query)use ($user_id) {
                    $query->where('user_id', $user_id);

                })
                ->orderBy('id', 'desc')
                ->first();
            $viewData['type'] = 'group';
            $viewData['members'] = ChatRequest::with('sender')->where('receiver_id',$user_id)->where('is_accepted',1)->get()->pluck('sender');
         
                $groupdata = ChatGroup::whereHas("groupMembers",function($query) use($user_id){
                    $query->where("user_id",$user_id);
                })
                ->addSelect(['last_message_date' => GroupMessages::query()
                    ->select('created_at')
                    ->whereColumn('group_messages.group_id', 'chat_groups.id')
                    ->latest('created_at')
                    ->limit(1)
                ])
                ->orderBy('last_message_date','desc')
                ->get();

                if(isset($groupdata[0]->unique_id)){
                    return redirect(baseUrl('group/chat/'.$groupdata[0]->unique_id));
                }
                
                $viewData['groupdata'] = $groupdata;
                $viewData['chat_messages'] =NULL;
                $viewData['group_members']=NULL;
                $viewData['group_id'] =$group_id=NULL;
                $viewData['currentGroupMember'] =NULL;
             
            if($group){
                $currentGroupMember = $group->groupMembers->firstWhere('user_id', auth()->user()->id);
                $viewData['currentGroupMember'] = $currentGroupMember;
                $chat_messages = GroupMessages::withTrashed()->where('group_id', '=', $group->id)
                ->where(function($query) use($user_id){
                    $query->whereNull('clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                    }) 
                ->with('replyTo')->get();
                $viewData['chat_messages'] = $chat_messages;
                if(count($chat_messages) > 0){
                    $viewData['chat_empty'] = true;
                }else{
                    $viewData['chat_empty'] = false;
                }
                $viewData['group_members'] = GroupMembers::with('member')->where('group_id', '=', $group->id)->get();
                $viewData['group_id'] =$group_id=$group->id;
                $chat_members =  GroupMembers::with(['member' => function($query){
                    $query->select('id','first_name','last_name');
                }])->where('user_id',"!=",auth()->user()->id)->where('group_id', '=', $group->id)->get();
                $members = array();
                foreach($chat_members as $member){
                    $members[] = $member->member->first_name." ".$member->member->last_name;
                }
                $viewData['chat_members'] = json_encode($members);

               
            }
             $viewData['chat_requests'] = ChatRequest::orderBy('id', "desc")
                                        ->where(function($query) use($group_id) {
                                           $query->where('receiver_id',auth()->user()->id)->where('is_accepted','!=','1');
                                        })->with(['sender','receiver'])
                                        ->get();
                                        $viewData['chat_notifications'] =  ChatNotification::where('user_id', auth()->user()->id)
                                        ->where('is_read', '=', 0)
                                        ->orderBy('id', 'desc')
                                        ->with(['sender','receiver'])
                                        ->get();
           userActiveStatus();
        
            return view('admin-panel.01-message-system.group-chat.chat.chat',$viewData);
            
        } catch (\Exception $e) {
            // Handle any errors that occur during the process.
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
    }
    
    public function deleteChatMessage($msg_id,Request $request)
    {
        $userId = auth()->user()->id;
        $getChat = GroupMessages::findOrFail($msg_id);
        $socket_data = [
                "action" => "delete_msg_for_me",
                "message_id" => $msg_id,
                "messageUniqueId" => $getChat->unique_id,
                "group_id" => $getChat->group_id,
                "sender_id" => $userId
            ];
        initGroupChatSocket($getChat->group_id, $socket_data);

        $responseMessage = $getChat->markAsDeletedForUser($userId);
        return response()->json($responseMessage);
    
    }
    public function deleteChatMessageforAll($msg_id,Request $request)
    {
            $grp_msg = GroupMessages::where('id',$msg_id)->first();
            $uploadPath = groupChatDir();
            $file=$grp_msg->attachment;
            if($file){
                mediaDeleteApi($uploadPath,$file);
            }
            $groupId=$grp_msg->group_id;
            $socket_data = [
                "action" => "deleted_msg_for_everyone",
                "messageUniqueId" => $grp_msg->unique_id,
                "group_id" => $groupId,
            ];
            $reactionFetch=GroupMessageReaction::where('message_id',$grp_msg->id)->first();
                if($reactionFetch){
                 $reactionFetch->delete();
                }
            initGroupChatSocket($groupId, $socket_data);
            $group_members = GroupMembers::where("group_id",$groupId)->where("user_id","!=",auth()->user()->id)->get();
            foreach($group_members as $gm){
                $socket_data = [
                    "action" => "deleted_msg_for_everyone",
                    "group_id" => $groupId,
                    "receiver_id"=>$gm->user_id,
                    "deleted_by" => auth()->user()->id
                ];
                initUserSocket($gm->user_id, $socket_data);
            }
            $grp_msg_read = GroupMessagesRead::where('group_message_id',$msg_id)->delete();
            $grp_msg->delete();
            return response()->json('Message Deleted Successfully');

    
    }


    public function groupChat($conversation_id,Request $request)
    {
        try {
            $user_id = auth()->user()->id;
            $isMember = ChatGroup::where("unique_id", $conversation_id) // Check for the specific group ID
                    ->whereHas("groupMembers", function ($query) use ($user_id) {
                        $query->where("user_id", $user_id);
                    })
                    ->exists(); // Returns true if a record exists

        if ($isMember) {
            
            $viewData = $this->chatData($conversation_id);
                



            return view('admin-panel.01-message-system.group-chat.chat.chat',$viewData);
            
            // User is a member of the group
        } else {
            return redirect()->back()->with("error", "you don't have access to this group");

            // User is NOT a member of the group
        }

        } catch (\Exception $e) {
           // dd($e->getMessage());
            // Handle any errors that occur during the process.
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
    }

    public function groupChatAjax($conversation_id,Request $request){
        $viewData = $this->chatData($conversation_id);
        $grpChat=ChatGroup::where('unique_id',$conversation_id)->first();
      


        $view = view("admin-panel.01-message-system.group-chat.chat.message-container",$viewData);
        $contents = $view->render();
        
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function chatData($conversation_id)
    {
        $user_id = auth()->user()->id;
        $group = ChatGroup::with('groupMembers')->whereHas('groupMembers', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
        ->where("unique_id", $conversation_id)
        ->orderBy('id', 'desc')
        ->first();
       
        $viewData['group'] = $group;
        
        $draft_message = DraftChatMessage::where("user_id",auth()->user()->id)->where("reference_id",$group->id)->where("type","group_chat")->first();
       // dd($draft_message);
        $viewData['draft_message'] = $draft_message->message??'';
        $viewData['type'] = 'group';
        $viewData['members'] = ChatRequest::with('sender')->where('receiver_id', $user_id)->where('is_accepted', 1)->get()->pluck('sender');
        
        $groupdata = ChatGroup::whereHas("groupMembers", function ($query) use ($user_id) {
            $query->where("user_id", $user_id);
        })
            ->addSelect([
                'last_message_date' => GroupMessages::query()
                    ->select('created_at')
                    ->whereColumn('group_messages.group_id', 'chat_groups.id')
                    ->latest('created_at')
                    ->where(function ($query) use ($user_id) {
                        $query->whereNull('group_messages.clear_for');
                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                    })
                    ->limit(1)
            ])
            ->orderBy('last_message_date', 'desc')
            ->get();

        $viewData['groupdata'] = $groupdata;
        $viewData['chat_messages'] = NULL;
        $viewData['group_members'] = NULL;
        $viewData['group_id'] = $group_id = NULL;
        $viewData['chat_members'] = NULL;
        $viewData['currentGroupMember'] = NULL;
        $viewData['group_requests'] = Null;
        if ($group) {
            $currentGroupMember = $group->groupMembers->firstWhere('user_id', auth()->user()->id);
            $viewData['currentGroupMember'] = $currentGroupMember;
            $viewData['group_members'] = GroupMembers::with('member')->where('group_id', '=', $group->id)->get();

            $chat_messages = GroupMessages::withTrashed()->where('group_id', '=', $group->id)
                ->where(function ($query) use ($user_id) {
                    $query->whereNull('clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                })
                ->with('replyTo')
                ->get();
            if(count($chat_messages) > 0){
                $viewData['chat_empty'] = false;
            }else{
                $viewData['chat_empty'] = true;
            }
            $viewData['chat_messages'] = $chat_messages;
            $viewData['group_id'] = $group_id = $group->id;
            $chat_members = GroupMembers::with([
                'member' => function ($query) {
                    $query->select('id', 'first_name', 'last_name');
                }
            ])->where('user_id', "!=", auth()->user()->id)->where('group_id', '=', $group->id)->get();
            $members = array();
            foreach ($chat_members as $member) {
                $members[] = ($member->member ? ($member->member->first_name ?? '') . ' ' . ($member->member->last_name ?? '') : '');
            }
            $viewData['chat_members'] = json_encode($members);
            $viewData['group_requests'] = GroupJoinRequest::with('requester')
            ->where('group_id', $group->id)
            ->where('status', 0) 
            ->get();
        }
        $viewData['chat_requests'] = ChatRequest::orderBy('id', "desc")
            ->where(function ($query) use ($group_id) {
                $query->where('receiver_id', auth()->user()->id)->where('is_accepted', '!=', '1');
            })->with(['sender', 'receiver'])
            ->get();

            $viewData['chat_notifications'] =  ChatNotification::where('user_id',auth()->user()->id)
            ->where('is_read', '=', 0)
            ->orderBy('id', 'desc')
            ->with(['sender','receiver'])
            ->get();
        return $viewData;
    }
    public function sendMessage($groupId,Request $request)
    {
     
        try {
            $validator = Validator::make($request->all(), [
                'send_msg' =>['sometimes', 'input_sanitize'],
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

            if($request->send_msg!=NULL){
                $message=$request->send_msg;
            }else{
                $message=$request->message;
            }

           // dd($request);
            $newName = "";
            $group_chat = ChatGroup::where("id",$groupId)->first();
            if($request->attachment!=NULL || ($message!=NULL && $message!="")){
                $attachedFile = [];
                
                $viewData['group_id'] =$groupId;
                $chat_message = new GroupMessages;
                if ($request->hasFile('attachment')) {
                 
                    foreach ($request->file('attachment') as $file) 
                    {
                        $allowedTypes = [
                                'jpeg', 'jpg', 'png', 'gif', 'bmp', 'webp', 'svg', // Image
                                'xls', 'xlsx','çsv', // Excel
                                'pdf',         // PDF
                                'txt',         // Plain text
                                'mp3',         // Audio
                                'mp4', 'mpeg'  // Video
                            ];

                        // $file = $request->file('attachment');
                        $fileResponse=validateFileType($file,$allowedTypes);
                        if(!$fileResponse['status']){
                            return response()->json($fileResponse);
                        }
                        $fileName = $file->getClientOriginalName();
                        $newName = mt_rand(1, 99999) . "-" . $fileName;
                        $uploadPath = groupChatDir();
                        $sourcePath = $file->getPathName();
                        
                        $api_response = mediaUploadApi("upload-file",$sourcePath,$uploadPath,$newName);
                    
                        if(($api_response['status']??'') === 'success'){
                            $attachedFile[] = $newName;
                            // $chat_message->attachment = $newName;
                            $response['status'] = true;
                            $socket_data = [
                                "action" => "new_file_uploaded",
                                "file" =>$newName,
                            ];
                            initGroupChatSocket($groupId, $socket_data);

                            // $response['filename'] = $newName;
                            // $response['message'] = "Record added successfully";
                        }
                    }
                }

                // Create message data for service
                $messageData = [
                    'message' => $message,
                    'group_id' => $groupId,
                    'reply_to' => $request->reply_to,
                    'user_id' => auth()->user()->id,
                    'attachments' => $attachedFile,
                ];

                $result = $this->groupChatService->sendGroupMessage($groupId, $messageData, auth()->user()->id);

                if ($result['success']) {
                    $chat_message = $result['data'];
                } else {
                    $response['status'] = false;
                    $response['message'] = $result['message'];
                    return response()->json($response);
                }

                $group_members = GroupMembers::where("group_id",$groupId)->where("user_id","!=",auth()->user()->id)->get();
                foreach($group_members as $gm){
                    $group_message_read= new GroupMessagesRead();
                    $group_message_read->group_id=$groupId;
                    $group_message_read->user_id = $gm->user_id;
                    $group_message_read->group_message_id = $chat_message->id;
                    $group_message_read->save();

                    updateMessagingBox(auth()->user()->id,$gm->user_id);
                }
                $userId = auth()->user()->id;
                $get_chat_message=GroupMessages::withTrashed()->where('id',$chat_message->id)
                                                            ->where(function($query) use($userId){
                                                            $query->whereNull('clear_for');
                                                            $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                                                            }) 
                                                            ->with('replyTo')->first();
                if($get_chat_message->message!=NULL || $get_chat_message->attachment!=NULL){
                    $viewData['chat_msg']=$get_chat_message;
                }else{
                    $viewData['chat_msg']=NULL;
                }
                $group_chat_first_msg = GroupMessages::where("id",$request->reply_to)->first();
                $viewData['openfrom'] = $request->openfrom;
                 $view = View::make('admin-panel.01-message-system.group-chat.chat.msg_sent_block', $viewData);
                 $contents = $view->render();
                // dd($contents);
                if($request->reply_to!=NULL){
                    if ($group_chat_first_msg->user_id != auth()->user()->id) {
                        $arr = [
                            'comment' =>'*'. auth()->user()->first_name . " " . auth()->user()->last_name . '* has replied to your message ' . $group_chat->name . ' Group.',
                            'type' => 'group_chat',
                            'redirect_link' => 'group/chat/'.$group_chat->unique_id,
                            'is_read' => 0,
                            'user_id' =>$group_chat_first_msg->user_id ?? '',
                            'send_by' => auth()->user()->id ?? '',
                        ];
                        chatNotification($arr);
                    }
                }

                
                if (strpos($request->send_msg, '@') !== false)  {
                    $parts = explode('*', $request->send_msg);
                    $name = trim(str_replace('@', '', $parts[1]));
               
                    $names = explode(' ', $name, 2); 

                    $receiver = User::where('first_name', $names[0] ?? '')
                                ->where('last_name', $names[1] ?? '')
                                ->latest()
                                ->first();
                 
                    $arr = [
                        'comment' => '*'.auth()->user()->first_name . " " . auth()->user()->last_name . '* has mentioned you on ' . $group_chat->name . ' Group.',
                        'type' => 'group_chat',
                        'redirect_link' => 'group/chat/'.$group_chat->unique_id,
                        'is_read' => 0,
                        'user_id' => $receiver->id ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];
                    chatNotification($arr);
                }

             $senderId = auth()->user()->id;
                $group = ChatGroup::findOrFail($groupId);

                // Step 1: Get settings
                $grpSettings = GroupSettings::where('group_id', $groupId)->first();
                $group_chat = $group; // for message title use

                if ($grpSettings){ // Fail-safe if no settings exist
                    $type=$grpSettings->who_can_see_my_message;
                }else{
                     $type="none";
                }
                // Step 2: Determine recipients
                $receiverIds = [];

                switch ($type) {
                    case 'members':
                        $receiverIds = GroupMessagePermission::where('group_id', $groupId)
                            ->where('member_id', '!=', $senderId)
                            ->pluck('member_id')
                            ->toArray();
                        break;

                    case 'admins':
                        $receiverIds = GroupMembers::where('group_id', $groupId)
                            ->where('is_admin', true)
                            ->where('user_id', '!=', $senderId)
                            ->pluck('user_id')
                            ->toArray();
                        break;

                    case 'everyone':
                    default:
                        $receiverIds = GroupMembers::where('group_id', $groupId)
                            ->where('user_id', '!=', $senderId)
                            ->pluck('user_id')
                            ->toArray();
                        break;
                }
                

                // Step 3: Prepare socket payload and notify each user
                foreach ($receiverIds as $memberId) {
                    $socket_data = [
                        "action" => "new_message",
                        "message" => $message,
                        "group_id" => $groupId,
                        "last_message_id" => $chat_message->id,
                        "sender_id" => $senderId,
                        "receiver_id" => $memberId,
                    ];
                    initGroupChatSocket($groupId, $socket_data);

                    // Optional: notify via user-specific channel
                    $notification_data = [
                        "action" => "new_group_message",
                        "message" => "Message received in group {$group_chat->name} from " . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        "group_id" => $groupId,
                        "sender_id" => $senderId,
                        "total_unread_count" => unreadTotalGroupMessages($memberId) + unreadTotalChatMessages($memberId),
                        "unread_count" => unreadTotalGroupMessages($memberId),
                        "receiver_id" => $memberId,
                    ];
                    initUserSocket($memberId, $notification_data);
                }

                
                $response['status'] = true;
                $response['contents'] = $contents;
                $response['id'] = $chat_message->id;
                return response()->json($response);
            }
        } catch (\Exception $e) {
             return response()->json($e->getMessage());
        }
    }
   
    public function getConversation($chat_unique_id,Request $request)
    {

            $user_id=auth()->user()->id;
            $viewData['group']=$group= ChatGroup::where('unique_id', $chat_unique_id)->first();
            $viewData['type'] = 'group';
            $viewData['groupMembers'] = DB::table('group_members')
                    ->join('users', 'group_members.user_id', '=', 'users.id')
                    ->select('group_members.user_id', 'users.id', 'users.first_name', 'users.last_name','users.profile_image')
                    ->where('group_members.group_id', '=', $group->id)
                    ->where('group_members.deleted_at', NULL)
                    ->get();
            $viewData['groupdata'] = ChatGroup::with(['members', 'lastMessage'])
                                                ->whereHas('members', function ($query) use ($user_id) {
                                                    $query->where('user_id', $user_id);
                                                })->get();


            $viewData['chat_messages'] = GroupMessages::withTrashed()->where('group_id', '=', $group->id)
                                                            ->where(function($query) use($user_id){
                                                                $query->whereNull('clear_for');
                                                                $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                                                            }) ->get();
            $viewData['group_id'] =$group->id;

            $view = View::make('admin-panel.01-message-system.group-chat.chat.get-conversation', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            return response()->json($response);
    }


    /**
     * Remove the specified setting from the database.
     *
     * @param string $id The unique id of the setting.
     * @return \Illuminate\Http\RedirectResponse
     */

    public function groupSearch(Request $request)
    {
        $search=$request->search;
        if ($request->ajax()) { 
            $user_id=auth()->user()->id;  
            $viewData['groupdata'] = ChatGroup::with([
                                        'members' => function($qq) use ($user_id) {
                                            $qq->where('user_id', $user_id);
                                        },
                                        'lastMessage'
                                    ])
                                    
                                    ->when($search, function ($query) use ($search,$user_id) {
                                        $query->whereHas('members', function ($query2) use ($user_id) {
                                            $query2->where('user_id', $user_id); // Filter groups where the current user is a member
                                        });
                                        $query->where(function($query3) use($search){
                                            $query3->where('name', 'LIKE', "%{$search}%") // Search group name
                                            ->orWhereHas('groupMessages', function ($messageQuery) use ($search) {
                                                $messageQuery->where('message', 'LIKE', "%{$search}%"); // Search group messages
                                            });
                                        });
                                        
                                    })
                                    ->get();
            // For each group, check permissions and attach settings
       
            $view = View::make('admin-panel.01-message-system.group-chat.chat.chat_sidebar_ajax', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
        }else{
            $response['status'] = false;
            return response()->json($response);
        }
    }

    public function getGroupsList(Request $request)
{
   $user_id = auth()->user()->id;
    $search = $request->search;
    $perPage = 15;
    $page = $request->input('page', 1);

    // Fetch paginated group data
    $groupQuery = ChatGroup::whereHas("groupMembers", function ($query) use ($user_id) {
            $query->where("user_id", $user_id);
        })
        ->when($search, function ($query) use ($search) {
            $query->where("name", "LIKE", "%" . $search . "%");
        })
        ->withExists('groupRequest')
        ->addSelect([
            'last_message_date' => GroupMessages::query()
                ->select('created_at')
                ->whereColumn('group_messages.group_id', 'chat_groups.id')
                ->latest('created_at')
                ->where(function ($query) use ($user_id) {
                    $query->whereNull('group_messages.clear_for')
                        ->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                })
                ->limit(1)
        ])
        ->orderBy('last_message_date', 'desc');

     $groupdata = $groupQuery->paginate($perPage, ['*'], 'page', $page);

    // For each group, check permissions and attach settings
    
            // Prepare response data
            $viewData['groupdata'] = $groupdata;
            $view = View::make('admin-panel.01-message-system.group-chat.chat.chat_sidebar_ajax', $viewData);
            $contents = $view->render();

            $response['contents'] = $contents;
            $response['count'] = $groupdata->total(); // Total number of records
            $response['current_page'] = $groupdata->currentPage(); // Current page number
            $response['has_more_pages'] = $groupdata->hasMorePages(); // Check if more pages are available

    return response()->json($response);
}

    public function getReactedMsg($group_id,$message_uid,Request $request)
    {
            $userId=auth()->user()->id;
            $chat_messages = GroupMessages::withTrashed()->with('sentBy')
                        ->where('group_id',$group_id)
                        ->where('unique_id',$message_uid)
                        ->where(function($query) use($userId){
                             $query->whereNull('clear_for');
                             $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                        })
                        ->first();
                $chat_messages = $chat_messages ? collect([$chat_messages]) : collect([]);
                $viewData['chat_messages'] = $chat_messages;
                $viewData['group_id'] = $group_id;
                $viewData['msg_type'] = 'reaction_msg';
               
                $view = View::make('admin-panel.01-message-system.group-chat.chat.chat_ajax', $viewData);
                $contents = $view->render();
                $response['msg_type'] = 'reaction_msg';
                $response['contents'] = $contents;
                $response['messageUniqueId'] = $message_uid;
                return response()->json($response);
      
    }

    public function getGroupInformation($group_id){
        $group = ChatGroup::where("unique_id",$group_id)->first();
        $viewData['group'] = $group;
        $view = view("admin-panel.01-message-system.group-chat.chat.group-information",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);

    }
    public function getGroupChat($group_id,$last_msg_id,Request $request)
    {
        try {
            $viewData['last_msg_id'] =$last_msg_id;
            $userId=auth()->user()->id;
            $chat_messages = GroupMessages::withTrashed()->with('sentBy')
                        ->where('group_id',$group_id)
                        ->where(function($query) use($last_msg_id){
                            if($last_msg_id != 0){
                                $query->where("id",">",$last_msg_id);
                            }
                        })
                        ->where(function($query) use($userId){
                             $query->whereNull('clear_for');
                             $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                        })  
                        ->limit(50)
                        ->latest()
                        ->get();
                         $current_first_msg_id=$request->first_msg_id;
                        if($last_msg_id==0){
                                $first_msg_id = $chat_messages[count($chat_messages) - 1]->id??0;
                        }else{
                                $first_msg_id=$current_first_msg_id;

                        }
            $chat_messages = $chat_messages->sortBy("id");
            $last_message = GroupMessages::withTrashed()->with('sentBy')
                            ->where(function($query) use($userId){
                                    $query->whereNull('clear_for');
                                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                            })
                        ->where('group_id',$group_id)
                        ->latest()
                        ->first();

            $response['first_msg_id'] = $first_msg_id;
            $response['last_msg_unique_id'] = $last_message?$last_message->unique_id:0;
            $response['last_msg_id'] = $last_message->id??0;
            $response['message_id'] = $last_message->unique_id??0;

        
            if($last_msg_id != ($last_message->id??0)){
                $viewData['chat_messages'] = $chat_messages;
                $viewData['group_id'] = $group_id;
              


                $viewData['openfrom'] = $request->openfrom;
                $viewData['msg_type'] = 'all_msg';
                $view = View::make('admin-panel.01-message-system.group-chat.chat.chat_ajax', $viewData);
                $contents = $view->render();
                $response['chat_messages'] = $chat_messages->count();
                $response['contents'] = $contents;
                $response['new_msg'] = true;
            }else{
                
                $response['new_msg'] = false;
                $view = View::make('admin-panel.01-message-system.message-centre.empty-chat', $viewData);
                $contents = $view->render();
                $response['contents'] = $contents;
            }
          
            // if($first_msg_id == 0 && $last_message->id??0 == 0){
            //     $view = View::make('admin-panel.01-message-system.group-chat.chat.empty-chat', $viewData);
            //     $contents = $view->render();
            //     $response['contents'] = $contents;
            // }
            $group_member_typing =  GroupMembers::where('group_id', $group_id)
                            ->where('user_id','!=', auth()->user()->id)
                            ->where('is_typing',1)
                            ->get();

            $is_typing = false;
            $members_typing = array();
            if(count($group_member_typing) > 0) {
                $is_typing = true;
                foreach($group_member_typing as $key => $mem){
                    if($key < 2){
                        $members_typing[] = substr($mem->member->first_name,0,3);
                    }
                }
                if(count($group_member_typing) > 2){
                    $members_typing[] = "+".(count($group_member_typing) - count($members_typing));
                }
            }
            GroupMessagesRead::where('group_id',$group_id)->where("user_id",auth()->user()->id)->update(['status'=>'read']);
          
            $socket_data = [
                "action" => "group_message_read",
                "group_id" => $group_id,
                "unread_count" => unreadTotalGroupMessages($userId),
                "total_unread" => unreadTotalChatMessages($userId) + unreadTotalGroupMessages($userId),
                "receiver_id" => auth()->user()->id,

            ];
            initGroupChatSocket($group_id, $socket_data);

            
            $group_members = GroupMembers::where("group_id",$group_id)->where("user_id","!=",auth()->user()->id)->get();
            foreach($group_members as $gm){
                updateMessagingBox(auth()->user()->id,$gm->user_id);
            }
            $response['member_typing'] = implode(",",$members_typing);
            $response['mt'] =count($members_typing);
            $response['is_typing'] = $is_typing;
         
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage()." LINE: ".$e->getLine();
            return response()->json($response);
        }
    }
    public function getOlderGroupChat($group_id,$first_msg_id,Request $request)
    {
        try {
          
            $userId = auth()->user()->id;
            $chat_messages = GroupMessages::withTrashed()->with('sentBy')
                        ->where('group_id',$group_id)
                        ->where(function($query) use($first_msg_id){
                            $query->where("id","<",$first_msg_id);
                         })  
                        ->where(function($query) use($userId){
                             $query->whereNull('clear_for');
                             $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                        })  
                        ->limit(50) 
                        ->latest()       
                        ->get();
            if(count($chat_messages)>0){
                $first_msg_id=$chat_messages[count($chat_messages) - 1]->id??0;
           
            }
           


            $chat_messages = $chat_messages->sortBy("id");
            $last_message = GroupMessages::withTrashed()->with('sentBy')
                            ->where(function($query) use($userId){
                                    $query->whereNull('clear_for');
                                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                            })
                        ->where('group_id',$group_id)
                        ->latest()
                        ->first();
            $response['first_msg_id'] = $first_msg_id;
            $response['last_msg_unique_id'] = $last_message?$last_message->unique_id:0;
            $response['last_msg_id'] = $last_message->id??0;
            $response['message_id'] = $last_message->unique_id??0;
            $viewData['chat_messages'] = $chat_messages;
            $viewData['msg_type'] = 'all_msg';
            $viewData['group_id'] = $group_id;
            $view = View::make('admin-panel.01-message-system.group-chat.chat.chat_ajax', $viewData);
            $contents = $view->render();
            $response['chat_messages'] = $chat_messages->count();
            $response['contents'] = $contents;
            $response['new_msg'] = true;
         
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage()." LINE: ".$e->getLine();
            return response()->json($response);
        }
    }
    public function updateGroupName($groupId,Request $request)
    {

    // Update the typing status in the database
        $chat = ChatGroup::where('id', $groupId)->first();
        $chat->name= $request->name;
        $chat->save();
        return response()->json(['success' => true]);
    }
    public function updateTypingStatus(Request $request)
    {
        $groupId = $request->input('group_id');

    // Update the typing status in the database
        $group_member = GroupMembers::where('group_id', $groupId)
        ->where('user_id', auth()->user()->id)->first();
        if(!empty($group_member)){
            $group_member->is_typing= $request->is_typing;
            $group_member->save();
        } 
        $group_member_typing =  GroupMembers::where('group_id', $groupId)
                            // ->where('user_id','!=', auth()->user()->id)
                            ->where('is_typing',1)
                            ->get();

            $is_typing = false;
            $members_typing = array();
            if(count($group_member_typing) > 0) {
                $is_typing = true;
                foreach($group_member_typing as $key => $mem){
                    if($key < 2){
                        $members_typing[] = substr($mem->member->first_name,0,3);
                    }
                }
                if(count($group_member_typing) > 2){
                    $members_typing[] = "+".(count($group_member_typing) - count($members_typing));
                }
            }

            $socket_data = [
                    "action" => "user_typing",
                    "member_typing" => implode(",",$members_typing),
                    "mt" => count($members_typing),
                    "group_id" => $groupId,
                    "isTyping" => $is_typing,
                    "sender_id" => auth()->user()->id,
                ];
        initGroupChatSocket($groupId, $socket_data);


        return response()->json(['success' => true]);
    }

    public function fetchTypingStatus($groupId,Request $request)
    {
        
        $typingUsers = GroupMembers::where('group_id', $groupId)
        ->where('is_typing', 1)
        ->where('user_id','!=', auth()->user()->id)
        ->with('member') // Load only the 'name' field from the users table
        ->get()
        ->pluck('member.first_name'); // Extract user names

        return response()->json(['typing_users' => $typingUsers]);


    }

    public function searchGroupMessages(Request $request)
    {
        $userId=auth()->user()->id;
        $search = $request->input('search');
        $groupId = $request->input('group_id');
        $startOfLastWeek = Carbon::now()->subWeek();
        $now = Carbon::now();
        $viewData['chat_messages'] = GroupMessages::where('group_id', $groupId)->with('sentBy')
                                    ->where('message', 'LIKE', "%{$search}%")
                                    ->where(function($query) use($userId){
                                        $query->whereNull('clear_for');
                                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                                    })  
                                    ->whereBetween('created_at', [$startOfLastWeek, $now])
                                    ->get();
        $viewData['group_id'] = $groupId;
        $viewData['openfrom'] = $request->openfrom;
       

        $viewData['msg_type'] = 'search_msg';
        $view = View::make('admin-panel.01-message-system.group-chat.chat.chat_ajax', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);


    }
public function currentUserGroupsList(Request $request){
        $viewData['currentUserGroupsList'] = currentUserGroupsList();
        
        $contents = view("components.connected-users",$viewData)->render();
        $response['status'] = true;
        $response['count'] = count($viewData['currentUserGroupsList']);

        $response['contents'] = $contents;

        return response()->json($response);
    }

     public function removeGroupMember($id,Request $request)
    {
        $data = GroupMembers::find($id);

        // Check if user is the last member in the group
        $totalMembers = GroupMembers::where('group_id', $data->group_id)->count();
        
        // Check if there are other admins in the group (excluding the current user)
        $otherAdmins = GroupMembers::where('group_id', $data->group_id)
            ->where('is_admin', 1)
            ->where('id', '!=', $data->id)
            ->count();
        
        if($data->is_admin == 0 || $totalMembers == 1 || $otherAdmins > 0){
            $group_id = $data->group_id;
            $groupMemberId = $data->user_id;
            $data->delete();
            $getMemberName=User::where('id', $groupMemberId)->first()->first_name;
            $group_message = new GroupMessages;
            $group_message->group_id=$group_id;
                if($groupMemberId==auth()->user()->id){
                    
                    $comment= $group_message->message='*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has left this Group.';
                    $groupMemberStatus="Left";
                }else{
                    $comment=$group_message->message='*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has removed *'.$getMemberName.'* from this Group.';
                    $groupMemberStatus="Removed";
                }
                $group_message->user_id=0;
                $group_message->save();
            $fetchAllMembers=GroupMembers::where(['group_id'=>$group_id])->get();
            
            $group =  ChatGroup::where("id",$group_id)->first();
            if(count($fetchAllMembers) == 0){
                ChatGroup::where("id",$group_id)->delete();
            }
            foreach ($fetchAllMembers as $member) {
            if($member->user_id!=auth()->user()->id){
               $arr = [
                'comment' => $comment,
                'type' =>'group_chat',
                'redirect_link' => 'group/chat/'.$group->unique_id,
                'is_read' => 0,
                'user_id' => $member->user_id ?? '',
                'send_by' => auth()->user()->id ?? '',
                ];
                 chatNotification($arr);
            }
            }
    
                 $socket_data = [
                        "action" => "group_member_removed",
                        "group_id" => $group->id,
                        "receiver_id"=>$groupMemberId,
                        "message"=>$comment,
                ];
                     
            initGroupChatSocket($group->id, $socket_data);
                
            $response['status'] = true;
            $response['removed'] = 'yes';
            $response['redirect_back'] = baseUrl('/group/chat');
            $response['message'] = "Removed Successfully";
    
            return response()->json($response);
        }else{
            $response['status'] = true;
            $response['removed'] = 'no';
            $response['redirect_back'] =  baseUrl('/group/mark-as-admin/'.$id);
            $response['message'] = "Removed Successfully";
    
            return response()->json($response);
          
        }
       
    }


    public function markAsAdminModal($id)
    {   
        $data = GroupMembers::find($id);

        $groupMembers =  GroupMembers::where('group_id',$data->group_id)->where('id','!=',$data->id)->get();
        $viewData['pageTitle'] = "Add admin";
        $viewData['groupMembers'] = $groupMembers;
        $viewData['currentGroupMember'] = $data;
        $view = view("admin-panel.01-message-system.group-chat.chat.remove-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function markAsAdmin($member_id,$current_member_id)
    {
        GroupMembers::where('unique_id',$member_id)->update(['is_admin'=> 1]);
        GroupMembers::where('unique_id',$current_member_id)->delete();

        $response['status'] = true;
        $response['removed'] = 'yes';
        $response['redirect_back'] = baseUrl('/group/chat');
        $response['message'] = "Mark as admin Successfully";

        return response()->json($response);
    }

    public function clearGroupChatForUser($group_id,Request $request)
    {
       
        $userId = auth()->user()->id;
        $selectedMessageIds = $request->input('clear_msg');        
        $messages = GroupMessages::withTrashed()->whereIn('id',$selectedMessageIds)->where('group_id', $group_id)->get();
        $msgIds=[];
        foreach ($messages as $message) {
          $msgIds[]=$message->unique_id;
          $deletedBy = !empty($message->clear_for) ? explode(',', $message->clear_for) : [];
            if (!in_array($userId, $deletedBy)) {
                $deletedBy[] = $userId; // Add the user ID to the `deleted_by` array
            }
            if(count($deletedBy)>0){
                $deletedBy=implode(',',$deletedBy);

            }
            GroupMessages::withTrashed()->where('id', $message->id)->update(['clear_for' => $deletedBy]);
        }

        $userId=auth()->user()->id;
        $message_count = GroupMessages::withTrashed()->with('sentBy')
                    ->where('group_id',$group_id)
                    ->where(function($query) use($userId){
                            $query->whereNull('clear_for');
                            $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                    })  
                    ->count();
        $response['msgIds'] = $msgIds;
        $response['message_count'] = $message_count;
        $response['message'] = 'Chat Cleared Successfully';
        return response()->json($response);
    }

    public function deleteGroup($group_id,Request $request){
        $group = ChatGroup::where("unique_id",$group_id)->first();
        $socket_grp_id=  $group->id;
         ChatGroup::deleteRecord($group_id);       
        $response['status'] = true;
        $socket_data = [
                    "action" => "group_deleted",
                    "group_id" => $socket_grp_id,

                ];
                 
        initGroupChatSocket($socket_grp_id, $socket_data);
       
        return redirect(baseUrl('/group/chat'))->with("success","Group deleted successfully");
    }

    public function addReactionToMessage(Request $request){
        $message_id = $request->message_id;
        $grp_message =  GroupMessages::where("unique_id",$message_id)->first();
        $reaction=GroupMessageReaction::updateOrCreate(['message_id'=>$grp_message->id,'added_by'=>auth()->user()->id],['reaction'=>$request->reaction]);
    
        $group =  ChatGroup::where("id",$grp_message->group_id)->first();
        $arr = [
            'comment' => '*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has reacted *'. $request->reaction . '* to your message from '.$group->name.'  Group.',
            'type' =>'group_chat',
            'redirect_link' => 'group/chat/'.$group->unique_id,
            'is_read' => 0,
            'user_id' =>$grp_message->user_id ?? '',
            'send_by' => auth()->user()->id ?? '',
        ];
        chatNotification($arr);

        groupMessageReaction($group->id,$message_id,'add');
        $response['status'] = true;
        return response()->json($response);
    }

    public function removeReactionToMessage(Request $request){
        $reactionUniqueId = $request->message_id;
        $reaction= GroupMessageReaction::where("unique_id",$reactionUniqueId)->where('added_by',auth()->user()->id)->first();
        $grpMsg= GroupMessages::where("id",$reaction->message_id)->first();

        groupMessageReaction($grpMsg->group_id,$grpMsg->unique_id,'remove');
        $reaction->delete();
        $response['status'] = true;
        return response()->json($response);
    }

    public function previewFile(Request $request){
        $viewData['file_name'] = $request->file_name;
        $viewData['fileUrl'] = groupChatDirUrl($request->file_name,'r');
        $viewData['chatMessage'] =  GroupMessages::where("unique_id",$request->chat_id)->first();
        $view = view("admin-panel.01-message-system.group-chat.chat.preview-file",$viewData);
        
        $response['status'] = true;
        $response['contents'] = $view->render();

        return response()->json($response);
    }

    public function updateGroupImage(Request $request, $groupId){
    $request->validate([
        'group_update_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    ]);
    $group = ChatGroup::where('id', $groupId)->first();
    if (!$group) {
        return response()->json(['status' => false, 'message' => 'Group not found.'], 404);
    }
        if ($file = $request->file('group_update_image')) {
            $fileName = $file->getClientOriginalName();
                $newName = mt_rand(1, 99999) . "-" . $fileName;
                $uploadPath = groupChatDir();
                $sourcePath = $file->getPathName();
                $api_response = mediaUploadApi("upload-file",$sourcePath,$uploadPath,$newName);
                if(($api_response['status']??'') === 'success'){
                    $group->group_image = $newName;
                    $group->save();
                    $response['status'] = true;
                    $response['message'] = "Record added successfully";
                }else{
                    $response['status'] = false;
                    $response['message'] = "Error while upload";
                }
        }
        return response()->json(['status' => true, 'message' => 'Group image updated successfully.']);
    }

    public function updateGroupMessage(Request $request, $group_id)
    {
        $chatMessage =  GroupMessages::where("unique_id",$group_id)->first();
        $chatMessage->message = $request->message;
        $chatMessage->edited_at = now();
        $chatMessage->save();
        $socket_data = [
                    "action" => "message_edited",
                    "messageUniqueId" => $chatMessage->unique_id,
                    "editedMessage" => $chatMessage->message,

                ];
        initGroupChatSocket($chatMessage->group_id, $socket_data);

        return response()->json(['status' => true, 'message' => 'Message updated successfully.']);
      
    }
     public function refreshGroupList(Request $request){
        $viewData['currentUserGroupsList'] = currentUserGroupsList();
        $contents = view("components.partials.group-user-list",$viewData)->render();
        $response['status'] = true;
        $response['count'] = count($viewData['currentUserGroupsList']);

        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function saveDraftMessage(Request $request){
        DraftChatMessage::updateOrCreate([
            'reference_id'=>$request->input("group_id"),
            'user_id'=>auth()->user()->id,
            'type'=>"group_chat"
        ],
        [
            'message'=> $request->input("message")
        ]);

        $response['status'] = true;

        return response()->json($response);
        
    }

    public function chatNotifications(Request $request){
        $records = ChatNotification::where('user_id',auth()->user()->id)
        ->orderBy('id', 'desc')
        ->whereIn('type', ['group_chat','invite_request'])
        ->with(['sender','receiver'])
        ->paginate(50);
       

        $viewData['chat_notifications'] = $records;
        $contents = view("admin-panel.01-message-system.group-chat.chat.chat-notifications",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        $response['record'] = $records->items();
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();

        return response()->json($response);
    }

    public function markAsRead(Request $request){
      
        $chatNotification = ChatNotification::where('unique_id',$request->notificationId)->first();
        $chatNotification->is_read = 1 ;
        $chatNotification->save();
    
        $count = chatNotificationsCount(auth()->user()->id);

        $response['status'] = true;
        $response['count'] = $count;
        $response['unique_id'] = $chatNotification->unique_id;
        $response['message'] = "Marked as Read Successfully";
        return response()->json($response);
   
    }

    public function getOtherGroupsList(Request $request)
{
    $user_id = auth()->user()->id; 
    $search = $request->search;
    $perPage = 15; // Number of groups per page
    $page = $request->input('page', 1); // Get the current page from the request, default to 1

    // Query to fetch groups where the user is not a member
    $groupQuery = ChatGroup::whereDoesntHave("groupMembers", function ($query) use ($user_id) {
        $query->where("user_id", $user_id);
    })
    ->withExists('groupRequest')
    ->where('type', 'Public')
    ->where(function ($query) use ($search) {
        if ($search != '') {
            $query->where("name", "LIKE", "%" . $search . "%");
        }
    })
    ->addSelect([
        'last_message_date' => GroupMessages::query()
            ->select('created_at')
            ->whereColumn('group_messages.group_id', 'chat_groups.id')
            ->latest('created_at')
            ->where(function ($query) use ($user_id) {
                $query->whereNull('group_messages.clear_for');
                $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
            })
            ->limit(1)
    ]);
    $groupQuery->orderBy('id', 'desc');
    $groupdata = $groupQuery->paginate($perPage, ['*'], 'page', $page);
    $viewData['show_empty'] = $request->fetchNewer;

    // Prepare response data
    $viewData['groupdata'] = $groupdata;
   
    $view = View::make('admin-panel.01-message-system.group-chat.chat.other_group_chat_sidebar_ajax', $viewData);
    $contents = $view->render();

    $response['contents'] = $contents;
    $response['count'] = $groupdata->total(); // Total number of records
    $response['current_page'] = $groupdata->currentPage(); // Current page number
    $response['has_more_pages'] = $groupdata->hasMorePages(); // Check if more pages are available
    return response()->json($response);
}

   public function fetchGroupChats(Request $request){

    $contents = view("admin-panel.01-message-system.group-chat.chat.group-chats")->render();
    $response['status'] = true;
     $response['contents'] = $contents;
    // $response['record'] = $records->items();
    // $response['last_page'] = $records->lastPage();
    // $response['current_page'] = $records->currentPage();
    // $response['total_records'] = $records->total();

    return response()->json($response);
}

public function sendJoinRequest(Request $request)
{
    $user_id=auth()->user()->id; 
    $group_id = $request->input('group_id');
    $group_chat = ChatGroup::where("id",$group_id)->first();
    // Check if the user already sent a request
    $existingRequest = GroupJoinRequest::where('group_id', $group_id)
        ->where('requested_by', $user_id)
        ->where('status', 0)
        ->first();

    if ($existingRequest) {
        return response()->json(['status' => false, 'message' => 'Request already sent']);
    }

    GroupJoinRequest::create([
        'group_id' => $group_id,
        'requested_by' => $user_id,
        'status' => 0, 
    ]);
    
    $arr = [
        'comment' =>'*'. auth()->user()->first_name." ".auth()->user()->last_name.'* has requested to Join *'.$group_chat->name.'* Group.',
        'type' =>'group_chat',
        'redirect_link' => 'group/chat/'.$group_chat->unique_id,

        'is_read' => 0,
        'user_id' =>$group_chat->added_by ?? '',
        'send_by' => auth()->user()->id ?? '',
        ];
chatNotification($arr);

    $response['status'] = true;
    $response['redirect_back'] = baseUrl('group/chat/');
    $response['message'] = "Join request sent successfully";
    return response()->json($response);

}


public function acceptGroupMemberRequest($id,Request $request)
{
    
    $groupRequest = GroupJoinRequest::where("unique_id",$id)->first();
     $group_chat = ChatGroup::where("id",$groupRequest->group_id)->first();
        $fetch_members=GroupMembers::withTrashed()->where(['group_id'=>$groupRequest->group_id,'user_id'=>$groupRequest->requested_by])->first();
        if( $fetch_members!=NULL && $fetch_members->deleted_at!=NULL){
            $restoremember =  GroupMembers::withTrashed()->find($fetch_members->id);
            $restoremember->restore();
            $restoremember->update([
                    'is_admin' => '0',
            ]);
        }
        elseif($fetch_members==NULL){
            $groupuser = new GroupMembers();
            $groupuser->added_by=auth()->user()->id;
            $groupuser->group_id = $groupRequest->group_id;
            $groupuser->user_id = $groupRequest->requested_by;
            $groupuser->save();
            $newMemberId= $groupRequest->requested_by;
        }
        $newMemberId= $groupRequest->requested_by;

        $newMember = User::find($newMemberId);
        $newMemberName = $newMemberId > 0 
            ? optional($newMember)->first_name . ' ' . optional($newMember)->last_name
            : "New Member";
      
            $group_message = new GroupMessages;
            $group_message->group_id=$groupRequest->group_id;
            $group_message->message='*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has added *'.$newMemberName .'* to this Group.';
            $group_message->user_id=0;
            $group_message->save();
         
            $arr = [
                    'comment' =>'*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has added *'.$newMemberName . '* to '.$group_chat->name.' Group.',
                    'type' =>'group_chat',
                    'redirect_link' => 'group/chat/'.$group_chat->unique_id,

                    'is_read' => 0,
                    'user_id' => $groupRequest->requested_by ?? '',
                    'send_by' => auth()->user()->id ?? '',
                    ];
            chatNotification($arr);

            $groupRequest->update([
                'status' => 1,
                'accepted_by' => auth()->user()->id,
            ]);
            $response['group_join_rqst_count']=groupJoinRequestCount($groupRequest->group_id);
            $response['status'] = true;
            $response['group_id'] = $groupRequest->group_id;
            $response['unique_id'] = $id;
            $response['redirect_back'] = baseUrl('/group/chat');
            $response['message'] = "Group Member Added Successfully";
            return response()->json($response);
    
}

public function rejectGroupMemberRequest($id,Request $request)
{

    $groupRequest = GroupJoinRequest::where("unique_id",$id)->first();
    $user = User::find($groupRequest->requested_by);
    $group_chat = ChatGroup::where("id",$groupRequest->group_id)->first();
    $arr = [
   'comment' => '*' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '* has rejected *' . $user->first_name . ' ' . $user->last_name . '*\'s request to join ' . $group_chat->name . ' Group.',
        'type' =>'group_chat',
        'redirect_link' => 'group/chat/'.$group_chat->unique_id,
        'is_read' => 0,
        'user_id' => $groupRequest->requested_by ?? '',
        'send_by' => auth()->user()->id ?? '',
        ];
chatNotification($arr);
GroupJoinRequest::deleteRecord($id);
$response['status'] = true;
$response['group_id'] = $groupRequest->group_id;
$response['unique_id'] = $id;
$response['redirect_back'] = baseUrl('/group/chat');
$response['message'] = "Group Request Removed Successfully";
return response()->json($response);

}

public function withdrawJoinRequest(Request $request)
{
    $user_id = auth()->user()->id; 
    $group_id = $request->input('group_id');

    // Check if the user has a pending request (status 0) for this group
    $existingRequest = GroupJoinRequest::where('group_id', $group_id)
        ->where('requested_by', $user_id)
        ->where('status', 0)
        ->first();

    if (!$existingRequest) {
        return response()->json(['status' => false, 'message' => 'No pending request found']);
    }

    $user = User::find($existingRequest->requested_by);
    $group_chat = ChatGroup::where("id",$group_id)->first();
    $arr = [
   'comment' => '*' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '* has withdraw request to join ' . $group_chat->name . ' Group.',
        'type' =>'group_chat',
        'redirect_link' => 'group/chat/'.$group_chat->unique_id,
        'is_read' => 0,
        'user_id' => $group_chat->added_by ?? '',
        'send_by' => auth()->user()->id ?? '',
        ];
    chatNotification($arr);

    GroupJoinRequest::deleteRecord($existingRequest->unique_id);

    $response['status'] = true;
    $response['redirect_back'] = baseUrl('group/chat/');
    $response['message'] = "Join request withdrawn successfully";
    return response()->json($response);

}

public function editGroup(Request $request,$groupId)
    {
        // dd($groupId);
        $user_id=auth()->user()->id;
        $pageTitle = "Edit New Group";
        $viewData['pageTitle'] = $pageTitle;
        $viewData['record'] = ChatGroup::where("unique_id", $groupId)->first();
        
        // Get available members for adding to the group
        $result = $this->groupChatService->getAvailableMembers($user_id);
        
        if ($result['success']) {
            $viewData['members'] = $result['members'];
        } else {
            $viewData['members'] = collect();
        }

        $view = View::make('admin-panel.01-message-system.group-chat.edit-new-group',$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function updateGroup(Request $request,$groupId)
    {
   
        $validator = Validator::make($request->all(), [
            'name' => 'required', // Ignore current group ID
            'group_type' => 'required',
            'description' => 'required|max:300',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=500',
        ], [
            'banner_image.image' => 'The file must be an image.',
            'banner_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'banner_image.dimensions' => 'The image must have a minimum width of 500 pixels.',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['error_type'] = 'validation';
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        $group = ChatGroup::where('unique_id', $groupId)->first();
        $group->name = $request->name;
        $group->type = $request->group_type;
        $group->added_by = auth()->user()->id;
        $group->description = $request->description;
        if ($file = $request->file('group_image')) {

            $fileName = $file->getClientOriginalName();
                $newName = mt_rand(1, 99999) . "-" . $fileName;
               
                $uploadPath = groupChatDir();
                $sourcePath = $file->getPathName();
                $api_response = mediaUploadApi("upload-file",$sourcePath,$uploadPath,$newName);
              
                if(($api_response['status']??'') === 'success'){
                    $group->group_image = $newName;
                    $response['status'] = true;
                    // $response['filename'] = $newName;
                    $response['message'] = "Record added successfully";
                }else{
                    $response['status'] = false;
                    $response['message'] = "Error while upload";
                }
        }
        if ($file = $request->file('banner_image')) {

            $fileName = $file->getClientOriginalName();
                $newName = mt_rand(1, 99999) . "-" . $fileName;
               
                $uploadPath = groupChatDir();
                $sourcePath = $file->getPathName();
                $api_response = mediaUploadApi("upload-file",$sourcePath,$uploadPath,$newName);
              
                if(($api_response['status']??'') === 'success'){
                    $group->banner_image = $newName;
                    $response['status'] = true;
                    // $response['filename'] = $newName;
                    $response['message'] = "Record added successfully";
                }else{
                    $response['status'] = false;
                    $response['message'] = "Error while upload";
                }
        }
        $group->save();
        $member_ids = array();
        if($request->member_id){
            $member_ids = $request->member_id;
            $member_ids[] = auth()->user()->id;
        }else{
            $member_ids[] = auth()->user()->id;
        }
        GroupMembers::where(['group_id'=>$group->id])->whereNotIn('user_id',$member_ids)->delete();

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('group/chat/'.$group->unique_id);
        $response['message'] = "Group Details Updated successfully";

        return response()->json($response);

    }


    public function pendinggGroupJoinRequest(Request $request)
    {
        $user_id=auth()->user()->id; 
        $search = $request->search;
        $groupdata = ChatGroup::whereHas('groupJoinRequest', function ($query) {
            $query->where('requested_by', auth()->user()->id)
                  ->where('status', 0); // Only pending requests
        })
        ->whereDoesntHave("groupMembers", function ($query) use ($user_id) {
            $query->where("user_id", $user_id);
        })
        ->where('type', 'Public')
        ->when(!empty($search), function ($query) use ($search) {
            $query->where("name", "LIKE", "%".$search."%");
        })
        ->addSelect([
            'last_message_date' => GroupMessages::query()
                ->select('created_at')
                ->whereColumn('group_messages.group_id', 'chat_groups.id')
                ->latest('created_at')
                ->where(function ($query) use ($user_id) {
                    $query->whereNull('group_messages.clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                })
                ->limit(1)
        ])
        ->orderBy('last_message_date', 'desc')
        ->get();
     
        $viewData['show_empty'] = false;
       $viewData['groupdata'] = $groupdata;
       $view = View::make('admin-panel.01-message-system.group-chat.chat.other_group_chat_sidebar_ajax', $viewData);
       $contents = $view->render();
       $response['contents'] = $contents;
       $response['count'] = count($groupdata);
       return response()->json($response);
      
   }

   public function deleteSelectedAttachments($msg_id,Request $request)
   {


       $userId = auth()->user()->id;
       $chatMessage = GroupMessages::where("unique_id",$msg_id)->first();

       $chat = ChatGroup::where("id",$chatMessage->group_id)->first();
       
       $attachments = !empty($chatMessage->attachment) ? explode(',', $chatMessage->attachment) : [];
       $filenameToDelete = trim($request->filename);
    
       // Remove only the selected file
       $filteredAttachments = array_filter($attachments, function ($file) use ($filenameToDelete) {
           return $file !== $filenameToDelete;
       });

       $path = groupChatDirUrl($request->filename);
      $api_response = mediaDeleteApi($path,$request->filename);
       

       // If no attachments remain, delete the entire message
       if (empty($filteredAttachments)) {
           $chatMessage->delete();
           
            $socket_data = [
           "action" => "delete_selected_attachments",
           "messageUniqueId" => $msg_id,
           "attachments" => [],
           ];
           initGroupChatSocket($chat->id, $socket_data);

           return redirect()->back()->with('success', 'Message deleted as no attachments remain.');
       }

       // Update and save the remaining attachments
       $chatMessage->attachment = implode(',', $filteredAttachments);
       $chatMessage->save();

       $socket_data = [
           "action" => "delete_selected_attachments",
           "messageUniqueId" => $msg_id,
           "attachments" => $filenameToDelete,
       ];
       initGroupChatSocket($chatMessage->group_id, $socket_data);

       return response()->json([
           'status' => true,
           'message' => 'Selected files deleted successfully.',
           'attachments' => $filenameToDelete
       ]);
   }

   public function fetchChatBot($group_id){
        $viewData['groupId'] = $group_id;
        $user_id=auth()->user()->id;
        $group = ChatGroup::with('groupMembers')->whereHas('groupMembers', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
        ->where("id", $group_id)
        ->first();

        $member=GroupMembers::where(['group_id'=>$group->id,'user_id'=> $user_id])->first();
        $viewData['member'] = $member;
        $viewData['group'] = $group;
        $viewData['currentGroupMember'] =NULL;
        if($group){
            $currentGroupMember = $group->groupMembers->firstWhere('user_id', auth()->user()->id);
            $viewData['currentGroupMember'] = $currentGroupMember;
        }
        $view = view("admin-panel.01-message-system.message-centre.chatbot.group-chatbot",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function checkGroupExists(Request $request){
        $group_id = $request->group_id;
        $user_id = $request->user_id;
        $count = ChatGroup::where("id",$group_id)
                ->whereHas('groupMembers',function($query) use($user_id){
                    $query->where("user_id",$user_id);
                })
                ->count();
        if($count > 0){
            $response['exists'] = true;
        }else{
            $response['exists'] = false;
        }
        $response['status'] = true;
        return response()->json($response);
    }

    public function getGroupInfo($group_id){
        $group = ChatGroup::where("id",$group_id)->first();
        $viewData['group'] = $group;
        $currentGroupMember = $group->groupMembers->firstWhere('user_id', auth()->user()->id);
        $viewData['currentGroupMember'] = $currentGroupMember;
        $group_members = GroupMembers::with('member')->where('group_id', '=', $group->id)->get();
        $viewData['group_members'] = $group_members;
        $view = view("admin-panel.01-message-system.group-chat.chat.group-info-sidebar",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);

    }

    public function getGroupJoinRequest($group_id)
    {   
        $group = ChatGroup::where("id",$group_id)->first();
        $viewData['group'] = $group;
        $viewData['group_requests'] = GroupJoinRequest::with('requester')
            ->where('group_id', $group->id)
            ->where('status', 0) 
            ->get();
        $view = view("admin-panel.01-message-system.group-chat.chat.group-request-sidebar",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function getSharedFile($group_id)
    {   
        $group = ChatGroup::where("id",$group_id)->first();
        $viewData['group'] = $group;
       
        $view = view("admin-panel.01-message-system.group-chat.chat.file-shared-sidebar",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

 public function groupsList(Request $request)
   {
     $viewData['pageTitle'] = "Groups List";
     $user_id = auth()->user()->id;
     $lastSegment = collect(request()->segments())->last();

        $viewData['type'] = $type = $lastSegment;
        
        // Configure page submenu based on type
        $pageTitle = "Groups Listing";
        $pageDescription = "Navigate through the available options below";
        
        switch($type) {
            case 'my-joined-group-list':
                $pageTitle = "My Joined Groups";
                $pageDescription = "View and manage groups you have joined";
                break;
            case 'my-created-group-list':
                $pageTitle = "My Created Groups";
                $pageDescription = "Manage groups you have created";
                break;
            case 'sent-request':
                $pageTitle = "Sent Requests";
                $pageDescription = "Track your group join requests";
                break;
            case 'received-request':
                $pageTitle = "Received Requests";
                $pageDescription = "Manage incoming group join requests";
                break;
            default:
                $pageTitle = "Groups Listing";
                $pageDescription = "Browse and join available groups";
                break;
        }
        
        $viewData['page_arr'] = [
            'page_title' => $pageTitle,
            'page_description' => $pageDescription,
            'page_type' => 'group-listings',
            'canCreateGroup' => true, // Add permission flag for create group button
        ];
  
        return view('admin-panel.01-message-system.groups-listing.group-list', $viewData);
    }
    
    public function messageCentreGroupsList(Request $request)
    {
        $viewData['pageTitle'] = "Groups List";
        $user_id = auth()->user()->id;
        $lastSegment = collect(request()->segments())->last();

        $viewData['type'] = $type = $lastSegment;
        
        // Configure page submenu for message centre based on type
        $pageTitle = "Groups Listing";
        $pageDescription = "Navigate through the available options below";
        
        switch($type) {
            case 'my-joined-group-list':
                $pageTitle = "My Joined Groups";
                $pageDescription = "View and manage groups you have joined";
                break;
            case 'my-created-group-list':
                $pageTitle = "My Created Groups";
                $pageDescription = "Manage groups you have created";
                break;
            case 'sent-request':
                $pageTitle = "Sent Requests";
                $pageDescription = "Track your group join requests";
                break;
            case 'received-request':
                $pageTitle = "Received Requests";
                $pageDescription = "Manage incoming group join requests";
                break;
            default:
                $pageTitle = "Groups Listing";
                $pageDescription = "Browse and join available groups";
                break;
        }
        
        $viewData['page_arr'] = [
            'page_title' => $pageTitle,
            'page_description' => $pageDescription,
            'page_type' => 'group-listings',
            'canCreateGroup' => true, // Add permission flag for create group button
        ];
  
        return view('admin-panel.01-message-system.message-centre.groups-listing.group-list', $viewData);
    }
 
     public function groupReceivedReqAjaxList(Request $request)
     {
        $viewData['pageTitle'] = "Groups Received Request List";
        $user_id = auth()->user()->id;
        $search = $request->search;
        $perPage = 15;
        $page = $request->input('page', 1);
        $lastSegment = collect(request()->segments())->last();

        $viewData['type'] =$type= $lastSegment;

        $groupQuery = ChatGroup::whereHas("groupMembers", function ($query) use ($user_id) {
                        $query->where("user_id", $user_id);
                    })
                    ->when($search, function ($query) use ($search) {
                        $query->where("name", "LIKE", "%" . $search . "%");
                    })
                    ->withExists('groupRequest')
                    ->whereHas('groupMembers', function ($query) use ($user_id) {
                        $query->where("user_id", $user_id)
                            ->where("is_admin", 1);
                    })
                    ->whereHas('groupRequest', function ($query) {
                        $query->where('status', 0);
                    })
                    ->with(['groupRequest' => function ($query) {
                        $query->where('status', 0)
                            ->with('requester');
                    }]);

            $groupdata = $groupQuery->paginate($perPage, ['*'], 'page', $page);
             //  dd($groupdata);
            $viewData['groupdata']  = $records= $groupdata;
            $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;
            $viewData['show_load_more'] = $records->currentPage() < $records->lastPage();
            $response['last_page'] =$viewData['last_page']= $records->lastPage();
            $response['current_page'] =$viewData['current_page']= $records->currentPage();
            $response['total_records'] =$viewData['total_records']= $records->total();

            $view = View::make('admin-panel.01-message-system.groups-listing.group-received-req-ajax-list', $viewData);
            $contents = $view->render();

            $response['contents'] = $contents;
            return response()->json($response);

        }
    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myGroupsAjaxList(Request $request)
    {
        $viewData['pageTitle'] = "Groups List";
        $user_id = auth()->user()->id;
        $search = $request->search;
        $perPage = 15;
        $type=$viewData['type']= $request->type;

        $page = $request->input('page', 1);
        // Main query
    
        $groupQuery = ChatGroup::whereHas("groupMembers", function ($query) use ($user_id) {
            $query->where("user_id", $user_id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where("name", "LIKE", "%" . $search . "%");
            })
            ->when($type == "my-created-group-list", function ($query) use ($user_id) {
                $query->where('added_by', $user_id)
                  ;
            })
            ->withExists('groupRequest');

        $groupdata = $groupQuery->paginate($perPage, ['*'], 'page', $page);
        $viewData['groupdata'] = $records= $groupdata;
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;
        $viewData['show_load_more'] = $records->currentPage() < $records->lastPage();
        $response['last_page'] =$viewData['last_page']= $records->lastPage();
        $response['current_page'] =$viewData['current_page']= $records->currentPage();
        $response['total_records'] =$viewData['total_records']= $records->total();
       
        $view = View::make('admin-panel.01-message-system.groups-listing.my-group-ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }
    
    public function groupsAjaxList(Request $request)
    {
        
        $type = $request->type;
        $user_id = auth()->user()->id; 
        $search = $request->search;
        $perPage = 15; // Number of groups per page
        $page = $request->input('page', 1); // Get the current page from the request, default to 1

        // Query to fetch groups where the user is not a member
       $groupQuery = ChatGroup::whereDoesntHave("groupMembers", function ($query) use ($user_id) {
        $query->where("user_id", $user_id);
        })
        ->withExists('groupRequest')
        ->where('type', 'Public')
        ->when($type == "sent-request", function ($query) use ($user_id) {
            $query->whereHas('groupRequest', function ($q) use ($user_id) {
                $q->where('requested_by', $user_id);
            });
        })
        ->when($type == "groups-list", function ($query) use ($user_id) {
            $query->whereDoesntHave('groupRequest', function ($q) use ($user_id) {
                $q->where('requested_by', $user_id);
            });
        })
        ->when($search != '', function ($query) use ($search) {
            $query->where("name", "LIKE", "%" . $search . "%");
        });

       $sentRequestCount = ChatGroup::whereDoesntHave('groupMembers', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->whereHas('groupRequest', function ($q) use ($user_id) {
                $q->where('requested_by', $user_id);
            })
            ->where('type', 'Public')
            ->count();

        $groupQuery->orderBy('id', 'desc');
        $groupdata = $groupQuery->paginate($perPage, ['*'], 'page', $page);
        $viewData['show_empty'] = $request->fetchNewer;
        $viewData['groupdata'] = $records=$groupdata;
        $viewData['type']=$response['type'] = $type;
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;
        $viewData['show_load_more'] = $records->currentPage() < $records->lastPage();

        $response['last_page'] =$viewData['last_page']= $records->lastPage();
        $response['current_page'] =$viewData['current_page']= $records->currentPage();
        $response['total_records'] =$viewData['total_records']= $records->total();
        $response['sent_req_count'] = $viewData['sent_req_count']=$sentRequestCount;
        $view = View::make('admin-panel.01-message-system.groups-listing.group-ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;


        return response()->json($response);
    }

}