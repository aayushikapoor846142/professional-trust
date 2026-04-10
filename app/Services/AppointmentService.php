<?php

namespace App\Services;

use App\Models\AppointmentBooking;
use App\Models\AppointmentTypes;
use App\Models\AppointmentReminder;
use App\Models\CompanyLocations;
use App\Models\ProfessionalLeave;
use App\Models\User;
use App\Models\ProfessionalServices;
use App\Models\CaseWithProfessionals;
use App\Models\WorkingHours;
use App\Models\StaffCases;
use App\Models\WorkingHourBreak;
use App\Models\AppointmentBookingFlow;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AppointmentStatus;
use App\Services\FeatureCheckService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class AppointmentService
{
    public function appointmentDashboard()
    {
        // Logic for appointment dashboard
    }

    public function appointmentSettings()
    {
        // Logic for appointment settings
    }

    public function index()
    {
        // Logic for listing appointments
    }

    public function getAjaxList($request)
    {
        // Logic for AJAX list
    }

    public function viewAppointment($unique_id = 0)
    {
        try {
            $userId = auth()->user()->id;
            $viewData['professional_id'] = auth()->user()->unique_id;
            $appointment_booking = AppointmentBooking::with('client','reminder','professional','service')
                ->where('unique_id',$unique_id)->first();
            $user = User::where('id', $appointment_booking->user_id)->first();
            $prof = User::where('id', $appointment_booking->professional_id)->first();
            $viewData['fetchLoctimezone'] = $fetchLoctimezone = CompanyLocations::withTrashed()->where('id',$appointment_booking->location_id)->first();
            $locationTimezone = $fetchLoctimezone->timezone;
            $viewData['clientTz'] = $clientTz = $appointment_booking->client->timezone;
            $viewData['profTz'] = $profTz = $locationTimezone;
            $viewData['profileTz'] = $profileTz = $appointment_booking->professional->timezone;
            $getAppointmentStatus = AppointmentStatus::where('appointment_id',$appointment_booking->id)->get();
            $viewData['getAppointmentStatus'] = $getAppointmentStatus;
            $viewData['startInClientTz'] = $startInClientTz = Carbon::createFromFormat('H:i:s', $appointment_booking->start_time, 'UTC')->setTimezone($clientTz);
            $viewData['endInClientTz'] =  $endInClientTz = Carbon::createFromFormat('H:i:s', $appointment_booking->end_time, 'UTC')->setTimezone($clientTz);
            $viewData['startInProfTz'] = $startInProfTz = Carbon::createFromFormat('H:i:s', $appointment_booking->start_time, 'UTC')->setTimezone($profTz);
            $viewData['endInProfTz'] =  $endInProfTz = Carbon::createFromFormat('H:i:s', $appointment_booking->end_time, 'UTC')->setTimezone($profTz);
            $viewData['appointment_data'] = $appointment_booking;
            $viewData['user'] = $user;
            $viewData['pageTitle'] = "View Appointment";
            return view('admin-panel.03-appointments.appointment-system.appointment-booking.view', $viewData);
        } catch (\Exception $e) {
            Log::error('Error in viewAppointment: ' . $e->getMessage(), ['exception' => $e]);
            return redirect(baseUrl('appointments/appointment-booking'))->with("error", "An error occurred while viewing the appointment.");
        }
    }

    public function cancelBookings()
    {
        try {
            $appointments = AppointmentBooking::with(['client','professional'])->where('appointment_date', '>=', date('Y-m-d'))->get();
            foreach ($appointments as $currentBookingAppointment) {
                $paymentStatus = $currentBookingAppointment->payment_status ?? null;
                $currentTime = new DateTime();
                $appointmentDate = $currentBookingAppointment->appointment_date;
                $appointmentStartTime = $currentBookingAppointment->start_time;
                $timeParts = explode(':', $appointmentStartTime);
                if (count($timeParts) === 2) {
                    $appointmentStartTime .= ':00';
                }
                $dateTimeString = $appointmentDate . ' ' . $appointmentStartTime;
                $appointmentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
                if ($appointmentDateTime) {
                    $oneHourBefore = clone $appointmentDateTime;
                    $oneHourBefore->sub(new DateInterval('PT1H'));
                    $isToday = ($appointmentDate === $currentTime->format('Y-m-d'));
                    $lessThanOneHourLeft = $paymentStatus !== 'paid' &&
                        $isToday &&
                        $currentTime >= $oneHourBefore &&
                        $currentTime < $appointmentDateTime &&
                        $currentBookingAppointment->status !== 'cancelled';
                    if ($lessThanOneHourLeft) {
                        $deadline="1 hour";
                        $cancelBooking = AppointmentBooking::find($currentBookingAppointment->id);
                        $cancelBooking->status = 'cancelled';
                        $cancelBooking->cancelled_by = auth()->user()->id;
                        $cancelBooking->cancel_date = date('Y-m-d');
                        $cancelBooking->cancelled_reason = 'Payment not done within ' . $deadline;
                        $cancelBooking->save();
                        $mailData['appointment'] = $currentBookingAppointment;
                        $mailData['deadline'] = $deadline;
                        $mailData['professional_name'] = $currentBookingAppointment->professional->first_name . " " . $currentBookingAppointment->professional->last_name;
                        $mailData['client_name'] = $currentBookingAppointment->client->first_name . " " . $currentBookingAppointment->client->last_name;
                        $mail_message = \View::make('emails.appointment_booking_cancel', $mailData);
                        $mailData['mail_message'] = $mail_message;
                        $parameter['to'] = $currentBookingAppointment->client->email;
                        $parameter['to_name'] = $mailData['client_name'];
                        $parameter['message'] = $mail_message;
                        $parameter['subject'] = "Appointment Booking";
                        $parameter['view'] = "emails.appointment_booking_cancel";
                        $parameter['data'] = $mailData;
                        $data = sendMail($parameter);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Error in cancelBookings: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public function addAppointment($unique_id = 0)
    {
        try {
            $userId = auth()->user()->id;
            
            $viewData['professional_id'] = auth()->user()->getRelatedProfessionalUniqueId();
            $viewData['appointmentBookingFlow'] = AppointmentBookingFlow::with(['timeDuration','service','appointmentType','location','workingHours'])
                ->where('status', 'completed')
                
                ->visibleToUser(auth()->user()->id)
                ->get();
            $professionalId = auth()->user()->getRelatedProfessionalId();
            $query = CaseWithProfessionals::with('clients')
                ->select('client_id')
                ->distinct();
            if (auth()->user()->role == 'professional') {
                $query->where('professional_id', auth()->user()->id);
            } else {
                $case_ids = StaffCases::where('staff_id', auth()->user()->id)->pluck('case_id')->toArray();
                $query->whereIn('id', $case_ids);
            }
            $cases = $query->get();
            $clients = $cases->pluck('clients')
                ->flatten()
                ->unique('id')
                ->map(function ($client) {
                    return [
                        'full_name' => $client->first_name . ' ' . $client->last_name,
                        'id' => $client->id
                    ];
                })
                ->values();
            $locationIdsWithWorkingHours = WorkingHours::whereIn('location_id', function ($query) {
                $query->select('id')
                    ->from('company_locations')
                    ->where('type_label', 'company');
            })->visibleToUser(auth()->user()->id)
            ->pluck('location_id')->toArray();
            $viewData['companyLocations'] = CompanyLocations::where('type_label', 'company')
                ->visibleToUser(auth()->user()->id)
                ->where('status','!=', 'inactive')
                ->whereIn('id', $locationIdsWithWorkingHours)
                ->get();
            $viewData['clients'] = $clients;
            $step = AppointmentBooking::where('unique_id',$unique_id)->first();
            $days = array();
            $start_end_time = '';
            if($step){
                if($step->location_id != ''){
                    $workingHours = WorkingHours::where("professional_id", $professionalId)
                    ->where("location_id", $step->location_id)
                    ->pluck('day');
                    if(!empty($workingHours)){
                        $days = $workingHours->toArray();
                    }
                }
                if($step->start_time != '' && $step->end_time != ''){
                    $start_end_time = $step->start_time."-".$step->end_time;
                }
                $LeaveDays = ProfessionalLeave::where('professional_id', $professionalId)
                        ->where('location_id', $step->location_id)
                        ->pluck('leave_date')
                        ->map(function ($date) {
                            return \Carbon\Carbon::parse($date)->format('Y-m-d');
                        })
                        ->toArray();
                $viewData['LeaveDays'] = $LeaveDays;
                $viewData['days'] = $days;
                $viewData['start_end_time'] = $start_end_time;
                $viewData['fetchLoctimezone'] = $fetchLoctimezone = CompanyLocations::withTrashed()->where('id',$step->location_id)->first();
                $locationTimezone = $fetchLoctimezone->timezone;
                $viewData['profTz'] = $profTz = $locationTimezone;
                $bookingFlowServiceIds = [];
                if(Session::get('predefined_booking_flow')){
                    $appointmentFlowService = AppointmentBookingFlow::where('id', Session::get('predefined_booking_flow'))->value('service_id');
                    $bookingFlowServiceIds = explode(",",$appointmentFlowService );
                }
                $clientTz = $step->client->timezone;
                if($step->completed_step>=4){
                    $viewData['startInClientTz'] = $startInClientTz = Carbon::createFromFormat('H:i:s', $step->start_time, 'UTC')->setTimezone($clientTz);
                    $viewData['endInClientTz'] =  $endInClientTz = Carbon::createFromFormat('H:i:s', $step->end_time, 'UTC')->setTimezone($clientTz);
                    $viewData['startInProfTz'] = $startInProfTz = Carbon::createFromFormat('H:i:s', $step->start_time, 'UTC')->setTimezone($profTz);
                    $viewData['endInProfTz'] =  $endInProfTz = Carbon::createFromFormat('H:i:s', $step->end_time, 'UTC')->setTimezone($profTz);
                }
                $viewData['appointment_data'] = $step;
                $viewData['appointment_date'] = $step->appointment_date;
                $viewData['working_hours_id'] = $step->working_hours_id;
                $viewData['booking_id'] = $viewData['appointment_booking_id'] = $step->unique_id;
                $viewData['completed_step'] = $step->completed_step;
                $viewData['amount_to_pay'] = $step->price;
                $viewData['clientTz'] = $clientTz;
                $viewData['fetchLoctimezone'] = $fetchLoctimezone = CompanyLocations::withTrashed()->where('id',$step->location_id)->first();
                $locationTimezone = $fetchLoctimezone->timezone;
                $viewData['profTz'] = $profTz = $locationTimezone;
            }else{
                $viewData['LeaveDays'] = [];
                $viewData['days'] = array();
                $viewData['start_end_time'] = $start_end_time;
                $viewData['appointment_data'] = NULL;
                $viewData['booking_id'] = 0;
                $bookingFlowServiceIds = [];
                $viewData['working_hours_id'] = 0;
                $viewData['appointment_date'] = "";
                $viewData['completed_step'] = 0;
            }
            $appointmentTypes = AppointmentTypes::with('timeDuration')
                ->where('professional_id',$professionalId)
                ->visibleToUser(auth()->user()->id)
                ->get();
            $professionalServices = ProfessionalServices::with(['subServices', 'parentService'])
                ->visibleToUser(auth()->user()->id)
                ->when(!empty($bookingFlowServiceIds), function ($query) use ($bookingFlowServiceIds) {
                    return $query->whereIn('id', $bookingFlowServiceIds);
                })
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
            $viewData['appointmentTypes'] = $appointmentTypes;
            $viewData['groupedServices'] = $groupedServices;
            $viewData['pageTitle'] = "Book Appointment";
            if(count($appointmentTypes)>0 && count($professionalServices)>0 && count($professionalServices)>0 && count($locationIdsWithWorkingHours)>0){
                return view('admin-panel.03-appointments.appointment-system.appointment-booking.add', $viewData);
            }else{
                return redirect(baseUrl('appointments/appointment-booking'))->with("error", "Please add Time Duration, Appointment Types, Services and Working hours  First");
            }
        } catch (\Exception $e) {
            Log::error('Error in addAppointment: ' . $e->getMessage(), ['exception' => $e]);
            return redirect(baseUrl('appointments/appointment-booking'))->with("error", "An error occurred while loading the appointment form.");
        }
    }

    public function saveAppointment($request)
    {
        try {
            $unique_id= $request->booking_id;
           
            $response['redirect_back'] = baseUrl('appointments/appointment-booking/save-booking/'.$unique_id);

            if($request->type == 'appointment_for'){
                $validator = Validator::make($request->all(), [
                    'appointment_for' => 'required',
                    'appointment_mode' =>$request->input('booking_type') === 'booking_flow' ? '' : 'required',
                    'location_id' => $request->input('booking_type') === 'booking_flow' ? '' : 'required',
                    'booking_type' => 'required',
                    'predefined_booking_flow' =>  $request->input('booking_type') === 'booking_flow' ? 'required' : '',
                ]);
                $user = User::where('unique_id',$request->professional_id)->first();

                if ($validator->fails()) {
                    $response['status'] = false;
                    $error = $validator->errors()->toArray();
                    $errMsg = array();

                    foreach ($error as $key => $err) {
                        $errMsg[$key] = $err[0];
                    }
                    $response['error_type'] = 'validation';
                    $response['message'] = $errMsg;
                    return $response;
                }
                $appointmentBooking = AppointmentBooking::where('unique_id', $request->booking_id)->first();
                if( $appointmentBooking){
                    $object= AppointmentBooking::find($appointmentBooking->id);
                }else{
                    $object=new AppointmentBooking();
                }
                $object->unique_id = randomNumber();
                $object->user_id = $request->input('appointment_for');
                $object->professional_id =$user->id;
                $object->booking_type =$request->booking_type;
                if($request->booking_type=="booking_flow" && $request->predefined_booking_flow>0){
                    Session::put('predefined_booking_flow',$request->predefined_booking_flow);
                    $bookingFlow = AppointmentBookingFlow::findOrFail($request->predefined_booking_flow);
                    $object->appointment_type_id = $bookingFlow->appointment_type_id;
                    $object->appointment_mode =$bookingFlow->appointment_mode;
                    $object->location_id =$bookingFlow->location_id;     
                }else{
                    $object->appointment_mode =$request->appointment_mode;
                    $object->location_id =$request->location_id;
                }
                if($request->booking_type=="general"){
                    \Session::forget('predefined_booking_flow');
                }
                $object->added_by =auth()->user()->id;
                $object->created_by ="professional";
                if( $object->completed_step<=1){
                    $object->completed_step = 1;
                    $object->status = 'pending';
                    $object->payment_status = 'pending';
                }
                $object->save();
                $unique_id= $object->unique_id;
                $response['redirect_back'] = baseUrl('appointments/appointment-booking/save-booking/'.$unique_id);
            }elseif($request->type == 'services'){
                $validator = Validator::make($request->all(), [
                    'service' => 'required',
                    'appointment_type' => 'required',
                    'additional_info' =>'nullable', 
                ], [
                    'appointment_type.required' => 'Please select an appointment type.',
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
                    return $response;
                }
                $fetchServiceId = ProfessionalServices::where('id',$request->service)->first();
                $appointmentBooking = AppointmentBooking::where('unique_id',$request->booking_id)->first();        
                $fetchAppointmentType=AppointmentTypes::with('timeDuration')->where('id',$request->appointment_type)->first();
                if(!empty($appointmentBooking)){
                    if($fetchAppointmentType->timeDuration->type == 'Minutes'){
                        $interval = $fetchAppointmentType->timeDuration->duration;
                    }else{
                        $interval = $fetchAppointmentType->timeDuration->duration * 60;
                    }
                    $object= AppointmentBooking::find($appointmentBooking->id);
                    $object->price = $fetchAppointmentType->price;
                    $object->currency = $fetchAppointmentType->currency;
                    $appointmentBooking->appointments_gap = optional($fetchAppointmentType->timeDuration)->break_time;
                    if($object->booking_type=="general"){
                        $object->appointment_type_id = $request->appointment_type;
                    }          
                    $object->sub_service_id = $fetchServiceId->service_id;
                    $object->professional_service_id = $request->service;
                    $object->meeting_duration = $interval;
                    if( $object->completed_step<=2){
                        $object->status = 'draft';
                        $object->completed_step = 2;
                    }
                    $object->save();
                } 
            }
            elseif($request->type == 'appointment-date'){
                $validator = Validator::make($request->all(), [
                    'selected_date' => 'required',
                    'time_slot' => 'required',
                    'booking_id' => 'required',
                ]);
                if ($validator->fails()) {
                    $response['status'] = false;
                    $response['message'] = "Please Select an Appointment Date and Timeslot";
                    return $response;
                }
                
                $appointmentBooking = AppointmentBooking::where('unique_id',$request->booking_id)->first();
                if(empty($appointmentBooking)){
                    return [
                        'status' => false,
                        'message' => 'Appointment booking not found. Please start the booking process again.',
                        'error_type' => 'validation'
                    ];
                }
                
                $object= AppointmentBooking::find($appointmentBooking->id);
                if( $object->completed_step<=3){
                    $object->completed_step = 3;
                }
                $object->working_hours_id = $request->working_hours_id;
                $object->appointment_date = $request->selected_date;
                $object->time_type = $request->time_type;
                                $object->save();
                
                // Process time slot
                $duration = explode("-",$request->input("time_slot"));
                if (count($duration) !== 2) {
                    return [
                        'status' => false,
                        'message' => 'Invalid time slot format. Please select a valid time slot.',
                        'error_type' => 'validation'
                    ];
                }
                
                $appointmentBooking = AppointmentBooking::where('unique_id',$request->booking_id)->first();
                if(!empty($appointmentBooking)){
                    $object= AppointmentBooking::find($appointmentBooking->id);
                    $fetchLoctimezone= CompanyLocations::withTrashed()->where('id',$appointmentBooking->location_id)->first();
                    
                    if (!$fetchLoctimezone) {
                        return [
                            'status' => false,
                            'message' => 'Location timezone not found. Please contact support.',
                            'error_type' => 'validation'
                        ];
                    }
                    
                    $locationTimezone=$fetchLoctimezone->timezone;
                    $profTz=$locationTimezone;
                    
                    try {
                        $utcStartTime = Carbon::createFromFormat('H:i:s',  $duration[0], $profTz)
                                    ->setTimezone('UTC')
                                    ->format('H:i:s');
                        $utcEndTime = Carbon::createFromFormat('H:i:s',  $duration[1], $profTz)
                                    ->setTimezone('UTC')
                                    ->format('H:i:s');
                        $object->start_time = $utcStartTime;
                        $object->end_time = $utcEndTime;
                        $object->save();
                    } catch (\Exception $e) {
                        Log::error('Time conversion error: ' . $e->getMessage(), [
                            'duration' => $duration,
                            'timezone' => $profTz
                        ]);
                        return [
                            'status' => false,
                            'message' => 'Error processing time slot. Please try again.',
                            'error_type' => 'system'
                        ];
                    }
                } 
            }
            elseif($request->type == 'additional_information'){
                $validator = Validator::make($request->all(), [
                    'additional_info' =>'nullable', 
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
                    return $response;
                }
                $fetchServiceId = ProfessionalServices::where('id',$request->service)->first();
                $appointmentBooking = AppointmentBooking::where('unique_id',$request->booking_id)->first();        
                $fetchAppointmentType=AppointmentTypes::with('timeDuration')->where('id',$request->appointment_type)->first();
                if(!empty($appointmentBooking)){
                    $object= AppointmentBooking::find($appointmentBooking->id);
                    $object->additional_info = $request->additional_info; 
                    if( $object->completed_step <=4){
                        $object->completed_step = 4;
                    }
                    $object->save();
                } 
            }
            elseif($request->type == 'appointment-preview'){
                $appointmentBooking = AppointmentBooking::with(['client','professional'])->where('unique_id',$request->booking_id)->first();
                if(!empty($appointmentBooking)){
                    if($appointmentBooking->completed_step=="5"){
                        $appointmentBooking->update_count = $appointmentBooking->update_count + 1;
                    }
                    $appointmentBooking->completed_step = 5;
                    $alreadyBookedCount = AppointmentBooking::where('professional_id', $appointmentBooking->professional_id)
                        ->whereDate('appointment_date', $appointmentBooking->appointment_date)
                        ->where('start_time', $appointmentBooking->start_time)
                        ->where('end_time', $appointmentBooking->end_time)
                        ->where('unique_id','!=', $appointmentBooking->unique_id)
                        ->whereIn('status', ['approved','awaiting'])
                        ->count();
                    if ($alreadyBookedCount>0) {
                        $bookingData = AppointmentBooking::find($appointmentBooking->id);
                        $bookingData->status = 'draft';
                        $bookingData->start_time = NULL;
                        $bookingData->end_time = NULL;
                        $bookingData->appointment_date = NULL;
                        $bookingData->completed_step = 2;
                        $bookingData->save();
                        $response['message'] = "This exact time slot has already been booked.";
                        $response['status'] = "specific";
                        return $response;
                    }
                    if($appointmentBooking->price==0 || $request->mark_as_free == 1){
                        $appointmentBooking->status = 'approved';
                        $appointmentBooking->price = 0;
                        $appointmentBooking->payment_status = 'paid';
                        $appointmentBooking->paid_by = auth()->user()->id;
                        $appointmentBooking->mark_as_free = 1;
                        $appointmentBooking->free_by = auth()->user()->id;
                        $appointmentBooking->completed_step = 6;

                        // save feature
                            $featureCheckService = new \App\Services\FeatureCheckService();
                            $featureCheckService->savePlanFeature(
                                'appointments',
                                auth()->user()->id,
                                1, // action type: add
                                1, // count: 1 appointment
                                [
                                    'appointment_id' => $appointmentBooking->id,
                                    'appointment_unique_id' => $appointmentBooking->unique_id,
                                    'appointment_date' => $appointmentBooking->appointment_date,
                                    'appointment_status' => 'approved',
                                    'payment_status' => 'paid',
                                    'is_free_appointment' => false,
                                    'professional_id' => $appointmentBooking->professional_id,
                                    'client_id' => $appointmentBooking->user_id,
                                    'payment_method' => 'stripe',
                                ]
                            );


                        // end feature
                        $response['redirect_back'] = baseUrl('appointments/appointment-booking-success/'.$request->booking_id);
                        $user = User::where('id',auth()->user()->id)->first();
                        $location = CompanyLocations::withTrashed()->where('id',$appointmentBooking->location_id)->first();
                        $invoice = new Invoice();
                        $invoice->user_id = $user->id;
                        $invoice->added_by = auth()->user()->id;
                        $invoice->first_name = $user->first_name;
                        $invoice->last_name = $user->last_name;
                        $invoice->email = $user->email;
                        $invoice->country_code = $user->country_code;
                        $invoice->phone_no = $user->phone_no;
                        $invoice->address = $location->address_1.' '.$location->address_2;
                        $invoice->city = $location->city;
                        $invoice->state = $location->state;
                        $invoice->zip = $location->pincode;
                        $invoice->country = $location->country;
                        $invoice->discount = 0;
                        $invoice->b_address = $location->address_1.' '.$location->address_2;
                        $invoice->b_city =  $location->city;
                        $invoice->b_state = $location->state;
                        $invoice->b_zip = $location->pincode;
                        $invoice->b_country = $location->country;
                        $invoice->reference_id = $appointmentBooking->id ?? 0;
                        $invoice->currency = 'CAD';
                        $invoice->tax = 0;
                        $invoice->sub_total = 0;
                        $invoice->total_amount = 0;
                        $invoice->payment_status = 'paid' ;
                        $invoice->paid_date = date('Y-m-d');
                        $invoice->transaction_id = '';
                        $invoice->invoice_type = 'appointment-booking';
                        $invoice->save();
                        $invoice_id = $invoice->id;
                        $invoice_item = new InvoiceItem();
                        $invoice_item->invoice_id = $invoice_id;
                        $invoice_item->discount = 0;
                        $invoice_item->particular = "Amount paid for Appointment Booking <b>TrustVisory</b> initiative";
                        $invoice_item->amount = 0;
                        $invoice_item->save();
                        $invoice_items = InvoiceItem::where("invoice_id",$invoice->id)->get();
                        $pdfData = ['invoice_number' => $invoice->invoice_number,"invoice_items"=>$invoice_items,"invoice"=>$invoice];
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', $pdfData);
                        $invoice_folder = storage_path("app/public/invoices"); 
                        if (!is_dir($invoice_folder)) {
                            mkdir($invoice_folder, 0777, true);
                        }
                        $filePath = storage_path('app/public/invoices/invoice_' . $invoice->invoice_number . '.pdf');
                        file_put_contents($filePath, $pdf->output());
                        $fetchLoctimezone = CompanyLocations::withTrashed()->where('id', $appointmentBooking->location_id)->first();
                        $locationTimezone = $fetchLoctimezone->timezone;
                        $mailData['fetchLoctimezone'] = $fetchLoctimezone;
                        $mailData['clientTz']=$clientTz=$appointmentBooking->client->timezone;
                        $mailData['profTz']= $profTz=$locationTimezone;
                        $mailData['startInClientTz'] = $startInClientTz = Carbon::createFromFormat('H:i:s', $appointmentBooking->start_time, 'UTC')->setTimezone($clientTz);
                        $mailData['endInClientTz'] =  $endInClientTz = Carbon::createFromFormat('H:i:s', $appointmentBooking->end_time, 'UTC')->setTimezone($clientTz);
                        $mailData['startInProfTz'] = $startInProfTz = Carbon::createFromFormat('H:i:s', $appointmentBooking->start_time, 'UTC')->setTimezone($profTz);
                        $mailData['endInProfTz'] =  $endInProfTz = Carbon::createFromFormat('H:i:s', $appointmentBooking->end_time, 'UTC')->setTimezone($profTz);
                        $mailData['appointment'] = $appointmentBooking;
                        $mailData['professional_name'] = $appointmentBooking->professional->first_name . " " . $appointmentBooking->professional->last_name;
                        $mailData['client_name'] = $appointmentBooking->client->first_name . " " . $appointmentBooking->client->last_name;
                        $mail_message = \View::make('emails.appointment_booking', $mailData);
                        $mailData['mail_message'] = $mail_message;
                        $parameter['to'] =$appointmentBooking->client->email;
                        $parameter['to_name'] = $appointmentBooking->client->first_name . " " . $appointmentBooking->client->last_name;
                        $parameter['message'] = $mail_message;
                        $parameter['subject'] = "Appointment Booking";
                        $parameter['view'] = "emails.appointment_booking";
                        $parameter['data'] = $mailData;
                        $parameter['invoice_pdf'] = $filePath;
                        $dataprof=sendMail($parameter);
                        $arr = [
                            'comment' =>'*'. auth()->user()->first_name . " " . auth()->user()->last_name . '* has booked an appointment with you. ',
                            'type' => 'appointment_booking',
                            'redirect_link' => 'appointments/appointment-booking/',
                            'is_read' => 0,
                            'user_id' =>$appointmentBooking->client->id ?? '',
                            'send_by' => auth()->user()->id ?? '',
                        ];
                        chatNotification($arr);
                    }else{
                        $appointmentBooking->status = 'awaiting';
                        if (is_null($appointmentBooking->awaiting_date)) {
                            $appointmentBooking->awaiting_date = Carbon::now(); 
                        }
                    }
                    $appointmentBooking->save();
                } 
            }
            $response['status'] = true;
            $response['message'] = "Record added successfully";
            return $response;
        } catch (\Exception $e) {
            Log::error('Error in saveAppointment: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => false, 
                'message' => 'An error occurred while saving the appointment. Error: ' . $e->getMessage(), 
                'error_type' => 'system'
            ];
        }
    }

    public function getAppointmentUniqueCheck($unique_id = 0)
    {
        // Logic for checking unique appointment
    }

    public function delete($id)
    {
        // Logic for deleting an appointment
    }

    public function deleteMultiple($ids)
    {
        // Logic for deleting multiple appointments
    }

    public function viewCalendar()
    {
        // Logic for viewing calendar
    }

    public function fetchHours($request)
    {
        // Logic for fetching working hours
    }

    public function fetchAvailableSlots($request)
    {
        try {
            $professional_unique_id = $request->professional_id;
            $unique_id = $request->booking_id;
            $location_id = $request->location_id;
            $date = $request->date;
            $dayName = strtolower(date('l', strtotime($date)));
            $professional = User::where('unique_id', $professional_unique_id)->first();
            if (!$professional) {
                return ['error' => 'Professional not found', 'status' => 404];
            }
            $currentBooking = AppointmentBooking::where("unique_id", $unique_id)->first();
            $selectedDate = $currentBooking->appointment_date ?? null;
            $location_id=$currentBooking->location_id;
            $professionalTz=$getLocationTimezone = CompanyLocations::withTrashed()->where("id", $location_id)->value('timezone');
            $professional_id= auth()->user()->getRelatedProfessionalId();
            $leaveTimes = [];
            $leaveRecords = ProfessionalLeave::visibleToUser(auth()->user()->id)
                ->where('location_id', $location_id)
                ->where('leave_date', $date)
                ->get();
            foreach ($leaveRecords as $leave) {
                try {
                    $leaveStart = Carbon::createFromFormat('H:i', $leave->start_time, $professionalTz)->setTimezone('UTC')->timestamp;
                    $leaveEnd = Carbon::createFromFormat('H:i', $leave->end_time, $professionalTz)->setTimezone('UTC')->timestamp;
                    $leaveTimes[] = [
                        'start' => $leaveStart,
                        'end' => $leaveEnd,
                    ];
                } catch (\Exception $e) {
                    continue;
                }
            }
            $workingHour = WorkingHours::visibleToUser(auth()->user()->id)->where('location_id',$location_id)->where('day',$dayName)->first();
            if (!$workingHour) {
                return ['status'=>true,'message' => 'No working hours found'];
            }
            if (!$workingHour->from_time || !$workingHour->to_time) {
                return ['error' => 'Working hours are missing from_time or to_time', 'status' => 400];
            }
            try {
                $startTime = Carbon::createFromFormat('H:i', $workingHour->from_time, $professionalTz)
                    ->setTimezone('UTC');
                $endTime = Carbon::createFromFormat('H:i', $workingHour->to_time, $professionalTz)
                    ->setTimezone('UTC');
                if ($date == date('Y-m-d')) {
                    $nowPlus3 = now($professionalTz)->addHours(1)->setTimezone('UTC');
                    if ($nowPlus3->greaterThan($startTime) && $nowPlus3->lessThan($endTime)) {
                        $startTime = $nowPlus3;
                    }
                }
                $startTime = $startTime->timestamp;
                $endTime = $endTime->timestamp;
            } catch (\Exception $e) {
                return ['status'=>true,'message' => 'Invalid time format in working hours'];
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
                ->whereIn("status", ['awaiting','approved','draft'])
                ->get();
            $occupiedSlots = [];
            foreach ($bookedAppointments as $appointment) {
                try {
                    $startDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $appointment->appointment_date . ' ' . $appointment->start_time,
                        'UTC'
                    );
                    $endDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $appointment->appointment_date . ' ' . $appointment->end_time,
                        'UTC'
                    );
                    $occupiedSlots[] = [
                        'startwithouttimestamp' => $startDateTime->setTimezone($professionalTz)->format('H:i:s'),
                        'endwithouttimestamp'   => $endDateTime->setTimezone($professionalTz)->format('H:i:s'),
                        'start'                 => $startDateTime->setTimezone($professionalTz)->timestamp,
                        'end'                   => $endDateTime->setTimezone($professionalTz)->timestamp,
                        'gap'                   => $gapDuration * 60,
                        'slot_gap'              => $slotDuration,
                    ];
                } catch (\Exception $e) {
                    continue;
                }
            }
            $timeline = [];
            $breaks = [];
            if (!$workingHour->no_break_time) {
                $breakRecords = WorkingHourBreak::where('working_hours_id', $workingHour->id)->get();
                foreach ($breakRecords as $brk) {
                    try {
                        $breaks[] = [
                            'start' => Carbon::createFromFormat('H:i:s', $brk->break_start, $professionalTz)->setTimezone('UTC')->timestamp,
                            'end' => Carbon::createFromFormat('H:i:s', $brk->break_end, $professionalTz)->setTimezone('UTC')->timestamp,
                        ];
                    } catch (\Exception $e) {
                        continue;
                    }
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
                        break;
                    }
                }
                $inBreakTime = false;
                foreach ($breaks as $brk) {
                    if (
                        ($startTime >= $brk['start'] && $startTime <= $brk['end']) ||
                        ($startTime + $slotDuration > $brk['start'] && $startTime + $slotDuration <= $brk['end']) ||
                        ($startTime <= $brk['start'] && $startTime + $slotDuration >= $brk['end'])
                    ) {
                        $inBreakTime = true;
                        break;
                    }
                }
                $inLeaveTime = false;
                foreach ($leaveTimes as $leave) {
                    if (
                        ($startTime >= $leave['start'] && $startTime < $leave['end']) ||
                        ($startTime + $slotDuration > $leave['start'] && $startTime + $slotDuration <= $leave['end']) ||
                        ($startTime <= $leave['start'] && $startTime + $slotDuration >= $leave['end'])
                    ) {
                        $inLeaveTime = true;
                        break;
                    }
                }
                if (!$inBreakTime && !$inLeaveTime) {
                    $startTimeClientTz = Carbon::createFromTimestamp($startTime, 'UTC')->setTimezone($professionalTz);
                    $endTimeClientTz = Carbon::createFromTimestamp($startTime + $slotDuration, 'UTC')->setTimezone($professionalTz);
                    $start24 = $startTimeClientTz->format('H:i:s');
                    $end24 = $endTimeClientTz->format('H:i:s');
                    $isBooked = collect($occupiedSlots)->contains(function ($slot) use ($start24, $end24) {
                        return $slot['startwithouttimestamp'] === $start24 && $slot['endwithouttimestamp'] === $end24;
                    });
                    if (!$isBooked) {
                        $availableSlots[] = [
                            'start_time_12' => $startTimeClientTz->format('h:i A'),
                            'end_time_12' => $endTimeClientTz->format('h:i A'),
                            'start_time_24' => $start24,
                            'end_time_24' => $end24,
                            'selected_start_time' => optional($selectedStart24ClientTz)->format('H:i:s'),
                            'selected_end_time' => optional($selectedEnd24ClientTz)->format('H:i:s'),
                            'type' => 'available',
                        ];
                    }
                }
                $startTime += $slotDuration + ($gapDuration * 60);
            }
            foreach ($breaks as $brk) {
                try {
                    $breakStart = Carbon::createFromTimestamp($brk['start'], 'UTC')->setTimezone($professionalTz);
                    $breakEnd = Carbon::createFromTimestamp($brk['end'], 'UTC')->setTimezone($professionalTz);
                    $timeline[] = [
                        'type' => 'break',
                        'start_time_12' => $breakStart->format('h:i A'),
                        'end_time_12' => $breakEnd->format('h:i A'),
                        'start_time_24' => $breakStart->format('H:i:s'),
                        'end_time_24' => $breakEnd->format('H:i:s'),
                    ];
                } catch (\Exception $e) {
                    continue;
                }
            }
            $timeline = array_merge($timeline, $availableSlots);
            usort($timeline, function ($a, $b) {
                return strcmp($a['start_time_24'], $b['start_time_24']);
            });
            return ['available_slots' => $timeline];
        } catch (\Exception $e) {
            Log::error('Error in fetchAvailableSlots: ' . $e->getMessage(), ['exception' => $e]);
            return ['status' => false, 'message' => 'An error occurred while fetching available slots.', 'error_type' => 'system'];
        }
    }

    public function fetchAvailableSlotsworkingcodesinglebreaktime($request)
    {
        // Logic for fetching available slots with single break time
    }

    public function fetchAvailableSlots_withouttimezone($request)
    {
        // Logic for fetching available slots without timezone
    }

    public function fetchAppointmentssss($request)
    {
        // Logic for fetching appointments (calendar events)
    }

    public function fetchAppointments($request)
    {
        // Logic for fetching appointments for a month
    }

    public function saveUserTimezone($request)
    {
        // Logic for saving user timezone
    }

    public function addJoiningLink($uid)
    {
        // Logic for adding a joining link
    }

    public function saveJoiningLink($request, $uid)
    {
        // Logic for saving a joining link
    }

    public function markStatus($uid, $status)
    {
        // Logic for marking appointment status
    }

    public function appointmentBookingSuccess($appointment_booking_id)
    {
        // Logic for appointment booking success
    }

    public function searchAppointments($request)
    {
        // Logic for searching appointments
    }

    public function saveReminder($request)
    {
        // Logic for saving a reminder
    }

    public function setReminderModal($appointmentId)
    {
        // Logic for setting reminder modal
    }

    public function cancelAwaitingAppointment($bookingId)
    {
        // Logic for cancelling awaiting appointment
    }
} 