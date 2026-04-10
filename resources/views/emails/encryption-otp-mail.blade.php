@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10"
    style="margin: 0 auto;">
    <tr>
        <td class="text_color_282828"
            style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
            <div>
                 <center>
                    <h1>File Password OTP</h1>
                </center>
                <p>
                    You have requested access to a secured password protected file through {{ siteSetting('company_name') }}.
                    To proceed, please use the following One-Time Password (OTP). This code is valid for
                    <b>2 minutes</b>.
                    For your security, <strong>do not share this OTP with anyone</strong>.
                </p>
                <p>
                    <center>
                        <h2>{{ $otp }}</h2>
                    </center>
                </p>
                <p>
                    If you did not request this file or OTP, please contact our support team immediately.
                </p>
            </div>
        </td>
    </tr>
    <tr>
        <td height="50"></td>
    </tr>
</table>

@endsection