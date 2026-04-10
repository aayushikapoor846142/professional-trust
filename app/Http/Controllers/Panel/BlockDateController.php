<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyLocations;
use App\Models\ProfessionalLeave;
use Illuminate\Support\Facades\Validator;
use App\Models\WorkingHours;
use Carbon\Carbon;
use View;
use App\Services\BlockDateService;

class BlockDateController extends Controller
{
    protected $blockDateService;

    public function __construct(BlockDateService $blockDateService)
    {
        $this->blockDateService = $blockDateService;
    }

    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    
    
    
    public function index()
    {
     
        $viewData['pageTitle'] = "Block Dates";
        return view('admin-panel.03-appointments.appointment-system.block-dates.lists', $viewData);
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
        $date = $request->selected_date;
       
        $records = ProfessionalLeave::with(['location','professional'])->where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("unique_id", "LIKE", "%" . $search . "%");
                }
            })
          ->when($date, function ($query, $date) {
        $query->whereDate('leave_date', $date);
    })
           
            ->orderBy('id', "desc")
            ->paginate();
       // $this->cancelBookings();
        $viewData['records'] = $records;
        $view = View::make('admin-panel.03-appointments.appointment-system.block-dates.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    public function addLeaves()
    {
        $viewData['pageTitle'] = "Add Block Date";
        // return $locationIdsWithWorkingHours = WorkingHours::whereIn('location_id', function ($query) {
        //     $query->select('id')
        //         ->from('company_locations')
        //         // ->where('user_id', auth()->user()->id)
        //         ->where('type_label', 'company');
        // })->pluck('location_id')->toArray();

        $locationIds = CompanyLocations::where('type_label', 'company')->pluck('id');

        $locationIdsWithWorkingHours = WorkingHours::whereIn('location_id', $locationIds)->pluck('location_id')->toArray();

                
        $viewData['companyLocations'] = CompanyLocations::where('type_label', 'company')
            ->where('status','!=', 'inactive')
            ->whereHas('workingHours')
            // ->whereIn('id', $locationIdsWithWorkingHours)
            ->get();
        
        $viewData['leaves'] = ProfessionalLeave::pluck('leave_date')->toArray();

        return view('admin-panel.03-appointments.appointment-system.block-dates.add', $viewData);

    } 
    public function saveLeaves(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:company_locations,id',
            'leave_dates' => 'required',
            'reason' => 'required',

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
        
        $dates = explode(',', $request->leave_dates);
        $location_id=$request->location_id;
        $professional_id=auth()->user()->id;

        $this->blockDateService->createLeaves($location_id, $professional_id, $dates, $request->reason);

        $response['status'] = true;
        $response['message'] = 'Leaves marked Successfully';
        $response['redirect_back'] = baseUrl('appointments/appointment-booking/calendar');
        return response()->json($response);

    }
    public function edit($uid)
    {
        $viewData['record'] = ProfessionalLeave::with(['location', 'professional'])->where("unique_id", $uid)->first();
        $viewData['pageTitle'] = "Edit Block Date";
        // $locationIdsWithWorkingHours = WorkingHours::whereIn('location_id', function ($query) {
        //     $query->select('id')
        //         ->from('company_locations')
        //         ->where('user_id', auth()->user()->id)
        //         ->where('type_label', 'company');
        // })->pluck('location_id')->toArray();

        $locationIds = CompanyLocations::where('type_label', 'company')->pluck('id');

        $locationIdsWithWorkingHours = WorkingHours::whereIn('location_id', $locationIds)->pluck('location_id')->toArray();
        
        $viewData['companyLocations'] = CompanyLocations::where('type_label', 'company')
            ->where('status','!=', 'inactive')
            ->whereIn('id', $locationIdsWithWorkingHours)
            ->get();
        return view('admin-panel.03-appointments.appointment-system.block-dates.edit', $viewData);
    }
    
    public function update($uid,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:company_locations,id',
            'leave_date' => 'required',
            'reason' => 'required',
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

        $location_id = $request->location_id;
        $date = $request->leave_date;
        $professional_id = auth()->user()->id;

        $result = $this->blockDateService->updateLeave($uid, $location_id, $professional_id, $date, $request->reason);

        $response['status'] = $result['status'];
        $response['message'] = $result['message'];
        if ($result['status']) {
            $response['redirect_back'] = baseUrl('appointments/block-dates');
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        $deleted = $this->blockDateService->deleteLeave($id);
        if ($deleted) {
            return redirect()->back()->with("success", "Record deleted successfully");
        }
        return redirect()->back()->with("error", "Record not found");
    }

    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        $this->blockDateService->deleteMultipleLeaves($ids);
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    public function fetchLocationLeaves(Request $request){
        $location_id  = $request->input("location_id");
        $result = $this->blockDateService->getLocationLeavesData($location_id);
        return response()->json($result);
    }

}