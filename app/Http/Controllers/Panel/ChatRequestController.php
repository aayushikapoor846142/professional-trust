<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\ChatRequest;
use App\Models\ChatInvitation;
use App\Models\Chat;
use App\Models\User;
use App\Models\FeedsConnection;
use DB;
use App\Services\ChatRequestService;

class ChatRequestController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of settings.
     *
     * @return \Illuminate\View\View
     */
 
 	public function index()
    {
        $viewData['pageTitle'] = "Chat Request";
        return view('admin-panel.01-message-system.chat_request.lists', $viewData);
    }


    /**
     * Get the professionals list via AJAX with search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input("status");
        $records = ChatRequest::orderBy('id', "desc")
            ->where(function($query) use($search,$status) {
               $query->where('receiver_id','=',auth()->user()->id);
            })->with(['sender','receiver'])
            ->paginate();
       
        $viewData['records'] = $records;
        $view = View::make('admin-panel.01-message-system.chat_request.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();

        return response()->json($response);
    }

    public function sendChatRequest($receiver_id)
    {
        $professional = User::where('unique_id', $receiver_id)->firstOrFail();
        $service = new ChatRequestService();
        $data = $service->sendChatRequest(auth()->user(), $professional);
        $viewData['professional'] = $professional;
        $viewData['record'] = $data;
        return redirect()->back()->with("success", "Request Sent Successfully");
    }

   
    public function declineChatRequest($chat_request_id)
    {
        $chat_req = ChatRequest::where("unique_id", $chat_request_id)->firstOrFail();
        $chat_req_user = User::where("id", $chat_req->receiver_id)->firstOrFail();
        $service = new ChatRequestService();
        $service->declineChatRequest($chat_req, auth()->user());
        $socket_data = [
            "action" => "new_chat_request",
            "receiver_id" => $chat_req->receiver_id,
            "count" => chatReqstCount($chat_req->receiver_id),
        ];
        initUserSocket($chat_req->receiver_id, $socket_data);
        if(request()->ajax()){
            $response['status'] = true;
            $response['unique_id'] = $chat_request_id;
            $response['message'] = "Request Declined Successfully";
            return response()->json($response);
        }else{
            return redirect()->back()->with("success", "Request Declined Successfully");
        }
    }
    public function acceptChatRequest($chat_request_id)
    {
        $chat_req = ChatRequest::where("unique_id", $chat_request_id)->firstOrFail();
        $service = new ChatRequestService();
        $userId = auth()->user()->id;
        $chat = $service->acceptChatRequest($chat_req, auth()->user());
        $getReceiver = auth()->user();
        $arr = [
            'comment' =>'*'.$getReceiver->first_name." ".$getReceiver->last_name.'* has accepted Chat Request.',
            'type' =>'group_chat',
            'redirect_link' => null,
            'is_read' => 0,
            'user_id' =>$chat_req->sender_id ?? '',
            'send_by' => $userId ?? '',
        ];
        chatNotification($arr);
        $socket_data = [
            "action" => "new_chat_request",
            "receiver_id" => $chat_req->receiver_id,
            "count" => chatReqstCount($chat_req->receiver_id),
        ];
        initUserSocket($chat_req->receiver_id, $socket_data);
        $response['status'] = true;
        $response['unique_id'] = $chat_request_id;
        $response['redirect_back'] = baseUrl('message-centre/chat/'.$chat->unique_id);
        $response['message'] = "Request Accepted Successfully";
        return response()->json($response);
    }

    /**
     * Update the specified setting in the database.
     *
     * @param string $id The unique id of the setting to edit.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function update($unique_id, Request $request)
    {
        $object = MessageSettings::where('unique_id', $unique_id)->first();
        $validator = Validator::make($request->all(), [
            'settings' => 'required',
          
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = [];

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        
       MessageSettings::updateOrCreate(
        ['unique_id' => $unique_id],
        // Attributes to update or create
        [
            'settings' => $request->input('settings'),
            'user_id' => auth()->user()->id,     
        ]
    );

     
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('message-settings');
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }
    
    
    /**
     * Remove the specified setting from the database.
     *
     * @param string $id The unique id of the setting.
     * @return \Illuminate\Http\RedirectResponse
     */
    
}