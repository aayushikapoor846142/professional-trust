@extends('emails.mail-master')
@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					Dear <b>{{$admin_name}}</b>,<br><br>
					
					A professional has sent a reminder for their pending withdrawal request that requires your attention.<br><br>
					
					<b>Reminder Details:</b><br>
					<b>Professional:</b> {{$professional_name}}<br>
					<b>Request ID:</b> #{{$request_id}}<br>
					<b>Amount:</b> ${{$amount}}<br>
					<b>Request Date:</b> {{$request_date}}<br><br>
					
					Please review and process this withdrawal request at your earliest convenience.<br><br>
					
					<b>Request Information:</b><br>
					• Request ID: {{$request_id}}<br>
					• Amount Requested: ${{$amount}}<br>
					• Request Date: {{$request_date}}<br>
					• Status: Pending Review<br><br>
					
					You can access the withdrawal request management panel to review and process this request.<br><br>
					
					Thank you for your attention to this matter.<br><br>
					
					Best regards,<br>
					TrustVisory Team
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection 