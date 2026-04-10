<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppointmentTypes;
use App\Models\AppointmentBooking;
use App\Models\AppointmentReminder;

use App\Jobs\SendAppointmentCancellation;

use App\Models\CompanyLocations;
use App\Models\ProfessionalLeave;
use App\Models\User;
use App\Models\ProfessionalServices;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\CaseWithProfessionals;
use App\Models\WorkingHours;
use Carbon\Carbon;
use App\Models\StaffCases;
use View;
use Session;
use Carbon\CarbonInterface;
use Auth;
use DateTime;
use DateInterval;
use App\Models\WorkingHourBreak;
use App\Models\AppointmentBookingFlow;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AppointmentStatus;
use App\Services\AppointmentService;
use App\Services\FeatureCheckService;
use App\Models\ImmigrationServices;

class AppointmentBookingController extends Controller
{
    protected $appointmentService;
    protected $featureCheckService;

    public function __construct(AppointmentService $appointmentService, FeatureCheckService $featureCheckService)
    {
        $this->appointmentService = $appointmentService;
        $this->featureCheckService = $featureCheckService;
    }

    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    
    public function appointmentDashboard()
    {
        $viewData['pageTitle'] = "Appointment Dashboard";
        $professionalId = auth()->user()->getRelatedProfessionalId();
        $statusKeys = ['all','draft', 'approved', 'awaiting', 'cancelled', 'archieved', 'completed', 'non-conducted'];
        $appointmentsCount = [];
        foreach ($statusKeys as $key) {
            if ($key == 'all') {
                $appointmentsCount[$key] = \App\Models\AppointmentBooking::where('professional_id',$professionalId)
                                             ->count();
            } else {
                $appointmentsCount[$key] = \App\Models\AppointmentBooking::where('status', $key)            
                                        ->where('professional_id',$professionalId)
                                        ->count();
            }
        }
        $viewData['appointmentsCount'] = $appointmentsCount;

        // Fetch next 5 upcoming appointments (status: approved, awaiting, ordered by date/time)
        $viewData['upcomingAppointments'] = \App\Models\AppointmentBooking::with(['client','service'])
            ->where('professional_id', $professionalId)
            ->whereIn('status', ['approved', 'awaiting'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        return view("admin-panel.03-appointments.appointment-system.appointment-dashboard", $viewData);
    }

    public function appointmentSettings()
    {
        $viewData['pageTitle'] = "Appointment Settings";
        return view("admin-panel.03-appointments.appointment-system.appointment-settings",$viewData);
    }

    public function index()
    {
        
        $user = auth()->user();
        $appointmentBookingFeatureStatus = $this->featureCheckService->canAddAppointmentBooking($user->id);
        
        $viewData['appointmentBookingFeatureStatus'] = $appointmentBookingFeatureStatus;
        $viewData['canAddAppointmentBooking'] = $appointmentBookingFeatureStatus['allowed'];


        $viewData['pageTitle'] = "Appointment Booking";
        $statusKeys = ['all','draft', 'approved', 'awaiting', 'cancelled', 'archieved', 'completed', 'non-conducted'];
        $appointmentsCount = [];
        $professionalId= auth()->user()->getRelatedProfessionalId();

        foreach ($statusKeys as $key) {
            if ($key == 'all') {
                $appointmentsCount[$key] = AppointmentBooking::where('professional_id',$professionalId)
                                             ->count();
            } else {
                $appointmentsCount[$key] = AppointmentBooking::where('status', $key)            
                                        ->where('professional_id',$professionalId)
                                        ->count();
            }
        }
       $viewData['appointmentsCount'] = $appointmentsCount; // ← include counts

        $viewData['mainServices'] = ImmigrationServices::where('parent_service_id',0)->orderBy('id','desc')->get();
         $viewData['status'] = AppointmentBooking::distinct()->pluck('status');
        return view('admin-panel.03-appointments.appointment-system.appointment-booking.lists', $viewData);
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $status = $request->input("status");
        $search = $request->input("search");
        $service_id = $request->input("service_id");
        $sub_service_id = $request->input("sub_service_id");
        $filter_status= $request->input("filter_status");
        $start_date = $request->input("start_date");
        $end_date = $request->input("end_date");


        // Main query
        $professionalId= auth()->user()->getRelatedProfessionalId();

        $records = AppointmentBooking::with(['client','service','location'])
            ->where(function ($query) use ($search, $status,$professionalId) {
                if ($search != '') {
                    $query->where(function($subQuery) use ($search) {
                        $subQuery->where("unique_id", "LIKE", "%" . $search . "%")
                                ->orWhereHas('client', function($clientQuery) use ($search) {
                                    $clientQuery->where('first_name', 'LIKE', "%" . $search . "%")
                                               ->orWhere('last_name', 'LIKE', "%" . $search . "%");
                                });
                    });
                }
                if ($status != '' && $status != 'all') {
                    $query->where("status", $status);
                }
                $query->where("professional_id", $professionalId);
            })
           // ->visibleToUser(auth()->user()->id)
            ->orderBy('id', "desc");

            
        if($service_id != 0 && $service_id != 0){
            $service = ImmigrationServices::where('unique_id',$service_id)->first();
            $records->where('professional_service_id',$service->id);
        }

        if($sub_service_id != 0 && $sub_service_id != 0){
            $sub_service = ImmigrationServices::where('unique_id',$sub_service_id)->first();
            $records->where('sub_service_id',$sub_service->id);
        }

        if($filter_status != ''){
            $records->where('status',$filter_status);
        }

        if ($start_date && $end_date) {
            $records->whereDate('created_at', '>=', $start_date)
                  ->whereDate('created_at', '<=', $end_date);
        } elseif ($start_date && !$end_date) {
            $records->whereDate('created_at', '>=', $start_date);
        } elseif (!$start_date && $end_date) {
            $records->whereDate('created_at', '<=', $end_date);
        }

            $records = $records->paginate();

    

        $viewData['records'] = $records;
        $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking.ajax-list', $viewData);
        $contents = $view->render();

        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();

        return response()->json($response);
    }

     public function viewAppointment($unique_id=0)
    {
        return $this->appointmentService->viewAppointment($unique_id);
    }

    /**
     * Show the form for creating a new roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function cancelBookings()
     {
         return $this->appointmentService->cancelBookings();
     }


    public function addAppointment($unique_id=0)
    {
        return $this->appointmentService->addAppointment($unique_id);
    }

    /**
     * Store a newly created role in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAppointment(Request $request)
    {
        $result = $this->appointmentService->saveAppointment($request);
        return response()->json($result);
    }

    public function getAppointmentUniqueCheck($unique_id=0)
    {
            $appointmentBooking = AppointmentBooking::where('unique_id', $unique_id)
                                ->first();

            $conflictingAppointments = AppointmentBooking::where('professional_id', $appointmentBooking->professional_id)
                ->whereDate('appointment_date', $appointmentBooking->appointment_date)
                ->where('start_time', $appointmentBooking->start_time)
                ->where('end_time', $appointmentBooking->end_time)
                ->where('unique_id','!=', $appointmentBooking->unique_id)
                ->whereIn('status', ['approved','awaiting'])
                ->orderByDesc('awaiting_date') 
                ->get();
            $response['status']=false;
            if (count($conflictingAppointments)>0) {
               
                    $cancelCurrentBooking = AppointmentBooking::find($appointmentBooking->id);   
                    $cancelCurrentBooking->status = 'draft';
                    $cancelCurrentBooking->start_time = NULL;
                    $cancelCurrentBooking->end_time = NULL;
                    $cancelCurrentBooking->appointment_date = NULL;
                    $cancelCurrentBooking->completed_step = '2';
                    $cancelCurrentBooking->save();
        
                $response['status']=true;
                $response['redirect_back'] = baseUrl('appointments/appointment-booking/');
                $response['message'] = "This exact time slot has already been booked.";
                
            }
            
            return response()->json($response);
    }


    /**
     * Remove the specified country from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $appointmentBooking = AppointmentBooking::where('unique_id',$id)->first();
        AppointmentBooking::deleteRecord($appointmentBooking->id);
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
            $act = AppointmentBooking::where('unique_id',$ids[$i])->first();
            // $id = base64_decode($ids[$i]);
            AppointmentBooking::deleteRecord($act->id);
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }
    public function viewCalendar()
    {                       
        $professionalId= auth()->user()->getRelatedProfessionalId();
    
        $viewData['pageTitle'] = " Appointments Booking";
        $locationIdsWithWorkingHours = WorkingHours::whereIn('location_id', function ($query) use ($professionalId) {
            $query->select('id')
                ->from('company_locations')
                ->where('user_id', $professionalId)
                ->where('type_label', 'company');
        })->pluck('location_id')->toArray();
        
        $viewData['companyLocations'] = CompanyLocations::visibleToUser(auth()->user()->id)
            ->where('type_label', 'company')
            ->where('status','!=', 'inactive')
            ->whereIn('id', $locationIdsWithWorkingHours)
            ->get();
      
        return view('admin-panel.03-appointments.appointment-system.appointment-calendar-setting',$viewData);
        // return view('admin-panel.03-appointments.appointment-system.appointment-booking.calendar', $viewData);

    } 
   
public function fetchHours(Request $request) {
    $professional_unique_id = $request->input("professional_id");
    $booking_id = $request->booking_id;

    $user = User::where('unique_id', $professional_unique_id)->first();
    if (!$user) {
        return response()->json(['error' => 'Professional not found'], 404);
    }

    $professional_id = $user->id;
    $start_date = $request->input("start");
    $end_date = $request->input("end");

    $dates = getBetweenDates($start_date, $end_date);
    $day_schedules = [];
    $appointmentBooking = AppointmentBooking::where('unique_id', $booking_id)->first();
    $location_id = $appointmentBooking->location_id;

    foreach ($dates as $date) {
        $dayName = strtolower(date("l", strtotime($date)));

        // Get working hours for the day
        $workingHour = WorkingHours::visibleToUser($professional_id)
            ->where("day", $dayName)
            ->where("location_id", $location_id)
            ->first();

        // ✅ Only check leave if workingHour exists
      
        // Count booked appointments
        $bookedAppointments = AppointmentBooking::where("appointment_date", $date)
            ->where("professional_id", $professional_id)
            ->count();

        if ($workingHour) {
                $leaveExists = ProfessionalLeave::where('professional_id', $professional_id)
                ->where('leave_date', $date)
                ->where('location_id', $workingHour->location_id)
                ->exists();

            if (!$leaveExists) {
                $day_schedules[] = [
                'id' => $workingHour->id,
                'start' => $date,
                'title' => $bookedAppointments > 0 ? "$bookedAppointments Appointment(s) booked" : "Available",
                'className' => $bookedAppointments > 0 ? 'text-primary booked-appointment' : 'bg-success',
                'time_type' =>'available',
            ];
        }
            
        } else {
            // Mark as day off if no working hour
            $day_schedules[] = [
                'start' => $date,
                'title' => "Day Off",
                'className' => 'bg-danger disabled-class',
                'time_type' => 'day_off'
            ];
        }
    }

    return response()->json($day_schedules);
}

    
 
    public function fetchAvailableSlots(Request $request)
    {
        $result = $this->appointmentService->fetchAvailableSlots($request);
        return response()->json($result);
    }
    
   

    public function fetchAvailableSlotsworkingcodesinglebreaktime(Request $request)
    {
        $professional_unique_id = $request->professional_id;
        $unique_id = $request->booking_id;

        $professional = User::where('unique_id', $professional_unique_id)->first();
        if (!$professional) {
            return response()->json(['error' => 'Professional not found'], 404);
        }
    
        $professional_id = $professional->id;
      //  $professionalTz = $professional->timezone;
    
       
        $working_hours_id = $request->working_hours_id;
        $workingHour = WorkingHours::find($working_hours_id);
        if (!$workingHour) {
            return response()->json(['error' => 'No working hours found'], 404);
        }
        $currentBooking = AppointmentBooking::where("unique_id", $unique_id)->first();
        $date= $selectedDate = $currentBooking->appointment_date ?? null;
        $location_id=$currentBooking->location_id;
        $professionalTz=$getLocationTimezone = CompanyLocations::withTrashed()->where("id", $location_id)->value('timezone');

        if (!$workingHour->from_time || !$workingHour->to_time) {
            return response()->json(['error' => 'Working hours are missing from_time or to_time'], 400);
        }
    
        try {
            $startTime = !$date || $date !== date('Y-m-d')
                ? Carbon::createFromFormat('H:i', $workingHour->from_time, $professionalTz)->setTimezone('UTC')->timestamp
                : now()->addHours(1)->setTimezone('UTC')->timestamp;
    
            $endTime = Carbon::createFromFormat('H:i', $workingHour->to_time, $professionalTz)->setTimezone('UTC')->timestamp;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid time format in working hours'], 400);
        }
    
      
        $selectedStart24 = $currentBooking->start_time ?? null;
        $selectedEnd24 = $currentBooking->end_time ?? null;
    
        $appointmentType = AppointmentTypes::where('id', $currentBooking->appointment_type_id ?? null)
                            ->with('timeDuration')
                            ->first();
    
        $gapDuration = $appointmentType?->timeDuration?->break_time ?? 0;
        $appointmentDuration = $appointmentType?->timeDuration?->duration ?? 0;
        $slotDuration = $appointmentDuration * 60;
    
        $bookedAppointments = AppointmentBooking::where("appointment_date", $date)
            ->where("professional_id", $professional_id)
            ->where("unique_id", '!=', $unique_id)
            ->get();
    
        $occupiedSlots = [];
        foreach ($bookedAppointments as $appointment) {
            try {
                $occupiedSlots[] = [
                    'start' => Carbon::createFromFormat('H:i:s', $appointment->start_time, $professionalTz)->setTimezone('UTC')->timestamp,
                    'end' => Carbon::createFromFormat('H:i:s', $appointment->end_time, $professionalTz)->setTimezone('UTC')->timestamp,
                    'gap' => $gapDuration * 60,
                    'slot_gap' => $slotDuration,
                ];
            } catch (\Exception $e) {
                continue;
            }
        }
    
        $breakStart = null;
        $breakEnd = null;
        if (!$workingHour->no_break_time && $workingHour->break_starttime && $workingHour->break_endtime) {
            try {
                $breakStart = Carbon::parse($workingHour->break_starttime, $professionalTz)->setTimezone('UTC')->timestamp;
                $breakEnd = Carbon::parse($workingHour->break_endtime, $professionalTz)->setTimezone('UTC')->timestamp;
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid break time format'], 400);
            }
        }
    
        $availableSlots = [];
        $previousSlotBooked = false;
        $additionalGap = 0;
    
        $selectedStart24ClientTz = null;
        $selectedEnd24ClientTz = null;
    
        if ($selectedStart24) {
            try {
                $selectedStart24ClientTz = Carbon::createFromFormat('H:i:s', $selectedStart24, 'UTC')->setTimezone($professionalTz);
            } catch (\Exception $e) {}
        }
    
        if ($selectedEnd24) {
            try {
                $selectedEnd24ClientTz = Carbon::createFromFormat('H:i:s', $selectedEnd24, 'UTC')->setTimezone($professionalTz);
            } catch (\Exception $e) {}
        }
    
        while ($startTime + $slotDuration <= $endTime) {
            $overlapping = false;
    
            foreach ($occupiedSlots as $booked) {
                if (
                    ($startTime >= $booked['start'] && $startTime < $booked['end']) ||
                    ($startTime + $slotDuration > $booked['start'] && $startTime + $slotDuration <= $booked['end']) ||
                    ($startTime <= $booked['start'] && $startTime + $slotDuration >= $booked['end'])
                ) {
                    $overlapping = true;
                    $additionalGap = $booked['gap'];
                    break;
                }
            }
    
            $inBreakTime = ($breakStart && $breakEnd) &&
                (($startTime >= $breakStart && $startTime < $breakEnd) ||
                    ($startTime + $slotDuration > $breakStart && $startTime + $slotDuration <= $breakEnd));
    
            if (!$overlapping && !$inBreakTime) {
                if ($previousSlotBooked && $additionalGap > 0) {
                    $startTime += $additionalGap;
                    $additionalGap = 0;
                    $previousSlotBooked = false;
                    continue;
                }
    
                $startTimeClientTz = Carbon::createFromTimestamp($startTime, 'UTC')->setTimezone($professionalTz);
                $endTimeClientTz = Carbon::createFromTimestamp($startTime + $slotDuration, 'UTC')->setTimezone($professionalTz);
    
                $availableSlots[] = [
                    'start_time_12' => $startTimeClientTz->format('h:i A'),
                    'end_time_12' => $endTimeClientTz->format('h:i A'),
                    'start_time_24' => $startTimeClientTz->format('H:i:s'),
                    'end_time_24' => $endTimeClientTz->format('H:i:s'),
                    'selected_start_time' => optional($selectedStart24ClientTz)->format('H:i:s'),
                    'selected_end_time' => optional($selectedEnd24ClientTz)->format('H:i:s'),
                ];
            } else {
                $previousSlotBooked = true;
            }
    
            $startTime += $slotDuration;
        }
    
        return response()->json(['available_slots' => $availableSlots]);
    }
    
    public function fetchAvailableSlots_withouttimezone(Request $request) {
        $professional_unique_id = $request->professional_id;
        $unique_id = $request->booking_id;
        $user = User::where('unique_id', $professional_unique_id)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Professional not found'], 404);
        }
        if(getUserTimeZone()){
            date_default_timezone_set(getUserTimeZone()); // Change this to your desired timezone
        }   else{
            date_default_timezone_set('Asia/Kolkata');

        }
        $professional_id = $user->id;
        $date = $request->date;
        $working_hours_id = $request->working_hours_id;
    
        // Fetch working hours
        $workingHour = WorkingHours::where("id", $working_hours_id)->first();
        if (!$workingHour) {
            return response()->json(['error' => 'No working hours found'], 404);
        }
        
        // Get booked appointments
        $currentBoookingAppointment = AppointmentBooking::where("unique_id", $unique_id)
                                    ->first();
        $selectedDate = $currentBoookingAppointment->appointment_date ?? null;
        $isToday = ($selectedDate === date('Y-m-d'));
        $selectedStart24 = $currentBoookingAppointment->start_time ?? null;
        $selectedEnd24 = $currentBoookingAppointment->end_time ?? null;
        $appointmentType = AppointmentTypes::where('id', $currentBoookingAppointment->appointment_type_id)
                            ->with('timeDuration')
                            ->first();
                        
        $gapDuration = $appointmentType?->timeDuration?->break_time ?? 0; // Default to 0 if null
        $appointmentDuration = $appointmentType?->timeDuration?->duration ?? 0; // Default to 0 if null
        $slotDuration = $appointmentDuration * 60; 
        $bookedAppointments = AppointmentBooking::where("appointment_date", $date)
            ->where("professional_id", $professional_id)
            ->where("unique_id",'!=', $unique_id)
            ->select('start_time', 'end_time', 'appointment_type_id')
            ->get();
    
        // Convert booked appointments into occupied time slots
        $occupiedSlots = [];
        foreach ($bookedAppointments as $appointment) {
               
            $occupiedSlots[] = [
                'start' => strtotime($appointment->start_time),
                'end' => strtotime($appointment->end_time),
                'gap' => $gapDuration * 60, // Convert minutes to seconds
                'slot_gap' => $slotDuration, 
            ];
        }
       
        // Get break time details
        $breakStart = $workingHour->no_break_time ? null : strtotime($workingHour->break_starttime);
        $breakEnd = $workingHour->no_break_time ? null : strtotime($workingHour->break_endtime);
    
        // Generate available slots based on working hours
        if(!$isToday){
            $startTime = strtotime($workingHour->from_time);
            $endTime = strtotime($workingHour->to_time);
        }else{
            $startTime = strtotime('+3 hours');
            $endTime = strtotime($workingHour->to_time);
           
         }
        $availableSlots = [];
        $previousSlotBooked = false;
        $additionalGap = 0;
    
        while ($startTime + $slotDuration <= $endTime) {
            $slotStart12 = date("h:i A", $startTime); // 12-hour format
            $slotEnd12 = date("h:i A", $startTime + $slotDuration);
            $slotStart24 = date("H:i:s", $startTime); // 24-hour format for DB
            $slotEnd24 = date("H:i:s", $startTime + $slotDuration);
    
            // Check if the slot falls within any booked appointment
            $overlapping = false;
            foreach ($occupiedSlots as $booked) {
                $slotDuration = $booked['slot_gap'];
                if (
                    ($startTime >= $booked['start'] && $startTime < $booked['end']) ||  
                    ($startTime + $slotDuration > $booked['start'] && $startTime + $slotDuration <= $booked['end']) ||
                    ($startTime <= $booked['start'] && $startTime + $slotDuration >= $booked['end'])
                ) {
                    $overlapping = true;
                    $additionalGap = $booked['gap'];
                    break;
                }
            }
    
            // Check if the slot falls within break time
            $inBreakTime = ($breakStart && $breakEnd) &&
                (($startTime >= $breakStart && $startTime < $breakEnd) ||
                    ($startTime + $slotDuration > $breakStart && $startTime + $slotDuration <= $breakEnd));
    
            // If previous slot was booked, insert a gap before the next available slot
            if (!$overlapping && !$inBreakTime) {
                if ($previousSlotBooked && $additionalGap > 0) {
                    $startTime += $additionalGap;
                    $additionalGap = 0;
                    $previousSlotBooked = false;
                    continue;
                }
                $availableSlots[] = [
                    'start_time_12' => $slotStart12, // User-friendly format
                    'end_time_12' => $slotEnd12,
                    'start_time_24' => $slotStart24, // DB format
                    'end_time_24' => $slotEnd24,
                    'selected_start_time' => $selectedStart24, // DB format
                    'selected_end_time' => $selectedEnd24,

                ];
            } else {
                $previousSlotBooked = true;
            }
    
            // Move to next slot
            $startTime += $slotDuration;
        }
    
        return response()->json(['available_slots' => $availableSlots]);
    }
    
    
    public function fetchAppointmentssss(Request $request)
    {
        $professional_id = auth()->user()->id;
        $start_date = $request->input("start");
        $end_date = $request->input("end");
        // Get all dates within the requested range
        $dates = getBetweenDates($start_date, $end_date);
        $day_schedules = [];
        $today = date("Y-m-d"); // Get today's date
    
        foreach ($dates as $date) {
            $dayName = strtolower(date("l", strtotime($date)));
    
            // Get working hours for this day
            $workingHour = WorkingHours::visibleToUser(auth()->user()->id)
                ->where("day", $dayName)
                ->first();
    
            // Check if appointments are booked on this date
            $bookedAppointments = AppointmentBooking::where("appointment_date", $date)
                ->where("professional_id", $professional_id)
                ->count();
    
            if ($date < $today) {
                // Past dates: Show booked appointments if available, otherwise "Past Date"
                $day_schedules[] = [
                    'start' => $date,
                    'title' => $bookedAppointments > 0 ? "$bookedAppointments Appointment(s) booked" : "Past Date",
                    'className' => $bookedAppointments > 0 ? 'text-secondary past-appointment' : 'bg-secondary',
                    'time_type' => $bookedAppointments > 0 ? 'past_booked' : 'past_date',
                ];
            } elseif ($workingHour) {
                // Today & Future Dates: Show availability or booked status
                $day_schedules[] = [
                    'id' => $workingHour->id,
                    'start' => $date,
                    'title' => $bookedAppointments > 0 ? "$bookedAppointments Appointment(s) booked" : "Available",
                    'className' => $bookedAppointments > 0 ? 'text-primary booked-appointment' : 'bg-success',
                    'time_type' => $bookedAppointments > 0 ? 'booked' : 'available',
                ];
            } else {
                // If no working hours exist, mark as a day off
                $day_schedules[] = [
                    'start' => $date,
                    'title' => "Day Off",
                    'className' => 'bg-danger',
                    'time_type' => 'day_off'
                ];
            }
        }
    
        return response()->json($day_schedules);
    }
  
    public function fetchAppointments(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');
        $selectedDay = (int) $request->get('selectedDay'); // Ensure selectedDay is an integer
        // $professionalId = $request->input('formData.professional_id');
        // $location_id = $request->input('formData.location_id');
        
          $professionalId = $request->get('professional_id');
        $location_id = $request->get('location_id');
        // Start and end of the month
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Fetch appointments for the selected month and professional
        $eventsQuery = AppointmentBooking::with('location')
        ->where('professional_id', $professionalId)
        ->whereBetween('appointment_date', [$start, $end]);      
          
        if($location_id > 1) {
            $eventsQuery->where('location_id', $location_id);
        }
      
        $bookings = $eventsQuery->get();
    
        if($selectedDay == 0){
            if($eventsQuery->count() > 0){
                $selectedDay = Carbon::parse($bookings[0]->appointment_date)->day;
            }
        }
        $events = [];
        $todayEvents = [];

        // Process each booking
        $bookings->each(function ($booking) use ($selectedDay, &$events, &$todayEvents) {
            $date = Carbon::parse($booking->appointment_date);

            $event = [
                'day' => $date->day,
                'month' => $date->month, // Store month (1-12) for JS grouping
                'time' => Carbon::parse($booking->start_time)->format('h:i A') . ' - ' . Carbon::parse($booking->end_time)->format('h:i A'),
                'title' => "Slot Booked for - " . $booking->client->first_name . ' ' . $booking->client->last_name,
                'description' => 'Location: '.optional($booking->location)->full_address.'  Appointment Status: ' . $booking->status,
            ];

            // Group events by month (0-indexed for JS)
            $monthIndex = $date->month - 1;
            if (!isset($events[$monthIndex])) {
                $events[$monthIndex] = [];
            }
            $events[$monthIndex][] = $event;
            
            // Filter events for the selected day
            if ($date->day == $selectedDay) {
                $todayEvents[] = $event;
            }
        });
     
        // Render HTML for today's events
        $html = view('admin-panel.03-appointments.appointment-system.appointment-booking.partials.events', [
            'todayEvents' => $todayEvents
        ])->render();

        // Return JSON response with events and today's events HTML
        return response()->json([
            'html' => $html,
            'events' => $events
        ]);
    }

    public function saveUserTimezone(Request $request)
    {
        $user = Auth::user(); // get currently logged in user
    
        if (!$user->timezone && $request->has('timezone')) {
            $user->timezone = $request->timezone;
            $user->save();
        }
    
        return response()->json(['status' => true, 'timezone' => $user->timezone]);
    }
    
    public function addJoiningLink($uid)
    {
        $user_id=auth()->user()->id;
        $appointment = AppointmentBooking::where('unique_id',$uid)->first();
        $viewData['appointment'] = $appointment;

        $pageTitle = "Add Joining Link";
        $viewData['appointmentBookingId'] = $uid;

        $viewData['pageTitle'] = $pageTitle;
        $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking.modals.add-joining-link',$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }
    
    
    public function saveJoiningLink(Request $request,$uid)
    {
        //   dd($request->id);
        $appointmentBooking = AppointmentBooking::with(['client','professional'])->where('unique_id',$uid)->first();
        $appointmentBooking->appointment_mode_details = $request->appointment_mode_details;
        $appointmentBooking->save();
        $mailData['fetchLoctimezone']= $fetchLoctimezone= CompanyLocations::withTrashed()->where('id',$appointmentBooking->location_id)->first();
        $locationTimezone=$fetchLoctimezone->timezone;
        $mailData['clientTz']=$clientTz=$appointmentBooking->client->timezone;
        $mailData['profTz']=$profTz=$locationTimezone;

        $mailData['startInClientTz'] = $startInClientTz = Carbon::createFromFormat('H:i:s', $appointmentBooking->start_time, 'UTC')->setTimezone($clientTz);
        $mailData['endInClientTz'] =  $endInClientTz = Carbon::createFromFormat('H:i:s', $appointmentBooking->end_time, 'UTC')->setTimezone($clientTz);

        $mailData['appointment'] = $appointmentBooking;
        $mailData['professional_name'] = $appointmentBooking->professional->first_name . " " . $appointmentBooking->professional->last_name;
        $mailData['client_name'] = $appointmentBooking->client->first_name . " " . $appointmentBooking->client->last_name;
        $mail_message = \View::make('emails.appointment-joining-link', $mailData);
        $mailData['mail_message'] = $mail_message;
        
        $parameter['to'] =$appointmentBooking->client->email;
        $parameter['to_name'] = $appointmentBooking->client->first_name . " " . $appointmentBooking->client->last_name;
        $parameter['message'] = $mail_message;
        $parameter['subject'] = "Appointment Details";
        $parameter['view'] = "emails.appointment-joining-link";
        $parameter['data'] = $mailData;
        try {
            $dataprof = sendMail($parameter);
        } catch (\Exception $e) {
            \Log::error('Mail sending failed: ' . $e->getMessage());
        }
        
      //  dd( $appointmentBooking->id);
        return redirect()->back()->with("success", "Joining Link updated successfully");
    }
    public function markStatus($uid,$status)
    {
        if (!in_array($status, ['completed', 'non-conducted'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }
    
        $appointment = AppointmentBooking::where('unique_id',$uid)->first();
        $appointment->status = $status;
        $appointment->save();
       
        return redirect()->back()->with("success", "Appointment status updated successfully");
    }

    public function appointmentBookingSuccess($appointment_booking_id)
    {
        $viewData['appointment_booking_id'] = $appointment_booking_id;
        return view('admin-panel.03-appointments.appointment-system.appointment-booking.appointment-booking-success', $viewData);
    }

    public function searchAppointments(Request $request){
        $status = $request->input("status");
        $search = $request->input("search");
        $professionalId= auth()->user()->getRelatedProfessionalId();

        $records = AppointmentBooking::with(['client','professional','service','location'])
                ->where(function ($query) use ($search, $status,$professionalId) {
                    if ($search != '') {
                        $query->where("unique_id", "LIKE", "%" . $search . "%");
                    }
                    if ($status != '' && $status != 'all') {
                        $query->where("status", $status);
                    }
                    $query->orWhere(function($q) use($search){
                        $q->whereHas("client",function($q2) use($search){
                            $q2->where(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%");
                        });
                    });
                    $query->groupBy("client_id");
                    $query->where("professional_id", $professionalId);
                })
                ->orderBy('id', "desc")
                ->get();
        $response['records'] = $records;
        $response['status'] = true;

        return response()->json($response);
    }

     public function saveReminder(Request $request)
    {
        $userId = auth()->user()->id; // get currently logged in user
    
          $validator = Validator::make($request->all(), [
                'reminder_date' => 'required',
                'reminder_message' =>'required',
                'reminder_time' => 'required',

        ]);
        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['error_type'] = 'validation';
            $response['message'] = $errMsg;
            return response()->json($response);
        }
            $time = date('H:i:s', strtotime($request->reminder_time));
            $reminder = new AppointmentReminder();
            $reminder->unique_id = randomNumber(); 
            $reminder->appointment_id = $request->appointment_id;
            $reminder->user_id = $userId;
            $reminder->reminder_date = $request->reminder_date;
            $reminder->reminder_time = $time;
            $reminder->message = $request->reminder_message;
            $reminder->is_sent = false;
            $reminder->save();

            return response()->json([
                'status' => true,
                'message' => 'Reminder set successfully.',
            ]);    
    }

    public function setReminderModal($appointmentId)
    {
        $userIid=auth()->user()->id;
        $pageTitle = "Set Reminder";
        $viewData['pageTitle'] = $pageTitle;

        $viewData['getAppointment']=$getAppointment=AppointmentBooking::where('unique_id',$appointmentId)->first();
        $viewData['appointmentId']=$getAppointment->id;

        $view = View::make('admin-panel.03-appointments.appointment-system.appointment-booking.modals.set-reminder',$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

     public function cancelAwaitingAppointment($bookingId)
    {
        $appointment = AppointmentBooking::where('unique_id', $bookingId)
            ->where('status','!=' ,'cancelled')
            ->first();

        if (!$appointment) {
            return redirect()->back()->with('error', 'Appointment cannot be cancelled or already processed.');
        }

         if ($appointment->appointment_date == now()->format('Y-m-d')) {
            return redirect()->back()->with('error', 'You cannot cancel an appointment scheduled for today.');
        }

        $appointment->status = 'cancelled';
        $appointment->cancelled_reason = 'Cancelled by professional Manually';
        $appointment->cancel_date = date('Y-m-d');
        $appointment->cancelled_by = auth()->user()->id;;
        $appointment->save();
                $appStatus=new AppointmentStatus;
                $appStatus->status='cancelled';
                $appStatus->appointment_id=$appointment->id;
                $appStatus->status_date=date('Y-m-d');
                $appStatus->unique_id=randomNumber();
                $appStatus->user_id=auth()->user()->id;
                $appStatus->save();
        SendAppointmentCancellation::dispatchSync($appointment, 'client');
        SendAppointmentCancellation::dispatchSync($appointment, 'professional');

        return redirect()->back()->with('success', 'Appointment has been cancelled successfully.');
    }

}
