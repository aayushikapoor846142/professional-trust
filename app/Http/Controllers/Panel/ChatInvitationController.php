<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatInvitation;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\ChatRequest;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\StaffUser;

class ChatInvitationController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }


    public function index()
    {
        $viewData['pageTitle'] = "Chat Invitations";
        return view('admin-panel.01-message-system.chat-invitations.lists', $viewData);
    }


    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input("status");
         $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $records = ChatInvitation::where(function ($query) use ($search,$status) {
            if ($search != '') {
                $query->where("name", "LIKE", "%" . $search . "%");
            }
            if($status != ''){
                $query->where("status",$status);
            }
            $query->where('added_by', \Auth::user()->id);
        })
        ->orderBy($sortColumn, $sortDirection)
        ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.01-message-system.chat-invitations.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }


    public function add()
    {
        $viewData['pageTitle'] = "Send Chat Invitation";
        $staff_users = StaffUser::with('user')->where('user_id',"!=",auth()->user()->id)
        ->whereHas("user",function($query){
            $query->where("status","active");
        })
        ->get()->pluck('user');
        $admin_users = User::where('role','admin')->where('id',"!=",auth()->user()->id)->where('status','active')->get();
        $all_users = $staff_users->merge($admin_users);
        foreach($all_users as $key => $user){
            $flag = 0;
            $chat_invite = ChatInvitation::where(function ($query) use ($user) {
                $query->where('added_by', $user->id)
                ->where('email', auth()->user()->email);
            })->orWhere(function ($query) use ($user) {
                $query->where('email', $user->email)
                ->where('added_by', auth()->user()->id);
            })->first();
            if($chat_invite){
                $flag = 1;
            }

            $chat_request = ChatRequest::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->user()->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                ->where('sender_id', auth()->user()->id);
            })->first();
            if($chat_request){
                $flag = 1;
            }

            $chat = Chat::where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                ->where('user2_id', auth()->user()->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('user2_id', $user->id)
                ->where('user1_id', auth()->user()->id);
            })->first();
            if($chat){
                $flag = 1;
            }

            if($flag == 1){
                unset($all_users[$key]);
            }
        }
        $viewData['all_users'] = $all_users;
        $view = view('admin-panel.01-message-system.chat-invitations.add', $viewData);
        $content = $view->render();

        $response['status'] = true;
        $response['contents'] = $content;

        return response()->json($response);
    }


    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255',  'valid_email'],
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

            $token = Str::random(64);
            $email = $request->input('email');
            // Check if email exists in the users table
            $user = User::where('email', $email)->first();
            $checkInvite= ChatInvitation::where('added_by',\Auth::user()->id)->where('email' , $email)->count();
           
            if($checkInvite==0 ){

            if($user) {
                $checkInviteReverse= ChatInvitation::where('email',\Auth::user()->email)->where('added_by' , $user->id)->count();
                if($checkInviteReverse==0){
                ChatInvitation::create([
                    'email' => $email,
                    'token' => $token,
                    'added_by' => \Auth::user()->id,
                ]);
                $chatRequest = ChatRequest::where('sender_id', \Auth::user()->id)->where('receiver_id', $user->id)->count();
                $chatRequestOther = ChatRequest::where('receiver_id', \Auth::user()->id)->where('sender_id', $user->id)->count();
                if($chatRequest < 1 && $chatRequestOther < 1){
                    ChatRequest::create([
                        'unique_id' => randomNumber(),
                        'sender_id' => \Auth::user()->id,
                        'receiver_id' => $user->id,
                        'is_accepted' => 0,
                    ]);
                }

                $mailData['professional_name'] = $user->first_name . " " . $user->last_name;
                $mailData['sender_name'] = auth()->user()->first_name . " " . auth()->user()->last_name;
                $mail_message = \View::make('emails.chat_request_professional', $mailData);

                $mailData['mail_message'] = $mail_message;
                $parameter['to'] = $user->email;
                $parameter['to_name'] = $user->first_name . " " . $user->last_name;
                $parameter['message'] = $mail_message;
                $parameter['subject'] = "Received a Chat Request";
                $parameter['view'] = "emails.chat_request_professional";
                $parameter['data'] = $mailData;
                $mailRes = sendMail($parameter);
                $socket_data = [
                        "action" => "new_chat_request",
                        "receiver_id" => $user->id,
                        "count" => chatReqstCount($user->id),
                    ];
                initUserSocket($user->id, $socket_data);
                $response['status'] = true;
                $response['redirect_back'] = baseUrl('connections/invitations');
                $response['message'] = "Invitation Sent successfully";
             

                }else{
                      $response['status'] = false;
                      $response['message'] = "Invite Already Received";
                }

           
            } else {
              ChatInvitation::create([
                    'email' => $email,
                    'token' => $token,
                    'added_by' => \Auth::user()->id,
                ]);
            
                $mailData = ['token' => $token,'user'=>\Auth::user()->first_name.' '. \Auth::user()->last_name];
                $view = \View::make('emails.chat-invitations', $mailData);
                $message = $view->render();
                $parameter = [
                    'to' => $email,
                    'message' => $message,
                    'subject' => 'Invitation for Chat',
                    'view' => 'emails.chat-invitations',
                    'data' => $mailData,
                ];
                sendMail($parameter);
               

                $response['status'] = true;
                $response['redirect_back'] = baseUrl('connections/invitations');
                $response['message'] = "Invitation Sent successfully to a new user.";
           
           }
         }else{
                $response['status'] = false;
                $response['message'] = "Invite Already Sent";

            }
            return response()->json($response);
        } catch (\Exception $e) {
            // Optionally log the error: \Log::error($e);
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function deleteSingle($id)
    {
        $chatInvitation = ChatInvitation::where('unique_id', $id)->first();

        if (!$chatInvitation) {
            return redirect()->back()->with("error", "Record not found.");
        }

        try {
            ChatInvitation::deleteRecord($chatInvitation->id);
            return redirect()->back()->with("success", "Record deleted Successfully");
        } catch (\Exception $e) {
            // Optionally log the error: \Log::error($e);
            return redirect()->back()->with("error", "Failed to delete record.");
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        $notFound = [];
        $deleted = 0;

        foreach ($ids as $uniqueId) {
            $act = ChatInvitation::where('unique_id', $uniqueId)->first();
            if ($act) {
                try {
                    ChatInvitation::deleteRecord($act->id);
                    $deleted++;
                } catch (\Exception $e) {
                    // Optionally log the error
                }
            } else {
                $notFound[] = $uniqueId;
            }
        }

        $response['status'] = true;
        $response['deleted'] = $deleted;
        $response['not_found'] = $notFound;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

}
