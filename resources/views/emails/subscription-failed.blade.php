@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					<p>Hi there,</p>
					<p>We encountered an issue while processing your subscription payment. Unfortunately, the payment did not go through.</p>
					<p>Please verify your payment details and try again. If you need assistance, feel free to contact our support team.</p>
					<p>Thank you for choosing our services.</p>
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection