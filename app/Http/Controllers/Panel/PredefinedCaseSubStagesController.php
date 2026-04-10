<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Forms;
use App\Models\DocumentsFolder;
use App\Models\PredefinedCaseStages;
use App\Models\PredefinedCaseSubStages;

class PredefinedCaseSubStagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $viewData['pageTitle'] = "Predefined Case Stages";
        return view('admin-panel.08-cases.predefined-case-stages.lists', $viewData);
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $records = PredefinedCaseStages::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
            })->with(['userAdded'])
            ->where('added_by',auth()->user()->id)
            ->orderBy('id', "desc")
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.08-cases.predefined-case-stages.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add(Request $request)
    {
        $viewData['pageTitle'] = "Add Sub Stage";
        $stage = PredefinedCaseStages::where('unique_id',$request->stage_id)->first();
        $this->fillFormsAndDocuments($viewData);
        $viewData['stage_id'] = $request->stage_id;
        $viewData['action'] = 'add_sub_segment';
        $view = view('admin-panel.08-cases.predefined-case-stages.add-sub-stages', $viewData);
        $contents = $view->render();

        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $stage_id = $request->input("stage_id");
        $stage = PredefinedCaseStages::where("id",$stage_id)->first();
        if (!$stage) {
            return $this->notFoundResponse('Stage not found');
        }

        $object = new PredefinedCaseSubStages();
        $this->fillSubStage($object, $request, $stage->id);
        $object->save();

        $response['status'] = true;
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,$id)
    {
        $record = PredefinedCaseSubStages::where('unique_id',$id)->first();
        if (!$record) {
            return $this->notFoundResponse('Record not found');
        }
        $this->fillFormsAndDocuments($viewData);
        $viewData['stage_id'] = $record->predefined_case_stage_id;
        $viewData['record'] = $record;
        $viewData['action'] = 'edit_sub_segment';
        $view = view('admin-panel.08-cases.predefined-case-stages.edit-sub-stages', $viewData);
        $contents = $view->render();

        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $object = PredefinedCaseSubStages::where('unique_id',$id)->first();
        if (!$object) {
            return $this->notFoundResponse('Record not found');
        }
        $this->fillSubStage($object, $request, $object->predefined_case_stage_id);
        $object->save();

        $response['status'] = true;
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteSingle($id)
    {
        $action = PredefinedCaseSubStages::where('unique_id',$id)->first();
        if (!$action) {
            return $this->notFoundResponse('Record not found');
        }
        PredefinedCaseSubStages::deleteRecord($action->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function view($unique_id)
    {
        $record = PredefinedCaseSubStages::where("unique_id", $unique_id)->first();
        if (!$record) {
            return $this->notFoundResponse('Record not found');
        }
        $viewData['record'] = $record;
        $view = view('admin-panel.08-cases.predefined-case-stages.view-sub-stages', $viewData);
        $contents = $view->render();

        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function updateSorting(Request $request)
    {
        $segments = $request->segments;
        foreach($segments as $segment){
            PredefinedCaseSubStages::where("id",$segment['id'])->update(['sort_order'=>$segment['position']]);
        }
        $response['status'] = true;
        $response['message'] = 'Segment order updated';

        return response()->json($response);
    }

    // --- DRY & Business Logic Helpers ---

    private function fillFormsAndDocuments(&$viewData)
    {
        $viewData['forms'] = Forms::where('added_by',auth()->user()->id)->get();
        $viewData['default_documents'] = DocumentsFolder::where('user_id',auth()->user()->id)->get();
    }

    private function validationRules()
    {
        return [
            'name' => 'required',
            'stage_type' => 'required|string|in:fill-form,case-document',
            'form_id' => 'required_if:stage_type,fill-form|nullable|exists:forms,id',
            'default_documents' => 'required_if:stage_type,case-document|array',
        ];
    }

    private function validationErrorResponse($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = array();
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        $response['status'] = false;
        $response['message'] = $errMsg;
        return response()->json($response);
    }

    private function notFoundResponse($message)
    {
        $response['status'] = false;
        $response['message'] = $message;
        return response()->json($response, 404);
    }

    private function fillSubStage($object, $request, $stageId)
    {
        $object->user_id = auth()->user()->id;
        if (!$object->exists) {
            $object->unique_id = randomNumber();
        }
        $object->predefined_case_stage_id = $stageId;
        $object->name = $request->input("name");
        $object->stage_type = $request->input("stage_type");
        $object->sort_order = $request->input("sort_order")??0;
        $object->status = $object->status ?? 'pending';
        $object->type_id = $request->input("stage_type") == "fill-form" ? $request->input("form_id") : null;
        if($request->input("stage_type") == 'case-document'){
            $case_document = array();
            if($request->input("default_documents")){
                $case_document['default_documents'] = $request->input("default_documents");
            }
            if(!empty($case_document)){
                $object->case_documents = json_encode($case_document);
            }
        }else{
            $object->case_documents = '';
        }
    }

    public function markAsComplete(Request $request)
    {
        $sub_stage = PredefinedCaseSubStages::where('unique_id',$request->id)->first();
        if (!$sub_stage) return ["status" => false, "message" => "SubStage not found"];
        PredefinedCaseSubStages::where('unique_id',$request->id)->update(['status' => 'complete']);
        return ["status" => true, "message" => "Record updated successfully"];
    }
}
