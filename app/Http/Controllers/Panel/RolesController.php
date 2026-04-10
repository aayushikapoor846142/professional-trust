<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;

class RolesController extends Controller
{
 
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Role";
        return view('admin-panel.06-roles.roles.lists', $viewData);
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

        $records = Roles::with(['user'])
                ->where(function ($query) use ($search) {
                    if ($search != '') {
                        $query->where("name", "LIKE", "%" . $search . "%")
                            ->orWhereHas('user', function ($q) use ($search) {
                                $q->where("first_name", "LIKE", "%" . $search . "%")
                                ->orWhere("last_name", "LIKE", "%" . $search . "%");
                            });
                    }
                })
                ->visibleToUser(auth()->user()->id)
                ->orderBy($sortColumn, $sortDirection)
                ->paginate();
      
        $viewData['records'] = $records;
        $view = View::make('admin-panel.06-roles.roles.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
       
        $viewData['pageTitle'] = 'Add Role';
        $view = view('admin-panel.06-roles.roles.add', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);

    }

    /**
     * Format validation errors for JSON response.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return array
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
     * Store a newly created role in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|input_string|max:255'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        $roles = Roles::where('name',$request->name)->where('added_by',auth()->user()->id)->first();
        if(!empty($roles)){
            $response['status'] = false;
            $response['error_type'] = 'unique_name';
            $response['message'] = 'Name is already added.';
            return response()->json($response);
        }   
        $professional=staffProfessionals(auth()->user()->id);

        // if(auth()->user()->role=="professional"){
        //     $addedBy=auth()->user()->id;
        // }else{
        //     $addedBy=$professional->added_by;
        // }
        // 
        Roles::create([
            'unique_id' => randomNumber(),
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'added_by' => auth()->user()->id,
        ]);

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('roles');
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified roles.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $record = Roles::where('unique_id',$id)->first();
        if (!$record) {
            abort(403, 'Role not found');
        }
      
        $viewData['pageTitle'] = "Edit Role";
        $viewData['record'] = $record;
        $view = view('admin-panel.06-roles.roles.edit', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
        
    }

    /**
     * Update the specified country in the database.
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $object = Roles::where('unique_id',$id)->first();
        if (!$object) {
            abort(404, 'Role not found');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|input_string|max:255',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        $roles = Roles::where('name',$request->name)->where('added_by',auth()->user()->id)->where('id','!=',$object->id)->first();
        if(!empty($roles)){
            $response['status'] = false;
            $response['error_type'] = 'unique_name';
            $response['message'] = 'Name is already added.';
            return response()->json($response);
        }  

        Roles::where('id', $object->id)->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('roles');
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    /**
     * Remove the specified country from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSingle($id)
    {
        $record = Roles::where('unique_id',$id)->first();
        if (!$record) {
            abort(404, 'Role not found');
        }
      
        Roles::deleteRecord($record->id);
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
        for ($i = 0; $i < count($ids); $i++) {
            $act = Roles::where('unique_id',$ids[$i])->first();
            if ($act && $act->isEditableBy(auth()->id())) {
                Roles::deleteRecord($act->id);
            }
           
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }
}
