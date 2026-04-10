<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppointmentTypes;
use App\Models\AppointmentBookingFlow;
use App\Models\CompanyLocations;

use App\Models\User;
use App\Models\ProfessionalServices;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\CaseWithProfessionals;
use App\Models\WorkingHours;
use Carbon\Carbon;
use View;
use Carbon\CarbonInterface;
use Auth;
use DateTime;
use DateInterval;

class AppointmentBookingFlowController extends Controller
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
            $viewData['pageTitle'] = "Appointment Booking Flow";
            return view('admin-panel.03-appointments.appointment-system.appointment-booking-flow.pages.lists', $viewData);
        }

        public function getAjaxList(Request $request)
        {
            $professionalId = auth()->user()->getRelatedProfessionalId();
            $search = $request->input("search");
            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

            $records = $this->getPaginatedAppointmentBookingFlows($search, $professionalId,$sortColumn,$sortDirection);
            $viewData['records'] = $records;
            $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking-flow.pages.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        }

        private function getPaginatedAppointmentBookingFlows($search, $professionalId,$sortColumn,$sortDirection)
        {
            return AppointmentBookingFlow::with(['timeDuration','service','appointmentType','location','workingHours'])
                ->where(function ($query) use ($search, $professionalId) {
                    if ($search != '') {
                        $query->where("unique_id", "LIKE", "%" . $search . "%");
                    }
                    $query->where("professional_id", $professionalId);
                })
                ->orderBy($sortColumn,$sortDirection)
                ->visibleToUser(auth()->user()->id)
                ->paginate();
        }

        public function add($unique_id = 0)
        {
            $ids = $this->getUserAndProfessionalIds();
            $viewData['professional_id'] = $ids['professionalUId'];

            $appointmentBookingFlow = "";
            if ($unique_id != 0) {
                $appointmentBookingFlow = $this->getAppointmentBookingFlow($unique_id);
          
            }

            $viewData['locationIdsWithWorkingHours'] = WorkingHours::visibleToUser($ids['userId'])
                ->whereIn('location_id', function ($query) {
                    $query->select('id')
                        ->from('company_locations')
                        ->where('type_label', 'company');
                })->pluck('location_id')
                ->where('professional_id', $ids['professionalId'])
                ->toArray();

            $viewData['companyLocations'] = $this->getCompanyLocations($ids['userId'], $appointmentBookingFlow);
            $viewData['appointmentTypes'] = $this->getAppointmentTypes($ids['professionalId'], $ids['userId']);
            $viewData['groupedServices'] = $this->getGroupedProfessionalServices($ids['professionalId']);
            $viewData['appointmentBookingFlow'] = $appointmentBookingFlow;
            $viewData['pageTitle'] = "Add Appointment Booking Flow";
            $viewData['durations'] = checkTimeDuration();
            $viewData['appointmentTypes'] = checkAppointmentTypes();
            return view('admin-panel.03-appointments.appointment-system.appointment-booking-flow.pages.add', $viewData);
        }


        public function addTimeDuration($unique_id = 0)
        {
            $appointmentBookingFlow = $this->getAppointmentBookingFlow($unique_id);
            if (!$appointmentBookingFlow) {
                return response()->json(['status' => false, 'message' => 'Appointment Booking Flow not found']);
            }
            $viewData['appointmentBookingFlow'] = $appointmentBookingFlow;
            $viewData['durations'] = checkTimeDuration();
            
            $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.time-duration.add', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
            
        }

        public function addAppointmentType($unique_id = 0)
        {
            $appointmentBookingFlow = $this->getAppointmentBookingFlow($unique_id);
            if (!$appointmentBookingFlow) {
                return response()->json(['status' => false, 'message' => 'Appointment Booking Flow not found']);
            }
            $viewData['appointmentTypes'] = checkAppointmentTypes();
            $viewData['appointmentBookingFlow'] = $appointmentBookingFlow;
            $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.appointment-type.add', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
            
        }

        public function addLocation($unique_id = 0)
        {
            $ids = $this->getUserAndProfessionalIds();
            $viewData['professional_id'] = $ids['professionalUId'];

            $appointmentBookingFlow = $this->getAppointmentBookingFlow($unique_id);
            if (!$appointmentBookingFlow) {
                return response()->json(['status' => false, 'message' => 'Appointment Booking Flow not found']);
            }
            $viewData['appointmentBookingFlow'] = $appointmentBookingFlow;
            $viewData['companyLocations'] = $this->getCompanyLocations($ids['userId'], $appointmentBookingFlow);
            
            $viewData['locationIdsWithWorkingHours']= $locationIdsWithWorkingHours = WorkingHours::visibleToUser(auth()->user()->id)->  
                whereIn('location_id', function ($query) {
                    $query->select('id')
                        ->from('company_locations')
                        ->where('type_label', 'company');
                })->pluck('location_id')    
                ->where('professional_id',$ids['professionalId'])   
                ->toArray();
            

            $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.location.add', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
            
        }

        public function addService($unique_id = 0)
        {
            $ids = $this->getUserAndProfessionalIds();
            $viewData['professional_id'] = $ids['professionalUId'];

            $appointmentBookingFlow = $this->getAppointmentBookingFlow($unique_id);
            if (!$appointmentBookingFlow) {
                return response()->json(['status' => false, 'message' => 'Appointment Booking Flow not found']);
            }
            $viewData['appointmentBookingFlow'] = $appointmentBookingFlow;
           

            $professionalServices = ProfessionalServices::with(['subServices', 'parentService'])
                                ->where('user_id',$ids['professionalId'])
                                ->get();
                    
            $groupedServices = $professionalServices->groupBy(function ($item) {
                                return $item->parentService->name ?? 'Unknown Service';
                            })->map(function ($group) {
                                return $group->map(function ($service) {
                                    return [
                                        'id' => $service->id,
                                        'name' => $service->subServices->name ?? 'Unknown Sub-Service',
                                    ];
                                })->values();
            
                            });
            $viewData['groupedServices'] = $groupedServices;
            $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.service.add', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
            
        }

        public function save(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'description' => 'required',
                'appointment_mode' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($this->formatValidationErrors($validator));
            }

            $user = auth()->user();
            $professionalId = $user->getRelatedProfessionalId();

            $appointment_booking_flow = $this->getOrCreateAppointmentBookingFlow($request, $professionalId, $user->id);

            $appointment_booking_flow->fill([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'professional_id' => $professionalId,
                'appointment_mode' => $request->appointment_mode,
                'added_by' => $user->id,
            ])->save();

            return response()->json([
                'status' => true,
                'redirect_back' => baseUrl('appointments/appointment-booking-flow/add/' . $appointment_booking_flow->unique_id),
                'message' => "Record added successfully"
            ]);
        }

        private function getOrCreateAppointmentBookingFlow($request, $professionalId, $userId)
        {
            $appointment_booking_flow = $request->booking_flow_id
                ? $this->getAppointmentBookingFlow($request->booking_flow_id)
                : new AppointmentBookingFlow(['unique_id' => randomNumber(), 'status' => 'draft']);

            if ($request->booking_flow_id && $request->appointment_mode != $appointment_booking_flow->appointment_mode) {
                $appointment_booking_flow->location_id = "";
                $appointment_booking_flow->status = 'draft';
            }

            if ($request->submit_type == 'save_and_complete') {
                $appointment_booking_flow->status = 'completed';
            }

            return $appointment_booking_flow;
        }

        public function saveTimeDuration(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'time_duration_id' => 'required',
            ],  [
                'time_duration_id.required' => 'Please select a time duration.',
            ]);
    
            if ($validator->fails()) {
                return response()->json($this->formatValidationErrors($validator));
            }

            $appointment_booking_flow = AppointmentBookingFlow::where('unique_id',$request->duration_booking_flow_id)->first();
            $appointment_booking_flow->time_duration_id = $request->time_duration_id;
            $appointment_booking_flow->save();

            $response['status'] = true;
            $response['message'] = "Record added successfully";
    
            return response()->json($response);
        }

        public function saveAppointmentType(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'appointment_type_id' => 'required',
            ],  [
                'appointment_type_id.required' => 'Please select a appointment type.',
            ]);
    
            if ($validator->fails()) {
                return response()->json($this->formatValidationErrors($validator));
            }

            $appointment_booking_flow = AppointmentBookingFlow::where('unique_id',$request->duration_booking_flow_id)->first();
            $appointment_booking_flow->appointment_type_id = $request->appointment_type_id;
            $appointment_booking_flow->save();

            $response['status'] = true;
            $response['message'] = "Record added successfully";
    
            return response()->json($response);
        }


        
    
        public function showWorkingHoursModal($location_uid)
        {
            $userId = auth()->user()->id;
            $getLocId = CompanyLocations::where('unique_id', $location_uid)->value('id');
            $viewData['getLocId'] = $getLocId;
            $viewData['timezones'] = $this->getTimezoneList();
            $viewData['getSelectedTimezone'] = CompanyLocations::where('id', $getLocId)->value('timezone');

            $schedules = WorkingHours::visibleToUser($userId)->where('location_id', $getLocId)->get();
            $viewData['records'] = $this->formatSchedules($schedules);

            $viewData['professional_id'] = $userId;
            $viewData['companyLocations'] = $this->getCompanyLocationsForWorkingHoursModal($userId);
            $viewData['pageTitle'] = "Add Working Hours";
            $viewData['load_type'] = 'modal';

            $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking-flow.modals.working-hours-modal', $viewData);
            $contents = $view->render();
            return response()->json(['contents' => $contents, 'status' => true]);
        }

        private function getTimezoneList()
        {
            return array_map(function($tz) {
                return ["label" => $tz, "value" => $tz];
            }, \DateTimeZone::listIdentifiers());
        }

        private function formatSchedules($schedules)
        {
            $records = [];
            foreach ($schedules as $schedule) {
                $breaks = $schedule->breaks->map(function ($break) {
                    return [
                        'start' => $break->break_start,
                        'end' => $break->break_end,
                    ];
                })->toArray();

                $records[$schedule->day] = [
                    'from' => $schedule->from_time,
                    'to' => $schedule->to_time,
                    'no_break_time' => $schedule->no_break_time,
                    'breaks' => $breaks,
                ];
            }
            return $records;
        }

        private function getCompanyLocationsForWorkingHoursModal($userId)
        {
            return CompanyLocations::visibleToUser($userId)
                ->where('status', '!=', 'inactive')
                ->where('type_label', 'company')
                ->get();
        }

        public function saveServices(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'service' => 'required',
            ],  [
                'service.required' => 'Please select a services.',
            ]);
    
            if ($validator->fails()) {
                return response()->json($this->formatValidationErrors($validator));
            }

            $appointment_booking_flow = AppointmentBookingFlow::where('unique_id',$request->duration_booking_flow_id)->first();
            $appointment_booking_flow->service_id = implode(',',$request->service);
            $appointment_booking_flow->save();

            $response['status'] = true;
            $response['message'] = "Record added successfully";
    
            return response()->json($response);
        }

        public function saveLocations(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'location_id' => 'required',
            ],  [
                'location_id.required' => 'Please select a location.',
            ]);
    
            if ($validator->fails()) {
                return response()->json($this->formatValidationErrors($validator));
            }

            $appointment_booking_flow = AppointmentBookingFlow::where('unique_id',$request->duration_booking_flow_id)->first();
            $appointment_booking_flow->location_id = $request->location_id;
            $appointment_booking_flow->save();

            $response['status'] = true;
            $response['message'] = "Record added successfully";
    
            return response()->json($response);
        }

    private function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return [
            'status' => false,
            'error_type' => 'validation',
            'message' => $errMsg,
        ];
    }

    private function getAppointmentBookingFlow($unique_id)
    {
        return AppointmentBookingFlow::where('unique_id', $unique_id)->first();
    }

    private function getCompanyLocations($userId, $appointmentBookingFlow = null)
    {
        return CompanyLocations::visibleToUser($userId)
            ->where('type_label', 'company')
            ->when(isset($appointmentBookingFlow->appointment_mode), function ($query) use ($appointmentBookingFlow) {
                $query->where("type", $appointmentBookingFlow->appointment_mode);
            })
            ->whereHas("workingHours")
            ->where('status', '!=', 'inactive')
            ->get();
    }

    private function getUserAndProfessionalIds()
    {
        $user = auth()->user();
        return [
            'user' => $user,
            'userId' => $user->id,
            'professionalId' => $user->getRelatedProfessionalId(),
            'professionalUId' => $user->getRelatedProfessionalUniqueId(),
        ];
    }

    private function getGroupedProfessionalServices($professionalId)
    {
        $professionalServices = ProfessionalServices::with(['subServices', 'parentService'])
            ->where('user_id', $professionalId)
            ->get();

        return $professionalServices->groupBy(function ($item) {
            return $item->parentService->name ?? 'Unknown Service';
        })->map(function ($group) {
            return $group->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->subServices->name ?? 'Unknown Sub-Service',
                ];
            })->values();
        });
    }

    private function getAppointmentTypes($professionalId, $userId)
    {
        return AppointmentTypes::with('timeDuration')
            ->where('professional_id', $professionalId)
            ->visibleToUser($userId)
            ->get();
    }
}
// 