<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatNotification;

class NotificationsController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Notification";
        return view('admin-panel.09-utilities.notifications.lists', $viewData);
    }

    /**
     * Get the list of Support Payment with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
       
        $records = ChatNotification::where('user_id', Auth::user()->id)->where('type','post_case')
            ->orderBy('id', "desc")
            ->paginate(50);

        $viewData['records'] = $records;
        $viewData['last_page'] = $records->lastPage();
        $viewData['current_page'] = $records->currentPage();
        $viewData['total_records'] = $records->total();
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;
        $view = View::make('admin-panel.09-utilities.notifications.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }


}