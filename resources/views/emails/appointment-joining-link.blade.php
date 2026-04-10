@extends('emails.mail-master')
@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">

                <p>Hi {{ optional($appointment->client)->first_name }},</p>

                <p>Thank you for scheduling an appointment with {{ optional($appointment->professional)->first_name . ' ' . optional($appointment->professional)->last_name }}.

                This is a reminder for your upcoming session: </p>

               <p>


                <b>**Appointment Details:**</b>
                <br>
                <b> -**Date:**</b> [{{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}]            
                <b> -** Booking Slot:**</b>  (as per your timezone): ({{$clientTz}}):{{ (date("h:i A", strtotime($startInClientTz))??'').'-'.(date("h:i A", strtotime($endInClientTz)) ?? '') }}</br>
                <b> -**Booking Slot: **</b> (as per Professional's Selected Location Timezone) ({{$profTz}}) :{{ ($appointment->start_time_converted ? $appointment->start_time_converted : '' ). ' - ' . ($appointment->end_time_converted ? $appointment->end_time_converted : '') }}</br>
                <b>- **Mode:**</b> Online
                </p>
                <p>
                To join the meeting, please use the link below:
                </p>
                <p>
                **[Join Meeting With]({{$appointment->appointment_mode_details}})**
                </p>
                <p>
                    If you have any questions or need to reschedule, feel free to contact us at your convenience.
                </p>


                <p>Looking forward to connecting with you.</p>
                <p>Thank you for choosing our service!</p>
                <p>Best regards, <br>
                Trustvisory Team</p>

				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection
