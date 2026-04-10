@extends('emails.mail-master')

@section('content')
<table  align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: Inter, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="CDSTyEMail-receipt-container">
				<div class="CDSTyEMail-receipt-container-image" style="text-align:center"><img src="https://media.trustvisory.com/get-file-url?file=40237-winner-2.png&file_path=media&t=gxNXSSOWZgZTGlQ&s=r" style="margin: 0 auto;"  /></div>
				<h3><b>Dear, Admin</b></h3>
                <p>Below user Supported.</p>
                <label><b>Name:</b> {{$name}}</label></br>
                <label><b>Email:</b> {{$email}}</label></br>
                <label><b>Payment Type:</b> {{$payment_type}}</label></br>
                <label><b>Total Amount:</b> {{currencySymbol()}}{{$totalAmount}}</label>
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection