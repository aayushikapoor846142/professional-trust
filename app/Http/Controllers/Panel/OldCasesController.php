<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CaseQuotation;
use App\Models\CaseQuotationItem;
use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Models\ProfessionalServices;
use View;
use App\Models\Cases;
use Illuminate\Support\Facades\Validator;
use App\Models\CaseComment;
use App\Models\ProfessionalSubServices;
use App\Models\SubServicesTypes;
use App\Models\ClientCaseHistory;
use App\Models\CaseProposalHistory;
use App\Models\CaseWithProfessionals;
use App\Models\ProfessionalCaseViewed;

class OldCasesController extends Controller
{
    /**
     * Display a listing of the cases.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Cases List";
        return view('admin-panel.08-cases.cases.lists', $viewData);
    }

    public function overview()
    {
        $viewData['pageTitle'] = "Cases Overview";
        return view("admin-panel.08-cases.cases.cases-overview",$viewData);
    }
    /**
     * Get the cases list via AJAX with search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");



    // Fetch cases with related ProfessionalServices, filtering by ImmigrationServices if needed
        $records = Cases::with(['services'])
        ->when($search, function ($query) use ($search) {
            $query->where('title', 'LIKE', "%{$search}%") // Searching in the 'title' column
                ->orWhereHas('services', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%"); // Searching in ImmigrationServices 'name'
                });
        })
        ->where("status","posted")
        ->orderBy("id","desc")
        ->paginate(5);

        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage()??0;
        $viewData['last_page'] = $records->lastPage()??0;
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;

        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] =  $records->total();
        $view = View::make('admin-panel.08-cases.cases.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function proposalHistory(Request $request)
    {

        // Fetch cases with related ProfessionalServices, filtering by ImmigrationServices if needed
        $comment = Cases::where("unique_id", $request->id)->with(['services', 'subServices', 'comments'])->first();
        $records = CaseComment::with(['caseProposalHistory.caseQuotation.particulars'])->where('case_id', $comment->id)
                ->where('added_by', \Auth::user()->id)
                ->orderBy('id','desc')
                ->paginate();
        $case_history =  ClientCaseHistory::where('client_case_id',$comment->id)->first();
     
        $viewData['case_history'] = $case_history;
        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage()??0;
        $viewData['last_page'] = $records->lastPage()??0;
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;

        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] =  $records->total();
        $view = View::make('admin-panel.08-cases.cases.proposal-history-ajax', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;

        return response()->json($response);
    }


    public function viewDetails($id)
    {

       
        // $chatNotificaiton = ChatNotification::where('redirect_link',$id)->where('user_id',auth()->user()->id)->where('type','post-case')->first();

        // if($chatNotificaiton->is_read == 0){
        //     $chatNotificaiton->is_read = 1;
        //     $chatNotificaiton->save();
        // }
        $record = Cases::where("unique_id", $id)->with(['services', 'subServices', 'comments'])->first();

        $professionalCaseView = ProfessionalCaseViewed::where('user_id',auth()->user()->id)->where('case_id',$record->id)->first();

        if(!empty($professionalCaseView)){
            $professionalCaseView->view_count = $professionalCaseView->view_count + 1;
            $professionalCaseView->last_view_date = date('Y-m-d H:i:s');
        }else{
            $professionalCaseView = new ProfessionalCaseViewed;
            $professionalCaseView->user_id = auth()->user()->id;
            $professionalCaseView->case_id = $record->id;
            $professionalCaseView->view_count = 1;
            $professionalCaseView->last_view_date = date('Y-m-d H:i:s');
        }

        $professionalCaseView->save();
      
        $comment = CaseComment::where('case_id', $record->id)
                ->where('added_by', \Auth::user()->id)
                ->orderBy('id','desc')
                ->where('status','pending')
                ->first();
        $professionalService = ProfessionalServices::where('user_id', \Auth::user()->id)
        ->where('parent_service_id', $record->parent_service_id)
        ->where('service_id', $record->sub_service_id)
        ->first();
    
        $sub_services = [];
        if(!empty($professionalService)){
            $sub_services =  ProfessionalSubServices::with(['subServiceTypes'])->where('professional_service_id',$professionalService->id)->get();
        }

 
        // if (!$professionalService) {
        //      return handleUnauthorizedAccess('You are not authorized to edit this Page');;
        // }
        $quotations = Quotation::where("service_id",$record->sub_service_id)->get();

        $case_quotations = CaseQuotation::where("case_id",$record->id)->where("added_by",\Auth::user()->id)->first();
       
        $case_history =  ClientCaseHistory::where('client_case_id',$record->id)->where('professional_id',auth()->user()->id)->first();
       
        $case_quotation_history = CaseQuotation::where("case_id",$record->id)->where("added_by",\Auth::user()->id)->get();

        $viewData['pageType'] = 'view';
        $viewData['record'] = $record;
        $viewData['case_history'] = $case_history;
        $viewData['quotations'] = $quotations;
        $viewData['case_quotations'] = $case_quotations;
        $viewData['pageTitle'] = "View Case Details";
        $viewData['comment'] = $comment;
        $viewData['sub_services'] = $sub_services;
        $viewData['case_quotation_history'] = $case_quotation_history;
        return view('admin-panel.08-cases.cases.view', $viewData);
    }

    public function fetchQuotation(Request $request){
        $quotation_id = $request->quotation_id;
        $quotation = Quotation::where("id",$quotation_id)->first();
        $html = '';
        if(!empty($quotation)){
            foreach($quotation->particulars as $particular){
                $html .='<tr>';
                $html .='<input type="hidden" name="items['.$particular->id.'][id]" value="'.$particular->id.'" />';
                $html .='<td><input type="text" class="item-name form-control" name="items['.$particular->id.'][name]" placeholder="Enter item name" value="'.$particular->particular.'"></td>';
                $html .='<td><input type="number" name="items['.$particular->id.'][amount]" class="item-amount form-control" min="0" value="'.$particular->amount.'"></td>';
                $html .='<td><input type="text" class="row-sub-total form-control" name="items['.$particular->id.'][row_sub_total]"  value="'.$particular->amount.'" disabled>';
                $html .='</td>';
                $html .='<td><span class="invoice-remove-btn">X</span></td>';
                $html .='</tr>';
            }
        }
        $response['status'] = true;
        $response['contents'] = $html;

        return response()->json($response);
    }

    public function saveComments(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'sub_service_type_id' => 'required',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:1',
                'total_amount' => 'required|numeric|min:1'
            ],
  [
                'items.required' => 'At least one item is required.',
                'items.array' => 'Invalid item format.',
                'items.min' => 'You must add at least one item.',
            
                'items.*.name.required' => 'The item name is required.',
                'items.*.name.string' => 'The item name must be a valid text.',
                'items.*.name.max' => 'The item name should not exceed 255 characters.',
            
                'items.*.amount.required' => 'The item amount is required.',
                'items.*.amount.numeric' => 'The item amount must be a number.',
                'items.*.amount.min' => 'The item amount must be at least 1.',
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
            \DB::beginTransaction();
            $case = Cases::with('comments')->where('id', $request->case_id)->first();
    
            // Check if user already added a comment for this case
            $existingComment = CaseComment::where('case_id', $request->case_id)->where('status','sent')
            ->where('added_by', \Auth::user()->id)
            ->first();
        
            if ($existingComment) {
                $existingComment->status = 'withdraw';
                $existingComment->save();
            }

            $comment = null;
            if (!empty($request->exist_comment_id)) {
                $comment = CaseComment::where('case_id', $request->case_id)
                    ->where('id', $request->exist_comment_id)
                    ->first();
            }
        
            // If no existing comment is found, create a new one
            if (!$comment) {
                $comment = new CaseComment();
            }
        
            // Set or update the comment details
            $comment->case_id = $request->case_id;
            $comment->comments = htmlentities($request->input('description'));
            $comment->sub_service_type_id = $request->sub_service_type_id;
            $comment->added_by = \Auth::user()->id;
            $comment->status = 'sent';
            $comment->save();
            $case_comment_id = $comment->id;
            


            // $case_quotation = CaseQuotation::firstOrNew(['case_id' => $case->id]);
            $case_quotation = new CaseQuotation();
            if($request->quotation_id){
                $quotation = Quotation::where("id",$request->quotation_id)->first();
                $case_quotation->currency = $quotation->currency;
            }else{
                $case_quotation->currency = 'CAD';
            }
            $case_quotation->case_id = $request->case_id;
            $case_quotation->total_amount = $request->total_amount;
            
            $case_quotation->client_id = $case->added_by;
            $case_quotation->added_by = auth()->user()->id;
            $case_quotation->quotation_id = $request->quotation_id;
            $case_quotation->save();
            // CaseQuotationItem::where("quotation_id",$case_quotation->id)->delete();
            foreach ($request->items as $item) {
                CaseQuotationItem::create([
                    'particular' => $item['name'],
                    'amount' => $item['amount'],
                    'quotation_id' => $case_quotation->id
                ]);
            }
            $parameter = [
                'user_id' => $case->added_by,
                'case_id' => $case->unique_id
            ];
            sendChatNotification($parameter);
          

            $caseProposalHistory = new CaseProposalHistory();
            $caseProposalHistory->case_id = $case->id;
            $caseProposalHistory->case_comment_id = $case_comment_id;
            $caseProposalHistory->case_quotation_id = $case_quotation->id;
            $caseProposalHistory->added_by = auth()->user()->id;
            $caseProposalHistory->save();

             $parameter2 = [
                'case_proposals_count' => $case->comments->count(),
                'user_id' => $case->added_by,
                'case_id' => $case->unique_id,
                'send_by'=>auth()->user()->id,
                'comment'=>'New Proposal for Case'.' '. $case->title,
                'type'=>'new_case_proposal',
            ];
            globalNotification($parameter2);
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('/cases/view/' . $case->unique_id);
            $response['message'] = "Data Added successfully";
            \DB::commit();
            return response()->json($response);
        }catch(Exception $e){
            \DB::rollback();
            return response()->json(['status'=>true,'message'=>$e->getMessage()]);
        }
        
    }

    public function generateCaseProposal(Request $request)
    {

        $apiData['user_id'] = (string) auth()->user()->unique_id;
        $apiData['message'] = (string) $request->description .' '.$request->services;
      
        $apiResponse = assistantApiCall('create_proposal', $apiData);

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
            
            $response['data'] = $apiResponse['message'];
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
        
        $case = Cases::where('unique_id',$id)->first();
        if(!empty($case)){
           $case_history =  ClientCaseHistory::where('client_case_id',$case->id)->where('professional_id',auth()->user()->id)->first();
            if(empty($case_history)){
                CaseComment::where('case_id',$case->id)->where('added_by',auth()->user()->id)->update(['status' => 'withdraw']);
                return redirect()->back()->with("success", "Proposal withdraw successfully");
            }else{
                return redirect()->back()->with("error", "Proposal not withdraw");
            }
        }
       
    }


    public function editCase($id)
{
   
    $record = Cases::where("unique_id", $id)
        ->with(['services', 'subServices', 'comments'])
        ->first();
   
    $comment = CaseComment::where('case_id', $record->id)
        ->where('added_by', \Auth::user()->id)
        ->orderBy('id', 'desc')
        ->where('status', 'sent')
        ->first();

    $professionalService = ProfessionalServices::where('user_id', \Auth::user()->id)
        ->where('parent_service_id', $record->parent_service_id)
        ->where('service_id', $record->sub_service_id)
        ->first();

    $sub_services = [];
    if (!empty($professionalService)) {
        $sub_services = ProfessionalSubServices::with(['subServiceTypes'])
            ->where('professional_service_id', $professionalService->id)
            ->get();
    }

    $quotations = Quotation::where("service_id", $record->sub_service_id)->get();
    $case_praposal_history = CaseProposalHistory::where("case_id", $record->id)
    ->where('case_comment_id',$comment->id)
    ->where("added_by", \Auth::user()->id)
    ->first();
    
    $case_quotations = CaseQuotation::where("case_id", $record->id)
        ->where('id',$case_praposal_history->case_quotation_id)
        ->where("added_by", \Auth::user()->id)
        ->first();
       

    $case_history = ClientCaseHistory::where('client_case_id', $record->id)
        ->where('professional_id', auth()->user()->id)
        ->first();


    $case_quotation_history = CaseQuotation::where("case_id", $record->id)
        ->where("added_by", \Auth::user()->id)
        ->get();

    $viewData['pageType'] = 'edit';
    $viewData['record'] = $record;
    $viewData['case_history'] = $case_history;
    $viewData['quotations'] = $quotations;
    $viewData['case_quotations'] = $case_quotations;
    $viewData['pageTitle'] = "View Case Details";
    $viewData['comment'] = $comment;
    $viewData['sub_services'] = $sub_services;
    $viewData['case_quotation_history'] = $case_quotation_history;
    return view('admin-panel.08-cases.cases.view', $viewData);
}
}
