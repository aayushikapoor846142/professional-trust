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

class ManageServicesController extends Controller
{

    public function getAllServices(Request $request)
    {
        $search = $request->input("search");
        $records = ImmigrationServices::with('subServices')
                            ->where('parent_service_id','0')
                            ->whereHas("subServices")
                            ->where(function($query) use($search){
                                $query->where("name","LIKE","%".$search."%");
                                $query->orWhere(function($query) use($search){
                                    $query->whereHas("subServices",function($q) use($search){
                                        $q->where("name","LIKE","%".$search."%");
                                    });
                                });
                            })
                            ->get();

        $viewData['records'] = $records;
        $viewData['subservices'] = ProfessionalServices::where('user_id',auth()->user()->id)
                                    ->get()->pluck('service_id')->toArray();
        $viewData['checkedParentServiceIds'] = CaseWithProfessionals::where('professional_id', auth()->user()->id) // Adjust based on your column
                                    ->pluck('parent_service_id')
                                    ->toArray();
        $viewData['checkedSubServiceIds'] = CaseWithProfessionals::where('professional_id', auth()->user()->id) // Adjust based on your column
                                    ->pluck('sub_service_id')
                                    ->toArray();
        $view = View::make('admin-panel.04-profile.manage-services.all-services', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        $response['records'] = $records;
        return response()->json($response);

    }

    public function saveMyService(Request $request){
        $service = ImmigrationServices::where("id",$request->id)->first();
        if($request->type == 'selected'){
            ProfessionalServices::updateOrCreate(['service_id'=>$request->id,'user_id'=>auth()->user()->id],['parent_service_id'=>$service->parent_service_id]);
            $response['message'] = "Service selected successfully";
        }else{
            $service = ProfessionalServices::where("service_id",$request->id)->where("user_id",auth()->user()->id)->first();
            if(!empty($service)){
                ProfessionalServices::deleteRecord($service->unique_id);
            }
            
            $response['message'] = "Service removed successfully";
        }
        $response['status'] = true;
        return response()->json($response);
    }

    public function getSelectedServices(Request $request)
    {
        $search = $request->input("search");

        $parent_service_id = ProfessionalServices::with('ImmigrationServices')
                            ->where('user_id',auth()->user()->id)
                            ->get()->pluck('parent_service_id')->toArray();
        $records = ImmigrationServices::with('subServices')
                            ->where('parent_service_id','0')
                            ->whereHas("subServices")
                            ->where(function($query) use($search){
                                $query->where("name","LIKE","%".$search."%");
                                $query->orWhere(function($query) use($search){
                                    $query->whereHas("subServices",function($q) use($search){
                                        $q->where("name","LIKE","%".$search."%");
                                    });
                                });
                            })
                            ->whereIn('id',$parent_service_id)
                            ->get();

        $viewData['records'] = $records;
        $viewData['subservices'] = ProfessionalServices::where('user_id',auth()->user()->id)
                                    ->get()->pluck('service_id')->toArray();
        $viewData['checkedParentServiceIds'] = CaseWithProfessionals::where('professional_id', auth()->user()->id) // Adjust based on your column
                                    ->pluck('parent_service_id')
                                    ->toArray();
        $viewData['checkedSubServiceIds'] = CaseWithProfessionals::where('professional_id', auth()->user()->id) // Adjust based on your column
                                    ->pluck('sub_service_id')
                                    ->toArray();

        $viewData['service_template'] = 'lists';
        return view('admin-panel.04-profile.profile.profile-master',$viewData); 
        // return view('admin-panel.04-profile.manage-services.list', $viewData);
    }

    public function getAllSubServices($main_service_id)
    {
        $main_service = ImmigrationServices::where('unique_id',$main_service_id)->first();
        $records = ImmigrationServices::with('subServices')
                            ->where('parent_service_id',$main_service->id)
                            ->get();

        // Get already selected services for this user and main service
        $selectedServices = ProfessionalServices::where('user_id', auth()->user()->id)
                            ->where('parent_service_id', $main_service->id)
                            ->pluck('service_id')
                            ->toArray();

        $selectedSubServices = ProfessionalServices::where('user_id', auth()->user()->id)
                            ->where('parent_service_id', $main_service->id)
                           ->get();
        $viewData['records'] = $records;
        $viewData['selectedServices'] = $selectedServices;
        $viewData['selectedSubServices'] = $selectedSubServices;
        $viewData['subservice_id'] = 0;
         $types = \App\Models\SubServicesTypes::orderBy('id','desc')->get();
           $viewData['types'] = $types;
        $view = View::make('admin-panel.04-profile.manage-services.add-services', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    // public function saveService(Request $request){
    //     return $request->all();
    //     $service = ImmigrationServices::where("id",$request->id)->first();
      
    //     ProfessionalServices::updateOrCreate(['service_id'=>$request->id,'user_id'=>auth()->user()->id],['parent_service_id'=>$service->parent_service_id]);
    //     $response['message'] = "Service selected successfully";
        
    //     $response['status'] = true;
    //     return response()->json($response);
    // }

    public function saveService(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'main_service_id' => 'required|string',
                'selected_service_ids' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first()
                ]);
            }

            $mainServiceId = $request->input('main_service_id');
            $selectedServiceIds = $request->input('selected_service_ids');
            $userId = auth()->user()->id;

            // Convert comma-separated string to array
            $serviceIds = array_filter(explode(',', $selectedServiceIds));

            if (empty($serviceIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No services selected'
                ]);
            }

            // Get the main service to verify it exists
            $mainService = ImmigrationServices::where('id', $mainServiceId)->first();
            if (!$mainService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Main service not found'
                ]);
            }

            // Begin transaction
            DB::beginTransaction();

            try {
                // First, remove existing services for this user and main service
                ProfessionalServices::where('user_id', $userId)
                    ->where('parent_service_id', $mainService->id)
                    ->delete();

                // Insert new selected services
                $servicesToInsert = [];
                foreach ($serviceIds as $serviceId) {
                    // Verify the service exists and belongs to the main service
                    $service = ImmigrationServices::where('id', $serviceId)
                        ->where('parent_service_id', $mainService->id)
                        ->first();

                    if ($service) {
                        $servicesToInsert[] = [
                            'unique_id' => randomNumber(),
                            'user_id' => $userId,
                            'service_id' => $serviceId,
                            'parent_service_id' => $mainService->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($servicesToInsert)) {
                    ProfessionalServices::insert($servicesToInsert);
                }

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Services saved successfully!',
                    'data' => [
                        'main_service_id' => $mainServiceId,
                        'selected_count' => count($servicesToInsert),
                        'services' => $servicesToInsert
                    ]
                ]);

            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving services: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Get sub service types for a given subservice id
     */
    public function getSubServiceType(Request $request)
    {
        $subservice_id = $request->input('subservice_id');
        if (!$subservice_id) {
            return response()->json(['status' => false, 'message' => 'No subservice id provided.']);
        }

        // All available types
        $types = SubServicesTypes::select('id','unique_id','name')->orderBy('id','desc')->get();

        // Fetch already saved types for this subservice and user
        $savedTypeIds = \App\Models\ProfessionalSubServices::where('professional_service_id', $subservice_id)
            ->where('user_id', auth()->user()->id)
            ->pluck('sub_services_type_id')
            ->toArray();

        // Fetch all ProfessionalSubServices for this subservice and user, with type name
        $serviceDetails = \App\Models\ProfessionalSubServices::with('subServiceTypes')
            ->where('professional_service_id', $subservice_id)
            ->where('user_id', auth()->user()->id)
            ->get();

        // Mark selected types
        $types = $types->map(function($type) use ($savedTypeIds) {
            $type->selected = in_array($type->id, $savedTypeIds);
            return $type;
        })->toArray();

        $viewData['types'] = $types;
        $viewData['subservice_id'] = $subservice_id;
        $viewData['savedTypeIds'] = $savedTypeIds; // For Blade if needed
        $viewData['serviceDetails'] = $serviceDetails;

        $view = view('admin-panel.04-profile.manage-services.partials.subservice-types', $viewData)->render();

        return response()->json([
            'status' => true,
            'contents' => $view,
            'types' => $types,
            'subservice_id' => $subservice_id,
            'savedTypeIds' => $savedTypeIds,
            'serviceDetails' => $serviceDetails
        ]);
    }

    // public function addServiceType(Request $request)
    // {
       
    //     $record = ProfessionalServices::where("id", $request->subservice_id)->first();
    //     if (!$record) {
    //         return response()->json(['status' => false, 'message' => 'Subservice not found.']);
    //     }

    //     $selectedTypeIds = $request->selected_service_type_ids;
    //     if (!is_array($selectedTypeIds)) {
    //         $selectedTypeIds = explode(',', $selectedTypeIds); // In case it's a comma-separated string
    //     }

    //     $created = [];
    //     foreach ($selectedTypeIds as $typeId) {
    //         if (!$typeId) continue;
    //         $created[] = ProfessionalSubServices::create([
    //             'user_id' => \Auth::user()->id,
    //             'professional_service_id' => $record->id,
    //             'service_id' => $record->service_id,
    //             'sub_services_type_id' => $typeId,
    //             'added_by' => \Auth::user()->id,
    //             'status' => 'pending'
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Service types added!',
    //         'created' => $created
    //     ]);
    // }
    public function addServiceType(Request $request)
{
    $record = ProfessionalServices::where("id", $request->subservice_id)->first();
    if (!$record) {
        return response()->json(['status' => false, 'message' => 'Subservice not found.']);
    }

    $selectedTypeIds = $request->selected_service_type_ids;
    if (!is_array($selectedTypeIds)) {
        $selectedTypeIds = explode(',', $selectedTypeIds); // If comma-separated string
    }

    $userId = auth()->user()->id;

    // Get existing type IDs for this user and subservice
    $existingTypeRecords = ProfessionalSubServices::where('user_id', $userId)
        ->where('professional_service_id', $record->id)
        ->pluck('sub_services_type_id')
        ->toArray();

    // Calculate which to delete and which to add
    $toDelete = array_diff($existingTypeRecords, $selectedTypeIds);
    $toAdd = array_diff($selectedTypeIds, $existingTypeRecords);

    // Delete records not in the selected list
    if (!empty($toDelete)) {
        ProfessionalSubServices::where('user_id', $userId)
            ->where('professional_service_id', $record->id)
            ->whereIn('sub_services_type_id', $toDelete)
            ->delete();
    }

    // Create new records for newly selected types
    $created = [];
    foreach ($toAdd as $typeId) {
        if (!$typeId) continue;
        $created[] = ProfessionalSubServices::create([
            'user_id' => $userId,
            'professional_service_id' => $record->id,
            'service_id' => $record->service_id,
            'sub_services_type_id' => $typeId,
            'added_by' => $userId,
            'status' => 'pending'
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Service types updated!',
        'created' => $created
    ]);
}


    public function addServiceTypeDetail(Request $request,$id)
    {
        $record = ProfessionalSubServices::where('unique_id',$id)->first();

        $viewData['record'] = $record;

        $service_id = $record->professional_service_id;
        $forms = Forms::orderBy('id','desc')->where('added_by', auth()->user()->id)
                            ->whereHas('serviceAssesmentForm',function($query) use($service_id){
                                $query->where("professional_service_id",$service_id);
                            })
                            ->get();

        $viewData['forms'] = $forms;
        $viewData['documents'] = DocumentsFolder::where('added_by',auth()->user()->id)->get();
        $view = View::make('admin-panel.04-profile.manage-services.edit-service-details', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function updateSubServiceType(Request $request,$id)
    {
        if($request->type == 'form'){

            $validator = Validator::make($request->all(), [
                'consultancy_fees' => 'required',
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();

                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                $response['error_type'] = "validation";
                return response()->json($response);
            }

            $professionalSubServices = ProfessionalSubServices::where('unique_id',$id)->first();
            $professionalSubServices->consultancy_fees = $request->consultancy_fees;
            $professionalSubServices->professional_fees = $request->professional_fees;
            $professionalSubServices->save();

        }else{

            $validator = Validator::make($request->all(), [
                'description' => 'required',
                // 'form_id' => 'required',
                // 'document' => 'required',
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();

                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                $response['error_type'] = "validation";
                return response()->json($response);
            }

            $professionalSubServices = ProfessionalSubServices::where('unique_id',$id)->first();
            $professionalSubServices->description = $request->description;
            $professionalSubServices->form_id = $request->input('form_id');
            $professionalSubServices->document_folders = !empty($request->document) ? implode(',',$request->document) : '';
            $professionalSubServices->save();

        }
        
        $response['status'] = true;
        $response['redirect_back'] = baseUrl('manage-services');
        $response['message'] = "Record added successfully";
        return response()->json($response);
    }

    // public function generateAssessment($id)
    // {
    //     try {
           
    //         $record = ProfessionalServices::where('unique_id', $id)->first();
    //         $viewData['record'] = $record;
    //         $viewData['pageTitle'] = "Generate Assessment Using AI";
    //         $viewData['id'] = $id;
    //         return view('admin-panel.04-profile.manage-services.generate-assesment-form', $viewData);
    //     } catch (\Exception $e) {
    //         abort(404, 'Unable to load assessment generation form.');
    //     }
    // }

    // private function findProfessionalServiceOrFail($id)
    // {
    //     $service = ProfessionalServices::where('unique_id', $id)->first();
    //     if (!$service) {
    //         abort(404, 'Professional Service not found');
    //     }
    //     return $service;
    // }

    // public function submitGenerateAssessment($id,Request $request){
    //     try {
    //         $prof_sub_service = ProfessionalServices::where('unique_id',$id)->first();
    //         $msg = $request->message;

    //         if($request->is_modify == "no"){
    //             $message = "I m providing immigration service for ".($prof_sub_service->subServices->parentService->name??'')." ".$prof_sub_service->subServices->name??''.". ".$msg;

    //         }else{
    //             $message = $msg;
    //         }
           
    //         $form_type = $request->form_type;
    //         $apiData['user_id'] = (string) auth()->user()->unique_id;
    //         $apiData['message'] = $message;
    //         $apiData['service_id'] = (string) $prof_sub_service->service_id;
    //         $apiData['form_type'] = (string) $request->form_type;

          
    //         $apiResponse = assistantApiCall('application_form', $apiData);
            
    //         if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
    //             $sample_json = formJsonSample();
    //             $json_sample = array();
    //             foreach($sample_json as $js){
    //                 $json_sample[$js['fields']] = $js;
    //             }
    //             $form_json = array();
    //             if($request->form_type == 'step_form'){
    //                 $data_arr = $apiResponse['message'];
    //                 $form_name = $data_arr['form_name'];
    //                 // $form_name = "Step Form ".mt_rand();
    //                 $steps = $data_arr['steps'];
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
    //                 if(!empty($form_json)){
    //                     $viewData['fg_field_json'] = json_encode($form_json);
    //                     $viewData['form_name'] = $form_name;
    //                     $viewData['form_type'] = $form_type;
    //                     $viewData['id'] = $id;
    //                     $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
    //                 }
                    
    //                 $response['preview_form'] = $preview_form;
    //                 if(!empty($form_json)){
    //                     $response['fg_field_json'] = json_encode($form_json);
    //                 }else{
    //                     $response['fg_field_json'] = "";
    //                 }
    //                 $response['form_type'] = $form_type;
    //                 $response['status'] = true;
    //             }else{
    //                 $data_arr = $apiResponse['message'];
                  
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
    //                     if(!empty($form_json)){
    //                         $viewData['fg_field_json'] = json_encode($form_json);
    //                         $viewData['form_name'] = $form_name;
    //                         $viewData['form_type'] = $form_type;
    //                         $viewData['id'] = $id;
    //                         $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
    //                     }
                       
    //                     $response['preview_form'] = $preview_form;
    //                     if(!empty($form_json)){
    //                         $response['fg_field_json'] = json_encode($form_json);
    //                     }else{
    //                         $response['fg_field_json'] = "";
    //                     }
    //                     $response['form_type'] = $form_type;
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
    //         return $this->jsonError('An unexpected error occurred: ' . $e->getMessage());
    //     }
    // }

    // public function submitGenerateAssessment($id,Request $request){
    //     try {
    //         $prof_sub_service = ProfessionalServices::where('unique_id',$id)->first();
          
    //         // $msg = $request->message;

    //         // if($request->is_modify == "no"){
    //         //     $message = "I m providing immigration service for ".($prof_sub_service->subServices->parentService->name??'')." ".$prof_sub_service->subServices->name??''.". ".$msg;

    //         // }else{
    //         //     $message = $msg;
    //         // }
           
    //         $form_type = 'single_form';
    //         // $apiData['user_id'] = (string) auth()->user()->unique_id;
    //         // $apiData['message'] = $message;
    //         $apiData['service_name'] = $prof_sub_service->subServices->name;

          
    //         $apiResponse = assistantApiCall('ai-agents/generate-service-assessment-form', $apiData);
            
    //         if(isset($apiResponse['status']) && $apiResponse['status'] == 'success'){
    //             $sample_json = formJsonSample();
    //             $json_sample = array();
    //             foreach($sample_json as $js){
    //                 $json_sample[$js['fields']] = $js;
    //             }
    //             $form_json = array();
    //             // if($request->form_type == 'step_form'){
    //             //     $data_arr = $apiResponse['message'];
    //             //     $form_name = $data_arr['form_name'];
    //             //     // $form_name = "Step Form ".mt_rand();
    //             //     $steps = $data_arr['steps'];
    //             //     $index = 0;
    //             //     $fg_index = 0;
    //             //     foreach($steps as $step){
    //             //         $groupFields = array();
    //             //         $fg_field_format = array();
    //             //         $fg_field_format = $json_sample['fieldGroups'];
    //             //         $fg_field_format['groupFields'] = $groupFields;
    //             //         $fg_field_format['settings']['label'] = str_replace("'s","&apos;",$step['step_heading']);
    //             //         $fg_field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //         $fg_field_format['settings']['stepHeading'] = str_replace("'s","&apos;",$step['step_heading']);
    //             //         $fg_field_format['index'] = randomNumber();
    //             //         $fg_index = $index;
    //             //         $form_json[$index] = $fg_field_format;
    //             //         $index++;
    //             //         foreach($step['questions'] as $json){
    //             //             $field_format = array();
    //             //             $newOptions = array();
    //             //             if(isset($json['options'])){
    //             //                 $options = $json['options'];
    //             //                 foreach($options as $k => $v){
    //             //                     $options[$k] = str_replace("'s","&apos;",$v);
    //             //                     $newOptions[mt_rand(1000,9999)] = str_replace("'s","&apos;",$v);
    //             //                 }
    //             //                 $json['options'] = $newOptions;
    //             //             }
                            
    //             //             if($json['type'] == 'text'){
    //             //                 $field_format = $json_sample['textInput'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                                
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'number'){
    //             //                 $field_format = $json_sample['numberInput'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'radio'){
    //             //                 $field_format = $json_sample['radio'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['options'] = $json['options'];
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'checkbox'){
    //             //                 $field_format = $json_sample['checkbox'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
                                
    //             //                 $field_format['settings']['options'] = $json['options'];
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'dropdown'){
    //             //                 $field_format = $json_sample['dropDown'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['options'] = $json['options'];
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'email'){
    //             //                 $field_format = $json_sample['emailInput'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'textarea'){
    //             //                 $field_format = $json_sample['textarea'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             if($json['type'] == 'date'){
    //             //                 $field_format = $json_sample['dateInput'];
    //             //                 $field_format['index'] =randomNumber();
    //             //                 $field_format['settings']['label'] = str_replace("'s","&apos;",$json['question']);
    //             //                 $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
    //             //                 $form_json[$index] = $field_format;
    //             //             }
    //             //             $groupFields[] = $field_format['index'];
    //             //             $index++;
    //             //         }
    //             //         $form_json[$fg_index]['groupFields'] = $groupFields;
    //             //     }
    //             //     $preview_form = '';
    //             //     if(!empty($form_json)){
    //             //         $viewData['fg_field_json'] = json_encode($form_json);
    //             //         $viewData['form_name'] = $form_name;
    //             //         $viewData['form_type'] = $form_type;
    //             //         $viewData['id'] = $id;
    //             //         $preview_form = view("admin-panel.04-profile.my-services.preview-assessment-form",$viewData)->render();
    //             //     }
                    
    //             //     $response['preview_form'] = $preview_form;
    //             //     if(!empty($form_json)){
    //             //         $response['fg_field_json'] = json_encode($form_json);
    //             //     }else{
    //             //         $response['fg_field_json'] = "";
    //             //     }
    //             //     $response['form_type'] = $form_type;
    //             //     $response['status'] = true;
    //             // }else{
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
    //             // }
                    
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


    // public function saveGenerateAssessment($id, Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'formName' => 'required',
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

    //     $professional_service = ProfessionalServices::where('unique_id',$id)->first();
    //     $forms = Forms::create([
    //         'unique_id' => randomNumber(),
    //         'added_by' => \Auth::user()->id,
    //         'name' => $request->input('formName'),
    //         'form_type' => $request->input('form_type'),
    //         'fg_field_json' => $request->input('fg_field_json'),
    //     ]);

    //     ServiceAssesmentForm::create([
    //         'unique_id' => randomNumber(),
    //         'added_by' => \Auth::user()->id,
    //         'professional_service_id' => $professional_service->id,
    //         'service_id' => $professional_service->service_id,
    //         'form_id' => $forms->id
    //     ]);

    //     $response['status'] = true;
    //     $response['message'] = "Record added successfully";
    //     $response['redirect_back'] = baseUrl('manage-services/list-assessment/'.$id);
    //     return response()->json($response);
    // }

    public function pinMyService(Request $request)
    {
        $id = $request->input("id");
        $is_pin = $request->input("is_pin");

        $record = ProfessionalServices::where('id',$id)->first();
        if (! $record->isEditableBy(auth()->id())) {
        return handleUnauthorizedAccess('You are not authorized to edit this Page');
        }
        ProfessionalServices::where('id',$id)->where('user_id',auth()->user()->id)->update(['is_pin' => $is_pin]);
        // for ($i = 0; $i < count($ids); $i++) {
        //     ProfessionalServices::where('service_id',$ids[$i])->where('user_id',auth()->user()->id)->update(['is_pin' => 1]);
        // }
        $response['status'] = true;
        $response['message'] = 'Services pin successfully';
        \Session::flash('success', 'Services pin successfully');
        return response()->json($response);

    }

        public function pinnedServicesAjax(Request $request)
    {
        $search = $request->input("search");
        $records = ImmigrationServices::with('subServices')
                            ->where('parent_service_id','0')
                            ->whereHas("subServices")
                            ->where(function($query) use($search){
                                $query->where("name","LIKE","%".$search."%");
                                $query->orWhere(function($query) use($search){
                                    $query->whereHas("subServices",function($q) use($search){
                                        $q->where("name","LIKE","%".$search."%");
                                    });
                                });
                            })
                            ->get();

        $viewData['records'] = $records;
        $services = ProfessionalServices::visibleToUser(auth()->user()->id)
                                        ->where("is_pin",1)
                                        ->get();
        $viewData['services'] = $services;
       
        $view = View::make('admin-panel.04-profile.manage-services.selected-pin-service', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['records'] = $records;
        return response()->json($response);
    }

    public function removeSubServiceType($id)
    {
        $record = ProfessionalSubServices::where('unique_id',$id)->first();
        if (!$record) {
            abort(404, 'Service not found');
        }
        ProfessionalSubServices::deleteRecord($record->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function removeSubService($id)
    {
        $record = ProfessionalServices::where('unique_id',$id)->first();
        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ]);
        }
        
        // Check if user has permission to delete this service
        if ($record->user_id != auth()->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to delete this service'
            ]);
        }
        
        ProfessionalServices::deleteRecord($record->unique_id);
        
        return response()->json([
            'status' => true,
            'message' => 'Service removed successfully'
        ]);
    }

    public function addPathway($unique_id = 0)
    {
        if($unique_id != 0){
            $service = ImmigrationServices::where('unique_id',$unique_id)->first();
            if(empty($service)){
                return redirect()->to(baseUrl('/manage-services'))
                 ->with('error', 'Service not found');
            }
        }

        $viewData['pageTitle'] = 'Add Configuration';
        $viewData['subServiceType'] = SubServicesTypes::orderBy('id','desc')->get();
        $viewData['main_service_id'] = $unique_id;
        return view('admin-panel\04-profile\manage-services\configuration\lists',$viewData);
    }

    // public function fetchPathways(Request $request)
    // {
    //     $records = ImmigrationServices::with('subServices')
    //                         ->where('parent_service_id','0')
    //                         ->whereHas("subServices")
    //                         ->get();

    //     $viewData['records'] = $records;
    //     $viewData['main_service_id'] = $request->main_service_id;
    //     $view = View::make('admin-panel.04-profile.manage-services.configuration.all-pathways', $viewData);
    //     $contents = $view->render();
    //     $response['status'] = true;
    //     $response['contents'] = $contents;
    //     return response()->json($response);

    // }
    public function fetchPathways(Request $request)
    {
        $allRecords = ImmigrationServices::with('subServices')
            ->where('parent_service_id','0')
            ->whereHas("subServices")
            ->get();

        // Only include parent services that are pending for the current user
        $records = $allRecords->filter(function($service) {
            return checkPendingService($service->id);
        })->values();
     
        $viewData['records'] = $records;
        $viewData['main_service_id'] = $request->main_service_id;
        $view = View::make('admin-panel.04-profile.manage-services.configuration.all-pathways', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);

    }

    public function fetchSubPathways(Request $request, $service_id)
    {
       
        $main_service_id = $request->main_service_id;

        if($main_service_id != 0){
            $service = ImmigrationServices::where('unique_id',$request->main_service_id)->first();
        }else{
            $service = ImmigrationServices::where('unique_id',$service_id)->first();
        }
        

        $parent_service =  ProfessionalServices::where('parent_service_id',$service->id)->where('user_id',auth()->user()->id)->get()->pluck('service_id')->toArray();
        $records = ImmigrationServices::where('parent_service_id',$service->id)
                            // ->whereNotIn('id',$parent_service)
                            ->get();

        $viewData['records'] = $records;
        $viewData['parent_service'] = $parent_service;
        $view = View::make('admin-panel.04-profile.manage-services.configuration.sub-pathways', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function savePathways(Request $request)
    {
        if(!empty($request->selectedSubPathway)){
            $id = [];
            $main_service_id = '';
            
            // Get the main service ID from the first selected service
            $first_service = ImmigrationServices::where('unique_id',$request->selectedSubPathway[0])->first();
            $main_service = ImmigrationServices::where('id',$first_service->parent_service_id)->first();
            $main_service_id = $main_service->unique_id;
            
            foreach($request->selectedSubPathway as $value){
                $service = ImmigrationServices::where('unique_id',$value)->first();
                
                // Check if this service is already saved for this user
                $existing_service = ProfessionalServices::where('user_id',auth()->user()->id)
                    ->where('service_id',$service->id)
                    ->where('parent_service_id',$service->parent_service_id)
                    ->first();
                
                if (!$existing_service) {
                    $professionalServices = new ProfessionalServices;
                    $professionalServices->parent_service_id = $service->parent_service_id;
                    $professionalServices->service_id = $service->id;
                    $professionalServices->user_id = auth()->user()->id;
                    $professionalServices->is_pin = 0;
                    $professionalServices->save();

                    $id[] = $professionalServices->unique_id;
                } else {
                    $id[] = $existing_service->unique_id;
                }
            }
            
            $response['status'] = true;
            $response['redirect_url'] = baseUrl('manage-services/add-pathway/'.$main_service_id);
            $response['message'] = 'Saved Successfully';
           
        }else{
            $response['status'] = false;
            $response['message'] = 'Please select services';
           
        }
         return response()->json($response);
    }

    public function addSubTypePathways(Request $request,$id)
    {
        $record = ProfessionalServices::where('unique_id',$id)->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ]);
        }

        // Get the main service for debugging
        $main_service = ImmigrationServices::where('id', $record->parent_service_id)->first();
        
        // Get existing configurations for this service
        $existingConfigurations = ProfessionalSubServices::where('professional_service_id', $record->id)
            ->where('user_id', auth()->user()->id)
            ->with(['subServicesType', 'form'])
            ->get();
       
        $viewData['record'] = $record;
        $viewData['main_service'] = $main_service; // Pass for debugging
        $viewData['existingConfigurations'] = $existingConfigurations; // Pass existing configurations

        $service_id = $record->id;
        $forms = Forms::orderBy('id','desc')->where('added_by', auth()->user()->id)
                            ->whereHas('serviceAssesmentForm',function($query) use($service_id){
                                $query->where("professional_service_id",$service_id);
                            })
                            ->get();

        //  $forms = Forms::orderBy('id','desc')->where('added_by', auth()->user()->id)
                           
        //                     ->get();

        $viewData['forms'] = $forms;
        $viewData['subServiceType'] = SubServicesTypes::orderBy('id','desc')->get();
        $viewData['documents'] = DocumentsFolder::where('added_by',auth()->user()->id)->get();
        $view = View::make('admin-panel.04-profile.manage-services.configuration.add-configuration-detail', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        $response['debug'] = [
            'service_id' => $record->id,
            'service_unique_id' => $record->unique_id,
            'main_service_name' => $main_service ? $main_service->name : 'Unknown',
            'existing_configurations_count' => $existingConfigurations->count()
        ];
        return response()->json($response);
    }

    private function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    public function saveSubTypePathways(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'selected_service_type_ids' => 'required',
            'professional_fees' => 'required',
            'consultancy_fees' => 'required',
            'document' => 'required',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        $service = ProfessionalServices::where('unique_id',$request->id)->first();
        
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ]);
        }

        // Validate that the service belongs to the correct main service
        $main_service = ImmigrationServices::where('id', $service->parent_service_id)->first();
        if (!$main_service) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid service configuration'
            ]);
        }

        $ids = [];
        if($request->selected_service_type_ids != '')
        {
            foreach(explode(',',$request->selected_service_type_ids) as $value)
            {
                // Check if this configuration already exists to avoid duplicates
                $existing_config = ProfessionalSubServices::where('user_id', auth()->user()->id)
                    ->where('professional_service_id', $service->id)
                    ->where('sub_services_type_id', $value)
                    ->first();
                
                if (!$existing_config) {
                    $professional_servies = ProfessionalSubServices::create([
                        'unique_id' => randomNumber(),
                        'user_id' => auth()->user()->id,
                        'professional_service_id' => $service->id,
                        'service_id' => $service->service_id,
                        'sub_services_type_id' => $value,
                        'professional_fees' => $request->professional_fees,
                        'consultancy_fees' => $request->consultancy_fees,
                        'form_id' => $request->form_id,
                        'document_folders' =>$request->document,
                        'added_by' => auth()->user()->id,
                        'status' => 'pending'
                    ]);
                    $ids[] = $professional_servies->unique_id;
                    
                    \Log::info('Created new configuration', [
                        'configuration_id' => $professional_servies->id,
                        'unique_id' => $professional_servies->unique_id,
                        'sub_services_type_id' => $value
                    ]);
                } else {
                    // Update existing configuration
                    $existing_config->update([
                        'professional_fees' => $request->professional_fees,
                        'consultancy_fees' => $request->consultancy_fees,
                        'form_id' => $request->form_id,
                        'document_folders' => $request->document,
                    ]);
                    $ids[] = $existing_config->unique_id;
                    
                    \Log::info('Updated existing configuration', [
                        'configuration_id' => $existing_config->id,
                        'unique_id' => $existing_config->unique_id,
                        'sub_services_type_id' => $value
                    ]);
                }
            }

            // Get all configurations for this specific service
            $professionalSubService = ProfessionalSubServices::with(['subServicesType', 'form'])
                ->where('professional_service_id', $service->id)
                ->where('user_id', auth()->user()->id)
                ->get();
            
            $viewData['records'] = $professionalSubService;
            $view = View::make('admin-panel.04-profile.manage-services.configuration.configuration-detail', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['status'] = true;
            $response['message'] = 'Configuration saved successfully';
            $response['debug'] = [
                'service_id' => $service->id,
                'service_unique_id' => $service->unique_id,
                'main_service_name' => $main_service->name,
                'configurations_count' => $professionalSubService->count(),
                'debug_service_name' => $request->debug_service_name ?? 'Not provided'
            ];
            return response()->json($response);

        }else{
            $response['status'] = false;
            $response['message'] = 'Please select at least one service type';
            return response()->json($response);
        }
       
    }

    public function removeConfiguration($id)
    {
        $record = ProfessionalSubServices::where('unique_id', $id)->first();
        
        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Configuration not found'
            ]);
        }

        // Check if user has permission to delete this configuration
        if (!$record->isEditableBy(auth()->user()->id)) {
         return handleUnauthorizedAccess('You are not authorized to edit this Page');
        }

        $record->delete();

        return response()->json([
            'status' => true,
            'message' => 'Configuration removed successfully'
        ]);
    }

    public function displaySubPathway(Request $request)
    {
        $main_service = ImmigrationServices::where('unique_id',$request->main_service_id)->first();
        
        if (!$main_service) {
            return response()->json([
                'status' => false,
                'message' => 'Main service not found'
            ]);
        }
        
        // Get only the sub-services that belong to this specific main service
        $sub_service_ids = ImmigrationServices::where('parent_service_id',$main_service->id)->get()->pluck('id')->toArray();

        // Filter professional services to only show those for this specific main service
        // The key fix: filter by the exact main service ID and ensure service_id is in the sub-services list
        $records = ProfessionalServices::where('user_id',auth()->user()->id)
                    ->where('parent_service_id',$main_service->id) // This ensures we only get services for this specific parent
                    ->whereIn('service_id',$sub_service_ids) // This ensures we only get sub-services of this parent
                    ->with(['subServices', 'professionalServiceTypes']) // Eager load the subServices and professionalServiceTypes relationships
                    ->get();
        
       
        // Check for existing configurations for each service
        foreach($records as $record) {
            $configurations = \App\Models\ProfessionalSubServices::where('professional_service_id', $record->id)
                ->where('user_id', auth()->user()->id)
                ->count();
           
        }
        
        $viewData['records'] = $records;
        $viewData['main_service'] = $main_service; // Pass the main service for debugging
        $view = View::make('admin-panel.04-profile.manage-services.configuration.selected-pathways', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        $response['count'] = $records->count();
        $response['message'] = 'Data loaded successfully';
        return response()->json($response);
    }

    public function editConfiguration(Request $request, $id)
    {
        $configuration = ProfessionalSubServices::where('unique_id', $id)->first();
        
        if (!$configuration) {
            return response()->json([
                'status' => false,
                'message' => 'Configuration not found'
            ]);
        }

        // Check if user has permission to edit this configuration
        if (!$configuration->isEditableBy(auth()->user()->id)) {
          return handleUnauthorizedAccess('You are not authorized to edit this Page');
        }

        // Get the main service for debugging
        $main_service = ImmigrationServices::where('id', $configuration->subService->parent_service_id ?? 0)->first();
        
 $service_id = $configuration->professional_service_id;
         $forms = Forms::orderBy('id','desc')->where('added_by', auth()->user()->id)
                            ->whereHas('serviceAssesmentForm',function($query) use($service_id){
                                $query->where("professional_service_id",$service_id);
                            })
                            ->get();

        $viewData['configuration'] = $configuration;
        $viewData['main_service'] = $main_service;
        $viewData['forms'] = $forms;
        $viewData['subServiceType'] = SubServicesTypes::orderBy('id','desc')->get();
        $viewData['documents'] = DocumentsFolder::where('added_by',auth()->user()->id)->get();
        
        $view = View::make('admin-panel.04-profile.manage-services.configuration.edit-configuration-detail', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        $response['debug'] = [
            'configuration_id' => $configuration->id,
            'configuration_unique_id' => $configuration->unique_id,
            'sub_services_type_name' => $configuration->subServicesType->name ?? 'Unknown',
            'main_service_name' => $main_service ? $main_service->name : 'Unknown'
        ];
        return response()->json($response);
    }

    public function updateConfiguration(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'professional_fees' => 'required',
            'consultancy_fees' => 'required',
            'document' => 'required',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        $configuration = ProfessionalSubServices::where('unique_id', $id)->first();
        
        if (!$configuration) {
            return response()->json([
                'status' => false,
                'message' => 'Configuration not found'
            ]);
        }

        // Check if user has permission to edit this configuration
        if (!$configuration->isEditableBy(auth()->user()->id)) {
           return handleUnauthorizedAccess('You are not authorized to edit this Page');
        }


        // Update the configuration
        $configuration->update([
            'professional_fees' => $request->professional_fees,
            'consultancy_fees' => $request->consultancy_fees,
            'form_id' => $request->form_id,
            'document_folders' => $request->document,
        ]);

        // Get the updated configuration with relationships
        $updatedConfiguration = ProfessionalSubServices::with(['subServicesType', 'form'])
            ->where('unique_id', $id)
            ->first();

        // Get the main service for debugging
        $main_service = ImmigrationServices::where('id', $configuration->subService->parent_service_id ?? 0)->first();
        
       

        $viewData['records'] = collect([$updatedConfiguration]);
        $view = View::make('admin-panel.04-profile.manage-services.configuration.configuration-detail', $viewData);
        $contents = $view->render();
        
        $response['contents'] = $contents;
        $response['status'] = true;
        $response['message'] = 'Configuration updated successfully';
        $response['debug'] = [
            'configuration_id' => $configuration->id,
            'configuration_unique_id' => $configuration->unique_id,
            'sub_services_type_name' => $configuration->subServicesType->name ?? 'Unknown',
            'main_service_name' => $main_service ? $main_service->name : 'Unknown'
        ];
        return response()->json($response);
    }


    
}   
