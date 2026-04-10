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
class MessageCentreController extends Controller
{
    protected $messageCentreService;

    public function __construct(MessageCentreService $messageCentreService)
    {
        $this->messageCentreService = $messageCentreService;
        // Constructor method for initializing middleware or other components if needed
    }

    public function overview()
    {
        $userId = auth()->user()->id;
        $viewData['pageTitle'] = "Message Overview";

        // Total Chats (individual)
        $viewData['totalChats'] = \App\Models\Chat::where(function($query) use($userId){
            $query->where('user1_id', $userId)->orWhere('user2_id', $userId);
        })->count();

        // Unread Messages (individual)
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        // Total Group Chats
        $viewData['totalGroupChats'] = \App\Models\ChatGroup::whereHas('groupMembers', function($query) use($userId) {
            $query->where('user_id', $userId);
        })->count();

        // Invitations
        $viewData['invitations'] = chatReqstCount($userId);

        // Notifications
        $viewData['notifications'] = chatNotificationsCount($userId);

        // Recent Group Chats (latest 10 by last message date)
        $viewData['recentGroupChats'] = \App\Models\ChatGroup::with('lastMessage')
            ->whereHas('groupMembers', function($query) use($userId) {
                $query->where('user_id', $userId);
            })
            ->addSelect(['last_message_date' => \App\Models\GroupMessages::query()
                ->select('created_at')
                ->whereColumn('group_messages.group_id', 'chat_groups.id')
                ->latest('created_at')
                ->limit(1)
            ])
            ->orderByDesc('last_message_date')
            ->limit(5)
            ->get();

        // Recent Chats (latest 10 by last message date)
        $viewData['recentChats'] = \App\Models\Chat::with(['lastMessage', 'addedBy', 'chatWith'])
            ->where(function ($query) use ($userId) {
                $query->where('user2_id', $userId)
                    ->orWhere('user1_id', $userId);
            })
            ->orderByDesc(\DB::raw('(SELECT MAX(created_at) FROM chat_messages WHERE chat_id = chats.id)'))
            ->limit(5)
            ->get();

        return view("admin-panel.01-message-system.message-centre.message-overview", $viewData);
    }

    /**
     * Display the list of settings.
     *
     * @return \Illuminate\View\View
     */
 
 
    public function chatInitialize($receiver_id)
    {

        try {

            // Calculate the new expiration date, 30 days from now.
            // Retrieve the job using the provided unique ID.
            $chat = Chat::where(['user2_id'=> $receiver_id,'chat_type'=>'individual'])->first();

            $user_id=auth()->user()->id;
            // Check if the job exists.
            if (is_null($chat)) {
                 
             
                // Update the job's payment details.
                $chat_ins=new Chat;
                $chat_ins->unique_id = randomNumber();
                $chat_ins->user1_id=$user_id;
                $chat_ins->chat_type = 'individual';
                $chat_ins->user2_id = $receiver_id;
                $chat_ins->save();                
                
                // Redirect back with a success message.
                return redirect(url('panel/message-centre/'))->with("success", "Chat Initialized Successfully");
            } else {

                return redirect(url('panel/message-centre/'))->with("success", "You may start chat now");
            }
        } catch (\Exception $e) {
            // Handle any errors that occur during the process.
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
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
    public function searchUsers(Request $request){
        $value = $request->value;
        $users = User::where('id', '!=', auth()->user()->id)
         ->where(function($query) use($value){
            $query->where("first_name","LIKE","%".$value."%");
            $query->orWhere("last_name","LIKE","%".$value."%");
            $query->orWhere("email","LIKE","%".$value."%");
        })
        ->where('status', 'active') // Ensure user is active
        ->whereIn('role', ['professional', 'associate', 'client'])
        ->get();

        $admin_users = User::where("role","admin")
        ->where(function($query) use($value){
            $query->where("first_name","LIKE","%".$value."%");
            $query->orWhere("last_name","LIKE","%".$value."%");
            $query->orWhere("email","LIKE","%".$value."%");
        })
        ->where("id","!=",auth()->user()->id)
        ->get();

        $all_users = $users->merge($admin_users);
        foreach($all_users as $key => $user){
            $flag = 0;
            $userId=auth()->user()->id;
            $userId2=$user->id;
                    
            $chat=chatExists($userId,$userId2);

            // if($chat){
            //     $flag = 1;
            // }
        
            $chat_request = ChatRequest::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', auth()->user()->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                      ->where('sender_id', auth()->user()->id);
            })->first();
            if($chat_request){
                if($flag == 0){
                    $user->invitation_send = 1;
                    $user->invitation_accepted=$chat_request->is_accepted;
                    $user->chat_reqst_id=$chat_request->unique_id;
                    $user->receiver_id=$chat_request->receiver_id;
                }
            }
             $chat_invite = ChatInvitation::where(function ($query) use ($user) {
                $query->where('added_by', $user->id)
                      ->where('email', auth()->user()->email);
            })->orWhere(function ($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('added_by', auth()->user()->id);
            })->first();
            if($chat_invite){
                if($flag == 0){
                    $user->invitation_send = 1;
                    $user->invitation_id = $chat_invite->unique_id;
                    $user->invitation_accepted=$chat_invite->status;

                }
            }

            if($flag == 1){
                unset($all_users[$key]);
            }
        }
        $viewData['users'] = $all_users;

        $contents = view("admin-panel.01-message-system.message-centre.search-users",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }
    public function index(Request $request)
    {
   
        try {
              userActiveStatus();
            // Get the user's IP address
            $user_id=auth()->user()->id;
        
            $chat = Chat::where(function($query) use($user_id){
                $query->whereNull('delete_for');
                $query->orWhereRaw('NOT FIND_IN_SET(?, delete_for)', [$user_id]);
            })
            ->where(function($query) use($user_id){
                $query->where('user1_id',$user_id);
                $query->orWhere('user2_id',$user_id);
            })
            ->orderBy('id','desc')->first();
            // dd($user_id);
            // dd($chat);
            if(!$chat && url()->current() != baseUrl("message-centre/")){
                return redirect(baseUrl("message-centre/"));
            }else{
                
                if($chat &&  url()->current() == baseUrl("message-centre/")){
                    return redirect(baseUrl("message-centre/chat/".$chat->unique_id));
                }
            }
            $viewData['chat'] = $chat;
            $viewData['type'] = 'individual';
            $groupdata = ChatGroup::with([
                'members' => function($query) use ($user_id) {
                    $query->where('user_id', $user_id);

                },
                'lastMessage' // Eager load last message
            ])
            ->addSelect(['last_message_date' => GroupMessages::query()
                ->select('created_at')
                ->whereColumn('group_messages.group_id', 'chat_groups.id')
                ->latest('created_at')
                ->limit(1)
            ])
            ->orderBy('last_message_date','desc')
            ->get();
            $viewData['groupdata'] = $groupdata;
            $viewData['chat_id'] =$chat_id=NULL;
            $viewData['chat_requests'] = ChatRequest::orderBy('id', "desc")
                                        ->where(function($query) {
                                           $query->where('receiver_id',auth()->user()->id)->where('is_accepted','!=','1');
                                        })->with(['sender','receiver'])
                                        ->get();
            $viewData['chat_user_list']=NULL;

            if($chat){
                $viewData['get_chat_user'] = User::where('id', $chat->user2_id)->first();
                $viewData['get_chat_professional'] = User::where('id', $chat->user1_id)->first();
                $viewData['chat_user_list']=$chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                                ->where(function ($query) {
                                    $query->where('user2_id', auth()->user()->id)
                                        ->orWhere('user1_id', auth()->user()->id);
                                })
                                ->get()
                                ->sortByDesc(fn($chat) => $chat->lastMessage->created_at ?? null);
            
                $viewData['chat_user_list'] =$chat_user_list; 
            
                $viewData['chat_id'] =$chat_id=$chat->id;
                $chat_messages = ChatMessage::withTrashed()
                ->with('sentBy')->where('chat_id',$chat->id)
                ->where(function($query) use($user_id){
                        $query->whereNull('clear_for');
                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
                })
                ->get();
                $viewData['chat_messages'] = $chat_messages;
                if(count($chat_messages) > 0){
                    $viewData['chat_empty'] = false;
                }else{
                    $viewData['chat_empty'] = true;
                }                       
            }
            return view('admin-panel.01-message-system.message-centre.chat',$viewData);
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
    }

    public function individualChat($conversation_id = '',Request $request)
    {
        try {
          
            $viewData = $this->chatData($conversation_id);
            if($viewData){
            //    dd('sjsjsj');
                return view('admin-panel.01-message-system.message-centre.chat',$viewData);
            }else{
                return redirect(baseUrl("message-centre/"));
            }
            
        } catch (\Exception $e) {
            // Handle any errors that occur during the process.
            return redirect()->back()->with("error", "An error occurred: " . $e->getMessage());
        }
    }

    public function individualChatAjax($conversation_id,Request $request){
        $viewData = $this->chatData($conversation_id);

        if($viewData){
            $view = view("admin-panel.01-message-system.message-centre.message-container",$viewData);
            $contents = $view->render();
        }else{
            return redirect(baseUrl("message-centre/"));

        }
      
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function chatData($conversation_id)
    {
        $user_id = auth()->user()->id;
        $chat = Chat::where('unique_id', $conversation_id)
            ->where(function ($query) use ($user_id) {
                $query->whereNull('delete_for');
                $query->orWhereRaw('NOT FIND_IN_SET(?, delete_for)', [$user_id]);
            })
            ->where(function($query) use($user_id){
                $query->where('user1_id',$user_id);
                $query->orWhere('user2_id',$user_id);
            })
            ->orderBy('id', 'desc')
            ->first();
            if(!$chat){
                $viewData = false;
                return  $viewData;
            }

        $draft_message = DraftChatMessage::where("user_id",auth()->user()->id)->where("reference_id",$chat->id)->where("type","individual_chat")->first();
        $viewData['draft_message'] = $draft_message->message??'';
        $viewData['chat'] = $chat;
        $viewData['type'] = 'individual';
        $groupdata = ChatGroup::with([
            'members' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);

            },
            'lastMessage' // Eager load last message
        ])->addSelect([
                'last_message_date' => GroupMessages::query()
                    ->select('created_at')
                    ->whereColumn('group_messages.group_id', 'chat_groups.id')
                    ->latest('created_at')
                    ->limit(1)
            ])
            ->orderBy('last_message_date', 'desc')
            ->get();
        $viewData['groupdata'] = $groupdata;
        $viewData['chat_id'] = $chat_id = NULL;
        //  dd($chat_id);
        $viewData['chat_requests'] = ChatRequest::orderBy('id', "desc")
            ->where(function ($query) use ($chat_id) {
                $query->where('receiver_id', auth()->user()->id)->where('is_accepted', '!=', '1');
            })->with(['sender', 'receiver'])
            ->get();
        $viewData['chat_user_list'] = NULL;
        if ($chat) {
            $viewData['get_chat_user'] = User::where('id', $chat->user2_id)->first();
            $viewData['get_chat_professional'] = User::where('id', $chat->user1_id)->first();
            $chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                ->where(function ($query) {
                    $query->where('user2_id', auth()->user()->id)
                        ->orWhere('user1_id', auth()->user()->id);
                })
                ->get()
                ->sortByDesc(fn($chat) => $chat->lastMessage->created_at ?? null);

            $viewData['chat_user_list'] = $chat_user_list;

            $viewData['chat_id'] = $chat_id = $chat->id;
            $chat_messages =  ChatMessage::withTrashed()->with('sentBy')
            ->where(function ($query) use ($user_id) {
                $query->whereNull('clear_for');
                $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$user_id]);
            })
            ->where('chat_id', $chat->id)->get();
            $viewData['chat_messages'] = $chat_messages;
            if(count($chat_messages) > 0){
                $viewData['chat_empty'] = false;
            }else{
                $viewData['chat_empty'] = true;
            }

        }
        return $viewData;
    }


    public function getConversation($chat_id,Request $request)
    {
        $chat = Chat::where('unique_id', $chat_id)->first();
        $viewData['chat'] = $chat;
        $viewData['get_chat_user'] = User::where('id', $chat->user2_id)->first();
        $viewData['get_chat_professional'] = User::where('id', $chat->user1_id)->first();
        $viewData['type'] = 'individual';
        // $chat_user_list = ChatRequest::where(['is_accepted'=> '1'])->with('receiver')->get();
        $viewData['chat_user_list']=$chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                    ->where(function ($query) {
                        $query->where('user2_id', auth()->user()->id)
                                ->orWhere('user1_id', auth()->user()->id);
                    })
                    ->get()
                    ->sortByDesc(fn($chat) => $chat->lastMessage->created_at ?? null);

        $chat_messages = ChatMessage::withTrashed()->with('sentBy')
                        ->where('chat_id',$chat->id)
                        ->get();
        $viewData['chat_user_list'] =$chat_user_list; 
        $viewData['chat_id'] =$chat->id;
        $viewData['chat_messages'] = $chat_messages;
        $view = View::make('admin-panel.01-message-system.message-centre.get-conversation', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['new_msg'] = true;
        return response()->json($response);
    }

    public function sendMessage($chat_id,Request $request)
    {
        return $this->messageCentreService->sendMessage($chat_id, $request);
    }
    

    public function conversationList(Request $request)
     {

            $chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                ->where(function ($query) {
                    $query->where('user2_id', auth()->user()->id)
                        ->orWhere('user1_id', auth()->user()->id);
                })
                ->get()
                ->sortByDesc(fn($chat) => $chat->lastMessage->created_at ?? null);

            $viewData['chat_user_list'] = $chat_user_list;
            

             $view = View::make('admin-panel.01-message-system.message-centre.chat_sidebar_ajax',$viewData);
             $contents = $view->render();
             $response['contents'] = $contents;
            // dd( response()->json($response));

            return response()->json($response);

            // return view('admin-panel.01-message-system.message-centre.chat_sidebar_ajax',$viewData);
    
       
    }
    public function chatSearch(Request $request)
    {
        $search=$request->search;
        if ($request->ajax()) { 
            $user_id=auth()->user()->id; 

            $viewData['chat_user_list'] = $chat_user_list = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                                    ->where(function ($query) {
                                        $query->where('user2_id', auth()->user()->id)
                                              ->orWhere('user1_id', auth()->user()->id);
                                    })
                                    ->when($search, function ($query) use ($search) {
                                        $query->where(function ($q) use ($search) {
                                            $q->where(function ($q1) use ($search) {
                                                $q1->where('user1_id', auth()->user()->id)
                                                   ->whereHas('chatWith', function ($subQuery) use ($search) {
                                                       $subQuery->where('first_name', 'LIKE', "%{$search}%")
                                                       ->orWhere('last_name', 'LIKE', "%{$search}%");
                                                   });
                                            })
                                            ->orWhere(function ($q2) use ($search) {
                                                $q2->where('user2_id', auth()->user()->id)
                                                   ->whereHas('addedBy', function ($subQuery) use ($search) {
                                                       $subQuery->where('first_name', 'LIKE', "%{$search}%")
                                                       ->orWhere('last_name', 'LIKE', "%{$search}%");;
                                                   });
                                            })
                                            ->orWhereHas('chatMessages', function ($chatMessagesQuery) use ($search) {
                                                $chatMessagesQuery->where('message', 'LIKE', "%{$search}%");
                                            });
                                        });
                                    })
                                    ->get();

            $view = View::make('admin-panel.01-message-system.message-centre.chat_sidebar_ajax', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            return response()->json($response);
        }
    }

    //  public function chatRequests($search_param,Request $request)
    // {
    //     $search=$search_param;
    //     if ($request->ajax()) { 
    //         $user_id=auth()->user()->id; 
            
    //         $view = View::make('admin-panel.chat');
    //         $contents = $view->render();
    //         $response['contents'] = $contents;
    //         return response()->json($response);
    //     }
    // }


    public function fetchChats($chat_id,$last_msg_id,Request $request)
    {
        return $this->messageCentreService->fetchChats($chat_id, $last_msg_id, $request);
    }

    public function fetchOlderChats($chat_id,$first_msg_id,Request $request)
    {
        try {
           $openFrom=$request->openfrom;
            $chat = Chat::where('id', $chat_id)->with(['chatWith','addedBy'])->first();
            if($chat->user1_id != auth()->user()->id){
                $receiver_id = $chat->user1_id;
            }else{
                $receiver_id = $chat->user2_id;
            }
            $userId=auth()->user()->id;
            $viewData['chat'] = $chat;
            $viewData['receiver_id'] = $receiver_id;
            $unread_messages = ChatMessageRead::where('chat_id',$chat->id)
            ->where("receiver_id",auth()->user()->id)
            ->where("status","unread")
            ->get();
            
            
            ChatMessageRead::where('chat_id',$chat->id)->where("receiver_id",auth()->user()->id)->update(['status'=>'read']);
                $msg_ids = array();
                foreach($unread_messages as $msg){
                   $msg_ids[] = $msg->chatMessage->unique_id; 
                }
                if(!empty($msg_ids)){
                    $socket_data = [
                        "action" => "message_read",
                        // "message" => "called from messagecentrecontroller",
                        "chat_id" => $chat->id,
                        "message_id" => implode(",",$msg_ids),
                        "sender_id" => auth()->user()->id,
                    ];
                    initChatSocket($chat_id, $socket_data);
                }
                
                $chat_messages = ChatMessage::withTrashed()
                        ->with('sentBy')
                        ->where('chat_id',$chat_id)
                        ->where(function($query) use($userId){
                            $query->whereNull('clear_for');
                            $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                        })
                        ->where(function($query) use($first_msg_id){
                           $query->where("id","<",$first_msg_id);
                        })
                        ->limit(50) 
                        ->latest()                       
                        ->get();
                        
            $chat_messages = $chat_messages->sortBy('id'); 
            if(count($chat_messages)>0){
                $first_msg_id=$chat_messages[count($chat_messages) - 1]->id??0;
           
            }
            $response['first_msg_id'] = $first_msg_id;
            $response['message_id'] = $chat_messages[0]->unique_id??0;
            $last_read_message_id =  $last_read_message->message_id??0;
            $response['last_read_message'] = $last_read_message_id;
            $viewData['chat_messages'] = $chat_messages;
            $viewData['openfrom'] = $openFrom;
            $viewData['chat_id'] = $chat_id;

            if(!empty($msg_ids) && $first_msg_id == 0){
                $viewData['unread_from_id'] = $msg_ids[0];
            }
            $view = View::make('admin-panel.01-message-system.message-centre.chat_ajax', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['new_msg'] = true;
          
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
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['err_message'] = $e->getMessage();
            $response['err_line'] = $e->getLine(); // Add the line number

            return response()->json($response);
        }
    }
    


    /**
     * Remove the specified setting from the database.
     *
     * @param string $id The unique id of the setting.
     * @return \Illuminate\Http\RedirectResponse
     */
    

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
            blockChat($chat->blocked_by,$blockedUserId,$chat->id,'blocked');
            return response()->json('Chat Blocked Successfully');
    
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

            blockChat($blockedBy,$blockedUserId,$chat->id,'unblocked');
            return response()->json('Chat Unblocked Successfully');
    
    }
    
    public function clearChatForUser($chat_id,Request $request)
    {
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
    }
            

    public function deleteChatForUser($chat_id,Request $request)
    {
        $userId = auth()->user()->id;
        $chat = Chat::findOrFail($chat_id);
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
        // Call the model method to delete the chat and its dependencies
        $responseMessage = $chat->deleteChatForAll($userId);
        return response()->json($responseMessage);
    }
   
    public function deleteChatMessage($msg_id,Request $request)
    {
     
        $userId = auth()->user()->id;
        $chatMessage = ChatMessage::findOrFail($msg_id);
    
        if ($request->has('files')){
            $attachments = !empty($chatMessage->attachment) ? explode(',', $chatMessage->attachment) : [];
            $filesToDelete = $request->input('files'); 
         
            $filteredAttachments = array_filter($attachments, function ($file) use ($filesToDelete) {
                return !in_array(trim($file), $filesToDelete);
            });
         
            if (empty($filteredAttachments)) {
                // If no attachments remain, delete the message for the user
                $responseMessage = $chatMessage->deleteForUser($userId);
                return response()->json($responseMessage);
            }

            $chatMessage->attachment = implode(',', $filteredAttachments);
            $chatMessage->save();
            return response()->json([
                'message' => 'Selected file(s) deleted successfully.',
                'success' => true,
                'message_id' => $chatMessage->unique_id,
                'attachments' => $filesToDelete
            ]);
        }else
        {
            $socket_data = [
                "action" => "delete_msg_for_me",
                "message_id" => $msg_id,
                "messageUniqueId" => $chatMessage->unique_id,
                "chat_id" => $chatMessage->chat_id,
                "sender_id" => $userId
            ];
            initChatSocket($chatMessage->chat_id, $socket_data);
             // Call the model's method
            $responseMessage = $chatMessage->deleteForUser($userId);
            return response()->json($responseMessage);
        }
    }
    public function deleteChatMessageforBoth($msg_id,Request $request)
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
      
        return response()->json('Message Deleted Successfully');
    }


    public function updateTypingStatus(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $isTyping = $request->input('is_typing'); // 1 = typing, 0 = not typing
        $userId = auth()->id();
       // dd($isTyping);
        $receiver_id = 0;
        $chat = Chat::findOrFail($request->chat_id);
        if ($chat->user1_id == $userId) {
            $chat->user1_typing= $isTyping;
            $receiver_id = $chat->user2_id;
        } elseif ($chat->user2_id == $userId) {
            $chat->user2_typing= $isTyping;
            $receiver_id = $chat->user1_id;
        }
        $chat->save();
        $socket_data = [
            "action" => "user_typing",
            "message" => "",
            "chat_id" => $chat_id,
            "receiver_id" => $receiver_id,
            "isTyping" => $isTyping,
            "sender_id" => auth()->user()->id,
        ];
        initChatSocket($chat_id, $socket_data);
        return response()->json(['success' => true]);
    }

    public function fetchTypingStatus($chat_id,Request $request)
    {
        
        $userId=auth()->user()->id;
        $chat = Chat::find($chat_id);
        if ($chat->user1_id == $userId) {
            $typingStatus = $chat->user2_typing ;
            $typing_user = $chat->user2_typing ? $chat->chatWith->first_name . ' is typing...' : null;

        } elseif ($chat->user2_id == $userId) {
            $typingStatus = $chat->user1_typing ;
            $typing_user = $chat->user1_typing ? $chat->addedBy->first_name . ' is typing...' : null;
        }

        return response()->json([
            'is_typing' => $typingStatus,
            'user' => $typing_user
        ]);
    }
    
    public function searchChatMessages(Request $request)
    {
        $search = $request->input('search');
        $chatId = $request->input('chat_id');
        $startOfLastWeek = Carbon::now()->subWeek();
        $now = Carbon::now();
        $chat =  Chat::where('id', $chatId)->with(['chatWith','addedBy'])->first();
        if($chat->user1_id != auth()->user()->id){
            $receiver_id = $chat->user1_id;
        }else{
            $receiver_id = $chat->user2_id;
        }
        $userId=auth()->user()->id;
        $viewData['chat'] = $chat;
        $viewData['receiver_id'] = $receiver_id;
        $viewData['chat_id'] = $chat->id;
        $viewData['openfrom'] = $request->openfrom;

        $chat_msgs = ChatMessage::where('chat_id', $chatId)
                    ->with('sentBy') // Eager load the 'sentBy' relationship
                    ->when($search != "", function ($query) use ($search) {
                        $query->where('message', 'LIKE', "%{$search}%");
                    }) // Add search condition if $search is not empty
                    ->where(function ($query) use ($userId) {
                        $query->whereNull('clear_for')
                            ->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                    }) // Handle clear_for logic
                    ->whereBetween('created_at', [$startOfLastWeek, $now]) // Filter by date range
                    ->get(); // Fetch the results

        $viewData['chat_messages'] = $chat_msgs; // Assign results to $viewData
        $view = View::make('admin-panel.01-message-system.message-centre.chat_ajax', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }


    public function trackMessages(Request $request)
    {
        try {
            $chats = Chat::where('user1_id',auth()->user()->id)->orWhere("user2_id",auth()->user()->id)->pluck("id");
            $chat_ids = array(); 
            if(!empty($chats)){
                $chat_ids = $chats->toArray();
            }
            $new_msg = false;
            $message = '';
            if(!empty($chat_ids)){
                $chat_message = ChatMessage::withTrashed()->whereIn("chat_id",$chat_ids)
                ->where("sent_by","!=",auth()->user()->id)
                ->whereHas("chatMessageRead",function($query){
                    $query->where("status","unread");
                })
                ->latest()
                ->first();
                if(!empty($chat_message)){
                    if(!\Session::get("last_chat_msg")){
                        // $new_msg = true;
                        // $message = $chat_message->sentBy->first_name." ".$chat_message->sentBy->last_name;
                        \Session::put("last_chat_msg",$chat_message->id);
                    }else{
                        if(\Session::get("last_chat_msg") != $chat_message->id){
                            $new_msg = true;
                            $message = $chat_message->sentBy->first_name." ".$chat_message->sentBy->last_name. " sent new message!";
                            \Session::put("last_chat_msg",$chat_message->id);
                        }
                    }
                }else{
                    \Session::put("last_chat_msg",0);
                }
            }
            if($new_msg){
                $response['message'] = $message;
            }
            
            $response['status'] = true;
            $response['new_msg'] = $new_msg;
            return response()->json($response);
        
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
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
        $message_id = $request->message_id;
        $message_reaction =  MessageCentreReaction::where("unique_id",$message_id)->first();
        MessageCentreReaction::where("unique_id",$message_id)->where('added_by',auth()->user()->id)->delete();
        $response['status'] = true;
        messageReaction($message_id,'','',$message_reaction->message->chat_id,"remove_reaction");
        return response()->json($response);
    }



    public function refreshChatList(Request $request){
        $viewData['chatUsersList'] = chatUsersList();
        $contents = view("components.partials.chat-user-list",$viewData)->render();
        $response['status'] = true;
        $response['count'] = count($viewData['chatUsersList']);

        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function chatRequests(Request $request){
        $viewData['chat_requests'] = ChatRequest::orderBy('id', "desc")
                                    ->where(function($query) {
                                        $query->where('receiver_id',auth()->user()->id)->where('is_accepted','!=','1');
                                    })->with(['sender','receiver'])
                                    ->get();
        
        $contents = view("admin-panel.01-message-system.message-centre.chat-requests",$viewData)->render();
        $response['status'] = true;
        $response['request_count'] = count($viewData['chat_requests']);

        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function previewFile(Request $request){
        $viewData['file_name'] = $request->file_name;
        $viewData['chatMessage'] =  ChatMessage::where("unique_id",$request->chat_id)->first();
      
        $viewData['fileUrl'] = chatDirUrl($request->file_name,'r');
        $view = view("admin-panel.01-message-system.message-centre.preview-file",$viewData);
        
        $response['status'] = true;
        $response['contents'] = $view->render();

        return response()->json($response);
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

    public function saveDraftMessage(Request $request){
        DraftChatMessage::updateOrCreate([
            'reference_id'=>$request->input("chat_id"),
            'user_id'=>auth()->user()->id,
            'type'=>"individual_chat"
        ],
        [
            'message'=> $request->input("message")
        ]);

        $response['status'] = true;

        return response()->json($response);
        
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
        $view = view("admin-panel.01-message-system.message-centre.chatbot.chatbot",$viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function deleteSelectedAttachments($msg_id,Request $request)
    {
    
        $userId = auth()->user()->id;
        $chatMessage = ChatMessage::where("unique_id",$msg_id)->first();

        $chat = Chat::where("id",$chatMessage->chat_id)->first();
        
        $attachments = !empty($chatMessage->attachment) ? explode(',', $chatMessage->attachment) : [];
        $filenameToDelete = trim($request->filename);
     
        // Remove only the selected file
        $filteredAttachments = array_filter($attachments, function ($file) use ($filenameToDelete) {
            return $file !== $filenameToDelete;
        });

        $path = chatDirUrl($request->filename);
       $api_response = mediaDeleteApi($path,$request->filename);
        

        // If no attachments remain, delete the entire message
        if (empty($filteredAttachments)) {
            $chatMessage->delete();
             $socket_data = [
            "action" => "delete_selected_attachments",
            "messageUniqueId" => $msg_id,
            "attachments" => [],
            ];
            initChatSocket($chat->id, $socket_data);

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
        initChatSocket($chat->id, $socket_data);

        return response()->json([
            'status' => true,
            'message' => 'Selected files deleted successfully.',
            'attachments' => $filenameToDelete
        ]);
  
    
    }
    
    public function getReactedMsg($chat_id,$message_uid,Request $request)
    {
        $userId=auth()->user()->id;
        $chat_messages = ChatMessage::withTrashed()->with('sentBy')
                    ->where('chat_id',$chat_id)
                    ->where('unique_id',$message_uid)
                    ->where(function($query) use($userId){
                            $query->whereNull('clear_for');
                            $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                    })
                    ->first();
            $chat_messages = $chat_messages ? collect([$chat_messages]) : collect([]);
            $viewData['chat_messages'] = $chat_messages;
            $viewData['chat_id'] = $chat_id;
            $viewData['msg_type'] = 'reaction_msg';
            $view = View::make('admin-panel.01-message-system.message-centre.chat_ajax', $viewData);
            $contents = $view->render();
            $response['msg_type'] = 'reaction_msg';
            $response['contents'] = $contents;
            $response['messageUniqueId'] = $message_uid;
            return response()->json($response);
      
    }

    public function checkChatExists(Request $request){
        $chat_id = $request->chat_id;
        $user_id = $request->user_id;
        $count = Chat::where("id",$chat_id)
                ->where(function($query) use($user_id){
                    $query->where("user1_id",$user_id);
                    $query->orWhere("user2_id",$user_id);
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

    public function getUserProfile($chat_id)
    {
        $user_id=auth()->user()->id;
        $chat = Chat::where('id',$chat_id)->first();

        if($chat){
            $viewData['get_chat_user'] = User::where('id', $chat->user2_id)->first();
            $viewData['get_chat_professional'] = User::where('id', $chat->user1_id)->first();
             $viewData['chat_id'] = $chat->id;
        }else{
            $viewData['chat_id'] = NULL;
        }

        $view = View::make('admin-panel.01-message-system.message-centre.user-profile-sidebar', $viewData);
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

        $view = View::make('admin-panel.01-message-system.message-centre.share-file-sidebar', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }
}