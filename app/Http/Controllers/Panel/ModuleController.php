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

class ModuleController extends Controller
{
    /**
     * Format validation errors for JSON responses.
     */
    private function formatValidationErrors(array $errors)
    {
        $errMsg = [];
        foreach ($errors as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Fetch a module by unique_id or fail.
     */
    private function getModuleByUniqueIdOrFail($id)
    {
        return Module::with(['moduleAction'])->where('unique_id', $id)->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $viewData['pageTitle'] = "Module";
        return view('admin-panel.09-utilities.module.lists', $viewData);
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
        $records = Module::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
            })->with(['user'])
            ->orderBy('id', "desc")
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.09-utilities.module.ajax-list', $viewData);
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
        $actions = Action::orderBy('id','desc')->get();
        $viewData['pageTitle'] = "Add Module";
        $viewData['action'] = $actions;
        return view('admin-panel.09-utilities.module.add', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:modules,name,',
            'actions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator->errors()->toArray());
            return response()->json($response);
        }

        \DB::beginTransaction();
        try {
            $module = Module::create([
                'unique_id' => randomNumber(),
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name')),
                'added_by' => \Auth::user()->id,
            ]);

            foreach ($request->actions as $value) {
                if (!empty(trim($value))) {
                    ModuleAction::create(['module_id' => $module->id, 'action' => trim($value)]);
                }
            }

            \DB::commit();

            $response['status'] = true;
            $response['message'] = "Module added successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get module data for modal editing.
     */
    public function get($id)
    {
        try {
            $module = $this->getModuleByUniqueIdOrFail($id);
            return response()->json([
                'status' => true,
                'module' => $module
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Module not found'
            ]);
        }
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
        $viewData['record'] = $this->getModuleByUniqueIdOrFail($id);
        $viewData['options'] = ['apple', 'banana', 'cherry'];
        $viewData['selectedOptions'] = ['banana', 'cherry'];
        $viewData['module_action'] = collect($viewData['record']->moduleAction)->pluck('action')->toArray();
        $viewData['pageTitle'] = "Edit Module";
        $viewData['action'] = Action::orderBy('id','desc')->get();
        return view('admin-panel.09-utilities.module.edit', $viewData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $object = Module::where('unique_id',$id)->firstOrFail();
        
        // Add authorization check
        if (! $object->isEditableBy(auth()->id())) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this module.'
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:modules,name,' . $object->id,
            'actions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator->errors()->toArray());
            return response()->json($response);
        }

        \DB::beginTransaction();
        try {
            Module::where('id', $object->id)->update([
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name')),
            ]);

            // Delete existing actions
            ModuleAction::where("module_id", $object->id)->delete();

            // Add new actions
            foreach ($request->actions as $value) {
                if (!empty(trim($value))) {
                    ModuleAction::create([
                        'module_id' => $object->id,
                        'action' => trim($value)
                    ]);
                }
            }

            \DB::commit();

            $response['status'] = true;
            $response['message'] = "Module updated successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteSingle($id)
    {
        $action = $this->getModuleByUniqueIdOrFail($id);
        Module::deleteRecord($action->id);
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
        \DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $act = $this->getModuleByUniqueIdOrFail($id);
                Module::deleteRecord($act->id);
            }
            \DB::commit();
            $response['status'] = true;
            \Session::flash('success', 'Records deleted successfully');
            return response()->json($response);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rolePrivileges()
    {
        $viewData['pageTitle'] = "Role Privileges";
        $viewData['activeTab'] = 'role-privileges';

       $records = Module::where('panel','professional')->get();
        $professional=staffProfessionals(auth()->user()->id);

        if(auth()->user()->role=="professional"){
            $addedBy=auth()->user()->id;
        }else{
            $addedBy=$professional->added_by;
        }
        $role_permission = RolePrevilege::where('added_by',$addedBy)->get();
       
        $role_wise_permissions = array();
        foreach($role_permission as $permission){
            $role_wise_permissions[$permission->role][$permission->module][] = $permission['action'];
        }

        $viewData['records'] = $records;
        $viewData['role_wise_permissions'] = $role_wise_permissions;

        $roles = Roles::where("added_by",auth()->user()->id)
        ->orderBy('id', "desc")
        ->pluck('slug');
        if(!empty($roles)){
            $roles = $roles->toArray();
        }else{
            $roles = array();
        }
        $viewData['roles'] = $roles;
        return view('admin-panel.09-utilities.module.role-permission', $viewData);
    }

  public function saveRolePrivileges(Request $request){
    try{

        $validator = Validator::make($request->all(), [
          'permission' => 'required',
        ],[
          'permission.required' => 'The permission is required.',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
          $response['status'] = false;
          $errorMessages = $validator->errors()->toArray();
          $formattedErrors ='';

          foreach ($errorMessages as $field => $errors) {
            $formattedErrors .= $errors[0]."<br>";
          }

          $response['message'] = $formattedErrors;
          return response()->json($response);
        }
      
        $permission = $request->permission;
        foreach($permission as $role_id => $modules){
            $role_permission_ids = array();
            foreach($modules as $module => $actions){
                foreach($actions as $action){
                    $role_permission = RolePrevilege::updateOrCreate(['role'=>$role_id,"module"=>$module,"action"=>$action],['added_by'=>\Auth::user()->id]);
                    $role_permission_ids[] = $role_permission->id;
                }
            }
            RolePrevilege::where("role",$role_id)->whereNotIn("id",$role_permission_ids)->delete();
        }
        
      $response = [
        'status' => true,
        'redirect_back' => baseUrl('role-privileges'),
        'message' => "Module updated successfully"
      ];

      // Return response as JSON
      return response()->json($response);
    } catch (\Exception $e) {
      

      // Handle exception and return error response
      return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
  }
}
