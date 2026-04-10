@extends('emails.mail-master')
@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">

                <p>Hi {{ optional($appointment->client)->first_name }},</p>

                <p>Your appointment with {{ optional($appointment->professional)->first_name . ' ' . optional($appointment->professional)->last_name }} has been successfully booked for <strong>{{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}</strong>.</p>

                <p>Please note that you are required to complete the payment at least <strong>one day before</strong> your appointment date to confirm your booking.</p>

                <p><strong>Appointment Details:</strong></p>
                <ul>
                    <li>Professional: {{ optional($appointment->professional)->first_name . ' ' . optional($appointment->professional)->last_name }}</li>
                    <li>Date: {{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}</li>
                    <li>Service: {{ optional($appointment->service)->name ?? 'NA' }}</li>
                    <li>Price: {{ currencySymbol($appointment->currency) . ' ' . $appointment->price }}</li>
                    <li>Booking Slot: (as per your timezone) ({{$clientTz}}): {{ ($appointment->start_time_converted ? $appointment->start_time_converted : '' ). ' - ' . ($appointment->end_time_converted ? $appointment->end_time_converted : '') }}</li>
                    <li>Booking Slot: (as per Professional's Selected Location Timezone) ({{$profTz}}):{{ (date("h:i A", strtotime($startInProfTz))??'').'-'.(date("h:i A", strtotime($endInProfTz)) ?? '') }}</li>
                    <li>Appointment Mode: {{ $appointment->appointment_mode }}</li>
                    <li>Booking Additional Info: {{ $appointment->additional_info }}</li>

                    <li>Appointment Location: {{optional($fetchLoctimezone->company)->company_name ?? ''}},{{$fetchLoctimezone->address_1 ?? ''}},
                    {{$fetchLoctimezone->address_2 ?? ''}},{{$fetchLoctimezone->state ?? ''}},{{$fetchLoctimezone->city ?? ''}},
                    {{$fetchLoctimezone->pincode ?? ''}},{{$fetchLoctimezone->country ?? ''}}
                    </li>

                    <li>Booking ID: {{ $appointment->unique_id }}</li>
                </ul>

                <p>Please <a href="{{ url('/login') }}">Login</a> into your panel to complete your payment.</p>

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
