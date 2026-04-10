<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfessionalServices;
use View;
use Illuminate\Support\Facades\Validator;
use App\Models\CaseStages;
use App\Models\CaseWithProfessionals;
use App\Models\CaseFolders;
use App\Models\CaseRetainAgreements;
use App\Models\Forms;
use App\Models\CaseSubStages;
use App\Models\PredefinedCaseStages;
use App\Models\CaseDocuments;
use Illuminate\Support\Arr;
use App\Models\Invoice;
use App\Services\CaseStageService;

class CaseStagesController extends Controller
{
    protected $caseStageService;

    public function __construct(CaseStageService $caseStageService)
    {
        $this->caseStageService = $caseStageService;
    }
    

    public function stagesList($case_id)
    {
        $case = CaseWithProfessionals::where('unique_id',$case_id)->first();

        $records = CaseStages::with(['caseSubStages'])->where('case_id',$case->id)
            ->orderBy('sort_order', "asc")
            ->get();

        $percentage = 0;
        $doneTask = 0;
        $pendingTask = 0;
        if($records->isNotEmpty()){
           
            $percentage = ($records->where('status','complete')->count() / $records->count()) * 100;
           
            $subStages =  CaseSubStages::whereIn('stage_id',$records->pluck('id')->toArray())->get();
            $doneTask = $subStages->where('status','complete')->count();
            $pendingTask = $subStages->count() - $subStages->where('status','complete')->count();
        }
       

        $viewData['records'] = $records;
        $viewData['pageTitle'] = "Case Stages";
        $viewData['case_id'] = $case_id;
        $viewData['percentage'] = round($percentage);
        $viewData['doneTask'] = $doneTask;
        $viewData['pendingTask'] = $pendingTask;
        return view('admin-panel.08-cases.case-with-professionals.stages.lists', $viewData);
    }


    public function getStagesAjaxList(Request $request)
    {
   
        $case = CaseWithProfessionals::where('unique_id',$request->case_id)->first();
        return $records = CaseStages::with(['caseSubStages'])->where('case_id',$case->id)
           ->orderBy('id', "desc")
            ->get();

        $viewData['records'] = $records;
        $viewData['case_id'] = $request->case_id;
        $view = View::make('admin-panel.08-cases.case-with-professionals.stages.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function addStages($case_id)
    {
          
        $viewData['pageTitle'] = "Add Case Stage";
        $viewData['case_id'] = $case_id;
        $view = view("admin-panel.08-cases.case-with-professionals.stages.add",$viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }
   
    public function saveStages(Request $request,$case_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'short_description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $case = $this->getCaseOrFail($case_id);

        $caseStages = CaseStages::where('case_id',$case->id)->where('user_id',auth()->user()->id)->orderBy('id','desc')->first();

        $sort_order = 1;
        if(!empty($caseStages)){
            $sort_order = $caseStages->sort_order + 1;
        }
        $nextStage = CaseStages::where('case_id',$case->id)
                    ->orderBy('sort_order', 'asc')
                    ->where('status','pending')
                    ->orWhere('status','in-progress')
                    ->first();

        $status = "pending";
        if(empty($nextStage)){
            $status="in-progress";
        }

        $stageData = [
            'stage_name' => $request->input('name'),
            'description' => $request->input('short_description'),
            'stage_fees' => $request->input('fees'),
            'stage_type' => 'custom',
            'user_id' => auth()->user()->id,
            'case_id' => $case->id,
            'added_by' => auth()->user()->id,
            'sort_order' => $sort_order,
            'status' => $status
        ];
        $this->caseStageService->createStage($case, $stageData, auth()->user()->id);

        $response['status'] = true;
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }

    public function editStages($id)
    {
        $viewData['pageTitle'] = "Edit Case Stage";
        $viewData['record'] = CaseStages::where('unique_id',$id)->first();
        $view = view("admin-panel.08-cases.case-with-professionals.stages.edit",$viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }

    public function updateStages(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'short_description' => 'required'
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


        $caseStages = CaseStages::where('unique_id',$id)->first();
        $caseStages->name  = $request->name;
        $caseStages->short_description = $request->short_description;
        $caseStages->fees  = $request->fees;
        $caseStages->save();

        $response['status'] = true;
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    public function deleteStages($id)
    {
        $case_document = CaseStages::where('unique_id',$id)->first();
        CaseStages::deleteRecord($case_document->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function workflow($id)
    {
        $case = CaseWithProfessionals::where('unique_id',$id)->first();
        $retainAgreement = CaseRetainAgreements::where('professional_case_id',$case->id)->first();
        $viewData['pageTitle'] = "Workflow";
        $viewData['case_id'] = $id;
        $viewData['retainAgreement'] = $retainAgreement;
        $view = view("admin-panel.08-cases.case-with-professionals.stages.workflow-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function generateWorkflow(Request $request){
        // Business logic for generating workflow stages and sub-stages has been moved to CaseStageService
        $case_id = $request->input("case_id");
        $case = $this->getCaseOrFail($case_id);
       
        $retainAgreement = CaseRetainAgreements::where('professional_case_id',$case->id)->first();
        // $apiData['user_id'] = (string) auth()->user()->unique_id;
        // $apiData['visa_type'] = $case->subServices->name??'';
        
        // $apiData['service_type'] = $case->subServicesTypes->subServiceTypes->name??'';
        // $apiData['case_description'] = $case->case_description??'';
        // $apiData['workflow_description'] = $retainAgreement->agreement;
        // $apiData['destination_country'] = 'Canada';
        // $apiData['bot_type'] = 'new_bot';


        $arrayData = [
            'service' => $case->services->name??'',
            'sub_service' => $case->subServices->name??'',
            'retain_agreement' => $retainAgreement->agreement??'',
            'case_description' => $case->case_description??'',
        ];
        $parameters['case_stages_summary'] = json_encode($arrayData);
        $apiData = [
            'parameters' => $parameters,
        ];
        $apiResponse = assistantApiCall('ai-agents/generate-case-stages', $apiData);
        pre($apiResponse);
        if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
            pre($apiResponse);
            // if(!empty($apiResponse['workflow'])){
            //     $this->caseStageService->generateWorkflowStages($case, $apiResponse, auth()->user()->id);
            // }
            // $response['status'] = true;
            // $response['message'] = "Success fully";

        }else{
            $response['status'] = false;
            $response['message'] = "Please generate again";
        }

        return response()->json($response);
    }

    public function addWorkflow($case_id)
    {
        $viewData['pageTitle'] = "Add Workflow";
        $viewData['case_id'] = $case_id;
        $viewData['predefinedCaseStages'] = PredefinedCaseStages::orderBy('id','desc')->get();
        $view = view("admin-panel.08-cases.case-with-professionals.stages.add-workflow",$viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }

    // public function saveWorkflow(Request $request)
    // {
    //     $predefinedCaseStages = PredefinedCaseStages::with(['predefinedCaseSubStages'])->where('id',$request->workflow_id)->first();

    //     $case = CaseWithProfessionals::where('unique_id',$request->case_id)->first();

    //     $exsiting_stages = CaseStages::where('case_id',$case->id)->where('predefined_case_stage_id',$predefinedCaseStages->id)->first();

    //     $case_stage_id = "";
    //     if(empty($exsiting_stages)){
           
    //         $caseStages = CaseStages::create([
    //             'unique_id' => randomNumber(),
    //             'name' => $predefinedCaseStages->name,
    //             'short_description' => $predefinedCaseStages->short_description,
    //             'fees' => $predefinedCaseStages->fees,
    //             'stage_type' => 'custom',
    //             'user_id' => $predefinedCaseStages->user_id,
    //             'case_id' => $case->id,
    //             'sort_order' => $predefinedCaseStages->sort_order,
    //             'status' => $predefinedCaseStages->status,
    //             'predefined_case_stage_id' => $predefinedCaseStages->id,
    //             'added_by' => auth()->user()->id,
    //         ]);
    //         $case_stage_id = $caseStages->id;
    //     }else{
    //         $case_stage_id = $exsiting_stages->id;
    //     }

    //     foreach($predefinedCaseStages->predefinedCaseSubStages as $value){
            
    //         $existing_sub_stages = CaseSubStages::where('case_id',$case->id)->where('stage_id',$case_stage_id)->where('predefined_case_sub_stage_id',$value->id)->first();
    //         if(empty($existing_sub_stages)){
    //             $object = new CaseSubStages();
    //             $object->case_id = $case->id;
    //             $object->client_id = $case->client_id;
    //             $object->user_id = $value->user_id;
    //             $object->unique_id = randomNumber();
    //             $object->stage_id = $case_stage_id;
    //             $object->name = $value->name;
    //             $object->stage_type = $value->stage_type;
    //             $object->status = $value->status;
    //             $object->predefined_case_sub_stage_id = $value->id;
    //             $object->sort_order = $value->sort_order;
    //             $object->case_documents = $value->case_documents;
    //             $object->type_id = $value->type_id;
    //             $object->save();
    //         }
           
    //     }

    //     $response['status'] = true;
    //     $response['message'] = "Record added successfully";

    //     return response()->json($response);
        
    // }
   
    public function saveWorkflow(Request $request)
    {
        if(empty($request->workflow_id)){
            $response['status'] = false;
            $response['message'] = "Please select stages";
        }else{

            foreach($request->workflow_id as $row){
                $predefinedCaseStages = PredefinedCaseStages::with(['predefinedCaseSubStages'])->where('id',$row)->first();

                $case = $this->getCaseOrFail($request->case_id);

                $exsiting_stages = CaseStages::where('case_id',$case->id)->where('predefined_case_stage_id',$predefinedCaseStages->id)->first();

                $case_stage_id = "";
                if(empty($exsiting_stages)){
                    $stageData = [
                        'stage_name' => $predefinedCaseStages->name,
                        'description' => $predefinedCaseStages->short_description,
                        'stage_fees' => $predefinedCaseStages->fees,
                        'stage_type' => 'custom',
                        'user_id' => $predefinedCaseStages->user_id,
                        'case_id' => $case->id,
                        'sort_order' => $predefinedCaseStages->sort_order,
                        'status' => $predefinedCaseStages->status,
                        'predefined_case_stage_id' => $predefinedCaseStages->id,
                        'added_by' => auth()->user()->id,
                    ];
                    $caseStages = $this->caseStageService->createStage($case, $stageData, auth()->user()->id);
                    $case_stage_id = $caseStages->id;
                }else{
                    $case_stage_id = $exsiting_stages->id;
                }

                foreach($predefinedCaseStages->predefinedCaseSubStages as $value){
                    $existing_sub_stages = CaseSubStages::where('case_id',$case->id)->where('stage_id',$case_stage_id)->where('predefined_case_sub_stage_id',$value->id)->first();
                    if(empty($existing_sub_stages)){
                        $subStageData = [
                            'client_id' => $case->client_id,
                            'user_id' => $value->user_id,
                            'unique_id' => randomNumber(),
                            'stage_id' => $case_stage_id,
                            'name' => $value->name,
                            'stage_type' => $value->stage_type,
                            'status' => $value->status,
                            'predefined_case_sub_stage_id' => $value->id,
                            'sort_order' => $value->sort_order,
                            'case_documents' => $value->case_documents,
                            'type_id' => $value->type_id,
                        ];
                        $this->caseStageService->createSubStage($case, (object)['id' => $case_stage_id], $subStageData, $value->user_id);
                    }
                }
            }
           

            $response['status'] = true;
            $response['message'] = "Record added successfully";

        }
        
        return response()->json($response);
        
    }
   
    public function updateSorting(Request $request){
       
        $stageId = $request->stageId;
 
        if (is_array($stageId)) {
            foreach ($stageId as $index => $id) {
                CaseStages::where('unique_id', $id)->update(['sort_order' => $index + 1]);
            }

            return response()->json(['status' => 'success', 'message' => 'Order updated successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
    }


    public function viewSubStage($sub_stage_id)
    {
        $caseSubStages = CaseSubStages::where("unique_id", $sub_stage_id)->first();
        $case = CaseWithProfessionals::where('id',$caseSubStages->case_id)->first();
        if($caseSubStages->stage_type == "fill-form"){
            $forms = Forms::where('id',$caseSubStages->type_id)->first();
            $viewData['case_id'] = $case->unique_id;
            $viewData['pageTitle'] = "View My Case Details";
            $viewData['record'] = $caseSubStages;
            $parsed = $this->parseFormReply($caseSubStages->form_json, $caseSubStages->form_reply);
            $viewData['last_saved'] = $parsed['last_saved'];
            $viewData['form_json'] = $parsed['form_json'];
            $viewData['form'] = $forms;
        }

        if($caseSubStages->stage_type == "case-document"){
            $viewData['case_id'] = $case->unique_id;
            $viewData['pageTitle'] = "View My Case Details";
            $uploadedDocs = json_decode($caseSubStages->form_reply, true);
            $viewData['uploadedDocs'] = $uploadedDocs;
            $viewData['record'] = $caseSubStages;
        }
       
        return view('admin-panel.08-cases.case-with-professionals.stages.view-sub-stages', $viewData);
    }

    public function markAsComplete(Request $request)
    {
        
        $caseStages = CaseStages::where('unique_id',$request->id)->first();

        $caseSubStages = CaseSubStages::where('stage_id',$caseStages->id)->get();

        $completed_count =  $caseSubStages->where('status','complete')->count();
        $total_count =  $caseSubStages->count();

        if($caseSubStages->isEmpty()){
            $response['status'] = false;
            $response['message'] = "Please add task and complete it.";
        }else{
            if($completed_count != $total_count){
                $response['status'] = false;
                $response['message'] = "Please complete all task.";
            }else{
                CaseStages::where('unique_id',$request->id)->update(['status' => 'complete']);
                $response['status'] = true;
                $response['message'] = "Record updated successfully";

                $nextStage = CaseStages::where('id', '>', $caseStages->id)
                    ->where('case_id',$caseStages->case_id)
                    ->where('status','!=','complete')
                    ->orderBy('sort_order', 'asc')
                    ->first();
                if(!empty($nextStage)){
                    $nextStage->status = 'in-progress';
                    $nextStage->save();
                }   
            }   
        }
       
        return response()->json($response);
    }

    // public function fillSubStage($stage_id)
    // {   
    //     $records = CaseSubStages::where('unique_id',$stage_id)->first();

    //     $other_document_folders = CaseFolders::where('case_id', $records->case_id)
    //                 ->whereIN('id', json_decode($records->folder_id,true))
    //                 ->get();

    //     if($records->case_documents != ''){
    //         $documents =  json_decode($records->case_documents,true);

    //         $folder = array_keys($documents);
    //         $files = Arr::flatten(array_values($documents));
    //         $case_documents = CaseDocuments::whereIn('folder_id',$folder)->whereIn('id',$files)->get();
    //     }else{
    //         $case_documents = [];
    //     }
       
       
    //     $case = CaseWithProfessionals::where('id',$records->case_id)->first();
    //     $viewData['records'] = $records;
    //     $viewData['case_id'] = $case->unique_id;
    //     $viewData['other_document_folders'] = $other_document_folders;
    //     $viewData['case_documents'] = $case_documents;
    //     $view = View::make('admin-panel.08-cases.case-with-professionals.stages.sub-stages-fill', $viewData);
    //     $contents = $view->render();
    //     $response['contents'] = $contents;
    //     return response()->json($response);
    // }
    public function fillSubStage($sub_stage_id)
    {   
        $records = CaseSubStages::where('unique_id',$sub_stage_id)->first();

        $case_documents = [];
        $other_document_folders = [];
        if($records->stage_type == "case-document"){
            $other_document_folders = CaseFolders::where('case_id', $records->case_id)
                    ->whereIN('id', json_decode($records->folder_id,true))
                    ->get();

            if($records->case_documents != ''){
                $documents =  json_decode($records->case_documents,true);

                $folder = array_keys($documents);
                $files = Arr::flatten(array_values($documents));
                $case_documents = CaseDocuments::whereIn('folder_id',$folder)->whereIn('id',$files)->get();
            }else{
                $case_documents = [];
            }
        }
        
        if($records->stage_type == "payment"){
           $viewData['invoice'] =  Invoice::where('reference_id',$records->id)->first();
        }   
       
       
        $case = CaseWithProfessionals::where('id',$records->case_id)->first();
        $viewData['records'] = $records;
        $viewData['case_id'] = $case->unique_id;
        $viewData['other_document_folders'] = $other_document_folders;
        $viewData['case_documents'] = $case_documents;
        $viewData['form'] = Forms::where('id',$records->form_id)->first();
       
        $view = View::make('admin-panel.08-cases.case-with-professionals.stages.sub-stages-fill', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }  

    public function viewForm($sub_stage_id)
    {
            $records = CaseSubStages::where('unique_id',$sub_stage_id)->first();
            $case = CaseWithProfessionals::where('id',$records->case_id)->first();
            $forms = Forms::where('id',$records->form_id)->first();
            $viewData['case_id'] = $case->unique_id;
            $viewData['pageTitle'] = "View My Case Details";
            $viewData['record'] = $records;
            $parsed = $this->parseFormReply($records->form_json, $records->form_reply);
            $viewData['last_saved'] = $parsed['last_saved'];
            $viewData['form_json'] = $parsed['form_json'];
            $viewData['form'] = $forms;


            return view('admin-panel.08-cases.case-with-professionals.stages.submited-form', $viewData);
            
        }

    /**
     * Handle validation error response in a consistent way.
     */
    private function validationErrorResponse($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return response()->json([
            'status' => false,
            'message' => $errMsg
        ]);
    }

    /**
     * Fetch a case by unique_id or abort with 404.
     */
    private function getCaseOrFail($case_id)
    {
        $case = CaseWithProfessionals::where('unique_id', $case_id)->first();
        if (!$case) {
            abort(404, 'Case not found');
        }
        return $case;
    }

    /**
     * Parse form reply and return form_json, last_saved, and form_reply array.
     */
    private function parseFormReply($form_json_raw, $form_reply_raw)
    {
        $form_json = [];
        $last_saved = '';
        $form_reply = [];
        if ($form_reply_raw != '') {
            $form_json = json_decode($form_json_raw, true);
            $postData = json_decode($form_reply_raw, true);
            $last_saved = trim($form_reply_raw);
            foreach ($form_json as $form) {
                $temp = $form;
                if (isset($form['name']) && isset($postData[$form['name']])) {
                    if (isset($form['values'])) {
                        $values = $form['values'];
                        $final_values = [];
                        foreach ($values as $value) {
                            $tempVal = $value;
                            if (is_array($postData[$form['name']])) {
                                if (in_array($value['value'], $postData[$form['name']])) {
                                    $tempVal['selected'] = 1;
                                } else {
                                    $tempVal['selected'] = 0;
                                }
                            } else {
                                if ($value['value'] == $postData[$form['name']]) {
                                    $tempVal['selected'] = 1;
                                    if ($form['type'] == 'autocomplete') {
                                        $temp['value'] = $value['value'];
                                    }
                                } else {
                                    $tempVal['selected'] = 0;
                                }
                            }
                            $final_values[] = $tempVal;
                        }
                        $temp['values'] = $final_values;
                    } else {
                        $temp['value'] = $postData[$form['name']];
                    }
                }
                $form_reply[] = $temp;
            }
            $form_json = json_encode($form_reply);
        }
        return [
            'form_json' => $form_json,
            'last_saved' => $last_saved,
            'form_reply' => $form_reply
        ];
    }

    // public function generateStagesViaAi($case_id)
    // {
    //     $case = $this->getCaseOrFail($case_id);
    //     $viewData['case_id'] = $case->unique_id;
    //     $viewData['pageTitle'] = "Generate Stages via AI";
    //     $view = view('admin-panel.08-cases.case-with-professionals.stages.generate-case-stages-via-ai', $viewData);
    //     $contents = $view->render();
    //     $response['status'] = true;
    //     $response['contents'] = $contents;
    //     return response()->json($response);
    // }
}