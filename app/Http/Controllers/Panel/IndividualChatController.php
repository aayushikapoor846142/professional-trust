<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ChatInvitation;
use App\Models\DraftChatMessage;
use App\Models\GroupMessages;
use Illuminate\Http\Request;
use View;
use App\Models\Chat;
use App\Models\ChatMessageRead;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatGroup;
use Carbon\Carbon;
use App\Models\MessageCentreReaction;
use App\Models\ChatRequest;
use Illuminate\Support\Facades\Validator;
use App\Services\MessageCentreService;

class IndividualChatController extends Controller
{
    protected $messageCentreService;

    public function __construct(MessageCentreService $messageCentreService)
    {
        $this->messageCentreService = $messageCentreService;
    }

    public function index(Request $request)
    {
        try {
            userActiveStatus();
            // Get the user's IP address
            $user_id=auth()->user()->id;
        
            $chat = Chat::with(['lastMessage'])
                ->where(function($query) use($user_id){
                    $query->whereNull('delete_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, delete_for)', [$user_id]);
                })
                ->where(function($query) use($user_id){
                    $query->where('user1_id',$user_id);
                    $query->orWhere('user2_id',$user_id);
                })
                ->get()
                ->sortByDesc(function($chat) {
                    return $chat->lastMessage ? $chat->lastMessage->created_at : $chat->created_at;
                })
                ->first();
               
            $viewData['welcome_page'] = true;
            $viewData['chat_id'] = 0;
            $viewData['chat_users'] = $this->messageCentreService->getChatUsers(auth()->user()->id);
            return view('admin-panel.01-message-system.individual-chats.index',$viewData);
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
        // $data = $this->messageCentreService->getMessages($request); 
        
    }

    public function individualChat(Request $request, $chat_id)
    {
        // $data = $this->messageCentreService->getMessages($request); 
        $result = $this->messageCentreService->fetchIndividualChats($chat_id,0,$request);
        if($result['status']){
            $viewData = $result['data'];
        }else{
            return redirect(baseUrl('/individual-chats'))->with("error",$result['message']??'Chat not available');
        }
        $chat = $this->messageCentreService->getChat($chat_id);
        if($chat && ($chat->user1_id != auth()->user()->id && $chat->user2_id != auth()->user()->id)){
            return redirect(baseUrl('/'))->with("error", "You are not authorized to access this chat");
        }
        $viewData['chat'] = $chat;
        $viewData['chat_id'] = $chat_id;
        $viewData['chat_users'] = $this->messageCentreService->getChatUsers(auth()->user()->id,$chat_id);
        $viewData['welcome_page'] = false;
        
        // Ensure chat_messages and receiver_id are available for the view
        if (!isset($viewData['chat_messages'])) {
            $viewData['chat_messages'] = collect(); // Empty collection if no messages
        }
        if (!isset($viewData['receiver_id'])) {
            // Calculate receiver_id if not provided
            if ($chat->user1_id != auth()->user()->id) {
                $viewData['receiver_id'] = $chat->user1_id;
            } else {
                $viewData['receiver_id'] = $chat->user2_id;
            }
        }
       
        return view('admin-panel.01-message-system.individual-chats.index',$viewData);
    }
    public function composeMessage()
    {
        $user_id = auth()->user()->id;
        $pageTitle = "Compose Message";
        $viewData['pageTitle'] = $pageTitle;

        // Get only connected users (users with existing chats)
        $connected_users = collect();
        
        // Get users from existing chats
        $existing_chats = Chat::where(function($query) use($user_id) {
            $query->where('user1_id', $user_id)
                  ->orWhere('user2_id', $user_id);
        })->where('chat_type', 'individual')->get();
        
        foreach($existing_chats as $chat) {
            $other_user_id = ($chat->user1_id == $user_id) ? $chat->user2_id : $chat->user1_id;
            $user = User::where('id', $other_user_id)
                       ->where('status', 'active')
                       ->first();
            
            if($user) {
                $user->chat_id = $chat->id;
                $user->chat_unique_id = $chat->unique_id;
                $connected_users->push($user);
            }
        }
        
        // Remove duplicates and sort by first name
        $connected_users = $connected_users->unique('id')->sortBy('first_name');
        
        $viewData['users'] = $connected_users;

        $view = View::make('admin-panel.01-message-system.message-centre.compose-message', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function sendMessage($chat_id,Request $request)
    {
        return $this->messageCentreService->sendMessage($chat_id, $request);
    }
    public function updateMessage(Request $request, $chat_id)
    {
  
        $chatMessage =  ChatMessage::where("unique_id",$chat_id)->first();
        $chatMessage->message = $request->message;
        $chatMessage->edited_at = now();
        $chatMessage->save();
        $socket_data = [
            "action" => "message_edited",
            "chat_id" => $chatMessage->chat_id,
            "messageUniqueId" => $chatMessage->unique_id,
            "editedMessage" => $chatMessage->message,
        ];
        initChatSocket($chatMessage->chat_id, $socket_data);
        return response()->json(['status' => true,"updated_message"=> $request->message, 'message' => 'Message updated successfully.']);
      
    }
    public function individualChatAjax($chat_id,Request $request)
    {
        $last_msg_id = $request->last_msg_id??0;
        $chat_id = $request->chat_id??0;
        $chat = $this->messageCentreService->getChat($chat_id);
        $result = $this->messageCentreService->fetchIndividualChats($chat_id,$last_msg_id,$request);
        if($result['status']){
            $viewData = $result['data'];
        }else{
            return response()->json(['status'=>false,'message'=>$result['data']['err_message']]);
        }
        $viewData['chat_id'] = $chat_id;
        $viewData['chat'] = $chat;
        $content = view('admin-panel.01-message-system.individual-chats.message-container',$viewData)->render();
        $response['status'] = true;
        $response['message'] = $content;
        $response['last_msg_id'] = $result['data']['last_message_id']??0;
        $response['first_msg_id'] = $result['data']['first_message_id']??0;
        $response['unread_count'] = $result['data']['unread_count']??0;
        $response['total_unread'] = $result['data']['total_unread']??0;
        return response()->json($response);
    }   

    public function loadChatMessages($chat_id,Request $request)
    {
        $last_msg_id = $request->last_msg_id??0;
        if($request->openfrom == "chatBot"){    
            $chat_id = $request->chat_id??0;
            $chat = Chat::where("id",$chat_id)->first();
            $chat_id = $chat->unique_id;
        }else{
            $chat = $this->messageCentreService->getChat($chat_id);
        }
        // $chat_id = $request->chat_id??0;
        
        $result = $this->messageCentreService->fetchIndividualChats($chat_id,$last_msg_id,$request);
        if($result['status']){
            $viewData = $result['data'];
        }else{
            return response()->json(['status'=>false,'message'=>$result['data']['err_message']]);
        }
        $viewData['chat_id'] = $chat_id;
        $viewData['chat'] = $chat;
        $content = view('admin-panel.01-message-system.individual-chats.messages',$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $content;
        $response['last_msg_id'] = $result['data']['last_message_id']??0;
        $response['first_msg_id'] = $result['data']['first_message_id']??0;
        $response['unread_count'] = $result['data']['unread_count']??0;
        $response['total_unread'] = $result['data']['total_unread']??0;
        return response()->json($response);
    }   

    public function fetchChatSidebar(Request $request)
    {
        $chat_users = $this->messageCentreService->getChatUsers(auth()->user()->id);
        $viewData['chat_id'] = $request->chat_unique_id??0;
        $viewData['chat_users'] = $chat_users;
        $content = view('admin-panel.01-message-system.individual-chats.chat-sidebar',$viewData)->render();
        return response()->json(['status'=>true,'message'=>$content]);
    }

    public function deleteChatMessageForMe($msg_id,Request $request)
    {
     
        $userId = auth()->user()->id;
        $chatMessage = ChatMessage::findOrFail($msg_id);
        $socket_data = [
            "action" => "delete_msg_for_me",
            "message_id" => $msg_id,
            "messageUniqueId" => $chatMessage->unique_id,
            "chat_id" => $chatMessage->chat_id,
            "sender_id" => $userId
        ];
        initChatSocket($chatMessage->chat_id, $socket_data);
         // Call the model's method
        $response['message'] = $chatMessage->deleteForUser($userId);
        $response['message_id'] = $chatMessage->unique_id;   
        $response['status'] = true;
        return response()->json($response);
       
    }
    public function deleteChatMessageForEveryone($msg_id,Request $request)
    {
        $userId = auth()->user()->id;
       
        $getChatMsg= ChatMessage::where('id',$msg_id)->first();
        $chat_id=$getChatMsg->chat_id;
        $uploadPath = chatDir();
        $file=$getChatMsg->attachment;
        if($file){
           $data= mediaDeleteApi($uploadPath,$file);
        }
       
        $chat = Chat::findOrFail($chat_id);
        $messageUniqueId=$getChatMsg->unique_id;
        $getChatMsgRead= ChatMessageRead::where('chat_id',$chat_id)->where('message_id',$msg_id)->delete();
      
        if($chat->user1_id != $userId){
            $receiver_id = $chat->user1_id;
        }else{
            $receiver_id = $chat->user2_id;
        }
        $socket_data = [
            "action" => "deleted_msg_for_everyone",
            "message_id" => $msg_id,
            "messageUniqueId" => $messageUniqueId,
            "chat_id" => $chat_id,
        ];
        $reactionFetch=MessageCentreReaction::where('message_id',$getChatMsg->id)->first();
        if($reactionFetch){
          $reactionFetch->delete();
        }
        $getChatMsg->delete();
        initChatSocket($chat_id, $socket_data);

        $socket_data = [
            "action" => "deleted_msg_for_everyone",
            "chat_id" => $chat_id,
            "receiver_id"=>$receiver_id,
            "deleted_by" => auth()->user()->id
        ];
        initUserSocket($receiver_id, $socket_data);
        $response['status'] = true; 
        $response['message'] = 'Message Deleted Successfully';
        return response()->json($response);
    }
    public function previewFile(Request $request){
        $viewData['file_name'] = $request->file_name;
        $viewData['chatMessage'] =  ChatMessage::where("unique_id",$request->chat_id)->first();
      
        $viewData['fileUrl'] = chatDirUrl($request->file_name,'r');
        $view = view("admin-panel.01-message-system.individual-chats.preview-file",$viewData);
        
        $response['status'] = true;
        $response['contents'] = $view->render();

        return response()->json($response);
    }
    public function getChatFiles(Request $request, $chat_id)
    {
        try {
            $userId = auth()->user()->id;
            
            // Verify user has access to this chat
            $chat = Chat::where('unique_id', $chat_id)
                        ->where(function($query) use($userId) {
                            $query->where('user1_id', $userId)
                                ->orWhere('user2_id', $userId);
                        })
                        ->first();

            if (!$chat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chat not found or access denied'
                ], 403);
            }

            // $page = $request->input('page', 1);
            // $search = $request->input('search', '');
            // $perPage = 2;

            // Use the existing helper function
            // $result = chatAttachments($chat_id, $page, $perPage, $search);
            $result = $this->messageCentreService->getChatFiles($chat_id);
         
            $viewData['files'] = $result;
            $attachments = view('admin-panel.01-message-system.individual-chats.files.shared-files',$viewData)->render();
           
            return response()->json([
                'status' => true,
                // 'files' => $result,
                'contents' => $attachments,
                // 'hasMorePages' => $data->current_page < $data->last_page,
                // 'currentPage' => $data->current_page,
                // 'totalPages' => $data->last_page
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching chat files: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addReactionToMessage(Request $request){
        $message_id = $request->message_id;
        $message =  ChatMessage::where("unique_id",$message_id)->first();
        $reaction=  MessageCentreReaction::updateOrCreate(['message_id'=>$message->id,'added_by'=>auth()->user()->id],['reaction'=>$request->reaction]);

        messageReaction($message_id,$request->reaction,$reaction->unique_id,$message->chat_id,"add_reaction");
        $response['status'] = true;
        return response()->json($response);
    }

    public function removeReactionToMessage(Request $request){
        try{
            $message_id = $request->message_id;
            $message_reaction =  MessageCentreReaction::where("unique_id",$message_id)->first();
            MessageCentreReaction::where("unique_id",$message_id)->where('added_by',auth()->user()->id)->delete();
            $response['status'] = true;
            messageReaction($message_id,'',$message_reaction->unique_id,$message_reaction->message->chat_id,"remove_reaction");
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json(['status'=>false,'message'=>$e->getMessage()." ".$e->getLine()." Line: ".$e->getTraceAsString()]);
        }
    }


    public function getUserProfile($chat_id)
    {
        $user_id=auth()->user()->id;
        $chat = Chat::where('unique_id',$chat_id)->first();

        if($chat){
            $viewData['get_chat_user'] = User::where('id', $chat->user2_id)->first();
            $viewData['get_chat_professional'] = User::where('id', $chat->user1_id)->first();
            $viewData['chat_id'] = $chat->id;
        }else{
            $viewData['chat_id'] = NULL;
        }

        $view = View::make('admin-panel.01-message-system.individual-chats.user-profile-sidebar', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function getSharedFile($chat_id)
    {
        $user_id=auth()->user()->id;
        $chat = Chat::where('id', $chat_id)->first();

        $viewData['chat'] = $chat;
        if($chat){
            $viewData['get_chat_user'] = User::where('id', $chat->user2_id)->first();
            $viewData['get_chat_professional'] = User::where('id', $chat->user1_id)->first();
            $viewData['chat_id'] = $chat->id;
        }else{
            $viewData['chat_id'] = NULL;
        }

        $view = View::make('admin-panel.01-message-system.individual-chats.share-file-sidebar', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function blockChat($chat_id,Request $request)
    {
            $chat = Chat::find($chat_id);
            $chat->blocked_chat=1;
            $chat->blocked_by=auth()->user()->id;
            $chat->save();
            if($chat->user1_id==$chat->blocked_by){
                $blockedUserId=$chat->user2_id;
            }else{
                $blockedUserId=$chat->user1_id;

            }

            $socket_data = [
                "action" => "blocked",
                "chat_id" => $chat->id,
                'blocked_by' => $chat->blocked_by,
                "blockedUserId" => $blockedUserId,
            ];
            initChatSocket($chat->id, $socket_data);

            blockChat($chat->blocked_by,$blockedUserId,$chat->id,'blocked');
            $response['status'] = true;
            $response['message'] = 'Chat Blocked Successfully';
            return response()->json($response);
    
    }
     public function unblockChat($chat_id,Request $request)
    {
            $chat = Chat::find($chat_id);
            $chat->blocked_chat=0;
            $chat->blocked_by=NULL;
            $chat->save();
            $blockedBy=auth()->user()->id;
            if($chat->user1_id==auth()->user()->id){
                $blockedUserId=$chat->user2_id;
                
            }else{
                $blockedUserId=$chat->user1_id;
            }

            $socket_data = [
                "action" => "unblocked",
                "chat_id" => $chat->id,
                'blocked_by' => $blockedBy,
                "blockedUserId" => $blockedUserId,
            ];
            initChatSocket($chat->id, $socket_data);

            blockChat($blockedBy,$blockedUserId,$chat->id,'unblocked');

            $response['status'] = true;
            $response['message'] = 'Chat Unblocked Successfully';
            return response()->json($response);
    
    }

    public function clearChatForUser(Request $request, $chat_id)
    {
        try {
            $userId = auth()->user()->id;
            $selectedMessageIds = $request->input('clear_msg'); // Array of selected message IDs
            $messages = ChatMessage::withTrashed()->whereIn('id',$selectedMessageIds)->get();
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
                ChatMessage::withTrashed()->where('id', $message->id)
                    ->update(['clear_for' => $deletedBy]);
            }
            $message_count = ChatMessage::withTrashed()
                            ->with('sentBy')
                            ->where('chat_id',$chat_id)
                            ->where(function($query) use($userId){
                                $query->whereNull('clear_for');
                                $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                            })
                            ->count();
            $response['msgIds'] = $msgIds;
            $response['message_count'] = $message_count;
            $response['message'] = 'Chat cleared Successfully';
            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while clearing messages: ' . $e->getMessage()
            ]);
        }
    }


    public function deleteChatForUser($chat_id,Request $request)
    {
        $userId = auth()->user()->id;
        $chat = Chat::where('unique_id',$chat_id)->first();
        $chat_id=$chat->id;
        if($chat->user1_id == auth()->user()->id){
            $receiver_id = $chat->user2_id;
        }else{
            $receiver_id = $chat->user1_id;
        }
        $socket_data = [
            "action" => "chat_deleted",
            "chat_id" => $chat_id,
            "receiver_id"=>$receiver_id,
            "deleted_by" => auth()->user()->id
        ];
        initUserSocket(auth()->user()->id, $socket_data);
        initUserSocket($receiver_id, $socket_data);
        initChatSocket($chat_id, $socket_data);
        // Call the model method to delete the chat and its dependencies
        $responseMessage = $chat->deleteChatForAll($userId);
        return redirect(baseUrl('individual-chats'))->with('success','Chat deleted successfully');
    }

    public function fetchChatBot($chat_id){
        $viewData['chatId'] = $chat_id;

        $chat = Chat::where("id",$chat_id)->first();
        if($chat->user1_id != auth()->user()->id){
            $user_id = $chat->user1_id;
        }else{
            $user_id = $chat->user2_id;
        }
        $user = User::where("id",$user_id)->first();
        $viewData['userId'] = $user_id;
        $viewData['user'] = $user;
        $viewData['chat'] = $chat;
        $viewData['username'] = $user->first_name." ".$user->last_name;
        $view = view("admin-panel.01-message-system.chatbot.chatbot",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }
}   

?>