<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfessionalServices;
use View;
use App\Models\Cases;
use Illuminate\Support\Facades\Validator;
use App\Models\CaseComment;
use App\Models\CaseWithProfessionals;
use App\Models\Forms;
use App\Models\ProfessionalCaseRequests;
use App\Models\User;
use App\Models\ProfessionalRequestNote;
use App\Models\StaffCases;
use App\Models\CaseChat;
use App\Models\GroupMembers;
use App\Models\CaseRetainAgreements;
use App\Models\ChatNotification;
use App\Models\DocumentsFolder;
use App\Models\CaseFolders;
use App\Models\ImmigrationServices;

class MyCasesController extends Controller
{
    /**
     * Display a listing of the cases.
     *
     * @return \Illuminate\View\View
     */

     
    public function overview()
    {
        $viewData['pageTitle'] = "Cases Overview";

        $viewData['recentPostCases'] = Cases::with(['userAdded', 'submitProposal'])
                    ->where('status', 'posted')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();

        $viewData['recentCaseWithProfessionals'] = CaseWithProfessionals::with(['client', 'professional'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();

        return view("admin-panel.08-cases.cases-overview",$viewData);
    }

    public function index()
    {
        $records = CaseWithProfessionals::with(['services']);
            if(auth()->user()->role == 'professional'){
                $records->where('professional_id',auth()->user()->id);
            }else{
              $cases_ids = StaffCases::where('staff_id',auth()->user()->id)->get()->pluck('case_id')->toArray();
              $records->whereIn('id',$cases_ids);
            }
            $records = $records->orderBy('id','desc')
            ->count();

        $in_progress = CaseWithProfessionals::with(['services'])->where('status','in-progress');
            if(auth()->user()->role == 'professional'){
                $in_progress->where('professional_id',auth()->user()->id);
            }else{
              $cases_ids = StaffCases::where('staff_id',auth()->user()->id)->get()->pluck('case_id')->toArray();
              $in_progress->whereIn('id',$cases_ids);
            }
            $in_progress = $in_progress->orderBy('id','desc')
            ->count();

        $viewData['pageTitle'] = "My Cases List";
        $viewData['total'] = $records;
        $viewData['in_progress'] = $in_progress;
        $viewData['mainServices'] = ImmigrationServices::where('parent_service_id',0)->orderBy('id','desc')->get();
        return view('admin-panel.08-cases.case-with-professionals.lists', $viewData);
    }

    /**
     * Get the cases list via AJAX with search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    
    public function fetchCaseRequestComments($caseRequestId,Request $request)
    {
        $viewData['request_notes'] = ProfessionalRequestNote::with('User')->where('professional_case_request_id',$caseRequestId)->orderBy('created_at', 'desc')->get();
       
        $view = View::make('admin-panel.08-cases.case-with-professionals.partials.case_request_comments', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['html'] = $contents;

        return response()->json($response);
    }
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $service_id = $request->input("service_id");
        $sub_service_id = $request->input("sub_service_id");
        $start_date = $request->input("start_date");
        $end_date = $request->input("end_date");
        $data_view = $request->input("data_view");
        // Fetch cases with related ProfessionalServices, filtering by ImmigrationServices if needed
        $records = CaseWithProfessionals::with(['services'])
            // ->when($search, function ($query) use ($search) {
            //     $query->where('case_title', 'LIKE', "%{$search}%") // Searching in the 'title' column
            //         ->orWhereHas('services', function ($q) use ($search) {
            //             $q->where('case_title', 'LIKE', "%{$search}%"); // Searching in ImmigrationServices 'name'
            //         });
                
            // });
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('case_title', 'LIKE', "%{$search}%")
                    ->orWhere('unique_id', 'LIKE', "%{$search}%"); // ✅ Added this line
                })
                ->orWhereHas('services', function ($q) use ($search) {
                    $q->where('case_title', 'LIKE', "%{$search}%"); // Searching in related model
                });
            });

            if(auth()->user()->role == 'professional'){
                $records->where('professional_id',auth()->user()->id);
            }else{
              $cases_ids = StaffCases::where('staff_id',auth()->user()->id)->get()->pluck('case_id')->toArray();
              $records->whereIn('id',$cases_ids);
            }

            if($service_id != 0 && $service_id != 0){
                $service = ImmigrationServices::where('unique_id',$service_id)->first();
                $records->where('parent_service_id',$service->id);
            }

            if($sub_service_id != 0 && $sub_service_id != 0){
                $sub_service = ImmigrationServices::where('unique_id',$sub_service_id)->first();
                $records->where('sub_service_id',$sub_service->id);
            }

            if ($start_date && $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            } elseif ($start_date && !$end_date) {
                $records->whereDate('created_at', '>=', $start_date);
            } elseif (!$start_date && $end_date) {
                $records->whereDate('created_at', '<=', $end_date);
            }

            $records = $records->orderBy('id','desc')
            // ->where('payment_status','paid')
            ->paginate(5);

        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage()??0;
        $viewData['last_page'] = $records->lastPage()??0;
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;

        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] =  $records->total();

        // if($data_view == "compact"){
        //     $view = View::make('admin-panel.08-cases.case-with-professionals.compact-ajax-list', $viewData);
        // }else{
        //     $view = View::make('admin-panel.08-cases.case-with-professionals.ajax-list', $viewData);
        // }
        $view = View::make('admin-panel.08-cases.case-with-professionals.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;

        return response()->json($response);
    }

        public function getCompactAjaxList(Request $request)
    {
        $search = $request->input("search");
        $service_id = $request->input("service_id");
        $sub_service_id = $request->input("sub_service_id");
        $start_date = $request->input("start_date");
        $end_date = $request->input("end_date");
        $data_view = $request->input("data_view");
        // Fetch cases with related ProfessionalServices, filtering by ImmigrationServices if needed
        $records = CaseWithProfessionals::with(['services'])
            // ->when($search, function ($query) use ($search) {
            //     $query->where('case_title', 'LIKE', "%{$search}%") // Searching in the 'title' column
            //         ->orWhereHas('services', function ($q) use ($search) {
            //             $q->where('case_title', 'LIKE', "%{$search}%"); // Searching in ImmigrationServices 'name'
            //         });
                
            // });
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('case_title', 'LIKE', "%{$search}%")
                    ->orWhere('unique_id', 'LIKE', "%{$search}%"); // ✅ Added this line
                })
                ->orWhereHas('services', function ($q) use ($search) {
                    $q->where('case_title', 'LIKE', "%{$search}%"); // Searching in related model
                });
            });

            if(auth()->user()->role == 'professional'){
                $records->where('professional_id',auth()->user()->id);
            }else{
              $cases_ids = StaffCases::where('staff_id',auth()->user()->id)->get()->pluck('case_id')->toArray();
              $records->whereIn('id',$cases_ids);
            }

            if($service_id != 0 && $service_id != 0){
                $service = ImmigrationServices::where('unique_id',$service_id)->first();
                $records->where('parent_service_id',$service->id);
            }

            if($sub_service_id != 0 && $sub_service_id != 0){
                $sub_service = ImmigrationServices::where('unique_id',$sub_service_id)->first();
                $records->where('sub_service_id',$sub_service->id);
            }

            if ($start_date && $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            } elseif ($start_date && !$end_date) {
                $records->whereDate('created_at', '>=', $start_date);
            } elseif (!$start_date && $end_date) {
                $records->whereDate('created_at', '<=', $end_date);
            }

            $records = $records->orderBy('id','desc')
            // ->where('payment_status','paid')
            ->paginate(5);

        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage()??0;
        $viewData['last_page'] = $records->lastPage()??0;
        $viewData['next_page'] = ($records->lastPage()??0) != 0 ?($records->currentPage() + 1):0;

        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] =  $records->total();
   
        $view = View::make('admin-panel.08-cases.case-with-professionals.compact-ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;

        return response()->json($response);
    }


    public function viewDetails($id)
    {
        $cases = CaseWithProfessionals::where("unique_id", $id)->with(['services', 'subServices'])->first();
        
    
        $viewData['record'] = $cases;
        $viewData['forms'] = Forms::where('id',$cases->form_id)->first();
        $viewData['pageTitle'] = "View My Case Details";
        $viewData['case_id'] = $id;
        $viewData['total_requests']  = ProfessionalCaseRequests::where('case_id', $cases->id)->count();
        $viewData['recent_requests']  = ProfessionalCaseRequests::where('case_id', $cases->id)->latest()->take(5)->get();

        $recent_messages = [];
        if ($cases->caseChats && $cases->caseChats->group_chat_id) {
            $recent_messages = \App\Models\GroupMessages::where('group_id', $cases->caseChats->group_chat_id)->latest()->take(5)->get();
        }

        $viewData['recent_messages'] = $recent_messages;
        return view('admin-panel.08-cases.case-with-professionals.view', $viewData);
    }

    public function sendReqeust($id)
    {

        $caseDetail = CaseWithProfessionals::where('unique_id',$id)->first();
    
        $viewData['pageTitle'] = "View My Case Details";
        $viewData['case_id'] = $id;
        return view('admin-panel.08-cases.case-with-professionals.send-request', $viewData);
    }

    public function requestAjaxList($id)
    {
        $caseDetail = CaseWithProfessionals::where('unique_id',$id)->first();
          
        $viewData['requests'] = ProfessionalCaseRequests::with(['cases'])->where('user_id',auth()->user()->id)->where('case_id',$caseDetail->id)->orderBy('id','desc')->get();


        $staff_cases = StaffCases::where('case_id',$caseDetail)->where('staff_id',auth()->user()->id)->first();

        // $other_document_folders = CaseFolders::where('case_id', $caseDetail->id)
        //     ->where(function ($query) use ($caseDetail) {
        //         $query->where('added_by', $caseDetail->professional_id)
        //             ->orWhere('added_by', $caseDetail->client_id);
        //     })
        //     ->orderBy('sort_order', 'asc')
        //     ->get();
         $other_document_folders = DocumentsFolder::where('added_by',auth()->user()->id)->get();

        $viewData['all_folders'] = $other_document_folders;
        $viewData['forms'] = Forms::visibleToUser(auth()->user()->id)->orderBy('id','desc')->get(); 
        $viewData['pageTitle'] = "View My Case Details";
        $viewData['case_id'] = $id;
       
        $view = View::make('admin-panel.08-cases.case-with-professionals.request-ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);

    }

    public function addReqeust($id)
    {

        $caseDetail = CaseWithProfessionals::where('unique_id',$id)->first();
          
        $viewData['requests'] = ProfessionalCaseRequests::with(['cases'])->where('user_id',auth()->user()->id)->where('case_id',$caseDetail->id)->orderBy('id','desc')->get();


        $staff_cases = StaffCases::where('case_id',$caseDetail)->where('staff_id',auth()->user()->id)->first();

        // $other_document_folders = CaseFolders::where('case_id', $caseDetail->id)
        //     ->where(function ($query) use ($caseDetail) {
        //         $query->where('added_by', $caseDetail->professional_id)
        //             ->orWhere('added_by', $caseDetail->client_id);
        //     })
        //     ->orderBy('sort_order', 'asc')
        //     ->get();

        $other_document_folders = DocumentsFolder::where('added_by',auth()->user()->id)->get();
        $viewData['all_folders'] = $other_document_folders;
        $viewData['forms'] = Forms::visibleToUser(auth()->user()->id)->orderBy('id','desc')->get(); 
        $viewData['pageTitle'] = "View My Case Details";
        $viewData['case_id'] = $id;
        return view('admin-panel.08-cases.case-with-professionals.add-request', $viewData);
    }

    public function editRequest($id)
    {
        $professional_case_request = ProfessionalCaseRequests::where('unique_id',$id)->first();
        
        if (!$professional_case_request) {
            abort(404, 'Request not found');
        }
        
        // Check if user has access to this request
        if ($professional_case_request->user_id != auth()->user()->id) {
            abort(403, 'Access denied');
        }
        
        $caseDetail = CaseWithProfessionals::where('id',$professional_case_request->case_id)->first();
        
        if (!$caseDetail) {
            abort(404, 'Case not found');
        }
        
        $viewData['requests'] = ProfessionalCaseRequests::with(['cases'])->where('user_id',auth()->user()->id)->where('case_id',$caseDetail->id)->orderBy('id','desc')->get();

        $staff_cases = StaffCases::where('case_id',$caseDetail)->where('staff_id',auth()->user()->id)->first();

        // $other_document_folders = CaseFolders::where('case_id', $caseDetail->id)
        //     ->where(function ($query) use ($caseDetail) {
        //         $query->where('added_by', $caseDetail->professional_id)
        //             ->orWhere('added_by', $caseDetail->client_id);
        //     })
        //     ->orderBy('sort_order', 'asc')
        //     ->get();
         $other_document_folders = DocumentsFolder::where('added_by',auth()->user()->id)->get();

        $viewData['all_folders'] = $other_document_folders;
        $viewData['professional_case_request'] = $professional_case_request;
        $viewData['forms'] = Forms::visibleToUser(auth()->user()->id)->orderBy('id','desc')->get(); 
        $viewData['pageTitle'] = "Edit Request";
        $viewData['case_id'] = $caseDetail->unique_id;
        return view('admin-panel.08-cases.case-with-professionals.edit-request', $viewData);
    }

    public function updateRequest($id){
        $request = request();
        $validator = Validator::make($request->all(), [
            'title' => 'required|input_sanitize',
            'status' =>'required',
            'request_type' =>'required',
            'message_body' =>'required',
            'assesment_form_id' => 'required_if:request_type,assesment-form-request',
            'document_id' => 'required_if:request_type,document-request',
            'information' => 'required_if:request_type,information-request'
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

        $caseRequest = ProfessionalCaseRequests::where('unique_id', $id)->first();
        if (!$caseRequest) {
            return response()->json(['status' => false, 'message' => 'Request not found.']);
        }

        $caseDetail = CaseWithProfessionals::where('id', $caseRequest->case_id)->first();
        if (!$caseDetail) {
            return response()->json(['status' => false, 'message' => 'Case not found.']);
        }

        $additional_detail = "";
        if($request->input('request_type') == "assesment-form-request")
        {
            $form = Forms::where('id',$request->input('assesment_form_id'))->first();
            $additional_detail = $form ? $form->fg_field_json : '';
        }else if($request->input('request_type') == "document-request"){
            $additional_detail = $request->input('document_id') ? implode(',',$request->input('document_id')) : '';
        }else if($request->input('request_type') == "information-request"){
            $additional_detail = $request->input('information');
        }

        $caseRequest->title = $request->input('title');
        $caseRequest->status = $request->input('status');
        $caseRequest->request_type = $request->input('request_type');
        $caseRequest->form_id = $request->input('assesment_form_id') ?? 0;
        $caseRequest->message_body = $request->input('message_body');
        $caseRequest->attachment = $request->input('attachments');
        $caseRequest->additional_detail = $additional_detail;
        $caseRequest->save();

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('case-with-professionals/send-request/'.$caseDetail->unique_id);
        $response['message'] = "Request updated successfully";
        return response()->json($response);
    }

    public function deleteRequest($id)
    {
        $record = ProfessionalCaseRequests::where('unique_id',$id)->first();
        if (!$record) {
            abort(404, 'Request not found');
        }
        ProfessionalCaseRequests::deleteRecord($record->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function viewReqeust($id)
    {
        $record = ProfessionalCaseRequests::with(['userAdded','cases.userAdded'])->where('unique_id',$id)->first();
        
        if (!$record) {
            abort(404, 'Request not found');
        }
        
        // Check if user has access to this request
        if ($record->user_id != auth()->user()->id) {
            abort(403, 'Access denied');
        }
     
        $caseDetail = CaseWithProfessionals::where('id',$record->case_id)->first();
        
        if (!$caseDetail) {
            abort(404, 'Case not found');
        }
        
        $viewData['record'] = $record;
        $viewData['request_notes'] = ProfessionalRequestNote::with('User')->where('professional_case_request_id',$record->id)->orderBy('created_at', 'desc')->get();
        $viewData['pageTitle'] = "View Request Details";
        $viewData['case_id'] = $caseDetail->unique_id;
        return view('admin-panel.08-cases.case-with-professionals.view-request', $viewData);
    }

    public function viewReqeustForm($id)
    {
        $caseRequest = ProfessionalCaseRequests::where("unique_id", $id)->first();
       
        $case = CaseWithProfessionals::where('id',$caseRequest->case_id)->first();

        if($caseRequest->request_type == "assesment-form-request"){
            $forms = Forms::where('id',$caseRequest->form_id)->first();
            $viewData['case_id'] = $case->unique_id;
            $viewData['pageTitle'] = "View My Case Details";
            $viewData['record'] = $caseRequest;
            $last_saved = '';
            $form_json = array();
            if($caseRequest->reply != ''){
                $form_json = json_decode($caseRequest->additional_detail,true);
                $form_reply = array();
                if($caseRequest->reply != ''){
                    $postData = json_decode($caseRequest->reply,true);
                    $last_saved = trim($caseRequest->reply);
                }
                
                foreach($form_json as $form){
                    $temp = array();
                    $temp = $form;
                    if(isset($form['name']) && isset($postData[$form['name']])){
                        if(isset($form['values'])){
                            $values = $form['values'];
                            $final_values = array();
                            foreach($values as $value){
                                $tempVal = $value;
                                if(is_array($postData[$form['name']])){
                                    if(in_array($value['value'],$postData[$form['name']])){
                                        $tempVal['selected'] = 1;
                                    }else{
                                        $tempVal['selected'] = 0;
                                    }
                                }else{
                                    if($value['value'] == $postData[$form['name']]){
                                        $tempVal['selected'] = 1;
                                        if($form['type'] == 'autocomplete'){
                                            $temp['value'] = $value['value'];
                                        }
                                    }else{
                                        $tempVal['selected'] = 0;
                                    }
                                }
                                $final_values[] = $tempVal;
                            }
                            $temp['values'] = $final_values;
                        }else{
                            $temp['value'] = $postData[$form['name']];
                        }
                    }
                    $form_reply[] = $temp;
                }
                $form_json = json_encode($form_reply);
            }
            $viewData['last_saved'] = $last_saved;
            $viewData['form_json'] = $form_json;
            $viewData['form'] = $forms;
        }

        if($caseRequest->request_type == "document-request"){
            $viewData['case_id'] = $case->unique_id;
            $viewData['pageTitle'] = "View My Case Details";
            $viewData['record'] = $caseRequest;
        }
        return view('admin-panel.08-cases.case-with-professionals.view-submitted-request', $viewData);
    }
    public function markAsCompleteRequest($id)
    {
        $record = ProfessionalCaseRequests::where('unique_id',$id)->first();
        $record->status = "complete";
        $record->save();
       
        return redirect()->back()->with("success", "Mark as Complete successfully");
    }

    public function downloadRequestAttachment(Request $request){
    
        $filekey = $request->file;
        return awsFileDownload(config('awsfilepath.professional_request_attachment') . '/' .$filekey);
    }

    public function uploadRequestAttachment(Request $request)
    {
        $attachmentName = "";
        $response = ['status' => false, 'message' => 'No file uploaded'];
        
        if ($file = $request->file) {
            try {
                $fileName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $attachmentName = mt_rand(1, 99999) . "-" . $fileName;
                $sourcePath = $file->getPathName();
                
                // Use mediaUploadApi instead of direct AWS upload
                $uploadPath = 'professional-request-attachment';
                $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $attachmentName);
                
                if (($api_response['status'] ?? '') === 'success') {
                    $response['status'] = true;
                    $response['filename'] = $attachmentName;
                    $response['message'] = "File uploaded successfully";
                } else {
                    $response['status'] = false;
                    $response['message'] = "Error uploading file: " . ($api_response['message'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                $response['status'] = false;
                $response['message'] = "Error uploading file: " . $e->getMessage();
            }
        }
        
        return response()->json($response);
    }

    public function saveRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|input_sanitize',
            'status' =>'required',
            'request_type' =>'required',
            'message_body' =>'required',
            'assesment_form_id' => 'required_if:request_type,assesment-form-request',
            'document_id' => 'required_if:request_type,document-request',
            'information' => 'required_if:request_type,information-request'
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

        $caseDetail = CaseWithProfessionals::where('unique_id',$request->case_id)->first();

        $additional_detail = "";
        if($request->input('request_type') == "assesment-form-request")
        {
            $form = Forms::where('id',$request->input('assesment_form_id'))->first();
            $additional_detail = $form->fg_field_json;
        }else if($request->input('request_type') == "document-request"){
            $additional_detail = implode(',',$request->input('document_id'));
        }else if($request->input('request_type') == "information-request"){
            $additional_detail = $request->input('information');
        }
        $caseRequest = ProfessionalCaseRequests::create([
            'unique_id' => randomNumber(),
            'user_id' => auth()->user()->id,
            'title' => $request->input('title'),
            'case_id' => $caseDetail->id,
            'form_id' => $request->input('assesment_form_id') ?? 0,
            'message_body' => $request->input('message_body'),
            'attachment' => $request->input('attachments'),
            'status' => $request->input('status'),
            'additional_detail' => $additional_detail,
            'request_type' => $request->input('request_type'),
        ]);
        
        // Get the newly created ID
        $newRecordId = $caseRequest->id;
      
        $user = User::where('id',$caseDetail->client_id)->first();

        // send Email to client
        $mailData  = array();
        $mailData['titles'] = $request->title;
        $mailData['casetitle'] = $caseDetail->case_title;
        $mailData['name'] = $user->first_name.' '.$user->last_name;
        $mailData['request_type'] = ucwords(str_replace('-', ' ', $request->input('request_type')));

        $view = \View::make('emails.professional-case-request', $mailData);
        $message = $view->render();

        $parameter = [
            'to' => $user->email,
            'to_name' => $user->first_name.' '.$user->last_name,
            'message' => $message,
            'subject' =>siteSetting("company_name").": New Case Request has been added",
            'view' => 'emails.new-professional-added',
            'data' => $mailData,
        ];
        // Send the email
        $mailRes = sendMail($parameter);
       
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('case-with-professionals/send-request/'.$request->case_id);
        $response['message'] = "Request send successfully";

        return response()->json($response);
    }



    public function uploadRequestNoteAttachment(Request $request)
    {
        $attachmentName = "";
        $response = ['status' => false, 'message' => 'No file uploaded'];
        
        if ($file = $request->file) {
            try {
                $fileName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $attachmentName = mt_rand(1, 99999) . "-" . 'Note-Attachment.' . $extension;
                $sourcePath = $file->getPathName();
                
                // Use mediaUploadApi instead of direct AWS upload
                $uploadPath = 'professional-request-note-attachment';
                $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $attachmentName);
                
                if (($api_response['status'] ?? '') === 'success') {
                    $response['status'] = true;
                    $response['filename'] = $attachmentName;
                    $response['message'] = "File uploaded successfully";
                } else {
                    $response['status'] = false;
                    $response['message'] = "Error uploading file: " . ($api_response['message'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                $response['status'] = false;
                $response['message'] = "Error uploading file: " . $e->getMessage();
            }
        }
        
        return response()->json($response);
    }

    public function saveNote(Request $request,$id)
    {
        
        $validator = Validator::make($request->all(), [
            'notes' => 'required',
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
        
        $caseDetail = CaseWithProfessionals::where('unique_id',$request->case_id)->first();
        
        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('attachment')) {
            $files = $request->file('attachment');
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    try {
                        $fileName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension() ?: 'png';
                        $attachmentName = mt_rand(1, 99999) . "-" . $fileName;
                        $sourcePath = $file->getPathName();
                        
                        // Use mediaUploadApi for file upload
                        $uploadPath = 'professional-request-note-attachment';
                        $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $attachmentName);
                        
                        if (($api_response['status'] ?? '') === 'success') {
                            $uploadedFiles[] = $attachmentName;
                        }
                    } catch (\Exception $e) {
                        // Log error but continue with other files
                        \Log::error('File upload error: ' . $e->getMessage());
                    }
                }
            }
        }
      
        ProfessionalRequestNote::create([
            'unique_id' => randomNumber(),
            'professional_case_request_id' => $id,
            'user_id' => auth()->user()->id,
            'notes' => $request->input('notes'),
            'attachment' => !empty($uploadedFiles) ? implode(',', $uploadedFiles) : null
        ]);

        $socket_data = [
            "action" => "new_case_request_comment",
            "case_id" => $caseDetail->id,
            "currentUserRole"=>auth()->user()->role,
            "client_id" => $caseDetail->client_id,
            "case_request_id"=>$id,            
            "professional_id" => auth()->user()->id,
        ];
        initUserSocket(auth()->user()->id, $socket_data);
        initUserSocket($caseDetail->client_id, $socket_data);
        $response['status'] = true;
        $response['message'] = "Record added successfully";
        return response()->json($response);
    }

    public function viewAssesmentForm($id)
    {
        $caseDetail = CaseWithProfessionals::where("unique_id", $id)->with(['subServicesTypes','services', 'subServices'])->first();
     
        $forms = Forms::where('id',$caseDetail->form_id)->first();
        $viewData['pageTitle'] = "View My Case Details";
        $viewData['case_id'] = $id;
        $viewData['record'] = $caseDetail;
        $last_saved = '';
        $form_json = array();
        if($caseDetail->form_id != ''){
            $form_json = json_decode($caseDetail->form_json,true);
            $form_reply = array();
            if($caseDetail->form_reply_json != ''){
                $postData = json_decode($caseDetail->form_reply_json,true);
                $last_saved = trim($caseDetail->form_reply_json);
            }
            
            foreach($form_json as $form){
                $temp = array();
                $temp = $form;
                if(isset($form['name']) && isset($postData[$form['name']])){
                    if(isset($form['values'])){
                        $values = $form['values'];
                        $final_values = array();
                        foreach($values as $value){
                            $tempVal = $value;
                            if(is_array($postData[$form['name']])){
                                if(in_array($value['value'],$postData[$form['name']])){
                                    $tempVal['selected'] = 1;
                                }else{
                                    $tempVal['selected'] = 0;
                                }
                            }else{
                                if($value['value'] == $postData[$form['name']]){
                                    $tempVal['selected'] = 1;
                                    if($form['type'] == 'autocomplete'){
                                        $temp['value'] = $value['value'];
                                    }
                                }else{
                                    $tempVal['selected'] = 0;
                                }
                            }
                            $final_values[] = $tempVal;
                        }
                        $temp['values'] = $final_values;
                    }else{
                        $temp['value'] = $postData[$form['name']];
                    }
                }
                $form_reply[] = $temp;
            }
            $form_json = json_encode($form_reply);
        }
        $viewData['last_saved'] = $last_saved;
        $viewData['form_json'] = $form_json;
        $viewData['form'] = $forms;
        
        return view('admin-panel.08-cases.case-with-professionals.assesment-form-view', $viewData);
    }

    public function assignToStaff($id)
    {
        $roles = getRoles()->pluck('slug')->toArray();
        $records = User::selectRaw("id, CONCAT(first_name, ' ', last_name) as name, role, added_by")
        ->whereIn("role", $roles)
        ->where('added_by', auth()->user()->id)
        ->orderBy('id', 'desc')
        ->get();
    

        $viewData['pageTitle'] = "Assign Staff";
        $cases = CaseWithProfessionals::where('unique_id',$id)->first();

        $selected_staff_ids = StaffCases::where('case_id',$cases->id)->get()->pluck('staff_id')->toArray();
        $viewData['users'] = $records;
        $viewData['cases'] = $cases;
        $viewData['selected_staff_ids'] = $selected_staff_ids;
        $view = view("admin-panel.08-cases.case-with-professionals.assign-to-staff",$viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }

    public function saveAssignStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staffs' => 'required',
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

        $cases = CaseWithProfessionals::where('unique_id',$request->case_id)->first();
        $case_id = $cases->id;

        // Incoming request user IDs
        $requestUserIds = $request->input("staffs");

        // Get existing user IDs from the database
        $existingUserIds = StaffCases::where('case_id',$case_id)->get()->pluck('staff_id')->toArray();

        // Find IDs to remove (exist in DB but not in request)
        $idsToRemove = array_diff($existingUserIds, $requestUserIds);

        // Find IDs to add (in request but not in existing)
        $idsToAdd = array_diff($requestUserIds, $existingUserIds);

        // Delete users that are not in the request
        StaffCases::whereIn('staff_id', $idsToRemove)->where('case_id',$case_id)->delete();
        $caseChat = CaseChat::where('case_id',$case_id)->first();
        // Insert new users
        foreach ($idsToAdd as $id) {

            StaffCases::create(['staff_id' => $id,'case_id' => $case_id,'added_by' => auth()->user()->id,'unique_id' => randomNumber()]); // Ensure `id` is fillable in the User model
            if ($caseChat) {
            $groupuser = new GroupMembers();
            $groupuser->unique_id=randomNumber();
            $groupuser->group_id = $caseChat->group_chat_id;
            $groupuser->user_id = $id;
            $groupuser->save();
            }
        }


        if ($caseChat) {
        GroupMembers::where('group_id',$caseChat->group_chat_id)->whereIn("user_id", $idsToRemove)->delete();
        }
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('case-with-professionals');
        $response['message'] = "Record edited successfully";

        return response()->json($response);
    }

    public function downloadFile(Request $request){
        $filekey = $request->file;
        return awsFileDownload(config('awsfilepath.professional_request_note_attachment') . '/' .$filekey);
    }

    public function retainAgreements($case_id)
    {
        $caseWithProfessional  = CaseWithProfessionals::where('unique_id',$case_id)->first();
         
        $caseRetainAgreements = CaseRetainAgreements::where('professional_case_id',$caseWithProfessional->id)->first();
        // if(empty($caseRetainAgreements)){
        //     CaseRetainAgreements::create([
        //         'professional_case_id'=>$caseWithProfessional->id,
        //         'added_by'=>auth()->user()->id,
        //         'status'=>'draft',
        //     ]);
        //     $caseRetainAgreements = CaseRetainAgreements::where('professional_case_id',$caseWithProfessional->id)->first();
        // }
        $viewData['CaseRetainAgreements'] = $caseRetainAgreements;
        $viewData['case_id'] = $case_id;
        $viewData['pageTitle'] = 'Retain Agreements';
        return view('admin-panel.08-cases.case-with-professionals.retain-agreements',$viewData);
    }

    public function saveRetainAgreements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agreement' => 'required',
            'title' => 'required',
            'signature_type' => 'required'
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

        
        $case = CaseWithProfessionals::where('unique_id',$request->case_id)->first();

        $caseRetainAg = CaseRetainAgreements::updateOrCreate(
            [
                'professional_case_id' => $case->id, // Search condition
            ],
            [
                'added_by' => auth()->user()->id,
                'status' => 'pending',
                'title' => $request->title,
                'agreement' => $request->agreement,
                'signature_type' => $request->signature_type,
                'posted_on' => date('Y-m-d'),
                'posted_by' => auth()->user()->id
            ]
        );

        $chatNotification = new ChatNotification();
        $chatNotification->comment = $case->case_title;
        $chatNotification->type = 'send_retain_agreement';
        $chatNotification->is_read = 0;
        $chatNotification->user_id = $case->client_id;
        $chatNotification->redirect_link = $case->unique_id;
        $chatNotification->send_by = \Auth::user()->id;
        $chatNotification->save();

        $count = ChatNotification::where('type','send_retain_agreement')->where('user_id',$case->client_id)->where('is_read',0)->count();
        $arr = [
            'comment' =>'*'. auth()->user()->first_name . " " . auth()->user()->last_name . '*  send you a agreement for a case .'.$case->case_title,
            'type' => 'send_retain_agreement',
            'redirect_link' => $case->unique_id,
            'is_read' => 0,
            'user_id' => $case->client_id,
            'count' => $count,
            'send_by' => auth()->user()->id ?? '',
        ];
        chatNotification($arr);

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('case-with-professionals');
        $response['message'] = "Agreement send successfully";

        return response()->json($response);
    }

    public function  checkRetainAgreements(Request $request,$id){

        $retainAgreement = CaseRetainAgreements::where('unique_id',$id)->first();
   
        $apiData['retain_agreement'] = $retainAgreement->agreement;
      
        $apiResponse = assistantApiCall('retain-agreement-checker', $apiData);
        \Log::info($apiResponse);
        if(isset($apiResponse['status']) && $apiResponse['status'] === true){
            $response['status'] = true;
          
            $response['message'] = "Your retain agreement is verifed succesfully";
            $response['agreement'] = "Your retain agreement is verifed succesfully";
           
        }else{
            $response['status'] = false;
          
            $response['message'] = "Your retain agreement is verifed succesfully";
            $response['agreement'] = $apiResponse['response'];
        }

        $response['agreement_id'] = $retainAgreement->unique_id;
        // $viewData['pageTitle'] = "Show Verify Agreement";
     
        // $view = view("admin-panel.08-cases.case-with-professionals.show-verify-retain-agreements",$viewData)->render();
        // $response['status'] = true;
        // $response['contents'] = $view;

        return response()->json($response);
    }

    // public function generateRetainAgreements($id)
    // {
    //     $retainAgreement = CaseRetainAgreements::where('unique_id',$id)->first();
   
    //     $apiData['retain_agreement'] = $retainAgreement->agreement;
      
    //     $apiResponse = assistantApiCall('generate-retain-agreement', $apiData);
    //     // $retainAgreement->agreement = $apiResponse['response'] ?? $retainAgreement->agreement;
    //     // $retainAgreement->save();

    //     $response['status'] = true;
    //     $response['agreement'] = $apiResponse['response'] ?? $retainAgreement->agreement;
    //     $response['message'] = "Your retain agreement append please check and save";

    //     return response()->json($response);

    //     // return redirect()->back()->with('success', 'Your Retain Agreement updated succesfully');


    // }

    public function aiRetainerAgreementForm($case_id){
        $case = CaseWithProfessionals::where("unique_id", $case_id)
                                    ->with(['services', 'subServices'])
                                    ->first();
        $viewData['case_id'] = $case_id;
        $viewData['pageTitle'] = "Retain Agreements";
        $view = view("admin-panel.08-cases.case-with-professionals.retain-agreements.generate-agreement-with-ai",$viewData);
        $contents = $view->render();

        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }
    public function generateRetainAgreements($case_id,Request $request)
    {
        $case = CaseWithProfessionals::where("unique_id", $case_id)
                                    ->with(['services', 'subServices'])
                                    ->first();

        
        if(auth()->user()->role != 'professional'){
            $professional_id = auth()->user()->getRelatedProfessionalId();
            $professional = User::find($professional_id);
        }else{
            $professional = auth()->user();
        }
        $professional_details = "Professional Name:".$professional->first_name." ".$professional->last_name;
        $professional_details .= "\nLicense Number: ".$professional->professionalLicense->license_number??'NA';
        $professional_details .="\nTitle:".$professional->professionalLicense->title??'NA';
        $professional_details .="\Class:".$professional->professionalLicense->class_level??'NA';
        $parameter['service_type'] = $case->services->name.' > '.$case->subServices->name;
        $parameter['professional_description'] = $professional_details;
        $parameter['case_description'] = $case->case_description;
        $apiData['parameters'] = $parameter;
        
        $apiResponse = assistantApiCall('ai-agents/retainer-agreement-question', $apiData);
        // pre($apiResponse);
        // exit;
       
        if($apiResponse['status'] == true || $apiResponse['status'] == 'success'){
            $response['data'] = $apiResponse['data']['result']??'';
            $response['status'] = true;
            $response['message'] = "Agreement generated succesfully";
            $viewData['records'] = $apiResponse['data']['result']??'';
            $viewData['case_id'] = $case_id;
        }else{
            $response['status'] = false;
            $response['message'] = "Please generate again";
        }

        $view = View::make('admin-panel.08-cases.case-with-professionals.retain-agreements.show-agreement-with-ai', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function saveAiRetainAgreements(Request $request,$case_id)
    {
        // return $request->all();
        $formatted = '';
        foreach ($request->input('answers', []) as $answer) {
            $formatted .= "{$answer['question']}: {$answer['value']}\n\n";
        }

        $parameter['retainer_agreement_detail'] = $formatted;
        $apiData['parameters'] = $parameter;
        

        $apiResponse = assistantApiCall('ai-agents/generate-retainer-agreement', $apiData);
        \Log::info($apiResponse);
        if($apiResponse['status'] == true || $apiResponse['status'] == 'success'){
            $response['status'] = true;
            $response['agreement'] = $apiResponse['data']['result'];
            $response['message'] = "Agreement generated succesfully";
        }else{
            $response['status'] = false;
            $response['message'] = "Please save again";
        }
        return response()->json($response);
    }
    public function retainAgreementAiBot($case_id)
    {
        $caseWithProfessional  = CaseWithProfessionals::where('unique_id',$case_id)->first();
        $viewData['CaseRetainAgreements'] = CaseRetainAgreements::where('professional_case_id',$caseWithProfessional->id)->first();
        $viewData['case_id'] = $case_id;
        $viewData['conversation_id'] = 122132;
        $viewData['pageTitle'] = 'AI Bot';
        return view('admin-panel.08-cases.case-with-professionals.retain-agreement-ai-bot',$viewData);
    }

    public function submitRetainAgreementBot($case_id,Request $request){
        try{
            \DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'message' => 'required', // <- file validation
            ]);
    
            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();
            
                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['error_type'] = "validation";
                $response['message'] = $errMsg;
                return response()->json($response);
            }
            $apiData['case_id'] = (string) $case_id;
            $apiData['user_id'] = (string) auth()->user()->unique_id;
            $apiData['message'] = $request->message;
            if($request->conversation_id != ''){
                $apiData['conversation_id'] = $request->conversation_id;
            }
            

            $apiResponse = assistantApiCall('retain_agreement_chat', $apiData);
            
            if(isset($apiResponse['status']) && ($apiResponse['status'] == 'success' || $apiResponse['status'] == true)) {
                $response['send_message'] = $request->message;
                $response['message'] = $apiResponse['message'] ?? '';
                $response['preview'] = $apiResponse['preview'] ?? '';
                $response['file'] = $apiResponse['download_url'] ?? '';
                $response['type'] = $apiResponse['type'] ?? '';
                $response['agreement_draft'] = $apiResponse['agreement_draft']??'';
                $response['file_upload_required'] = $apiResponse['file_upload_required'] ?? '';
                // $response['file_upload_required'] = true;
                $response['file_label'] = $apiResponse['file_label'] ?? '';
                // $response['redirect_back'] = url('case/post-case/'.$case->unique_id);
                $response['status'] = true;
                \DB::commit();
                
            }else{
                \DB::rollback();
                $response['status'] = false;
                $response['message'] = $apiResponse['message']??'Something went wrong try again';
            }
            return response()->json($response);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()." Line: ".$e->getLine()]);

        }
        
    }

    public function fetchRetainAgreementBot($case_id,Request $request){
        try{
            \DB::beginTransaction();

            $apiData['case_id'] = (string) $case_id;
            $apiData['user_id'] = (string) auth()->user()->unique_id;
            

            $apiResponse = assistantApiCall('fetch_retain_agreement_chat', $apiData);
          
            // final_response_check_agreement
           
            if(isset($apiResponse['data'])) {
                $response['send_message'] = $request->message;
                $viewData['results'] = $apiResponse['data'] ?? array();
                
                $view = view("admin-panel.08-cases.case-with-professionals.retain-agreement-chat",$viewData)->render();
                $response['contents'] = $view;
                // $response['preview'] = $apiResponse['preview'] ?? array();
                // $response['agreement_draft'] = $apiResponse['agreement_draft']??'';
                // $response['file_upload_required'] = $apiResponse['file_upload_required'] ?? '';
                // $response['file_upload_required'] = true;
                // $response['file_label'] = $apiResponse['file_label'] ?? '';
                // $response['redirect_back'] = url('case/post-case/'.$case->unique_id);
                $response['application_draft'] = $apiResponse['final_response_check_agreement'] ?? '';
                $response['status'] = true;
                \DB::commit();
            
            }else{
                \DB::rollback();
                $response['status'] = false;
                $response['message'] = $apiResponse['message']??'Something went wrong try again';
            }
            return response()->json($response);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()." Line: ".$e->getLine()]);

        }
        
    }

    public function showPopupForSaveAgreement($id)
    {
        $viewData['pageTitle'] = "Retain Agreement";
        $viewData['agreement_id'] = $id;
        $view = view("admin-panel.08-cases.case-with-professionals.retain-agreement-save-popup",$viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }


    // public function saveAiRetainAgreements(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $response['status'] = false;
    //         $error = $validator->errors()->toArray();
    //         $errMsg = array();

    //         foreach ($error as $key => $err) {
    //             $errMsg[$key] = $err[0];
    //         }
    //         $response['message'] = $errMsg;
    //         return response()->json($response);
    //     }

        
    //     $CaseRetainAgreements = CaseRetainAgreements::where('unique_id',$request->agreement_id)->first();

    //     $caseWithProfessionals = CaseWithProfessionals::where('id',$CaseRetainAgreements->professional_case_id)->first();

    //     $apiData['case_id'] = (string) $caseWithProfessionals->unique_id;
    //     $apiData['user_id'] = (string) auth()->user()->unique_id;
            

    //     $apiResponse = assistantApiCall('fetch_retain_agreement_chat', $apiData);

        
    //     $CaseRetainAgreements->agreement = $apiResponse['final_response_check_agreement'] ?? '';
    //     $CaseRetainAgreements->title = $request->title;
    //     $CaseRetainAgreements->posted_on = date('Y-m-d');
    //     $CaseRetainAgreements->posted_by = auth()->user()->id;
    //     $CaseRetainAgreements->save();


    //     $case = CaseWithProfessionals::where('id',$CaseRetainAgreements->professional_case_id)->first();

    //     $chatNotification = new ChatNotification();
    //     $chatNotification->comment = $case->case_title;
    //     $chatNotification->type = 'send_retain_agreement';
    //     $chatNotification->is_read = 0;
    //     $chatNotification->user_id = $case->client_id;
    //     $chatNotification->redirect_link = $case->unique_id;
    //     $chatNotification->send_by = \Auth::user()->id;
    //     $chatNotification->save();

    //     $count = ChatNotification::where('type','send_retain_agreement')->where('user_id',$case->client_id)->where('is_read',0)->count();
    //     $arr = [
    //         'comment' =>'*'. auth()->user()->first_name . " " . auth()->user()->last_name . '*  send you a agreement for a case .'.$case->case_title,
    //         'type' => 'send_retain_agreement',
    //         'redirect_link' => $case->unique_id,
    //         'is_read' => 0,
    //         'user_id' => $case->client_id,
    //         'count' => $count,
    //         'send_by' => auth()->user()->id ?? '',
    //     ];
    //     chatNotification($arr);

    //     $response['status'] = true;
    //     $response['redirect_back'] = baseUrl('case-with-professionals');
    //     $response['message'] = "Agreement send successfully";

    //     return response()->json($response);
    // }

    public function retainers(Request $request)
    {
        $status = $request->query('status', 'all'); // defaults to 'all' if missing

        if (empty($status)) {
            $status = 'all';
        }

        $records = CaseRetainAgreements::whereHas('case',function($query) {
                if(auth()->user()->role != 'professional'){
                    $query->whereHas("assignedStaff",function($q) {
                       $q->where('staff_id',auth()->user()->id); 
                    });
                }
                $query->where("professional_id",auth()->user()->getRelatedProfessionalId());
            })->get();
          
        $viewData['pageTitle'] = "Retainer Agreements";
        $viewData['status'] = $status;
        $viewData['records'] = $records;

        return view('admin-panel.08-cases.case-with-professionals.retain-agreements.lists', $viewData);
    }


     /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetainerAgreementAjax(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input("status");

        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
        $records = CaseRetainAgreements::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
            })->with(['userAdded'])
            ->whereHas('case',function($query) {
                if(auth()->user()->role != 'professional'){
                    $query->whereHas("assignedStaff",function($q) {
                       $q->where('staff_id',auth()->user()->id); 
                    });
                }
                $query->where("professional_id",auth()->user()->getRelatedProfessionalId());
            });
          if ($sortColumn === 'professional_case_id') {
    // Use a subquery to sort by related case title
    $records->with('case') // already included above, but ensure it's eager loaded
            ->orderBy(
                CaseWithProfessionals::select('case_title')
                    ->whereColumn('case_with_professionals.id', 'case_retain_agreements.professional_case_id'),
                $sortDirection
            );
} else {
    $records->orderBy($sortColumn, $sortDirection);
}


            if($request->status != 'all'){
                $records->where('status',$request->status);
            }
           $records = $records->paginate(1);

      
        $viewData = [
            'records' => $records,
            'current_page' => $records->currentPage() ?? 0,
            'last_page' => $records->lastPage() ?? 0,
            'next_page' => ($records->lastPage() ?? 0) != 0 ? ($records->currentPage() + 1) : 0
        ];
        $view = View::make('admin-panel.08-cases.case-with-professionals.retain-agreements.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }
    public function sendRetainerAgreementReminder($unique_id){
        $retainer_agreement = CaseRetainAgreements::where("unique_id",$unique_id)->first();
        $user = $retainer_agreement->case->client;
        $professional = $retainer_agreement->case->professional;
        $caseDetail = $retainer_agreement->case;
        $mailData  = array();
        $mailData['case_title'] = $caseDetail->case_title;
        $mailData['retainer_title'] = $retainer_agreement->title;
        $mailData['name'] = $user->first_name.' '.$user->last_name;
        $mailData['professional_name'] = $professional->first_name.' '.$professional->last_name;

        $view = \View::make('emails.retainer-agreement-reminder', $mailData);
        $message = $view->render();

        $parameter = [
            'to' => $user->email,
            'to_name' => $user->first_name.' '.$user->last_name,
            'message' => $message,
            'subject' =>siteSetting("company_name").": Reminder for Retainer Agreement",
            'view' => 'emails.retainer-agreement-reminder',
            'data' => $mailData,
        ];
        // Send the email
        $mailRes = sendMail($parameter);

        return redirect()->back()->with("success","Reminder sent successfully");
    }
}

