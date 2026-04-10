<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use DB;
use App\Models\ImmigrationServices;
use Illuminate\Support\Str;
use App\Models\ProfessionalServices;
use App\Models\CaseWithProfessionals;
use App\Models\SubServicesTypes;
use App\Models\ProfessionalSubServices;
use App\Models\ProfessionalServicePrice;
use App\Models\Forms;
use App\Models\DocumentsFolder;
use App\Models\Types;
use App\Models\ServiceAssesmentForm;

class ServiceAssesmentFormController extends Controller
{

    public function generateAssessment($id)
    {
        try {
           
            $record = ProfessionalServices::where('unique_id', $id)->first();
            $viewData['record'] = $record;
            $viewData['pageTitle'] = "Generate Assessment Using AI";
            $viewData['id'] = $id;
            return view('admin-panel.04-profile.manage-services.generate-assesment-form', $viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load assessment generation form.');
        }
    }

    // public function submitGenerateAssessment($id,Request $request){
    //     try {
          
    //         $prof_sub_service = ProfessionalServices::where('unique_id',$id)->first();
          
    //         // $msg = $request->message;

    //         // if($request->is_modify == "no"){
    //         //     $message = "I m providing immigration service for ".($prof_sub_service->subServices->parentService->name??'')." ".$prof_sub_service->subServices->name??''.". ".$msg;

    //         // }else{
    //         //     $message = $msg;
    //         // }
           
    //         $form_type = $request->form_type;
    //         // $apiData['user_id'] = (string) auth()->user()->unique_id;
    //         // $apiData['message'] = $message;
    //         $apiData['service_name'] = $prof_sub_service->subServices->name;
    //         $apiData['form_type'] = $request->form_type;
    //         $apiData['other_details'] = $request->message;
          
    //         $apiResponse = assistantApiCall('ai-agents/generate-service-assessment-form', $apiData);
    //         \Log::info($apiResponse);
    //         if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
    //             $sample_json = getSampleFormJson(true);
    //             $json_sample = array();
    //             foreach($sample_json as $js){
    //                 $json_sample[$js['fields']] = $js;
    //             }
    //             $form_json = array();
    //             if($request->form_type == 'step_form'){
    //                 $data_arr = $apiResponse['data']['result'];
    //                 $form_name = $data_arr['form_name'];
    //                 // $form_name = "Step Form ".mt_rand();
    //                 return $steps = $data_arr['steps'];
    //                 $index = 0;
    //                 $fg_index = 0;
    //                 foreach($steps as $step){
    //                     $groupFields = array();
    //                     $fg_field_format = array();
    //                     $fg_field_format = $json_sample['fieldGroups'];
    //                     $fg_field_format['groupFields'] = $groupFields;
    //                     $fg_field_format['settings']['label'] = str_replace("'s","&apos;",$step['step_heading']);
    //                     $fg_field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                     $fg_field_format['settings']['stepHeading'] = str_replace("'s","&apos;",$step['step_heading']);
    //                     $fg_field_format['index'] = randomNumber();
    //                     $fg_index = $index;
    //                     $form_json[$index] = $fg_field_format;
    //                     $index++;
    //                     foreach($step['questions'] as $json){
    //                         $field_format = array();
    //                         $newOptions = array();
    //                         if(isset($json['options'])){
    //                             $options = $json['options'];
    //                             foreach($options as $k => $v){
    //                                 $options[$k] = str_replace("'s","&apos;",$v);
    //                                 $newOptions[mt_rand(1000,9999)] = str_replace("'s","&apos;",$v);
    //                             }
    //                             $json['options'] = $newOptions;
    //                         }
                            
    //                         if($json['type'] == 'text'){
    //                             $field_format = $json_sample['textInput'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'number'){
    //                             $field_format = $json_sample['numberInput'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'radio'){
    //                             $field_format = $json_sample['radio'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'checkbox'){
    //                             $field_format = $json_sample['checkbox'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'dropdown'){
    //                             $field_format = $json_sample['dropDown'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'email'){
    //                             $field_format = $json_sample['emailInput'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'textarea'){
    //                             $field_format = $json_sample['textarea'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'date'){
    //                             $field_format = $json_sample['dateInput'];
    //                             $field_format['index'] =randomNumber();
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         $groupFields[] = $field_format['index'];
    //                         $index++;
    //                     }
    //                     $form_json[$fg_index]['groupFields'] = $groupFields;
    //                 }
    //                 $preview_form = '';
    //                 // if(!empty($form_json)){
    //                 //     $viewData['fg_field_json'] = json_encode($form_json);
    //                 //     $viewData['form_name'] = $form_name;
    //                 //     $viewData['form_type'] = $form_type;
    //                 //     $viewData['id'] = $id;
    //                 //     $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
    //                 // }
    //                 $forms = new Forms;
    //                     $forms->unique_id = randomNumber();
    //                     $forms->name = $form_name;
    //                     $forms->form_type = $form_type;
    //                     $forms->fg_field_json = json_encode($form_json);
    //                     $forms->added_by = auth()->user()->id;
    //                     $forms->save();

    //                     ServiceAssesmentForm::create([
    //                         'unique_id' => randomNumber(),
    //                         'added_by' => \Auth::user()->id,
    //                         'professional_service_id' => $prof_sub_service->id,
    //                         'service_id' => $prof_sub_service->service_id,
    //                         'form_id' => $forms->id
    //                     ]);
    //                 $response['preview_form'] = $preview_form;
    //                 if(!empty($form_json)){
    //                     $response['fg_field_json'] = json_encode($form_json);
    //                 }else{
    //                     $response['fg_field_json'] = "";
    //                 }
    //                 $response['form_type'] = $form_type;
    //                 $response['status'] = true;
    //             }else{
    //                $data_arr = $apiResponse['data']['result'];
                  
    //                 if(isset($data_arr['questions'])){
    //                     $form_name = $data_arr['form_name'];
    //                     $index = 0;
    //                     foreach($data_arr['questions'] as $json){
    //                         $field_format = array();
    //                         $newOptions = array();
    //                         if(isset($json['options'])){
    //                             $options = $json['options'];
                               
    //                             foreach($options as $k => $v){
    //                                 $options[$k] = str_replace("'s","&apos;",$v);
    //                                 $newOptions[mt_rand(1000,9999)] = str_replace("'s","&apos;",$v);
    //                             }
    //                             $json['options'] = $newOptions;
    //                         }
                            
    //                         if($json['type'] == 'text'){
    //                             $field_format = $json_sample['textInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $field_format['index'] = randomNumber();
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'number'){
    //                             $field_format = $json_sample['numberInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'radio'){
    //                             $field_format = $json_sample['radio'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'checkbox'){
    //                             $field_format = $json_sample['checkbox'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'dropdown'){
    //                             $field_format = $json_sample['dropDown'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'email'){
    //                             $field_format = $json_sample['emailInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'textarea'){
    //                             $field_format = $json_sample['textarea'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'date'){
    //                             $field_format = $json_sample['dateInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         $index++;
    //                     }
    //                     $preview_form = '';
    //                     // if(!empty($form_json)){
    //                     //     $viewData['fg_field_json'] = json_encode($form_json);
    //                     //     $viewData['form_name'] = $form_name;
    //                     //     $viewData['form_type'] = $form_type;
    //                     //     $viewData['id'] = $id;
    //                     //     $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
    //                     // }
    //                     $forms = new Forms;
    //                     $forms->unique_id = randomNumber();
    //                     $forms->name = $form_name;
    //                     $forms->form_type = $form_type;
    //                     $forms->fg_field_json = json_encode($form_json);
    //                     $forms->added_by = auth()->user()->id;
    //                     $forms->save();

    //                     ServiceAssesmentForm::create([
    //                         'unique_id' => randomNumber(),
    //                         'added_by' => \Auth::user()->id,
    //                         'professional_service_id' => $prof_sub_service->id,
    //                         'service_id' => $prof_sub_service->service_id,
    //                         'form_id' => $forms->id
    //                     ]);


    //                     $response['preview_form'] = $preview_form;
    //                     if(!empty($form_json)){
    //                         $response['fg_field_json'] = json_encode($form_json);
    //                     }else{
    //                         $response['fg_field_json'] = "";
    //                     }
    //                     $response['form_type'] = $form_type;
    //                     $response['redirect_back'] = baseUrl('manage-services');
    //                     $response['status'] = true;
    //                 }else{
    //                     $response['status'] = false;
    //                     $response['message'] = $data_arr['error']??'Something went try. Try again';
    //                 }
    //             }
                    
    //         }else{
    //             $response['message'] = "Something went wrong try again";
    //             $response['status'] = false;
    //         }

    //         return response()->json($response);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An unexpected error occurred.',
    //             'error' => $e->getMessage(), // Optional: Include for debugging
    //             // 'trace' => $e->getTraceAsString() // Uncomment only for debugging
    //         ], 500);
    //     }
    // }

    //  public function submitGenerateAssessment($id,Request $request){
    //     try {
          
    //         $prof_sub_service = ProfessionalServices::where('unique_id',$id)->first();
          
    //         // $msg = $request->message;

    //         // if($request->is_modify == "no"){
    //         //     $message = "I m providing immigration service for ".($prof_sub_service->subServices->parentService->name??'')." ".$prof_sub_service->subServices->name??''.". ".$msg;

    //         // }else{
    //         //     $message = $msg;
    //         // }
           
    //         $form_type = $request->form_type;
    //         // $apiData['user_id'] = (string) auth()->user()->unique_id;
    //         // $apiData['message'] = $message;
    //         $apiData['service_name'] = $prof_sub_service->subServices->name;
    //         $apiData['form_type'] = $request->form_type;
    //         $apiData['other_details'] = $request->message;
          
    //         $apiResponse = assistantApiCall('ai-agents/generate-service-assessment-form', $apiData);
    //         \Log::info($apiResponse);
    //         if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
    //             $sample_json = getSampleFormJson(true);
    //             $json_sample = array();
    //             foreach($sample_json as $js){
    //                 $json_sample[$js['fields']] = $js;
    //             }
    //             $form_json = array();
    //             if($request->form_type == 'step_form'){
    //                 $data_arr = $apiResponse['data']['result'];
    //                 $form_name = $data_arr['form_name'];
    //                 // Build $steps from questions if 'steps' key does not exist
    //                 $steps = [];
    //                 if (isset($data_arr['steps'])) {
    //                     $steps = $data_arr['steps'];
    //                 } elseif (isset($data_arr['questions'][0]) && is_array($data_arr['questions'][0])) {
    //                     foreach ($data_arr['questions'][0] as $step_key => $step_data) {
    //                         $steps[] = [
    //                             'step_heading' => ucfirst(str_replace('_', ' ', $step_key)),
    //                             'questions' => $step_data['questions']
    //                         ];
    //                     }
    //                 }
    //                 $form_json = [];
    //                 $step_number = 1;
    //                 foreach ($steps as $step) {
    //                     $groupFields = [];
    //                     $order = 0;
    //                     foreach ($step['questions'] as $json) {
    //                         $field_format = [];
    //                         $newOptions = [];
    //                         if (isset($json['options'])) {
    //                             $options = $json['options'];
    //                             foreach ($options as $k => $v) {
    //                                 $options[$k] = str_replace("'s", "&apos;", $v);
    //                                 $newOptions[mt_rand(1000, 9999)] = str_replace("'s", "&apos;", $v);
    //                             }
    //                             $json['options'] = $newOptions;
    //                         }
    //                         if ($json['type'] == 'text') {
    //                             $field_format = $json_sample['textInput'];
    //                         } elseif ($json['type'] == 'number') {
    //                             $field_format = $json_sample['numberInput'];
    //                         } elseif ($json['type'] == 'radio') {
    //                             $field_format = $json_sample['radio'];
    //                             $field_format['settings']['options'] = $json['options'];
    //                         } elseif ($json['type'] == 'checkbox') {
    //                             $field_format = $json_sample['checkbox'];
    //                             $field_format['settings']['options'] = $json['options'];
    //                         } elseif ($json['type'] == 'dropdown') {
    //                             $field_format = $json_sample['dropDown'];
    //                             $field_format['settings']['options'] = $json['options'];
    //                         } elseif ($json['type'] == 'email') {
    //                             $field_format = $json_sample['emailInput'];
    //                         } elseif ($json['type'] == 'textarea') {
    //                             $field_format = $json_sample['textarea'];
    //                         } elseif ($json['type'] == 'date') {
    //                             $field_format = $json_sample['dateInput'];
    //                         }
    //                         $field_format['index'] = randomNumber();
    //                         $field_format['settings']['label'] = str_replace("'s", "&apos;", $json['question']);
    //                         $field_format['settings']['name'] = "fg_" . mt_rand(1000, 9999);
    //                         $field_format['step'] = (string)$step_number;
    //                         $field_format['order'] = (string)$order;
    //                         $form_json[] = $field_format;
    //                         $groupFields[] = $field_format['index'];
    //                         $order++;
    //                     }
    //                     // Add the fieldGroups entry for this step
    //                     $fg_field_format = $json_sample['fieldGroups'];
    //                     $fg_field_format['groupFields'] = $groupFields;
    //                     $fg_field_format['settings']['label'] = str_replace("'s", "&apos;", $step['step_heading']);
    //                     $fg_field_format['settings']['name'] = "fg_" . mt_rand(1000, 9999);
    //                     $fg_field_format['settings']['stepHeading'] = str_replace("'s", "&apos;", $step['step_heading']);
    //                     $fg_field_format['index'] = randomNumber();
    //                     $fg_field_format['step'] = (string)$step_number;
    //                     $fg_field_format['order'] = (string)$order;
    //                     $form_json[] = $fg_field_format;
    //                     $step_number++;
    //                 }
    //                 $preview_form = '';
    //                 $forms = new Forms;
    //                 $forms->unique_id = randomNumber();
    //                 $forms->name = $form_name;
    //                 $forms->form_type = $form_type;
    //                 $forms->fg_field_json = json_encode($form_json);
    //                 $forms->added_by = auth()->user()->id;
    //                 $forms->save();

    //                 ServiceAssesmentForm::create([
    //                     'unique_id' => randomNumber(),
    //                     'added_by' => \Auth::user()->id,
    //                     'professional_service_id' => $prof_sub_service->id,
    //                     'service_id' => $prof_sub_service->service_id,
    //                     'form_id' => $forms->id
    //                 ]);
    //                 $response['preview_form'] = $preview_form;
    //                 if(!empty($form_json)){
    //                     $response['fg_field_json'] = json_encode($form_json);
    //                 }else{
    //                     $response['fg_field_json'] = "";
    //                 }
    //                 $response['form_type'] = $form_type;
    //                 $response['status'] = true;
    //             }else{
    //                $data_arr = $apiResponse['data']['result'];
                  
    //                 if(isset($data_arr['questions'])){
    //                     $form_name = $data_arr['form_name'];
    //                     $index = 0;
    //                     foreach($data_arr['questions'] as $json){
    //                         $field_format = array();
    //                         $newOptions = array();
    //                         if(isset($json['options'])){
    //                             $options = $json['options'];
                               
    //                             foreach($options as $k => $v){
    //                                 $options[$k] = str_replace("'s","&apos;",$v);
    //                                 $newOptions[mt_rand(1000,9999)] = str_replace("'s","&apos;",$v);
    //                             }
    //                             $json['options'] = $newOptions;
    //                         }
                            
    //                         if($json['type'] == 'text'){
    //                             $field_format = $json_sample['textInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $field_format['index'] = randomNumber();
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'number'){
    //                             $field_format = $json_sample['numberInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'radio'){
    //                             $field_format = $json_sample['radio'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'checkbox'){
    //                             $field_format = $json_sample['checkbox'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'dropdown'){
    //                             $field_format = $json_sample['dropDown'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['options'] = $json['options'];
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'email'){
    //                             $field_format = $json_sample['emailInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'textarea'){
    //                             $field_format = $json_sample['textarea'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         if($json['type'] == 'date'){
    //                             $field_format = $json_sample['dateInput'];
    //                             $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //                             $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //                             $form_json[$index] = $field_format;
    //                         }
    //                         $index++;
    //                     }
    //                     $preview_form = '';
    //                     // if(!empty($form_json)){
    //                     //     $viewData['fg_field_json'] = json_encode($form_json);
    //                     //     $viewData['form_name'] = $form_name;
    //                     //     $viewData['form_type'] = $form_type;
    //                     //     $viewData['id'] = $id;
    //                     //     $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
    //                     // }
    //                     $forms = new Forms;
    //                     $forms->unique_id = randomNumber();
    //                     $forms->name = $form_name;
    //                     $forms->form_type = $form_type;
    //                     $forms->fg_field_json = json_encode($form_json);
    //                     $forms->added_by = auth()->user()->id;
    //                     $forms->save();

    //                     ServiceAssesmentForm::create([
    //                         'unique_id' => randomNumber(),
    //                         'added_by' => \Auth::user()->id,
    //                         'professional_service_id' => $prof_sub_service->id,
    //                         'service_id' => $prof_sub_service->service_id,
    //                         'form_id' => $forms->id
    //                     ]);


    //                     $response['preview_form'] = $preview_form;
    //                     if(!empty($form_json)){
    //                         $response['fg_field_json'] = json_encode($form_json);
    //                     }else{
    //                         $response['fg_field_json'] = "";
    //                     }
    //                     $response['form_type'] = $form_type;
    //                     $response['redirect_back'] = baseUrl('manage-services');
    //                     $response['status'] = true;
    //                 }else{
    //                     $response['status'] = false;
    //                     $response['message'] = $data_arr['error']??'Something went try. Try again';
    //                 }
    //             }
                    
    //         }else{
    //             $response['message'] = "Something went wrong try again";
    //             $response['status'] = false;
    //         }

    //         return response()->json($response);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An unexpected error occurred.',
    //             'error' => $e->getMessage(), // Optional: Include for debugging
    //             // 'trace' => $e->getTraceAsString() // Uncomment only for debugging
    //         ], 500);
    //     }
    // }

        public function submitGenerateAssessment($id,Request $request){
        try {
          
            $prof_sub_service = ProfessionalServices::where('unique_id',$id)->first();
          
            // $msg = $request->message;

            // if($request->is_modify == "no"){
            //     $message = "I m providing immigration service for ".($prof_sub_service->subServices->parentService->name??'')." ".$prof_sub_service->subServices->name??''.". ".$msg;

            // }else{
            //     $message = $msg;
            // }
           
            $form_type = $request->form_type;
            // $apiData['user_id'] = (string) auth()->user()->unique_id;
            // $apiData['message'] = $message;
            $apiData['service_name'] = $prof_sub_service->subServices->name;
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
                    $forms->save();

                    ServiceAssesmentForm::create([
                        'unique_id' => randomNumber(),
                        'added_by' => \Auth::user()->id,
                        'professional_service_id' => $prof_sub_service->id,
                        'service_id' => $prof_sub_service->service_id,
                        'form_id' => $forms->id
                    ]);
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
                        $forms->save();

                        ServiceAssesmentForm::create([
                            'unique_id' => randomNumber(),
                            'added_by' => \Auth::user()->id,
                            'professional_service_id' => $prof_sub_service->id,
                            'service_id' => $prof_sub_service->service_id,
                            'form_id' => $forms->id
                        ]);


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
            $response['redirect_back'] = baseUrl('manage-services');
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
        $response['redirect_back'] = baseUrl('manage-services/list-assessment/'.$id);
        return response()->json($response);
    }

    private function findProfessionalServiceOrFail($id)
    {
        $service = ProfessionalServices::where('unique_id', $id)->first();
        if (!$service) {
            abort(404, 'Professional Service not found');
        }
        return $service;
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
            return view('admin-panel.04-profile.manage-services.assesment-form.list-assesment-form', $viewData);
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
            $view = View::make('admin-panel.04-profile.manage-services.assesment-form.assesment-ajax-list', $viewData);
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
            return view('admin-panel.04-profile.manage-services.assesment-form.view-assesment',$viewData);
        } catch (\Exception $e) {
            abort(404, 'Unable to load assessment view.');
        }
    }


}