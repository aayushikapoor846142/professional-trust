<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubServicesTypes;
use App\Models\ProfessionalSubServices;
use App\Models\ProfessionalServices;
use App\Models\Forms;
use App\Models\ProfessionalServicesFees;
use App\Models\ServiceAssesmentForm;
use View;
use App\Models\CaseWithProfessionals;
use App\Models\ServiceSendForm;
use DB;

use Illuminate\Support\Str;
use App\Models\DocumentsFolder;
use App\Models\ServiceFormReply;
use App\Models\ImmigrationServices;
use App\Services\ProfessionalSubServiceManager;

class ProfessionalSubServicesController extends Controller
{
    /**
     * @var ProfessionalSubServiceManager
     */
    protected $manager;

    /**
     * Inject the ProfessionalSubServiceManager service.
     */
    public function __construct(ProfessionalSubServiceManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Display a listing of the sub services.
     */
    public function index($id)
    {
        try {
            $service = $this->findProfessionalServiceOrFail($id);
            $viewData['pageTitle'] = "Sub Services";
            $viewData['id'] = $id;
            return view('admin-panel.04-profile.user-services.sub-services.lists', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load sub services.');
        }
    }

    /**
     * Get AJAX list of sub services.
     */
    public function getAjaxList(Request $request)
    {
        try {
            $search = $request->input("search");
            $service = $this->findProfessionalServiceOrFail($request->id);
            $service_id = $service->id;
            $records = ProfessionalSubServices::with(['forms','subServiceTypes'])
                ->orderBy('id', 'desc')
                ->where(function ($query) use ($search,$service_id) {
                    if ($search != '') {
                        $query->where("name", "LIKE", "%" . $search . "%");
                    }
                    $query->where('professional_service_id',$service_id);
                })
                ->visibleToUser(auth()->user()->id)
                ->paginate();
            $viewData['checkedProfServiceIds'] = CaseWithProfessionals::where('professional_id', auth()->user()->id)
                ->pluck('service_type_id')
                ->toArray();
            $viewData['records'] = $records;
            $view = view('admin-panel.04-profile.user-services.sub-services.ajax-list',$viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new sub service.
     */
    public function addSubService($id)
    {
        try {
            $service = $this->findProfessionalServiceOrFail($id);
            $type_ids = ProfessionalSubServices::where('service_id',$service->service_id)->get()->pluck('sub_services_type_id')->toArray();
            $viewData['pageTitle'] = "Add Sub Service";
            $viewData['service_id'] = $id;
            $viewData['sub_services_types'] = SubServicesTypes::whereNotIn('id',$type_ids)->orderBy('id','desc')->get();
            $viewData['forms'] = Forms::orderBy('id','desc')->where('added_by', auth()->user()->id)->get();
            $viewData['documents'] = DocumentsFolder::where('added_by',auth()->user()->id)->where('user_id',auth()->user()->id)->get();
            return view('admin-panel.04-profile.user-services.sub-services.add', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load add sub service form.');
        }
    }

    /**
     * Fetch ProfessionalServices by unique_id or fail.
     */
    private function findProfessionalServiceOrFail($id)
    {
        $service = ProfessionalServices::where('unique_id', $id)->first();
        if (!$service) {
            abort(404, 'Professional Service not found');
        }
        return $service;
    }

    /**
     * Fetch ProfessionalSubServices by unique_id or fail.
     */
    private function findProfessionalSubServiceOrFail($id)
    {
        $subService = ProfessionalSubServices::where('unique_id', $id)->first();
        if (!$subService) {
            abort(404, 'Professional Sub Service not found');
        }
        return $subService;
    }

    /**
     * Format validation errors for JSON response.
     */
    private function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Standard JSON response for errors.
     */
    private function jsonError($message, $error_type = 'error', $status = false)
    {
        return response()->json([
            'status' => $status,
            'error_type' => $error_type,
            'message' => $message
        ]);
    }

    public function saveSubService(Request $request,$id)
    {
        try {
            if(!empty($request->schedule)){
                if(array_sum(array_map('intval', $request->schedule)) < $request->professional_fees){
                    $pending = array_sum(array_map('intval', $request->schedule)) - $request->professional_fees;
                    return $this->jsonError('Your schedule is less than professional fees. Your Pending Amount is '.$pending, 'professional_fees');
                }
                if(array_sum(array_map('intval', $request->schedule)) > $request->professional_fees){
                    return $this->jsonError('Your schedule is more than professional fees', 'professional_fees');
                }
            }
            $validator = Validator::make($request->all(), [
                'sub_services_type_id' => 'required',
                'professional_fees' => 'required',
                'consultancy_fees' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->jsonError($this->formatValidationErrors($validator), 'validation');
            }
            $service = $this->findProfessionalServiceOrFail($id);
            $data = $request->all();
            $data['user_id'] = \Auth::user()->id;
            $subService = $this->manager->createSubService($service, $data);
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('my-services');
            $response['message'] = "Record added successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Edit a sub service.
     */
    public function editSubService($id)
    {
        try {
            $record = $this->findProfessionalSubServiceOrFail($id);
            if (! $record->isEditableBy(auth()->id())) {
                return handleUnauthorizedAccess('You are not authorized to edit this Page');
            }
            $viewData['record']  = $record;
            $viewData['documents'] = DocumentsFolder::where('user_id',auth()->user()->id)->get();
            $viewData['sub_services_types'] = SubServicesTypes::orderBy('id','desc')->get();
            $viewData['forms'] = Forms::orderBy('id','desc')->where('added_by', auth()->user()->id)->get();
            $viewData['pageTitle'] = "Edit Sub Services";
            return view('admin-panel.04-profile.user-services.sub-services.edit', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load edit sub service form.');
        }
    }

    public function updateSubService($id, Request $request)
    {
        try {
            $object = ProfessionalSubServices::where('unique_id',$id)->first();
            $validator = Validator::make($request->all(), [
                'sub_services_type_id' => 'required',
                'consultancy_fees' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->jsonError($this->formatValidationErrors($validator), 'validation');
            }
            $data = $request->all();
            $this->manager->updateSubService($object, $data);
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('my-services');
            $response['message'] = "Record updated successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Delete a sub service.
     */
    public function deleteSubService($id)
    {
        try {
            $services = $this->findProfessionalSubServiceOrFail($id);
            if (! $services->isEditableBy(auth()->id())) {
              return handleUnauthorizedAccess('You are not authorized to edit this Page');
            }
            $this->manager->deleteSubService($services);
            return redirect()->back()->with("success", "Record deleted successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "An unexpected error occurred: " . $e->getMessage());
        }
    }

    /**
     * Show the form for adding a document folder.
     */
    public function addDocumentFolder()
    {
        try {
            $viewData['pageTitle'] = "Add Folder";
            $view = view("admin-panel.04-profile.user-services.sub-services.add-document-folder",$viewData);
            $response['contents'] = $view->render();
            $response['status'] = true;
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function saveDocumentFolder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|input_string|max:255'
            ]);
            if ($validator->fails()) {
                return $this->jsonError($this->formatValidationErrors($validator), 'validation');
            }
            $data = $request->all();
            $data['user_id'] = \Auth::user()->id;
            $folder = $this->manager->createDocumentFolder($data);
            $response['status'] = true;
            $response['records'] = DocumentsFolder::where('user_id',\Auth::user()->id)->orderBy('id','desc')->get();
            $response['redirect_back'] = baseUrl('document-folders');
            $response['message'] = "Record added successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Generate assessment form view.
     */
    public function generateAssessment($id)
    {
        try {
            $record = $this->findProfessionalServiceOrFail($id);
            if (! $record->isEditableBy(auth()->id())) {
               return handleUnauthorizedAccess('You are not authorized to edit this Page');
            }
            $viewData['record'] = $record;
            $viewData['pageTitle'] = "Generate Assessment Using AI";
            $viewData['id'] = $id;
            return view('admin-panel.04-profile.user-services.sub-services.generate-assessment', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load assessment generation form.');
        }
    }

    public function submitGenerateAssessment($id,Request $request){
        try {
            $prof_sub_service = ProfessionalServices::where('unique_id',$id)->first();
            $msg = $request->message;

            if($request->is_modify == "no"){
                $message = "I m providing immigration service for ".($prof_sub_service->subServices->parentService->name??'')." ".$prof_sub_service->subServices->name??''.". ".$msg;

            }else{
                $message = $msg;
            }
           
            $form_type = $request->form_type;
            $apiData['user_id'] = (string) auth()->user()->unique_id;
            $apiData['message'] = $message;
            $apiData['service_id'] = (string) $prof_sub_service->service_id;
            $apiData['form_type'] = (string) $request->form_type;

          
            $apiResponse = assistantApiCall('application_form', $apiData);
            
            if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
                $sample_json = formJsonSample();
                $json_sample = array();
                foreach($sample_json as $js){
                    $json_sample[$js['fields']] = $js;
                }
                $form_json = array();
                if($request->form_type == 'step_form'){
                    $data_arr = $apiResponse['message'];
                    $form_name = $data_arr['form_name'];
                    // $form_name = "Step Form ".mt_rand();
                    $steps = $data_arr['steps'];
                    $index = 0;
                    $fg_index = 0;
                    foreach($steps as $step){
                        $groupFields = array();
                        $fg_field_format = array();
                        $fg_field_format = $json_sample['fieldGroups'];
                        $fg_field_format['groupFields'] = $groupFields;
                        $fg_field_format['settings']['label'] = str_replace("'s","&apos;",$step['step_heading']);
                        $fg_field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                        $fg_field_format['settings']['stepHeading'] = str_replace("'s","&apos;",$step['step_heading']);
                        $fg_field_format['index'] = randomNumber();
                        $fg_index = $index;
                        $form_json[$index] = $fg_field_format;
                        $index++;
                        foreach($step['questions'] as $json){
                            $field_format = array();
                            $newOptions = array();
                            if(isset($json['options'])){
                                $options = $json['options'];
                                foreach($options as $k => $v){
                                    $options[$k] = str_replace("'s","&apos;",$v);
                                    $newOptions[mt_rand(1000,9999)] = str_replace("'s","&apos;",$v);
                                }
                                $json['options'] = $newOptions;
                            }
                            
                            if($json['type'] == 'text'){
                                $field_format = $json_sample['textInput'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'number'){
                                $field_format = $json_sample['numberInput'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'radio'){
                                $field_format = $json_sample['radio'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['options'] = $json['options'];
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'checkbox'){
                                $field_format = $json_sample['checkbox'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                
                                $field_format['settings']['options'] = $json['options'];
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'dropdown'){
                                $field_format = $json_sample['dropDown'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['options'] = $json['options'];
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'email'){
                                $field_format = $json_sample['emailInput'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'textarea'){
                                $field_format = $json_sample['textarea'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'date'){
                                $field_format = $json_sample['dateInput'];
                                $field_format['index'] =randomNumber();
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            $groupFields[] = $field_format['index'];
                            $index++;
                        }
                        $form_json[$fg_index]['groupFields'] = $groupFields;
                    }
                    $preview_form = '';
                    if(!empty($form_json)){
                        $viewData['fg_field_json'] = json_encode($form_json);
                        $viewData['form_name'] = $form_name;
                        $viewData['form_type'] = $form_type;
                        $viewData['id'] = $id;
                        $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
                    }
                    
                    $response['preview_form'] = $preview_form;
                    if(!empty($form_json)){
                        $response['fg_field_json'] = json_encode($form_json);
                    }else{
                        $response['fg_field_json'] = "";
                    }
                    $response['form_type'] = $form_type;
                    $response['status'] = true;
                }else{
                    $data_arr = $apiResponse['message'];
                  
                    if(isset($data_arr['questions'])){
                        $form_name = $data_arr['form_name'];
                        $index = 0;
                        foreach($data_arr['questions'] as $json){
                            $field_format = array();
                            $newOptions = array();
                            if(isset($json['options'])){
                                $options = $json['options'];
                               
                                foreach($options as $k => $v){
                                    $options[$k] = str_replace("'s","&apos;",$v);
                                    $newOptions[mt_rand(1000,9999)] = str_replace("'s","&apos;",$v);
                                }
                                $json['options'] = $newOptions;
                            }
                            
                            if($json['type'] == 'text'){
                                $field_format = $json_sample['textInput'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $field_format['index'] = randomNumber();
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'number'){
                                $field_format = $json_sample['numberInput'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'radio'){
                                $field_format = $json_sample['radio'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['options'] = $json['options'];
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'checkbox'){
                                $field_format = $json_sample['checkbox'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                
                                $field_format['settings']['options'] = $json['options'];
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'dropdown'){
                                $field_format = $json_sample['dropDown'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['options'] = $json['options'];
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'email'){
                                $field_format = $json_sample['emailInput'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'textarea'){
                                $field_format = $json_sample['textarea'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            if($json['type'] == 'date'){
                                $field_format = $json_sample['dateInput'];
                                $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                $form_json[$index] = $field_format;
                            }
                            $index++;
                        }
                        $preview_form = '';
                        if(!empty($form_json)){
                            $viewData['fg_field_json'] = json_encode($form_json);
                            $viewData['form_name'] = $form_name;
                            $viewData['form_type'] = $form_type;
                            $viewData['id'] = $id;
                            $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
                        }
                       
                        $response['preview_form'] = $preview_form;
                        if(!empty($form_json)){
                            $response['fg_field_json'] = json_encode($form_json);
                        }else{
                            $response['fg_field_json'] = "";
                        }
                        $response['form_type'] = $form_type;
                        $response['status'] = true;
                    }else{
                        $response['status'] = false;
                        $response['message'] = $data_arr['error']??'Something went try. Try again';
                    }
                }

            }else{
                $response['message'] = "Something went wrong try again";
                $response['status'] = false;
            }

            return response()->json($response);
            
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }


    public function saveGenerateAssessment($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'formName' => 'required',
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

        $professional_service = ProfessionalServices::where('unique_id',$id)->first();
        $forms = Forms::create([
            'unique_id' => randomNumber(),
            'added_by' => \Auth::user()->id,
            'name' => $request->input('formName'),
            'form_type' => $request->input('form_type'),
            'fg_field_json' => $request->input('fg_field_json'),
        ]);

        ServiceAssesmentForm::create([
            'unique_id' => randomNumber(),
            'added_by' => \Auth::user()->id,
            'professional_service_id' => $professional_service->id,
            'service_id' => $professional_service->service_id,
            'form_id' => $forms->id
        ]);

        $response['status'] = true;
        $response['message'] = "Record added successfully";
        $response['redirect_back'] = baseUrl('my-services/list-assessment/'.$id);
        return response()->json($response);
    }

    /**
     * List assessment forms.
     */
    public function listAssessment($id, Request $request)
    {
        try {
            $professional_service = $this->findProfessionalServiceOrFail($id);
            $service = ImmigrationServices::where('id',$professional_service->service_id)->first();
            $viewData['pageTitle'] = "Assesment Forms";
            $viewData['id'] = $id;
            $viewData['service_name'] = $service->name;
            return view('admin-panel.04-profile.user-services.sub-services.list-assesment-form', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load assessment list.');
        }
    }

    /**
     * AJAX list for assessment forms.
     */
    public function assessmentAjaxList(Request $request)
    {
        try {
            $search = $request->input("search");
            $professional_service = ProfessionalServices::where('unique_id',$request->id)->where('user_id',auth()->user()->id)->first();
            $form_ids = ServiceAssesmentForm::where('professional_service_id',$professional_service->id)->where('added_by',auth()->user()->id)->get()->pluck('form_id')->toArray();
            $records = Forms::where(function ($query) use ($search,$form_ids) {
                    if ($search != '') {
                        $query->where("name", "LIKE", "%" . $search . "%");
                    }
                    $query->whereIn('id',$form_ids);
                })
                ->orderBy('id', "desc")
                ->paginate();
            $viewData['records'] = $records;
            $view = View::make('admin-panel.04-profile.user-services.sub-services.assesment-ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * View a single assessment form.
     */
    public function viewAssessment($id)
    {
        try {
            $record = Forms::where("unique_id",$id)->first();
            if ($record->added_by !== auth()->user()->id) {
              return handleUnauthorizedAccess('You are not authorized to edit this Page');
            }
            $assesment_form = ServiceAssesmentForm::where('form_id',$record->id)->first();
            $professional_service = ProfessionalServices::where('id',$assesment_form->professional_service_id)->first();
            $viewData['record'] = $record;
            $last_saved = '';
            if($record !== null){
                $last_saved = '';
                $postData = array();
                $form_json = json_decode($record->fg_field_json,true);
                $form_reply = array();
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
            else{
                $form_json = $record->form_json;
            }
            $viewData['last_saved'] = $last_saved;
            $viewData['pageTitle'] = "Render Form";
            $viewData['professional_service_id'] = $professional_service->unique_id;
            return view('admin-panel.04-profile.user-services.sub-services.view-assesment',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load assessment view.');
        }
    }


    /**
     * Show the form for sending an assessment form.
     */
    public function sendAssesmentForm($id)
    {
        try {
            $viewData['pageTitle'] = "Send Mail";
            $viewData['form_id'] = $id;
            return view('admin-panel.04-profile.user-services.sub-services.send-assesment-form',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load send assessment form.');
        }
    }

    public function sendForm($form_id,Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'existing_user' => 'required|in:yes,no',
                'user_id' => 'required_if:existing_user,yes',
                'email' => 'required_if:existing_user,no',
            ]);
            if ($validator->fails()) {
                return $this->jsonError($this->formatValidationErrors($validator), 'validation');
            }
            $user = User::where('email',$request->email)->first();
            if(!empty($user)){
                return $this->jsonError('This email already exists.', 'exist');
            }
            $form = Forms::where("unique_id",$form_id)->first();
            $service_form =  ServiceAssesmentForm::where('form_id',$form->id)->where('added_by',auth()->user()->id)->first();
            $data = $request->all();
            $data['user_id'] = auth()->user()->id;
            if($request->existing_user == "yes"){
                $data['user'] = User::where("id",$request->input("user_id"))->first();
            }
            $object = $this->manager->sendForm($form, $service_form, $data);
            $response['status'] = true;
            $response['formJson'] = $object->form_fields_json;
            $response['formId'] = $object->form_id;
            $response['id'] = $object->id;
            $response['formType'] = $object->form_type;
            $response['edit_url'] = baseUrl('my-services/edit-send-form/'.$object->unique_id);
            $response['send_url'] = baseUrl('my-services/send-assesment-email/'.$object->unique_id);
            $response['message'] = 'Form send to user successfully';
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }



    /**
     * List sent assessment forms.
     */
    public function sendAssesmentFormList($id, Request $request)
    {
        try {
            $form  = Forms::where('unique_id',$id)->first();
            $assesment_form = ServiceAssesmentForm::where('form_id',$form->id)->first();
            $professional_service = ProfessionalServices::where('id',$assesment_form->professional_service_id)->first();
            $viewData['pageTitle'] = "Send Assesment Forms";
            $viewData['id'] = $id;
            $viewData['professional_service_id'] = $professional_service->unique_id;
            return view('admin-panel.04-profile.user-services.sub-services.send-form-list', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load send assessment form list.');
        }
    }

  
    /**
     * AJAX list for sent assessment forms.
     */
    public function sendAssesmentAjaxList(Request $request)
    {
        try {
            $search = $request->input("search");
            $form = Forms::where('unique_id',$request->id)->first();
            $id = $form->id;
            $records = ServiceSendForm::with(['form'])->where(function ($query) use ($search,$id) {
                    if ($search != '') {
                        $query->where("name", "LIKE", "%" . $search . "%");
                    }
                    $query->where('form_id',$id);
                })
                ->orderBy('id', "desc")
                ->paginate();
            $viewData['records'] = $records;
            $view = View::make('admin-panel.04-profile.user-services.sub-services.send-form-ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * View a sent assessment form reply.
     */
    public function viewAssessmentReply($id)
    {
        try {
            $form = ServiceSendForm::where("unique_id",$id)->first();
            $forms = Forms::where('id',$form->form_id)->first();
            $prevForm = ServiceFormReply::where("service_send_form_id",$form->id)->first();
            if(!empty($prevForm)){
                $last_saved = trim($prevForm->field_reply);
            }else{
                $last_saved = '';
            }
            $viewData['last_saved'] = $last_saved;
            $viewData['record'] = $form;
            $viewData['form'] = $form;
            $viewData['form_unique_id'] = $forms->unique_id;
            $viewData['prevForm'] = $prevForm;
            $viewData['pageTitle'] = $form->form_name;
            return view('admin-panel.04-profile.user-services.sub-services.view-assesment-reply',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load assessment reply view.');
        }
    }

    /**
     * Edit a sent form.
     */
    public function editSendForm(Request $request,$id)
    {
        try {
            $record = ServiceSendForm::where("unique_id",$id)->first();
            if ($record->added_by !== auth()->user()->id) {
             return handleUnauthorizedAccess('You are not authorized to edit this Page');
            }
            $record->form_fields_json;
            $viewData['record'] = $record;
            $viewData['pageTitle'] = "Edit Form";
            return view('admin-panel.04-profile.user-services.sub-services.edit-send-assesment-form',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load edit send form.');
        }
    }

    public function updateSendForm(Request $request,$id)
    {
        try {
            $fg_fields = $request->fg_fields;
            foreach($fg_fields as $key => $values){
                $fg_fields[$key]['index'] = $key;
                foreach($values['settings'] as $k => $v){
                    $values['settings'][$k] = str_replace("'","&apos;",$v);
                }
                $fg_fields[$key]['settings'] = $values['settings'];
            }
            $fg_fields = array_values($fg_fields);
            $object = ServiceSendForm::where("unique_id",$id)->first();
            $data = [
                'form_name' => $request->form_name,
                'form_type' => $request->form_type,
                'fg_fields' => $fg_fields
            ];
            $this->manager->updateSendForm($object, $data);
            $response['status'] = true;
            $response['message'] = "Form saved successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function sendAssesmentFormMail(Request $request,$id)
    {
        try {
            $record = ServiceSendForm::where("unique_id",$id)->first();
            if ($record->added_by !== auth()->user()->id) {
               return handleUnauthorizedAccess('You are not authorized to edit this Page');
            }
            $viewData['record'] = $record;
            $viewData['pageTitle'] = "Edit Form";
            return view('admin-panel.04-profile.user-services.sub-services.send-assesment-form-mail',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load send assessment form mail.');
        }
    }

    public function sendFormEmailToUser(Request $request,$id)
    {
        try {
            $record = ServiceSendForm::where("unique_id",$id)->first();
            $this->manager->markFormAsSent($record);
            $form = Forms::where('id',$record->form_id)->first();
            if($request->send_email == "yes"){
                $message = $request->message;
                $mailData['mail_message'] = $message;
                $mailData['name'] = '';
                $mailData['uuid'] = $record->unique_id;
                $mailData['url'] = clientTrustvisoryUrl().'/service-form-render/'.$record->unique_id;
                $view = View::make('emails.form-mail',$mailData);
                $message = $view->render();
                $parameter['to'] = $record->email;
                $parameter['to_name'] = 'Trustvisory';
                $parameter['message'] = $message;
                $parameter['subject'] ="Sent Form to Fill";
                $parameter['view'] = "emails.form-mail";
                $parameter['data'] = $mailData;
                $mailRes = sendMail($parameter);
                $response['redirect_back'] = baseUrl('my-services/send-assesment-form-list/'.$form->unique_id);
                $response['status'] = true;
                $response['message'] = "Form send successfully";
            }else{
                $response['redirect_back'] = baseUrl('my-services/send-assesment-form-list/'.$form->unique_id);
                $response['status'] = false;
                $response['message'] = "Form not send";
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function sendAssesmentFormUser(Request $request, $id)
    {
        try {
            $record = ServiceSendForm::where("unique_id",$id)->first();
            $this->manager->markFormAsSent($record);
            $form = Forms::where('id',$record->form_id)->first();
            $message = $request->message;
            $mailData['mail_message'] = $message;
            $mailData['name'] = '';
            $mailData['uuid'] = $record->unique_id;
            $mailData['url'] = clientTrustvisoryUrl().'/service-form-render/'.$record->unique_id;
            $view = View::make('emails.form-mail',$mailData);
            $message = $view->render();
            $parameter['to'] = $record->email;
            $parameter['to_name'] = 'Trustvisory';
            $parameter['message'] = $message;
            $parameter['subject'] ="Sent Form to Fill";
            $parameter['view'] = "emails.form-mail";
            $parameter['data'] = $mailData;
            $mailRes = sendMail($parameter);
            $url = baseUrl('my-services/send-assesment-form-list/'.$form->unique_id);
            return redirect($url)->with('success', 'Form Send Succesfully!');
        } catch (\Exception $e) {
            return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    public function analyzeAssesmentReply(Request $request,$id)
    {
        try {
            $service_send_form = ServiceSendForm::where('unique_id',$id)->first();
            $reply = ServiceFormReply::where('service_send_form_id',$service_send_form->id)->first();
            $form_structure =json_decode( $service_send_form->form_fields_json,true);
            $form_data =  json_decode($reply->field_reply,true);
            $form_reply = [];
            foreach ($form_structure as $field) {
                $name = $field['settings']['name'];
                if (isset($form_data[$name])) {
                    $answer = $form_data[$name];
                    if (is_array($answer)) {
                        $answer = implode('|', $answer);
                    }
                    $form_reply[] = [
                        'question' => $field['settings']['label'],
                        'answer' => $answer,
                        'question_type' => $field['fields']
                    ];
                }
            }
            $apiData['user_id'] = (string) auth()->user()->unique_id;
            $apiData['form_name'] = (string) $service_send_form->form_name;
            $apiData['form_reply'] = $form_reply;
            $apiResponse = assistantApiCall('application_summary', $apiData);
            if($apiResponse['status'] == 'success'){
                $this->manager->saveAssessmentSummary($service_send_form, $reply, $apiResponse['message']);
                return redirect()->back()->with('summary', $apiResponse['message']);
            }else{
                return redirect()->back()->with('summary', $apiResponse['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('summary', $e->getMessage());
        }
    }

    /**
     * Show the form for sending mail.
     */
    public function sendMail($id)
    {
        try {
            $viewData['pageTitle'] = "Send Mail";
            $viewData['form_id'] = $id;
            $viewData['users'] = User::where('role','client')->get();
            return view('admin-panel.04-profile.user-services.sub-services.send-assesment-form',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load send mail form.');
        }
    }
}