<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use Auth;
use App\Models\ChatRequest;
use App\Models\ChatGroup;
use App\Models\StaffUser;
use App\Models\GroupMessages;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\ChatInvitation;
use App\Models\FeedsConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\FeatureCheckService;

class ConnectController extends Controller
{
    
    protected $featureCheckService;
    
    public function __construct()
    {
        $this->featureCheckService = new FeatureCheckService();
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $viewData['chat_requests'] = ChatRequest::orderBy('id', "desc")
            ->where(function ($query) {
                $query->where('receiver_id', auth()->user()->id)->where('is_accepted', '!=', '1');
            })
            ->with(['sender', 'receiver'])
            ->get();
        $viewData['pending_user_list'] = null;
        $viewData['chat_user_list'] = null;
        $viewData['connected_user_list'] = null;
        $viewData['groupdata'] = $this->getUserGroupsWithLastMessage($user_id);
        $viewData['pageTitle'] = "Connect";

        $connectionFeatureStatus = $this->featureCheckService->canAddConnection();
        
        $viewData['connectionFeatureStatus'] = $connectionFeatureStatus;
        $viewData['canAddConnection'] = $connectionFeatureStatus['allowed'];

        return view('admin-panel.01-message-system.connect.connect', $viewData);
    }

    /**
     * Get the user's chat groups with last message date and eager loaded members.
     *
     * @param int $user_id
     * @return \Illuminate\Support\Collection
     */
    private function getUserGroupsWithLastMessage($user_id)
    {
        return ChatGroup::with([
            'members' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            },
            'lastMessage'
        ])
            ->addSelect(['last_message_date' => GroupMessages::query()
                ->select('created_at')
                ->whereColumn('group_messages.group_id', 'chat_groups.id')
                ->latest('created_at')
                ->limit(1)
            ])
            ->orderBy('last_message_date', 'desc')
            ->get();
    }

    /**
     * Get connected users based on type.
     *
     * @param int $userId
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getConnectedUsers($userId, $type = 'following')
    {
        if ($type == 'followers') {
            return FeedsConnection::where('connection_with', $userId)->get();
        }
        return FeedsConnection::where('user_id', $userId)->get();
    }

    /**
     * Get pending user IDs for a user.
     *
     * @param int $userId
     * @return array
     */
    private function getPendingUserIds($userId)
    {
        return ChatRequest::where('sender_id', $userId)
            ->where('is_accepted', 0)
            ->pluck('receiver_id')
            ->toArray();
    }

    /**
     * Get pending users for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingUsers($userId)
    {
        $pendingUserIds = $this->getPendingUserIds($userId);
        return User::select('id', 'first_name', 'last_name', 'email')
            ->whereIn('id', $pendingUserIds)
            ->get();
    }

    /**
     * Render the connection list sidebar via AJAX.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function connectionList(Request $request)
    {
        $pending_user = $this->getPendingUserIds(auth()->user()->id);
        $viewData['connected_user_list'] = $this->getConnectedUsers(auth()->user()->id, $request->type);
        $viewData['pending_user_list'] = User::whereIn('id', $pending_user)->get();
        $viewData['type'] = $request->type;
        $view = View::make('admin-panel.01-message-system.connect.connect_sidebar_ajax', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    /**
     * Render the pending connection list sidebar via AJAX.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pendingConnectionList(Request $request)
    {
        $viewData['pending_user_list'] = $this->getPendingUsers(auth()->user()->id);
        $view = View::make('admin-panel.01-message-system.connect.connect_pending_sidebar', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    /**
     * Get the connect user list with search and pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function connectUserList(Request $request)
    {
        $userId = auth()->user()->id;
        $professionalId = StaffUser::where('user_id', $userId)->value('added_by');
        $search = $request->search;

        $recordsA = User::where(function ($query) use ($userId, $professionalId) {
            $query->where('added_by', $userId);
            if ($professionalId) {
                $query->orWhere('added_by', $professionalId);
            }
        })
            ->where('id', '!=', $userId)
            ->select('id', 'first_name', 'last_name', 'email', 'role', 'status', 'added_by')
            ->get();

        $recordsB = User::where('id', '!=', $userId)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where("first_name", "LIKE", "%$search%")
                        ->orWhere("last_name", "LIKE", "%$search%")
                        ->orWhere("email", "LIKE", "%$search%");
                });
            })
            ->where("status", "active")
            ->whereIn('role', ['professional', 'associate', 'client'])
            ->orderByDesc('id')
            ->select('id', 'first_name', 'last_name', 'email', 'role', 'status', 'added_by')
            ->get();

        $merged = $recordsA->merge($recordsB)->unique('id')->sortByDesc('id')->values();
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $merged->forPage($currentPage, $perPage);
        $paginated = new LengthAwarePaginator(
            $currentItems,
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        $viewData['userList'] = $paginated;
        if (auth()->user()->role != "professional") {
            $viewData['userList'] = User::where(function ($query) use ($professionalId) {
                $query->where('added_by', auth()->user()->id)
                    ->orWhere('added_by', $professionalId)
                    ->orWhere('id', $professionalId);
            })
                ->where('id', '!=', auth()->user()->id)
                ->select('id', 'first_name', 'last_name', 'email', 'role', 'status', 'added_by')
                ->paginate(15);
        }
        $view = View::make('admin-panel.01-message-system.connect.connect_ajax', $viewData);
        $contents = $view->render();
        $response = [
            'contents' => $contents,
            'last_page' => $paginated->lastPage(),
            'current_page' => $paginated->currentPage(),
            'total_records' => $paginated->total(),
        ];
        return response()->json($response);
    }

    /**
     * Remove a connection invitation and related chat requests.
     *
     * @param string $invitation_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function removeConnection($invitation_id)
    {
        $checkInvite = ChatInvitation::where('unique_id', $invitation_id)->first();
        if ($checkInvite && $checkInvite->status == 0) {
            ChatInvitation::where('unique_id', $invitation_id)->delete();
            $user = User::where("email", $checkInvite->email)->first();
            if ($user) {
                ChatRequest::where('sender_id', \Auth::user()->id)->where('receiver_id', $user->id)->delete();
                ChatRequest::where('receiver_id', \Auth::user()->id)->where('sender_id', $user->id)->delete();
                $this->removeUserConnectionHelper($user->id, auth()->user()->id);

               $this->featureCheckService->deleteConnectFeatureHistory(
                    'connections',
                    Auth::user()->id,
                    $checkInvite->id
                );
            }
            $status = true;
            $message = 'Invitation request is removed';
        } else {
            $status = false;
            $message = 'Cannot remove the invitation';
        }
        if (request()->ajax()) {
            $response['status'] = $status;
            $response['message'] = $message;
            return response()->json($response);
        } else {
            if ($status) {
                return redirect()->back()->with("success", $message);
            } else {
                return redirect()->back()->with("error", $message);
            }
        }
    }

    /**
     * Send a connection invitation to a user.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendConnection($user_id)
    {
        $user = User::where('id', $user_id)->first();
        $token = Str::random(64);
        $email = $user ? $user->email : null;
        $checkInvite = ChatInvitation::where('added_by', \Auth::user()->id)->where('email', $email)->count();
        if ($checkInvite == 0) {
            if ($user) {
                $checkInviteReverse = ChatInvitation::where('email', \Auth::user()->email)->where('added_by', $user->id)->count();
                if ($checkInviteReverse == 0) {
                    $chatInvite = ChatInvitation::create([
                        'email' => $email,
                        'token' => $token,
                        'added_by' => \Auth::user()->id,
                    ]);

                    $result = $this->featureCheckService->savePlanFeature(
                        'connections', 
                        Auth::user()->id, 
                        1, // action type: add
                        1, // count: 1 staff member
                        [
                            'user_id' => $chatInvite->id,
                            'email' => $chatInvite->email,
                        ]
                    );

                    $chatRequest = ChatRequest::where('sender_id', \Auth::user()->id)->where('receiver_id', $user->id)->count();
                    $chatRequestOther = ChatRequest::where('receiver_id', \Auth::user()->id)->where('sender_id', $user->id)->count();
                    if ($chatRequest < 1 && $chatRequestOther < 1) {
                        $chatInvite = ChatRequest::create([
                            'unique_id' => $this->randomNumberHelper(),
                            'sender_id' => \Auth::user()->id,
                            'receiver_id' => $user->id,
                            'is_accepted' => 0,
                        ]);

                        $result = $this->featureCheckService->savePlanFeature(
                            'connections', 
                            Auth::user()->id, 
                            1, // action type: add
                            1, // count: 1 staff member
                            [
                                'user_id' => $chatInvite->id,
                                'email' => $chatInvite->email,
                            ]
                        );
                    }
                    $sockett_data = [
                        "action" => "new_chat_request",
                        "receiver_id" => $user->id,
                        "count" => $this->chatReqstCountHelper($user->id),
                    ];
                    $this->initUserSocketHelper($user->id, $sockett_data);
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
                    $this->sendMailHelper($parameter);
                    $this->sendChatNotification($user, 'invite_request', auth()->user()->first_name . " " . auth()->user()->last_name . '   has sent you a connection request ');
                    $response['status'] = true;
                    $response['redirect_back'] = baseUrl('connect');
                    $response['message'] = "Connection Sent successfully";
                } else {
                    $response['status'] = false;
                    $response['message'] = "Connection Already Received";
                }
            } else {
                $chatInvite = ChatInvitation::create([
                    'email' => $email,
                    'token' => $token,
                    'added_by' => \Auth::user()->id,
                ]);

                $result = $this->featureCheckService->savePlanFeature(
                    'connections', 
                    Auth::user()->id, 
                    1, // action type: add
                    1, // count: 1 staff member
                    [
                        'user_id' => $chatInvite->id,
                        'email' => $chatInvite->email,
                    ]
                );

                $mailData = ['token' => $token, 'user' => \Auth::user()->first_name . ' ' . \Auth::user()->last_name];
                $view = \View::make('emails.chat-invitations', $mailData);
                $message = $view->render();
                $parameter = [
                    'to' => $email,
                    'message' => $message,
                    'subject' => 'Invitation for Chat',
                    'view' => 'emails.chat-invitations',
                    'data' => $mailData,
                ];
                $this->sendMailHelper($parameter);
                $response['status'] = true;
                $response['redirect_back'] = baseUrl('connect');
                $response['message'] = "Connection Sent successfully to a new user.";
            }
        } else {
            $response['status'] = false;
            $response['message'] = "Connection Already Sent";
        }
        return response()->json($response);
    }

    /**
     * Follow a user.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow($user_id)
    {
        auth()->user()->following()->syncWithoutDetaching([
            $user_id => ['unique_id' => $this->randomNumberHelper()]
        ]);
        FeedsConnection::create([
            'unique_id' => $this->randomNumberHelper(),
            'connection_with' => $user_id,
            'user_id' => auth()->user()->id,
            'connection_type' => 'follow',
            'status' => 'active'
        ]);
        $response['status'] = true;
        $response['message'] = "You are now following";
        return response()->json($response);
    }

    /**
     * Unfollow a user.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow($user_id)
    {
        auth()->user()->following()->detach($user_id);
        FeedsConnection::where('user_id', auth()->user()->id)->where('connection_with', $user_id)->delete();
        $response['status'] = true;
        $response['message'] = "You are now following";
        return response()->json($response);
    }

    /**
     * Remove a connection or follower.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, $user_id)
    {
        $feed = FeedsConnection::where('user_id', auth()->user()->id)->where('connection_with', $user_id)->first();
        if (!empty($feed)) {
            DB::table('follows')->where('follower_id', $user_id)->where('followee_id', auth()->user()->id)->delete();
            $feed->delete();
        }
        if ($request->remove_connection == "yes") {
            $this->removeUserConnectionHelper($user_id, auth()->user()->id);
        }
        $response['status'] = true;
        $response['message'] = "Connection removed";
        return response()->json($response);
    }

    /**
     * Follow back a user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followBack(Request $request)
    {
        Follow::updateOrCreate([
            'follower_id' => $request->user_id,
            'followee_id' => auth()->user()->id,
        ]);
        FeedsConnection::updateOrCreate([
            'connection_with' => $request->user_id,
            'user_id' => auth()->user()->id,
            'connection_type' => 'follow',
        ], [
            'status' => 'active'
        ]);
        $this->sendChatNotification((object)['id' => $request->user_id], 'invite_request', auth()->user()->first_name . " " . auth()->user()->last_name . '   has started following you now ');
        $this->checkUserConnectionHelper($request->user_id, auth()->user()->id, 'follow');
        $response['status'] = true;
        $response['message'] = "You are now following";
        return response()->json($response);
    }

    /**
     * Remove a user from followers.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromFollowers($user_id)
    {
        $feed = FeedsConnection::where('user_id', $user_id)->where('connection_with', auth()->user()->id)->first();
        if (!empty($feed)) {
            auth()->user()->following()->detach($user_id);
            $feed->delete();
        }
        $response['status'] = true;
        $response['message'] = "Removed from followers";
        return response()->json($response);
    }

    // --- Private Helper Methods for Reusability ---

    /**
     * Send a chat notification.
     *
     * @param object $user
     * @param string $type
     * @param string $comment
     * @return void
     */
    private function sendChatNotification($user, $type, $comment)
    {
        $arr_reply = [
            'comment' => $comment,
            'type' => $type,
            'redirect_link' => null,
            'is_read' => 0,
            'user_id' => $user->id ?? '',
            'send_by' => auth()->user()->id ?? '',
        ];
        chatNotification(arr: $arr_reply);
    }

    /**
     * Remove user connection using helper.
     *
     * @param int $userId
     * @param int $authUserId
     * @return void
     */
    private function removeUserConnectionHelper($userId, $authUserId)
    {
        removeUserConnection($userId, $authUserId);
    }

    /**
     * Send mail using helper.
     *
     * @param array $parameter
     * @return void
     */
    private function sendMailHelper($parameter)
    {
        sendMail($parameter);
    }

    /**
     * Call chat request count helper.
     *
     * @param int $userId
     * @return mixed
     */
    private function chatReqstCountHelper($userId)
    {
        return chatReqstCount($userId);
    }

    /**
     * Call random number helper.
     *
     * @return mixed
     */
    private function randomNumberHelper()
    {
        return randomNumber();
    }

    /**
     * Call init user socket helper.
     *
     * @param int $userId
     * @param array $sockett_data
     * @return void
     */
    private function initUserSocketHelper($userId, $sockett_data)
    {
        initUserSocket($userId, $sockett_data);
    }

    /**
     * Call check user connection helper.
     *
     * @param int $userId
     * @param int $authUserId
     * @param string $type
     * @return void
     */
    private function checkUserConnectionHelper($userId, $authUserId, $type)
    {
        checkUserConnection($userId, $authUserId, $type);
    }
}