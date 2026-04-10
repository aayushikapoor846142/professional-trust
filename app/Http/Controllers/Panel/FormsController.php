<?php

namespace App\Http\Controllers\Panel;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\Forms;
use App\Models\SendForms;
use App\Models\User;
use Auth;
use DB;
use App\Models\ImmigrationServices;
use App\Models\PredefinedForms;
use App\Models\ProfessionalServices;

class FormsController extends Controller
{
    public function forms()
    {
        $viewData['pageTitle'] = "Forms";
        $step_form_counts = Forms::where("form_type","step_form")->visibleToUser(auth()->user()->id)->count();
        $single_form_counts = Forms::where("form_type","single_form")->visibleToUser(auth()->user()->id)->count();
        $all_form_counts = Forms::visibleToUser(auth()->user()->id)->count();

        $viewData['step_form_counts'] = $step_form_counts;
        $viewData['single_form_counts'] = $single_form_counts;
        $viewData['all_form_counts'] = $all_form_counts;
        
        return view('admin-panel.08-cases.forms.lists', $viewData);
    }

    public function getAjaxList(Request $request)
    {   
        $search = $request->input("search");
        $form_type = $request->input("form_type");
        $form_type_filter = $request->input("form_type_filter");
        $records = Forms::orderBy('id', 'desc')
        ->where(function ($query) use ($search,$form_type,$form_type_filter) {
            if ($search != '') {
                $query->where("name", "LIKE", "%" . $search . "%");
            }
            if ($form_type != '' && $form_type != 'all') {
                $query->where("form_type", "LIKE",$form_type);
            }
            if ($form_type_filter != '') {
                \Log::info($form_type_filter);
                $query->where("form_type", $form_type_filter);
            }
        })
        ->visibleToUser(auth()->user()->id)
        ->paginate();

        $viewData['records'] = $records;
        $view = view('admin-panel.08-cases.forms.ajax-list',$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    public function createForm()
    {
        $viewData['pageTitle'] = 'Create Form';
        $immigrationServices = ImmigrationServices::where('parent_service_id',0)->get();
        $viewData['immigrationServices'] = $immigrationServices;
        return view('admin-panel.08-cases.forms.add',$viewData);
    }

    public function saveForm(Request $request)
    {
        try{
            $fg_fields = $request->fg_fields;
            foreach($fg_fields as $key => $values){
                $fg_fields[$key]['index'] = $key;
                foreach($values['settings'] as $k => $v){
                    $values['settings'][$k] = str_replace("'","&apos;",$v);
                }
                $fg_fields[$key]['settings'] = $values['settings'];
            }
            $fg_fields = array_values($fg_fields);
            $object = new Forms();
            $object->unique_id = randomNumber();
            $object->name = $request->form_name;
            $object->form_type = $request->form_type;
            $object->fg_field_json = json_encode($fg_fields);
            $object->added_by = \Auth::user()->id;
            $object->parent_service_id = $request->parent_service_id;
            $object->sub_service_id = $request->sub_service_id;
            $object->type="manually";
            $object->save();
        
            $response['status'] = true;
            $response['message'] = "Form saved successfully";
            $response['redirect_back'] = baseUrl('forms/');
            \Session::flash("success","Form Saved Successfully");
            return response()->json($response);
        }catch(\Exception $e){
            return response(['status'=>false,'message'=>$e->getMessage().' '.$e->getFile().' '.$e->getLine()]);
        }
        
    }
    public function editForm($id)
    {
        $record = $this->getFormOrFail($id);
       
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Form";
        $viewData['immigrationServices'] = ImmigrationServices::where('parent_service_id',0)->get();
        $viewData['selectedServiceId'] = $record->parent_service_id;
        $viewData['selectedSubServiceId'] = $record->sub_service_id;
        return view('admin-panel.08-cases.forms.edit',$viewData);
    }

    public function viewReply($id)
    {
        $forms = Forms::with(['sendForm.formReply'])->where('unique_id',$id)->first();
    
        $viewData['pageTitle'] = "View My Case Details";
        $filledForms = [];

        foreach ($forms->sendForm as $formSend) {
            $formStructure = json_decode($formSend->form_fields_json, true);
            $fieldReply = json_decode($formSend->formReply->field_reply ?? '{}', true);
            $filledForm = [];

            foreach ($formStructure as $field) {
                $fieldData = $field;
                $settings = $field['settings'] ?? [];
                $name = $settings['name'] ?? null;

                if (!$name) {
                    $filledForm[] = $fieldData;
                    continue;
                }

                if (isset($fieldReply[$name])) {
                    $replyValue = $fieldReply[$name];

                    if (isset($settings['options'])) {
                        // Convert options from key-value to array with selected
                        $finalOptions = [];

                        foreach ($settings['options'] as $optKey => $optVal) {
                            $selected = 0;

                            if (is_array($replyValue)) {
                                if (in_array($optVal, $replyValue) || in_array($optKey, array_keys($replyValue))) {
                                    $selected = 1;
                                }
                            } else {
                                if ($optVal == $replyValue || $optKey == $replyValue) {
                                    $selected = 1;
                                }
                            }

                            $finalOptions[] = [
                                'key' => $optKey,
                                'value' => $optVal,
                                'selected' => $selected
                            ];
                        }

                        $settings['options'] = $finalOptions;
                    } else {
                        // For input fields
                        $settings['value'] = $replyValue;
                    }

                    $fieldData['settings'] = $settings;
                }

                $filledForm[] = $fieldData;
            }

            // Store form with metadata if needed
            $filledForms[] = [
                'uuid' => $formSend->uuid,
                'email' => $formSend->email,
                'submitted_at' => $formSend->form_reply->created_at ?? null,
                'filled_fields' => $filledForm
            ];
        }

        $viewData['form'] = $forms;
        $viewData['all_filled_forms'] = $filledForms;
        return view('admin-panel.08-cases.forms.view-reply', $viewData);
    }

    public function updateForm($id,Request $request)
    {
        $object = $this->getFormOrFail($id);
      
        $fg_fields = $request->fg_fields;
        // pre($fg_fields);exit;
        foreach($fg_fields as $key => $values){
            $fg_fields[$key]['index'] = $key;
            if(isset($values['settings'])){
                foreach($values['settings'] as $k => $v){
                    $values['settings'][$k] = str_replace("'","&apos;",$v);
                }
                $fg_fields[$key]['settings'] = $values['settings'];
            }
        }
        $fg_fields = array_values($fg_fields);
       
        $object->name = $request->form_name;
        $object->form_type = $request->form_type;
        $object->fg_field_json = json_encode($fg_fields);
        $object->parent_service_id = $request->parent_service_id;
        $object->sub_service_id = $request->sub_service_id;
        $object->added_by = auth()->id();
        $object->save();

        $response['status'] = true;
        $response['message'] = "Form saved successfully";
        $response['redirect_back'] = baseUrl('forms/');
        \Session::flash("success","Form Updated Successfully");
        return response()->json($response);
    }

    public function renderForm($id)
    {
        $record = $this->getFormOrFail($id);
     
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
        return view('admin-panel.08-cases.forms.render-form',$viewData);
    }

    public function deleteForm($id)
    {
        $record = $this->getFormOrFail($id);
    
        Forms::deleteRecord($record->id);
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('form');
        $response['message'] = 'Form deleted successfully';
        return redirect()->back()->with("success","Form has been deleted!");
    }

    public function sendMail($id)
    {
        $viewData['pageTitle'] = "Send Mail";
        $viewData['form_id'] = $id;

        $view = View::make('admin-panel.08-cases.forms.send-mail',$viewData);
        
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response); 
    }

    public function sendForm($form_id,Request $request){
       
           $validator = Validator::make($request->all(), [
                'email' => 'required|email',
        'message' => 'required|string|max:1000',
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
        $form = Forms::where("unique_id",$form_id)->first();
        $uuid = generateUUID();
        
        $name = '';
    
        $object = new SendForms();
        $response_message = 'Form send to user successfully';
        
        if($request->input("registered_user")){
            $user = User::where("id",$request->input("send_to"))->first();
            $object->user_id = $user->id;
            $object->email = $user->email;
            $email = $user->email;
            $object->registered_user = 1;
            $response_message = 'Form send to registered user successfully';
            $name = $user->first_name." ".$user->last_name;
        }else{
            $user = User::where("email",$request->input("email"))->first();
            if(!empty($user)){
                $object->user_id = $user->id;
                $response_message = 'User with email already exists, mail send to user successfully';
                $email = $user->email;
                $name = $user->first_name." ".$user->last_name;
            }
            $object->email = $request->email;
            $object->registered_user = 0;
            $email = $request->email;
        }
        $object->form_id = $form->id;
        $object->form_name = $form->name;
        $object->form_type = $form->form_type;
        if($request->input("case_id")){
            $object->case_id = $request->input("case_id");
        }
        $object->form_fields_json = $form->fg_field_json;
        $object->uuid = $uuid;
        $object->added_by = auth()->id();
        $object->save();

        $message = $request->message;
        $mailData['mail_message'] = $message;
        $mailData['name'] = $name;
        $mailData['uuid'] = $uuid;
        $mailData['url'] = mainTrustvisoryUrl().'/form-render/'.$uuid;
        $view = View::make('emails.form-mail',$mailData);
        $message = $view->render();
        $parameter['to'] = $email;
        $parameter['to_name'] = 'Trustvisory';
        $parameter['message'] = $message;
        $parameter['subject'] ="Sent Form to Fill";
        $parameter['view'] = "emails.form-mail";
        $parameter['data'] = $mailData;
        $mailRes = sendMail($parameter);
       
        $response['status'] = true;
        $response['message'] = $response_message;

        return response()->json($response);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        $forms = Forms::whereIn("unique_id", $ids)->get();
        foreach ($forms as $act) {
            if ($act->isEditableBy(auth()->id())) {
                Forms::deleteRecord($act->id);
            }
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    private function getFormOrFail($unique_id)
    {
        $form = Forms::where('unique_id', $unique_id)->first();
        if (!$form) {
            abort(404, 'Form not found');
        }
        return $form;
    }

     private function getPredefinedFormOrFail($unique_id)
    {
        $form = PredefinedForms::where('unique_id', $unique_id)->first();
        if (!$form) {
            abort(404, 'Form not found');
        }
        return $form;
    }

    public function generateViaAi(Request $request)
    {
        $viewData['immigrationServices'] = ImmigrationServices::where('parent_service_id',0)->get();
        $viewData['pageTitle'] = "Generate Via AI";
        $view = \View::make('admin-panel.08-cases.forms.generate-ai-modal', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

      public function saveAiForm(Request $request){
        try {
            
            $sub_service = ImmigrationServices::where('id',$request->sub_service_id)->first();
          
            $form_type = $request->form_type;
            // $apiData['user_id'] = (string) auth()->user()->unique_id;
            // $apiData['message'] = $message;
            $apiData['service_name'] = $sub_service->name;
            $apiData['form_type'] = $request->form_type;
            $apiData['other_details'] = $request->message;
            // return $apiData;
            $apiResponse = assistantApiCall('ai-agents/generate-service-assessment-form', $apiData);
           
            if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
                $form_json = array();
                if($request->form_type == 'step_form'){
                    $sample_json = getSampleFormJson(true);
                    $json_sample = array();
                    foreach($sample_json as $js){
                        $json_sample[$js['fields']] = $js;
                    }
                    $data_arr = $apiResponse['data']['result'];
                    $form_name = $data_arr['form_name'];
                    // Build $steps from questions if 'steps' key does not exist
                    $steps = [];
                    if (isset($data_arr['steps'])) {
                        $steps = $data_arr['steps'];
                    } elseif (isset($data_arr['questions'][0]) && is_array($data_arr['questions'][0])) {
                        foreach ($data_arr['questions'][0] as $step_key => $step_data) {
                            $steps[] = [
                                'step_heading' => ucfirst(str_replace('_', ' ', $step_key)),
                                'questions' => $step_data['questions']
                            ];
                        }
                    }
                    $form_json = [];
                    $step_number = 1;
                    foreach ($steps as $step) {
                        $groupFields = [];
                        $order = 0;
                        foreach ($step['questions'] as $json) {
                            $field_format = [];
                            $newOptions = [];
                            if (isset($json['options'])) {
                                $options = $json['options'];
                                foreach ($options as $k => $v) {
                                    $options[$k] = str_replace("'s", "&apos;", $v);
                                    $newOptions[mt_rand(1000, 9999)] = str_replace("'s", "&apos;", $v);
                                }
                                $json['options'] = $newOptions;
                            }
                            if ($json['type'] == 'text') {
                                $field_format = $json_sample['textInput'];
                            } elseif ($json['type'] == 'number') {
                                $field_format = $json_sample['numberInput'];
                            } elseif ($json['type'] == 'radio') {
                                $field_format = $json_sample['radio'];
                                $field_format['settings']['options'] = $json['options'];
                            } elseif ($json['type'] == 'checkbox') {
                                $field_format = $json_sample['checkbox'];
                                $field_format['settings']['options'] = $json['options'];
                            } elseif ($json['type'] == 'dropdown') {
                                $field_format = $json_sample['dropDown'];
                                $field_format['settings']['options'] = $json['options'];
                            } elseif ($json['type'] == 'email') {
                                $field_format = $json_sample['emailInput'];
                            } elseif ($json['type'] == 'textarea') {
                                $field_format = $json_sample['textarea'];
                            } elseif ($json['type'] == 'date') {
                                $field_format = $json_sample['dateInput'];
                            }
                            $field_format['index'] = randomNumber();
                            $field_format['settings']['label'] = str_replace("'s", "&apos;", $json['question']);
                            $field_format['settings']['name'] = "fg_" . mt_rand(1000, 9999);
                            $field_format['step'] = (string)$step_number;
                            $field_format['order'] = (string)$order;
                            $form_json[] = $field_format;
                            $groupFields[] = $field_format['index'];
                            $order++;
                        }
                        // Add the fieldGroups entry for this step
                        $fg_field_format = $json_sample['fieldGroups'];
                        $fg_field_format['groupFields'] = $groupFields;
                        $fg_field_format['settings']['label'] = str_replace("'s", "&apos;", $step['step_heading']);
                        $fg_field_format['settings']['name'] = "fg_" . mt_rand(1000, 9999);
                        $fg_field_format['settings']['stepHeading'] = str_replace("'s", "&apos;", $step['step_heading']);
                        $fg_field_format['index'] = randomNumber();
                        $fg_field_format['step'] = (string)$step_number;
                        $fg_field_format['order'] = (string)$order;
                        $form_json[] = $fg_field_format;
                        $step_number++;
                    }
                    $preview_form = '';

                    $forms = new Forms;
                    $forms->unique_id = randomNumber();
                    $forms->name = $form_name;
                    $forms->form_type = $form_type;
                    $forms->fg_field_json = json_encode($form_json);
                    $forms->added_by = auth()->user()->id;
                    $forms->type = "ai";
                    $forms->parent_service_id = $request->service_id;
                    $forms->sub_service_id = $request->sub_service_id;
                    $forms->save();

                    $response['preview_form'] = $preview_form;
                    if(!empty($form_json)){
                        $response['fg_field_json'] = json_encode($form_json);
                    }else{
                        $response['fg_field_json'] = "";
                    }
                    $response['form_type'] = $form_type;
                    $response['status'] = true;
                }else{
                    $sample_json = getSampleFormJson(false);
                    $json_sample = array();
                    foreach($sample_json as $js){
                        $json_sample[$js['fields']] = $js;
                    }
                   $data_arr = $apiResponse['data']['result'];
                  
                    if(isset($data_arr['questions'])){
                        
                        $form_name = $data_arr['form_name'];
                        $form_json = [];
                        $groupFields = [];
                        $order = 0;
                        foreach($data_arr['questions'] as $json){
                            $field_format = [];
                            $newOptions = [];
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
                            } elseif($json['type'] == 'number'){
                                $field_format = $json_sample['numberInput'];
                            } elseif($json['type'] == 'radio'){
                                $field_format = $json_sample['radio'];
                                $field_format['settings']['options'] = $json['options'];
                            } elseif($json['type'] == 'checkbox'){
                                $field_format = $json_sample['checkbox'];
                                $field_format['settings']['options'] = $json['options'];
                            } elseif($json['type'] == 'dropdown'){
                                $field_format = $json_sample['dropDown'];
                                $field_format['settings']['options'] = $json['options'];
                            } elseif($json['type'] == 'email'){
                                $field_format = $json_sample['emailInput'];
                            } elseif($json['type'] == 'textarea'){
                                $field_format = $json_sample['textarea'];
                            } elseif($json['type'] == 'date'){
                                $field_format = $json_sample['dateInput'];
                            }
                            $field_format['index'] = randomNumber();
                            $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                            $field_format['step'] = "1";
                            $field_format['order'] = (string)$order;
                            $form_json[] = $field_format;
                            $groupFields[] = $field_format['index'];
                            $order++;
                        }
                        // Add the fieldGroups entry for this single form
                        $fg_field_format = $json_sample['fieldGroups'];
                        $fg_field_format['groupFields'] = $groupFields;
                        $fg_field_format['settings']['label'] = $form_name;
                        $fg_field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                        $fg_field_format['settings']['stepHeading'] = $form_name;
                        $fg_field_format['index'] = randomNumber();
                        $fg_field_format['step'] = "1";
                        $fg_field_format['order'] = (string)$order;
                        $form_json[] = $fg_field_format;
                        $preview_form = '';

                        $forms = new Forms;
                        $forms->unique_id = randomNumber();
                        $forms->name = $form_name;
                        $forms->form_type = $form_type;
                        $forms->fg_field_json = json_encode($form_json);
                        $forms->added_by = auth()->user()->id;
                        $forms->type = "ai";
                        $forms->parent_service_id = $request->service_id;
                        $forms->sub_service_id = $request->sub_service_id;
                        $forms->save();

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
            $response['form_id'] = $forms->id;
            $response['redirect_back'] = baseUrl('forms');
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(), // Optional: Include for debugging
                // 'trace' => $e->getTraceAsString() // Uncomment only for debugging
            ], 500);
        }
    }

    public function fetchSubService(Request $request)
    {
        $service_id = $request->input("service_id");
        $professioanl_service = ProfessionalServices::where('parent_service_id',$service_id)->where('user_id',auth()->user()->id)->get()->pluck('service_id')->toArray();
        $services = ImmigrationServices::whereIn("id", $professioanl_service)->get();
        $options = '<option value="">Select Sub Service</option>';
        foreach ($services as $service) {
            $options .= '<option value="' . $service->id . '">' . $service->name . '</option>';
        }
        $response['options'] = $options;
        $response['status'] = true;
        return response()->json($response);
    }


    public function getSubServices($parentServiceId)
    {
        try {
            \Log::info('Fetching sub-services for parent service ID: ' . $parentServiceId);
            
            $subServices = ImmigrationServices::where('parent_service_id', $parentServiceId)->get();
            
            \Log::info('Found sub-services count: ' . $subServices->count());
            \Log::info('Raw sub-services: ' . $subServices->toJson());
            
            $formattedSubServices = $subServices->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name
                ];
            });
            
            \Log::info('Formatted sub-services: ' . $formattedSubServices->toJson());
            
            $response = [
                'status' => true,
                'message' => 'Sub-services fetched successfully',
                'data' => [
                    'subServices' => $formattedSubServices
                ]
            ];
            
            \Log::info('API response: ' . json_encode($response));
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in getSubServices: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function predefinedTemplates()
    {
        $step_form_counts = PredefinedForms::where("form_type","step_form")->count();
        $single_form_counts = PredefinedForms::where("form_type","single_form")->count();
        $all_form_counts = PredefinedForms::count();
        $viewData['pageTitle'] = 'Predefined Templates';
        $immigrationServices = ImmigrationServices::where('parent_service_id',0)->get();
        $viewData['predefinedForms'] = PredefinedForms::orderBy('id','desc')->get();
        $viewData['immigrationServices'] = $immigrationServices;
        
        return view('admin-panel.08-cases.forms.predefined-templates',$viewData);
    }

    public function predefinedAjaxTemplates(Request $request)
    {   
        $search = $request->input("search");
        $form_type = $request->input("form_type");
        $records = PredefinedForms::orderBy('id', 'desc')
        ->where(function ($query) use ($search,$form_type) {
            if ($search != '') {
                $query->where("name", "LIKE", "%" . $search . "%");
            }
            if ($form_type != '' && $form_type != 'all') {
                $query->where("form_type", "LIKE",$form_type);
            }
        })
        ->paginate();

        $viewData['records'] = $records;
        $view = view('admin-panel.08-cases.forms.predefined-ajax-list',$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }


    public function predefinedRenderForm($id)
    {
        $record = $this->getPredefinedFormOrFail($id);
     
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
        $viewData['pageTitle'] = "Preview Form";

        $view = view("admin-panel.08-cases.forms.predefined-render-form", $viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);

        // return view('admin-panel.08-cases.forms.predefined-render-form',$viewData);
    }

    public function savePredefinedTemplate($id)
    {
        $predefinedForm = PredefinedForms::where('unique_id',$id)->first();

        if(!empty($predefinedForm)){
            $form = new Forms();
            $form->unique_id = randomNumber();
            $form->added_by = auth()->user()->id;
            $form->type = $predefinedForm->type;
            $form->form_type = $predefinedForm->form_type;
            $form->parent_service_id = $predefinedForm->parent_service_id;
            $form->sub_service_id = $predefinedForm->sub_service_id;
            $form->name = $predefinedForm->name;
            $form->fg_field_json = $predefinedForm->fg_field_json;
            $form->save();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('form');
            $response['message'] = 'Form saved successfully';
            return redirect()->back()->with("success","Form saved successfully");
        }else{
            $response['status'] = false;
            $response['redirect_back'] = baseUrl('form');
            $response['message'] = 'Form not found';
            return redirect()->back()->with("success","Form not found");
        }
       
    }
}