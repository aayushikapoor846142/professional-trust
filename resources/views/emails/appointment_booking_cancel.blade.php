@extends('emails.mail-master')
@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
                <h2>Dear {{ $recipientName}}</h2>
				@if($autoCancel)
					<p>We regret to inform you that due to incomplete payment your appointment with {{ $appointmentWithName }} scheduled for {{ $appointment_date }} at {{ $appointment_time }} has been cancelled .</p>
					<p>You can rebook your appointment from your dashboard.</p>
				@else
					@if($recipientType=="professional")
					 <p>You have successfully cancelled your appointment with {{ $appointmentWithName }} that was scheduled for {{ $appointment_date }} at {{ $appointment_time }}.</p>
					 <p>If you wish to reschedule, you can easily rebook a new appointment from your dashboard.</p>
					@else
					 <p>We would like to inform you that {{ $appointmentWithName }} has cancelled the appointment originally scheduled for {{ $appointment_date }} at {{ $appointment_time }}.</p>
					 <p>Please visit your dashboard for more details or to manage your upcoming schedule.</p>
					@endif
				@endif
            
				<p> Please<a href="{{ url('/login') }}">Login</a> into your panel </p>
                <p> If you need further assitance please feel free to contact us. </p>
                <p>Best regards, <br>
                Trustvisory Team</p>
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection
