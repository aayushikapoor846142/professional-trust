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

class GroupMessageController extends Controller
{
    protected $groupChatService;

    public function __construct(GroupChatService $groupChatService)
    {
        $this->groupChatService = $groupChatService;
    }

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        $pageTitle = "Group Messages";
        $viewData['pageTitle'] = $pageTitle;
        $viewData['user_id'] = $user_id;
        $viewData['welcome_page'] = true;
        $result = $this->groupChatService->getUserGroupsList($user_id);
        if ($result['success']) {
            $viewData['groups'] = $result['groupdata'];
        } else {
            $viewData['groups'] = collect();
        }
        $viewData['group_id'] = 0;
        $viewData['last_message_id'] = 0;
        $viewData['first_message_id'] = 0;
        return view("admin-panel.01-message-system.group-messages.index",$viewData);
    }   
    
    public function groupChat(Request $request,$group_id)
    {
        $user_id = auth()->user()->id;
        $group_data = $this->groupChatService->getGroup($group_id); 
        if (!$group_data['success']) {
            return redirect('group-message')->with('error', $group_data['message']);
        }

        $group = $group_data['group'];
        $group_members = $group_data['group_members'];
        // $group_messages = $group_data['group_messages'];
        $result = $this->groupChatService->getGroupMessages($group->id,0,$user_id);
     
        if (!$result['status']) {
            return redirect('group-message')->with('error', $result['message']);
        }
       
        $group_messages = $result['data']['group_messages'];
        $last_msg_id = $result['data']['last_msg_id'];
        $first_msg_id = $result['data']['first_msg_id'];
        $has_previous_messages = $result['data']['has_previous_messages']??0;
        $pageTitle = "Group Messages";
        $currentGroupMember = $group->groupMembers->firstWhere('user_id', auth()->user()->id);

        $viewData['pageTitle'] = $pageTitle;
        $viewData['user_id'] = $user_id;
        $viewData['welcome_page'] = false;
        $viewData['group_id'] = $group_id;
        $viewData['group'] = $group;
        $viewData['group_members'] = $group_members;
        $viewData['group_messages'] = $group_messages;
        $viewData['currentGroupMember'] = $currentGroupMember;
        $viewData['last_message_id'] = $last_msg_id;
        $viewData['first_message_id'] = $first_msg_id;
        $viewData['has_previous_messages'] = $has_previous_messages;
        $result = $this->groupChatService->getAvailableMembers($user_id);
        if ($result['success']) {
            $viewData['members'] = $result['members'];
        } else {
            $viewData['members'] = collect();
        }
        $result = $this->groupChatService->getUserGroupsList($user_id);
        if ($result['success']) {
            $viewData['groups'] = $result['groupdata'];
        } else {
            $viewData['groups'] = collect();
        }
        return view("admin-panel.01-message-system.group-messages.index",$viewData);
    } 

    /**
     * Send message to group - Similar flow to GroupChatController
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'send_msg' => ['sometimes', 'input_sanitize'],
                'group_id' => 'required|integer',
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

            $groupId = $request->group_id;
            $message = $request->send_msg ?? $request->message ?? '';
            $attachedFile = [];

            // Check if message or attachment exists
            if ($request->attachment != null || ($message != null && $message != "")) {
                
                // Handle file uploads
                if ($request->hasFile('attachment')) {
                    foreach ($request->file('attachment') as $file) {
                        $allowedTypes = [
                            'jpeg', 'jpg', 'png', 'gif', 'bmp', 'webp', 'svg', // Image
                            'xls', 'xlsx', 'csv', // Excel
                            'pdf',         // PDF
                            'txt',         // Plain text
                            'mp3',         // Audio
                            'mp4', 'mpeg'  // Video
                        ];

                        $fileResponse = validateFileType($file, $allowedTypes);
                        if (!$fileResponse['status']) {
                            return response()->json($fileResponse);
                        }

                        $fileName = $file->getClientOriginalName();
                        $newName = mt_rand(1, 99999) . "-" . $fileName;
                        $uploadPath = groupChatDir();
                        $sourcePath = $file->getPathName();
                        
                        $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
                    
                        if (($api_response['status'] ?? '') === 'success') {
                            $attachedFile[] = $newName;
                            
                            // Notify via socket for file upload
                            $socket_data = [
                                "action" => "new_file_uploaded",
                                "file" => $newName,
                            ];
                            initGroupChatSocket($groupId, $socket_data);
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

                // Send message using GroupChatService
                $result = $this->groupChatService->sendGroupMessage($groupId, $messageData, auth()->user()->id);

                if (!$result['success']) {
                    $response['status'] = false;
                    $response['message'] = $result['message'];
                    return response()->json($response);
                }

                $chat_message = $result['data'];

                // Create read records for group members
                $group_members = GroupMembers::where("group_id", $groupId)
                    ->where("user_id", "!=", auth()->user()->id)
                    ->get();

                foreach ($group_members as $gm) {
                    $group_message_read = new GroupMessagesRead();
                    $group_message_read->group_id = $groupId;
                    $group_message_read->user_id = $gm->user_id;
                    $group_message_read->group_message_id = $chat_message->id;
                    $group_message_read->save();

                    updateMessagingBox(auth()->user()->id, $gm->user_id);
                }

                // Get message with relationships for view
                $userId = auth()->user()->id;
                $get_chat_message = GroupMessages::withTrashed()
                    ->where('id', $chat_message->id)
                    ->where(function($query) use($userId) {
                        $query->whereNull('clear_for');
                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                    }) 
                    ->with('replyTo')
                    ->first();

                $viewData = [];
                if ($get_chat_message->message != null || $get_chat_message->attachment != null) {
                    $viewData['chat_msg'] = $get_chat_message;
                } else {
                    $viewData['chat_msg'] = null;
                }

                // Handle reply notifications
                if ($request->reply_to != null) {
                    $group_chat_first_msg = GroupMessages::where("id", $request->reply_to)->first();
                    $group_chat = ChatGroup::find($groupId);
                    
                    if ($group_chat_first_msg && $group_chat_first_msg->user_id != auth()->user()->id) {
                        $arr = [
                            'comment' => '*'. auth()->user()->first_name . " " . auth()->user()->last_name . '* has replied to your message in ' . $group_chat->name . ' Group.',
                            'type' => 'group_chat',
                            'redirect_link' => 'group-message/chat/'.$group_chat->unique_id,
                            'is_read' => 0,
                            'user_id' => $group_chat_first_msg->user_id ?? '',
                            'send_by' => auth()->user()->id ?? '',
                        ];
                        chatNotification($arr);
                    }
                }

                // Handle @mentions
                if (strpos($request->send_msg ?? '', '@') !== false) {
                    $parts = explode('*', $request->send_msg ?? '');
                    $name = trim(str_replace('@', '', $parts[1] ?? ''));
             
                    $names = explode(' ', $name, 2); 
                    $receiver = User::where('first_name', $names[0] ?? '')
                        ->where('last_name', $names[1] ?? '')
                        ->latest()
                        ->first();
                 
                    if ($receiver) {
                        $group_chat = ChatGroup::find($groupId);
                        $arr = [
                            'comment' => '*'.auth()->user()->first_name . " " . auth()->user()->last_name . '* has mentioned you in ' . $group_chat->name . ' Group.',
                            'type' => 'group_chat',
                            'redirect_link' => 'group-message/chat/'.$group_chat->unique_id,
                            'is_read' => 0,
                            'user_id' => $receiver->id ?? '',
                            'send_by' => auth()->user()->id ?? '',
                        ];
                        chatNotification($arr);
                    }
                }

                // Handle group message permissions and notifications
                $senderId = auth()->user()->id;
                $group = ChatGroup::findOrFail($groupId);

                // Get group settings
                $grpSettings = GroupSettings::where('group_id', $groupId)->first();
                $group_chat = $group;

                if ($grpSettings) {
                    $type = $grpSettings->who_can_see_my_message;
                } else {
                    $type = "none";
                }

                // Determine recipients based on settings
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
                if(!in_array(auth()->user()->id,$receiverIds)){
                    $receiverIds[] = auth()->user()->id;
                }

                $socket_data = [
                    "action" => "new_message",
                    "message" => $message,
                    "group_id" => $groupId,
                    "last_message_id" => $chat_message->id,
                    "sender_id" => $senderId,
                    // "receiver_id" => $memberId,
                ];
                initGroupChatSocket($groupId, $socket_data);
                // Render view for response
                $response['status'] = true;
                return response()->json($response);
            }

            $response['status'] = false;
            $response['message'] = 'No message or attachment provided';
            return response()->json($response);

        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
    public function updateGroupMessage(Request $request, $message_uid)
    {
        $chatMessage = GroupMessages::where("unique_id",$message_uid)->where("user_id",auth()->user()->id)->first();
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

    public function switchGroup(Request $request, $group_id)
    {
        try {
            $user_id = auth()->user()->id;
            $last_msg_id = $request->last_msg_id ?? 0;
            
            // Get group data
            $group_data = $this->groupChatService->getGroup($group_id);
            if (!$group_data['success']) {
                return response()->json(['status' => false, 'message' => $group_data['message']]);
            }

            $group = $group_data['group'];
            $group_members = $group_data['group_members'];
            
            // Get group messages
            $result = $this->groupChatService->getGroupMessages($group->id, $last_msg_id, $user_id);
            if (!$result['status']) {
                return response()->json(['status' => false, 'message' => $result['data']['err_message'] ?? 'Failed to fetch group messages']);
            }

            $group_messages = $result['data']['group_messages'];
            $last_msg_id = $result['data']['last_msg_id'];
            $first_msg_id = $result['data']['first_msg_id'];
            $currentGroupMember = $group->groupMembers->firstWhere('user_id', $user_id);

            $viewData = [
                'group_id' => $group_id,
                'group' => $group,
                'group_members' => $group_members,
                'group_messages' => $group_messages,
                'currentGroupMember' => $currentGroupMember,
                'last_msg_id' => $last_msg_id,
                'first_msg_id' => $first_msg_id,
                'user_id' => $user_id
            ];

            // Render the message container view
            $messages_content = view('admin-panel.01-message-system.group-messages.components.chat-container', $viewData)->render();



            
            $response = [
                'status' => true,
                'message' => $messages_content,
                'last_msg_id' => $last_msg_id,
                'first_msg_id' => $first_msg_id
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function loadGroupMessages(Request $request, $group_id)
    {
        try {
            $user_id = auth()->user()->id;
            $last_msg_id = $request->last_message_id ?? 0;
            
            // Get group data
            $group_data = $this->groupChatService->getGroup($group_id);
            if (!$group_data['success']) {
                return response()->json(['status' => false, 'message' => $group_data['message']]);
            }

            $group = $group_data['group'];
            $group_members = $group_data['group_members'];
            
            // Get group messages
            $result = $this->groupChatService->getGroupMessages($group->id, $last_msg_id, $user_id);
            if (!$result['status']) {
                return response()->json(['status' => false, 'message' => $result['data']['err_message'] ?? 'Failed to fetch group messages']);
            }

            $group_messages = $result['data']['group_messages'];
            $last_msg_id = $result['data']['last_msg_id'];
            $first_msg_id = $result['data']['first_msg_id'];
            $currentGroupMember = $group->groupMembers->firstWhere('user_id', $user_id);

            $viewData = [
                'group_id' => $group_id,
                'group' => $group,
                'group_members' => $group_members,
                'group_messages' => $group_messages,
                'currentGroupMember' => $currentGroupMember,
                'last_msg_id' => $last_msg_id,
                'first_msg_id' => $first_msg_id,
                'user_id' => $user_id
            ];

            // Render the messages view (just the messages, not the full container)
            $content = view('admin-panel.01-message-system.group-messages.components.group-messages', $viewData)->render();


            $result = $this->groupChatService->getUserGroupsList(auth()->user()->id);
            $viewData = array();
            if ($result['success']) {
                $viewData['groups'] = $result['groupdata'];
            } else {
                $viewData['groups'] = collect();
            }
            $viewData['group_id'] = $group->unique_id??0;
            $sidebar_content = view('admin-panel.01-message-system.group-messages.components.group-sidebar',$viewData)->render();
        
            $response = [
                'status' => true,
                'messages_content' => $content,
                'last_msg_id' => $last_msg_id,
                'first_msg_id' => $first_msg_id
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function addReactionToMessage(Request $request){
        $message_id = $request->message_id;
        $grp_message =  GroupMessages::where("unique_id",$message_id)->first();
        $reaction=GroupMessageReaction::updateOrCreate(['message_id'=>$grp_message->id,'added_by'=>auth()->user()->id],['reaction'=>$request->reaction]);
    
        $group =  ChatGroup::where("id",$grp_message->group_id)->first();
        $arr = [
            'comment' => '*'.auth()->user()->first_name." ".auth()->user()->last_name.'* has reacted *'. $request->reaction . '* to your message from '.$group->name.'  Group.',
            'type' =>'group_chat',
            'redirect_link' => 'group-message/chat/'.$group->unique_id,
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

    public function getReactedMessage($group_id,$message_uid,Request $request)
    {
        $userId=auth()->user()->id;
        $group = ChatGroup::where("id",$group_id)->first();
        $group_messages = GroupMessages::withTrashed()->with('sentBy')
                ->where('group_id',$group_id)
                ->where('unique_id',$message_uid)
                ->where(function($query) use($userId){
                        $query->whereNull('clear_for');
                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                })
                ->first();
        $group_messages = $group_messages ? collect([$group_messages]) : collect([]);
        $viewData['group_messages'] = $group_messages;
        $viewData['group_id'] = $group->unique_id;
        $viewData['msg_type'] = 'reaction_msg';
        
        $view = View::make('admin-panel.01-message-system.group-messages.components.group-messages', $viewData);
        $contents = $view->render();
        $response['msg_type'] = 'reaction_msg';
        $response['contents'] = $contents;
        $response['messageUniqueId'] = $message_uid;
        return response()->json($response);
      
    }

    public function deleteMessageForMe($msg_id,Request $request)
    {
        $userId = auth()->user()->id;
        $message = GroupMessages::where('unique_id',$msg_id)->first();
        $socket_data = [
                "action" => "delete_msg_for_me",
                "message_id" => $msg_id,
                "messageUniqueId" => $message->unique_id,
                "group_id" => $message->group_id,
                "sender_id" => $userId
            ];
        initGroupChatSocket($message->group_id, $socket_data);

        $responseMessage = $message->markAsDeletedForUser($userId);
        $response['status'] = true;
        $response['message'] = 'Message Deleted Successfully';
        return response()->json($response);
    
    }
    public function deleteMessageForAll($msg_id,Request $request)
    {
            $grp_msg = GroupMessages::where('unique_id',$msg_id)->first();
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
            $group_members = GroupMembers::where("group_id",$groupId)
            ->get();
            
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
            $response['status'] = true;
            $response['message'] = 'Message Deleted Successfully';
            return response()->json($response);
    }

    public function fetchGroupSidebar(Request $request)
    {
        $result = $this->groupChatService->getUserGroupsList(auth()->user()->id);
        if ($result['success']) {
            $viewData['groups'] = $result['groupdata'];
        } else {
            $viewData['groups'] = collect();
        }
        $viewData['group_id'] = $request->group_id;
        $content = view('admin-panel.01-message-system.group-messages.components.group-sidebar',$viewData)->render();
        return response()->json(['status'=>true,'sidebar_content'=>$content]);
    }

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

        $view = View::make('admin-panel.01-message-system.group-messages.components.add-new-group', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
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
            $group = $result['group'];
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('group-message/chat/'.$group->unique_id);
            $response['message'] = $result['message'];
            
        } else {
            $response['status'] = false;
            $response['message'] = $result['message'];
        }

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

        $view = View::make('admin-panel.01-message-system.group-messages.components.view-group-members', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }
    public function getGroupInfo($group_id){
        $group = ChatGroup::where("unique_id",$group_id)->first();
        $viewData['group'] = $group;
        $currentGroupMember = $group->groupMembers->firstWhere('user_id', auth()->user()->id);
        $viewData['currentGroupMember'] = $currentGroupMember;
        $group_members = GroupMembers::with('member')->where('group_id', '=', $group->id)->get();
        $viewData['group_members'] = $group_members;
        $view = view("admin-panel.01-message-system.group-messages.components.group-info-sidebar",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);

    }

    public function getGroupJoinRequest($group_id)
    {   
        $group = ChatGroup::where("unique_id",$group_id)->first();
        $viewData['group'] = $group;
        $viewData['group_requests'] = GroupJoinRequest::with('requester')
            ->where('group_id', $group->id)
            ->where('status', 0) 
            ->get();
        $view = view("admin-panel.01-message-system.group-messages.components.group-request-sidebar",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function getSharedFile($group_id)
    {   
        $group = ChatGroup::where("unique_id",$group_id)->first();
        $viewData['group'] = $group;
        $result = $this->groupChatService->getGroupChatFiles($group_id);
        $viewData['files'] = $result;       
        $view = view("admin-panel.01-message-system.group-messages.components.files.shared-files",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }
    public function previewFile(Request $request){
        $viewData['file_name'] = $request->file_name;
        $viewData['fileUrl'] = groupChatDirUrl($request->file_name,'r');
        $viewData['chatMessage'] =  GroupMessages::where("unique_id",$request->chat_id)->first();
        $view = view("admin-panel.01-message-system.group-messages.components.files.preview-file",$viewData);
        
        $response['status'] = true;
        $response['contents'] = $view->render();

        return response()->json($response);
    }
    public function editGroup(Request $request,$groupId)
    {
        // dd($groupId);
        $user_id=auth()->user()->id;
        $pageTitle = "Edit New Group";
        $viewData['pageTitle'] = $pageTitle;
        $viewData['record'] = ChatGroup::where("unique_id", $groupId)->first();
    

        $view = View::make('admin-panel.01-message-system.group-messages.components.edit-group',$viewData);
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
        $response['redirect_back'] = baseUrl('group-message/chat/'.$group->unique_id);
        $response['message'] = "Group Details Updated successfully";

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

        $view = View::make('admin-panel.01-message-system.group-messages.components.add-new-members', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
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

                $socket_data = [
                    "action" => "new_group_added",
                    'message'=>"*".auth()->user()->first_name." ".auth()->user()->last_name.'* has you to group *'.$group_chat->name.'*',
                    "member_id" => $member,
                ];
                initGroupMessageSocket($member, $socket_data);
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
                        'redirect_link' => 'group-message/chat/' . $group_id,
                        'is_read' => 0,
                        'user_id' => $member->user_id ?? '',
                        'send_by' => auth()->user()->id ?? '',
                    ];
                    chatNotification($arr);
                }
            }
       
            }
     
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('group-message/chat/'.$group_id);
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
                    $redirect_back = 'group-message/chat/' . $group_chat->unqiue_id;
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
            $redirect_back = baseUrl('group-message/chat/');
        }

        return redirect($redirect_back)->with($msg_type, $message);

    }

    /**
     * Fetch older messages for infinite scroll
     */
    public function fetchOlderMessages(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $group_id = $request->group_id;
            $first_msg_id = $request->first_msg_id;
            $user_id  = $request->user_id;
            // Call service to fetch older messages
            $result = $this->groupChatService->fetchOlderMessages($group_id, $first_msg_id, $user_id);
            if (!$result['success']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }

            // Prepare view data
            $viewData['group_messages'] = $result['group_messages'];
            $viewData['msg_type'] = 'all_msg';
            $viewData['group_id'] = $group_id;
            
            // Render the view
            $view = View::make('admin-panel.01-message-system.group-messages.components.group-messages', $viewData);
            $contents = $view->render();

            // Return response
            return response()->json([
                'status' => true,
                'contents' => $contents,
                'first_msg_id' => $result['first_msg_id'],
                'has_more_messages' => $result['has_more_messages'],
                'message_count' => $result['message_count']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage() . " LINE: " . $e->getLine()
            ]);
        }
    }

    
    public function removeGroupAdmin($member_id,Request $request)
    {

           $fetch_member=GroupMembers::where(['unique_id'=>$member_id])->first();
           $fetch_member->is_admin=0;
           $fetch_member->save();

        return redirect()->back()->with("success", "Remove from group admin successfully");


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
                'redirect_link' => 'group-message/chat/'.$group->unique_id,
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
                
            return redirect(baseUrl('/group/chat'))->with("message", "Removed Successfully");

            // $response['status'] = true;
            // $response['removed'] = 'yes';
            // $response['redirect_back'] = baseUrl('/group/chat');
            // $response['message'] = "Removed Successfully";
    
            // return response()->json($response);
        }else{

            return redirect(baseUrl('/group/mark-as-admin/'.$id))->with("success", "Removed Successfully");
            // $response['status'] = true;
            // $response['removed'] = 'no';
            // $response['redirect_back'] =  baseUrl('/group/mark-as-admin/'.$id);
            // $response['message'] = "Removed Successfully";
    
            // return response()->json($response);
          
        }
       
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
       
        return redirect(baseUrl('/group-message/chat'))->with("success","Group deleted successfully");
    }
}