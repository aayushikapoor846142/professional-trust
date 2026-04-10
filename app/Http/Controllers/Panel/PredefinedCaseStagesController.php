<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\ModuleAction;
use App\Models\Module;
use App\Models\Action;
use App\Models\RolePrevilege;
use App\Models\PredefinedCaseStages;
use App\Models\PredefinedCaseSubStages;

class PredefinedCaseStagesController extends Controller
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
            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $records = PredefinedCaseStages::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
            })->with(['userAdded'])
            ->where('added_by',auth()->user()->id)
                     ->orderBy($sortColumn, $sortDirection)
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
    public function add()
    {
        $viewData['pageTitle'] = "Add Predefined Case Stages";
        return view('admin-panel.08-cases.predefined-case-stages.add', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        $validator = $this->validatePredefinedCaseStage($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        PredefinedCaseStages::create([
            'unique_id' => randomNumber(),
            'name' => $request->input('name'),
            'short_description' => $request->input('short_description'),
            'fees' => $request->input('fees'),
            'sort_order' => $request->input('sort_order'),
            'stage_type' => 'custom',
            'user_id' => auth()->user()->id,
            'added_by' => auth()->user()->id,
        ]);

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('predefined-case-stages');
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
    public function edit($id)
    {
        $record = $this->findByUniqueIdOrFail($id);
        $viewData['predefined_case_stages'] = $record;
        $viewData['pageTitle'] = "Edit Predefined Case Stages";
        return view('admin-panel.08-cases.predefined-case-stages.edit', $viewData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $object = $this->findByUniqueIdOrFail($id);
        $validator = $this->validatePredefinedCaseStage($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $object->name = $request->input('name');
        $object->short_description = $request->input('short_description');
        $object->fees = $request->input('fees');
        $object->sort_order = $request->input('sort_order');
        $object->save();

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('predefined-case-stages');
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteSingle($id)
    {
        $action = $this->findByUniqueIdOrFail($id);
        PredefinedCaseStages::deleteRecord($action->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    /**
     * Remove multiple Country from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        foreach ($ids as $uniqueId) {
            $act = PredefinedCaseStages::where('unique_id', $uniqueId)->first();
            if ($act) {
                PredefinedCaseStages::deleteRecord($act->id);
            }
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    public function view($id)
    {
        $record = $this->findByUniqueIdOrFail($id);
        $viewData['predefined_case_stages'] = $record;
        $viewData['pageTitle'] = "Edit Predefined Case Stages";
        return view('admin-panel.08-cases.predefined-case-stages.view', $viewData);
        // return view('admin-panel.08-cases.predefined-case-stages.view-backup', $viewData);
    }

    public function views($id)
    {
        $record = $this->findByUniqueIdOrFail($id);
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Predefined Case Stages";

        $view = view('admin-panel.08-cases.predefined-case-stages.view-stages', $viewData);
        $contents = $view->render();

        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    // --- Private Helper Methods ---

    private function validatePredefinedCaseStage(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required',
            'short_description' => 'required'
        ]);
    }

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
        ], 422);
    }

    private function findByUniqueIdOrFail($id)
    {
        $record = PredefinedCaseStages::where('unique_id', $id)->first();
       
        return $record;
    }


    public function markAsComplete(Request $request)
    {
        
        $caseStages = PredefinedCaseStages::where('unique_id',$request->id)->first();
        $caseSubStages = PredefinedCaseSubStages::where('predefined_case_stage_id',$caseStages->id)->get();

        $completed_count =  $caseSubStages->where('status','complete')->count();
        $total_count =  $caseSubStages->count();

        if($caseSubStages->isEmpty()){
            $response['status'] = false;
            $response['message'] = "Please add task and complete it.";
        }else{
            if($completed_count != $total_count){
                $response['status'] = false;
                $response['message'] = "Please mark as sub stages as complete first.";
            }else{
                PredefinedCaseStages::where('unique_id',$request->id)->update(['status' => 'complete']);
                $response['status'] = true;
                $response['message'] = "Record updated successfully";
            }   
        }
       
        return response()->json($response);
    }
}
