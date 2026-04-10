<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatMessageRead;
use App\Models\User;
use App\Models\MessageCentreReaction;
use App\Models\ChatRequest;
use App\Models\DraftChatMessage;
use App\Models\ChatInvitation;
use App\Models\GroupMessages;
use App\Models\ChatGroup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MessageCentreService
{
    public function sendMessage($chat_id, Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
            if($request->attachment!=NULL || $message!=NULL){
                $viewData['chat_id'] =$chat_id;
                $chat_message=new ChatMessage();
                $chat_message->chat_id=$chat_id;
                if($request->reply_to!=NULL){
                    $chat_message->reply_to=$request->reply_to;
                }
                if ($request->hasFile('attachment')) {
                    $attachedFile = [];
                    foreach ($request->file('attachment') as $file) {
                        $allowedTypes = [
                            'jpeg', 'jpg', 'png', 'gif', 'bmp', 'webp', 'svg',
                            'xls', 'xlsx', 'csv',
                            'pdf',
                            'txt',
                            'mp3',
                            'mp4', 'mpeg'
                        ];
                        $fileResponse=validateFileType($file,$allowedTypes);
                        if(!$fileResponse['status']){
                            return response()->json($fileResponse);
                        }
                        $fileName = $file->getClientOriginalName();
                        $newName = mt_rand(1, 99999) . "-" . $fileName;
                        $uploadPath = chatDir();
                        $sourcePath = $file->getPathName();
                        $api_response = mediaUploadApi("upload-file",$sourcePath,$uploadPath,$newName);
                        if(($api_response['status']??'') === 'success'){
                            $attachedFile[] = $newName;
                            $response['status'] = true;
                        }
                    }
                }
                $chat_message->message=$message;
                $chat_message->parent_id=0;
                $chat_message->sent_by=auth()->user()->id;
                if(($message!="" && $message!=NULL) || !empty($attachedFile)){
                    if(!empty($attachedFile)){
                        $chat_message->attachment = implode(',',$attachedFile);
                    }
                    $chat_message->save();
                }
                $chat = Chat::where("id",$chat_id)->first();
                DraftChatMessage::where("reference_id",$chat_id)->where("type","individual_chat")->where("user_id",auth()->user()->id)->delete();
                if($chat->user1_id != auth()->user()->id){
                    $receiver_id = $chat->user1_id;
                }else{
                    $receiver_id = $chat->user2_id;
                }
                $chat_msg_read = new ChatMessageRead();
                $chat_msg_read->receiver_id = $receiver_id;
                $chat_msg_read->chat_id = $chat_id;
                $chat_msg_read->message_id = $chat_message->id;
                $chat_msg_read->status = "unread";
                $chat_msg_read->save();
                if($chat_message->message || $chat_message->attachment!=NULL){
                    $viewData['chat_msg']=$chat_message;
                }else{
                    $viewData['chat_msg']=NULL;
                }
                $viewData['receiver_id'] = $receiver_id;
                $viewData['chat'] = $chat;
                $viewData['openfrom'] = $request->openfrom;
                $view = \View::make('admin-panel.01-message-system.message-centre.msg_sent_block', $viewData);
                $contents = $view->render();
                $response['contents'] = $contents;
                $response['id'] = $chat_message->id;
                $userActivityStatus=getUserCurrentStatus($receiver_id);
                $socket_data = [
                    "action" => "new_message",
                    "message" => $message,
                    "chat_id" => $chat_id,
                    "receiver_id" => $receiver_id,
                    "last_message_id" => $chat_message->id,
                    "sender_id" => auth()->user()->id,
                    "userActivityStatus"=>$userActivityStatus
                ];
                initChatSocket($chat_id, $socket_data);
                $socket_data = [
                    "action" => "new_chat_message",
                    "message" => "Message receive from ".auth()->user()->first_name." ".auth()->user()->last_name,
                    "receiver_id" => $receiver_id,
                    "chat_id" => $chat_id,
                    "last_message_id" => $chat_message->id,
                    "unread_count"=>unreadTotalChatMessages($receiver_id),
                    "total_unread_count"=>unreadTotalGroupMessages($receiver_id)+unreadTotalChatMessages($receiver_id),
                    "sender_id" => auth()->user()->id,
                ];
                initUserSocket($receiver_id, $socket_data);
                updateMessagingBox(auth()->user()->id,$receiver_id);
                userActiveStatus();
                $response['status'] = true;
                return response()->json($response);
            }
        } catch (\Exception $e) {
             return response()->json($e->getMessage());
        }
    }

    public function fetchChats($chat_id, $last_msg_id, Request $request)
    {
        try {
            $chat = Chat::where('id', $chat_id)->with(['chatWith','addedBy'])->first();
            if($chat->user1_id != auth()->user()->id){
                $receiver_id = $chat->user1_id;
            }else{
                $receiver_id = $chat->user2_id;
            }
            $userId=auth()->user()->id;
            $viewData['chat'] = $chat;
            $viewData['receiver_id'] = $receiver_id;
            $prev_msg_read = ChatMessageRead::where('chat_id',$chat->id)
                                            ->where("receiver_id",$receiver_id)
                                            ->where("status",'read')
                                            ->latest()
                                            ->first();
                $unread_messages = ChatMessageRead::where('chat_id',$chat->id)
                ->where("receiver_id",auth()->user()->id)
                ->where("status","unread")
                ->get();
                $chat_messages = ChatMessage::withTrashed()
                        ->with('sentBy')
                        ->where('chat_id',$chat_id)
                        ->where(function($query) use($userId){
                            $query->whereNull('clear_for');
                            $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                        })
                        ->where(function($query) use($last_msg_id){
                            if($last_msg_id != 0){
                                $query->where("id",">",$last_msg_id);
                            }
                        })
                        ->latest()
                        ->limit(50) 
                        ->get();
                        $current_first_msg_id=$request->first_msg_id;
                        if($last_msg_id==0){
                                $first_msg_id = $chat_messages[count($chat_messages) - 1]->id??0;
                        }else{
                                $first_msg_id=$current_first_msg_id;
                        }
            $chat_messages = $chat_messages->sortBy('id'); 
            $last_message = ChatMessage::withTrashed()->with('sentBy')
                        ->where('chat_id',$chat_id)
                        ->latest()
                        ->first();
            $last_sent_message = ChatMessage::withTrashed()->with('sentBy')
                        ->where('chat_id',$chat_id)
                        ->latest()
                        ->first();
            $last_read_message = ChatMessageRead::where("chat_id",$chat_id)
                                ->where("receiver_id",$receiver_id)
                                ->where('status','read')
                                ->orderBy("id","desc")
                                ->first();
            $msg_ids = array();
            foreach($unread_messages as $msg){
                $msg_ids[] = $msg->chatMessage->unique_id; 
            }
            if(checkPrivacySettings('message-center','read-receipts',auth()->user()->id,url()->full())){
                ChatMessageRead::where('chat_id',$chat->id)->where("receiver_id",auth()->user()->id)->update(['status'=>'read']);
            }
            $socket_data = [
                "action" => "message_read",
                "chat_id" => $chat->id,
                "message_id" => implode(",",$msg_ids),
                "unread_count" => unreadTotalChatMessages($userId),
                "total_unread" => unreadTotalChatMessages($userId) + unreadTotalGroupMessages($userId),
                "sender_id" => auth()->user()->id,
            ];
            initChatSocket($chat->id, $socket_data);
            $response['first_msg_id'] = $first_msg_id;
            $response['last_msg_read'] = false;
            $response['last_msg_unique_id'] = $last_message?$last_message->unique_id:0;
            $response['last_msg_id'] = $last_message?$last_message->id:0;
            $response['message_id'] = $last_message?$last_message->unique_id:0;
            $last_read_message_id =  $last_read_message->message_id??0;
            $response['last_read_message'] = $last_read_message_id;
            $viewData['first_msg_id'] = $first_msg_id;
            $viewData['last_msg_id'] = $last_message?$last_message->id:0;
            if($last_sent_message){
                if($last_read_message_id == $last_sent_message->id){
                    $response['last_msg_read'] = true;
                }else{
                    $response['last_msg_read'] = false;
                }
                if($last_msg_id != $last_sent_message->id){
                    $viewData['chat_messages'] = $chat_messages;
                    $viewData['openfrom'] = $request->openfrom;
                    $viewData['chat_id'] = $chat->id;
                    if(!empty($msg_ids) && $last_msg_id == 0){
                        $viewData['unread_from_id'] = $msg_ids[0];
                    }
                    $view = \View::make('admin-panel.01-message-system.message-centre.chat_ajax', $viewData);
                    $contents = $view->render();
                    $response['contents'] = $contents;
                    $response['new_msg'] = true;
                }else{
                    $response['new_msg'] = false;
                }
            }else{
                $response['last_msg_read'] = false;
            }
            if($viewData['first_msg_id'] == 0 && $viewData['last_msg_id'] == 0){
                $view = \View::make('admin-panel.01-message-system.message-centre.empty-chat', $viewData);
                $contents = $view->render();
                $response['contents'] = $contents;
            }
            if(isset($last_read_message->id)){
                if(($prev_msg_read->message_id??0) == $last_read_message->id){
                    $response['prev_msg_read'] = 0;
                }else{
                    $response['prev_msg_read'] = $prev_msg_read->message_id??0;
                }
            }else{
                $response['prev_msg_read'] = 0;
            }
            $userId = auth()->user()->id;
            $chat = Chat::with(['reactions'])->findOrFail($chat->id);
            $is_typing = false;
            if($chat->user1_id != $userId) {
                if($chat->user1_typing == 1){
                    $is_typing = true;
                }
            }elseif ($chat->user2_id != $userId) {
                if($chat->user2_typing == 1){
                    $is_typing = true;
                }
            }
            if($chat->blocked_chat==1){
                $response['is_blocked'] = true;
            }else{
                $response['is_blocked'] = false;
            }
            $response['is_typing'] = $is_typing;
            userActiveStatus();    
            updateMessagingBox(auth()->user()->id,$receiver_id);
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['err_message'] = $e->getMessage();
            $response['err_line'] = $e->getLine();
            return response()->json($response);
        }
    }

    // Add other methods as needed
} 