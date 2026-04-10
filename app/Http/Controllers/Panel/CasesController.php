<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Invoice;
use App\Models\SupportByUser;
use Stripe\Stripe;

use Illuminate\Support\Facades\Log;
use App\Models\UserSubscriptionHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatNotification;
use App\Models\Cases;
use App\Models\CaseComment;
use App\Models\ProfessionalFavouriteCase;
use App\Models\ModulePrivacy;
use App\Models\ModulePrivacyOptions;
use App\Models\UserPrivacySettings;
use App\Models\ProfessionalCaseViewed;
use App\Models\ProfessionalServices;
use App\Models\ProfessionalSubServices;
use App\Models\Quotation;
use App\Models\CaseQuotation;
use App\Models\ClientCaseHistory;
use App\Models\CaseQuotationItem;
use App\Models\CaseProposalHistory;
use App\Models\ChatGroup;
use App\Models\GroupMembers;
use App\Models\CaseChat;
use App\Services\CasesService;
use App\Models\ImmigrationServices;
class CasesController extends Controller
{
    protected $casesService;

    public function __construct(CasesService $casesService)
    {
        $this->casesService = $casesService;
        // Constructor method for initializing middleware or other components if needed
    }

    public function index()
    {
        $type = request()->query('type', 'all');
        $viewData = $this->casesService->getIndexData(auth()->id(), $type, url()->full());
        return view('admin-panel.08-cases.cases.lists', $viewData);
    }

    public function fetchSubService(Request $request)
    {
        
        $service_id = $request->input("service_id");
        $parent_service = ImmigrationServices::where('unique_id',$service_id)->first();
        $services = ImmigrationServices::where("parent_service_id", $parent_service->id)->get();
        $options = '<option value="">Select Service</option>';
        foreach ($services as $service) {
            $options .= '<option value="' . $service->unique_id . '">' . $service->name . '</option>';
        }
        $response['options'] = $options;
        $response['status'] = true;
        return response()->json($response);
    }
    /**
     * Get the list of Support Payment with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $result = $this->casesService->getCasesList(
            auth()->id(),
            $request->input('type'),
            $request->input('search'),
            $request->case_id,
            $request->is_mobile == 'true',
            $request->input('service_id'),
            $request->input('sub_service_id'),
            $request->input('priority'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('hour_range'),
            $request->input('trending_case'),
            $request->input('sort_by')
        );
        return response()->json($result);
    }

    public function getGridAjaxList(Request $request)
    {
        $result = $this->casesService->getGridCasesList(
            auth()->id(),
            $request->input('type'),
            $request->input('search'),
            $request->case_id,
            $request->is_mobile == 'true',
            $request->input('service_id'),
            $request->input('sub_service_id'),
            $request->input('priority'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('hour_range'),
            $request->input('trending_case'),
            $request->input('sort_by')
        );
        return response()->json($result);
    }

    public function markAsFavourite($case_id)
    {
        $result = $this->casesService->toggleFavourite(auth()->id(), $case_id);
        return redirect()->back()->with($result['status'], $result['message']);
    }

    public function updateSettings(Request $request)
    {
        $result = $this->casesService->updatePrivacySettings(auth()->id(), $request->settings);
        return redirect()->back()->with($result['status'], $result['message']);
    }

    public function readUnread(Request $request)
    {
        $result = $this->casesService->setReadUnread($request->input('ids'), $request->input('type'));
        \Session::flash('success', $result['message']);
        return response()->json(['status' => $result['status']]);
    }


    public function viewDetails($id)
    {
        
        $viewData = $this->casesService->getCaseDetails(auth()->id(), $id);
        return view('admin-panel.08-cases.cases.view', $viewData);
    }

    public function fetchQuotation(Request $request)
    {
        $result = $this->casesService->getQuotationHtml($request->quotation_id);
        return response()->json($result);
    }

    public function saveProposals(Request $request)
    {
        $result = $this->casesService->saveProposal(auth()->id(), $request->all());
        return response()->json($result);
    }

    public function proposalHistory(Request $request)
    {
        $result = $this->casesService->getProposalHistory(auth()->id(), $request->id);
        return response()->json($result);
    }

    public function editProposal($id)
    {
        $result = $this->casesService->getEditProposalData(auth()->id(), $id);
        return response()->json($result);
    }   

    public function updateProposals(Request $request)
    {
        $result = $this->casesService->updateProposals(auth()->id(), $request->all());
        return response()->json($result);
    }

    public function createGroup($case_id)
    {
        $result = $this->casesService->createOrGetGroup(auth()->id(), $case_id);
        return response()->json($result);
    }
    public function generateCaseProposal(Request $request)
    {
        $case = Cases::where('id',$request->case_id)->first();
        $parameter['case_summary'] = (string) $case->description;
        $parameter['service_type'] = (string) $request->services;
        $apiData['parameters'] = $parameter;
        $apiResponse = assistantApiCall('ai-agents/generate-case-proposal', $apiData);
        // pre($apiResponse);
        if($apiResponse['status'] == true || $apiResponse['status'] == 'success'){
            // // Check if user already added a comment for this case
            // $existingComment = CaseComment::where('case_id', $request->case_id)
            //     ->where('added_by', \Auth::user()->id)
            //     ->first();
            
            // if(!empty($existingComment)){
            //     $existingComment->status = "withdraw";
            //     $existingComment->save();
            // }
            // $object = new CaseComment();
            // $object->case_id = $request->case_id;
            // $object->comments = $apiResponse['message'];
            // $object->added_by = \Auth::user()->id;
            // $object->status = 'pending';
            // $object->save();
            
            $response['data'] = $apiResponse['data']['result']??'';
            $response['status'] = true;
            $response['message'] = "proposal generated succesfully";
        }else{
            $response['status'] = false;
            $response['message'] = "Please generate again";
        }

        return response()->json($response);
       
    }
    public function withdrawProposal($id)
    {
        $result = $this->casesService->withdrawProposal(auth()->id(), $id);
        return redirect()->back()->with($result['status'], $result['message']);
    }

    

}
