@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10"
    style="margin: 0 auto;">
    <tr>
        <td mc:edit="text003" class="text_color_282828"
            style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
            <div>
                <p>Hi {{$name}},</p>
                <p>We detected a login from a new device or city:</p>
                <p><strong>City:</strong> {{ $city }}</p>
                <p><strong>Device Type:</strong> {{ $deviceType }}</p>
                <p>If this wasn't you, please take appropriate action.</p>
                <p>Thank you!</p>
            </div>
        </td>
    </tr>
    <tr>
        <td height="50"></td>
    </tr>
</table>
@endsection