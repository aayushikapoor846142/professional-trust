<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use View;
use App\Models\User;
use Carbon\Carbon;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Follow;
use App\Models\ProfessionalJoiningRequest;
use App\Services\AssociateService;

class AssociatesController extends Controller
{
    protected $professionalsService;

    public function __construct(AssociateService $professionalsService)
    {
        $this->professionalsService = $professionalsService;
    }

    /**
     * Display a listing of the cases.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $status = request()->query('status');
        if ($status == "") {
            $status = "all";
        } else {
            $status = request()->query('status');
        }

        $result = $this->professionalsService->getProfessionalsListingData($status);
        
        if ($result['success']) {
            return view('admin-panel.06-roles.associate.lists', $result['data']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Get the cases list via AJAX with search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $result = $this->professionalsService->getProfessionalsAjaxList($request);
        
        if ($result['success']) {
            return response()->json($result['data']);
        } else {
            return response()->json([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }


    public function viewJoinRequest($id)
    {
        $viewData['record'] = ProfessionalJoiningRequest::where('unique_id',$id)->first();
        $viewData['pageTitle'] = "Summary";
        $view = view('admin-panel.06-roles.associate.view-join-request', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function acceptProposal($id)
    {
       $record = ProfessionalJoiningRequest::where('unique_id',$id)->first();
        if (!$record) {
            abort(404, 'request not found');
        }
      
        $record->status = 1;
        $record->save();
        return redirect()->back()->with("success", "Request accepted successfully");
    }

    public function rejectProposal($id)
    {
       $record = ProfessionalJoiningRequest::where('unique_id',$id)->first();
        if (!$record) {
            abort(404, 'request not found');
        }
      
        $record->status = 2;
        $record->save();
        return redirect()->back()->with("success", "Request rejected successfully");
    }
}