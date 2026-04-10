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

class UserPlanFeatureController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }


    public function index()
    {
        $viewData['pageTitle'] = "User Plan Feature History";
        return view('admin-panel.09-utilities.user-plan-feature.lists', $viewData);
    }


    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input("status");
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $records = \App\Models\UserPlanFeatureHistory::with(['user', 'addedByUser'])
            ->select([
                'user_id',
                'feature_key',
                'module_name',
                'plan_limit',
                \DB::raw('SUM(current_usage) as total_usage'),
                \DB::raw('MAX(added_by) as added_by'),
                \DB::raw('MAX(created_at) as latest_created_at'),
                \DB::raw('MAX(unique_id) as unique_id')
            ])
            ->where(function ($query) use ($search, $status) {
                if ($search != '') {
                    $query->where("feature_key", "LIKE", "%" . $search . "%")
                          ->orWhere("module_name", "LIKE", "%" . $search . "%")
                          ->orWhere("description", "LIKE", "%" . $search . "%");
                }
                if($status != ''){
                    $query->where("current_usage", $status);
                }
            })
            ->where('user_id',auth()->user()->id)
            ->groupBy('user_id', 'feature_key', 'module_name', 'plan_limit')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.09-utilities.user-plan-feature.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

}
