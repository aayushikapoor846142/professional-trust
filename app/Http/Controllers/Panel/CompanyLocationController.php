<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Country;
use App\Models\Category;
use App\Models\CompanyLocations;
use App\Models\CdsProfessionalCompany;

class CompanyLocationController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Helper to format validation errors.
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
     * Helper to save or update address.
     */
    private function saveOrUpdateAddress($data, $uniqueId = null, $extra = [])
    {
        $attributes = [
            'user_id' => auth()->id(),
            'address_1' => $data['address1'] ?? $data['address_1'] ?? 'NA',
            'address_2' => $data['address2'] ?? $data['address_2'] ?? 'NA',
            'country' => $data['country'] ?? 'NA',
            'state' => $data['state'] ?? 'NA',
            'city' => $data['city'] ?? 'NA',
            'pincode' => $data['pincode'] ?? 'NA',
            'type_label' => $data['type_label'] ?? '',
            'location_name' => $data['location_name'] ?? '',
            'type' => $data['type'] ?? '',
            'status' => $data['status'] ?? '',
        ];
        $attributes = array_merge($attributes, $extra);
        if ($uniqueId) {
            CompanyLocations::where('unique_id', $uniqueId)->update($attributes);
        } else {
            $attributes['unique_id'] = randomNumber();
            CompanyLocations::create($attributes);
        }
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Company Locations";
        return view('admin-panel.company-locations.lists', $viewData);
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
        $records = CompanyLocations::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
                $query->where('user_id',auth()->id());
            })
            ->orderBy('id', "desc")
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.company-locations.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new action.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $viewData['pageTitle'] = "Add Company Location";
        $viewData['countries'] = Country::all();
        return view('admin-panel.company-locations.add', $viewData);
    }

    /**
     * Store a newly created action in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_1' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        try {
            $this->saveOrUpdateAddress($request->all(), null, [
                'added_by' => auth()->id(),
            ]);
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('company-locations');
            $response['message'] = "Record added successfully";
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = 'An error occurred while saving the address.';
        }
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified action.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = CompanyLocations::where('unique_id', $id)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        }
        if ($record->added_by !== auth()->id()) {
   return handleUnauthorizedAccess('You are not authorized to edit this Page');
        }
        $viewData['record'] = $record;
        $viewData['countries'] = Country::all();
        $viewData['pageTitle'] = "Edit Company Location";
        return view('admin-panel.company-locations.edit', $viewData);
    }

    

    /**
     * Remove the specified country from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSingle($id)
    {
        try {
            $location = CompanyLocations::where('unique_id', $id)->firstOrFail();
            CompanyLocations::deleteRecord($location->id);
            return redirect()->back()->with("success", "Record deleted successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "Error deleting record.");
        }
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
        try {
            $locationIds = CompanyLocations::whereIn('unique_id', $ids)->pluck('id');
            foreach ($locationIds as $locationId) {
                CompanyLocations::deleteRecord($locationId);
            }
            $response['status'] = true;
            \Session::flash('success', 'Records deleted successfully');
        } catch (\Exception $e) {
            $response['status'] = false;
            \Session::flash('error', 'Error deleting records.');
        }
        return response()->json($response);
    }

    public function saveAddressFromSignup(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'address1' => ($request->input('type_label') === 'personal' || $request->input('type') === 'onsite') ? 'required' : '',
            'country' => ($request->input('type_label') === 'personal' || $request->input('type') === 'onsite') ? 'required' : '',
            'state' => ($request->input('type_label') === 'personal' || $request->input('type') === 'onsite') ? 'required' : '',
            'city' => ($request->input('type_label') === 'personal' || $request->input('type') === 'onsite') ? 'required' : '',
            'pincode' => ($request->input('type_label') === 'personal' || $request->input('type') === 'onsite') ? 'required' : '',
            'status' => $request->input('type_label') === 'company' ? 'required' : '',
            'type' => $request->input('type_label') === 'company' ? 'required' : '',
            'location_name' => $request->input('type_label') === 'company' ? 'required' : '',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        try {
            if($request->address_id != '' && $request->address_id != '0'){
                $this->saveOrUpdateAddress($request->all(), $request->address_id);
            } else {

                if($request->type_label == "personal"){
                    $personal_address = CompanyLocations::where('user_id', auth()->id())
                        ->where('type_label', 'personal')
                        ->count();

                    if($personal_address > 0)
                    {
                        $response['status'] = false;
                        $response['error_type'] = 'invalid_data';
                        $response['message'] = "Can not add more address";
                
                        return response()->json($response);
                    }
                }
                $address = CompanyLocations::where('user_id', auth()->id())
                    ->where('type_label', $request->type_label)
                    ->count();
                $is_primary = ($address == 0)?1:0;

                $cds_company = CdsProfessionalCompany::where("user_id", auth()->id())->first();

                $this->saveOrUpdateAddress($request->all(), null, [
                    'added_by' => auth()->id(),
                    'company_id' => $cds_company->id ?? 0,
                    'is_primary' => $is_primary,
                    'location_name' => $request->location_name ?? '',
                    'type' => $request->type ?? '',
                    'status' => $request->status ?? ''
                ]);
            }
          

            $response['status'] = true;
            $response['message'] = "Record added successfully";

        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = 'An error occurred while saving the address.';
        }
        return response()->json($response);
    }


    public function getAddressForSignup(Request $request)
    {
        $records = CompanyLocations::where(function ($query) {
                $query->where('user_id',auth()->id());
            })
            ->orderBy('id', "desc")
            ->where('type_label',$request->type)
            ->get();

        $viewData['records'] = $records;
        if($request->type == 'company'){
            $view = View::make('auth.company-address', $viewData);
        }else{
            $view = View::make('auth.personal-address', $viewData);
        }
        
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function deleteLocationData($location_id){
        CompanyLocations::where("user_id",auth()->id())->where("unique_id",$location_id)->delete();
        return redirect()->back()->with("success","Location deleted successfully");
    }
    
    public function markCompanyAsPrimary(Request $request){
        CdsProfessionalCompany::where("user_id",auth()->id())->update(['is_primary'=>0]);
        CdsProfessionalCompany::where("user_id",auth()->id())->where("unique_id",$request->company_id)->update(['is_primary'=>1]);

        CompanyLocations::where("user_id",auth()->id())->update(['is_primary'=>0]);
        CompanyLocations::where("user_id",auth()->id())->where("unique_id",$request->location_id)->update(['is_primary'=>1]);

        $response['status'] = true;
        $response['message'] = 'Company set as primary';
        return response()->json($response);
    }


    public function manageAddress($uid)
    {
        $viewData['pageTitle'] = "Manage Company Address";
        $viewData['showSidebar'] = true;
        $record = CdsProfessionalCompany::where("unique_id", $uid)->first();
        $viewData['company'] = $record;
        $viewData['template'] = 'companies.manage-company-address';
        return view('admin-panel.04-profile.profile.profile-master', $viewData);
    }

    public function getCompanyAddress(Request $request)
    {
        $records = CompanyLocations::where(function ($query) {
            $query->where('user_id',auth()->id());
        })
        ->orderBy('id', "desc")
        ->where('company_id',$request->company_id)
        ->where('type_label','company')
        ->get();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.04-profile.profile.companies.company-address', $viewData);
        
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }
    public function addCompanyAddress($id,$company_id,Request $request){
        $adddressInfo = array();
        if($id != 0){
            $adddressInfo = CompanyLocations::where(function ($query) {
                $query->where('user_id',auth()->id());
            })
            ->orderBy('id', "desc")
            ->where('unique_id',$id)
            ->first();
        }
        $company = CdsProfessionalCompany::where("unique_id",$company_id)->first();
        $viewData['adddressInfo'] = $adddressInfo;
        $viewData['company'] = $company;
        $viewData['pageTitle'] = "Company Address";
        $viewData['countries'] = Country::all();
        $viewData['id'] = $id;
        $view = view("admin-panel.04-profile.profile.companies.company-address-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function saveCompanyAddress($id,$company_id,Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'address1' =>'required',
            'country' =>'required',
            'state' =>'required',
            'city' =>'required',
            'pincode' =>'required',
            'status' =>'required',
            'type' => 'required',
            'location_name' => 'required',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        try {
            if($id != '' && $id != '0'){
                $this->saveOrUpdateAddress($request->all(), $id);
            } else {

                $address = CompanyLocations::where('user_id', auth()->id())
                    ->where('type_label', 'company')
                    ->count();
                $is_primary = ($address == 0)?1:0;

                $cds_company = CdsProfessionalCompany::where("unique_id",$company_id)->first();
                $this->saveOrUpdateAddress($request->all(), null, [
                    'added_by' => auth()->id(),
                    'type_label' => 'company',
                    'company_id' => $cds_company->id ?? 0,
                    'is_primary' => $is_primary,
                    'location_name' => $request->location_name ?? '',
                    'type' => $request->type ?? '',
                    'status' => $request->status ?? ''
                ]);
            }
          

            $response['status'] = true;
            $response['message'] = "Record added successfully";

        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = 'An error occurred while saving the address.';
        }
        return response()->json($response);
    }
}
