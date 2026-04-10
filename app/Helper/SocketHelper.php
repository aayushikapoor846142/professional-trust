<?php
use App\Models\ChatInvitation;
use Spatie\PdfToImage\Pdf;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelReader;
use PhpOffice\PhpSpreadsheet\Reader\Xls as LegacyExcelReader;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as PdfWriter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\GroupSettings;
use App\Models\GroupMessagePermission;

use App\Models\GroupMembers;

use App\Events\MessageReactionAdded;
use App\Events\GroupMessageReactionChange;
use App\Events\ChatBlocked;
use App\Models\GroupMessagesRead;


use App\Models\FeedsConnection;
use App\Models\FeedComments;

use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\ChatMessageRead;
use App\Models\ChatRequest;
use App\Models\GroupMessages;
use App\Models\ChatMessage;
use App\Models\Feeds;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatNotification;
use App\Models\GroupJoinRequest;

if(!function_exists("initChatSocket")){
    function initChatSocket($chat_id,$socket_data)
    {
        event(new \App\Events\ChatSocket($chat_id,$socket_data));
    }
}
if(!function_exists("chatReqstCount")){
    function chatReqstCount($receiver_id){
           return  $chat_requests_count = ChatRequest::orderBy('id', "desc")
                                        ->where('receiver_id',$receiver_id)->where('is_accepted','!=','1')
                                        ->count();
            }
}
if(!function_exists("userActiveStatus")){
    function userActiveStatus(){
        if (Auth::check()) {
            $data = User::where('id',auth()->user()->id)->update([
                'last_activity' => now(),
                'is_login' => 1,
            ]);
            User::where('last_activity', '<', Carbon::now()->subMinutes(5))
            ->update(['is_login' => 0]);
        }     
    }
  }

if(!function_exists("userInctiveStatus")){
function userInctiveStatus(){

    if (Auth::check()) {
            User::where('id',auth()->user()->id)->update(['is_login' => 0]);
        }
    }
}
if(!function_exists("getUserCurrentStatus")){
function getUserCurrentStatus($userId){
$getUserStatus=User::where('id',$userId)->first();
         if($getUserStatus->is_login==1){
           $userActivityStatus="Active";
         }else{
           $userActivityStatus="InActive";
         }

         return $userActivityStatus;
     }
}
if(!function_exists("validateFileType")){
   
 function validateFileType($file,  $allowedTypes = [])
 {

        // Get file MIME type
        $fileMimeType = $file->getMimeType();

        // Check file extension
        $fileExtension = strtolower($file->getClientOriginalExtension());

        // Check allowed types
        if (!empty($allowedTypes) && !in_array($fileExtension, $allowedTypes)) {
            $response['status'] = false;
            $response['message'] = "Invalid file type. Allowed types are: " . implode(', ', $allowedTypes);
           
        }else{
            $response = ['status' => true, 'message' => 'File is valid.'];
        }
         return $response;

    }
}


if(!function_exists("initUserNotification")){
    function initUserNotification($user_id,$socket_data)
    {
        event(new \App\Events\UserNotification($user_id,$socket_data));
    }
}
if(!function_exists("initUserSocket")){
    function initUserSocket($user_id,$socket_data)
    {
        event(new \App\Events\UserSocket($user_id,$socket_data));
    }
}
if(!function_exists("messageReaction")){
    function messageReaction($message_id,$reaction,$reaction_unique_id,$chat_id,$action)
    {
        event(new MessageReactionAdded(
            $message_id,
            $reaction,
            auth()->user()->id,
            $chat_id,
            $reaction_unique_id,
            $action
        ));
    }
}
if(!function_exists("blockChat")){
    function blockChat($blockedBy,$blockedUserId,$chatId,$status)
    {
    event(new ChatBlocked(
        $blockedBy,
        $blockedUserId,
        $chatId,
        $status
    ));

    }
}


if(!function_exists("groupMessageReaction")){
    function groupMessageReaction($groupId,$message_id,$status)
    {
        event(new GroupMessageReactionChange(
            $groupId,
            $message_id,
            $status,
        ));
    }
}


if(!function_exists("initFeedContentSocket")){
    function initFeedContentSocket($feed_id,$socket_data)
    {
        event(new \App\Events\FeedContentSocket($feed_id,$socket_data));
      
    }
}

if(!function_exists("initDiscussionContentSocket")){
    function initDiscussionContentSocket($discussion_id,$socket_data)
    {
        event(new \App\Events\DiscussionContentSocket($discussion_id,$socket_data));
    
    }
}

if(!function_exists("initGroupChatSocket")){
    function initGroupChatSocket($group_id,$socket_data)
    {
        event(new \App\Events\GroupChatSocket($group_id,$socket_data));
    }
}
if(!function_exists("initGroupMessageSocket")){
    function initGroupMessageSocket($user_id,$socket_data)
    {
        event(new \App\Events\GroupMessageSocket($user_id,$socket_data));
    }
}

if(!function_exists("initDiscussionThreadSocket")){
    function initDiscussionThreadSocket($discussion_id,$socket_data)
    {
        event(new \App\Events\DiscussionThreadSocket($discussion_id,$socket_data));
    
    }
}

function unreadChatMessage($chat_id,$message_id,$receiver_id){
    $chat_msg_read = new ChatMessageRead();
    $chat_msg_read->receiver_id = $receiver_id;
    $chat_msg_read->chat_id = $chat_id;
    $chat_msg_read->message_id = $message_id;
    $chat_msg_read->status = "read";
    $chat_msg_read->save();

    $msg_read = ChatMessageRead::where("id",$chat_msg_read->id)->first();
    $socket_data = [
        "action" => "message_read",
        // "message" => "called from sockethelper",
        "chat_id" => $chat_id,
        "message_id" => $msg_read->chatMessage->unique_id,
        "sender_id" => auth()->user()->id,
    ];
    initChatSocket($chat_id, $socket_data);
  //
}

function readChatMessage($chat_id,$message_id,$receiver_id){
    $chat_msg_read =  ChatMessageRead::where('chat_id',$chat_id)
                        ->where('receiver_id',$receiver_id)
                        ->where('message_id',$message_id)
                        ->first();
    $chat_msg_read->status = "read";
    $chat_msg_read->save();
//   return "unread";
}   
function loginStatus($user){
    if($user->is_login == 0){
        return 0;
    }
    $lastActiveTime = $user->last_activity;

    // Parse the last active time into a Carbon instance
    $lastActive = Carbon::parse($lastActiveTime);

    // Get the current time
    $currentTime = Carbon::now();
    $is_login = 0;
    // Calculate the difference in minutes
    $minutesDifference = $lastActive->diffInMinutes($currentTime);
    if ($lastActive->diffInMinutes($currentTime) < 1) {
        // Difference is less than 1 minute, show in milliseconds
        $millisecondsDifference = $lastActive->diffInMilliseconds($currentTime);
        if($millisecondsDifference == 0){
            $user->is_login = 0;
            $user->save();
        }else{
            $is_login = 1;
        }
    } else {
        // Difference is 1 minute or more, show in minutes
        $minutesDifference = $lastActive->diffInMinutes($currentTime);
        if($minutesDifference > 0 && $minutesDifference < 5){
            $is_login = 1;
        }else{
            $user->is_login = 0;
            $user->save();
        }
    }
    
    return $is_login;
}

function triggerLoginStatus(){
    $chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                                ->where(function ($query) {
                                    $query->where('user2_id', auth()->user()->id)
                                        ->orWhere('user1_id', auth()->user()->id);
                                })
                                ->get();
            foreach($chat_user_list as $chat){
                if($chat->user1_id != auth()->user()->id){
                    $receiver_id = $chat->user1_id;
                }else{
                    $receiver_id = $chat->user2_id;
                }
                $socket_data = [
                    "action" => "user_online",
                    "message" => "",
                    "chat" => $chat->id,
                    "receiver_id" => $receiver_id,
                    "sender_id" => auth()->user()->id,
                ];
                initUserSocket($receiver_id, $socket_data);
            }
}
function triggerLogoutStatus(){
    User::where('id',auth()->user()->id)->update([
        'is_login' => 0, // Mark user as offline
        'last_activity' => now(), // Optional: Set last activity timestamp
    ]);
    $chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                                ->where(function ($query) {
                                    $query->where('user2_id', auth()->user()->id)
                                        ->orWhere('user1_id', auth()->user()->id);
                                })
                                ->get();
            foreach($chat_user_list as $chat){
                if($chat->user1_id != auth()->user()->id){
                    $receiver_id = $chat->user1_id;
                }else{
                    $receiver_id = $chat->user2_id;
                }
                $socket_data = [
                    "action" => "user_logout",
                    "message" => "",
                    "chat" => $chat->id,
                    "receiver_id" => $receiver_id,
                    "sender_id" => auth()->user()->id,
                ];
                initUserSocket($receiver_id, $socket_data);
            }
}


function chatExists($user1_id, $user2_id)
{
    $chatExists = Chat::where(function ($query) use ($user1_id, $user2_id) {
            $query->where('user1_id', $user1_id)
                  ->where('user2_id', $user2_id);
        })
        ->orWhere(function ($query) use ($user1_id, $user2_id) {
            $query->where('user1_id', $user2_id)
                  ->where('user2_id', $user1_id);
        })
        ->first();

    return $chatExists;
}
if(!function_exists("initialAttachments")){

    function initialAttachments($groupId)
    {

        return $initialAttachments = GroupMessages::where('group_id', $groupId)
        ->whereNotNull('attachment')
        ->latest()
        ->paginate(5);
      //  dd($initialAttachments); // Load first 10 attachments
    }
}
if(!function_exists("groupAttachments")){

function groupAttachments($groupId, $page = 1, $perPage = 50, $search = "")
{
    $search = request()->get('search', '');
    $page = request()->get('page', $page);
    $type=request()->get('type','');
    $query = GroupMessages::where('group_id', $groupId)
        ->whereNotNull('attachment')
        ->whereNull('clear_for')
        ->latest();

    if (!empty($search) && empty($type)) {
        $query->where('attachment', 'LIKE', "%{$search}%");
    }

    $groupAttachments = $query->paginate($perPage, ['*'], 'page', $page);

    if ($groupAttachments->isEmpty()) {
        return NULL;
    }

    // If first page and no search, return collection directly
    if ($page == 1 && empty($search) && empty($type)) {
        return $groupAttachments;
    }

    // Render HTML for AJAX response
    $html = view('admin-panel.01-message-system.group-chat.chat.partials.attachments', compact('groupAttachments'))->render();

    return response()->json([
        'data' => $groupAttachments->items(),
        'html' => $html,
        'current_page' => $groupAttachments->currentPage(),
        'last_page' => $groupAttachments->lastPage(),
    ]);
}


}
if(!function_exists("chatAttachments")){

function chatAttachments($chatId, $page = 1, $perPage = 1, $search = "")
{
    $search = request()->get('search', '');
    $page = request()->get('page', $page);
    $type=request()->get('type','');
    $query = ChatMessage::where('chat_id', $chatId)
        ->whereNotNull('attachment')
        ->whereNull('clear_for')
        ->latest();

    if (!empty($search) && empty($type)) {
        $query->where('attachment', 'LIKE', "%{$search}%");
    }

    $chatAttachments = $query->paginate($perPage, ['*'], 'page', $page);
   // dd($chatAttachments);
    if ($chatAttachments->isEmpty()) {
        return NULL;
    }

    // If first page and no search, return collection directly
    if ($page == 1 && empty($search) && empty($type)) {
        return $chatAttachments;
    }

    // Render HTML for AJAX response
    $html = view('admin-panel.01-message-system.message-centre.partials.attachments', compact('chatAttachments'))->render();

    return response()->json([
        'data' => $chatAttachments->items(),
        'html' => $html,
        'current_page' => $chatAttachments->currentPage(),
        'last_page' => $chatAttachments->lastPage(),
    ]);
}


}

if(!function_exists("FeedsListData")){
    function FeedsListData($type="my",$search="",$user_id = "")
    {

        $query = Feeds::with(['user', 'likes', 'comments']);
                    $connection_ids = FeedsConnection::where('user_id',$user_id)->get()->pluck('connection_with')->toArray();

        if($type=="other"){
            $query->whereIn('added_by', $connection_ids);
                   
        }elseif($type=="commented"){
            $commentedFeedIds = FeedComments::where('added_by', $user_id)
                                ->pluck('feed_id')
                                ->toArray();

             $query->whereIn('id', $commentedFeedIds);
                $query->where(function ($q) use ($connection_ids,$user_id) {
                    $q->whereIn('added_by', $connection_ids)
                    ->orWhere('added_by', $user_id);

                });
            
        }else{
            $query->where('added_by', $user_id);
                   
        }
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('post', 'LIKE', "%{$search}%") // Search in feed post
                    ->orWhereHas('user', function ($q) use ($search) { // Search in user name
                        $q->where('first_name', 'LIKE', "%{$search}%")
                        ;
                    })
                    ->orWhereHas('comments', function ($q) use ($search) { // Search in comments by the current user
                        $q->where('added_by', auth()->user()->id)
                            ->where('comment', 'LIKE', "%{$search}%");
                    });
                });
            }

        $query->latest();
        $feedsData=  $query->get();
       
        return $feedsData;
         
        }
    }
if(!function_exists("currentUserGroupsList")){
    function currentUserGroupsList()
    {
        return $groupdata = ChatGroup::whereHas("groupMembers",function($query) {
                    $query->where("user_id",auth()->user()->id);
                })
                ->addSelect(['last_message_date' => GroupMessages::query()
                    ->select('created_at')
                    ->whereColumn('group_messages.group_id', 'chat_groups.id')
                    ->latest('created_at')
                    ->limit(1)
                ])
                ->orderBy('last_message_date','desc')
                ->get();
            }
        }
if(!function_exists("chatUsersList")){
    function chatUsersList()
    {
        return Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                            ->where(function ($query) {
                                $query->where('user2_id', auth()->user()->id)
                                      ->orWhere('user1_id', auth()->user()->id);
                            })
                            ->get()
                            ->sortByDesc(fn($chat) => $chat->lastMessage->created_at ?? null)->fresh();
         
         }
    }
if(!function_exists("chatNotificationsCount")){
    function chatNotificationsCount($userId)
    {

        $chatNotifCount=  ChatNotification::where('user_id',$userId)
                                        ->where('is_read', '=', 0)
                                        ->orderBy('id', 'desc')
                                        ->count();
        return  $chatNotifCount;
    }
}

if(!function_exists("chatNotification")){
    function chatNotification($arr)
    {

        if($arr['type'] == "award_case"){
            $notif_data = [
                "action" => "award_case",
                "receiver_id" =>  $arr['user_id'],
                "message"=>$arr['comment'],
                "count" => $arr['count']
            ];
            initUserNotification($arr['user_id'], $notif_data);
           
        }else if($arr['type'] == "accept_retain_agreement"){
            $notif_data = [
                "action" => "accept_retain_agreement",
                "receiver_id" =>  $arr['user_id'],
                "message"=>$arr['comment'],
                "count" => $arr['count']
            ];
            initUserNotification($arr['user_id'], $notif_data);

        }else if($arr['type'] == "submit-review"){
            ChatNotification::create([
                'comment' => $arr['comment'] ?? null,
                'type' => $arr['type'] ?? null,
                'redirect_link' => $arr['redirect_link'] ?? null,
                'is_read' => $arr['is_read'] ?? null,
                'user_id' => $arr['user_id'] ?? null,
                'send_by' => $arr['send_by'] ?? null,
            ]);
           
        }else if($arr['type'] == "appointment_booking"){

            $notif_data = [
                "action" => "appointment_booking",
                "receiver_id" =>  $arr['user_id'],
                "message"=>$arr['comment'],
            ];
            initUserNotification($arr['user_id'], $notif_data);
        }
        else{
            ChatNotification::create([
                'comment' => $arr['comment'] ?? null,
                'type' => $arr['type'] ?? null,
                'redirect_link' => $arr['redirect_link'] ?? null,
                'is_read' => $arr['is_read'] ?? null,
                'user_id' => $arr['user_id'] ?? null,
               'send_by' => !empty($arr['send_by']) ? (int) $arr['send_by'] : null,
            ]);
             $socket_data = [
                "action" => "new_notification",
                "receiver_id" =>  $arr['user_id'],
                "message"=>$arr['comment'],
                "count" => chatNotificationsCount( $arr['user_id']),
            ];
            initUserSocket($arr['user_id'], $socket_data);
        }
    }
}


function generatePdfThumbnail($pdfUrl) {
    try {
        // Step 1: Download the PDF to a temporary file
        $tempPdfPath = public_path('uploads/temp/'.uniqid('pdf_', true) . '.pdf');

        $pdfContent = file_get_contents($pdfUrl); // Download PDF
        if ($pdfContent === false) {
            throw new Exception('Failed to download PDF.');
        }

        file_put_contents($tempPdfPath, $pdfContent); // Save the downloaded PDF to a temp file

        // Step 2: Generate a thumbnail
        $pdf = new Pdf($tempPdfPath);
        $pdf->setPage(1); // Use the first page for the thumbnail
        $tempImagePath = sys_get_temp_dir() . '/' . uniqid('thumb_', true) . '.jpg';

        $pdf->saveImage($tempImagePath); // Generate thumbnail and save to temp image

        // Step 3: Convert the thumbnail to base64
        $imageData = file_get_contents($tempImagePath); // Read image content
        $base64Thumbnail = 'data:image/jpeg;base64,' . base64_encode($imageData);

        // Step 4: Clean up temporary files
        unlink($tempPdfPath); // Delete temp PDF
        unlink($tempImagePath); // Delete temp image

        // Return base64-encoded thumbnail
        return $base64Thumbnail;
    } catch (Exception $e) {
        return null; // Handle errors gracefully
    }
}
function unreadTotalChatMessages($user_id = ''){
    if($user_id == ''){
        $user_id = auth()->user()->id;
    }
    $chat_message_read = ChatMessageRead::where("receiver_id",$user_id)->where("status","unread")->count();
    return $chat_message_read;
}
function unreadTotalGroupMessages($user_id = ''){
    if($user_id == ''){
        $user_id = auth()->user()->id;
    }
    $chat_message_read = GroupMessagesRead::where("user_id",$user_id)->where("status","unread")->count();
    return $chat_message_read;
}



function checkIfFollowing($user_id,$connection_with)
{
    $check_exists = FeedsConnection::where("user_id",$user_id)
                    ->where("connection_with",$connection_with)
                    ->count();
    return $check_exists;
}

function checkInvitation($added_by,$connect_id){
    $user = User::where("id",$connect_id)->first();
    $checkInvite= ChatInvitation::where('added_by',$added_by)
                ->where('email' , $user->email)
                ->first();
    return $checkInvite;
}

 function groupJoinRequestCount($group_id = '') {
   
    $requests = GroupJoinRequest::where("group_id",$group_id)
    ->where("status",0)
    ->count();
  
    return $requests;
}

function checkFollowing($user_id)
{
    $check = FeedsConnection::where('connection_with',$user_id)->where('user_id',auth()->user()->id)->first();
    // $check = FeedsConnection::where('connection_with',auth()->user()->id)->where('user_id',$user_id)->first();
    if(!empty($check)){
        return 'yes';
    }else{
        return 'no';
    }
}

function checkFollow($user_id)
{
    $data = FeedsConnection::where('user_id',auth()->user()->id)->where('connection_with',$user_id)->where('connection_type','!=','connect')->first();

    if(empty($data)){
        return 'Follow';
    }else{
        return 'Unfollow';
    }
}
function updateMessagingBox($sender_id,$receiver_id){
    $socket_data = [
        "action" => "update_messaging_box",
        "receiver_id" => $receiver_id,
        "receiver_unread_count"=>unreadTotalChatMessages($receiver_id),
        "receiver_total_unread_count"=>unreadTotalGroupMessages($receiver_id)+unreadTotalChatMessages($receiver_id),
        "sender_id" => $sender_id,
    ];
    initUserSocket($receiver_id, $socket_data);

    $socket_data = [
        "action" => "update_messaging_box",
        "receiver_id" => $receiver_id,
        "sender_unread_count"=>unreadTotalChatMessages(auth()->user()->id),
        "sender_total_unread_count"=>unreadTotalGroupMessages($sender_id)+unreadTotalChatMessages($sender_id),
        "sender_id" => $sender_id,
    ];
    initUserSocket($sender_id, $socket_data);
}
function minify_html($html) {
    // Remove whitespace between tags, line breaks, and extra spaces
    return preg_replace(
        ['/>\s+</', '/\s{2,}/', '/<!--.*?-->/s'], 
        ['><', ' ', ''], 
        $html
    );
}


if (!function_exists('checkGroupPermission')) {
    function checkGroupPermission(string $type, int $groupId, $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        $group = ChatGroup::find($groupId);
        if (!$group) return false;

        $settings = GroupSettings::where('group_id', $groupId)->first();
        if (!$settings) return true;

        $member = GroupMembers::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->first();

        switch ($type) {
            case 'only_admins_can_post':
                return $settings->only_admins_can_post != 1;

            case 'members_can_add_members':
                return $settings->members_can_add_members == 1;

            case 'can_see_messages':
                switch ($settings->who_can_see_my_message) {
                    case 'members':
                        return GroupMessagePermission::where('group_id', $groupId)
                            ->where('member_id', $userId)
                            ->exists();
                    case 'admins':
                        return $member && $member->is_admin;
                    case 'everyone':
                    default:
                        return true;
                }

            default:
                return false;
        }
    }
}

// global notification

if(!function_exists("initGlobalNotification")){
    function initGlobalNotification($user_id,$socket_data)
    {
        event(new \App\Events\GlobalNotification($user_id,$socket_data));
    }
}

if(!function_exists("globalNotification")){
    function globalNotification($arr)
    {

        if($arr['type'] == "post_case"){
            $notif_data = [
                "action" => "post_case",
                "receiver_id" =>  $arr['user_id'],
                "case_id" => $arr['redirect_link'],
                "message"=>$arr['comment']
            ];
           
            initGlobalNotification($arr['user_id'], $notif_data);
                   
        }
    }
}
if(!function_exists("initTicketSystemSocket")){
    function initTicketSystemSocket($user_id,$socket_data)
    {
        event(new \App\Events\TicketSystemSocket($user_id,$socket_data));
    }
}
if(!function_exists("initChatMessageSocket")){
    function initChatMessageSocket($user_id,$socket_data)
    {
        event(new \App\Events\ChatMessageSocket($user_id,$socket_data));
    }
}
// end
    
?>