<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfessionalServices;
use View;
use Illuminate\Support\Facades\Validator;
use App\Models\CaseStages;
use App\Models\CaseWithProfessionals;
use App\Models\Forms;
use App\Models\DocumentsFolder;
use App\Models\CaseFolders;
use App\Models\CaseSubStages;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentLinkParameter;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Services\CaseSubStageService;

class CaseSubStagesController extends Controller
{
    protected $caseSubStageService;

    public function __construct(CaseSubStageService $caseSubStageService)
    {
        $this->caseSubStageService = $caseSubStageService;
    }
    

    // public function stagesList($case_id)
    // {
    //     $viewData['pageTitle'] = "Case Stages";
    //     $viewData['case_id'] = $case_id;
    //     return view('admin-panel.08-cases.case-with-professionals.stages.lists', $viewData);
    // }


    // public function getStagesAjaxList(Request $request)
    // {
    //     // $search = $request->input("search");
    //     $case = CaseWithProfessionals::where('unique_id',$request->case_id)->first();
    //     $records = CaseStages::where('case_id',$case->id)
    //         ->where('user_id',auth()->user()->id)
    //         ->orderBy('id', "desc")
    //         ->get();

    //     $viewData['records'] = $records;
    //     $viewData['case_id'] = $request->case_id;
    //     $view = View::make('admin-panel.08-cases.case-with-professionals.stages.ajax-list', $viewData);
    //     $contents = $view->render();
    //     $response['contents'] = $contents;
    //     return response()->json($response);
    // }

    public function addSubStages($stage_id)
    {
        $viewData = $this->caseSubStageService->getAddSubStageData($stage_id, auth()->user());
        $view = view("admin-panel.08-cases.case-with-professionals.sub-stages.add", $viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }
   
    public function saveSubStages(Request $request,$stage_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'stage_type' => 'required|string|in:fill-form,case-document,payment',
            'form_id' => 'required_if:stage_type,fill-form|nullable|exists:forms,id',
            'folder' => 'required_if:stage_type,case-document|array',
            'fees' => 'required_if:stage_type,payment',
            'payment_description' => 'required_if:stage_type,payment'
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

        $result = $this->caseSubStageService->saveSubStage($request->all(), $stage_id, auth()->user());
        return response()->json($result);
    }
    private function generateSignature($params)
    {
        ksort($params); // Sort parameters
        $string = http_build_query($params);
        return hash_hmac('sha256', $string, apiKeys('STRIPE_SECRET'));
    }
    public function editSubStages($id)
    {
        $viewData = $this->caseSubStageService->getEditSubStageData($id, auth()->user());
        $view = view("admin-panel.08-cases.case-with-professionals.sub-stages.edit", $viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }

    public function updateSubStages(Request $request,$id)
    {
       
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'stage_type' => 'required|string|in:fill-form,case-document,payment',
            'form_id' => 'required_if:stage_type,fill-form|nullable|exists:forms,id',
            'folder' => 'required_if:stage_type,case-document|array',
            'fees' => 'required_if:stage_type,payment'
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

        $result = $this->caseSubStageService->updateSubStage($request->all(), $id, auth()->user());
        return response()->json($result);
    }

    public function deleteSubStages($id)
    {
        $result = $this->caseSubStageService->deleteSubStage($id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function markAsComplete(Request $request)
    {
        $result = $this->caseSubStageService->markSubStageAsComplete($request->id);
        return response()->json($result);
    }

    public function updateSorting(Request $request)
    {
        $result = $this->caseSubStageService->updateSubStageSorting($request->subStageId);
        return response()->json($result);
    }
   
}