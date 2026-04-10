@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10"
    style="margin: 0 auto;">
    <tr>
        <td mc:edit="text003" class="text_color_282828"
            style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
            <div>
                <center>
                    <h1>
                        Verification code
                    </h1>
                </center>
                <p>
                    Thank you for choosing {{siteSetting('company_name')}}. Use the following OTP to complete the procedure to sign up. OTP is
                    valid for&nbsp;<b>2 minutes</b>. Do not share this code with others
                </p>
                <p>
                    <center>
                        <h2>{{ $otp }}</h2>
                    </center>
                </p>
            </div>
            <!-- <div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					Your OTP for Registration
					<br><br>
					<strong>OTP:</strong> {{ $otp }}
					<br><br>
					<a href="">Resend OTP</a>
				</div>
			</div> -->
        </td>
    </tr>
    <tr>
        <td height="50"></td>
    </tr>
</table>
@endsection