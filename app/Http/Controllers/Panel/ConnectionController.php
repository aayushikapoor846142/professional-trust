<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use DB;
use App\Models\UserConnection;

class ConnectionController extends Controller
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
 
    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $viewData['pageTitle'] = "Connections";
            return view('admin-panel.04-profile.user-connections.lists', $viewData);
        } catch (\Exception $e) {
            \Log::error('Error loading connections view: ' . $e->getMessage());
            abort(500, 'Unable to load the connections page.');
        }
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        try {
            $loginUserId = auth()->id();

            $records = UserConnection::where(function ($query) use ($loginUserId) {
                $query->where('user1_id', $loginUserId)
                    ->orWhere('user2_id', $loginUserId);
            })
            ->with(['user1', 'user2']) // assuming relations are defined
            ->paginate();

            // Transform to return only the connected user
            $records->getCollection()->transform(function ($connection) use ($loginUserId) {
            $otherUser = $connection->user1_id == $loginUserId ? $connection->user2 : $connection->user1;
               
            return [
                'connection_id' => $connection->id,
                'unique_id' => $connection->unique_id,
                'connected_from' => $connection->connected_from,
                'added_by' => $connection->added_by,
                'connected_user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->first_name .' '.$otherUser->last_name,
                    'email' => $otherUser->email,
                    'role' => $otherUser->role
                ] : null,
                'created_at' => $connection->created_at,
            ];
        });

            $viewData['records'] = $records;
            $view = View::make('admin-panel.04-profile.user-connections.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error fetching user connections: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch connections.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}