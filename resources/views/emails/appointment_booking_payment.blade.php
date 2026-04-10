@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					<p>Hi {{ $professional_name }},</p>

					<p>Your appointment with client <strong>{{ $client_name }}</strong> has been successfully <strong>booked and paid</strong> for <strong>{{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}</strong>.</p>

					<p><strong>Appointment Details:</strong></p>
					<ul>
						<li>Client: {{ $client_name }}</li>
						<li>Appointment Date (As per client's Timezone): {{ $appointment->client_timezone_date ?? ''  }}</li>
						<li>Appointment Date (As per your Timezone): {{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}</li>
						<li>Service: {{ optional($appointment->service)->name ?? 'NA' }}</li>
						<li>Price Paid: {{ currencySymbol($appointment->currency) . ' ' . $appointment->price }}</li>
						<li>Booking Slot: {{ ($appointment->start_time_converted ? $appointment->start_time_converted : '' ). ' - ' . ($appointment->end_time_converted ? $appointment->end_time_converted : '') }}</li>
						<li>Booking ID: {{ $appointment->unique_id }}</li>
						<li>Appointment Mode: {{ $appointment->appointment_mode }}</li>
						<li>Booking Additional Info: {{ $appointment->additional_info }}</li>

					</ul>

					<p>No further action is required from your side. You may log in to your panel for more details or to manage your appointment.</p>

					<p><a href="{{ professionalTrustvisoryUrl().'/login' }}">Click here to log in</a>.</p>

					<p>Thank you for using Trustvisory!</p>

					<p>Best regards,<br>
					Trustvisory Team</p>
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection
