<?php

namespace App\Jobs;

use App\Models\AppointmentBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

use App\Models\CompanyLocations;

class SendAppointmentCancellation 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;
    protected $recipientType;

    /**
     * Create a new job instance.
     *
     * @param Appointment $appointment
     * @param string $recipientType ('client' or 'professional')
     */
    public function __construct(AppointmentBooking $appointment, string $recipientType)
    {
        $this->appointment = $appointment;
        $this->recipientType = $recipientType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->sendCancellationEmail($this->appointment, $this->recipientType);
    }

    /**
     * Send cancellation email to the specified recipient (client or professional).
     *
     * @param Appointment $appointment
     * @param string $recipientType
     * @return void
     */
    private function sendCancellationEmail($appointment, $recipientType)
    {
        // Select recipient (client or professional) and prepare email data
        if ($recipientType == 'client') {
            $recipient = $appointment->client;
            $recipientName = $appointment->client->first_name . ' ' . $appointment->client->last_name;
            $recipientEmail = $appointment->client->email;
            $appointmentWithName = $appointment->professional->first_name . ' ' . $appointment->professional->last_name;
            $receiver_id=$appointment->client->id;
            
            $socket_data = [
                "action" => "auto_cancellation_booking",
                "receiver_id" => $receiver_id,
                "appointmnet_uid" => $appointment->unique_id,
            ];
            initUserSocket($receiver_id, $socket_data);
            $clientTz = $recipient->timezone ?? 'UTC';
            $mailData['clientTz'] = $clientTz;
        
            $mailData['startInClientTz'] = $startInClientTz = Carbon::createFromFormat('H:i:s', $appointment->start_time, 'UTC')->setTimezone($clientTz)->format('h:i A');
            $mailData['endInClientTz'] = $endInClientTz = Carbon::createFromFormat('H:i:s', $appointment->end_time, 'UTC')->setTimezone($clientTz)->format('h:i A');
            $appointmentTime = $startInClientTz . ' - ' . $endInClientTz;
            $appointmentDate=$appointment->client_timezone_date;

        } else {
            $recipient = $appointment->professional;
            $recipientName = $appointment->professional->first_name . ' ' . $appointment->professional->last_name;
            $recipientEmail = $appointment->professional->email;
            $appointmentWithName = $appointment->client->first_name . ' ' . $appointment->client->last_name;
            $receiver_id=$appointment->professional->id;
            $socket_data = [
                "action" => "auto_cancellation_booking",
                "receiver_id" => $receiver_id,
                "appointmnet_uid" => $appointment->unique_id,
            ];
            initUserSocket($receiver_id, $socket_data);
                $location = CompanyLocations::find($appointment->location_id);
                $locationTimezone = $location->timezone ?? 'UTC'; // Fallback to UTC if not found
                
                // Convert start and end times to professional's timezone
                $mailData['startInProfTz'] = $startInProfTz = Carbon::createFromFormat('H:i:s', $appointment->start_time, 'UTC')->setTimezone($locationTimezone)->format('h:i A');
                $mailData['endInProfTz'] = $endInProfTz = Carbon::createFromFormat('H:i:s', $appointment->end_time, 'UTC')->setTimezone($locationTimezone)->format('h:i A');
                $appointmentTime = $startInProfTz . ' - ' . $endInProfTz;
                $appointmentDate=$appointment->appointment_date;
        }
        if($appointment->cancelled_reason=="Cancelled by professional Manually"){
            $autoCancel=false;
        }else{
            $autoCancel=true;
        }

        // Prepare the email data
        $mailData = [
            'autoCancel' => $autoCancel,
            'appointment' => $appointment,
            'recipientName' => $recipientName,
            'recipientEmail' => $recipientEmail,
            'appointment_date' => $appointmentDate,
            'recipientType'=>$recipientType,
            'appointment_time' =>  $appointmentTime,
            'appointmentWithName'=>$appointmentWithName,
            'professional_name' => $appointment->professional->first_name . ' ' . $appointment->professional->last_name,
        ];
        

        // Prepare the message content using the appointment cancellation template
        $mail_message = \View::make('emails.appointment_booking_cancel', $mailData);
        $mailData['mail_message'] = $mail_message;

        // Send email to the recipient
        $parameter['to'] = $recipientEmail;
        $parameter['to_name'] = $recipientName;
        $parameter['message'] = $mail_message;
        $parameter['subject'] = "Your appointment with {$appointmentWithName} has been cancelled";
        $parameter['view'] = "emails.appointment_booking_cancel";
        $parameter['data'] = $mailData;

        // Assuming you have a custom `sendMail` function
        $data = sendMail($parameter);
    }
}
