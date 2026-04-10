<?php
use App\Models\AppointmentBooking;
use App\Models\AppointmentBookingFlow;
use App\Models\TimeDuration;
use Illuminate\Support\Str;
use App\Models\AppointmentTypes;
use App\Models\ProfessionalServices;

if(!function_exists("checkTimeDuration")){
    function checkTimeDuration()
    {        
         $professionalId = auth()->user()->getRelatedProfessionalId();

        return TimeDuration::where("professional_id", $professionalId)
                ->visibleToUser(auth()->user()->id)
                ->get();
        
    }
}

if(!function_exists("getDuration")){
    function getDuration()
    {       
         $professionalId = auth()->user()->getRelatedProfessionalId();

        return TimeDuration::where('professional_id',$professionalId)    
                ->visibleToUser(auth()->user()->id)
                ->orderBy('id','desc')->get();
        
    }
}
if (!function_exists('str_replace_array')) {
    function str_replace_array($search, array $replace, $subject) {
        foreach ($replace as $value) {
            $subject = preg_replace('/' . preg_quote($search, '/') . '/', is_numeric($value) ? $value : "'{$value}'", $subject, 1);
        }
        return $subject;
    }
}

if(!function_exists("checkAppointmentTypes")){
    function checkAppointmentTypes()
    {
        $professionalId = auth()->user()->getRelatedProfessionalId();
        return AppointmentTypes::where("professional_id",  $professionalId)
                        ->visibleToUser(auth()->user()->id)
                        ->get();
        
    }
}


if(!function_exists("getAppointmentTypes")){
    function getAppointmentTypes()
    {
          $professionalId = auth()->user()->getRelatedProfessionalId();
        return AppointmentTypes::where('professional_id',$professionalId)
                        ->visibleToUser(auth()->user()->id)
                        ->orderBy('id','desc')->get();
        
    }
}

if(!function_exists("getServices")){
    function getServices($ids)
    {
        return ProfessionalServices::with(['ImmigrationServices'])->whereIn('id',explode(',',$ids))->orderBy('id','desc')->get();
        
    }
}
if(!function_exists("appointmentCounts")){
    function appointmentCounts($professionalId)
    {
       return AppointmentBooking::where('professional_id',$professionalId)->count();
    }

}
if(!function_exists("appointmentBookingFlowCounts")){
    function appointmentBookingFlowCounts($professionalId)
    {
       return AppointmentBookingFlow::where('professional_id',$professionalId)->count();
    }

}
if(!function_exists("countAppointment")){
    function countAppointment($type)
    {
        $professionalId = auth()->user()->getRelatedProfessionalId();
        if ($type == 'all') {
            return  AppointmentBooking::where('professional_id',$professionalId)
                                            ->count();
        } else {
            return AppointmentBooking::where('status', $type)            
                                    ->where('professional_id',$professionalId)
                                    ->count();
        }
    }

}

